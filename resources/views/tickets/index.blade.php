<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Tickets | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('css/welcome.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/dashboard.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">

    <!-- Register Service Worker for offline capability -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>

    <style>
        .ticket-section-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: 1.5rem;
            color: #310669;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ticket-count-pill {
            font-family: 'Manrope', sans-serif;
            font-size: 0.8rem;
            font-weight: 800;
            background: #F3E8FF;
            color: #7E22CE;
            padding: 2px 10px;
            border-radius: 12px;
        }

        .tickets-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .mini-ticket-card {
            background: #FFFFFF;
            border: 2px solid #E5E7EB;
            border-radius: 14px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            box-sizing: border-box;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .mini-ticket-card:hover {
            transform: translateY(-2px);
            border-color: #D1D5DB;
        }

        .mini-ticket-card.rejected {
            border-color: #FCA5A5;
            background: #FEF2F2;
        }

        .mini-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            overflow: hidden;
        }

        .mini-ticket-id {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            color: #8A38F5;
            display: block;
        }

        .mini-ticket-card.rejected .mini-ticket-id {
            color: #DC2626;
        }

        .mini-ticket-title {
            margin: 2px 0 0 0;
            font-size: 0.95rem;
            color: #1F2937;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-status-badge {
            text-transform: uppercase;
            font-size: 0.65rem;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .mini-status-badge.pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .mini-status-badge.rejected {
            background: #FEE2E2;
            color: #991B1B;
        }

        .mini-card-details {
            border-top: 1px dashed #E5E7EB;
            padding-top: 10px;
            font-size: 0.8rem;
            color: #4B5563;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow: hidden;
        }

        .mini-ticket-card.rejected .mini-card-details {
            border-top-color: #FCA5A5;
        }

        .detail-line {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-card-actions {
            display: flex;
            gap: 8px;
            margin-top: 4px;
        }

        .btn-action-view {
            flex: 1;
            text-align: center;
            background: #8A38F5;
            color: #FFFFFF;
            padding: 8px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.775rem;
            text-decoration: none;
            transition: background 0.15s ease;
            box-sizing: border-box;
        }

        .btn-action-view:hover {
            background: #7E22CE;
        }

        .btn-action-cancel {
            flex: 1;
            background: #FFFFFF;
            color: #DC2626;
            border: 1px solid #FCA5A5;
            padding: 8px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.775rem;
            cursor: pointer;
            transition: background 0.15s ease;
            box-sizing: border-box;
        }

        .btn-action-cancel:hover {
            background: #FEF2F2;
        }

        /* Inline Banner Notification with Undo Button */
        .inline-alert-banner {
            background: #FDF2F2;
            border: 1.5px solid #FCA5A5;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .btn-undo-action {
            background: #DC2626;
            color: #FFFFFF;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.775rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-undo-action:hover {
            background: #991B1B;
        }

        /* CUSTOM MODAL OVERLAY & BOX */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 28px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transform: scale(0.92);
            transition: transform 0.2s ease;
            text-align: center;
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: 1.35rem;
            color: #1F2937;
            margin: 0 0 8px 0;
        }

        .modal-description {
            font-family: 'Manrope', sans-serif;
            font-size: 0.875rem;
            color: #6B7280;
            margin: 0 0 24px 0;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-modal-cancel {
            flex: 1;
            background: #F3F4F6;
            color: #374151;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.825rem;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-modal-cancel:hover {
            background: #E5E7EB;
        }

        .btn-modal-confirm {
            flex: 1;
            background: #DC2626;
            color: #FFFFFF;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.825rem;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-modal-confirm:hover {
            background: #B91C1C;
        }
    </style>
</head>
<body>

    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        <!-- Header Area -->
        <div class="dashboard-header-area">
            <h1 class="user-display-name" style="font-size: clamp(40px, 5vw, 60px);">Inquiry Tickets</h1>
        </div>

        @php
            $allTickets = Auth::user()->tickets()->with('items')->latest()->get();

            $pendingTickets = $allTickets->filter(function($t) {
                return in_array(strtolower($t->status), ['pending staff review', 'pending', 'under review']);
            });

            $rejectedTickets = $allTickets->filter(function($t) {
                return in_array(strtolower($t->status), ['rejected', 'declined', 'cancelled']);
            });
        @endphp

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="dash-card">

                {{-- GENERAL SUCCESS MESSAGE --}}
                @if(session('success'))
                    <div style="background: #DEF7EC; border: 1.5px solid #31C48D; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; color: #03543F; font-weight: 700; font-size: 0.875rem;">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- INLINE TICKET CANCELLED BANNER WITH UNDO BUTTON --}}
                @if(session('ticket_cancelled'))
                    <div class="inline-alert-banner">
                        <span style="font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 0.875rem; color: #991B1B;">
                            {{ session('ticket_cancelled') }}
                        </span>

                        @if(session('ticket_cancelled_id'))
                            <form action="{{ route('tickets.restore', session('ticket_cancelled_id')) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-undo-action">
                                    Undo
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

                {{-- 1. PENDING TICKETS SECTION --}}
                <div style="margin-bottom: 32px;">
                    <h3 class="ticket-section-title">
                        Pending Tickets
                        <span class="ticket-count-pill">{{ $pendingTickets->count() }}</span>
                    </h3>

                    @if($pendingTickets->isEmpty())
                        <p class="empty-state-text" style="margin-top: 0;">You have no pending inquiry tickets under review.</p>
                    @else
                        <div class="tickets-mini-grid">
                            @foreach($pendingTickets as $ticket)
                                <div class="mini-ticket-card">
                                    <div>
                                        <div class="mini-card-header">
                                            <div style="overflow: hidden;">
                                                <span class="mini-ticket-id">Ticket #{{ $ticket->id }}</span>
                                                <h4 class="mini-ticket-title">
                                                    {{ $ticket->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                                </h4>
                                            </div>
                                            <span class="mini-status-badge pending">
                                                {{ $ticket->status }}
                                            </span>
                                        </div>

                                        <div class="mini-card-details">
                                            <div class="detail-line"><strong>Pickup:</strong> {{ \Carbon\Carbon::parse($ticket->pickup_date)->format('M j, Y') }} at {{ $ticket->pickup_time }}</div>
                                            <div class="detail-line"><strong>Items:</strong> {{ $ticket->items->sum('quantity') }} total item(s)</div>
                                        </div>
                                    </div>

                                    <div class="mini-card-actions">
                                        <a href="{{ route('orders.ticket', $ticket->id) }}" class="btn-action-view">
                                            View Ticket
                                        </a>

                                        {{-- Cancel Form using Custom Modal Trigger --}}
                                        <form id="cancel-form-{{ $ticket->id }}" action="{{ route('orders.destroy', $ticket->id) }}" method="POST" style="flex: 1;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action-cancel" onclick="openConfirmModal('{{ $ticket->id }}')">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- 2. REJECTED / CANCELLED TICKETS SECTION --}}
                <div>
                    <h3 class="ticket-section-title" style="color: #991B1B;">
                        Rejected & Cancelled Tickets
                        <span class="ticket-count-pill" style="background: #FEE2E2; color: #991B1B;">{{ $rejectedTickets->count() }}</span>
                    </h3>

                    @if($rejectedTickets->isEmpty())
                        <p class="empty-state-text" style="margin-top: 0;">No rejected or cancelled inquiry tickets.</p>
                    @else
                        <div class="tickets-mini-grid">
                            @foreach($rejectedTickets as $ticket)
                                <div class="mini-ticket-card rejected">
                                    <div>
                                        <div class="mini-card-header">
                                            <div style="overflow: hidden;">
                                                <span class="mini-ticket-id">Ticket #{{ $ticket->id }}</span>
                                                <h4 class="mini-ticket-title">
                                                    {{ $ticket->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                                </h4>
                                            </div>
                                            <span class="mini-status-badge rejected">
                                                {{ $ticket->status }}
                                            </span>
                                        </div>

                                        <div class="mini-card-details">
                                            <div class="detail-line"><strong>Pickup Date:</strong> {{ \Carbon\Carbon::parse($ticket->pickup_date)->format('M j, Y') }}</div>
                                            @if(!empty($ticket->rejection_reason))
                                                <div class="detail-line" style="color: #B91C1C;"><strong>Reason:</strong> {{ $ticket->rejection_reason }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mini-card-actions">
                                        <a href="{{ route('orders.ticket', $ticket->id) }}" class="btn-action-view" style="background: #DC2626;">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div> 

    {{-- CUSTOM CONFIRMATION MODAL --}}
    <div class="modal-overlay" id="confirmModal">
        <div class="modal-card">
            <h3 class="modal-title">Cancel Ticket Inquiry?</h3>
            <p class="modal-description">Are you sure you want to cancel this ticket? You will be able to restore it from the alert banner if needed.</p>
            <div class="modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeConfirmModal()">Keep Ticket</button>
                <button type="button" class="btn-modal-confirm" id="confirmSubmitBtn">Yes, Cancel Ticket</button>
            </div>
        </div>
    </div>

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        let targetFormId = null;

        function openConfirmModal(ticketId) {
            targetFormId = 'cancel-form-' + ticketId;
            document.getElementById('confirmModal').classList.add('active');
        }

        function closeConfirmModal() {
            targetFormId = null;
            document.getElementById('confirmModal').classList.remove('active');
        }

        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            if (targetFormId) {
                document.getElementById(targetFormId).submit();
            }
        });

        // Close modal when clicking on the dimmed backdrop
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirmModal();
            }
        });

        window.addEventListener('pageshow', function (event) {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;

            const loader = document.getElementById("globalLoader");
            const startTime = Date.now();
            function hideLoader() {
                if (loader) {
                    loader.classList.add("fade-out");
                    setTimeout(() => loader.remove(), 600);
                }
            }
            window.addEventListener("load", function () {
                const elapsedTime = Date.now() - startTime;
                if (elapsedTime < 1000) hideLoader();
                else setTimeout(hideLoader, Math.max(0, 3000 - elapsedTime));
            });
            setTimeout(hideLoader, 4000);
        });
    </script>
</body>
</html>