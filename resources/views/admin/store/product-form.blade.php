@extends('layouts.portal')

@section('title', $product->exists ? 'Edit product' : 'Add product')

@section('content')
<div class="mb-4">
    <a class="small text-decoration-none" href="{{ route('admin.store.index') }}">← Shop content</a>
    <h1 class="portal-heading h2 mt-2 mb-1">{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
    <p class="portal-subtitle mb-0">Update the catalog details and see a product-card preview.</p>
</div>
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.store.products.update', $product) : route('admin.store.products.store') }}">
    @csrf
    @if($product->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-lg-7">
            <section class="portal-module p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="productName">Product name</label>
                        <input id="productName" class="form-control" name="name" required maxlength="255" value="{{ old('name', $product->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="productCategory">Category</label>
                        <input id="productCategory" class="form-control" name="category" required maxlength="100" value="{{ old('category', $product->category ?? 'General') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="productPrice">Price (PHP)</label>
                        <input id="productPrice" class="form-control" name="price" type="number" step=".01" min="0" required value="{{ old('price', $product->price) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="stockQuantity">Stock quantity</label>
                        <input id="stockQuantity" class="form-control" name="stock_quantity" type="number" min="0" required value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="stockStatus">Stock status</label>
                        <select id="stockStatus" class="form-select" name="stock_status">
                            @foreach(['In Stock', 'Low Stock', 'Out of Stock'] as $status)
                                <option value="{{ $status }}" @selected(old('stock_status', $product->stock_status ?? 'In Stock') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="productVisibility">Visibility</label>
                        <select id="productVisibility" class="form-select" name="is_active">
                            <option value="1" @selected((string) old('is_active', $product->is_active ?? 1) === '1')>Published</option>
                            <option value="0" @selected((string) old('is_active', $product->is_active ?? 1) === '0')>Hidden</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="productDescription">Description</label>
                        <textarea id="productDescription" class="form-control" name="description" rows="4" maxlength="10000">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="productImage">Product photo</label>
                        <input id="productImage" class="form-control" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(!$product->exists)>
                        <div class="form-text">JPG, PNG, or WebP; up to 5 MB.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="productFeatures">Highlights</label>
                        <textarea id="productFeatures" class="form-control" name="features" rows="4" maxlength="4000">{{ old('features', implode("\n", $product->attributes['features'] ?? [])) }}</textarea>
                        <div class="form-text">Enter one plain-text product highlight per line.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="productGallery">Additional gallery photos</label>
                        <input id="productGallery" class="form-control" name="gallery[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        <div class="form-text">Optional; existing gallery images are retained when editing.</div>
                    </div>
                </div>
                <p class="portal-muted small mt-3 mb-3">Product URLs use a stable, name-based slug. Existing external image URLs remain supported.</p>
                <button class="btn btn-primary" type="submit">{{ $product->exists ? 'Save changes' : 'Create product' }}</button>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="portal-module">
                <div class="portal-module-header"><h2 class="portal-module-title">Shop card preview</h2></div>
                <div class="p-3">
                    @php($previewImage = $product->image_url ? (str_starts_with($product->image_url, 'http') ? $product->image_url : asset('storage/'.$product->image_url)) : asset('images/logo.png'))
                    <article class="catalog-card">
                        <img id="imagePreview" src="{{ $previewImage }}" alt="">
                        <div class="catalog-card-copy">
                            <span id="categoryPreview" class="catalog-category">{{ $product->category ?: 'Category' }}</span>
                            <h3 id="namePreview">{{ $product->name ?: 'Product name' }}</h3>
                            <div id="pricePreview" class="catalog-price">₱{{ number_format((float) $product->price, 2) }}</div>
                            <p id="descriptionPreview" class="portal-muted small">{{ $product->description }}</p>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const productField = name => document.querySelector(`[name="${name}"]`);
    productField('name').addEventListener('input', event => document.getElementById('namePreview').textContent = event.target.value || 'Product name');
    productField('category').addEventListener('input', event => document.getElementById('categoryPreview').textContent = event.target.value || 'Category');
    productField('price').addEventListener('input', event => document.getElementById('pricePreview').textContent = `₱${(Number(event.target.value) || 0).toFixed(2)}`);
    productField('description').addEventListener('input', event => document.getElementById('descriptionPreview').textContent = event.target.value);
    productField('image').addEventListener('change', event => {
        if (event.target.files[0]) document.getElementById('imagePreview').src = URL.createObjectURL(event.target.files[0]);
    });
</script>
@endpush
