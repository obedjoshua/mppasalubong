@extends('layouts.app')

@section('content')
        <div class="marquee">
            <div class="marquee-track">
                <span class="marquee-item">fresh baked daily <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">made with love <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">taste the tradition <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">order your pasalubong <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">fresh baked daily <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">made with love <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">taste the tradition <span class="sparkle">✧˖°</span></span>
                <span class="marquee-item">order your pasalubong <span class="sparkle">✧˖°</span></span>
            </div>
        </div>
        <div class="dashboard-container">
            <section class="hero-section">
                <div class="hero-bg-images">
                    <img src="images/v163_73.png" alt="Cake" class="hero-cake cake-tl" loading="lazy">
                    <img src="images/v163_118.png" alt="Cake" class="hero-cake cake-tr" loading="lazy">
                    <img src="images/v163_121.png" alt="Cake" class="hero-cake cake-bl" loading="lazy">
                    <img src="images/v163_123.png" alt="Cake" class="hero-cake cake-br" loading="lazy">
                </div>
                <div class="hero-title">
                    <span class="t1">Once tasted<br>you will</span>
                    <span class="t2">Never Forget!</span>
                </div>
                <a href="{{ route('shop') }}" wire:navigate class="btn-shop-now" style="text-decoration: none;">
                    shop now 
                    <div class="arrow-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </div>
                </a>
            </section>
            
            <section class="loaves-grid">
                <!-- STRAWBERRY CARD -->
                <div class="loaf-card" style="background: #FFDDDD;">
                    <div class="loaf-visuals">
                        <div class="loaf-bg-pattern" style="background-image: url('images/v136_341.avif');"></div>
                        <div class="loaf-typography">
                            <div class="flavor-name" style="color: #E92028;">Strawberry</div>
                            <div class="loaf-type">Vanilla<br>Loaf</div>
                            <div class="loaf-sub">w/ Cream Cheese</div>
                        </div>
                        <div class="loaf-images">
                            <img src="images/v163_47.png" alt="Packaged Loaf" class="cake-img-top" loading="lazy">
                        </div>
                    </div>

                    <a href="{{ route('shop') }}" wire:navigate class="loaf-card-btn red-variant" style="text-decoration: none;">
                        <div class="btn-left-img">
                            <img src="images/v136_331.png" alt="icon" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                        </div>
                        <span class="btn-text">shop strawberry</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>
                </div>

                <!-- BLUEBERRY CARD -->
                <div class="loaf-card" style="background: #E6E8FA;">
                    <div class="loaf-visuals">
                        <div class="loaf-bg-pattern" style="background-image: url('images/v136_450.jpg');"></div>
                        <div class="loaf-typography">
                            <div class="flavor-name" style="color: var(--accent-blue);">Blueberry</div>
                            <div class="loaf-type">Vanilla<br>Loaf</div>
                            <div class="loaf-sub">w/ Cream Cheese</div>
                        </div>
                        <div class="loaf-images">
                            <img src="images/v163_54.png" alt="Packaged Loaf" class="cake-img-top" loading="lazy">
                        </div>
                    </div>

                    <a href="{{ route('shop') }}" wire:navigate class="loaf-card-btn blue-variant" style="text-decoration: none;">
                        <div class="btn-left-img">
                            <img src="images/v136_312.png" alt="icon" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                        </div>
                        <span class="btn-text">shop blueberry</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>
                </div>

                <!-- MANGO CARD -->
                <div class="loaf-card" style="background: #FFF9D1;">
                    <div class="loaf-visuals">
                        <div class="loaf-bg-pattern" style="background-image: url('images/v136_371.jpg');"></div>
                        <div class="loaf-typography">
                            <div class="flavor-name" style="color: #fdbe02;">Mango</div>
                            <div class="loaf-type">Vanilla<br>Loaf</div>
                            <div class="loaf-sub">w/ Cream Cheese</div>
                        </div>
                        <div class="loaf-images">
                            <img src="images/v163_67.png" alt="Packaged Loaf" class="cake-img-top" loading="lazy">
                        </div>
                    </div>

                    <a href="{{ route('shop') }}" wire:navigate class="loaf-card-btn yellow-variant" style="text-decoration: none;">
                        <div class="btn-left-img">
                            <img src="images/v136_388.avif" alt="icon" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                        </div>
                        <span class="btn-text">shop mango</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </a>
                </div>
            </section>
            
            <section class="banner-section">
                <div class="banner-box box-01">
                    <img src="images/v480_59.jpg" alt="Banner Image" loading="lazy">
                </div>
                <div class="banner-box box-02">
                    <div class="small-banner">
                        <img src="images/v480_78.avif" alt="Banner Image" loading="lazy">
                    </div>
                    <div class="small-banner">
                        <img src="images/v480_010.jpg" alt="Banner Image" loading="lazy">
                    </div>
                </div>
                <div class="banner-box box-03">
                    <img src="images/v210_84.png" alt="Banner Graphic" class="graphic-01" loading="lazy">
                    <div class="box-03-text">
                        <div class="t-line-01">Special</div>
                        <div class="t-line-02">Bakery</div>
                    </div>
                    <button class="banner-btn">
                        <span>shop now</span>
                        <div class="btn-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </button>
                </div>
            </section>
            
            <section class="info-grid">
                <div class="info-card-large">
                    <div class="info-card-text">
                        <div class="t1">The Goodness of</div>
                        <div class="t2">Traditional Bakery</div>
                    </div>
                    <img src="images/v500_127.jpeg" alt="Bakery" class="info-bg" loading="lazy">
                    <button class="info-btn">
                        <span>learn more</span>
                        <div class="btn-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </button>
                </div>
                <div class="info-card-small-stack">
                    <div class="info-card-small">
                        <div class="tag">FRESH</div>
                        <div class="t1">Baked with</div>
                        <div class="t2">Heart & Soul</div>
                    </div>
                    <div class="info-card-small-visual">
                        <div class="avatar-stack">
                            <img src="images/v500_222.png" alt="User" loading="lazy">
                            <img src="images/v500_010.jpeg" alt="User" loading="lazy">
                            <img src="images/v500_927.jpeg" alt="User" loading="lazy">
                        </div>
                        <div class="visual-text">
                            <div class="t1">5,000+</div>
                            <div class="t2">Happy Customers</div>
                        </div>
                        <div class="rating">
                            ★★★★★
                        </div>
                    </div>
                    <div class="info-card-small-accent">
                        <div class="accent-icon">🥖</div>
                        <div class="accent-text">Premium<br>Ingredients</div>
                    </div>
                </div>
                <div class="info-card-medium">
                    <div class="quote-row">
                        <br>
                        <a href="{{ route('shop') }}" wire:navigate>shop all products &rarr;</a>
                    </div>
                </div>
            </section>
            
            <section class="marquee-visual">
                <div class="marquee-title-box">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <path d="M30 0L37.5 22.5L60 30L37.5 37.5L30 60L22.5 37.5L0 30L22.5 22.5L30 0Z" fill="var(--accent-yellow)"/>
                    </svg>
                </div>
                <h2 class="section-heading">Discover Our<br>Delicious Carousel</h2>
                
                <div class="scrolling-images-container">
                    <div class="scrolling-images-track">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                    </div>
                </div>

                <div class="scrolling-images-container reverse">
                    <div class="scrolling-images-track">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                        <img src="images/v600_2.png" alt="Product" loading="lazy">
                        <img src="images/v600_1.png" alt="Product" loading="lazy">
                    </div>
                </div>
                
                <div class="marquee-title-box">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                        <path d="M30 0L37.5 22.5L60 30L37.5 37.5L30 60L22.5 37.5L0 30L22.5 22.5L30 0Z" fill="var(--accent-yellow)"/>
                    </svg>
                </div>
            </section>
            
            <section class="footer-visuals">
                <div class="f-img">
                    <img src="images/v901_001.png" alt="Pastry" loading="lazy">
                </div>
                <div class="f-img">
                    <img src="images/v901_003.png" alt="Pastry" loading="lazy">
                </div>
            </section>
        </div>
        
        @include('partials.Footer')
@endsection
