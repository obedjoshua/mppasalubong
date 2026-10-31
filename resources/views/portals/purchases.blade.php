@extends('layouts.portal')

@section('title', 'Purchases')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="portal-muted small fw-bold text-uppercase mb-1">Store management</p>
        <h1 class="portal-heading h2 mb-1">Purchases</h1>
        <p class="portal-subtitle mb-0">View and manage customer purchases.</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<section class="portal-module">
    <div class="portal-module-header">
        <div>
            <h2 class="portal-module-title">All Purchases</h2>
            <p class="portal-muted small mb-0">Customer purchases from the online shop.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table portal-table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($purchases as $purchase)
                <tr>
                    <td><strong>#{{ $purchase->id }}</strong></td>
                    <td>
                        {{ $purchase->user->name ?? 'Guest' }}
                        <br><small class="portal-muted">{{ $purchase->user->email ?? 'N/A' }}</small>
                    </td>
                    <td>
                        {{ $purchase->product->name ?? 'N/A' }}
                        <br><small class="portal-muted">{{ $purchase->product->category ?? '' }}</small>
                    </td>
                    <td>{{ $purchase->quantity }}</td>
                    <td>&#8369;{{ number_format((float) $purchase->total_price, 2) }}</td>
                    <td>
                        <span class="portal-pill 
                            @if($purchase->status === 'completed') bg-success
                            @elseif($purchase->status === 'pending') bg-warning
                            @else bg-danger @endif">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('M d, Y H:i') }}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.purchases.show', $purchase) }}">View</a>
                            <form method="POST" action="{{ route('admin.purchases.status', $purchase) }}">
                                @csrf @method('PUT')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $purchase->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $purchase->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="portal-muted text-center py-4">No purchases yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $purchases->links() }}</div>
</section>
@endsection
