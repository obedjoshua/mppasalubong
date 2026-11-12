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
                    <img src="images/v163_73.png" alt="Cake" class="hero-cake cake-tl">
                    <img src="images/v163_118.png" alt="Cake" class="hero-cake cake-tr">
                    <img src="images/v163_121.png" alt="Cake" class="hero-cake cake-bl">
                    <img src="images/v163_123.png" alt="Cake" class="hero-cake cake-br">
                </div>
                <div class="hero-title">
                    <span class="t1">Once tasted<br>you will</span>
                    <span class="t2">Never Forget!</span>
                </div>
                <a href="{{ route('shop') }}" class="btn-shop-now" style="text-decoration: none;">
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
                            <img src="images/v163_47.png" alt="Packaged Loaf" class="cake-img-top">
                        </div>
                    </div>

                    <div class="loaf-card-btn red-variant">
                        <div class="btn-left-img">
                            <img src="images/v136_331.png" alt="icon" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <span class="btn-text">shop strawberry</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
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
                            <img src="images/v163_54.png" alt="Packaged Loaf" class="cake-img-top">
                        </div>
                    </div>

                    <div class="loaf-card-btn blue-variant">
                        <div class="btn-left-img">
                            <img src="images/v136_312.png" alt="icon" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <span class="btn-text">shop blueberry</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
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
                            <img src="images/v163_67.png" alt="Packaged Loaf" class="cake-img-top">
                        </div>
                    </div>

                    <div class="loaf-card-btn yellow-variant">
                        <div class="btn-left-img">
                            <img src="images/v136_297.png" alt="icon" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <span class="btn-text">shop mango</span>
                        <div class="btn-right-circle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                    </div>
                </div>
            </section>

            <section class="upper-grid-3col">
                <div class="promo-tall-card">
                    <img src="images/v901_003.png" alt="Promo">
                </div>
                <div class="middle-stack">
                    <div class="baking-greatness-img-card">
                        <img src="images/v193_60.png" alt="Baking Greatness">
                    </div>
                    <div class="promo-landscape-card">
                        <img src="images/v500_010.jpeg" alt="New Addition">
                    </div>
                </div>
                <div class="customize-cakes">
                    <img src="images/v183_50.png" alt="Custom Tiered Cake" class="custom-cake-bg">
                    <div class="custom-cake-content">
                        <div class="custom-cake-text">
                            <div class="custom-sub">Customize your</div>
                            <div class="custom-main">Cakes</div>
                        </div>
                        <button class="loaf-card-btn purple-variant">
                            <span class="btn-text">order now</span>
                            <div class="btn-right-circle">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 50%; height: 50%; color: white;">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </div>
                        </button>
                    </div>
                </div>
            </section>

            <section class="lower-grid-2col">
                <div class="classic-yema">
                    <div class="yema-bg-text">
                        <div class="text-classic">CLASSIC</div>
                        <div class="yema-sub-group">
                            <div class="text-yema">Yema</div>
                            <div class="text-cheesecake">cheese cake</div>
                        </div>
                    </div>
                    <img src="images/v163_123.png" alt="Classic Yema" class="yema-cake-img">
                    <button class="btn-shop-yema">
                        <span class="btn-text">shop now</span>
                        <div class="btn-icon-brown">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    </button>
                </div>

                <div class="testimonials" id="testimonial-carousel">
                    <!-- Top Header with Title and Custom Match Arrows -->
                    <div class="testimonials-header">
                        <div class="testimonials-title">what customers <span class="italic-review">really</span> say</div>
                        <div class="testimonials-arrows">
                            <span class="carousel-arrow prev-btn">&lt;</span>
                            <span class="carousel-arrow next-btn">&gt;</span>
                        </div>
                    </div>
                
                    <!-- Horizontal Sliding Track Container -->
                    <div class="testimonial-slider-viewport">
                        <div class="testimonial-track" id="testimonial-track">
                            
                            <!-- SLIDE 1: BLUEBERRY REVIEW -->
                            <div class="testimonial-slide" style="background: #00E6EA;">
                                <div class="scattered-fruits">
                                    <img src="images/v136_312.png" alt="" class="fruit-item f-blur-sm f-1">
                                    <img src="images/v136_312.png" alt="" class="fruit-item f-blur-lg f-2">
                                    <img src="images/v136_312.png" alt="" class="fruit-item f-sharp f-3">
                                </div>
                                <div class="testimonial-content-layer">
                                    <div class="review-heading" style="color: var(--accent-blue);">AMAZING!</div>
                                    <div class="review-body" style="color: var(--accent-blue);">
                                        Not your ordinary cake in the market, the most beautiful and delicious cake in San Miguel and nearby towns.
                                    </div>
                                    <div class="review-author" style="color: var(--accent-blue);">-M. Calma</div>
                                </div>
                            </div>
                
                            <!-- SLIDE 2: STRAWBERRY REVIEW -->
                            <div class="testimonial-slide" style="background: #D8FF00;">
                                <div class="scattered-fruits">
                                    <img src="images/v136_331.png" alt="" class="fruit-item f-blur-lg f-1">
                                    <img src="images/v136_331.png" alt="" class="fruit-item f-sharp f-2">
                                </div>
                                <div class="testimonial-content-layer">
                                    <div class="review-heading" style="color: #BC1017;">SUUPERRB</div>
                                    <div class="review-body" style="color: #723A2A;">
                                        I was at the store and saw the loaves! I tried the Strawberry, and it was sooooooo good. I'll totally be getting more of this one!
                                    </div>
                                    <div class="review-author" style="color: #723A2A;">-Jimmy Diamond</div>
                                </div>
                            </div>
                
                            <!-- SLIDE 3: MANGO REVIEW -->
                            <div class="testimonial-slide" style="background: #FFD214;">
                                <div class="scattered-fruits">
                                    <img src="images/v136_297.png" alt="" class="fruit-item f-blur-sm f-1">
                                    <img src="images/v136_297.png" alt="" class="fruit-item f-sharp f-2">
                                </div>
                                <div class="testimonial-content-layer">
                                    <div class="review-heading" style="color: #9D3C00; font-family: 'Aloevera', serif; text-transform: lowercase;">will never forget</div>
                                    <div class="review-body" style="color: #7A3000;">
                                        it is really that 'once tasted you'll never forget', cause I tried the mango loaf and the taste of it is still in my head
                                    </div>
                                    <div class="review-author" style="color: #7A3000;">-V. Sempio</div>
                                </div>
                            </div>
                
                        </div>
                    </div>
                
                    <!-- Bottom Action Text -->
                    <div class="testimonials-footer-text">
                        Love what you’re savoring?<br>
                        <a href="#">Leave a review</a> and tell us what you think
                    </div>
                </div>
            </section>

            <section class="lifestyle-section">
                <div class="shape-divider-top">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                    </svg>
                </div>

                <h2 class="lifestyle-heading">
                    it's not just a pasalubong,<br>it's a lifestyle
                </h2>

                <!-- Top Carousel: Moving Left -->
                <div class="infinite-carousel-wrapper">
                    <div class="infinite-track track-left">
                        <img src="images/carousel/001.jpeg" alt="Lifestyle 1" class="carousel-img">
                        <img src="images/carousel/002.jpeg" alt="Lifestyle 2" class="carousel-img">
                        <img src="images/carousel/003.jpeg" alt="Lifestyle 3" class="carousel-img">
                        <img src="images/carousel/004.jpeg" alt="Lifestyle 4" class="carousel-img">
                        <img src="images/carousel/005.jpeg" alt="Lifestyle 5" class="carousel-img">
                        <img src="images/carousel/006.jpeg" alt="Lifestyle 6" class="carousel-img">
                        <img src="images/carousel/007.jpeg" alt="Lifestyle 7" class="carousel-img">
                        <img src="images/carousel/008.jpeg" alt="Lifestyle 8" class="carousel-img">
                        <img src="images/carousel/009.jpeg" alt="Lifestyle 9" class="carousel-img">
                        <img src="images/carousel/010.jpeg" alt="Lifestyle 10" class="carousel-img">

                        <!-- Duplicated 10 Images -->
                        <img src="images/carousel/001.jpeg" alt="Lifestyle 1" class="carousel-img">
                        <img src="images/carousel/002.jpeg" alt="Lifestyle 2" class="carousel-img">
                        <img src="images/carousel/003.jpeg" alt="Lifestyle 3" class="carousel-img">
                        <img src="images/carousel/004.jpeg" alt="Lifestyle 4" class="carousel-img">
                        <img src="images/carousel/005.jpeg" alt="Lifestyle 5" class="carousel-img">
                        <img src="images/carousel/006.jpeg" alt="Lifestyle 6" class="carousel-img">
                        <img src="images/carousel/007.jpeg" alt="Lifestyle 7" class="carousel-img">
                        <img src="images/carousel/008.jpeg" alt="Lifestyle 8" class="carousel-img">
                        <img src="images/carousel/009.jpeg" alt="Lifestyle 9" class="carousel-img">
                        <img src="images/carousel/010.jpeg" alt="Lifestyle 10" class="carousel-img">
                    </div>
                </div>

                <!-- Bottom Carousel: Moving Right -->
                <div class="infinite-carousel-wrapper">
                    <div class="infinite-track track-right">
                        <img src="images/carousel/011.jpeg" alt="Lifestyle 11" class="carousel-img">
                        <img src="images/carousel/012.jpeg" alt="Lifestyle 12" class="carousel-img">
                        <img src="images/carousel/013.jpeg" alt="Lifestyle 13" class="carousel-img">
                        <img src="images/carousel/014.jpeg" alt="Lifestyle 14" class="carousel-img">
                        <img src="images/carousel/015.jpeg" alt="Lifestyle 15" class="carousel-img">
                        <img src="images/carousel/016.jpeg" alt="Lifestyle 16" class="carousel-img">
                        <img src="images/carousel/017.jpeg" alt="Lifestyle 17" class="carousel-img">
                        <img src="images/carousel/018.jpeg" alt="Lifestyle 18" class="carousel-img">
                        <img src="images/carousel/019.jpeg" alt="Lifestyle 19" class="carousel-img">
                        <img src="images/carousel/020.jpeg" alt="Lifestyle 20" class="carousel-img">

                        <!-- Duplicated 10 Images -->
                        <img src="images/carousel/011.jpeg" alt="Lifestyle 11" class="carousel-img">
                        <img src="images/carousel/012.jpeg" alt="Lifestyle 12" class="carousel-img">
                        <img src="images/carousel/013.jpeg" alt="Lifestyle 13" class="carousel-img">
                        <img src="images/carousel/014.jpeg" alt="Lifestyle 14" class="carousel-img">
                        <img src="images/carousel/015.jpeg" alt="Lifestyle 15" class="carousel-img">
                        <img src="images/carousel/016.jpeg" alt="Lifestyle 16" class="carousel-img">
                        <img src="images/carousel/017.jpeg" alt="Lifestyle 17" class="carousel-img">
                        <img src="images/carousel/018.jpeg" alt="Lifestyle 18" class="carousel-img">
                        <img src="images/carousel/019.jpeg" alt="Lifestyle 19" class="carousel-img">
                        <img src="images/carousel/020.jpeg" alt="Lifestyle 20" class="carousel-img">
                    </div>
                </div>
                <div class="shape-divider-bottom">
                    <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                        <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                    </svg>
                </div>
            </section>

            <section class="two-col-promo-grid">
                <div class="promo-grid-item">
                    <img src="images/v500_222.png" alt="Triple Choco Cake" class="promo-grid-img">
                </div>
                <div class="promo-grid-item">
                    <img src="images/v500_127.jpeg" alt="Blueberry Vanilla Loaf" class="promo-grid-img">
                </div>
            </section>

        @include('partials.footer')
        </div>
