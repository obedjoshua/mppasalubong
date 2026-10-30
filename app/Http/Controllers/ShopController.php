<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PromotionalBanner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->when($request->filled('category'), fn ($query) => $query->where('category', (string) $request->string('category')))
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.(string) $request->string('q').'%'))
            ->orderBy('name')
            ->get();

        return view('shop', [
            'products' => $products,
            'categories' => Product::query()->where('is_active', true)->select('category')->distinct()->orderBy('category')->pluck('category'),
            'banners' => PromotionalBanner::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('shop.product', compact('product'));
    }

    public function lookup(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Product::query()->where('is_active', true)
            ->when(! empty($filters['q']), fn ($builder) => $builder->where(function ($products) use ($filters) {
                $term = '%'.$filters['q'].'%';
                $products->where('name', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhere('description', 'like', $term);
            }))
            ->when(! empty($filters['category']), fn ($builder) => $builder->where('category', $filters['category']))
            ->orderBy('name');

        $products = $query->limit(50)->get()->map(fn (Product $product) => [
            'name' => $product->name,
            'slug' => $product->slug,
            'url' => route('shop.product', $product->slug),
            'category' => $product->category,
            'description' => $product->description,
            'price' => (float) $product->price,
            'currency' => 'PHP',
            'available' => $product->stock_quantity > 0 && $product->stock_status !== 'Out of Stock',
            'availability' => $product->stock_quantity > 0 && $product->stock_status !== 'Out of Stock' ? 'in_stock' : 'out_of_stock',
            'image_url' => $product->image_url ? $this->publicImageUrl($product->image_url) : null,
        ]);

        return response()->json(['data' => $products]);
    }

    private function publicImageUrl(string $path): string
    {
        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }
}
