@extends('layouts.portal')

@section('title', 'Shop content')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="portal-muted small fw-bold text-uppercase mb-1">Store management</p>
        <h1 class="portal-heading h2 mb-1">Shop content</h1>
        <p class="portal-subtitle mb-0">Manage products, promotions, and their public previews.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('shop') }}" target="_blank" rel="noopener">View shop</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<section class="portal-module mb-4">
    <div class="portal-module-header">
        <div>
            <h2 class="portal-module-title">Products</h2>
            <p class="portal-muted small mb-0">Published products appear in the public shop and chatbot lookup.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.store.products.create') }}">Add product</a>
    </div>
    <div class="table-responsive">
        <table class="table portal-table table-hover mb-0">
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong><br><small class="portal-muted">{{ $product->slug }}</small></td>
                    <td>{{ $product->category }}</td>
                    <td>₱{{ number_format((float) $product->price, 2) }}</td>
                    <td>{{ $product->stock_quantity }} · {{ $product->stock_status }}</td>
                    <td><span class="portal-pill">{{ $product->trashed() ? 'Removed' : ($product->is_active ? 'Published' : 'Hidden') }}</span></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            @if(!$product->trashed())
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.store.products.edit', $product) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.store.products.destroy', $product) }}" onsubmit="return confirm('Remove this product from the shop?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.store.products.restore', $product->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary">Restore</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="portal-muted text-center py-4">There are no catalog products yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $products->links() }}</div>
</section>

<section class="portal-module">
    <div class="portal-module-header">
        <div>
            <h2 class="portal-module-title">Promotional hero banners</h2>
            <p class="portal-muted small mb-0">Edit copy, images, layout, colors, and preview before publishing.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.store.banners.create') }}">Create banner</a>
    </div>
    <div class="table-responsive">
        <table class="table portal-table table-hover mb-0">
            <thead><tr><th>Banner</th><th>Order</th><th>Visibility</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($banners as $banner)
                <tr>
                    <td><strong>{{ $banner->title }}</strong><br><small class="portal-muted">{{ $banner->layout }} · {{ $banner->alignment }}</small></td>
                    <td>{{ $banner->sort_order }}</td>
                    <td><span class="portal-pill">{{ $banner->is_active ? 'Published' : 'Hidden' }}</span></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.store.banners.edit', $banner) }}">Edit preview</a>
                            <form method="POST" action="{{ route('admin.store.banners.destroy', $banner) }}" onsubmit="return confirm('Remove this banner?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="portal-muted text-center py-4">No promotional banners. Create one to replace the shop's fallback hero.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
