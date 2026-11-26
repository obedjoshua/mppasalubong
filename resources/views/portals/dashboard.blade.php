@extends('layouts.portal')

@section('title', ucfirst($portal) . ' Overview | Masaganang Pagpapala Pasalubong')

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

    /* Override portal gray background & shadows to match User Dashboard */
    .portal-content-wrapper,
    .portal-main {
        background: #FFFFFF !important;
        font-family: 'modern-bold', sans-serif;
    }

    .admin-dash-wrap {
        width: 100%;
        padding: 4px 15px 60px 15px;
        box-sizing: border-box;
    }

    /* Header matching User Dashboard Greeting Area */
    .admin-header-area {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .admin-greeting-sub {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(18px, 2.2vw, 24px);
        color: #310669;
        line-height: 1;
        margin-bottom: 4px;
    }

    .admin-display-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(34px, 5.5vw, 64px);
        color: #8a38f5;
        margin: 0;
        line-height: 1;
    }

    .admin-header-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .admin-pill-btn {
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

    .admin-pill-btn:hover {
        transform: translateY(2px);
        box-shadow: 0px 3px 0px #560db6;
    }

    .admin-live-badge {
        background: #FAF5FF;
        border: 2px solid #E9D5FF;
        color: #6B21A8;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        padding: 10px 16px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .admin-live-dot {
        width: 8px;
        height: 8px;
        background: #10B981;
        border-radius: 50%;
        display: inline-block;
    }

    /* 4 Stat Cards in User Dashboard Style (Crisp Border, No Drop Shadow) */
    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .admin-stat-box {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: border-color 0.15s ease;
    }

    .admin-stat-box.accent-purple { border-color: #D8B4FE; background: #FAF5FF; }
    .admin-stat-box.accent-green  { border-color: #A7F3D0; background: #ECFDF5; }
    .admin-stat-box.accent-blue   { border-color: #BFDBFE; background: #EFF6FF; }
    .admin-stat-box.accent-amber  { border-color: #FDE68A; background: #FFFBEB; }

    .admin-stat-label {
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
    }

    .admin-stat-value {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(28px, 3.2vw, 40px);
        color: #310669;
        line-height: 1.1;
        margin-top: 6px;
    }

    .admin-stat-box.accent-green .admin-stat-value { color: #065F46; }
    .admin-stat-box.accent-blue .admin-stat-value  { color: #1E40AF; }
    .admin-stat-box.accent-amber .admin-stat-value { color: #92400E; }

    /* Main 2-Column Content Grid matching .dashboard-content-grid */
    .admin-main-grid {
        display: grid;
        grid-template-columns: 1.75fr 1fr;
        gap: 14px;
        align-items: start;
    }

    .admin-col-stack {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* User Dashboard Card Container */
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

    .dash-link {
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        color: #8a38f5;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .dash-link:hover {
        text-decoration: underline;
    }

    /* Google Stock-Style Timeframe Filter Pills */
    .stock-filter-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        background: #F9FAFB;
        border: 2px solid #E5E7EB;
        border-radius: 14px;
        padding: 4px;
    }

    .stock-range-btn {
        background: transparent;
        border: none;
        border-radius: 10px;
        padding: 6px 12px;
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
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1.5px dashed #E5E7EB;
    }

    .stock-big-metric {
        font-family: 'Recoleta-Bold', serif;
        font-size: 28px;
        color: #1F2937;
    }

    .stock-delta-badge {
        font-family: 'modern-bold', sans-serif;
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
        height: 240px;
        overflow: visible;
        cursor: crosshair;
    }

    .stock-tooltip {
        position: absolute;
        top: 8px;
        left: 12px;
        background: #310669;
        color: #FFFFFF;
        padding: 6px 12px;
        border-radius: 10px;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.12s ease;
        z-index: 10;
    }

    /* Interactive Order Calendar */
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
        font-family: 'modern-bold', sans-serif;
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
        min-width: 150px;
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
        min-height: 68px;
        background: #FFFFFF;
        border: 2px solid #F3F4F6;
        border-radius: 14px;
        padding: 8px;
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

    .cal-day-cell.has-orders {
        border-color: #A7F3D0;
        background: #F0FDF4;
    }

    .cal-day-cell.has-orders:hover {
        border-color: #059669;
        background: #DCFCE7;
    }

    .cal-day-num {
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        color: #1F2937;
    }

    .cal-day-cell.is-today .cal-day-num {
        color: #8a38f5;
    }

    .cal-order-pill {
        background: #10B981;
        color: #FFFFFF;
        font-family: 'modern-bold', sans-serif;
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 999px;
        white-space: nowrap;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Live Feed & Module Rows */
    .admin-feed-list {
        max-height: 290px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .admin-feed-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #FAF5FF;
        border: 1.5px solid #E9D5FF;
        border-radius: 12px;
    }

    .admin-method-tag {
        background: #8a38f5;
        color: #FFFFFF;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .admin-path-text {
        font-size: 12px;
        color: #1F2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .admin-time-text {
        font-size: 11px;
        color: #6B7280;
    }

    .admin-module-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border: 2px solid #F3F4F6;
        border-radius: 14px;
        text-decoration: none;
        color: #1F2937;
        margin-bottom: 8px;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .admin-module-link:hover {
        border-color: #8a38f5;
        background: #FAF5FF;
    }

    .admin-module-pill {
        background: #F3E8FF;
        color: #6B21A8;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
    }

    /* Date Orders Modal */
    .cal-modal-overlay {
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

    .cal-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .cal-modal-card {
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

    .cal-modal-head {
        padding: 20px 24px;
        border-bottom: 2px solid #F3F4F6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .cal-modal-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: 22px;
        color: #310669;
        margin: 0;
    }

    .cal-modal-close {
        background: #FAF5FF;
        border: 2px solid #E9D5FF;
        color: #8a38f5;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        font-family: 'modern-bold', sans-serif;
        cursor: pointer;
    }

    .cal-modal-body {
        padding: 20px 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .modal-order-item {
        border: 2px solid #E5E7EB;
        border-radius: 16px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .modal-order-btn {
        background: #10B981;
        color: #FFFFFF;
        text-decoration: none;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 10px;
    }

    @media (max-width: 1100px) {
        .admin-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .admin-main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .admin-stats-grid {
            grid-template-columns: 1fr;
        }
        .cal-day-cell {
            min-height: 54px;
            padding: 6px;
        }
        .cal-order-pill {
            font-size: 9px;
            padding: 2px 5px;
        }
    }
</style>

<div class="admin-dash-wrap">
    <!-- 1. USER-DASHBOARD STYLE HEADER -->
    <div class="admin-header-area">
        <div>
            <div class="admin-greeting-sub">Operations Center</div>
            <h1 class="admin-display-title">{{ ucfirst($portal) }} Dashboard</h1>
        </div>
        <div class="admin-header-pills">
            <a class="admin-pill-btn" href="{{ route('admin.store.index') }}">Shop Content</a>
            <div class="admin-live-badge">
                <span class="admin-live-dot"></span>
                <span>Updated <time id="portal-updated-at">{{ \Illuminate\Support\Carbon::parse($liveData['updatedAt'])->format('H:i:s') }}</time></span>
            </div>
        </div>
    </div>

    <!-- 2. STAT CARDS -->
    <div class="admin-stats-grid">
        @foreach([
            ['all', 'Total Users', 'accent-purple'],
            ['total', 'API Requests · 24h', 'accent-blue'],
            ['successful', 'Successful Responses', 'accent-green'],
            ['serverErrors', 'Server Errors', 'accent-amber'],
        ] as [$key, $label, $accent])
            @php($value = $key === 'all' ? $userCounts[$key] : $liveData['stats'][$key])
            <div class="admin-stat-box {{ $accent }}">
                <div class="admin-stat-label">{{ $label }}</div>
                <div class="admin-stat-value" id="portal-stat-{{ $key }}">{{ number_format($value) }}</div>
            </div>
        @endforeach
    </div>

    <!-- 3. MAIN 2-COLUMN DASHBOARD GRID -->
    <div class="admin-main-grid">
        <!-- Left Column: Interactive Order Calendar + Lifetime Stock-Style API Chart -->
        <div class="admin-col-stack">

            <!-- DYNAMIC ORDER CALENDAR -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Order Pickup Calendar</h2>
                        <span style="font-size: 12px; color: #6B7280;">Click any date to inspect scheduled orders</span>
                    </div>
                    <div class="cal-nav-group">
                        <button type="button" class="cal-nav-btn" id="calPrevMonth" aria-label="Previous Month">&larr;</button>
                        <span class="cal-month-label" id="calMonthLabel">November 2026</span>
                        <button type="button" class="cal-nav-btn" id="calNextMonth" aria-label="Next Month">&rarr;</button>
                    </div>
                </div>

                <div class="cal-weekdays">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
                <div class="cal-days-grid" id="adminCalendarGrid"></div>
            </div>

            <!-- LIFETIME API TRAFFIC CHART (GOOGLE STOCK-STYLE FILTERING) -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">API Traffic History</h2>
                        <span style="font-size: 12px; color: #6B7280;">Persistent lifetime request volume (auto-appends every 15s)</span>
                    </div>

                    <div class="stock-filter-bar" id="stockRangeFilters">
                        <button type="button" class="stock-range-btn" data-range="1H">1H</button>
                        <button type="button" class="stock-range-btn active" data-range="24H">24H</button>
                        <button type="button" class="stock-range-btn" data-range="7D">7D</button>
                        <button type="button" class="stock-range-btn" data-range="30D">30D</button>
                        <button type="button" class="stock-range-btn" data-range="1Y">1Y</button>
                        <button type="button" class="stock-range-btn" data-range="ALL">ALL</button>
                    </div>
                </div>

                <div class="stock-summary-strip">
                    <div style="display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap;">
                        <span class="stock-big-metric" id="stockTotalInRange">0 requests</span>
                        <span class="stock-delta-badge" id="stockRangeSummary">Showing 24H window</span>
                    </div>
                    <div style="font-size: 12px; color: #6B7280; display: flex; gap: 14px; flex-wrap: wrap;">
                        <span>Avg latency: <strong style="color: #1F2937;" id="portal-stat-averageDuration">{{ number_format($liveData['stats']['averageDuration']) }} ms</strong></span>
                        <span>Client errors: <strong style="color: #1F2937;" id="portal-stat-clientErrors">{{ number_format($liveData['stats']['clientErrors']) }}</strong></span>
                    </div>
                </div>

                <div class="stock-chart-container">
                    <div class="stock-tooltip" id="stockChartTooltip"></div>
                    <svg class="stock-svg" id="portal-traffic-chart" viewBox="0 0 760 240" role="img" aria-label="Lifetime API traffic chart">
                        <defs>
                            <linearGradient id="portalChartFill" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#8a38f5" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#8a38f5" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="30" x2="760" y2="30"/>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="85" x2="760" y2="85"/>
                        <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="140" x2="760" y2="140"/>
                        <line stroke="#E5E7EB" x1="0" y1="195" x2="760" y2="195"/>
                        <polygon id="portal-traffic-area" fill="url(#portalChartFill)" points="0,195 760,195"/>
                        <polyline id="portal-traffic-line" fill="none" stroke="#8a38f5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="0,195 760,195"/>
                        <line id="stockHoverLine" x1="0" y1="20" x2="0" y2="195" stroke="#8a38f5" stroke-width="1.5" stroke-dasharray="3 3" style="display: none;"/>
                        <circle id="stockHoverDot" cx="0" cy="0" r="5" fill="#8a38f5" stroke="#FFFFFF" stroke-width="2" style="display: none;"/>
                        <g id="portal-traffic-labels" fill="#6B7280" font-size="11"></g>
                    </svg>
                </div>
            </div>

        </div>

        <!-- Right Column: Platform Modules + Live Request Feed -->
        <div class="admin-col-stack">
            <!-- Platform Modules -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2 class="dash-card-title">Platform Modules</h2>
                    <a href="{{ route('developer.api-requests.index') }}" class="dash-link">Logs &rarr;</a>
                </div>

                <a href="{{ route('admin.users.index') }}" class="admin-module-link">
                    <div>
                        <div style="font-size: 14px; color: #310669;">User Access</div>
                        <div style="font-size: 12px; color: #6B7280;">Manage accounts &amp; roles</div>
                    </div>
                    <span class="admin-module-pill">{{ number_format($userCounts['all']) }}</span>
                </a>

                <a href="{{ route('admin.store.index') }}" class="admin-module-link">
                    <div>
                        <div style="font-size: 14px; color: #310669;">Shop Content</div>
                        <div style="font-size: 12px; color: #6B7280;">Products &amp; hero banners</div>
                    </div>
                    <span class="admin-module-pill">Open</span>
                </a>

                <a href="{{ route('developer.posts.index') }}" class="admin-module-link">
                    <div>
                        <div style="font-size: 14px; color: #310669;">Post Review</div>
                        <div style="font-size: 12px; color: #6B7280;">Moderate community feed</div>
                    </div>
                    <span class="admin-module-pill">{{ number_format($pendingPosts) }} hidden</span>
                </a>

                @foreach($connections as $name => $connection)
                    <div class="admin-module-link" style="cursor: default;">
                        <div>
                            <div style="font-size: 14px; color: #1F2937; text-transform: capitalize;">{{ $name }} Connection</div>
                            <div style="font-size: 11px; color: #6B7280;">{{ $connection['detail'] }}</div>
                        </div>
                        <span class="admin-module-pill" style="{{ $connection['healthy'] ? 'background:#DEF7EC;color:#03543F;' : 'background:#FEE2E2;color:#991B1B;' }}">
                            {{ $connection['healthy'] ? 'Healthy' : 'Offline' }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Live Request Feed -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2 class="dash-card-title">Live Request Feed</h2>
                    <span class="admin-live-badge" style="padding: 5px 10px;">
                        <span class="admin-live-dot"></span>15s Poll
                    </span>
                </div>

                <div class="admin-feed-list" id="portal-live-feed" aria-live="polite">
                    @forelse($liveData['feed'] as $entry)
                        <div class="admin-feed-row">
                            <span class="admin-method-tag">{{ $entry['method'] }}</span>
                            <span class="admin-path-text" title="{{ $entry['path'] }}">{{ $entry['path'] }}</span>
                            <span class="admin-time-text">{{ $entry['time'] }}</span>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: #6B7280; margin: 0;">Waiting for instrumented API activity.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CALENDAR DATE ORDERS MODAL -->
<div class="cal-modal-overlay" id="calendarOrdersModal">
    <div class="cal-modal-card">
        <div class="cal-modal-head">
            <div>
                <h3 class="cal-modal-title" id="calModalDateTitle">Orders for Date</h3>
                <span style="font-size: 12px; color: #6B7280;" id="calModalOrderCount">0 orders scheduled</span>
            </div>
            <button type="button" class="cal-modal-close" id="closeCalModalBtn">&times;</button>
        </div>
        <div class="cal-modal-body" id="calModalOrdersList"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const numberFormat = new Intl.NumberFormat();
    const apiUrl = @js(route($portal === 'admin' ? 'admin.dashboard.live' : 'developer.dashboard.live'));
    const STORAGE_KEY = 'mpp_lifetime_api_history_v1';

    // =========================================================
    // 1. PERSISTENT LIFETIME API HISTORY (NEVER WIPES ON POLL)
    // =========================================================
    let activeRange = '24H';
    let currentRenderedCoords = [];
    let currentRenderedPoints = [];

    function loadStoredHistory() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function saveStoredHistory(points) {
        try {
            // Keep up to 10,000 lifetime buckets in localStorage
            const trimmed = points.slice(-10000);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(trimmed));
        } catch (e) {
            console.warn('Could not persist API history locally:', e);
        }
    }

    function normalizeIncomingPoints(rawPoints, backendHistory = []) {
        const map = new Map();

        // 1. Load previously saved lifetime points
        loadStoredHistory().forEach(pt => {
            if (pt && pt.key) map.set(pt.key, pt);
        });

        // 2. Merge backend lifetime history if provided
        (backendHistory || []).forEach(pt => {
            const ts = pt.timestamp ? new Date(pt.timestamp).getTime() : Date.now();
            const key = pt.timestamp || pt.label;
            map.set(key, {
                key,
                ts,
                label: pt.label || new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                count: Number(pt.count || 0)
            });
        });

        // 3. Merge latest 24h hourly points without deleting older keys
        const now = Date.now();
        (rawPoints || []).forEach((pt, idx) => {
            const estimatedTs = pt.timestamp
                ? new Date(pt.timestamp).getTime()
                : now - ((rawPoints.length - 1 - idx) * 3600 * 1000);
            const hourBucket = new Date(estimatedTs);
            hourBucket.setMinutes(0, 0, 0);
            const key = `${hourBucket.toISOString().slice(0, 13)}_${pt.label}`;

            const existing = map.get(key);
            map.set(key, {
                key,
                ts: hourBucket.getTime(),
                label: pt.label,
                // Preserve highest recorded count for that bucket so live refreshes never lose data
                count: Math.max(existing ? existing.count : 0, Number(pt.count || 0))
            });
        });

        const merged = Array.from(map.values()).sort((a, b) => a.ts - b.ts);
        saveStoredHistory(merged);
        return merged;
    }

    let lifetimeSeries = normalizeIncomingPoints(
        @js($liveData['traffic'] ?? []),
        @js($liveData['history'] ?? [])
    );

    function filterSeriesByRange(series, range) {
        if (!series.length) return [];
        const latestTs = series[series.length - 1].ts;
        const durations = {
            '1H': 1 * 3600 * 1000,
            '24H': 24 * 3600 * 1000,
            '7D': 7 * 24 * 3600 * 1000,
            '30D': 30 * 24 * 3600 * 1000,
            '1Y': 365 * 24 * 3600 * 1000,
            'ALL': Infinity
        };
        const cutoff = durations[range] === Infinity ? 0 : latestTs - durations[range];
        const filtered = series.filter(pt => pt.ts >= cutoff);
        return filtered.length ? filtered : series.slice(-24);
    }

    function renderStockChart() {
        const points = filterSeriesByRange(lifetimeSeries, activeRange);
        const chartLine = document.getElementById('portal-traffic-line');
        const chartArea = document.getElementById('portal-traffic-area');
        const labels = document.getElementById('portal-traffic-labels');
        const totalEl = document.getElementById('stockTotalInRange');
        const summaryEl = document.getElementById('stockRangeSummary');

        if (!points.length) return;

        const sum = points.reduce((acc, p) => acc + p.count, 0);
        totalEl.textContent = `${numberFormat.format(sum)} requests`;
        summaryEl.textContent = `Window: ${activeRange} (${points.length} data points)`;

        const values = points.map(p => p.count);
        const maximum = Math.max(1, ...values);

        const coords = values.map((val, idx) => {
            const x = 10 + idx * (740 / Math.max(1, values.length - 1));
            const y = 195 - (val / maximum) * 160;
            return [x, y];
        });

        currentRenderedCoords = coords;
        currentRenderedPoints = points;

        const pointsText = coords.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(' ');
        chartLine.setAttribute('points', pointsText);
        chartArea.setAttribute('points', `${coords[0][0].toFixed(1)},195 ${pointsText} ${coords[coords.length - 1][0].toFixed(1)},195`);

        labels.replaceChildren();
        const tickCount = Math.min(5, points.length);
        for (let i = 0; i < tickCount; i++) {
            const idx = Math.round((i / Math.max(1, tickCount - 1)) * (points.length - 1));
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', String(coords[idx][0]));
            text.setAttribute('y', '222');
            text.setAttribute('text-anchor', i === 0 ? 'start' : (i === tickCount - 1 ? 'end' : 'middle'));
            text.textContent = points[idx].label;
            labels.appendChild(text);
        }
    }

    // Stock Hover Crosshair Tooltip
    const svgEl = document.getElementById('portal-traffic-chart');
    const hoverLine = document.getElementById('stockHoverLine');
    const hoverDot = document.getElementById('stockHoverDot');
    const tooltipEl = document.getElementById('stockChartTooltip');

    if (svgEl) {
        svgEl.addEventListener('mousemove', (e) => {
            if (!currentRenderedCoords.length) return;
            const rect = svgEl.getBoundingClientRect();
            const relX = ((e.clientX - rect.left) / rect.width) * 760;

            let closestIdx = 0;
            let minDist = Infinity;
            currentRenderedCoords.forEach(([x], idx) => {
                const dist = Math.abs(x - relX);
                if (dist < minDist) {
                    minDist = dist;
                    closestIdx = idx;
                }
            });

            const [cx, cy] = currentRenderedCoords[closestIdx];
            const pt = currentRenderedPoints[closestIdx];

            hoverLine.setAttribute('x1', cx);
            hoverLine.setAttribute('x2', cx);
            hoverLine.style.display = 'block';

            hoverDot.setAttribute('cx', cx);
            hoverDot.setAttribute('cy', cy);
            hoverDot.style.display = 'block';

            tooltipEl.textContent = `${pt.label} — ${numberFormat.format(pt.count)} requests`;
            tooltipEl.style.opacity = '1';
        });

        svgEl.addEventListener('mouseleave', () => {
            hoverLine.style.display = 'none';
            hoverDot.style.display = 'none';
            tooltipEl.style.opacity = '0';
        });
    }

    document.querySelectorAll('#stockRangeFilters .stock-range-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#stockRangeFilters .stock-range-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeRange = btn.dataset.range;
            renderStockChart();
        });
    });

    // =========================================================
    // 2. LIVE FEED & 15-SECOND NON-DESTRUCTIVE POLLING
    // =========================================================
    function renderFeed(entries) {
        const feed = document.getElementById('portal-live-feed');
        if (!feed) return;
        feed.replaceChildren();

        if (!entries || !entries.length) {
            const p = document.createElement('p');
            p.style.cssText = 'font-size:13px;color:#6B7280;margin:0;';
            p.textContent = 'Waiting for instrumented API activity.';
            feed.appendChild(p);
            return;
        }

        entries.forEach(entry => {
            const row = document.createElement('div');
            row.className = 'admin-feed-row';

            const method = document.createElement('span');
            method.className = 'admin-method-tag';
            method.textContent = entry.method;

            const path = document.createElement('span');
            path.className = 'admin-path-text';
            path.textContent = entry.path;
            path.title = entry.path;

            const time = document.createElement('span');
            time.className = 'admin-time-text';
            time.textContent = entry.time;

            row.append(method, path, time);
            feed.appendChild(row);
        });
    }

    renderStockChart();

    window.setInterval(async () => {
        try {
            const response = await fetch(apiUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();

            document.getElementById('portal-stat-total').textContent = numberFormat.format(data.stats.total);
            document.getElementById('portal-stat-successful').textContent = numberFormat.format(data.stats.successful);
            document.getElementById('portal-stat-serverErrors').textContent = numberFormat.format(data.stats.serverErrors);
            document.getElementById('portal-stat-clientErrors').textContent = numberFormat.format(data.stats.clientErrors);
            document.getElementById('portal-stat-averageDuration').textContent = `${numberFormat.format(data.stats.averageDuration)} ms`;
            document.getElementById('portal-updated-at').textContent = new Date(data.updatedAt).toLocaleTimeString();

            // Merge new points into lifetime history without losing existing data
            lifetimeSeries = normalizeIncomingPoints(data.traffic || [], data.history || []);
            renderStockChart();
            renderFeed(data.feed || []);
        } catch (err) {
            console.error(err);
        }
    }, 15000);

    // =========================================================
    // 3. DYNAMIC ORDER CALENDAR + DATE CLICK MODAL
    // =========================================================
    const ordersByDate = @js($calendarOrders ?? []);
    let currentCalDate = new Date();
    currentCalDate.setDate(1);

    const gridEl = document.getElementById('adminCalendarGrid');
    const monthLabelEl = document.getElementById('calMonthLabel');
    const modalOverlay = document.getElementById('calendarOrdersModal');
    const modalDateTitle = document.getElementById('calModalDateTitle');
    const modalOrderCount = document.getElementById('calModalOrderCount');
    const modalOrdersList = document.getElementById('calModalOrdersList');

    function formatYMD(dateObj) {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function openDateOrdersModal(dateStr, prettyDate) {
        const dayOrders = ordersByDate[dateStr] || [];
        modalDateTitle.textContent = prettyDate;
        modalOrderCount.textContent = `${dayOrders.length} order${dayOrders.length === 1 ? '' : 's'} scheduled`;
        modalOrdersList.replaceChildren();

        if (!dayOrders.length) {
            const empty = document.createElement('p');
            empty.style.cssText = 'color:#6B7280;font-size:14px;text-align:center;padding:24px 0;margin:0;';
            empty.textContent = 'No orders scheduled for pickup on this date.';
            modalOrdersList.appendChild(empty);
        } else {
            dayOrders.forEach(order => {
                const card = document.createElement('div');
                card.className = 'modal-order-item';

                const info = document.createElement('div');
                info.innerHTML = `
                    <div style="font-size:12px;color:#8a38f5;">Ticket #${order.id} &bull; ${order.pickup_time}</div>
                    <div style="font-size:15px;color:#1F2937;margin:2px 0;">${order.customer}</div>
                    <div style="font-size:12px;color:#6B7280;">${order.items} &bull; <strong>${order.status}</strong></div>
                `;

                const link = document.createElement('a');
                link.href = order.url;
                link.className = 'modal-order-btn';
                link.textContent = 'Open Ticket';

                card.append(info, link);
                modalOrdersList.appendChild(card);
            });
        }

        modalOverlay.classList.add('active');
    }

    function renderCalendar() {
        const year = currentCalDate.getFullYear();
        const month = currentCalDate.getMonth();
        const todayStr = formatYMD(new Date());

        monthLabelEl.textContent = currentCalDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        gridEl.replaceChildren();

        const firstDayOfWeek = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const prevMonthDays = new Date(year, month, 0).getDate();

        // Leading days from previous month
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const d = new Date(year, month - 1, prevMonthDays - i);
            gridEl.appendChild(createDayCell(d, true, todayStr));
        }

        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const d = new Date(year, month, day);
            gridEl.appendChild(createDayCell(d, false, todayStr));
        }

        // Trailing days to complete 35 or 42 cell grid
        const totalCells = gridEl.children.length;
        const remainder = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let nextDay = 1; nextDay <= remainder; nextDay++) {
            const d = new Date(year, month + 1, nextDay);
            gridEl.appendChild(createDayCell(d, true, todayStr));
        }
    }

    function createDayCell(dateObj, isOutside, todayStr) {
        const dateStr = formatYMD(dateObj);
        const dayOrders = ordersByDate[dateStr] || [];
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cal-day-cell';
        if (isOutside) btn.classList.add('is-outside');
        if (dateStr === todayStr) btn.classList.add('is-today');
        if (dayOrders.length > 0) btn.classList.add('has-orders');

        const num = document.createElement('span');
        num.className = 'cal-day-num';
        num.textContent = dateObj.getDate();
        btn.appendChild(num);

        if (dayOrders.length > 0) {
            const badge = document.createElement('span');
            badge.className = 'cal-order-pill';
            badge.textContent = `${dayOrders.length} order${dayOrders.length > 1 ? 's' : ''}`;
            btn.appendChild(badge);
        }

        const prettyDate = dateObj.toLocaleDateString('en-US', {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            year: 'numeric'
        });
        btn.addEventListener('click', () => openDateOrdersModal(dateStr, prettyDate));

        return btn;
    }

    document.getElementById('calPrevMonth').addEventListener('click', () => {
        currentCalDate.setMonth(currentCalDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('calNextMonth').addEventListener('click', () => {
        currentCalDate.setMonth(currentCalDate.getMonth() + 1);
        renderCalendar();
    });

    document.getElementById('closeCalModalBtn').addEventListener('click', () => {
        modalOverlay.classList.remove('active');
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) modalOverlay.classList.remove('active');
    });

    renderCalendar();
})();
</script>
@endpush
