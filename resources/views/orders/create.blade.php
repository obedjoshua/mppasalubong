@extends('layouts.app')

@section('title', 'Custom Order Ticket | Masaganang Pagpapala')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders-create.css') }}">
    <!-- MapLibre GL JS for OpenFreeMap -->
    <link href="https://unpkg.com/maplibre-gl@^4.7.1/dist/maplibre-gl.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/maplibre-gl@^4.7.1/dist/maplibre-gl.js"></script>
    <meta name="vision-route" content="{{ url('/api/vision/extract-colors') }}">
@endpush

@section('content')
        <div class="dashboard-container">
            <div class="create-wrapper">
                
                <div class="page-header">
                    <a href="{{ route('orders.index') }}" wire:navigate class="back-btn">
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

                            <!-- Map Container -->
                            <div class="map-picker-container">
                                <div id="openFreeMap" class="free-map-view"></div>
                                <div class="map-instruction-tag">💡 Drag the blue marker or click on the map to place your location pin.</div>
                            </div>
                            
                            <!-- Lat/Lng Hidden Fields -->
                            <input type="hidden" name="customer_lat" id="latInput">
                            <input type="hidden" name="customer_lng" id="lngInput">

                            <h3 class="card-heading" style="margin-top: 30px;">2. Select Pickup Shop</h3>
                            <input type="hidden" name="pickup_branch" id="pickupBranchInput" value="main_shop">

                            <div class="shop-selection-grid">
                                <!-- MAIN BRANCH -->
                                <div class="shop-card active" id="shopCard_main" onclick="selectShop('main_shop')">
                                    <div class="shop-card-header">
                                        <div class="shop-icon">🏬</div>
                                        <div class="shop-info">
                                            <div class="shop-title-row">
                                                <span class="shop-name">Main Branch</span>
                                                <span class="shop-rec-badge" id="recBadge_main">Nearest</span>
                                            </div>
                                            <span class="shop-address">Poblacion, San Miguel, Bulacan</span>
                                        </div>
                                    </div>
                                    <div class="shop-distance-tag" id="distTag_main">📍 Calculating distance...</div>
                                </div>

                                <!-- SAN JOSE BRANCH -->
                                <div class="shop-card" id="shopCard_sanjose" onclick="selectShop('san_jose')">
                                    <div class="shop-card-header">
                                        <div class="shop-icon">🏪</div>
                                        <div class="shop-info">
                                            <div class="shop-title-row">
                                                <span class="shop-name">San Jose Branch</span>
                                                <span class="shop-rec-badge hidden-element" id="recBadge_sanjose">Nearest</span>
                                            </div>
                                            <span class="shop-address">Norberto St., San Jose, San Miguel</span>
                                        </div>
                                    </div>
                                    <div class="shop-distance-tag" id="distTag_sanjose">📍 Calculating distance...</div>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-nav-actions" style="justify-content: flex-end;">
                            <button type="button" class="btn-duo-3d" onclick="goToStep(2)">
                                Next: Choose Item Category ➔
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: CATEGORY SELECTION -->
                    <div class="wizard-step-panel" id="wizardStep2">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">3. What are we baking for you today?</h3>
                            <p style="color: #64748b; font-weight: 700; margin-bottom: 20px;">Choose a category to start building your order.</p>

                            <div class="category-selection-grid">
                                <div class="category-select-card" onclick="selectInitialCategory('custom_cake')">
                                    <div class="category-icon">🎂</div>
                                    <div class="category-title">Custom Cake</div>
                                    <div class="category-desc">Celebration cakes with AI image color & spec extraction.</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('cupcakes')">
                                    <div class="category-icon">🧁</div>
                                    <div class="category-title">Cupcakes</div>
                                    <div class="category-desc">Boxed sets or single giveaway cupcakes with custom swirl icing.</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('giveaway_loaf')">
                                    <div class="category-icon">🍞</div>
                                    <div class="category-title">Cheesy Taisan Loaf</div>
                                    <div class="category-desc">Soft baked loaves with optional box and ribbon packaging.</div>
                                </div>

                                <div class="category-select-card" onclick="selectInitialCategory('giveaway_round')">
                                    <div class="category-icon">🍰</div>
                                    <div class="category-title">Round Giveaways</div>
                                    <div class="category-desc">Yema cheese and chocolate cakes for event favors.</div>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" style="width: auto;" onclick="goToStep(1)">➔ Back to Location</button>
                        </div>
                    </div>

                    <!-- STEP 3: SPECIFICATIONS -->
                    <div class="wizard-step-panel" id="wizardStep3">
                        <div class="add-buttons-grid" style="margin-bottom: 20px;">
                            <button type="button" class="btn-add-large" id="btnAddCake"><span>🎂</span> Add Custom Cake</button>
                            <button type="button" class="btn-add-large" id="btnAddCupcakes"><span>🧁</span> Add Cupcakes</button>
                            <button type="button" class="btn-add-large" id="btnAddLoaf"><span>🍞</span> Add Taisan Loaf</button>
                            <button type="button" class="btn-add-large" id="btnAddRound"><span>🍰</span> Add Round Giveaway</button>
                        </div>

                        <!-- Dynamic Items Container -->
                        <div id="itemsContainer"></div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" style="width: auto;" onclick="goToStep(2)">➔ Back to Category</button>
                            <button type="button" class="btn-duo-3d" onclick="goToStep(4)">Next: Schedule & Submit ➔</button>
                        </div>
                    </div>

                    <!-- STEP 4: SCHEDULE & SUBMIT -->
                    <div class="wizard-step-panel" id="wizardStep4">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">5. Pickup Date & Time</h3>
                            
                            <div class="card-grid-2">
                                <!-- PICKUP DATE SELECTOR -->
                                <div class="input-group">
                                    <label class="input-label">Select Pickup Date</label>
                                    <div class="duo-dropdown-wrapper" id="wrap_pickup_date">
                                        <div class="duo-picker-trigger" id="trigger_pickup_date">
                                            <span id="dateTriggerText">Choose Pickup Date</span>
                                            <div class="dropdown-caret"></div>
                                        </div>
                                        <div class="duo-dropdown-popover popover-padding">
                                            <div class="cal-header">
                                                <button type="button" class="btn-duo-outline btn-cal-nav" id="btnPrevMonth">◀</button>
                                                <span id="calMonthYearTitle">Month Year</span>
                                                <button type="button" class="btn-duo-outline btn-cal-nav" id="btnNextMonth">▶</button>
                                            </div>
                                            <div class="cal-grid">
                                                <div class="cal-day-label">Su</div>
                                                <div class="cal-day-label">Mo</div>
                                                <div class="cal-day-label">Tu</div>
                                                <div class="cal-day-label">We</div>
                                                <div class="cal-day-label">Th</div>
                                                <div class="cal-day-label">Fr</div>
                                                <div class="cal-day-label">Sa</div>
                                            </div>
                                            <div class="cal-grid cal-dates-grid-margin" id="calDatesGrid"></div>
                                        </div>
                                        <input type="hidden" name="pickup_date" id="hiddenDate" required>
                                    </div>
                                </div>

                                <!-- PICKUP TIME ANALOG PICKER -->
                                <div class="input-group">
                                    <label class="input-label">Select Pickup Time</label>
                                    <div class="duo-dropdown-wrapper" id="wrap_pickup_time">
                                        <div class="duo-picker-trigger" id="trigger_pickup_time">
                                            <span id="timeTriggerText">08:00 AM</span>
                                            <div class="dropdown-caret"></div>
                                        </div>
                                        <div class="duo-dropdown-popover"></div>
                                        <input type="hidden" name="pickup_time" id="hiddenTime" value="08:00 AM" required>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group input-group-top-margin">
                                <label class="input-label">Order Notes or Special Instructions (Optional)</label>
                                <textarea name="additional_notes" class="clean-input" rows="3" placeholder="Specify landmark details or pickup notes..."></textarea>
                            </div>
                        </div>

                        <div class="wizard-nav-actions">
                            <button type="button" class="btn-duo-outline" style="width: auto;" onclick="goToStep(3)">➔ Back to Specs</button>
                            <button type="submit" class="btn-duo-3d btn-submit-ticket" id="btnSubmitTicket">
                                🚀 Submit Order Ticket
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection