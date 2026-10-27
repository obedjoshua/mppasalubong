@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/tickets.css') }}">
@endpush

@section('content')
<div class="staff-tickets-page">
    <a href="{{ route('staff.dashboard') }}" class="tickets-back-link">&larr; Back to order tickets</a>

    <header class="tickets-page-heading ticket-detail-heading">
        <div>
            <p class="tickets-eyebrow">Ticket review</p>
            <h1 class="tickets-page-title">Order #{{ $ticket->id }}</h1>
            <p class="tickets-page-description">Review order details and update its status or quote.</p>
        </div>
        <span class="ticket-status-badge">{{ $ticket->status }}</span>
    </header>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ticket-detail-grid">
        <section class="tickets-table-card ticket-detail-card">
            <div class="tickets-section-heading">
                <h2>Order details</h2>
            </div>
            <dl class="ticket-details-list">
                <div><dt>Customer</dt><dd>{{ $ticket->customer_name }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $ticket->customer_phone ?: 'Not provided' }}</dd></div>
                <div><dt>Pickup branch</dt><dd>{{ $ticket->branch_name }}</dd></div>
                <div><dt>Pickup date</dt><dd>{{ $ticket->pickup_date?->format('M d, Y') ?? 'Not set' }}</dd></div>
                <div><dt>Pickup time</dt><dd>{{ $ticket->pickup_time ?: 'Not set' }}</dd></div>
                <div><dt>Current total</dt><dd>₱{{ number_format((float) $ticket->total_price, 2) }}</dd></div>
                <div><dt>Deposit required</dt><dd>₱{{ number_format((float) $ticket->deposit_required, 2) }}</dd></div>
                <div><dt>Address</dt><dd>{{ $ticket->location_address ?: 'Not provided' }}</dd></div>
            </dl>

            @if($ticket->additional_notes)
                <div class="ticket-notes-block">
                    <h3>Customer notes</h3>
                    <p>{{ $ticket->additional_notes }}</p>
                </div>
            @endif

            @if($ticket->staff_notes)
                <div class="ticket-notes-block">
                    <h3>Staff notes</h3>
                    <p>{{ $ticket->staff_notes }}</p>
                </div>
            @endif
        </section>

        <section class="tickets-table-card ticket-detail-card">
            <div class="tickets-section-heading">
                <h2>Items</h2>
                <span>{{ $ticket->items->count() }} line(s)</span>
            </div>
            @forelse($ticket->items as $item)
                <article class="ticket-item-detail">
                    <div class="ticket-item-heading">
                        <strong>{{ $item->item_type }}</strong>
                        <span>Qty {{ $item->quantity }}</span>
                    </div>
                    @if($item->custom_options)
                        <dl class="ticket-options-list">
                            @foreach($item->custom_options as $key => $value)
                                <div>
                                    <dt>{{ \Illuminate\Support\Str::headline($key) }}</dt>
                                    <dd>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </article>
            @empty
                <p class="ticket-details-empty">No item details were saved for this ticket.</p>
            @endforelse
        </section>

        <section class="tickets-table-card ticket-detail-card">
            <div class="tickets-section-heading">
                <h2>Update status</h2>
            </div>
            <form method="POST" action="{{ route('staff.tickets.status', $ticket->id) }}" class="ticket-action-form">
                @csrf
                @method('PUT')
                <label class="tickets-filter-field" for="ticket-status">
                    <span>Status</span>
                    <select id="ticket-status" name="status" class="form-select" required>
                        @foreach(['Pending Staff Review', 'Approved for Payment', 'In Production', 'Ready for Pickup', 'Completed', 'Cancelled'] as $status)
                            <option value="{{ $status }}" @selected($ticket->status === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="tickets-primary-button">Save status</button>
            </form>
        </section>

        @if($ticket->status === 'Pending Staff Review')
            <section class="tickets-table-card ticket-detail-card">
                <div class="tickets-section-heading">
                    <h2>Quote this order</h2>
                </div>
                <form method="POST" action="{{ route('staff.tickets.quote', $ticket->id) }}" class="ticket-action-form">
                    @csrf
                    @method('PUT')
                    <label class="tickets-filter-field" for="quoted-price">
                        <span>Quoted price</span>
                        <input id="quoted-price" type="number" name="quoted_price" min="0" step="0.01" value="{{ old('quoted_price', $ticket->total_price) }}" class="form-control">
                    </label>
                    <label class="tickets-filter-field" for="deposit-amount">
                        <span>Deposit required</span>
                        <input id="deposit-amount" type="number" name="deposit_amount" min="0" step="0.01" value="{{ old('deposit_amount', $ticket->deposit_required) }}" class="form-control">
                    </label>
                    <label class="tickets-filter-field" for="staff-notes">
                        <span>Staff notes</span>
                        <textarea id="staff-notes" name="staff_notes" rows="3" maxlength="1000" class="form-control">{{ old('staff_notes', $ticket->staff_notes) }}</textarea>
                    </label>
                    <div class="ticket-review-actions">
                        <button type="submit" name="action" value="approve" class="tickets-primary-button">Approve with quote</button>
                        <button type="submit" name="action" value="reject" class="tickets-danger-button" formnovalidate>Reject ticket</button>
                    </div>
                </form>
            </section>
        @endif
    </div>
</div>
@endsection
