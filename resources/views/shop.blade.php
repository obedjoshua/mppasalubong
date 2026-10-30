@extends('layouts.app')

@section('title', 'Shop | Masaganang Pagpapala')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .catalog-page{--catalog-ink:#17314c;--catalog-blue:#b8e4f2;--catalog-accent:#8a38f5;font-family:Manrope,sans-serif;color:var(--catalog-ink);background:#fff}
        .catalog-categories{width:100%;background:#c9edf7;background-image:repeating-linear-gradient(145deg,transparent 0 15px,rgba(255,255,255,.17) 15px 19px);padding:24px 18px}
        .catalog-category-list{max-width:1000px;margin:auto;display:flex;justify-content:center;align-items:center;gap:clamp(22px,6vw,82px);overflow-x:auto}
        .catalog-category-link{display:flex;min-width:94px;flex-direction:column;align-items:center;gap:8px;color:#153a67;text-align:center;text-decoration:none;font-size:.81rem;font-weight:800;line-height:1.2;text-transform:uppercase}
        .catalog-category-icon{display:grid;width:54px;height:54px;place-items:center;border:2px solid currentColor;border-radius:50%;font-size:1.7rem;transition:transform .2s ease,background .2s ease}
        .catalog-category-link:hover .catalog-category-icon{transform:translateY(-3px);background:rgba(255,255,255,.5)}
        .catalog-shell{max-width:1480px;margin:auto;padding:28px clamp(18px,4vw,54px) 68px}
        .catalog-breadcrumbs{display:flex;gap:10px;align-items:center;margin:10px 0 24px;color:#777e87;font-size:.88rem}
        .catalog-breadcrumbs a{color:inherit;text-decoration:none}.catalog-breadcrumbs a:hover{color:var(--catalog-accent)}
        .catalog-hero{position:relative;display:flex;min-height:260px;align-items:center;overflow:hidden;margin:0 0 38px;border-radius:18px;background:#310669;color:#fff}
        .catalog-hero img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .catalog-hero:after{position:absolute;inset:0;background:linear-gradient(90deg,rgba(24,7,48,.85),rgba(24,7,48,.12));content:""}
        .catalog-hero[data-layout=split] img{left:auto;width:55%}
        .catalog-hero[data-layout=split]:after{background:linear-gradient(90deg,rgba(24,7,48,.94) 0%,rgba(24,7,48,.75) 47%,rgba(24,7,48,.06) 100%)}
        .catalog-hero-copy{position:relative;z-index:1;width:min(680px,85%);padding:clamp(24px,5vw,54px)}
        .catalog-hero-copy h1{margin:0 0 12px;font-size:clamp(2rem,4vw,3.3rem);font-weight:800;line-height:1.08}
        .catalog-hero-copy p{margin:0 0 20px;font-size:1rem;line-height:1.7}
        .catalog-hero-copy a{display:inline-block;padding:12px 18px;border-radius:10px;background:#8a38f5;color:#fff;font-weight:800;text-decoration:none}
        .catalog-toolbar{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px;margin:12px 0 22px}
        .catalog-toolbar h1{margin:0;color:#1d3044;font-size:clamp(1.5rem,3vw,2rem);font-weight:800}
        .catalog-search{display:flex;width:min(100%,420px);gap:8px}
        .catalog-search input{flex:1;min-width:0;padding:11px 14px;border:1px solid #dfe7ed;border-radius:10px;font:inherit}
        .catalog-search button{padding:10px 17px;border:0;border-radius:10px;background:#173c68;color:#fff;font:inherit;font-weight:800}
        .catalog-pills{display:flex;gap:9px;overflow-x:auto;margin:0 0 26px;padding:2px 0 8px}
        .catalog-pills a{flex:0 0 auto;padding:9px 15px;border-radius:24px;background:#eef7fa;color:#23486c;font-size:.85rem;font-weight:700;text-decoration:none}
        .catalog-pills a.active{background:#173c68;color:#fff}
        .catalog-section{margin:28px 0 44px}
        .catalog-section h2{margin-bottom:20px;color:#183454;font-size:1.25rem;font-weight:800}
        .catalog-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:clamp(12px,2.4vw,30px)}
        .catalog-card{min-width:0;text-align:center}
        .catalog-card-image{position:relative;display:flex;min-height:clamp(160px,24vw,310px);align-items:center;justify-content:center;overflow:hidden;background:#fff}
        .catalog-card-image img{width:100%;height:clamp(160px,24vw,310px);object-fit:contain;transition:transform .25s ease}
        .catalog-card:hover .catalog-card-image img{transform:scale(1.045)}
        .catalog-card-copy{padding:14px 8px 8px}
        .catalog-card h3{margin:0 0 8px;color:#17314c;font-size:clamp(.85rem,1.2vw,1.03rem);font-weight:700;line-height:1.4}
        .catalog-category-label{display:none}
        .catalog-price{margin:0 0 7px;color:#7132bd;font-size:.97rem;font-weight:800}
        .catalog-status{color:#707d88;font-size:.78rem}
        .catalog-cta{display:inline-flex;margin-top:12px;padding:9px 15px;border:1px solid #ded5eb;border-radius:9px;color:#542487;font-size:.82rem;font-weight:800;text-decoration:none}
        .catalog-cta:hover{border-color:#8a38f5;background:#f7f1ff}
        .catalog-empty{padding:42px;border-radius:16px;background:#f7fafb;color:#645673;text-align:center}
        .catalog-footer-gap{height:20px}
        @media(max-width:850px){.catalog-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.catalog-category-list{justify-content:flex-start}}
        @media(max-width:580px){.catalog-categories{padding:17px 12px}.catalog-category-list{gap:16px}.catalog-category-link{min-width:78px;font-size:.7rem}.catalog-category-icon{width:46px;height:46px;font-size:1.4rem}.catalog-shell{padding:18px 14px 46px}.catalog-hero{min-height:190px;margin-bottom:26px;border-radius:14px}.catalog-hero-copy{width:94%;padding:22px}.catalog-hero-copy p{font-size:.88rem}.catalog-toolbar{align-items:stretch;flex-direction:column}.catalog-search{width:100%}.catalog-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 8px}.catalog-card-image,.catalog-card-image img{height:clamp(145px,44vw,220px);min-height:145px}.catalog-card-copy{padding:10px 3px}.catalog-card h3{font-size:.86rem}.catalog-cta{padding:8px 10px;font-size:.75rem}}
    </style>
@endpush

@section('content')
<div class="catalog-page">
    <nav class="catalog-categories" aria-label="Shop categories">
        <div class="catalog-category-list">
            @php($categoryIcons = ['roll' => '🥐', 'cake' => '🎂', 'cookie' => '🍪', 'package' => '🍬', 'wedding' => '💍'])
            @forelse($categories as $category)
                @php($iconKey = \Illuminate\Support\Str::lower($category))
                @php($icon = collect($categoryIcons)->first(fn ($value, $key) => str_contains($iconKey, $key)) ?? '🍰')
                <a class="catalog-category-link" href="{{ route('shop', ['category' => $category]) }}">
                    <span class="catalog-category-icon" aria-hidden="true">{{ $icon }}</span>
                    <span>{{ $category }}</span>
                </a>
            @empty
                <span class="catalog-category-link"><span class="catalog-category-icon" aria-hidden="true">🍰</span><span>Freshly baked</span></span>
            @endforelse
            <a class="catalog-category-link" href="{{ route('shop') }}">
                <span class="catalog-category-icon" aria-hidden="true">✦</span>
                <span>More soon</span>
            </a>
        </div>
    </nav>

    <main class="catalog-shell">
        <nav class="catalog-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span aria-current="page">Shop</span></nav>

        @if($banners->isNotEmpty())
            @foreach($banners as $banner)
                @php($bannerImage = $banner->image_path ? asset('storage/'.$banner->image_path) : null)
                <section class="catalog-hero" data-layout="{{ $banner->layout }}" style="background:{{ $banner->background_color }};text-align:{{ $banner->alignment }}">
                    @if($bannerImage)<img src="{{ $bannerImage }}" alt="" @if(!$loop->first)loading="lazy"@endif>@endif
                    <div class="catalog-hero-copy" style="color:{{ $banner->text_color }};margin-left:{{ $banner->alignment === 'right' ? 'auto' : ($banner->alignment === 'center' ? 'auto' : '0') }};margin-right:{{ $banner->alignment === 'left' ? 'auto' : ($banner->alignment === 'center' ? 'auto' : '0') }}">
                        <h1>{{ $banner->title }}</h1>
                        @if($banner->body)<p>{{ $banner->body }}</p>@endif
                        @if($banner->link_url && $banner->cta_label)<a href="{{ $banner->link_url }}">{{ $banner->cta_label }}</a>@endif
                    </div>
                </section>
            @endforeach
        @endif

        <div class="catalog-toolbar">
            <div><h1>Our products</h1><span class="catalog-status">{{ $products->count() }} {{ Str::plural('product', $products->count()) }} to discover</span></div>
            <form class="catalog-search" action="{{ route('shop') }}" method="GET">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Find a product" aria-label="Find a product">
                <button type="submit">Search</button>
            </form>
        </div>
        <nav class="catalog-pills" aria-label="Filter products by category">
            <a class="{{ request('category') ? '' : 'active' }}" href="{{ route('shop', request()->except('category')) }}">All products</a>
            @foreach($categories as $category)
                <a class="{{ request('category') === $category ? 'active' : '' }}" href="{{ route('shop', array_merge(request()->except('category'), ['category' => $category])) }}">{{ $category }}</a>
            @endforeach
        </nav>

        @if($products->isEmpty())
            <div class="catalog-empty">No products match your search right now.</div>
        @else
            @foreach($products->groupBy('category') as $category => $items)
                <section class="catalog-section">
                    <h2>{{ $category }}</h2>
                    <div class="catalog-grid">
                        @foreach($items as $product)
                            @php($productImage = $product->image_url ? (str_starts_with($product->image_url, 'http') ? $product->image_url : asset('storage/'.$product->image_url)) : asset('images/logo.png'))
                            <article class="catalog-card">
                                <a class="catalog-card-image" href="{{ route('shop.product', $product->slug) }}" aria-label="View {{ $product->name }}">
                                    <img src="{{ $productImage }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                <div class="catalog-card-copy">
                                    <h3>{{ $product->name }}</h3>
                                    <div class="catalog-price">₱{{ number_format((float) $product->price, 2) }}</div>
                                    <span class="catalog-status">{{ $product->stock_quantity > 0 && $product->stock_status !== 'Out of Stock' ? 'Available to order' : 'Currently unavailable' }}</span>
                                    <a class="catalog-cta" href="{{ route('shop.product', $product->slug) }}">View product</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif
    </main>
    <div class="catalog-footer-gap"></div>
    @include('partials.Footer')
</div>
@endsection
