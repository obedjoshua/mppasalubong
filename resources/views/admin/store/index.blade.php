@extends('layouts.portal')

@section('title', 'Shop Content | Masaganang Pagpapala Pasalubong')

@section('content')
<style>
    @font-face {
        font-family: 'modern-bold';
        src: url('/fonts/samsungsharpsans-bold.otf') format('opentype');
        font-display: swap;
    }

    @font-face {
        font-family: 'Recoleta-Bold';
        src: url('/fonts/Recoleta Bold.otf') format('opentype');
        font-display: swap;
    }

    .portal-content-wrapper,
    .portal-main {
        background: #FFFFFF !important;
        font-family: 'modern-bold', sans-serif;
    }

    .store-dash-wrap {
        width: 100%;
        padding: 4px 15px 60px 15px;
        box-sizing: border-box;
    }

    /* Header matching User Dashboard */
    .store-header-area {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .store-greeting-sub {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(18px, 2.2vw, 24px);
        color: #310669;
        line-height: 1;
        margin-bottom: 4px;
    }

    .store-display-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(34px, 5.5vw, 64px);
        color: #8a38f5;
        margin: 0;
        line-height: 1;
    }

    .store-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .pill-btn-purple {
        background: #8a38f5;
        color: #FFFFFF !important;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        text-decoration: none;
        padding: 11px 22px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border: none;
        box-shadow: 0px 5px 0px #560db6;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }

    .pill-btn-purple:hover {
        transform: translateY(2px);
        box-shadow: 0px 3px 0px #560db6;
    }

    .pill-btn-green {
        background: #10B981;
        color: #FFFFFF !important;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        text-decoration: none;
        padding: 11px 22px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border: none;
        box-shadow: 0px 5px 0px #059669;
        transition: transform 0.1s ease, box-shadow 0.1s ease;
    }

    .pill-btn-green:hover {
        transform: translateY(2px);
        box-shadow: 0px 3px 0px #059669;
    }

    .pill-btn-outline {
        background: #FFFFFF;
        color: #310669 !important;
        border: 2.5px solid #E5E7EB;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 16px;
        text-transform: uppercase;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .pill-btn-outline:hover {
        border-color: #310669;
        background: #F9FAFB;
    }

    /* 4 Stat Boxes */
    .store-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .store-stat-box {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .store-stat-box.accent-purple { border-color: #D8B4FE; background: #FAF5FF; }
    .store-stat-box.accent-green  { border-color: #A7F3D0; background: #ECFDF5; }
    .store-stat-box.accent-blue   { border-color: #BFDBFE; background: #EFF6FF; }
    .store-stat-box.accent-amber  { border-color: #FDE68A; background: #FFFBEB; }

    .store-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
    }

    .store-stat-value {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(26px, 3vw, 38px);
        color: #310669;
        line-height: 1.1;
        margin-top: 6px;
    }

    .store-stat-box.accent-green .store-stat-value { color: #065F46; }
    .store-stat-box.accent-blue .store-stat-value  { color: #1E40AF; }
    .store-stat-box.accent-amber .store-stat-value { color: #92400E; }

    /* Main Content Grid */
    .store-top-grid {
        display: grid;
        grid-template-columns: 1.45fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
        align-items: start;
    }

    .store-col-stack {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .dash-card {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: clamp(18px, 2.5vw, 26px);
        box-shadow: none !important;
        box-sizing: border-box;
    }

    .dash-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .dash-card-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(20px, 2.2vw, 26px);
        color: #310669;
        margin: 0;
        line-height: 1.1;
    }

    /* Stock-Style Revenue / Orders Chart */
    .stock-filter-bar {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #F9FAFB;
        border: 2px solid #E5E7EB;
        border-radius: 14px;
        padding: 4px;
    }

    .stock-range-btn {
        background: transparent;
        border: none;
        border-radius: 10px;
        padding: 6px 11px;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        color: #6B7280;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .stock-range-btn:hover {
        color: #310669;
        background: #F3E8FF;
    }

    .stock-range-btn.active {
        background: #8a38f5;
        color: #FFFFFF;
    }

    .stock-summary-strip {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1.5px dashed #E5E7EB;
    }

    .stock-big-metric {
        font-family: 'Recoleta-Bold', serif;
        font-size: 26px;
        color: #1F2937;
    }

    .stock-delta-badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 8px;
        background: #DEF7EC;
        color: #03543F;
    }

    .stock-chart-container {
        position: relative;
        width: 100%;
    }

    .stock-svg {
        display: block;
        width: 100%;
        height: 220px;
        overflow: visible;
        cursor: crosshair;
    }

    .stock-tooltip {
        position: absolute;
        top: 6px;
        left: 10px;
        background: #310669;
        color: #FFFFFF;
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.12s ease;
        z-index: 10;
    }

    /* Category Inventory Bars */
    .cat-bar-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .cat-bar-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .cat-bar-meta {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #1F2937;
    }

    .cat-bar-track {
        width: 100%;
        height: 12px;
        background: #F3E8FF;
        border-radius: 999px;
        overflow: hidden;
    }

    .cat-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #8a38f5, #10B981);
        border-radius: 999px;
    }

    /* Daily Store Log Calendar */
    .cal-nav-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cal-nav-btn {
        background: #FAF5FF;
        border: 2px solid #E9D5FF;
        color: #8a38f5;
        border-radius: 10px;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .cal-nav-btn:hover {
        background: #8a38f5;
        color: #FFFFFF;
        border-color: #8a38f5;
    }

    .cal-month-label {
        font-family: 'Recoleta-Bold', serif;
        font-size: 18px;
        color: #310669;
        min-width: 145px;
        text-align: center;
    }

    .cal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        margin-bottom: 6px;
        text-align: center;
        font-size: 11px;
        color: #6B7280;
        text-transform: uppercase;
    }

    .cal-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }

    .cal-day-cell {
        min-height: 66px;
        background: #FFFFFF;
        border: 2px solid #F3F4F6;
        border-radius: 14px;
        padding: 7px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        cursor: pointer;
        text-align: left;
        transition: border-color 0.15s ease, background 0.15s ease, transform 0.1s ease;
    }

    .cal-day-cell:hover {
        border-color: #8a38f5;
        background: #FAF5FF;
        transform: translateY(-1px);
    }

    .cal-day-cell.is-outside {
        opacity: 0.35;
        background: #F9FAFB;
    }

    .cal-day-cell.is-today {
        border-color: #8a38f5;
        background: #FAF5FF;
    }

    .cal-day-cell.has-logs {
        border-color: #D8B4FE;
        background: #FAF5FF;
    }

    .cal-day-cell.has-purchases {
        border-color: #A7F3D0;
        background: #F0FDF4;
    }

    .cal-day-num {
        font-size: 13px;
        color: #1F2937;
    }

    .cal-log-pill {
        background: #8a38f5;
        color: #FFFFFF;
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 999px;
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cal-day-cell.has-purchases .cal-log-pill {
        background: #10B981;
    }

    /* Tables in User Dashboard Card Style */
    .store-table {
        width: 100%;
        border-collapse: collapse;
    }

    .store-table thead th {
        padding: 12px 14px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
        border-bottom: 2px solid #F3F4F6;
        text-align: left;
    }

    .store-table tbody td {
        padding: 14px;
        border-bottom: 1.5px solid #F3F4F6;
        font-size: 13px;
        color: #1F2937;
        vertical-align: middle;
    }

    .store-table tbody tr:hover {
        background: #FAF5FF;
    }

    .status-chip {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        text-transform: uppercase;
    }

    .status-chip.is-published { background: #DEF7EC; color: #03543F; }
    .status-chip.is-hidden    { background: #FEF3C7; color: #92400E; }
    .status-chip.is-removed   { background: #FEE2E2; color: #991B1B; }

    .tbl-btn {
        padding: 6px 12px;
        border-radius: 10px;
        font-family: 'modern-bold', sans-serif;
        font-size: 11px;
        text-decoration: none;
        border: 2px solid #E5E7EB;
        background: #FFFFFF;
        color: #310669;
        cursor: pointer;
        display: inline-block;
    }

    .tbl-btn:hover {
        border-color: #8a38f5;
        background: #FAF5FF;
    }

    .tbl-btn-danger {
        border-color: #FCA5A5;
        background: #FEF2F2;
        color: #DC2626;
    }

    .tbl-btn-danger:hover {
        border-color: #DC2626;
        background: #FEE2E2;
    }

    /* Daily Store Log Modal */
    .log-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.48);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.18s ease, visibility 0.18s ease;
    }

    .log-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .log-modal-card {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 22px;
        width: 100%;
        max-width: 580px;
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .log-modal-head {
        padding: 20px 24px;
        border-bottom: 2px solid #F3F4F6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .log-modal-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: 22px;
        color: #310669;
        margin: 0;
    }

    .log-modal-close {
        background: #FAF5FF;
        border: 2px solid #E9D5FF;
        color: #8a38f5;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        cursor: pointer;
    }

    .log-modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .log-entry-row {
        border: 2px solid #E5E7EB;
        border-radius: 16px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .log-type-tag {
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .log-type-tag.badge-purchase { background: #DEF7EC; color: #03543F; }
    .log-type-tag.badge-product  { background: #F3E8FF; color: #6B21A8; }
    .log-type-tag.badge-banner   { background: #FEF3C7; color: #92400E; }

    @media (max-width: 1100px) {
        .store-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .store-top-grid   { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .store-stats-grid { grid-template-columns: 1fr; }
        .cal-day-cell     { min-height: 54px; padding: 5px; }
    }
</style>

<div class="store-dash-wrap">
    <!-- 1. HEADER -->
    <div class="store-header-area">
        <div>
            <div class="store-greeting-sub">Store Management</div>
            <h1 class="store-display-title">Shop Content</h1>
        </div>
        <div class="store-header-actions">
            <a class="pill-btn-purple" href="{{ route('admin.store.products.create') }}">+ Add Product</a>
            <a class="pill-btn-green" href="{{ route('admin.store.banners.create') }}">+ Create Banner</a>
            <a class="pill-btn-outline" href="{{ route('shop') }}" target="_blank" rel="noopener">View Shop &nearr;</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #DEF7EC; border: 2px solid #31C48D; border-radius: 16px; padding: 12px 18px; color: #03543F; font-size: 13px; margin-bottom: 16px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #FEE2E2; border: 2px solid #F87171; border-radius: 16px; padding: 12px 18px; color: #991B1B; font-size: 13px; margin-bottom: 16px;">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- 2. STORE KPI STAT CARDS -->
    <div class="store-stats-grid">
        <div class="store-stat-box accent-purple">
            <div class="store-stat-label">Published Products</div>
            <div class="store-stat-value">{{ number_format($storeStats['activeProducts']) }}</div>
        </div>
        <div class="store-stat-box accent-green">
            <div class="store-stat-label">Shop Sales Revenue</div>
            <div class="store-stat-value">₱{{ number_format($storeStats['totalShopRevenue'], 2) }}</div>
        </div>
        <div class="store-stat-box accent-blue">
            <div class="store-stat-label">Active Hero Banners</div>
            <div class="store-stat-value">{{ number_format($storeStats['activeBanners']) }}</div>
        </div>
        <div class="store-stat-box accent-amber">
            <div class="store-stat-label">Low / Out of Stock</div>
            <div class="store-stat-value">{{ number_format($storeStats['lowOrOutOfStock']) }}</div>
        </div>
    </div>

    <!-- 3. DAILY STORE LOG CALENDAR & STORE CHARTS -->
    <div class="store-top-grid">
        <!-- Left: Daily Store Activity & Sales Calendar -->
        <div class="dash-card">
            <div class="dash-card-header">
                <div>
                    <h2 class="dash-card-title">Daily Store Log Calendar</h2>
                    <span style="font-size: 12px; color: #6B7280;">Click any date to inspect daily purchases, stock edits &amp; banner logs</span>
                </div>
                <div class="cal-nav-group">
                    <button type="button" class="cal-nav-btn" id="storeCalPrev" aria-label="Previous Month">&larr;</button>
                    <span class="cal-month-label" id="storeCalMonthLabel">November 2026</span>
                    <button type="button" class="cal-nav-btn" id="storeCalNext" aria-label="Next Month">&rarr;</button>
                </div>
            </div>

            <div class="cal-weekdays">
                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
            </div>
            <div class="cal-days-grid" id="storeCalendarGrid"></div>
        </div>

        <!-- Right: Store Revenue/Orders Trend Chart + Category Stock Breakdown -->
        <div class="store-col-stack">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Shop Revenue Trend</h2>
                        <span style="font-size: 12px; color: #6B7280;">Daily shop sales performance</span>
                    </div>
                    <div class="stock-filter-bar" id="salesRangeFilters">
                        <button type="button" class="stock-range-btn" data-range="7D">7D</button>
                        <button type="button" class="stock-range-btn active" data-range="30D">30D</button>
                        <button type="button" class="stock-range-btn" data-range="90D">90D</button>
                        <button type="button" class="stock-range-btn" data-range="ALL">ALL</button>
                    </div>
                </div>

                <div class="stock-summary-strip">
                    <span class="stock-big-metric" id="salesRangeTotal">₱0.00</span>
                    <span class="stock-delta-badge" id="salesRangeOrders">0 shop orders</span>
                </div>

                <div class="stock-chart-container">
                    <div class="stock-tooltip" id="salesChartTooltip"></div>
                    <svg class="stock-svg" id="storeSalesChart" viewBox="0 0 760 220" role="img" aria-label="Shop revenue history chart">
                        <defs>
                            <linearGradient id="storeSalesFill" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#10B981" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#10B981" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="30" x2="760" y2="30"/>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="85" x2="760" y2="85"/>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="140" x2="760" y2="140"/>
                        <line stroke="#E5E7EB" x1="0" y1="185" x2="760" y2="185"/>
                        <polygon id="storeSalesArea" fill="url(#storeSalesFill)" points="0,185 760,185"/>
                        <polyline id="storeSalesLine" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="0,185 760,185"/>
                        <line id="salesHoverLine" x1="0" y1="20" x2="0" y2="185" stroke="#10B981" stroke-width="1.5" stroke-dasharray="3 3" style="display: none;"/>
                        <circle id="salesHoverDot" cx="0" cy="0" r="5" fill="#10B981" stroke="#FFFFFF" stroke-width="2" style="display: none;"/>
                        <g id="storeSalesLabels" fill="#6B7280" font-size="11"></g>
                    </svg>
                </div>
            </div>

            <!-- Category Stock Distribution Chart -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Stock by Category</h2>
                        <span style="font-size: 12px; color: #6B7280;">Active catalog units across categories</span>
                    </div>
                </div>

                @php($maxStock = max(1, (int) $categoryBreakdown->max('stock')))
                <div class="cat-bar-list">
                    @forelse($categoryBreakdown as $cat)
                        <div class="cat-bar-item">
                            <div class="cat-bar-meta">
                                <span>{{ $cat['category'] }} ({{ $cat['count'] }} items)</span>
                                <span style="color: #8a38f5;">{{ number_format($cat['stock']) }} units</span>
                            </div>
                            <div class="cat-bar-track">
                                <div class="cat-bar-fill" style="width: {{ round(($cat['stock'] / $maxStock) * 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: #6B7280; margin: 0;">No active product categories yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 4. PRODUCTS CATALOG TABLE -->
    <div class="dash-card" style="margin-bottom: 16px;">
        <div class="dash-card-header">
            <div>
                <h2 class="dash-card-title">Products Catalog</h2>
                <span style="font-size: 12px; color: #6B7280;">Published products appear in the public shop and chatbot lookup</span>
            </div>
            <a class="pill-btn-purple" href="{{ route('admin.store.products.create') }}">Add Product</a>
        </div>

        <div style="overflow-x: auto;">
            <table class="store-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div style="color: #310669; font-size: 14px;">{{ $product->name }}</div>
                            <div style="font-size: 11px; color: #6B7280;">{{ $product->slug }}</div>
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>₱{{ number_format((float) $product->price, 2) }}</td>
                        <td>{{ $product->stock_quantity }} · {{ $product->stock_status }}</td>
                        <td>
                            @if($product->trashed())
                                <span class="status-chip is-removed">Removed</span>
                            @elseif($product->is_active)
                                <span class="status-chip is-published">Published</span>
                            @else
                                <span class="status-chip is-hidden">Hidden</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @if(!$product->trashed())
                                    <a class="tbl-btn" href="{{ route('admin.store.products.edit', $product) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.store.products.destroy', $product) }}" onsubmit="return confirm('Remove this product from the shop?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tbl-btn tbl-btn-danger">Remove</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.store.products.restore', $product->id) }}">
                                        @csrf
                                        <button type="submit" class="tbl-btn">Restore</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align: center; color: #6B7280; padding: 28px;">There are no catalog products yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 14px;">{{ $products->links() }}</div>
    </div>

    <!-- 5. PROMOTIONAL HERO BANNERS -->
    <div class="dash-card">
        <div class="dash-card-header">
            <div>
                <h2 class="dash-card-title">Promotional Hero Banners</h2>
                <span style="font-size: 12px; color: #6B7280;">Edit copy, images, layout, colors, and preview before publishing</span>
            </div>
            <a class="pill-btn-green" href="{{ route('admin.store.banners.create') }}">Create Banner</a>
        </div>

        <div style="overflow-x: auto;">
            <table class="store-table">
                <thead>
                    <tr>
                        <th>Banner</th>
                        <th>Order</th>
                        <th>Visibility</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($banners as $banner)
                    <tr>
                        <td>
                            <div style="color: #310669; font-size: 14px;">{{ $banner->title }}</div>
                            <div style="font-size: 11px; color: #6B7280;">{{ $banner->layout }} · {{ $banner->alignment }}</div>
                        </td>
                        <td>{{ $banner->sort_order }}</td>
                        <td>
                            <span class="status-chip {{ $banner->is_active ? 'is-published' : 'is-hidden' }}">
                                {{ $banner->is_active ? 'Published' : 'Hidden' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                <a class="tbl-btn" href="{{ route('admin.store.banners.edit', $banner) }}">Edit Preview</a>
                                <form method="POST" action="{{ route('admin.store.banners.destroy', $banner) }}" onsubmit="return confirm('Remove this banner?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tbl-btn tbl-btn-danger">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center; color: #6B7280; padding: 28px;">No promotional banners. Create one to replace the shop's fallback hero.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DAILY STORE LOG MODAL -->
<div class="log-modal-overlay" id="storeLogModal">
    <div class="log-modal-card">
        <div class="log-modal-head">
            <div>
                <h3 class="log-modal-title" id="storeModalDateTitle">Daily Store Logs</h3>
                <span style="font-size: 12px; color: #6B7280;" id="storeModalLogCount">0 activity logs</span>
            </div>
            <button type="button" class="log-modal-close" id="closeStoreLogModal">&times;</button>
        </div>
        <div class="log-modal-body" id="storeModalLogsList"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const currencyFormat = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
    const numberFormat = new Intl.NumberFormat();

    // =========================================================
    // 1. STOCK-STYLE SHOP REVENUE CHART
    // =========================================================
    const rawSales = @js($salesSeries ?? []);
    let activeSalesRange = '30D';
    let renderedCoords = [];
    let renderedPoints = [];

    function getFilteredSales(range) {
        if (!rawSales.length) return [];
        const latestTs = new Date(rawSales[rawSales.length - 1].timestamp).getTime();
        const windows = {
            '7D': 7 * 86400 * 1000,
            '30D': 30 * 86400 * 1000,
            '90D': 90 * 86400 * 1000,
            'ALL': Infinity
        };
        const cutoff = windows[range] === Infinity ? 0 : latestTs - windows[range];
        const filtered = rawSales.filter(pt => new Date(pt.timestamp).getTime() >= cutoff);
        return filtered.length ? filtered : rawSales;
    }

    function renderSalesChart() {
        const points = getFilteredSales(activeSalesRange);
        const lineEl = document.getElementById('storeSalesLine');
        const areaEl = document.getElementById('storeSalesArea');
        const labelsEl = document.getElementById('storeSalesLabels');
        const totalEl = document.getElementById('salesRangeTotal');
        const ordersEl = document.getElementById('salesRangeOrders');

        if (!points.length) {
            totalEl.textContent = '₱0.00';
            ordersEl.textContent = '0 shop orders recorded';
            return;
        }

        const totalRev = points.reduce((sum, p) => sum + Number(p.revenue || 0), 0);
        const totalOrd = points.reduce((sum, p) => sum + Number(p.orders || 0), 0);
        totalEl.textContent = currencyFormat.format(totalRev);
        ordersEl.textContent = `${numberFormat.format(totalOrd)} shop orders (${activeSalesRange})`;

        const values = points.map(p => Number(p.revenue || 0));
        const maxVal = Math.max(1, ...values);

        const coords = values.map((val, idx) => {
            const x = 12 + idx * (736 / Math.max(1, values.length - 1));
            const y = 185 - (val / maxVal) * 150;
            return [x, y];
        });

        renderedCoords = coords;
        renderedPoints = points;

        const ptsStr = coords.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(' ');
        lineEl.setAttribute('points', ptsStr);
        areaEl.setAttribute('points', `${coords[0][0].toFixed(1)},185 ${ptsStr} ${coords[coords.length - 1][0].toFixed(1)},185`);

        labelsEl.replaceChildren();
        const ticks = Math.min(5, points.length);
        for (let i = 0; i < ticks; i++) {
            const idx = Math.round((i / Math.max(1, ticks - 1)) * (points.length - 1));
            const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            t.setAttribute('x', String(coords[idx][0]));
            t.setAttribute('y', '210');
            t.setAttribute('text-anchor', i === 0 ? 'start' : (i === ticks - 1 ? 'end' : 'middle'));
            t.textContent = points[idx].label;
            labelsEl.appendChild(t);
        }
    }

    const salesSvg = document.getElementById('storeSalesChart');
    const hoverLine = document.getElementById('salesHoverLine');
    const hoverDot = document.getElementById('salesHoverDot');
    const tooltipEl = document.getElementById('salesChartTooltip');

    if (salesSvg) {
        salesSvg.addEventListener('mousemove', (e) => {
            if (!renderedCoords.length) return;
            const rect = salesSvg.getBoundingClientRect();
            const relX = ((e.clientX - rect.left) / rect.width) * 760;

            let bestIdx = 0;
            let bestDist = Infinity;
            renderedCoords.forEach(([x], i) => {
                const d = Math.abs(x - relX);
                if (d < bestDist) {
                    bestDist = d;
                    bestIdx = i;
                }
            });

            const [cx, cy] = renderedCoords[bestIdx];
            const pt = renderedPoints[bestIdx];
            hoverLine.setAttribute('x1', cx);
            hoverLine.setAttribute('x2', cx);
            hoverLine.style.display = 'block';

            hoverDot.setAttribute('cx', cx);
            hoverDot.setAttribute('cy', cy);
            hoverDot.style.display = 'block';

            tooltipEl.textContent = `${pt.label}: ${currencyFormat.format(pt.revenue)} (${pt.orders} orders)`;
            tooltipEl.style.opacity = '1';
        });

        salesSvg.addEventListener('mouseleave', () => {
            hoverLine.style.display = 'none';
            hoverDot.style.display = 'none';
            tooltipEl.style.opacity = '0';
        });
    }

    document.querySelectorAll('#salesRangeFilters .stock-range-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#salesRangeFilters .stock-range-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeSalesRange = btn.dataset.range;
            renderSalesChart();
        });
    });

    renderSalesChart();

    // =========================================================
    // 2. DAILY STORE LOG CALENDAR & MODAL
    // =========================================================
    const dailyLogs = @js($dailyLogs ?? []);
    let calCursor = new Date();
    calCursor.setDate(1);

    const calGrid = document.getElementById('storeCalendarGrid');
    const monthLabel = document.getElementById('storeCalMonthLabel');
    const modalOverlay = document.getElementById('storeLogModal');
    const modalDateTitle = document.getElementById('storeModalDateTitle');
    const modalLogCount = document.getElementById('storeModalLogCount');
    const modalLogsList = document.getElementById('storeModalLogsList');

    function toYMD(dateObj) {
        return `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
    }

    function openStoreLogModal(dateKey, prettyDate) {
        const entries = dailyLogs[dateKey] || [];
        modalDateTitle.textContent = prettyDate;
        modalLogCount.textContent = `${entries.length} store log${entries.length === 1 ? '' : 's'} recorded`;
        modalLogsList.replaceChildren();

        if (!entries.length) {
            const p = document.createElement('p');
            p.style.cssText = 'color:#6B7280;font-size:14px;text-align:center;padding:24px 0;margin:0;';
            p.textContent = 'No shop purchases, product edits, or banner updates recorded on this date.';
            modalLogsList.appendChild(p);
        } else {
            entries.forEach(item => {
                const row = document.createElement('div');
                row.className = 'log-entry-row';

                const left = document.createElement('div');
                left.innerHTML = `
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <span class="log-type-tag badge-${item.badge}">${item.type}</span>
                        <span style="font-size:11px;color:#6B7280;">${item.time}</span>
                    </div>
                    <div style="font-size:14px;color:#1F2937;">${item.title}</div>
                    <div style="font-size:12px;color:#6B7280;">${item.detail}</div>
                `;

                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'tbl-btn';
                link.textContent = 'Inspect';

                row.append(left, link);
                modalLogsList.appendChild(row);
            });
        }

        modalOverlay.classList.add('active');
    }

    function renderStoreCalendar() {
        const year = calCursor.getFullYear();
        const month = calCursor.getMonth();
        const todayStr = toYMD(new Date());

        monthLabel.textContent = calCursor.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        calGrid.replaceChildren();

        const firstWeekday = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevDays = new Date(year, month, 0).getDate();

        for (let i = firstWeekday - 1; i >= 0; i--) {
            calGrid.appendChild(buildStoreDayCell(new Date(year, month - 1, prevDays - i), true, todayStr));
        }

        for (let d = 1; d <= daysInMonth; d++) {
            calGrid.appendChild(buildStoreDayCell(new Date(year, month, d), false, todayStr));
        }

        const total = calGrid.children.length;
        const rem = total % 7 === 0 ? 0 : 7 - (total % 7);
        for (let n = 1; n <= rem; n++) {
            calGrid.appendChild(buildStoreDayCell(new Date(year, month + 1, n), true, todayStr));
        }
    }

    function buildStoreDayCell(dateObj, isOutside, todayStr) {
        const key = toYMD(dateObj);
        const entries = dailyLogs[key] || [];
        const hasPurchases = entries.some(e => e.badge === 'purchase');

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cal-day-cell';
        if (isOutside) btn.classList.add('is-outside');
        if (key === todayStr) btn.classList.add('is-today');
        if (entries.length > 0) btn.classList.add('has-logs');
        if (hasPurchases) btn.classList.add('has-purchases');

        const num = document.createElement('span');
        num.className = 'cal-day-num';
        num.textContent = dateObj.getDate();
        btn.appendChild(num);

        if (entries.length > 0) {
            const pill = document.createElement('span');
            pill.className = 'cal-log-pill';
            pill.textContent = `${entries.length} log${entries.length > 1 ? 's' : ''}`;
            btn.appendChild(pill);
        }

        const pretty = dateObj.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        btn.addEventListener('click', () => openStoreLogModal(key, pretty));

        return btn;
    }

    document.getElementById('storeCalPrev').addEventListener('click', () => {
        calCursor.setMonth(calCursor.getMonth() - 1);
        renderStoreCalendar();
    });

    document.getElementById('storeCalNext').addEventListener('click', () => {
        calCursor.setMonth(calCursor.getMonth() + 1);
        renderStoreCalendar();
    });

    document.getElementById('closeStoreLogModal').addEventListener('click', () => {
        modalOverlay.classList.remove('active');
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) modalOverlay.classList.remove('active');
    });

    renderStoreCalendar();
})();
</script>
@endpush