@endsection

<!-- UNCORRUPTED FIXED BOTTOM-RIGHT AI ASSISTANT WIDGET -->
<style>
    /* Duolingo 3D Button Styles */
    .btn-duolingo-purple {
        background-color: #7C3AED !important;
        border: none !important;
        border-bottom: 4px solid #5B21B6 !important;
        border-radius: 12px !important;
        transition: transform 0.1s cubic-bezier(0.2, 0.8, 0.4, 1), border-bottom 0.1s ease, background-color 0.15s ease !important;
    }
    .btn-duolingo-purple:hover {
        background-color: #6D28D9 !important;
        transform: translateY(-2px) !important;
        border-bottom-width: 6px !important;
    }
    .btn-duolingo-purple:active {
        transform: translateY(3px) !important;
        border-bottom-width: 1px !important;
    }

    .btn-duolingo-send {
        background-color: #7C3AED !important;
        border: none !important;
        border-bottom: 3px solid #5B21B6 !important;
        border-radius: 10px !important;
        transition: transform 0.1s ease, border-bottom 0.1s ease, background-color 0.15s ease !important;
    }
    .btn-duolingo-send:hover {
        background-color: #6D28D9 !important;
        transform: translateY(-1px) !important;
        border-bottom-width: 4px !important;
    }
    .btn-duolingo-send:active {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }

    /* Unboxed Clean Circular Avatar */
    .avatar-unboxed {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50% !important;
        transform: scaleX(-1);
        display: block;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    /* Bottom Right Expansion Drawer Animations */
    #chat-popup {
        transition: opacity 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        opacity: 0;
        transform: translateY(24px) scale(0.92);
        pointer-events: none;
        transform-origin: bottom right;
    }
    #chat-popup.open {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Messenger Typing Indicator Dots Animation */
    .messenger-typing {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 6px;
    }
    .messenger-typing span {
        width: 7px;
        height: 7px;
        background-color: #9ca3af;
        border-radius: 50%;
        display: inline-block;
        animation: messengerBounce 1.4s infinite ease-in-out both;
    }
    .messenger-typing span:nth-child(1) { animation-delay: 0s; }
    .messenger-typing span:nth-child(2) { animation-delay: 0.2s; }
    .messenger-typing span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes messengerBounce {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        40% { transform: scale(1); opacity: 1; }
    }

    /* Product UI Card Interactive Styling */
    .product-card-item {
        background-color: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        margin-top: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
    }
    .product-card-item:hover {
        border-color: #7C3AED;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(124, 58, 237, 0.15);
    }
    .product-card-btn {
        display: inline-block;
        background-color: #7C3AED;
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: bold;
        text-decoration: none;
        margin-top: 8px;
        border-bottom: 2px solid #5B21B6;
    }

    /* Ticket Card Interactive Item */
    .ticket-card-item {
        background-color: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px;
        margin-top: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
    }
    .ticket-card-item:hover {
        border-color: #7C3AED;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(124, 58, 237, 0.15);
    }

    /* Toast Notification */
    #chat-toast-pop {
        position: fixed !important;
        bottom: 96px !important;
        right: 24px !important;
        z-index: 2147483646 !important;
        background-color: #ffffff;
        color: #1f2937;
        padding: 10px 14px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.25);
        border: 1.5px solid #7C3AED;
        font-family: sans-serif;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        opacity: 0;
        transform: translateY(12px) scale(0.95);
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    #chat-toast-pop.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
</style>

