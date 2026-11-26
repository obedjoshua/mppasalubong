<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PromotionalBanner;
use App\Models\Purchase;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StoreManagementController extends Controller
{
    public function index(): View
    {
        $allProducts = Product::withTrashed()->get();
        $banners = PromotionalBanner::orderBy('sort_order')->orderBy('id')->get();
        $purchases = Purchase::with(['user', 'product'])->latest()->get();

        // Store summary metrics
        $storeStats = [
            'activeProducts'   => $allProducts->whereNull('deleted_at')->where('is_active', true)->count(),
            'lowOrOutOfStock'  => $allProducts->whereNull('deleted_at')->whereIn('stock_status', ['Low Stock', 'Out of Stock'])->count(),
            'activeBanners'    => $banners->where('is_active', true)->count(),
            'totalShopRevenue' => (float) $purchases->where('status', '!=', 'cancelled')->sum(function (Purchase $purchase): float {
                $amount = $purchase->getAttribute('total_amount') ?? $purchase->getAttribute('total_price') ?? $purchase->getAttribute('amount') ?? 0;

                return (float) $amount;
            }),
        ];

        // Category inventory breakdown for bar chart
        $categoryBreakdown = $allProducts->whereNull('deleted_at')
            ->groupBy(fn (Product $p): string => $p->category ?: 'Uncategorized')
            ->map(fn ($items, string $category): array => [
                'category' => $category,
                'count'    => $items->count(),
                'stock'    => (int) $items->sum('stock_quantity'),
            ])
            ->values();

        // Daily shop sales/activity series for stock-style timeframe chart
        $salesByDay = $purchases
            ->filter(fn (Purchase $p): bool => $p->created_at !== null && $p->status !== 'cancelled')
            ->groupBy(fn (Purchase $p): string => $p->created_at->format('Y-m-d'))
            ->map(function ($dayPurchases, string $dateStr): array {
                $revenue = $dayPurchases->sum(function (Purchase $p): float {
                    return (float) ($p->getAttribute('total_amount') ?? $p->getAttribute('total_price') ?? $p->getAttribute('amount') ?? 0);
                });

                return [
                    'timestamp' => Carbon::parse($dateStr)->toIso8601String(),
                    'label'     => Carbon::parse($dateStr)->format('M j'),
                    'orders'    => $dayPurchases->count(),
                    'revenue'   => round((float) $revenue, 2),
                ];
            })
            ->sortKeys()
            ->values();

        // Daily Store Logs grouped by Y-m-d (combines Purchases, Product catalog updates, & Banner updates)
        $dailyLogs = [];

        foreach ($purchases as $purchase) {
            if (! $purchase->created_at) {
                continue;
            }
            $dateKey = $purchase->created_at->format('Y-m-d');
            $customerName = method_exists($purchase, 'user') && $purchase->user
                ? $purchase->user->name
                : 'Customer';
            $productName = method_exists($purchase, 'product') && $purchase->product
                ? $purchase->product->name
                : 'Shop Item';
            $amount = (float) ($purchase->getAttribute('total_amount') ?? $purchase->getAttribute('total_price') ?? $purchase->getAttribute('amount') ?? 0);

            $dailyLogs[$dateKey][] = [
                'type'   => 'Purchase',
                'badge'  => 'purchase',
                'time'   => $purchase->created_at->format('H:i'),
                'title'  => "Order #{$purchase->id} · {$productName}",
                'detail' => "{$customerName} · ₱" . number_format($amount, 2) . ' (' . ucfirst((string) ($purchase->status ?? 'pending')) . ')',
                'url'    => route('admin.purchases.show', $purchase->id),
            ];
        }

        foreach ($allProducts as $product) {
            $logDate = $product->updated_at ?? $product->created_at;
            if (! $logDate) {
                continue;
            }
            $dateKey = $logDate->format('Y-m-d');
            $actionLabel = $product->trashed()
                ? 'Removed from catalog'
                : ($product->created_at && $product->created_at->equalTo($logDate) ? 'Added to catalog' : 'Catalog / stock updated');

            $dailyLogs[$dateKey][] = [
                'type'   => 'Product',
                'badge'  => 'product',
                'time'   => $logDate->format('H:i'),
                'title'  => $product->name,
                'detail' => "{$actionLabel} · Stock: {$product->stock_quantity} ({$product->stock_status})",
                'url'    => $product->trashed() ? route('admin.store.index') : route('admin.store.products.edit', $product->id),
            ];
        }

        foreach ($banners as $banner) {
            $logDate = $banner->updated_at ?? $banner->created_at;
            if (! $logDate) {
                continue;
            }
            $dateKey = $logDate->format('Y-m-d');

            $dailyLogs[$dateKey][] = [
                'type'   => 'Banner',
                'badge'  => 'banner',
                'time'   => $logDate->format('H:i'),
                'title'  => "Hero Banner: {$banner->title}",
                'detail' => ($banner->is_active ? 'Published' : 'Hidden') . " · {$banner->layout} layout",
                'url'    => route('admin.store.banners.edit', $banner->id),
            ];
        }

        foreach ($dailyLogs as $dateKey => $entries) {
            usort($entries, fn (array $a, array $b): int => strcmp($b['time'], $a['time']));
            $dailyLogs[$dateKey] = $entries;
        }

        return view('admin.store.index', [
            'products'          => Product::withTrashed()->latest()->paginate(12),
            'banners'           => $banners,
            'storeStats'        => $storeStats,
            'categoryBreakdown' => $categoryBreakdown,
            'salesSeries'       => $salesByDay,
            'dailyLogs'         => $dailyLogs,
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
