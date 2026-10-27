<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Our Delicacies | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('css/welcome.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">

    <!-- Register Service Worker for offline capability -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>

    <style>
        .shop-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 10px;
            box-sizing: border-box;
        }

        /* 1. HERO BANNER CAROUSEL */
        .banner-carousel-wrapper {
            margin: 16px 10px 32px 10px;
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(49, 6, 105, 0.1);
        }

        .banner-track {
            display: flex;
            transition: transform 0.4s ease-in-out;
            width: 100%;
        }

        .banner-slide {
            min-width: 100%;
            height: clamp(160px, 28vw, 340px);
            object-fit: cover;
            display: block;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dot.active {
            width: 24px;
            border-radius: 12px;
            background: #8A38F5;
        }

        /* 2. CATEGORY NAV PILLS */
        .category-nav-wrapper {
            margin-bottom: 28px;
            padding: 0 10px;
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: none; /* Firefox */
        }

        .category-nav-wrapper::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .category-pills {
            display: inline-flex;
            gap: 10px;
        }

        .cat-pill {
            padding: 10px 20px;
            border-radius: 30px;
            background: #F3F4F6;
            color: #4B5563;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1.5px solid transparent;
        }

        .cat-pill:hover, .cat-pill.active {
            background: #F3E8FF;
            color: #7E22CE;
            border-color: #C084FC;
        }

        /* 3. SECTION HEADINGS */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: clamp(1.25rem, 2vw, 1.65rem);
            color: #310669;
            margin: 0;
        }

        .section-link {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            color: #8A38F5;
            text-decoration: none;
        }

        .section-link:hover {
            text-decoration: underline;
        }

        /* 4. HORIZONTAL SCROLLER SHELF */
        .scroller-wrapper {
            position: relative;
            margin-bottom: 40px;
        }

        .horizontal-scroller {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 4px 10px 16px 10px;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            scrollbar-width: none;
        }

        .horizontal-scroller::-webkit-scrollbar {
            display: none;
        }

        /* 5. PRODUCT CARD DESIGN */
        .product-card {
            flex: 0 0 clamp(200px, 42vw, 240px);
            scroll-snap-align: start;
            background: #FFFFFF;
            border: 2px solid #F3F4F6;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            box-sizing: border-box;
            contain: layout style paint;
            will-change: transform, box-shadow;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: #E9D5FF;
            box-shadow: 0 12px 20px -5px rgba(126, 34, 206, 0.08);
        }

        .product-image-box {
            width: 100%;
            height: 180px;
            background: #F9FAFB;
            position: relative;
            overflow: hidden;
            contain: layout style paint;
        }

        .product-image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            contain: layout style paint;
        }

        .badge-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(49, 6, 105, 0.85);
            backdrop-filter: blur(4px);
            color: #FFFFFF;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.65rem;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 8px;
            letter-spacing: 0.5px;
        }

        .product-info {
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
        }

        .product-name {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.95rem;
            color: #1F2937;
            margin: 0;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            color: #8A38F5;
            margin-top: auto;
        }

        .btn-view-product {
            width: 100%;
            background: #F3E8FF;
            color: #7E22CE;
            border: none;
            padding: 10px;
            border-radius: 12px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.8rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            box-sizing: border-box;
            display: block;
            margin-top: 8px;
            transition: background 0.15s ease, color 0.15s ease;
            contain: layout style paint;
        }

        .btn-view-product:hover {
            background: #8A38F5;
            color: #FFFFFF;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .shop-container {
                padding: 0 8px;
            }

            .banner-carousel-wrapper {
                margin: 12px 8px 24px 8px;
                border-radius: 16px;
            }

            .banner-slide {
                height: clamp(140px, 35vw, 280px);
            }

            .category-nav-wrapper {
                padding: 0 8px;
                margin-bottom: 20px;
            }

            .cat-pill {
                padding: 8px 16px;
                font-size: 0.8rem;
            }

            .section-header {
                padding: 0 8px;
                margin-bottom: 12px;
            }

            .section-title {
                font-size: clamp(1.1rem, 3vw, 1.4rem);
            }

            .scroller-wrapper {
                margin-bottom: 28px;
            }

            .horizontal-scroller {
                gap: 12px;
                padding: 4px 8px 12px 8px;
            }

            .product-card {
                flex: 0 0 clamp(180px, 50vw, 220px);
            }

            .product-image-box {
                height: 160px;
            }

            .product-info {
                padding: 12px;
                gap: 4px;
            }

            .product-name {
                font-size: 0.9rem;
            }

            .product-price {
                font-size: 1rem;
            }

            .btn-view-product {
                padding: 8px;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .banner-carousel-wrapper {
                margin: 8px 4px 16px 4px;
                border-radius: 12px;
            }

            .banner-slide {
                height: clamp(120px, 45vw, 220px);
            }

            .category-pills {
                gap: 6px;
            }

            .cat-pill {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .section-title {
                font-size: clamp(1rem, 4vw, 1.2rem);
            }

            .horizontal-scroller {
                gap: 8px;
                padding: 4px 4px 8px 4px;
            }

            .product-card {
                flex: 0 0 clamp(160px, 60vw, 200px);
            }

            .product-image-box {
                height: 140px;
            }

            .product-info {
                padding: 10px;
            }

            .product-name {
                font-size: 0.85rem;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .product-card:hover,
            .btn-view-product:hover,
            .cat-pill:hover {
                transform: none;
                box-shadow: none;
                border-color: inherit;
                background: inherit;
                color: inherit;
            }

            .product-card:active,
            .btn-view-product:active {
                transform: scale(0.98);
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .banner-track {
                transition: none;
            }

            .product-card,
            .btn-view-product,
            .cat-pill {
                transition: none;
            }
        }
    </style>
</head>
<body>

    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        <div class="shop-container">
            
            <!-- 1. PROMOTIONAL PHOTO BANNER CAROUSEL (10px Side Margin Layout) -->
            <div class="banner-carousel-wrapper">
                <div class="banner-track" id="bannerTrack">
                    <!-- Replace image paths with your created promotional banners -->
                    <img src="{{ asset('images/banners/banner1.png') }}" alt="Special Offer" class="banner-slide" loading="eager" fetchpriority="high" onerror="this.src='https://via.placeholder.com/1200x350/8A38F5/FFFFFF?text=New+Dedication+Cakes'">
                    <img src="{{ asset('images/banners/banner2.png') }}" alt="New Treats" class="banner-slide"  onerror="this.src='https://via.placeholder.com/1200x350/310669/FFFFFF?text=Freshly+Baked+Loaves'">
                </div>
                <div class="carousel-indicators">
                    <div class="dot active" onclick="goToSlide(0)"></div>
                    <div class="dot" onclick="goToSlide(1)"></div>
                </div>
            </div>

            <!-- 2. QUICK CATEGORY FILTER PILLS -->
            <div class="category-nav-wrapper">
                <div class="category-pills">
                    <a href="#all" class="cat-pill active">All Products</a>
                    <a href="#new" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><polyline points="22 7 13.41 15.59 9 11.18 4.41 15.75 2 13 20 7"></polyline></svg>New Arrivals</a>
                    <a href="#cakes" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>Dedication Cakes</a>
                    <a href="#loaves" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M4 19h16"></path><path d="M9 3v2"></path><path d="M15 3v2"></path><path d="M8 19a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2"></path></svg>Loaves</a>
                    <a href="#acetates" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path><path d="M12 8v8"></path><path d="M8 12h8"></path></svg>Acetates</a>
                    <a href="#treats" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M12 2a10 10 0 1 0 10 10"></path><path d="M16 8l4 4-4 4-4-4z"></path></svg>Treats & Choco Bars</a>
                    <a href="#cupcakes" class="cat-pill"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><path d="M12 3v18"></path><path d="M5 10h14"></path><path d="M5 14h14"></path><path d="M8 6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2H8V6z"></path><path d="M7 19a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2z"></path></svg>Cupcakes</a>
                </div>
            </div>

            <!-- 3. CATEGORY SCROLLER: NEW ARRIVALS & ORIGINALS -->
            <div class="scroller-wrapper" id="new">
                <div class="section-header">
                    <h2 class="section-title">New & Originals</h2>
                    <a href="#" class="section-link">View Category →</a>
                </div>
                <div class="horizontal-scroller">
                    <!-- Sample Product Card -->
                    <div class="product-card">
                        <div class="product-image-box">
                            <span class="badge-tag">NEW</span>
                            <img src="{{ asset('images/products/" sample-cake.jpg') }}" alt="Special Cake" onerror="this.src='https://via.placeholder.com/240x180/F3E8FF/7E22CE?text=Dedication+Cake'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Mango Bravo Dedication Cake</h3>
                            <div class="product-price">₱650.00</div>
                            <!-- Link directly to product details page later -->
                            <a href="#" class="btn-view-product">View Product</a>
                        </div>
                    </div>

                    <div class="product-card">
                        <div class="product-image-box">
                            <span class="badge-tag">ORIGINAL</span>
                            <img src="{{ asset('images/products/" sample-loaf.jpg') }}" alt="Special Loaf" onerror="this.src='https://via.placeholder.com/240x180/F3E8FF/7E22CE?text=Special+Loaf'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Ube Cheese Loaf Special</h3>
                            <div class="product-price">₱180.00</div>
                            <a href="#" class="btn-view-product">View Product</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. CATEGORY SCROLLER: DEDICATION CAKES -->
            <div class="scroller-wrapper" id="cakes">
                <div class="section-header">
                    <h2 class="section-title">Dedication Cakes</h2>
                    <a href="#" class="section-link">View All Cakes →</a>
                </div>
                <div class="horizontal-scroller">
                    <div class="product-card">
                        <div class="product-image-box">
                            <img src="{{ asset('images/products/" choco-cake.jpg') }}" alt="Choco Fudge" onerror="this.src='https://via.placeholder.com/240x180/F3E8FF/7E22CE?text=Choco+Fudge'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Rich Choco Fudge Dedication Cake</h3>
                            <div class="product-price">₱580.00</div>
                            <a href="#" class="btn-view-product">View Product</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. CATEGORY SCROLLER: TREATS & CHOCO BARS -->
            <div class="scroller-wrapper" id="treats">
                <div class="section-header">
                    <h2 class="section-title">Treats & Choco Bars</h2>
                    <a href="#" class="section-link">Explore Treats →</a>
                </div>
                <div class="horizontal-scroller">
                    <div class="product-card">
                        <div class="product-image-box">
                            <span class="badge-tag">BESTSELLER</span>
                            <img src="{{ asset('images/products/" bar.jpg') }}" alt="Choco Bar" onerror="this.src='https://via.placeholder.com/240x180/F3E8FF/7E22CE?text=Choco+Bar'">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Handcrafted Dark Choco Bar</h3>
                            <div class="product-price">₱95.00</div>
                            <a href="#" class="btn-view-product">View Product</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        // Banner Auto-Slide Logic
        let currentSlide = 0;
        const track = document.getElementById('bannerTrack');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = dots.length;

        function goToSlide(index) {
            currentSlide = index;
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlide);
            });
        }

        if (totalSlides > 1) {
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                goToSlide(currentSlide);
            }, 5000);
        }

        // Loader Dismissal Logic
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("globalLoader");
            if (loader) {
                loader.classList.add("fade-out");
                setTimeout(() => loader.remove(), 600);
            }
        });
    </script>
</body>
</html>