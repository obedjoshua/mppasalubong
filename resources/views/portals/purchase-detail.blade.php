@extends('layouts.portal')

@section('title', 'Purchase #' . $purchase->id)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <a class="small text-decoration-none" href="{{ route('admin.purchases.index') }}">&larr; Purchases</a>
        <h1 class="portal-heading h2 mt-2 mb-1">Purchase #{{ $purchase->id }}</h1>
        <p class="portal-subtitle mb-0">View and manage this purchase.</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <section class="portal-module p-4">
            <h2 class="portal-module-title mb-3">Order Details</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Order Number</label>
                    <p class="mb-0">#{{ $purchase->id }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($purchase->created_at)->format('M d, Y h:i A') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <p class="mb-0">
                        <span class="portal-pill 
                            @if($purchase->status === 'completed') bg-success
                            @elseif($purchase->status === 'pending') bg-warning
                            @else bg-danger @endif">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Total</label>
                    <p class="mb-0 fw-bold">&#8369;{{ number_format((float) $purchase->total_price, 2) }}</p>
                </div>
            </div>
        </section>

        <section class="portal-module p-4 mt-4">
            <h2 class="portal-module-title mb-3">Customer Information</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name</label>
                    <p class="mb-0">{{ $purchase->user->name ?? 'Guest' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <p class="mb-0">{{ $purchase->user->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <p class="mb-0">{{ $purchase->user->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </section>
    </div>

    <div class="col-lg-4">
        <section class="portal-module p-4">
            <h2 class="portal-module-title mb-3">Product</h2>
            @if($purchase->product)
                <div class="d-flex gap-3 mb-3">
                    <img src="{{ $purchase->product->image_url ? (str_starts_with($purchase->product->image_url, 'http') ? $purchase->product->image_url : asset('storage/' . $purchase->product->image_url)) : asset('images/logo.png') }}" alt="{{ $purchase->product->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px;">
                    <div>
                        <h5 class="mb-1">{{ $purchase->product->name }}</h5>
                        <p class="portal-muted small mb-1">{{ $purchase->product->category }}</p>
                        <p class="mb-0 fw-bold">&#8369;{{ number_format((float) $purchase->product->price, 2) }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Quantity</label>
                        <p class="mb-0">{{ $purchase->quantity }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Line Total</label>
                        <p class="mb-0 fw-bold">&#8369;{{ number_format((float) ($purchase->product->price * $purchase->quantity), 2) }}</p>
                    </div>
                </div>
            @else
                <p class="portal-muted">Product no longer available</p>
            @endif
        </section>

        <section class="portal-module p-4 mt-4">
            <h2 class="portal-module-title mb-3">Update Status</h2>
            <form method="POST" action="{{ route('admin.purchases.status', $purchase) }}">
                @csrf @method('PUT')
                <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                    <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $purchase->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $purchase->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <small class="portal-muted">Status changes are saved automatically.</small>
            </form>
        </section>
    </div>
</div>
@endsection
