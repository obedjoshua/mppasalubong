@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tickets-show.css') }}?v={{ time() }}">
    <style>
        .lottie-review-banner {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #fff8f0;
            border: 1px solid #fed7aa;
            border-left: 5px solid #f97316;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .lottie-box {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
        }
        .review-info-content {
            flex: 1;
        }
        .review-info-content h4 {
            margin: 0 0 4px 0;
            color: #9a3412;
            font-size: 1.05rem;
            font-family: 'Recoleta-Bold', serif;
        }
        .review-info-content p {
            margin: 0 0 10px 0;
            font-size: 0.875rem;
            color: #431407;
            line-height: 1.4;
        }
        .btn-cancel-request {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.825rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-cancel-request:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }
        .admin-test-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .admin-test-badge {
            font-size: 0.75rem;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .admin-test-badge.testing {
            background: #fef3c7;
            color: #92400e;
        }
        .admin-test-badge.live {
            background: #e2e8f0;
            color: #475569;
        }
        .btn-admin-toggle {
            background: #334155;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-admin-toggle:hover {
            background: #1e293b;
        }
        .payment-redirect-banner {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .payment-partial-banner {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 5px solid #f59e0b;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .payment-paid-banner {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            border-left: 5px solid #10b981;
            padding: 14px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .paid-full-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #059669;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 999px;
            letter-spacing: 0.03em;
        }
        .payment-action-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-pay-now {
            background: #059669;
            color: #ffffff;
            font-weight: 800;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.2s ease;
        }
        .btn-pay-now:hover {
            background: #047857;
        }
        .btn-pay-remaining {
            background: #d97706;
            color: #ffffff;
            font-weight: 800;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.2s ease;
        }
        .btn-pay-remaining:hover {
            background: #b45309;
        }
        .btn-pay-full {
            background: #ffffff;
            color: #065f46;
            border: 1.5px solid #059669;
            font-weight: 700;
            padding: 9px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.2s ease;
        }
        .btn-pay-full:hover {
            background: #d1fae5;
        }
        .messenger-share-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 14px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .btn-messenger-share {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .btn-messenger-share:hover {
            background: #1d4ed8;
        }

        /* Download QR Pass Button */
        .btn-download-qr {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 10px;
            padding: 7px 14px;
            background: #8a38f5;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(138, 56, 245, 0.25);
            transition: all 0.2s ease;
            width: 100%;
            max-width: 165px;
        }

        .btn-download-qr:hover {
            background: #7225d8;
            transform: translateY(-1px);
        }

        .btn-download-qr:active {
            transform: translateY(0);
        }

        /* Full-Viewport Locked Reference Modal */
        html.modal-open,
        body.modal-open {
            overflow: hidden !important;
            height: 100% !important;
            overscroll-behavior: none !important;
        }

        body.modal-open .ticket-page-wrapper {
            pointer-events: none !important;
            user-select: none !important;
        }

        #itemSliderOverlay.dark-overlay-modal {
            display: none;
            position: fixed !important;
            inset: 0 !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100dvh !important;
            min-height: 100vh !important;
            background: rgba(15, 23, 42, 0.8) !important;
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 99999 !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 24px !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            overscroll-behavior: contain !important;
            pointer-events: auto !important;
        }

        #itemSliderOverlay.dark-overlay-modal.active {
            display: flex !important;
        }

        #itemSliderOverlay .overlay-card-wrapper {
            position: relative !important;
            max-width: 920px !important;
            width: 100% !important;
            max-height: calc(100dvh - 48px) !important;
            overflow-y: auto !important;
            overscroll-behavior: contain !important;
            margin: 0 auto !important;
            z-index: 100000 !important;
        }

        @media (max-width: 640px) {
            #itemSliderOverlay.dark-overlay-modal {
                padding: 12px !important;
            }
            #itemSliderOverlay .overlay-card-wrapper {
                max-height: calc(100dvh - 24px) !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Lottie Web Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <!-- Service Worker Registration -->
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
@endpush

