<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PromotionalBanner;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StoreManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.store.index', [
            'products' => Product::withTrashed()->latest()->paginate(12),
            'banners' => PromotionalBanner::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function createProduct(): View
    {
        return view('admin.store.product-form', ['product' => new Product]);
    }

    public function editProduct(Product $product): View
    {
        return view('admin.store.product-form', compact('product'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request, false);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['attributes'] = $this->productAttributes($request);
        $data['image_url'] = $request->file('image')?->store('products', 'public');
        Product::create($data);

        return redirect()->route('admin.store.index')->with('success', 'Product added to the catalog.');
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, true);
        $data['attributes'] = $this->productAttributes($request, $product);
        $data['image_url'] = $request->file('image')?->store('products', 'public') ?? $product->image_url;
        if ($request->hasFile('image') && $product->image_url && ! str_starts_with($product->image_url, 'http')) {
            Storage::disk('public')->delete($product->image_url);
        }
        $product->update($data);

        return redirect()->route('admin.store.index')->with('success', 'Product updated.');
    }

    public function destroyProduct(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Product removed from the shop.');
    }

    public function restoreProduct(int $product): RedirectResponse
    {
        Product::withTrashed()->findOrFail($product)->restore();

        return back()->with('success', 'Product restored.');
    }

    public function createBanner(): View
    {
        return view('admin.store.banner-form', ['banner' => new PromotionalBanner]);
    }

    public function editBanner(PromotionalBanner $banner): View
    {
        return view('admin.store.banner-form', compact('banner'));
    }

    public function storeBanner(Request $request): RedirectResponse
    {
        $data = $this->validateBanner($request);
        $data['image_path'] = $request->file('image')->store('banners', 'public');
        PromotionalBanner::create($data);

        return redirect()->route('admin.store.index')->with('success', 'Promotional banner published.');
    }

    public function updateBanner(Request $request, PromotionalBanner $banner): RedirectResponse
    {
        $data = $this->validateBanner($request, false);
        if ($request->hasFile('image')) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }
        $banner->update($data);

        return redirect()->route('admin.store.index')->with('success', 'Promotional banner updated.');
    }

    public function destroyBanner(PromotionalBanner $banner): RedirectResponse
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        $banner->delete();

        return back()->with('success', 'Promotional banner removed.');
    }

    private function validateProduct(Request $request, bool $isUpdate): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:10000',
            'price' => 'required|numeric|min:0|max:99999999',
            'stock_quantity' => 'required|integer|min:0',
            'stock_status' => ['required', Rule::in(['In Stock', 'Low Stock', 'Out of Stock'])],
            'is_active' => 'required|boolean',
            'image' => [$isUpdate ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'features' => 'nullable|string|max:4000',
            'gallery' => 'nullable|array|max:8',
            'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
    }

    private function productAttributes(Request $request, ?Product $product = null): array
    {
        $attributes = $product ? ($product->attributes ?? []) : [];
        $attributes['features'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $request->input('features')))));
        $gallery = $attributes['gallery'] ?? [];
        foreach ($request->file('gallery', []) as $image) {
            $gallery[] = $image->store('products/gallery', 'public');
        }
        $attributes['gallery'] = $gallery;

        return $attributes;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $suffix = 2;
        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    private function validateBanner(Request $request, bool $requireImage = true): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'body' => 'nullable|string|max:240',
            'link_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! is_string($value)) {
                        $fail('The link must be a site path or a valid HTTPS URL.');

                        return;
                    }

                    if (str_contains($value, '\\') || preg_match('/[\x00-\x20]/', $value)) {
                        $fail('The link must not contain whitespace or backslashes.');

                        return;
                    }

                    if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
                        return;
                    }

                    if (! str_starts_with(strtolower($value), 'https://') || filter_var($value, FILTER_VALIDATE_URL) === false) {
                        $fail('The link must be a site path or a valid HTTPS URL.');
                    }
                },
            ],
            'cta_label' => 'nullable|string|max:40',
            'background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'alignment' => ['required', Rule::in(['left', 'center', 'right'])],
            'layout' => ['required', Rule::in(['split', 'overlay'])],
            'is_active' => 'required|boolean',
            'sort_order' => 'required|integer|min:0|max:9999',
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        unset($data['image']);

        return $data;
    }
}
