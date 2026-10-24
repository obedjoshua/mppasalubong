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