@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/tickets.css') }}">
    <style>
        .recommended-card {
            background: #ffffff;
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
        }
        .recommended-badge {
            background: #dbeafe;
            color: #1e40af;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 10px;
            border-radius: 9999px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .action-button-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 16px;
        }
        @media (min-width: 640px) {
            .action-button-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .btn-step-primary {
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background 0.2s;
        }
        .btn-step-primary:hover {
            background: #1d4ed8;
        }
        .btn-step-warning {
            background: #d97706;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background 0.2s;
        }
        .btn-step-warning:hover {
            background: #b45309;
        }
        .btn-step-success {
            background: #059669;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background 0.2s;
        }
        .btn-step-success:hover {
            background: #047857;
        }
        .btn-step-dark {
            background: #1f2937;
            color: #ffffff;
            font-weight: 700;
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background 0.2s;
        }
        .btn-step-dark:hover {
            background: #111827;
        }
    </style>
@endpush

@section('content')
<div class="staff-tickets-page">
    <a href="{{ route('staff.dashboard') }}" class="tickets-back-link">&larr; Back to order tickets</a>

    <header class="tickets-page-heading ticket-detail-heading">
        <div>
            <p class="tickets-eyebrow">Ticket review</p>
            <h1 class="tickets-page-title">Order #{{ $ticket->id }}</h1>
            <p class="tickets-page-description">Review order details and process the recommended next step.</p>
        </div>
        <span class="ticket-status-badge">{{ $ticket->status }}</span>
    </header>

    @if(session('success'))
        <div class="alert alert-success" role="alert" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info" role="alert" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
            {{ session('info') }}
        </div>
    @endif

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

    {{-- RECOMMENDED NEXT STEP ACTION PANEL --}}
    @php
        $statusLower = strtolower($ticket->status);
    @endphp

    <section class="recommended-card">
        <span class="recommended-badge">⚡ Recommended Next Action Step</span>
        
        <form method="POST" action="{{ route('staff.tickets.quote', $ticket->id) }}" class="ticket-action-form">
            @csrf
            @method('PUT')

            {{-- STEP A: PENDING STAFF REVIEW -> MARK UNDER REVIEW --}}
            @if($statusLower === 'pending staff review' || $statusLower === 'pending')
                <p style="margin-bottom: 12px; font-size: 0.9rem; color: #374151;">
                    <strong>Step 1:</strong> Put this ticket in <em>Under Review</em> mode to lock customer edits while auditing item custom requests.
                </p>
                <div class="action-button-grid">
                    <button type="submit" name="action" value="mark_review" class="btn-step-warning">
                        🔒 Lock & Mark Under Review
                    </button>
                    <button type="submit" name="action" value="reject" class="tickets-danger-button" formnovalidate>
                        ✕ Reject Ticket
                    </button>
                </div>

            {{-- STEP B: UNDER REVIEW -> APPROVE & QUOTE PRICE --}}
            @elseif($statusLower === 'under review' || $statusLower === 'in review')
                <p style="margin-bottom: 12px; font-size: 0.9rem; color: #374151;">
                    <strong>Step 2:</strong> Set final quoted price quote to approve payment. (Customer editing is currently locked).
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 14px;">
                    <label class="tickets-filter-field">
                        <span>Quoted Price (₱) *</span>
                        <input type="number" name="quoted_price" min="0" step="0.01" value="{{ old('quoted_price', $ticket->total_price) }}" class="form-control" required>
                    </label>
                    <label class="tickets-filter-field">
                        <span>Deposit Required (₱)</span>
                        <input type="number" name="deposit_amount" min="0" step="0.01" value="{{ old('deposit_amount', $ticket->deposit_required) }}" class="form-control">
                    </label>
                </div>
                <div class="action-button-grid">
                    <button type="submit" name="action" value="approve_quote" class="btn-step-primary">
                        ✓ Approve Quote & Request Payment
                    </button>
                    <button type="submit" name="action" value="reject" class="tickets-danger-button" formnovalidate>
                        ✕ Reject Ticket
                    </button>
                </div>

            {{-- STEP C: APPROVED FOR PAYMENT -> WAITING OR MANUAL OVERRIDE --}}
            @elseif($statusLower === 'approved for payment' || $statusLower === 'approved')
                <p style="margin-bottom: 12px; font-size: 0.9rem; color: #374151;">
                    <strong>Step 3:</strong> Waiting for customer payment completion (Auto-advances upon payment). Manual staff override:
                </p>
                <div class="action-button-grid">
                    <button type="submit" name="action" value="start_production" class="btn-step-primary">
                        👨‍🍳 Force Advance to Kitchen Production
                    </button>
                </div>

            {{-- STEP D: IN PRODUCTION -> MARK READY --}}
            @elseif($statusLower === 'in production' || $statusLower === 'preparing')
                <p style="margin-bottom: 12px; font-size: 0.9rem; color: #374151;">
                    <strong>Step 4:</strong> Kitchen has finished preparation. Mark order as ready for pickup.
                </p>
                <div class="action-button-grid">
                    <button type="submit" name="action" value="mark_ready" class="btn-step-success">
                        🛍️ Mark Ready for Pickup (Notify Customer)
                    </button>
                </div>

            {{-- STEP E: READY FOR PICKUP -> COMPLETE --}}
            @elseif($statusLower === 'ready for pickup' || $statusLower === 'ready')
                <p style="margin-bottom: 12px; font-size: 0.9rem; color: #374151;">
                    <strong>Step 5:</strong> Customer is at pickup branch. Complete handoff and close ticket.
                </p>
                <div class="action-button-grid">
                    <button type="submit" name="action" value="complete_order" class="btn-step-dark">
                        ✅ Mark as Completed & Handoff
                    </button>
                </div>

            @else
                <p style="margin: 0; font-size: 0.9rem; color: #6b7280;">This order ticket has reached terminal state: <strong>{{ $ticket->status }}</strong>.</p>
            @endif

            <div style="margin-top: 14px;">
                <label class="tickets-filter-field">
                    <span>Staff Internal Notes</span>
                    <textarea name="staff_notes" rows="2" maxlength="1000" class="form-control" placeholder="Add optional staff internal logs...">{{ old('staff_notes', $ticket->staff_notes) }}</textarea>
                </label>
            </div>
        </form>
    </section>

    {{-- ORDER DETAIL GRID --}}
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
    </div>
</div>
@endsection