<!-- Floating Toast Message (Fixed Bottom-Right) -->
<div id="chat-toast-pop" onclick="toggleChatPopup()">
    <div style="width: 28px; height: 28px; flex-shrink: 0;">
        <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive" class="avatar-unboxed">
    </div>
    <span>Hi! Need help with your pasalubong orders? 👋</span>
</div>

<!-- Floating Trigger Button (Fixed Bottom-Right) -->
<div id="chat-widget-btn" onclick="toggleChatPopup()" style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 2147483647 !important; cursor: pointer;">
    <div class="btn-duolingo-purple" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; padding: 2px;">
        <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Ask Jive" class="avatar-unboxed">
    </div>
</div>

<!-- Floating Chat Drawer Popup (Fixed Bottom-Right) -->
<div id="chat-popup" style="position: fixed !important; bottom: 96px !important; right: 24px !important; width: 380px; max-width: 90vw; height: 520px; z-index: 2147483647 !important; border-radius: 16px; overflow: hidden; background-color: #ffffff; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.22); display: flex; flex-direction: column;">
    
    <!-- Header (Electric Purple) -->
    <div style="background-color: #7C3AED; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-radius: 16px 16px 0 0; box-sizing: border-box; width: 100%; flex-shrink: 0; border-bottom: 2px solid #6D28D9;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; flex-shrink: 0;">
                <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive" class="avatar-unboxed">
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="color: #ffffff; font-family: 'Recoleta-Bold', serif; font-size: 17px; font-weight: bold; line-height: 1.2;">Ask Jive</span>
                <span style="color: rgba(255, 255, 255, 0.8); font-size: 11px; font-family: sans-serif;">Powered by Nemotron AI</span>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('chat.page') }}" title="Expand to Full Page" style="color: #ffffff; text-decoration: none; padding: 6px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.18); border-radius: 10px; transition: background 0.15s ease;" onmouseover="this.style.background='rgba(255,255,255,0.32)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.707l4.096-4.096a.5.5 0 0 0 0-.707zm4.344-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.793l-4.096 4.096a.5.5 0 0 0 0 .707z"/>
                </svg>
            </a>
            <button type="button" onclick="toggleChatPopup()" style="background: rgba(255, 255, 255, 0.18); border: none; color: #ffffff; font-size: 18px; font-weight: bold; cursor: pointer; padding: 4px 10px; border-radius: 10px; line-height: 1; transition: background 0.15s ease;" onmouseover="this.style.background='rgba(255,255,255,0.32)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">&times;</button>
        </div>
    </div>

    <!-- Scrollable Messages Body -->
    <div id="popup-chat-body" style="flex: 1 1 auto; padding: 16px; background-color: #f8f9fa; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; box-sizing: border-box;">
        <div style="display: flex; gap: 10px; align-items: flex-start;">
            <div style="width: 34px; height: 34px; flex-shrink: 0;">
                <img src="{{ asset('images/ask_jive_avatar.png') }}" alt="Jive" class="avatar-unboxed">
            </div>
            <div style="background-color: #ffffff; padding: 12px 14px; border-radius: 14px; border: 1px solid #e5e7eb; max-width: 82%; box-shadow: 0 3px 6px rgba(0,0,0,0.03);">
                <p style="margin: 0; font-size: 13px; color: #1f2937; line-height: 1.45; font-family: sans-serif;">Hi! I'm Jive. How can I help you with your pasalubong orders or pending tickets today?</p>
            </div>
        </div>
    </div>

    <!-- Input Footer -->
    <div style="padding: 12px 14px; background-color: #ffffff; border-top: 1px solid #e5e7eb; border-radius: 0 0 16px 16px; flex-shrink: 0;">
        <form id="popup-chat-form" onsubmit="handlePopupSend(event)" style="display: flex; margin: 0; gap: 8px;">
            <input type="text" id="popup-chat-input" placeholder="Type your question..." autocomplete="off" style="flex: 1; border: 1.5px solid #d1d5db; padding: 10px 14px; border-radius: 12px; outline: none; font-size: 13px; font-family: sans-serif; background-color: #f9fafb; transition: border-color 0.15s ease;" onfocus="this.style.borderColor='#7C3AED'" onblur="this.style.borderColor='#d1d5db'">
            <button type="submit" class="btn-duolingo-send" style="color: #ffffff; padding: 10px 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.138L3.088 7.97l9.043-5.394Zm-6.527 8.01 7.51-7.51-5.394 9.043-2.116-3.333Z"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    let popupHistory = [];
    let hasAnsweredOnePlusOne = false;
    const avatarUrl = "{{ asset('images/ask_jive_avatar.png') }}";

    function toggleChatPopup() {
        const popup = document.getElementById('chat-popup');
        const toast = document.getElementById('chat-toast-pop');
        popup.classList.toggle('open');
        if (popup.classList.contains('open')) {
            toast.classList.remove('show');
        }
    }

    setInterval(() => {
        const popup = document.getElementById('chat-popup');
        const toast = document.getElementById('chat-toast-pop');
        if (!popup.classList.contains('open')) {
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 5000);
        }
    }, 10000);

    function showMessengerTyping() {
        const body = document.getElementById('popup-chat-body');
        const typingHtml = `
            <div id="messenger-typing-indicator" style="display: flex; gap: 10px; align-items: flex-start;">
                <div style="width: 34px; height: 34px; flex-shrink: 0;">
                    <img src="${avatarUrl}" alt="Jive" class="avatar-unboxed">
                </div>
                <div style="background-color: #ffffff; padding: 10px 14px; border-radius: 14px; border: 1px solid #e5e7eb; box-shadow: 0 3px 6px rgba(0,0,0,0.03);">
                    <div class="messenger-typing">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        `;
        body.insertAdjacentHTML('beforeend', typingHtml);
        body.scrollTop = body.scrollHeight;
    }

    function removeMessengerTyping() {
        const indicator = document.getElementById('messenger-typing-indicator');
        if (indicator) indicator.remove();
    }

    // Direct trigger when clicking "Ask Jive about this ticket"
    function askJiveAboutTicket(ticketId) {
        const input = document.getElementById('popup-chat-input');
        input.value = `What is the status of ticket ${ticketId}?`;
        document.getElementById('popup-chat-form').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    }

    // Helper: Parse [PRODUCT_CARD] and [TICKET_CARD] blocks into interactive dual-action elements
    function parseCards(rawText) {
        let parsed = rawText;

        // 1. Parse Product Cards
        const productRegex = /\[PRODUCT_CARD\]([\s\S]*?)\[END_PRODUCT_CARD\]/g;
        let prodMatch;
        while ((prodMatch = productRegex.exec(rawText)) !== null) {
            const content = prodMatch[1];
            const name = (content.match(/Name:\s*(.*)/i) || [])[1]?.trim() || 'Product';
            const price = (content.match(/Price:\s*(.*)/i) || [])[1]?.trim() || '';
            const branch = (content.match(/Branch:\s*(.*)/i) || [])[1]?.trim() || '';
            const desc = (content.match(/Description:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (content.match(/Link:\s*(.*)/i) || [])[1]?.trim() || '/shop';

            const cardHtml = `
                <div class="product-card-item">
                    <div style="font-size: 14px; font-weight: bold; color: #1f2937;">${name}</div>
                    ${price ? `<div style="font-size: 13px; font-weight: 700; color: #7C3AED; margin-top: 2px;">${price}</div>` : ''}
                    ${branch ? `<div style="font-size: 11px; color: #6b7280; margin-top: 2px;">📍 ${branch}</div>` : ''}
                    ${desc ? `<div style="font-size: 12px; color: #4b5563; margin-top: 4px;">${desc}</div>` : ''}
                    <a href="${link}" class="product-card-btn">View Product &rarr;</a>
                </div>
            `;
            parsed = parsed.replace(prodMatch[0], cardHtml);
        }

        // 2. Parse Ticket Cards with Dual Actions (View Details & Ask Jive)
        const ticketRegex = /\[TICKET_CARD\]([\s\S]*?)\[END_TICKET_CARD\]/g;
        let tickMatch;
        while ((tickMatch = ticketRegex.exec(rawText)) !== null) {
            const content = tickMatch[1];
            const id = (content.match(/ID:\s*(.*)/i) || [])[1]?.trim() || 'TCK-000';
            const subject = (content.match(/Subject:\s*(.*)/i) || [])[1]?.trim() || 'Support Ticket';
            const status = (content.match(/Status:\s*(.*)/i) || [])[1]?.trim() || 'Pending';
            const date = (content.match(/Date:\s*(.*)/i) || [])[1]?.trim() || '';
            const link = (content.match(/Link:\s*(.*)/i) || [])[1]?.trim() || `/tickets/${id.replace('TCK-', '')}`;

            const ticketHtml = `
                <div class="ticket-card-item" style="cursor: default;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <strong style="color:#7C3AED; font-size:13px;">${id}</strong>
                        <span style="font-size:11px; background:#fef3c7; color:#d97706; padding:2px 6px; border-radius:6px; font-weight:bold;">${status}</span>
                    </div>
                    <div style="font-size:12px; color:#374151; font-weight:600; margin-top:4px;">${subject}</div>
                    ${date ? `<div style="font-size:10px; color:#9ca3af; margin-top:4px;">Created: ${date}</div>` : ''}
                    
                    <div style="display:flex; gap:6px; margin-top:10px;">
                        <button type="button" onclick="window.location.href='${link}'" style="flex:1; background:#7C3AED; color:#fff; border:none; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:bold; cursor:pointer;">
                            View Details ↗
                        </button>
                        <button type="button" onclick="askJiveAboutTicket('${id}')" style="flex:1; background:#f3e8ff; color:#7C3AED; border:1px solid #7C3AED; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:bold; cursor:pointer;">
                            Ask Jive 💬
                        </button>
                    </div>
                </div>
            `;
            parsed = parsed.replace(tickMatch[0], ticketHtml);
        }

        return parsed;
    }

    function handlePopupSend(e) {
        e.preventDefault();
        const input = document.getElementById('popup-chat-input');
        const message = input.value.trim();
        if (!message) return;

        appendPopupMessage('user', message);
        input.value = '';

        const lowerMsg = message.toLowerCase();

        // 1. Guardrail: 1+1 Special Handling
        if (lowerMsg.includes('1+1') || lowerMsg.includes('1 + 1') || lowerMsg.includes('one plus one')) {
            showMessengerTyping();
            setTimeout(() => {
                removeMessengerTyping();
                if (!hasAnsweredOnePlusOne) {
                    hasAnsweredOnePlusOne = true;
                    appendPopupMessage('assistant', "1 + 1 = 2! Please note that I can only help with pasalubong orders and support tickets. Please stay on topic! 😊");
                } else {
                    appendPopupMessage('assistant', "I have already answered 1+1! Please stay on topic regarding your pasalubong orders and support requests.");
                }
            }, 600);
            return;
        }

        // 2. Standard AI Response Flow
        showMessengerTyping();
        popupHistory.push({ role: 'user', content: message });

        fetch("{{ route('chat.send_message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ messages: popupHistory })
        })
        .then(res => {
            if (!res.ok) throw new Error("HTTP error " + res.status);
            return res.json();
        })
        .then(data => {
            removeMessengerTyping();
            if (data.success && data.reply) {
                appendPopupMessage('assistant', data.reply);
                popupHistory.push({ role: 'assistant', content: data.reply });
            } else {
                appendPopupMessage('assistant', "I can only assist you with pasalubong products, orders, and support tickets. Please stay on topic! 🧁");
            }
        })
        .catch(err => {
            removeMessengerTyping();
            console.error("Chat Error:", err);
            appendPopupMessage('assistant', "Please stay on topic with your pasalubong questions or ticket inquiries! 🧁");
        });
    }

    function appendPopupMessage(role, text) {
        const body = document.getElementById('popup-chat-body');
        const isUser = role === 'user';
        const avatarHtml = `
            <div style="width: 34px; height: 34px; flex-shrink: 0;">
                <img src="${avatarUrl}" alt="Jive" class="avatar-unboxed">
            </div>
        `;

        const processedText = !isUser ? parseCards(text) : text;

        const msgHtml = `
            <div style="display: flex; gap: 10px; align-items: flex-start; ${isUser ? 'justify-content: flex-end;' : ''}">
                ${!isUser ? avatarHtml : ''}
                <div style="background-color: ${isUser ? '#7C3AED' : '#ffffff'}; color: ${isUser ? '#ffffff' : '#1f2937'}; padding: 12px 14px; border-radius: 14px; border: 1px solid ${isUser ? '#7C3AED' : '#e5e7eb'}; max-width: 82%; box-shadow: 0 3px 6px rgba(0,0,0,0.03);">
                    <div style="margin: 0; font-size: 13px; line-height: 1.45; font-family: sans-serif;">${processedText}</div>
                </div>
            </div>
        `;
        body.insertAdjacentHTML('beforeend', msgHtml);
        body.scrollTop = body.scrollHeight;
    }
</script>