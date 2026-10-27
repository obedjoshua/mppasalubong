@extends('layouts.app')

@section('title', 'Custom Order Ticket')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders-create.css') }}?v={{ time() }}">
    <!-- MapLibre GL CSS -->
    <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" crossorigin="anonymous" />
@endpush

@push('scripts')
    <!-- MapLibre GL JS -->
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/orders-create.js') }}?v={{ time() }}" defer></script>
    <meta name="vision-route" content="{{ url('/api/vision/extract-colors') }}">
@endpush

@section('content')
<div class="order-wizard-page">
    <div class="wizard-container">

        <!-- Offline Status Banner -->
        <div id="offlineIndicator" class="offline-badge hidden-element">
            <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 4px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            Offline Mode Enabled — Your order ticket will be queued locally and auto-submitted when reconnected.
        </div>

        <!-- Wizard Navigation Steps Header -->
        <div class="wizard-step-header">
            <div id="indicatorStep1" class="step-indicator-item active" onclick="goToStep(1)">
                <span class="step-num">1</span>
                <span class="step-text">Location & Branch</span>
            </div>
            <div class="step-connector-line"></div>
            <div id="indicatorStep2" class="step-indicator-item" onclick="goToStep(2)">
                <span class="step-num">2</span>
                <span class="step-text">Select Products</span>
            </div>
            <div class="step-connector-line"></div>
            <div id="indicatorStep3" class="step-indicator-item" onclick="goToStep(3)">
                <span class="step-num">3</span>
                <span class="step-text">Design & Customization</span>
            </div>
            <div class="step-connector-line"></div>
            <div id="indicatorStep4" class="step-indicator-item" onclick="goToStep(4)">
                <span class="step-num">4</span>
                <span class="step-text">Pickup Schedule</span>
            </div>
        </div>

        <form id="ticketForm" action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Customer Name Fallback for Controller Validation -->
            <input type="hidden" name="customer_name" value="{{ Auth::check() ? Auth::user()->name : 'Valued Customer' }}">

            <!-- Hidden Location Coordinates & Branch -->
            <input type="hidden" id="latInput" name="customer_lat" value="">
            <input type="hidden" id="lngInput" name="customer_lng" value="">
            <input type="hidden" id="pickupBranchInput" name="pickup_branch" value="main_shop">

            <!-- STEP 1: LOCATION & BRANCH SELECTOR -->
            <div id="wizardStep1" class="wizard-step-panel active-step">
                <div class="section-title-wrapper">
                    <h2>1. Pin Your Location</h2>
                    <p class="section-subtitle">Search your town/barangay or place a pin on the map to calculate the closest pickup branch and preview the pickup route.</p>
                </div>

                <div class="search-input-wrapper">
                    <label class="input-label" for="customerLocationInput">Search Location or Street Address</label>
                    <div class="search-input-flex">
                        <input type="text" id="customerLocationInput" name="customer_address" class="clean-input" placeholder="Search town, barangay, or street address..." autocomplete="off">
                        <button type="button" id="btnUseCurrentLocation" class="btn-duo-outline btn-locate-me">
                            <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-right: 6px;"><circle cx="12" cy="12" r="10"></circle><line x1="22" y1="12" x2="18" y2="12"></line><line x1="6" y1="12" x2="2" y2="12"></line><line x1="12" y1="6" x2="12" y2="2"></line><line x1="12" y1="22" x2="12" y2="18"></line></svg>
                            Use Current Location
                        </button>
                    </div>
                    <div id="searchResultsPopover" class="search-results-popover"></div>
                </div>

                <!-- OpenFreeMap / MapLibre GL Canvas Map Container -->
                <div class="map-picker-container" style="position: relative; width: 100%; min-height: 380px; margin-top: 16px;">
                    <div id="openFreeMap" class="free-map-view" style="width: 100%; height: 380px; min-height: 380px; display: block; background-color: #e5e7eb; position: relative; z-index: 1;"></div>
                    <div class="map-instruction-tag">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Drag the blue marker or click anywhere on the map to set location & recalculate route.
                    </div>
                </div>

                <!-- Open Drive Route in Google Maps Action Button -->
                <div style="margin-top: 12px; display: flex; justify-content: flex-end; align-items: center;">
                    <button type="button" id="btnOpenGoogleMaps" onclick="openGoogleMapsRoute()" class="btn-duo-outline" style="background: #ffffff; color: #1e293b; border: 2px solid #e2e8f0; border-bottom: 4px solid #cbd5e1; border-radius: 12px; font-weight: 800; padding: 10px 18px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#ea4335" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"></path><circle cx="12" cy="9" r="2.5"></circle></svg>
                        Open Drive Route in Google Maps
                    </button>
                </div>

                <div class="section-title-wrapper mt-32">
                    <h2>2. Select Pickup Shop</h2>
                </div>

                <div class="shop-selection-grid">
                    <div id="shopCard_main" class="shop-card active" onclick="selectShop('main_shop', true)">
                        <div class="shop-card-header">
                            <div class="shop-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; vertical-align: text-bottom; margin-right: 6px;"><path d="M3 21h18"></path><path d="M9 8h1"></path><path d="M9 12h1"></path><path d="M9 16h1"></path><path d="M14 8h1"></path><path d="M14 12h1"></path><path d="M14 16h1"></path><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path></svg>
                                Main Branch
                                <span id="recBadge_main" class="rec-badge hidden-element">NEAREST</span>
                            </div>
                        </div>
                        <p class="shop-address">Poblacion, San Miguel, Bulacan</p>
                        <div id="distTag_main" class="dist-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: text-bottom; margin-right: 4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Calculating route...
                        </div>
                    </div>

                    <div id="shopCard_sanjose" class="shop-card" onclick="selectShop('san_jose', true)">
                        <div class="shop-card-header">
                            <div class="shop-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; vertical-align: text-bottom; margin-right: 6px;"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9v.01"></path><path d="M9 12v.01"></path><path d="M9 15v.01"></path><path d="M9 18v.01"></path></svg>
                                San Jose Branch
                                <span id="recBadge_sanjose" class="rec-badge hidden-element">NEAREST</span>
                            </div>
                        </div>
                        <p class="shop-address">Norberto St., San Jose, San Miguel</p>
                        <div id="distTag_sanjose" class="dist-tag">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: text-bottom; margin-right: 4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Calculating route...
                        </div>
                    </div>
                </div>

                <div class="wizard-actions-bar">
                    <div></div>
                    <button type="button" class="btn-primary-action" onclick="goToStep(2)">
                        Next: Select Products
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-left: 6px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 2: CATEGORY SELECTOR -->
            <div id="wizardStep2" class="wizard-step-panel">
                <div class="section-title-wrapper">
                    <h2>Choose Product Category</h2>
                    <p class="section-subtitle">Pick an initial product type to add to your custom order ticket.</p>
                </div>

                <div class="category-selection-grid">
                    <div class="category-card" onclick="selectInitialCategory('custom_cake')">
                        <div class="category-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px;"><path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"></path><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"></path><path d="M2 21h20"></path><path d="M7 8v3"></path><path d="M12 8v3"></path><path d="M17 8v3"></path><path d="M7 4h.01"></path><path d="M12 4h.01"></path><path d="M17 4h.01"></path></svg>
                        </div>
                        <div class="category-title">Custom Cake Design</div>
                        <p class="category-desc">Custom layered cakes with AI image spec extraction & isometric preview.</p>
                    </div>

                    <div class="category-card" onclick="selectInitialCategory('cupcakes')">
                        <div class="category-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px;"><path d="M18 11V6a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v5"></path><path d="M4 11h16a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6H9a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1z"></path><path d="M12 2v2"></path></svg>
                        </div>
                        <div class="category-title">Cupcakes</div>
                        <p class="category-desc">Boxed or individual giveaway cupcakes with custom icing styles.</p>
                    </div>

                    <div class="category-card" onclick="selectInitialCategory('giveaway_loaf')">
                        <div class="category-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px;"><rect x="3" y="8" width="18" height="12" rx="3"></rect><path d="M6 8V6a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v2"></path></svg>
                        </div>
                        <div class="category-title">Cheesy Taisan Loaf</div>
                        <p class="category-desc">Fresh baked Taisan loaf giveaways with optional gift box & ribbon.</p>
                    </div>

                    <div class="category-card" onclick="selectInitialCategory('giveaway_round')">
                        <div class="category-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#8A38F5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width: 48px; height: 48px;"><circle cx="12" cy="12" r="9"></circle><path d="M12 3v9l6 3"></path></svg>
                        </div>
                        <div class="category-title">Round Giveaways</div>
                        <p class="category-desc">6-inch or 8-inch round Yema Cheese Cake and Triple Choco cakes.</p>
                    </div>
                </div>

                <div class="wizard-actions-bar">
                    <button type="button" class="btn-duo-outline" onclick="goToStep(1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-right: 6px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </button>
                    <div></div>
                </div>
            </div>

            <!-- STEP 3: CUSTOMIZATION ENGINE -->
            <div id="wizardStep3" class="wizard-step-panel">
                <div class="section-title-wrapper">
                    <h2>Design & Product Specs</h2>
                    <p class="section-subtitle">Customize specifications, message toppers, and upload reference photos.</p>
                </div>

                <div id="itemsContainer" class="items-stack"></div>

                <div class="add-more-products-bar">
                    <span class="add-more-title">+ Add Another Item to Ticket:</span>
                    <div class="add-buttons-flex">
                        <button type="button" id="btnAddCake" class="btn-duo-outline">+ Custom Cake</button>
                        <button type="button" id="btnAddCupcakes" class="btn-duo-outline">+ Cupcakes</button>
                        <button type="button" id="btnAddLoaf" class="btn-duo-outline">+ Taisan Loaf</button>
                        <button type="button" id="btnAddRound" class="btn-duo-outline">+ Round Cake</button>
                    </div>
                </div>

                <div class="wizard-actions-bar">
                    <button type="button" class="btn-duo-outline" onclick="goToStep(2)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-right: 6px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </button>
                    <button type="button" class="btn-primary-action" onclick="goToStep(4)">
                        Next: Schedule Pickup
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-left: 6px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 4: SCHEDULE -->
            <div id="wizardStep4" class="wizard-step-panel">
                <div class="section-title-wrapper">
                    <h2>Pickup Date & Time Schedule</h2>
                    <p class="section-subtitle">Select when you will pick up your order at the branch.</p>
                </div>

                <div class="duo-picker-grid">
                    <div class="duo-dropdown-wrapper" id="wrap_pickup_date">
                        <label class="input-label">Pickup Date</label>
                        <div class="duo-picker-trigger" id="trigger_pickup_date">
                            <span id="dateTriggerText">Select Date</span>
                            <div class="dropdown-caret"></div>
                        </div>
                        <div class="duo-dropdown-popover calendar-popover">
                            <div class="cal-header-bar">
                                <button type="button" id="btnPrevMonth" class="cal-nav-btn">‹</button>
                                <span id="calMonthYearTitle" class="cal-month-title"></span>
                                <button type="button" id="btnNextMonth" class="cal-nav-btn">›</button>
                            </div>
                            <div class="cal-days-header">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div id="calDatesGrid" class="cal-dates-grid"></div>
                        </div>
                        <input type="hidden" id="hiddenDate" name="pickup_date" value="">
                    </div>

                    <div class="duo-dropdown-wrapper" id="wrap_pickup_time">
                        <label class="input-label">Pickup Time</label>
                        <div class="duo-picker-trigger" id="trigger_pickup_time">
                            <span id="timeTriggerText">08:00 AM</span>
                            <div class="dropdown-caret"></div>
                        </div>
                        <div class="duo-dropdown-popover clock-picker-popover"></div>
                        <input type="hidden" id="hiddenTime" name="pickup_time" value="08:00 AM">
                    </div>
                </div>

                <div class="wizard-actions-bar">
                    <button type="button" class="btn-duo-outline" onclick="goToStep(3)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: text-bottom; margin-right: 6px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </button>
                    <button type="submit" id="btnSubmitTicket" class="btn-primary-action btn-submit-final">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; vertical-align: text-bottom; margin-right: 6px;"><path d="M12 2v4"></path><path d="M12 18v4"></path><path d="M4.93 4.93l2.83 2.83"></path><path d="M16.24 16.24l2.83 2.83"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="M4.93 19.07l2.83-2.83"></path><path d="M16.24 7.76l2.83-2.83"></path></svg>
                        Submit Custom Order Ticket
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection