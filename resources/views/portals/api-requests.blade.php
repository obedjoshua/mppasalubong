@extends('layouts.portal')

@section('title', 'API requests')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">API request log</h1>
    <p class="text-secondary mb-0">Review recorded requests and filter by method, response code, or route.</p>
</div>
<form method="GET" class="portal-module p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label for="method" class="form-label">Method</label>
            <select id="method" name="method" class="form-select">
                <option value="">All methods</option>
                @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                    <option value="{{ $method }}" @selected(($filters['method'] ?? '') === $method)>{{ $method }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <label for="status" class="form-label">Status</label>
            <input id="status" name="status" type="number" min="100" max="599" class="form-control" value="{{ $filters['status'] ?? '' }}" placeholder="e.g. 200">
        </div>
        <div class="col-sm-4">
            <label for="path" class="form-label">Route contains</label>
            <input id="path" name="path" class="form-control" maxlength="100" value="{{ $filters['path'] ?? '' }}" placeholder="api/vision">
        </div>
        <div class="col-sm-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('developer.api-requests.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>
<div class="portal-module">
    <div class="table-responsive">
        <table class="table portal-table table-hover align-middle mb-0">
            <thead><tr><th>Time</th><th>Method</th><th>Endpoint</th><th>Status</th><th>Duration</th></tr></thead>
            <tbody>
            @forelse($requests as $requestMetric)
                <tr>
                    <td>{{ $requestMetric->created_at->format('M j, Y H:i:s') }}</td>
                    <td>{{ $requestMetric->method }}</td>
                    <td><code>{{ $requestMetric->path }}</code></td>
                    <td><span class="badge {{ $requestMetric->status >= 500 ? 'text-bg-danger' : ($requestMetric->status >= 400 ? 'text-bg-warning' : 'text-bg-success') }}">{{ $requestMetric->status }}</span></td>
                    <td>{{ $requestMetric->duration_ms }} ms</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-5">No tracked API requests match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <p class="small text-secondary">Only instrumented API routes are recorded. Request bodies, credentials, and client IP addresses are not collected.</p>
        {{ $requests->links() }}
    </div>
</div>
@endsection