@section('content')
@php
    $normalizedStatus = strtolower(trim(str_replace(' ', '_', $ticket->status ?? '')));
    $productionStatuses = ['in_production', 'preparing', 'ready_for_pickup', 'completed'];
    $showOrderTracker = in_array($normalizedStatus, $productionStatuses, true);

    $quotedTotal = (float) ($ticket->total_price ?? $ticket->total_amount ?? $ticket->quoted_price ?? 0);
    $amountPaid = (float) ($ticket->amount_paid ?? 0);

    // Backwards compatibility for legacy orders already marked 'paid' without amount_paid set
    if ($amountPaid <= 0 && strtolower((string) $ticket->payment_status) === 'paid') {
        $amountPaid = $quotedTotal;
    }

    $downpaymentAmount = round($quotedTotal * 0.30, 2);
    $remainingBalance = max(0, round($quotedTotal - $amountPaid, 2));
    $isFullyPaid = ($quotedTotal > 0 && $remainingBalance <= 0) || strtolower((string) $ticket->payment_status) === 'paid';
    $hasPartialPayment = ($quotedTotal > 0 && $amountPaid > 0 && $remainingBalance > 0);
    $isApprovedForPayment = in_array(strtolower(trim(str_replace('_', ' ', (string) $ticket->status))), ['approved', 'approved for payment'], true);
@endphp

