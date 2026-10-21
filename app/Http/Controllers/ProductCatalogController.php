<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCatalogController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);
        return view('staff.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'stock_status' => 'required|in:In Stock,Low Stock,Out of Stock',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'stock_status' => $validated['stock_status'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'New product added to catalog.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'stock_status' => 'required|in:In Stock,Low Stock,Out of Stock',
            'is_active' => 'required|boolean',
        ]);

        $product->update($validated);

        return back()->with('success', "Product {$product->name} updated successfully.");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // Soft delete execution

        return back()->with('info', "Product {$product->name} archived from store.");
    }
}