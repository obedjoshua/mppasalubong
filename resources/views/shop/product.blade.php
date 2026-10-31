@extends('layouts.app')

@section('title', $product->name.' | Masaganang Pagpapala')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .product-detail{--product-purple:#8a38f5;max-width:1280px;margin:0 auto;padding:24px clamp(18px,4vw,48px) 72px;font-family:Manrope,sans-serif;color:#302c35}
        .product-breadcrumbs{display:flex;gap:10px;align-items:center;margin:10px 0 30px;color:#77808a;font-size:.88rem}
        .product-breadcrumbs a{color:inherit;text-decoration:none}.product-breadcrumbs a:hover{color:var(--product-purple)}
        .product-detail-grid{display:grid;grid-template-columns:minmax(0,1.05fr) minmax(320px,.95fr);gap:clamp(28px,6vw,80px);align-items:start}
        .product-gallery{position:sticky;top:24px}
        .product-main-image{display:block;width:100%;height:min(55vw,560px);min-height:320px;object-fit:contain;background:#fff}
        .product-thumbnails{display:flex;gap:12px;overflow-x:auto;margin-top:24px;padding:3px}
        .product-thumbnails button{flex:0 0 94px;height:88px;padding:5px;border:2px solid transparent;border-radius:11px;background:#fff;cursor:pointer}
        .product-thumbnails button.active,.product-thumbnails button:focus-visible{border-color:var(--product-purple)}
        .product-thumbnails img{width:100%;height:100%;object-fit:contain}
        .product-info{padding:10px 0}
        .product-category{color:#7a7780;font-size:.8rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase}
        .product-info h1{margin:8px 0 12px;color:#56545a;font-size:clamp(2rem,4vw,3rem);font-weight:800;line-height:1.15}
        .product-price{color:var(--product-purple);font-size:1.55rem;font-weight:800}
        .product-availability{display:inline-block;margin:14px 0;padding:7px 12px;border-radius:20px;background:#f1e9fa;color:#623592;font-size:.82rem;font-weight:800}
        .product-description{color:#66636a;font-size:.95rem;line-height:1.8;white-space:pre-line}
        .product-highlights{padding-left:20px;color:#47434b;line-height:1.8}
        .product-order{display:grid;grid-template-columns:1fr 150px;gap:12px;align-items:end;margin-top:28px;padding-top:22px;border-top:1px solid #eeeaf1}
        .product-order label{grid-column:1/-1;color:#777;font-size:.86rem;font-weight:700;text-transform:uppercase}
        .product-order input{width:100%;padding:12px;border:1px solid #dedbe2;border-radius:8px;font:inherit}
        .product-order a{grid-column:1/-1;padding:15px 20px;border-radius:11px;background:var(--product-purple);box-shadow:0 4px 0 #6523c1;color:#fff;text-align:center;text-decoration:none;font-weight:800}
        .product-order a[aria-disabled=true]{background:#aaa;box-shadow:none;pointer-events:none}
        .add-to-cart-btn{grid-column:1/-1;padding:15px 20px;border-radius:11px;background:#22c55e;box-shadow:0 4px 0 #16a34a;color:#fff;text-align:center;border:none;font-family:inherit;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all 0.2s}
        .add-to-cart-btn:hover{background:#16a34a;transform:translateY(-2px);box-shadow:0 6px 0 #16a34a}
        .add-to-cart-btn:active{transform:translateY(2px);box-shadow:0 2px 0 #16a34a}
        .add-to-cart-btn:disabled{background:#aaa;box-shadow:none;cursor:not-allowed;transform:none}
        .add-to-cart-btn.added{background:#22c55e}
        .product-order-note{margin-top:16px;color:#79757d;font-size:.8rem;line-height:1.7}
        @media(max-width:760px){.product-detail{padding:12px 16px 46px}.product-detail-grid{grid-template-columns:1fr;gap:18px}.product-gallery{position:static}.product-main-image{height:76vw;min-height:260px;max-height:430px}.product-thumbnails{margin-top:12px}.product-info{padding:2px 0}.product-info h1{font-size:2rem}}
    </style>
@endpush

@section('content')
@php
    $mainImage = $product->image_url
        ? (str_starts_with($product->image_url, "http") ? $product->image_url : asset("storage/".$product->image_url))
        : asset("images/logo.png");
    $gallery = collect($product->attributes["gallery"] ?? [])
        ->map(fn ($image) => str_starts_with($image, "http") ? $image : asset("storage/".$image))
        ->prepend($mainImage)->unique()->values();
    $features = $product->attributes["features"] ?? [];
    $available = $product->stock_quantity > 0 && $product->stock_status !== "Out of Stock";
@endphp
<main class="product-detail">
    <nav class="product-breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route("home") }}">Home</a><span>/</span><a href="{{ route("shop") }}">Shop</a><span>/</span><span aria-current="page">{{ $product->name }}</span>
    </nav>
    <div class="product-detail-grid">
        <section class="product-gallery" aria-label="Product images">
            <img class="product-main-image" id="detailMainImage" src="{{ $mainImage }}" alt="{{ $product->name }}">
            @if($gallery->count() > 1)
                <div class="product-thumbnails">
                    @foreach($gallery as $image)
                        <button type="button" class="{{ $loop->first ? "active" : "" }}" data-image="{{ $image }}" aria-label="View product image {{ $loop->iteration }}">
                            <img src="{{ $image }}" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </section>
        <section class="product-info">
            <span class="product-category">{{ $product->category }}</span>
            <h1>{{ $product->name }}</h1>
            <div class="product-price">&#8369;{{ number_format((float) $product->price, 2) }}</div>
            <span class="product-availability">{{ $available ? "Available to order" : "Currently unavailable" }}</span>
            @if($product->description)<p class="product-description">{{ $product->description }}</p>@endif
            @if(count($features))
                <h2 class="h5 mt-4">Product highlights</h2>
                <ul class="product-highlights">@foreach($features as $feature)<li>{{ $feature }}</li>@endforeach</ul>
            @endif
            <div class="product-order">
                <label for="quantity">Quantity</label>
                <input id="quantity" type="number" min="1" max="{{ max(1, $product->stock_quantity) }}" value="1" @disabled(!$available)>
                <button id="addToCartBtn" class="add-to-cart-btn" @disabled(!$available) data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; margin-right: 8px;">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></path>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    Add to Cart
                </button>
                <a id="orderLink" href="{{ route("orders.create", ["product" => $product->slug, "quantity" => 1]) }}" @if(!$available) aria-disabled="true" @endif style="margin-top: 12px; display: block; text-align: center; color: var(--product-purple); font-weight: 600; font-size: 0.9rem;">Or continue to order request form</a>
            </div>
            <p class="product-order-note">Items added to cart can be checked out with online payment via PayMongo.</p>
        </section>
    </div>
</main>
@include("partials.Footer")
@endsection

@push("scripts")
<script>
    document.querySelectorAll(".product-thumbnails button").forEach(button => {
        button.addEventListener("click", () => {
            document.getElementById("detailMainImage").src = button.dataset.image;
            document.querySelectorAll(".product-thumbnails button").forEach(item => item.classList.remove("active"));
            button.classList.add("active");
        });
    });

    const quantity = document.getElementById("quantity");
    quantity?.addEventListener("change", () => {
        const value = Math.max(1, Math.min(Number(quantity.max), Number(quantity.value) || 1));
        quantity.value = value;
        const link = document.getElementById("orderLink");
        const url = new URL(link.href);
        url.searchParams.set("quantity", value);
        link.href = url.toString();
    });

    // Add to cart functionality
    document.getElementById("addToCartBtn")?.addEventListener("click", function() {
        const btn = this;
        const productId = btn.dataset.productId;
        const productName = btn.dataset.productName;
        const productPrice = btn.dataset.productPrice;
        const qty = parseInt(document.getElementById("quantity").value) || 1;

        btn.disabled = true;
        btn.innerHTML = '<svg class="spinner" viewBox="0 0 24 24" style="width:20px;height:20px;animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4 31.4" stroke-linecap="round"></circle></svg> Adding...';

        fetch("{{ route("cart.add") }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: qty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.classList.add("added");
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; margin-right: 8px;"><polyline points="20 6 9 17 4 12"></polyline></svg> Added to Cart!';
                setTimeout(() => {
                    btn.classList.remove("added");
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; margin-right: 8px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Add to Cart';
                    btn.disabled = false;
                }, 2000);
                
                const cartCount = document.getElementById("cart-count");
                if (cartCount) cartCount.textContent = data.cart_count;
            } else {
                alert("Failed to add to cart: " + (data.message || "Unknown error"));
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; margin-right: 8px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Add to Cart';
            }
        })
        .catch(err => {
            console.error(err);
            alert("Failed to add to cart. Please try again.");
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; margin-right: 8px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Add to Cart';
        });
    });
</script>
@endpush