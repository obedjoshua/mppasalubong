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
    {{-- Any other page-specific JS can go here --}}
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
                            <div id="map" style="width: 100%; height: 400px; border-radius: 12px; margin-top: 15px; border: 2px solid #eee;"></div>
                            
                            <!-- Lat/Lng Hidden Fields -->
                            <input type="hidden" name="customer_lat" id="customerLat">
                            <input type="hidden" name="customer_lng" id="customerLng">

                            <h3 class="card-heading" style="margin-top: 30px;">2. Select Pickup Shop</h3>
                            <input type="hidden" name="pickup_branch" id="selectedBranchInput">

                            <div class="branch-selector-grid">
                                <!-- BRANCH A -->
                                <div class="branch-card" data-branch="Branch A (San Jose)" data-lat="15.79" data-lng="120.99">
                                    <div class="branch-icon">🏢</div>
                                    <div class="branch-info">
                                        <div class="branch-name-row">
                                            <span class="b-name">Branch A</span>
                                            <span class="b-dist">-- km</span>
                                        </div>
                                        <span class="b-loc">San Jose City</span>
                                    </div>
                                    <div class="branch-check"></div>
                                </div>

                                <!-- BRANCH B -->
                                <div class="branch-card" data-branch="Branch B (Lupao)" data-lat="15.85" data-lng="120.91">
                                    <div class="branch-icon">🏢</div>
                                    <div class="branch-info">
                                        <div class="branch-name-row">
                                            <span class="b-name">Branch B</span>
                                            <span class="b-dist">-- km</span>
                                        </div>
                                        <span class="b-loc">Lupao</span>
                                    </div>
                                    <div class="branch-check"></div>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-footer">
                            <button type="button" class="btn-wizard-next" id="toStep2">
                                Next: Choose Item Category ➔
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: CATEGORY SELECTION -->
                    <div class="wizard-step-panel" id="wizardStep2">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">3. What are we baking?</h3>
                            <p class="step-desc">Pick a category to see available options and specifications.</p>

                            <div class="category-grid">
                                <div class="cat-card" data-category="Cake">
                                    <div class="cat-icon">🎂</div>
                                    <div class="cat-info">
                                        <div class="cat-name-row">
                                            <span class="c-name">Custom Cake</span>
                                            <span class="c-tag">Popular</span>
                                        </div>
                                        <span class="c-desc">Birthday, Wedding, or any occasion.</span>
                                    </div>
                                    <div class="cat-check"></div>
                                </div>

                                <div class="cat-card" data-category="Cupcakes">
                                    <div class="cat-icon">🧁</div>
                                    <div class="cat-info">
                                        <div class="cat-name-row">
                                            <span class="c-name">Cupcakes</span>
                                            <span class="c-tag">Sweet</span>
                                        </div>
                                        <span class="c-desc">Box of 6, 12, or custom sets.</span>
                                    </div>
                                    <div class="cat-check"></div>
                                </div>
                                <!-- Add more categories as needed -->
                            </div>
                        </div>

                        <div class="wizard-footer">
                            <button type="button" class="btn-wizard-back" id="backToStep1">Back</button>
                            <button type="button" class="btn-wizard-next" id="toStep3">Next: Specifications ➔</button>
                        </div>
                    </div>

                    <!-- STEP 3: SPECIFICATIONS -->
                    <div class="wizard-step-panel" id="wizardStep3">
                        <h3 class="card-heading">4. Specifications</h3>
                        <div class="specs-divider"></div>

                        <div class="specs-scroll-area">
                            <div class="upload-section">
                                <strong>Reference Images (Max 3)</strong>
                                <ul class="upload-list" id="imageList">
                                    <!-- Dynamic Image Items -->
                                </ul>

                                <div class="upload-controls">
                                    <div class="file-input-wrapper">
                                        <label class="btn-upload">
                                            <span>📷 Upload Image</span>
                                            <input type="file" id="imageInput" accept="image/*" class="hidden-element">
                                        </label>
                                        <div id="imageLoader" class="inline-loader hidden-element"></div>
                                    </div>
                                    <div class="color-palette" id="colorPalette">
                                        <!-- Extracted Colors -->
                                    </div>
                                    <div id="imageCountNote" class="upload-note">No images uploaded yet.</div>
                                </div>
                            </div>

                            <div class="details-section">
                                <label class="input-label">Describe your order</label>
                                <div class="textarea-wrapper">
                                    <div class="textarea-header">
                                        <span class="hint">Themes, colors, flavor, etc.</span>
                                        <div class="char-count">0 / 500</div>
                                    </div>
                                    <div class="textarea-container">
                                        <div class="mention-suggestions hidden-element">
                                            <div class="suggestion-item">#Chocolate</div>
                                            <div class="suggestion-item">#Vanilla</div>
                                            <div class="suggestion-item">#Birthday</div>
                                        </div>
                                        <textarea name="specifications" id="specsInput" class="custom-textarea" placeholder="Example: 2-tier chocolate cake with floral design..."></textarea>
                                    </div>
                                    <button type="button" class="btn-ai-assist" id="aiAssistBtn">✨ AI Suggestions</button>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-footer">
                            <button type="button" class="btn-wizard-back" id="backToStep2">Back</button>
                            <button type="button" class="btn-wizard-next" id="toStep4">Next: Schedule & Submit ➔</button>
                        </div>
                    </div>

                    <!-- STEP 4: SCHEDULE & SUBMIT -->
                    <div class="wizard-step-panel" id="wizardStep4">
                        <div class="duo-3d-card">
                            <h3 class="card-heading">5. Finalize & Schedule</h3>
                            <div class="summary-preview"></div>
                            
                            <div class="schedule-inputs">
                                <div class="input-group">
                                    <strong>Pickup Date</strong>
                                    <ul class="date-options">
                                        <!-- Dynamic Dates -->
                                    </ul>
                                </div>
                                <div class="input-group">
                                    <label class="input-label">Additional Notes (Optional)</label>
                                    <textarea name="additional_notes" class="clean-textarea" placeholder="Anything else we should know?"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-footer">
                            <button type="button" class="btn-wizard-back" id="backToStep3">Back</button>
                            <button type="submit" class="btn-wizard-submit">Submit Order Ticket</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
