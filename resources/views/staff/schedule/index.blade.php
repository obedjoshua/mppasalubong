@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/schedule.css') }}">
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Production Schedule</h1>
        <p class="text-muted small mb-0">Filter and organize baking/decorating queues by date and branch.</p>
    </div>

    <form method="GET" action="{{ route('staff.schedule.index') }}" class="d-flex gap-2">
        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
        <select name="branch" class="form-select form-select-sm">
            <option value="">All Branches</option>
            <option value="main_shop" {{ $branch === 'main_shop' ? 'selected' : '' }}>Main Shop</option>
            <option value="san_jose" {{ $branch === 'san_jose' ? 'selected' : '' }}>San Jose Branch</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary fw-bold">Filter</button>
    </form>
</div>

@if($scheduledSlots->isEmpty())
    <div class="card p-5 text-center text-muted border-0 shadow-sm">
        No orders scheduled for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.
    </div>
@else
    <div class="d-flex flex-column gap-4">
        @foreach($scheduledSlots as $timeSlot => $tickets)
            <div class="card slot-group-card border-0 shadow-sm">
                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold small">Time Slot: {{ $timeSlot }}</span>
                    <span class="badge bg-primary rounded-pill">{{ $tickets->count() }} Ticket(s)</span>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        @foreach($tickets as $ticket)
                            <div class="col-12 col-md-4">
                                <div class="card ticket-item-card border p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-monospace fw-bold text-primary">#{{ $ticket->id }}</span>
                                        <span class="badge bg-info text-dark">{{ $ticket->status }}</span>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $ticket->customer_name }}</h6>
                                    <p class="text-muted small mb-2">Branch: {{ $ticket->branch_name }}</p>
                                    <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="small fw-bold text-decoration-none">View Ticket Details &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection