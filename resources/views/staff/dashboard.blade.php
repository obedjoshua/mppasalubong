@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/dashboard.css') }}">
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Staff Operations Dashboard</h1>
        <p class="text-muted small mb-0">Real-time order metrics and daily branch pickup schedules.</p>
    </div>
    <a href="{{ route('staff.walkin.create') }}" class="btn btn-primary fw-bold shadow-sm">
        + New Walk-In Order
    </a>
</div>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-4">
        <div class="card border-warning bg-warning-subtle h-100 shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle fw-bold text-warning-emphasis text-uppercase small">Pending Staff Review</h6>
                <p class="display-6 fw-bold text-warning-emphasis mb-0 mt-2">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-info bg-info-subtle h-100 shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle fw-bold text-info-emphasis text-uppercase small">Approved for Payment</h6>
                <p class="display-6 fw-bold text-info-emphasis mb-0 mt-2">{{ $approvedCount }}</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="card border-primary bg-primary-subtle h-100 shadow-sm">
            <div class="card-body">
                <h6 class="card-subtitle fw-bold text-primary-emphasis text-uppercase small">In Production</h6>
                <p class="display-6 fw-bold text-primary-emphasis mb-0 mt-2">{{ $inProductionCount }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Today's Scheduled Pickups Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0">Today's Scheduled Pickups</h5>
        <span class="badge bg-secondary rounded-pill">{{ $todayPickups->count() }} Pickups</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small">
                <tr>
                    <th>Ticket</th>
                    <th>Customer</th>
                    <th>Time Slot</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayPickups as $ticket)
                    <tr>
                        <td class="font-monospace fw-bold text-primary">#{{ $ticket->id }}</td>
                        <td class="fw-semibold">{{ $ticket->customer_name }}</td>
                        <td>{{ $ticket->pickup_time }}</td>
                        <td>{{ $ticket->branch_name }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary fw-bold">Inspect &rarr;</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No custom orders scheduled for pickup today.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection