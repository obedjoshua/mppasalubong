@extends('layouts.portal')

@section('title', 'API & Chatbot Telemetry | Masaganang Pagpapala Pasalubong')

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

    .api-dash-wrap {
        width: 100%;
        padding: 4px 15px 60px 15px;
        box-sizing: border-box;
    }

    /* Header matching User Dashboard */
    .api-header-area {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .api-greeting-sub {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(18px, 2.2vw, 24px);
        color: #310669;
        line-height: 1;
        margin-bottom: 4px;
    }

    .api-display-title {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(34px, 5.5vw, 64px);
        color: #8a38f5;
        margin: 0;
        line-height: 1;
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

    /* 4 KPI Stat Boxes */
    .api-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .api-stat-box {
        background: #FFFFFF;
        border: 2.5px solid #E5E7EB;
        border-radius: 20px;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .api-stat-box.accent-purple { border-color: #D8B4FE; background: #FAF5FF; }
    .api-stat-box.accent-green  { border-color: #A7F3D0; background: #ECFDF5; }
    .api-stat-box.accent-blue   { border-color: #BFDBFE; background: #EFF6FF; }
    .api-stat-box.accent-amber  { border-color: #FDE68A; background: #FFFBEB; }

    .api-stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
    }

    .api-stat-value {
        font-family: 'Recoleta-Bold', serif;
        font-size: clamp(26px, 3vw, 38px);
        color: #310669;
        line-height: 1.1;
        margin-top: 6px;
    }

    .api-stat-box.accent-green .api-stat-value { color: #065F46; }
    .api-stat-box.accent-blue .api-stat-value  { color: #1E40AF; }
    .api-stat-box.accent-amber .api-stat-value { color: #92400E; }

    /* Charts Grid */
    .api-charts-grid {
        display: grid;
        grid-template-columns: 1.55fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
        align-items: start;
    }

    .api-col-stack {
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

    /* Stock-Style Timeframe & Series Filter */
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

    .stock-legend-row {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 12px;
        color: #4B5563;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .stock-chart-container {
        position: relative;
        width: 100%;
    }

    .stock-svg {
        display: block;
        width: 100%;
        height: 235px;
        overflow: visible;
        cursor: crosshair;
    }

    .stock-tooltip {
        position: absolute;
        top: 6px;
        left: 10px;
        background: #310669;
        color: #FFFFFF;
        padding: 7px 12px;
        border-radius: 10px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.12s ease;
        z-index: 10;
    }

    /* Progress / Breakdown Bars */
    .bar-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .bar-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .bar-meta {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #1F2937;
    }

    .bar-track {
        width: 100%;
        height: 11px;
        background: #F3E8FF;
        border-radius: 999px;
        overflow: hidden;
    }

    .bar-fill-purple {
        height: 100%;
        background: linear-gradient(90deg, #8a38f5, #a855f7);
        border-radius: 999px;
    }

    .bar-fill-green {
        height: 100%;
        background: #10B981;
        border-radius: 999px;
    }

    /* Service Filter Tabs & Form Controls */
    .service-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }

    .service-tab {
        background: #F9FAFB;
        border: 2px solid #E5E7EB;
        color: #6B7280;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 12px;
        text-decoration: none;
        text-transform: uppercase;
        transition: all 0.15s ease;
    }

    .service-tab:hover {
        border-color: #8a38f5;
        color: #310669;
        background: #FAF5FF;
    }

    .service-tab.active {
        background: #8a38f5;
        border-color: #8a38f5;
        color: #FFFFFF;
    }

    .api-filter-row {
        display: grid;
        grid-template-columns: 160px 160px 1fr auto;
        gap: 10px;
        align-items: end;
        margin-bottom: 18px;
        padding-bottom: 16px;
        border-bottom: 1.5px dashed #E5E7EB;
    }

    .candy-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        color: #6B7280;
        margin-bottom: 5px;
    }

    .candy-input,
    .candy-select {
        width: 100%;
        padding: 10px 14px;
        border: 2.5px solid #E5E7EB;
        border-radius: 14px;
        font-family: 'modern-bold', sans-serif;
        font-size: 13px;
        color: #1F2937;
        background: #FFFFFF;
        outline: none;
        box-sizing: border-box;
    }

    .candy-input:focus,
    .candy-select:focus {
        border-color: #8a38f5;
    }

    .pill-btn-purple-sm {
        background: #8a38f5;
        color: #FFFFFF;
        font-family: 'modern-bold', sans-serif;
        font-size: 12px;
        padding: 11px 20px;
        border-radius: 14px;
        text-transform: uppercase;
        border: none;
        box-shadow: 0px 4px 0px #560db6;
        cursor: pointer;
    }

    .pill-btn-purple-sm:hover {
        transform: translateY(1px);
        box-shadow: 0px 3px 0px #560db6;
    }

    /* Request Log Table */
    .api-table {
        width: 100%;
        border-collapse: collapse;
    }

    .api-table thead th {
        padding: 12px 14px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6B7280;
        border-bottom: 2px solid #F3F4F6;
        text-align: left;
    }

    .api-table tbody td {
        padding: 13px 14px;
        border-bottom: 1.5px solid #F3F4F6;
        font-size: 13px;
        color: #1F2937;
        vertical-align: middle;
    }

    .api-table tbody tr:hover {
        background: #FAF5FF;
    }

    .method-pill {
        background: #F3E8FF;
        color: #6B21A8;
        font-size: 11px;
        padding: 4px 9px;
        border-radius: 8px;
        text-transform: uppercase;
    }

    .service-badge {
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        margin-right: 6px;
    }

    .service-badge.is-chat    { background: #DEF7EC; color: #03543F; }
    .service-badge.is-vision  { background: #DBEAFE; color: #1E40AF; }
    .service-badge.is-general { background: #F3F4F6; color: #4B5563; }

    .status-pill {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .status-pill.is-2xx { background: #DEF7EC; color: #03543F; }
    .status-pill.is-4xx { background: #FEF3C7; color: #92400E; }
    .status-pill.is-5xx { background: #FEE2E2; color: #991B1B; }

    @media (max-width: 1100px) {
        .api-stats-grid  { grid-template-columns: repeat(2, 1fr); }
        .api-charts-grid { grid-template-columns: 1fr; }
        .api-filter-row  { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 600px) {
        .api-stats-grid { grid-template-columns: 1fr; }
        .api-filter-row { grid-template-columns: 1fr; }
    }
</style>

@php
    $activeService = $filters['service'] ?? request('service', 'all');
    $summary = $apiSummary ?? [
        'totalRequests'   => $requests->total(),
        'chatbotRequests' => 0,
        'chatbotAvgMs'    => 0,
        'successRate'     => 100.0,
    ];
    $statuses = $statusBreakdown ?? ['2xx' => 0, '3xx' => 0, '4xx' => 0, '5xx' => 0];
    $tiers = $chatbotTiers ?? [];
@endphp

<div class="api-dash-wrap">
    <!-- 1. HEADER -->
    <div class="api-header-area">
        <div>
            <div class="api-greeting-sub">Developer &amp; AI Telemetry</div>
            <h1 class="api-display-title">API &amp; Chatbot Logs</h1>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a class="pill-btn-outline" href="{{ route('chat.page') }}" target="_blank">Open Jive Chatbot &nearr;</a>
            <a class="pill-btn-outline" href="{{ route('admin.dashboard') }}">&larr; Dashboard</a>
        </div>
    </div>

    <!-- 2. 4 KPI STAT BOXES -->
    <div class="api-stats-grid">
        <div class="api-stat-box accent-purple">
            <div class="api-stat-label">Total Recorded API Calls</div>
            <div class="api-stat-value">{{ number_format($summary['totalRequests']) }}</div>
        </div>
        <div class="api-stat-box accent-green">
            <div class="api-stat-label">Jive Chatbot Queries</div>
            <div class="api-stat-value">{{ number_format($summary['chatbotRequests']) }}</div>
        </div>
        <div class="api-stat-box accent-blue">
            <div class="api-stat-label">Chatbot Avg Response</div>
            <div class="api-stat-value">{{ number_format($summary['chatbotAvgMs']) }} ms</div>
        </div>
        <div class="api-stat-box accent-amber">
            <div class="api-stat-label">API Success Rate</div>
            <div class="api-stat-value">{{ number_format($summary['successRate'], 1) }}%</div>
        </div>
    </div>

    <!-- 3. DETAILED CHARTS SECTION -->
    <div class="api-charts-grid">
        <!-- Left: Stock-Style Dual Line Chart (All API Traffic vs Jive Chatbot) -->
        <div class="dash-card">
            <div class="dash-card-header">
                <div>
                    <h2 class="dash-card-title">API &amp; Chatbot Traffic History</h2>
                    <span style="font-size: 12px; color: #6B7280;">Daily API volume compared with Jive AI assistant requests</span>
                </div>
                <div class="stock-filter-bar" id="apiRangeFilters">
                    <button type="button" class="stock-range-btn" data-range="7D">7D</button>
                    <button type="button" class="stock-range-btn active" data-range="30D">30D</button>
                    <button type="button" class="stock-range-btn" data-range="90D">90D</button>
                    <button type="button" class="stock-range-btn" data-range="ALL">ALL</button>
                </div>
            </div>

            <div class="stock-summary-strip">
                <span class="stock-big-metric" id="apiChartWindowSummary">0 requests</span>
                <div class="stock-legend-row">
                    <span><span class="legend-dot" style="background: #8a38f5;"></span>All API Calls</span>
                    <span><span class="legend-dot" style="background: #10B981;"></span>Jive Chatbot</span>
                </div>
            </div>

            <div class="stock-chart-container">
                <div class="stock-tooltip" id="apiChartTooltip"></div>
                <svg class="stock-svg" id="apiDualTrafficChart" viewBox="0 0 760 235" role="img" aria-label="API and Chatbot volume history chart">
                    <defs>
                        <linearGradient id="apiTotalFill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#8a38f5" stop-opacity="0.22"/>
                            <stop offset="100%" stop-color="#8a38f5" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="30" x2="760" y2="30"/>
                    <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="85" x2="760" y2="85"/>
                    <line stroke="#F3F4F6" stroke-dasharray="4 4" x1="0" y1="140" x2="760" y2="140"/>
                    <line stroke="#E5E7EB" x1="0" y1="195" x2="760" y2="195"/>
                    <polygon id="apiTotalArea" fill="url(#apiTotalFill)" points="0,195 760,195"/>
                    <polyline id="apiTotalLine" fill="none" stroke="#8a38f5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="0,195 760,195"/>
                    <polyline id="apiChatLine" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="0,195 760,195"/>
                    <line id="apiHoverLine" x1="0" y1="20" x2="0" y2="195" stroke="#310669" stroke-width="1.5" stroke-dasharray="3 3" style="display: none;"/>
                    <circle id="apiHoverDotTotal" cx="0" cy="0" r="5" fill="#8a38f5" stroke="#FFFFFF" stroke-width="2" style="display: none;"/>
                    <circle id="apiHoverDotChat" cx="0" cy="0" r="5" fill="#10B981" stroke="#FFFFFF" stroke-width="2" style="display: none;"/>
                    <g id="apiDualLabels" fill="#6B7280" font-size="11"></g>
                </svg>
            </div>
        </div>

        <!-- Right: Jive Chatbot Tier Resolution + HTTP Status Distribution -->
        <div class="api-col-stack">
            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Jive Chatbot Resolution Tiers</h2>
                        <span style="font-size: 12px; color: #6B7280;">DB instant lookups vs OpenRouter LLM calls</span>
                    </div>
                </div>

                @php($maxTier = max(1, (int) collect($tiers)->max()))
                <div class="bar-list">
                    @foreach($tiers as $tierName => $tierCount)
                        <div class="bar-item">
                            <div class="bar-meta">
                                <span>{{ $tierName }}</span>
                                <span style="color: #8a38f5;">{{ number_format($tierCount) }} calls</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill-purple" style="width: {{ round(($tierCount / $maxTier) * 100) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="dash-card">
                <div class="dash-card-header">
                    <div>
                        <h2 class="dash-card-title">Response Status Breakdown</h2>
                        <span style="font-size: 12px; color: #6B7280;">HTTP status distribution across endpoints</span>
                    </div>
                </div>

                @php($maxStatus = max(1, (int) collect($statuses)->max()))
                <div class="bar-list">
                    @foreach($statuses as $codeGroup => $count)
                        <div class="bar-item">
                            <div class="bar-meta">
                                <span>HTTP {{ $codeGroup }}</span>
                                <span style="color: #059669;">{{ number_format($count) }}</span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill-green" style="width: {{ round(($count / $maxStatus) * 100) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- 4. FILTERABLE REQUEST LOG TABLE -->
    <div class="dash-card">
        <div class="dash-card-header">
            <div>
                <h2 class="dash-card-title">Instrumented Request Log</h2>
                <span style="font-size: 12px; color: #6B7280;">Filter by service, HTTP method, response code, or route</span>
            </div>
        </div>

        <!-- Service Quick Tabs -->
        <div class="service-tabs">
            @foreach(['all' => 'All APIs', 'chatbot' => 'Jive Chatbot', 'vision' => 'Vision Recognition', 'general' => 'General Endpoints'] as $svcKey => $svcLabel)
                <a href="{{ route('developer.api-requests.index', array_merge(request()->except('page'), ['service' => $svcKey])) }}"
                   class="service-tab {{ $activeService === $svcKey ? 'active' : '' }}">
                    {{ $svcLabel }}
                </a>
            @endforeach
        </div>

        <!-- Detailed Filter Form -->
        <form method="GET" action="{{ route('developer.api-requests.index') }}" class="api-filter-row">
            <input type="hidden" name="service" value="{{ $activeService }}">
            <div>
                <label for="method" class="candy-label">Method</label>
                <select id="method" name="method" class="candy-select">
                    <option value="">All methods</option>
                    @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                        <option value="{{ $method }}" @selected(($filters['method'] ?? '') === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="candy-label">Status Code</label>
                <input id="status" name="status" type="number" min="100" max="599" class="candy-input" value="{{ $filters['status'] ?? '' }}" placeholder="e.g. 200">
            </div>
            <div>
                <label for="path" class="candy-label">Route / Tier Contains</label>
                <input id="path" name="path" class="candy-input" maxlength="100" value="{{ $filters['path'] ?? '' }}" placeholder="e.g. chat, tier4-openrouter, api/vision">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="pill-btn-purple-sm">Filter</button>
                <a href="{{ route('developer.api-requests.index') }}" class="pill-btn-outline" style="padding: 10px 14px; font-size: 12px;">Reset</a>
            </div>
        </form>

        <div style="overflow-x: auto;">
            <table class="api-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Service</th>
                        <th>Method</th>
                        <th>Endpoint &amp; Tier</th>
                        <th>Status</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $requestMetric)
                    @php
                        $isChat = str_contains(strtolower($requestMetric->path), 'chat');
                        $isVision = str_contains(strtolower($requestMetric->path), 'vision');
                        $statusClass = $requestMetric->status >= 500 ? 'is-5xx' : ($requestMetric->status >= 400 ? 'is-4xx' : 'is-2xx');
                    @endphp
                    <tr>
                        <td style="color: #6B7280; white-space: nowrap;">{{ $requestMetric->created_at?->format('M j, Y H:i:s') }}</td>
                        <td>
                            @if($isChat)
                                <span class="service-badge is-chat">Chatbot</span>
                            @elseif($isVision)
                                <span class="service-badge is-vision">Vision</span>
                            @else
                                <span class="service-badge is-general">API</span>
                            @endif
                        </td>
                        <td><span class="method-pill">{{ $requestMetric->method }}</span></td>
                        <td><code style="color: #310669; font-size: 12px;">{{ $requestMetric->path }}</code></td>
                        <td><span class="status-pill {{ $statusClass }}">{{ $requestMetric->status }}</span></td>
                        <td style="white-space: nowrap;">{{ number_format($requestMetric->duration_ms) }} ms</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6B7280; padding: 32px;">
                            No tracked API or chatbot requests match these filters.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span style="font-size: 12px; color: #6B7280;">
                Only instrumented API &amp; chatbot routes are recorded. Request bodies and client IP addresses are not collected.
            </span>
            <div>{{ $requests->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const numberFormat = new Intl.NumberFormat();
    const rawSeries = @js($dailyTotals ?? []);
    let activeRange = '30D';
    let renderedTotalCoords = [];
    let renderedChatCoords = [];
    let renderedPoints = [];

    function filterSeries(range) {
        if (!rawSeries.length) return [];
        const latestTs = new Date(rawSeries[rawSeries.length - 1].timestamp).getTime();
        const windows = {
            '7D': 7 * 86400 * 1000,
            '30D': 30 * 86400 * 1000,
            '90D': 90 * 86400 * 1000,
            'ALL': Infinity
        };
        const cutoff = windows[range] === Infinity ? 0 : latestTs - windows[range];
        const filtered = rawSeries.filter(pt => new Date(pt.timestamp).getTime() >= cutoff);
        return filtered.length ? filtered : rawSeries;
    }

    function renderDualChart() {
        const points = filterSeries(activeRange);
        const totalLine = document.getElementById('apiTotalLine');
        const totalArea = document.getElementById('apiTotalArea');
        const chatLine = document.getElementById('apiChatLine');
        const labelsEl = document.getElementById('apiDualLabels');
        const summaryEl = document.getElementById('apiChartWindowSummary');

        if (!points.length) {
            summaryEl.textContent = '0 recorded calls';
            return;
        }

        const totalCalls = points.reduce((sum, p) => sum + Number(p.total || 0), 0);
        const chatCalls = points.reduce((sum, p) => sum + Number(p.chatbot || 0), 0);
        summaryEl.textContent = `${numberFormat.format(totalCalls)} total · ${numberFormat.format(chatCalls)} chatbot (${activeRange})`;

        const maxVal = Math.max(1, ...points.map(p => Math.max(Number(p.total || 0), Number(p.chatbot || 0))));

        const totalCoords = points.map((p, idx) => {
            const x = 12 + idx * (736 / Math.max(1, points.length - 1));
            const y = 195 - (Number(p.total || 0) / maxVal) * 160;
            return [x, y];
        });

        const chatCoords = points.map((p, idx) => {
            const x = 12 + idx * (736 / Math.max(1, points.length - 1));
            const y = 195 - (Number(p.chatbot || 0) / maxVal) * 160;
            return [x, y];
        });

        renderedTotalCoords = totalCoords;
        renderedChatCoords = chatCoords;
        renderedPoints = points;

        const totalPtsStr = totalCoords.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(' ');
        const chatPtsStr = chatCoords.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(' ');

        totalLine.setAttribute('points', totalPtsStr);
        totalArea.setAttribute('points', `${totalCoords[0][0].toFixed(1)},195 ${totalPtsStr} ${totalCoords[totalCoords.length - 1][0].toFixed(1)},195`);
        chatLine.setAttribute('points', chatPtsStr);

        labelsEl.replaceChildren();
        const ticks = Math.min(5, points.length);
        for (let i = 0; i < ticks; i++) {
            const idx = Math.round((i / Math.max(1, ticks - 1)) * (points.length - 1));
            const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            t.setAttribute('x', String(totalCoords[idx][0]));
            t.setAttribute('y', '220');
            t.setAttribute('text-anchor', i === 0 ? 'start' : (i === ticks - 1 ? 'end' : 'middle'));
            t.textContent = points[idx].label;
            labelsEl.appendChild(t);
        }
    }

    const svgEl = document.getElementById('apiDualTrafficChart');
    const hoverLine = document.getElementById('apiHoverLine');
    const dotTotal = document.getElementById('apiHoverDotTotal');
    const dotChat = document.getElementById('apiHoverDotChat');
    const tooltipEl = document.getElementById('apiChartTooltip');

    if (svgEl) {
        svgEl.addEventListener('mousemove', (e) => {
            if (!renderedTotalCoords.length) return;
            const rect = svgEl.getBoundingClientRect();
            const relX = ((e.clientX - rect.left) / rect.width) * 760;

            let bestIdx = 0;
            let bestDist = Infinity;
            renderedTotalCoords.forEach(([x], idx) => {
                const d = Math.abs(x - relX);
                if (d < bestDist) {
                    bestDist = d;
                    bestIdx = i = idx;
                }
            });

            const [tx, ty] = renderedTotalCoords[bestIdx];
            const [cx, cy] = renderedChatCoords[bestIdx];
            const pt = renderedPoints[bestIdx];

            hoverLine.setAttribute('x1', tx);
            hoverLine.setAttribute('x2', tx);
            hoverLine.style.display = 'block';

            dotTotal.setAttribute('cx', tx);
            dotTotal.setAttribute('cy', ty);
            dotTotal.style.display = 'block';

            dotChat.setAttribute('cx', cx);
            dotChat.setAttribute('cy', cy);
            dotChat.style.display = 'block';

            tooltipEl.textContent = `${pt.label} · All API: ${numberFormat.format(pt.total)} | Chatbot: ${numberFormat.format(pt.chatbot)} (Avg ${pt.avgMs} ms)`;
            tooltipEl.style.opacity = '1';
        });

        svgEl.addEventListener('mouseleave', () => {
            hoverLine.style.display = 'none';
            dotTotal.style.display = 'none';
            dotChat.style.display = 'none';
            tooltipEl.style.opacity = '0';
        });
    }

    document.querySelectorAll('#apiRangeFilters .stock-range-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#apiRangeFilters .stock-range-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeRange = btn.dataset.range;
            renderDualChart();
        });
    });

    renderDualChart();
})();
</script>
@endpush