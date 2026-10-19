<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Order Ticket | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orders-create.css') }}">

    <!-- MapLibre GL JS for OpenFreeMap -->
    <link href="https://unpkg.com/maplibre-gl@^4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@^4.7.1/dist/maplibre-gl.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vision-route" content="{{ url('/api/vision/extract-colors') }}">
</head>
<body class="orders-page">

    <input type="checkbox" id="menu-toggle" class="hidden-element">
    @include('partials.side-menu')

    <div class="page-wrapper" id="page-wrapper">
        @include('partials.header')

        <div class="dashboard-container">
            <div class="create-wrapper">
                
                <div class="page-header">
                    <a href="{{ route('orders.index') }}" class="back-btn">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div>
                        <h1>Build Your Custom Bakery Ticket</h1>
                        <p>Customize your cake, cupcakes, or giveaway items step-by-step.</p>
                    </div>
                </div>

                <!-- 4-STEP WIZARD PROGRESS BAR -->
                <div class="wizard-progress-bar">
                    <div class="wizard-step-item active" id="indicatorStep1">
                        <div class="step-num">1</div>
                        <span class="step-label">Location & Pickup Shop</span>
                    </div>
                    <div class="wizard-step-divider"></div>
                    <div class="wizard-step-item" id="indicatorStep2">
                        <div class="step-num">2</div>
                        <span class="step-label">Item Category</span>
                    </div>
                    <div class="wizard-step-divider"></div>
                    <div class="wizard-step-item" id="indicatorStep3">
                        <div class="step-num">3</div>
                        <span class="step-label">Specifications</span>
                    </div>
                    <div class="wizard-step-divider"></div>
                    <div class="wizard-step-item" id="indicatorStep4">
                        <div class="step-num">4</div>
                        <span class="step-label">Schedule & Submit</span>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="form-errors-banner">
                        <ul class="form-errors-list">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" id="ticketForm">
                    @csrf
                    <input type="hidden" name="customer_name" value="{{ Auth::check() ? Auth::user()->name : 'Guest' }}">

                    <!-- STEP 1: LOCATION, OPENFREEMAP & SHOP SELECTION -->
                    <div class="wizard-step-panel active-step" id="wizardStep1">
                        <div class="duo-3d-card">
                            <div class="notice-banner-pickup">
                                <span class="notice-icon">📌</span>
                                <div>
                                    <strong>Store Pickup Policy:</strong>
                                    <p>All orders are for <strong>Store Pickup Only</strong>. Pin your location to find the nearest branch.</p>
                                </div>
                            </div>

                            <h3 class="card-heading" style="margin-top: 24px;">1. Pin Your Location</h3>
                            
                            <div class="input-group">
                                <label class="input-label">Search Location or Street Address</label>
                                <div class="location-search-row">
                                    <div class="search-input-wrapper" style="flex: 1; position: relative;">
                                        <input type="text" name="customer_location" id="customerLocationInput" class="clean-input" placeholder="Search town, barangay, or street address..." required autocomplete="off">
                                        <div id="searchResultsPopover" class="search-results-popover"></div>
                                    </div>
                                    <button type="button" class="btn-duo-outline" id="btnUseCurrentLocation" style="width: auto; flex-shrink: 0;">
                                        📍 Use Current Location
                                    </button>
                                </div>
                            </div>

                            <!-- OPENFREEMAP CONTAINER -->
                            <div class="map-picker-container">
                                <div id="openFreeMap" class="free-map-view"></div>
                                <div class="map-instruction-tag">💡 Drag the blue marker or click on the map to set your location pin.</div>
                            </div>

                            <!-- HIDDEN COORDINATE FIELDS -->
                            <input type="hidden" name="latitude" id="latInput">
                            <input type="hidden" name="longitude" id="lngInput">

                            <h3 class="card-heading" style="margin-top: 28px;">2. Choose Pickup Branch</h3>
                            <input type="hidden" name="pickup_branch" id="pickupBranchInput" value="main_shop">

                            <div class="shop-selection-grid">
                                <div class="shop-card active" id="shopCard_main" onclick="selectShop('main_shop')">
                                    <div class="shop-card-header">
                                        <div class="shop-icon">🏬</div>
                                        <div class="shop-info">
                                            <div class="shop-title-row">
                                                <span class="shop-name">Main Shop</span>
                                                <span class="shop-rec-badge hidden-element" id="recBadge_main">⭐ Recommended (Closest)</span>
                                            </div>
                                            <span class="shop-address">5XP5+CVM, San Miguel, Bulacan</span>
                                        </div>
                                    </div>
                                    <div class="shop-distance-tag" id="distTag_main">Calculating distance...</div>
                                </div>

                                <div class="shop-card" id="shopCard_sanjose" onclick="selectShop('san_jose')">
                                    <div class="shop-card-header">
                                        <div class="shop-icon">🏪</div>
                                        <div class="shop-info">
                                            <div class="shop-title-row">
                                                <span class="shop-name">San Jose Branch</span>
                                                <span class="shop-rec-badge hidden-element" id="recBadge_sanjose">⭐ Recommended (Closest)</span>
                                            </div>
                                            <span class="shop-address">4XWH+RXG, Norberto Street, San Miguel, 3011 Bulacan</span>
                                        </div>
                                    </div>
                                    <div class="shop-distance-tag" id="distTag_sanjose">Calculating distance...</div>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-nav-actions" style="justify-content: flex-end;">
                            <button type="button" class="btn-duo-3d" onclick="goToStep(2)">
                                Next: Choose Item Type &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: INITIAL ITEM CATEGORY SELECTION -->
                    <div class="wizard-step-panel" id="wizardStep2">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">What would you like to build first?</h3>
                            <p style="color: #6b7280; font-weight: 700; margin-top: -12px; margin-bottom: 20px;">
                                Choose your primary bakery product to start your ticket.
                            </p>

                            <div class="category-selection-grid">
                                <div class="category-select-card" onclick="selectInitialCategory('custom_cake')">
                                    <div class="category-icon">🎂</div>
                                    <div class="category-title">Custom Cake</div>
                                    <div class="category-desc">Custom shapes, tiers, flavors & uploaded reference extraction</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('cupcakes')">
                                    <div class="category-icon">🧁</div>
                                    <div class="category-title">Cupcakes</div>
                                    <div class="category-desc">Single/duo/rainbow swirl frosting, custom packaging & toppers</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('giveaway_loaf')">
                                    <div class="category-icon">🍞</div>
                                    <div class="category-title">Cheesy Taisan Loaf</div>
                                    <div class="category-desc">Bulk giveaway loaves with optional boxes, ribbons & thank you tags</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('giveaway_round')">
                                    <div class="category-icon">🍰</div>
                                    <div class="category-title">Round Giveaways</div>
                                    <div class="category-desc">Mini/standard round giveaway cakes with premium packaging</div>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" onclick="goToStep(1)" style="width: auto;">
                                &larr; Back to Location
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: CUSTOMIZATION & LINE ITEMS -->
                    <div class="wizard-step-panel" id="wizardStep3">
                        <h3 class="section-title">Configure Order Items</h3>
                        <div id="itemsContainer"></div>

                        <div class="duo-3d-card add-card-dashed">
                            <div class="add-card-title">Need to add another item to this ticket?</div>
                            <div class="add-buttons-grid">
                                <button type="button" class="btn-add-large" id="btnAddCake">
                                    <span>🎂</span> Custom Cake
                                </button>
                                <button type="button" class="btn-add-large" id="btnAddCupcakes">
                                    <span>🧁</span> Cupcakes
                                </button>
                                <button type="button" class="btn-add-large" id="btnAddLoaf">
                                    <span>🍞</span> Cheesy Taisan
                                </button>
                                <button type="button" class="btn-add-large" id="btnAddRound">
                                    <span>🍰</span> Round Giveaways
                                </button>
                            </div>
                        </div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" onclick="goToStep(2)" style="width: auto;">
                                &larr; Back to Item Types
                            </button>
                            <button type="button" class="btn-duo-3d" onclick="goToStep(4)">
                                Next: Schedule & Additionals &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: SCHEDULE & SUBMIT -->
                    <div class="wizard-step-panel" id="wizardStep4">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">Event & Pickup Schedule</h3>
                            
                            <div style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 14px; padding: 14px; margin-bottom: 20px;">
                                <strong style="color: #8A38F5; font-size: 0.95rem;">⏰ Store Opening Hours:</strong>
                                <ul style="margin: 6px 0 0 18px; padding: 0; color: #475569; font-weight: 700; font-size: 0.88rem;">
                                    <li><strong>Monday – Saturday:</strong> 7:30 AM to 5:00 PM</li>
                                    <li><strong>Sunday:</strong> 7:00 AM – 9:00 AM and 11:00 AM only</li>
                                    <li>Orders require at least <strong>2 days advance lead time</strong>.</li>
                                </ul>
                            </div>

                            <div class="card-grid-2">
                                <div class="input-group">
                                    <label class="input-label">Pickup Date</label>
                                    <div class="dropdown-wrapper" id="wrap_pickup_date">
                                        <div class="duo-picker-trigger" id="trigger_pickup_date">
                                            <span id="dateTriggerText">Select Pickup Date</span>
                                            <div class="dropdown-caret"></div>
                                        </div>
                                        <div class="duo-dropdown-popover popover-padding">
                                            <div class="cal-header">
                                                <button type="button" class="btn-duo-outline btn-cal-nav" id="btnPrevMonth">&lt;</button>
                                                <span id="calMonthYearTitle">October 2026</span>
                                                <button type="button" class="btn-duo-outline btn-cal-nav" id="btnNextMonth">&gt;</button>
                                            </div>
                                            <div class="cal-grid">
                                                <div class="cal-day-label">Su</div><div class="cal-day-label">Mo</div><div class="cal-day-label">Tu</div>
                                                <div class="cal-day-label">We</div><div class="cal-day-label">Th</div><div class="cal-day-label">Fr</div>
                                                <div class="cal-day-label">Sa</div>
                                            </div>
                                            <div class="cal-grid cal-dates-grid-margin" id="calDatesGrid"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pickup_date" id="hiddenDate" required>
                                </div>
                                
                                <div class="input-group">
                                    <label class="input-label">Pickup Time</label>
                                    <div class="dropdown-wrapper" id="wrap_pickup_time">
                                        <div class="duo-picker-trigger" id="trigger_pickup_time">
                                            <span id="timeTriggerText">08:00 AM</span>
                                            <div class="dropdown-caret"></div>
                                        </div>
                                        <div class="duo-dropdown-popover popover-padding">
                                            <div class="popover-title">Select Pickup Time Slot</div>
                                            <div class="time-picker-grid">
                                                <div>
                                                    <label class="input-label-sm">HOUR</label>
                                                    <div id="timeHourOpts"></div>
                                                </div>
                                                <div>
                                                    <label class="input-label-sm">MINUTE</label>
                                                    <div id="timeMinOpts"></div>
                                                </div>
                                                <div>
                                                    <label class="input-label-sm">AM/PM</label>
                                                    <div id="timeAmpmOpts"></div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-duo-3d btn-confirm-time" id="btnConfirmTime">Confirm Time</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pickup_time" id="hiddenTime" value="08:00 AM" required>
                                </div>
                            </div>

                            <div class="input-group input-group-top-margin">
                                <label class="input-label">Additional Instructions & Notes</label>
                                <textarea name="additional_notes" rows="3" class="clean-input" placeholder="e.g. Please handle with care, party starts at 2 PM, include extra candles..."></textarea>
                            </div>
                        </div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" onclick="goToStep(3)" style="width: auto;">
                                &larr; Back to Customization
                            </button>
                            <button type="submit" class="btn-duo-3d btn-submit-ticket">
                                Submit Ticket for Admin Pricing
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        window.closedDates = @json($closedDates ?? []);
    </script>
    <script src="{{ asset('js/orders-create.js') }}"></script>
</body>
</html>