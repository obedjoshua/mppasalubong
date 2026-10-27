@extends('layouts.portal')

@section('title', ucfirst($portal).' overview')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="portal-muted small fw-bold text-uppercase mb-1">Operations center</p>
        <h1 class="portal-heading h2 mb-1">{{ ucfirst($portal) }} dashboard</h1>
        <p class="portal-subtitle mb-0">Live service activity and platform overview.</p>
    </div>
    <div class="portal-pill"><span class="portal-live-dot"></span>Updated <time id="portal-updated-at">{{ \Illuminate\Support\Carbon::parse($liveData['updatedAt'])->format('H:i:s') }}</time></div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['all', 'Users', 'portal-stat-purple'],
        ['total', 'API requests · 24h', 'portal-stat-blue'],
        ['successful', 'Successful responses', 'portal-stat-green'],
        ['serverErrors', 'Server errors', 'portal-stat-lime'],
    ] as [$key, $label, $color])
        @php($value = $key === 'all' ? $userCounts[$key] : $liveData['stats'][$key])
        <div class="col-6 col-xl-3">
            <section class="portal-stat-card {{ $color }}">
                <div class="portal-stat-label">{{ $label }}</div>
                <div class="portal-stat-value" id="portal-stat-{{ $key }}">{{ number_format($value) }}</div>
            </section>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <section class="portal-module">
            <div class="portal-module-header">
                <div>
                    <h2 class="portal-module-title">API traffic</h2>
                    <p class="portal-muted small mb-0">Tracked requests by hour · last 24 hours</p>
                </div>
                <a class="portal-pill text-decoration-none" href="{{ route('developer.api-requests.index') }}">Request logs</a>
            </div>
            <div class="portal-chart-wrap">
                <svg class="portal-chart" id="portal-traffic-chart" viewBox="0 0 760 220" role="img" aria-label="API requests by hour over the last 24 hours">
                    <defs>
                        <linearGradient id="portalChartFill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#7950d8" stop-opacity=".22"/>
                            <stop offset="100%" stop-color="#7950d8" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <line class="portal-chart-grid" x1="0" y1="30" x2="760" y2="30"/>
                    <line class="portal-chart-grid" x1="0" y1="80" x2="760" y2="80"/>
                    <line class="portal-chart-grid" x1="0" y1="130" x2="760" y2="130"/>
                    <line class="portal-chart-grid" x1="0" y1="180" x2="760" y2="180"/>
                    <polygon class="portal-chart-area" id="portal-traffic-area" points="0,180 760,180"/>
                    <polyline class="portal-chart-line" id="portal-traffic-line" points="0,180 760,180"/>
                    <g id="portal-traffic-labels" fill="#92909e" font-size="11"></g>
                </svg>
                <div class="d-flex flex-wrap gap-3 px-1 pb-2 small portal-muted">
                    <span><span class="portal-live-dot"></span>Tracked API requests</span>
                    <span>Average latency: <strong id="portal-stat-averageDuration">{{ number_format($liveData['stats']['averageDuration']) }} ms</strong></span>
                    <span>Client errors: <strong id="portal-stat-clientErrors">{{ number_format($liveData['stats']['clientErrors']) }}</strong></span>
                </div>
            </div>
        </section>
    </div>
    <div class="col-xl-4">
        <section class="portal-module">
            <div class="portal-module-header">
                <div>
                    <h2 class="portal-module-title">Live request feed</h2>
                    <p class="portal-muted small mb-0">Refreshes every 15 seconds</p>
                </div>
                <span class="portal-pill"><span class="portal-live-dot"></span>Live</span>
            </div>
            <div class="portal-feed-list" id="portal-live-feed" aria-live="polite">
                @forelse($liveData['feed'] as $entry)
                    <div class="portal-feed-item">
                        <span class="portal-feed-method">{{ $entry['method'] }}</span>
                        <span class="portal-feed-path" title="{{ $entry['path'] }}">{{ $entry['path'] }}</span>
                        <span class="portal-feed-time">{{ $entry['time'] }}</span>
                    </div>
                @empty
                    <p class="portal-muted small p-3 mb-0">Waiting for instrumented API activity.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <section class="portal-module">
            <div class="portal-module-header">
                <div>
                    <h2 class="portal-module-title">Recent API requests</h2>
                    <p class="portal-muted small mb-0">Latest tracked endpoint activity</p>
                </div>
                <a href="{{ route('developer.api-requests.index') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table portal-table table-hover mb-0">
                    <thead><tr><th>Time</th><th>Method</th><th>Endpoint</th><th>Status</th><th>Duration</th></tr></thead>
                    <tbody id="portal-recent-requests">
                    @forelse($liveData['feed'] as $entry)
                        <tr>
                            <td>{{ $entry['time'] }}</td>
                            <td><span class="portal-pill">{{ $entry['method'] }}</span></td>
                            <td><code>{{ $entry['path'] }}</code></td>
                            <td><span class="badge {{ $entry['status'] >= 500 ? 'text-bg-danger' : ($entry['status'] >= 400 ? 'text-bg-warning' : 'text-bg-success') }}">{{ $entry['status'] }}</span></td>
                            <td>{{ $entry['duration'] }} ms</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="portal-muted text-center py-4">No tracked API requests yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="portal-module">
            <div class="portal-module-header">
                <div>
                    <h2 class="portal-module-title">Platform modules</h2>
                    <p class="portal-muted small mb-0">Accounts, moderation and connections</p>
                </div>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <span><strong class="d-block">User access</strong><small class="portal-muted">Manage accounts and roles</small></span><span class="portal-pill">{{ number_format($userCounts['all']) }}</span>
                </a>
                <a href="{{ route('developer.posts.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                    <span><strong class="d-block">Post review</strong><small class="portal-muted">Moderate community content</small></span><span class="portal-pill">{{ number_format($pendingPosts) }} hidden</span>
                </a>
                @foreach($connections as $name => $connection)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span><strong class="d-block text-capitalize">{{ $name }} connection</strong><small class="portal-muted">{{ $connection['detail'] }}</small></span>
                        <span class="badge {{ $connection['healthy'] ? 'text-bg-success' : 'text-bg-danger' }}">{{ $connection['healthy'] ? 'Healthy' : 'Unavailable' }}</span>
                    </div>
                @endforeach
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong class="d-block">Git CLI</strong><small class="portal-muted">Read-only repository status</small></span>
                        <span class="badge {{ $gitStatus['available'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $gitStatus['available'] ? 'Available' : 'Unavailable' }}</span>
                    </div>
                    <pre class="small portal-muted mt-2 mb-0">{{ $gitStatus['output'] }}</pre>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="small portal-muted">
    User totals: {{ number_format($userCounts['customers']) }} customers · {{ number_format($userCounts['staff']) }} staff · {{ number_format($userCounts['developers']) }} developers · {{ number_format($userCounts['admins']) }} admins.
    Traffic reflects instrumented API endpoints only; request bodies and client IP addresses are not collected.
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const feed = document.getElementById('portal-live-feed');
        const recentRows = document.getElementById('portal-recent-requests');
        const chartLine = document.getElementById('portal-traffic-line');
        const chartArea = document.getElementById('portal-traffic-area');
        const labels = document.getElementById('portal-traffic-labels');
        const numberFormat = new Intl.NumberFormat();
        const apiUrl = @js(route($portal === 'admin' ? 'admin.dashboard.live' : 'developer.dashboard.live'));

        function renderChart(points) {
            const values = points.map(point => point.count);
            const maximum = Math.max(1, ...values);
            const coordinates = values.map((value, index) => {
                const x = 8 + index * (744 / Math.max(1, values.length - 1));
                const y = 180 - (value / maximum) * 145;
                return [x, y];
            });
            const pointsText = coordinates.map(([x, y]) => `${x},${y}`).join(' ');
            chartLine.setAttribute('points', pointsText);
            chartArea.setAttribute('points', `${coordinates[0][0]},180 ${pointsText} ${coordinates[coordinates.length - 1][0]},180`);
            labels.replaceChildren();
            [0, 6, 12, 18, 23].forEach(index => {
                if (!points[index]) return;
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', String(coordinates[index][0]));
                text.setAttribute('y', '207');
                text.setAttribute('text-anchor', index === 0 ? 'start' : (index === 23 ? 'end' : 'middle'));
                text.textContent = points[index].label;
                labels.appendChild(text);
            });
        }

        function renderFeed(entries) {
            feed.replaceChildren();
            recentRows.replaceChildren();
            if (!entries.length) {
                const empty = document.createElement('p');
                empty.className = 'portal-muted small p-3 mb-0';
                empty.textContent = 'Waiting for instrumented API activity.';
                feed.appendChild(empty);
                const row = recentRows.insertRow();
                const cell = row.insertCell();
                cell.colSpan = 5;
                cell.className = 'portal-muted text-center py-4';
                cell.textContent = 'No tracked API requests yet.';
                return;
            }

            entries.forEach(entry => {
                const item = document.createElement('div');
                item.className = 'portal-feed-item';
                [entry.method, entry.path, entry.time].forEach((value, index) => {
                    const span = document.createElement('span');
                    span.className = index === 0 ? 'portal-feed-method' : (index === 1 ? 'portal-feed-path' : 'portal-feed-time');
                    span.textContent = value;
                    if (index === 1) span.title = value;
                    item.appendChild(span);
                });
                feed.appendChild(item);

                const row = recentRows.insertRow();
                [entry.time, entry.method, entry.path, entry.status, `${entry.duration} ms`].forEach((value, index) => {
                    const cell = row.insertCell();
                    if (index === 0 || index === 4) {
                        cell.textContent = value;
                    } else if (index === 1) {
                        const method = document.createElement('span');
                        method.className = 'portal-pill';
                        method.textContent = value;
                        cell.appendChild(method);
                    } else if (index === 2) {
                        const path = document.createElement('code');
                        path.textContent = value;
                        cell.appendChild(path);
                    } else {
                        const badge = document.createElement('span');
                        badge.className = `badge ${value >= 500 ? 'text-bg-danger' : (value >= 400 ? 'text-bg-warning' : 'text-bg-success')}`;
                        badge.textContent = value;
                        cell.appendChild(badge);
                    }
                });
            });
        }

        function refreshDashboard(data) {
            document.getElementById('portal-stat-total').textContent = numberFormat.format(data.stats.total);
            document.getElementById('portal-stat-successful').textContent = numberFormat.format(data.stats.successful);
            document.getElementById('portal-stat-serverErrors').textContent = numberFormat.format(data.stats.serverErrors);
            document.getElementById('portal-stat-clientErrors').textContent = numberFormat.format(data.stats.clientErrors);
            document.getElementById('portal-stat-averageDuration').textContent = `${numberFormat.format(data.stats.averageDuration)} ms`;
            document.getElementById('portal-updated-at').textContent = new Date(data.updatedAt).toLocaleTimeString();
            renderChart(data.traffic);
            renderFeed(data.feed);
        }

        renderChart(@js($liveData['traffic']));
        window.setInterval(async () => {
            try {
                const response = await fetch(apiUrl, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`Dashboard refresh failed (${response.status})`);
                refreshDashboard(await response.json());
            } catch (error) {
                console.error(error);
            }
        }, 15000);
    })();
</script>
@endpush
