@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/tickets.css') }}">
@endpush

@section('content')
<div class="staff-tickets-page">
    <header class="tickets-page-heading">
        <div>
            <p class="tickets-eyebrow">Staff workspace</p>
            <h1 class="tickets-page-title">Order Ticket Review</h1>
            <p class="tickets-page-description">Review customer orders and follow their progress across branches.</p>
        </div>
        <a href="{{ route('staff.walkin.create') }}" class="tickets-primary-link">Create walk-in order</a>
    </header>

    <section class="tickets-filter-card" aria-labelledby="ticket-filters-heading">
        <div class="tickets-section-heading">
            <h2 id="ticket-filters-heading">Filter tickets</h2>
            <span>{{ $tickets->total() }} total</span>
        </div>

        <form method="GET" action="{{ route('staff.dashboard') }}" class="tickets-filter-form">
            <label class="tickets-filter-field">
                <span>Status</span>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach($statuses as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>

            <label class="tickets-filter-field">
                <span>Branch</span>
                <select name="branch" class="form-select">
                    <option value="">All branches</option>
                    <option value="main_shop" @selected($branch === 'main_shop')>Main Shop</option>
                    <option value="san_jose" @selected($branch === 'san_jose')>San Jose Branch</option>
                </select>
            </label>

            <div class="tickets-filter-actions">
                <button type="submit" class="tickets-primary-button">Apply filters</button>
                @if($status || $branch)
                    <a href="{{ route('staff.dashboard') }}" class="tickets-reset-link">Clear filters</a>
                @endif
            </div>
        </form>
    </section>

    <section class="tickets-table-card" aria-labelledby="ticket-list-heading">
        <div class="tickets-section-heading tickets-table-heading">
            <h2 id="ticket-list-heading">Order tickets</h2>
            @if($status || $branch)
                <span class="tickets-filter-summary">
                    @if($status){{ $status }}@endif
                    @if($status && $branch) · @endif
                    @if($branch){{ $branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}@endif
                </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table tickets-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Ticket</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Status</th>
                        <th scope="col">Total</th>
                        <th scope="col">Created</th>
                        <th scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="ticket-number">#{{ $ticket->id }}</td>
                            <td>
                                <span class="ticket-customer-name">{{ $ticket->customer_name }}</span>
                                @if($ticket->items->isNotEmpty())
                                    <span class="ticket-item-count">{{ $ticket->items->sum('quantity') }} item(s)</span>
                                @endif
                            </td>
                            <td>{{ $ticket->branch_name }}</td>
                            <td><span class="ticket-status-badge">{{ $ticket->status }}</span></td>
                            <td class="ticket-total">₱{{ number_format((float) $ticket->total_price, 2) }}</td>
                            <td class="ticket-created">{{ $ticket->created_at?->format('M d, Y h:i A') }}</td>
                            <td class="ticket-action-cell">
                                <a href="{{ route('staff.tickets.show', $ticket->id) }}" class="tickets-view-link">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="tickets-empty-state">
                                <strong>No order tickets found</strong>
                                <span>Try changing or clearing the selected filters.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <footer class="tickets-pagination">
                <span>Showing {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} of {{ $tickets->total() }}</span>
                {{ $tickets->links() }}
            </footer>
        @endif
    </section>
</div>
@endsection