<div class="ticket-page-wrapper">

    <div class="ticket-nav-bar">
        <a href="{{ route('orders.index') }}" class="btn-back-link">
            ← Back to Orders
        </a>
        <span style="font-size: 0.85rem; color: #6b21a8; font-weight: bold;">Order Ticket View</span>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; font-weight: bold;">
            {{ session('error') }}
        </div>
    @endif

    {{-- STEP 6: ADMIN TESTING CONTROL TOGGLE --}}
    @if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
        <div class="admin-test-bar">
            <div>
                <strong style="font-size: 0.85rem; color: #334155;">Testing Mode:</strong>
                <span class="admin-test-badge {{ $ticket->is_testing ? 'testing' : 'live' }}">
                    {{ $ticket->is_testing ? 'DUMMY GATEWAY ACTIVE' : 'LIVE XENDIT ACTIVE' }}
                </span>
            </div>
            <form action="{{ route('admin.tickets.toggle-testing', $ticket->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-admin-toggle">
                    Toggle Testing Mode
                </button>
            </form>
        </div>
    @endif

    {{-- STEP 5: ORDER STATUS TRACKER (ONLY VISIBLE ONCE ORDER ENTERS PRODUCTION) --}}
    @if($showOrderTracker)
        @include('partials.order-status-tracker', ['ticket' => $ticket])
    @endif

    {{-- PAYMENT STATUS & ACTION BANNERS --}}
    @if($isFullyPaid && $quotedTotal > 0)
        {{-- 1. PAID IN FULL BANNER --}}
        <div class="payment-paid-banner">
            <div>
                <h4 style="margin: 0 0 2px 0; color: #065f46; font-size: 1rem; font-weight: bold;">Payment Completed</h4>
                <p style="margin: 0; font-size: 0.82rem; color: #047857;">
                    Total Paid: <strong>₱{{ number_format($quotedTotal, 2) }}</strong> &bull; No remaining balance due.
                </p>
            </div>
            <span class="paid-full-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                PAID IN FULL
            </span>
        </div>
    @elseif($hasPartialPayment)
        {{-- 2. DOWNPAYMENT PAID — REMAINING BALANCE BANNER --}}
        <div class="payment-partial-banner">
            <div>
                <h4 style="margin: 0 0 2px 0; color: #92400e; font-size: 1rem; font-weight: bold;">Downpayment Paid — Balance Remaining</h4>
                <p style="margin: 0; font-size: 0.82rem; color: #78350f;">
                    Full Quote: <strong>₱{{ number_format($quotedTotal, 2) }}</strong> &bull;
                    Paid: <strong>₱{{ number_format($amountPaid, 2) }}</strong> &bull;
                    Remaining Balance: <strong>₱{{ number_format($remainingBalance, 2) }}</strong>
                </p>
            </div>
            <div class="payment-action-group">
                <a href="{{ route('orders.pay', ['orderTicket' => $ticket->id, 'payment_type' => 'remaining']) }}" class="btn-pay-remaining">
                    Pay Remaining Balance (₱{{ number_format($remainingBalance, 2) }})
                </a>
            </div>
        </div>
    @elseif($isApprovedForPayment)
        {{-- 3. INITIAL APPROVAL — 30% DOWNPAYMENT OR FULL PAYMENT --}}
        <div class="payment-redirect-banner">
            <div>
                <h4 style="margin: 0 0 2px 0; color: #065f46; font-size: 1rem; font-weight: bold;">Order Approved for Payment!</h4>
                <p style="margin: 0; font-size: 0.8rem; color: #047857;">
                    Full Quoted Price: <strong>₱{{ number_format($quotedTotal, 2) }}</strong> &bull;
                    Required 30% Downpayment: <strong>₱{{ number_format($downpaymentAmount, 2) }}</strong>
                </p>
            </div>
            <div class="payment-action-group">
                <a href="{{ route('orders.pay', ['orderTicket' => $ticket->id, 'payment_type' => 'downpayment']) }}" class="btn-pay-now">
                    Pay 30% Downpayment (₱{{ number_format($downpaymentAmount, 2) }})
                </a>
                <a href="{{ route('orders.pay', ['orderTicket' => $ticket->id, 'payment_type' => 'full']) }}" class="btn-pay-full">
                    Pay Full (₱{{ number_format($quotedTotal, 2) }})
                </a>
            </div>
        </div>
    @endif

    {{-- UNDER REVIEW BANNER WITH LOTTIE & CANCEL REQUEST BUTTON --}}
    @if(in_array(strtolower($ticket->status), ['under review', 'in review', 'review mode']))
        <div class="lottie-review-banner">
            <div class="lottie-box">
                <lottie-player 
                    src="https://assets10.lottiefiles.com/packages/lf20_mbe45w62.json" 
                    background="transparent" 
                    speed="1" 
                    style="width: 100%; height: 100%;" 
                    loop 
                    autoplay>
                </lottie-player>
            </div>
            <div class="review-info-content">
                <h4>Ticket Under Staff Review</h4>
                <p>Our bakery staff is currently auditing your custom order details and calculating your official price quote. Editing is locked while in review.</p>
                <a href="{{ route('orders.cancel-request.form', $ticket->id) }}" class="btn-cancel-request">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    Request Ticket Cancellation
                </a>
            </div>
        </div>
    @else
        <div class="notice-banner">
            <div style="font-size: 1.3rem; line-height: 1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </div>
            <div class="notice-content">
                <strong>Inquiry Received!</strong>
                @if($ticket->preferred_notification_channel === 'messenger')
                    <p>We've logged your custom order ticket. Updates will be sent directly to your <strong>Messenger</strong> inbox.</p>
                @else
                    <p>We've logged your custom order ticket. Notifications regarding price approval will be sent via <strong>Email ({{ $ticket->customer_email ?? 'your email' }})</strong> and <strong>SMS</strong>.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- DASHBOARD REDIRECT PROMPT --}}
    @if(session('just_submitted'))
        <div class="dashboard-redirect-prompt">
            <div class="prompt-text">
                <strong>Need to make edits or changes to your inquiry?</strong>
                <p>You can review, edit, or delete pending order tickets at any time directly from your user dashboard directory.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-dashboard-redirect">
                Go to Dashboard →
            </a>
        </div>
    @endif

    {{-- PRIMARY TICKET DISPLAY IMAGE --}}
    @php
        $pinnedPreviewImage = null;

        foreach ($ticket->items as $item) {
            $pinnedPreviewImage = $item->image_url;
            if ($pinnedPreviewImage && $pinnedPreviewImage !== asset('images/mascot-envelope.png')) {
                break;
            }
        }
        
        if (!$pinnedPreviewImage && $ticket->items->count() > 0) {
            $pinnedPreviewImage = $ticket->items->first()->image_url;
        }
    @endphp

    <div class="ticket-outer-container">
        <div class="ticket-card">
            
            <div class="ticket-main-section">

                @if($pinnedPreviewImage)
                    <div class="pinned-ref-container" onclick="openSliderOverlay()">
                        <div class="polaroid-frame">
                            <svg class="paperclip-svg" viewBox="0 0 500 600" width="100%" height="100%">
                                <defs>
                                    <linearGradient id="metal-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#f0f0f0"/>
                                        <stop offset="20%" stop-color="#ffffff"/>
                                        <stop offset="50%" stop-color="#555555"/>
                                        <stop offset="80%" stop-color="#ffffff"/>
                                        <stop offset="100%" stop-color="#777777"/>
                                    </linearGradient>
                                </defs>
                                <g transform="rotate(-15, 250, 300)">
                                    <path d="M 230 220 L 160 440 A 35 35 0 0 0 230 440 L 330 140 A 25 25 0 0 0 290 115 C 275 115 270 125 275 130" 
                                          fill="none" stroke="url(#metal-grad)" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                            </svg>

                            <div class="polaroid-img-box">
                                <img src="{{ $pinnedPreviewImage }}" 
                                     alt="Ref Image" 
                                     class="polaroid-img" 
                                     onerror="this.onerror=null; this.src='{{ asset('images/mascot-envelope.png') }}';" />
                            </div>
                            <div class="polaroid-caption">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Ref Image
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="ticket-header-row">
                    <div class="brand-title-group">
                        <img src="{{ asset('images/logo.png') }}" alt="Bakery Logo" style="max-height: 48px;" onerror="this.style.display='none'" />
                        <div>
                            <strong style="font-family: 'Recoleta-Bold', serif; font-size: 1.15rem; color: #111827; display: block;">Masaganang Pagpapala Pasalubong</strong>
                            <small style="color: #64748b;">Official Order Inquiry Receipt</small>
                        </div>
                    </div>
                    <div class="badge-status-group">
                        <div class="ticket-number-badge">#{{ $ticket->id }}</div>
                        <div><span class="status-badge">{{ $ticket->status }}</span></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: auto 1fr; gap: 6px 14px; font-size: 0.85rem; margin-bottom: 12px;">
                    <strong style="font-family: 'Recoleta-Bold', serif;">Customer:</strong>
                    <span>{{ $ticket->customer_name }}</span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Branch:</strong>
                    <span style="color: #8a38f5; font-weight: bold;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        {{ $ticket->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}
                        @if($ticket->calculated_distance_km)
                            <small style="color: #64748b; font-weight: normal;">({{ $ticket->calculated_distance_km }} km away)</small>
                        @endif
                    </span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Date:</strong>
                    <span>{{ \Carbon\Carbon::parse($ticket->pickup_date)->format('F j, Y') }}</span>

                    <strong style="font-family: 'Recoleta-Bold', serif;">Pickup Time:</strong>
                    <span>{{ $ticket->pickup_time }}</span>

                    @if($ticket->location_address && $ticket->location_address !== 'TBD')
                        <strong style="font-family: 'Recoleta-Bold', serif;">Location:</strong>
                        <span>{{ $ticket->location_address }}</span>
                    @endif

                    @if($quotedTotal > 0)
                        <strong style="font-family: 'Recoleta-Bold', serif;">Payment Status:</strong>
                        <span>
                            @if($isFullyPaid)
                                <strong style="color: #059669;">Paid in Full (₱{{ number_format($quotedTotal, 2) }})</strong>
                            @elseif($hasPartialPayment)
                                <strong style="color: #d97706;">Partially Paid (₱{{ number_format($amountPaid, 2) }} / ₱{{ number_format($quotedTotal, 2) }})</strong>
                            @else
                                <span style="color: #64748b;">Unpaid (Quoted: ₱{{ number_format($quotedTotal, 2) }})</span>
                            @endif
                        </span>
                    @endif
                </div>

                <table>
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left;">
                            <th style="width: 10%">#</th>
                            <th>Item Description</th>
                            <th class="numeric" style="width: 20%">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ticket->items as $index => $item)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</strong>
                                </td>
                                <td class="numeric">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- EDIT & DELETE BUTTONS: ONLY ACTIVE WHEN NOT IN REVIEW MODE --}}
                @if(in_array(strtolower($ticket->status), ['pending staff review', 'pending']))
                    <div style="display: flex; gap: 10px; margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                        <a href="{{ route('orders.edit', $ticket->id) }}" style="flex: 1; text-align: center; background: #f3e8ff; color: #7e22ce; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.825rem;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Edit Inquiry
                        </a>
                        <form action="{{ route('orders.destroy', $ticket->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to cancel and delete this order inquiry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: 100%; background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; padding: 8px; border-radius: 6px; font-weight: bold; font-size: 0.825rem; cursor: pointer;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Delete Ticket
                            </button>
                        </form>
                    </div>
                @endif

                <div class="accordion-toggle-box" onclick="toggleExpandedBreakdown()">
                    <span class="accordion-toggle-label" id="accordionLabel">View Full Order Breakdown</span>
                    <svg class="chevron-icon" id="chevronIcon" fill="none" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>

                <div id="expandedBreakdownPanel" class="expanded-breakdown-panel">
                    <div class="expanded-section-title">📋 Complete Order Form Details</div>
                    
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Notification Email:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_email ?? 'N/A' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Contact Phone:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Preferred Channel:</span>
                        <span class="expanded-detail-value">{{ ucfirst($ticket->preferred_notification_channel ?? 'Email') }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Assigned Branch:</span>
                        <span class="expanded-detail-value">{{ $ticket->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop' }}</span>
                    </div>
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Customer Location:</span>
                        <span class="expanded-detail-value">{{ $ticket->location_address ?? 'Store Pickup' }}</span>
                    </div>
                    @if($ticket->customer_latitude && $ticket->customer_longitude)
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Pinned Coordinates:</span>
                        <span class="expanded-detail-value">{{ $ticket->customer_latitude }}, {{ $ticket->customer_longitude }}</span>
                    </div>
                    @endif
                    @if($ticket->calculated_distance_km)
                    <div class="expanded-detail-row">
                        <span class="expanded-detail-label">Distance to Branch:</span>
                        <span class="expanded-detail-value">{{ $ticket->calculated_distance_km }} km</span>
                    </div>
                    @endif

                    <div class="expanded-section-title" style="margin-top: 10px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 6px;">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                        Customization Breakdown
                    </div>
                    @foreach($ticket->items as $index => $item)
                        <div style="margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px dashed #cbd5e1;">
                            <strong style="font-size: 0.8rem; color: #1e293b;">Item #{{ $index + 1 }}: {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</strong>
                            @if(!empty($item->custom_options))
                                @foreach($item->custom_options as $key => $value)
                                    @if(!in_array($key, ['reference_image', 'custom_tag_path']) && is_string($value))
                                        <div class="expanded-detail-row">
                                            <span class="expanded-detail-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="expanded-detail-value">{{ $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>

            <div class="ticket-stub-divider"></div>

            <div class="ticket-stub-section">
                @php
                    $fallbackQrUrl = $qrData['fallback_url'] ?? '';
                @endphp
                <div class="qr-wrapper-block">
                    <div class="qr-container" id="qrSourceContainer">
                        @if(isset($qrData) && $qrData['mode'] === 'inline_svg')
                            {!! $qrData['content'] !!}
                        @else
                            <img id="ticketQrImage" 
                                 src="{{ $qrData['primary_url'] ?? '#' }}" 
                                 crossorigin="anonymous"
                                 alt="Verification QR" 
                                 onerror="this.onerror=null; this.src='{{ $fallbackQrUrl }}';" />
                        @endif
                    </div>
                    <small style="font-size: 0.725rem; color: #64748b; font-weight: bold;">Scan for staff verification</small>

                    <button type="button" class="btn-download-qr" onclick="downloadTicketQrCard()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Download QR Pass</span>
                    </button>
                </div>

                <div style="margin-top: 20px; font-size: 0.775rem; color: #64748b; line-height: 1.3;">
                    <strong style="font-family: 'Recoleta-Bold', serif; color: #111827; font-size: 0.9rem; display: block;">~ Masaganang Pagpapala ~</strong>
                    <address style="font-style: normal; margin-top: 2px;">Salamat sa iyong pagtangkilik!</address>
                </div>
            </div>

        </div>
    </div>

    {{-- STEP 7: SHARE GUEST VIEW TO MESSENGER BANNER --}}
    <div class="messenger-share-bar">
        <span style="font-size: 0.8rem; color: #475569; font-weight: 600;">Share Guest Ticket to Messenger:</span>
        <a href="https://www.facebook.com/dialog/send?link={{ urlencode(route('tickets.guest-view', $ticket->id)) }}&app_id=YOUR_FB_APP_ID&redirect_uri={{ urlencode(url()->current()) }}" 
           target="_blank" 
           class="btn-messenger-share">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                <path d="M12 0C5.373 0 0 4.974 0 11.111c0 3.497 1.745 6.616 4.472 8.652v4.237l4.086-2.242c1.09.301 2.246.464 3.442.464 6.627 0 12-4.975 12-11.111C24 4.974 18.627 0 12 0zm1.191 14.963l-3.055-3.26-5.963 3.26 6.559-6.96 3.127 3.26 5.89-3.26-6.558 6.96z"/>
            </svg>
            <span>Send to Messenger</span>
        </a>
    </div>

</div>

{{-- TABBED OVERLAY MODAL DRAWER --}}
@if(count($ticket->items) > 0)
<div id="itemSliderOverlay" class="dark-overlay-modal" onclick="closeSliderOverlay(event)">
    <div class="overlay-card-wrapper" onclick="event.stopPropagation()">
        
        <button type="button" class="overlay-close-btn" onclick="closeSliderOverlay(null)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        {{-- TAB NAVIGATION HEADER --}}
        <div class="modal-tabs-header">
            @foreach($ticket->items as $idx => $item)
                <button type="button" 
                        class="tab-btn {{ $idx === 0 ? 'active-tab' : '' }}" 
                        onclick="switchTab({{ $idx }})" 
                        id="tabBtn_{{ $idx }}">
                    Item {{ $idx + 1 }}: {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                </button>
            @endforeach
        </div>

        @foreach($ticket->items as $idx => $item)
            @php
                $itemImage = $item->image_url;
            @endphp
        
            <div class="tab-content-panel {{ $idx === 0 ? 'active-panel' : '' }}" id="tabPanel_{{ $idx }}">
                
                <div class="overlay-left-image">
                    @if(!empty($itemImage))
                        <img src="{{ $itemImage }}"
                             alt="{{ $item->item_type }} Reference"
                             style="max-width: 100%; max-height: 100%; object-fit: contain; margin-bottom: 8px;"
                             onerror="this.onerror=null; this.src='{{ asset('images/mascot-envelope.png') }}';" />
                    @else
                        <div style="color: #94a3b8; font-size: 0.85rem; text-align: center; padding: 20px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                            No Reference Image Attached
                        </div>
                    @endif
                </div>
        
                <div class="overlay-right-details">
                    <span class="slide-badge">Item {{ $idx + 1 }} of {{ count($ticket->items) }}</span>
                    <h3 class="slide-title">
                        {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                    </h3>
        
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; display: flex; flex-direction: column; gap: 6px;">
                        <div class="expanded-detail-row">
                            <span class="expanded-detail-label">Quantity:</span>
                            <span class="expanded-detail-value">{{ $item->quantity }}</span>
                        </div>
        
                        @if(!empty($item->custom_options))
                            @foreach($item->custom_options as $key => $value)
                                @if(!in_array($key, ['reference_image', 'custom_tag_path']) && is_string($value))
                                    <div class="expanded-detail-row">
                                        <span class="expanded-detail-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                        <span class="expanded-detail-value">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
        
                    <div style="margin-top: 16px; padding: 8px 12px; background: #f8fafc; border-radius: 6px; font-size: 0.75rem; color: #64748b;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        Specifications tied to <strong>Ticket #{{ $ticket->id }}</strong>
                    </div>
                </div>
        
            </div>
        @endforeach

    </div>
</div>
@endif

<script>
    let activeScrollY = 0;

    function toggleExpandedBreakdown() {
        const panel = document.getElementById('expandedBreakdownPanel');
        const label = document.getElementById('accordionLabel');
        const icon = document.getElementById('chevronIcon');
        
        if (panel.classList.contains('show')) {
            panel.classList.remove('show');
            icon.classList.remove('rotate');
            label.innerText = 'View Full Order Breakdown';
        } else {
            panel.classList.add('show');
            icon.classList.add('rotate');
            label.innerText = 'Collapse Order Details';
        }
    }

    function blockBackgroundScroll(e) {
        const modalCard = document.querySelector('#itemSliderOverlay .overlay-card-wrapper');
        if (modalCard && modalCard.contains(e.target)) {
            return;
        }
        e.preventDefault();
    }

    function openSliderOverlay() {
        const modal = document.getElementById('itemSliderOverlay');
        const pageWrapper = document.querySelector('.ticket-page-wrapper');
        if (!modal) return;

        activeScrollY = window.scrollY || window.pageYOffset;

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.add('active');

        if (pageWrapper) {
            pageWrapper.setAttribute('inert', '');
            pageWrapper.setAttribute('aria-hidden', 'true');
        }

        document.documentElement.classList.add('modal-open');
        document.body.classList.add('modal-open');
        document.body.style.position = 'fixed';
        document.body.style.top = `-${activeScrollY}px`;
        document.body.style.left = '0';
        document.body.style.right = '0';
        document.body.style.width = '100%';

        document.addEventListener('wheel', blockBackgroundScroll, { passive: false });
        document.addEventListener('touchmove', blockBackgroundScroll, { passive: false });
    }

    function closeSliderOverlay(event) {
        if (!event || event.target.id === 'itemSliderOverlay') {
            const modal = document.getElementById('itemSliderOverlay');
            const pageWrapper = document.querySelector('.ticket-page-wrapper');
            if (!modal) return;

            modal.classList.remove('active');

            if (pageWrapper) {
                pageWrapper.removeAttribute('inert');
                pageWrapper.removeAttribute('aria-hidden');
            }

            document.documentElement.classList.remove('modal-open');
            document.body.classList.remove('modal-open');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';

            document.removeEventListener('wheel', blockBackgroundScroll);
            document.removeEventListener('touchmove', blockBackgroundScroll);

            window.scrollTo(0, activeScrollY);
        }
    }

    function switchTab(index) {
        document.querySelectorAll('.tab-btn').forEach((btn, i) => {
            btn.classList.toggle('active-tab', i === index);
        });

        document.querySelectorAll('.tab-content-panel').forEach((panel, i) => {
            panel.classList.toggle('active-panel', i === index);
        });
    }

    async function downloadTicketQrCard() {
        const btn = document.querySelector('.btn-download-qr span');
        const originalLabel = btn ? btn.innerText : 'Download QR Pass';
        if (btn) btn.innerText = 'Generating...';

        try {
            const ticketId = @json($ticket->id);
            const customerName = @json($ticket->customer_name ?? 'Customer');
            const pickupBranch = @json($ticket->pickup_branch === 'san_jose' ? 'San Jose Branch' : 'Main Shop');
            const pickupDate = @json(\Carbon\Carbon::parse($ticket->pickup_date)->format('F j, Y'));
            const pickupTime = @json($ticket->pickup_time ?? '');

            const canvas = document.createElement('canvas');
            const width = 600;
            const height = 820;
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');

            function roundRect(x, y, w, h, r) {
                ctx.beginPath();
                ctx.moveTo(x + r, y);
                ctx.arcTo(x + w, y, x + w, y + h, r);
                ctx.arcTo(x + w, y + h, x, y + h, r);
                ctx.arcTo(x, y + h, x, y, r);
                ctx.arcTo(x, y, x + w, y, r);
                ctx.closePath();
            }

            // 1. Soft Background
            ctx.fillStyle = '#f8fafc';
            ctx.fillRect(0, 0, width, height);

            // 2. Main White Card Container
            ctx.save();
            ctx.shadowColor = 'rgba(15, 23, 42, 0.10)';
            ctx.shadowBlur = 24;
            ctx.shadowOffsetY = 8;
            ctx.fillStyle = '#ffffff';
            roundRect(36, 36, width - 72, height - 72, 28);
            ctx.fill();
            ctx.restore();

            // Card Border
            ctx.strokeStyle = '#e2e8f0';
            ctx.lineWidth = 2;
            roundRect(36, 36, width - 72, height - 72, 28);
            ctx.stroke();

            // 3. Purple Header Banner
            ctx.save();
            roundRect(36, 36, width - 72, 155, 28);
            ctx.clip();
            const headerGrad = ctx.createLinearGradient(36, 36, width - 36, 191);
            headerGrad.addColorStop(0, '#7e22ce');
            headerGrad.addColorStop(1, '#9333ea');
            ctx.fillStyle = headerGrad;
            ctx.fillRect(36, 36, width - 72, 155);
            ctx.restore();

            // Header Text
            ctx.fillStyle = '#ffffff';
            ctx.textAlign = 'center';
            ctx.font = 'bold 23px Georgia, serif';
            ctx.fillText('Masaganang Pagpapala Pasalubong', width / 2, 92);

            ctx.fillStyle = '#f3e8ff';
            ctx.font = '600 14px sans-serif';
            ctx.fillText('OFFICIAL PICKUP VERIFICATION PASS', width / 2, 120);

            // Ticket ID Pill
            ctx.fillStyle = '#ffffff';
            roundRect(width / 2 - 115, 138, 230, 34, 17);
            ctx.fill();
            ctx.fillStyle = '#6b21a8';
            ctx.font = 'bold 16px monospace';
            ctx.fillText(`TICKET #${ticketId}`, width / 2, 160);

            // 4. QR Frame Box
            const qrBoxSize = 310;
            const qrBoxX = (width - qrBoxSize) / 2;
            const qrBoxY = 222;

            ctx.fillStyle = '#ffffff';
            ctx.strokeStyle = '#d8b4fe';
            ctx.lineWidth = 3;
            roundRect(qrBoxX, qrBoxY, qrBoxSize, qrBoxSize, 22);
            ctx.fill();
            ctx.stroke();

            // Extract QR Image (handles inline SVG or <img>)
            const qrContainer = document.getElementById('qrSourceContainer');
            const svgEl = qrContainer ? qrContainer.querySelector('svg') : null;
            const imgEl = qrContainer ? qrContainer.querySelector('img') : null;

            const qrImage = await new Promise((resolve, reject) => {
                if (svgEl) {
                    const clonedSvg = svgEl.cloneNode(true);
                    clonedSvg.setAttribute('width', '260');
                    clonedSvg.setAttribute('height', '260');
                    const svgData = new XMLSerializer().serializeToString(clonedSvg);
                    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
                    const url = URL.createObjectURL(svgBlob);
                    const img = new Image();
                    img.onload = () => {
                        URL.revokeObjectURL(url);
                        resolve(img);
                    };
                    img.onerror = reject;
                    img.src = url;
                } else if (imgEl) {
                    const img = new Image();
                    img.crossOrigin = 'anonymous';
                    img.onload = () => resolve(img);
                    img.onerror = () => resolve(imgEl);
                    img.src = imgEl.currentSrc || imgEl.src;
                } else {
                    reject(new Error('QR element not found'));
                }
            });

            ctx.drawImage(qrImage, qrBoxX + 25, qrBoxY + 25, 260, 260);

            // Helper text below QR
            ctx.fillStyle = '#64748b';
            ctx.font = 'bold 14px sans-serif';
            ctx.fillText('Present this QR code to staff upon pickup', width / 2, qrBoxY + qrBoxSize + 28);

            // 5. Dashed Ticket Perforation Divider
            ctx.save();
            ctx.setLineDash([8, 8]);
            ctx.strokeStyle = '#cbd5e1';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(64, 585);
            ctx.lineTo(width - 64, 585);
            ctx.stroke();
            ctx.restore();

            // 6. Order Summary Details Block
            ctx.fillStyle = '#f8fafc';
            roundRect(64, 605, width - 128, 122, 14);
            ctx.fill();

            const rows = [
                ['Customer:', customerName],
                ['Branch:', pickupBranch],
                ['Schedule:', `${pickupDate} • ${pickupTime}`]
            ];

            rows.forEach(([label, val], idx) => {
                const y = 636 + idx * 32;
                ctx.textAlign = 'left';
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 14px sans-serif';
                ctx.fillText(label, 86, y);

                ctx.textAlign = 'right';
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 14px sans-serif';
                ctx.fillText(String(val).slice(0, 34), width - 86, y);
            });

            // 7. Footer Note
            ctx.textAlign = 'center';
            ctx.fillStyle = '#7e22ce';
            ctx.font = 'italic bold 14px Georgia, serif';
            ctx.fillText('~ Salamat sa iyong pagtangkilik! ~', width / 2, 756);

            // Trigger PNG Download
            const link = document.createElement('a');
            link.download = `Ticket-${ticketId}-QR-Pass.png`;
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (err) {
            console.error('QR Download Error:', err);
            alert('Could not generate the QR image. Please try taking a screenshot or refreshing the page.');
        } finally {
            if (btn) btn.innerText = originalLabel;
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeSliderOverlay(null);
    });
</script>
@endsection