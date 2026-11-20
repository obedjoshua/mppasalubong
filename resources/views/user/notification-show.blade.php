<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title ?? 'Notification Details' }} | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">

    <style>
        @font-face {
            font-family: 'modern-bold';
            src: url('/fonts/samsungsharpsans-bold.otf') format('opentype');
            font-display: swap;
        }

        @font-face {
            font-family: 'Recoleta-Bold';
            src: url('/fonts/Recoleta Bold.otf') format('opentype');
            font-display: swap;
        }

        @keyframes floatPulse {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-4px) scale(1.04); }
        }

        @keyframes dashDraw {
            from { stroke-dashoffset: 48; }
            to { stroke-dashoffset: 0; }
        }

        .notif-top-bar {
            width: 100%;
            padding: 14px 15px 4px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            font-family: 'modern-bold', sans-serif;
        }

        .btn-back-notifications {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #faf5ff;
            color: #8a38f5;
            border: 1.5px solid #e9d5ff;
            padding: 10px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-family: 'modern-bold', sans-serif;
            font-size: 0.86rem;
            letter-spacing: 0.01em;
            box-shadow: none !important;
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .btn-back-notifications:hover {
            background: #8a38f5;
            border-color: #8a38f5;
            color: #ffffff;
            transform: translateX(-2px);
        }

        .notif-detail-container {
            width: 100%;
            padding: 0 15px;
            margin: 12px 0 32px 0;
            font-family: 'modern-bold', sans-serif;
            box-sizing: border-box;
        }

        .email-card {
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: none !important;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Compact, Full-Bleed Hero Header */
        .email-hero-header {
            position: relative;
            background: linear-gradient(135deg, #6b21a8 0%, #8a38f5 55%, #a855f7 100%);
            padding: 24px 28px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            overflow: hidden;
        }

        .email-hero-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/notif_header_01.png') }}');
            background-size: cover;
            background-position: center;
            opacity: 0.16;
            pointer-events: none;
        }

        .email-hero-left {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 18px;
            text-align: left;
        }

        .email-icon-badge {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            background: #ffffff;
            color: #8a38f5;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: none !important;
            animation: floatPulse 2.8s ease-in-out infinite;
        }

        .email-icon-badge svg path,
        .email-icon-badge svg polyline,
        .email-icon-badge svg rect,
        .email-icon-badge svg circle {
            stroke-dasharray: 48;
            animation: dashDraw 0.9s ease-out forwards;
        }

        .email-brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.35);
            padding: 4px 12px;
            border-radius: 999px;
            font-family: 'modern-bold', sans-serif;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .email-hero-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: clamp(1.35rem, 2.5vw, 1.9rem);
            margin: 0 0 4px 0;
            line-height: 1.15;
            color: #ffffff;
        }

        .email-hero-timestamp {
            font-family: 'modern-bold', sans-serif;
            font-size: 0.8rem;
            color: #f3e8ff;
        }

        /* Responsive Body Grid */
        .email-body {
            padding: 26px 28px;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .email-greeting {
            font-family: 'Recoleta-Bold', serif;
            font-size: 1.15rem;
            color: #1e1b4b;
            margin: 0 0 8px 0;
        }

        .email-message-text {
            font-family: 'modern-bold', sans-serif;
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0 0 18px 0;
        }

        /* Structured Info Box */
        .email-info-card {
            background: #faf5ff;
            border: 1.5px solid #e9d5ff;
            border-radius: 16px;
            padding: 16px 20px;
            margin: 0;
            box-shadow: none !important;
        }

        .email-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #d8b4fe;
            gap: 12px;
            flex-wrap: wrap;
        }

        .email-info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .email-info-row:first-child {
            padding-top: 0;
        }

        .email-info-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'modern-bold', sans-serif;
            font-size: 0.84rem;
            color: #6b21a8;
        }

        .email-info-value {
            font-family: 'modern-bold', sans-serif;
            font-size: 0.88rem;
            color: #1e293b;
            text-align: right;
            word-break: break-word;
        }

        /* Animated Order Status Stepper inside Notification */
        .notif-tracker-box {
            grid-column: 1 / -1;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 20px 22px 20px;
            box-shadow: none !important;
        }

        .notif-tracker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 8px;
            flex-wrap: wrap;
        }

        .notif-tracker-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .notif-live-pill {
            font-family: 'modern-bold', sans-serif;
            font-size: 0.72rem;
            padding: 4px 10px;
            border-radius: 999px;
            background: #f3e8ff;
            color: #7e22ce;
            transition: all 0.4s ease;
        }

        .stepper-track-wrapper {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0 6px;
        }

        .stepper-line-bg {
            position: absolute;
            top: 20px;
            left: 12.5%;
            right: 12.5%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            z-index: 1;
        }

        .stepper-line-fill {
            position: absolute;
            top: 20px;
            left: 12.5%;
            height: 4px;
            width: 0%;
            max-width: 75%;
            background: linear-gradient(90deg, #8a38f5, #10b981);
            border-radius: 4px;
            z-index: 2;
            transition: width 0.95s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .stepper-node {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 25%;
            text-align: center;
        }

        .stepper-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #cbd5e1;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin-bottom: 8px;
            box-shadow: none !important;
        }

        .stepper-node.is-completed .stepper-circle {
            background: #8a38f5;
            border-color: #8a38f5;
            color: #ffffff;
        }

        .stepper-node.is-current .stepper-circle {
            background: #10b981;
            border-color: #059669;
            color: #ffffff;
            transform: scale(1.1);
            box-shadow: none !important;
        }

        .stepper-label {
            font-family: 'modern-bold', sans-serif;
            font-size: 0.76rem;
            color: #64748b;
            line-height: 1.25;
            transition: color 0.4s ease;
        }

        .stepper-node.is-completed .stepper-label {
            color: #4c1d95;
        }

        .stepper-node.is-current .stepper-label {
            color: #065f46;
        }

        /* CTA Buttons */
        .email-cta-area {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .btn-email-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #8a38f5;
            color: #ffffff;
            padding: 12px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-family: 'modern-bold', sans-serif;
            font-size: 0.88rem;
            letter-spacing: 0.02em;
            box-shadow: none !important;
            transition: transform 0.18s ease, background 0.18s ease;
        }

        .btn-email-primary:hover {
            background: #7225d8;
            transform: translateY(-1px);
        }

        .btn-email-primary.btn-emerald {
            background: #059669;
        }

        .btn-email-primary.btn-emerald:hover {
            background: #047857;
        }

        /* Compact Email Footer */
        .email-footer {
            background: #f8fafc;
            border-top: 1.5px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            font-family: 'modern-bold', sans-serif;
            font-size: 0.78rem;
            color: #64748b;
        }

        /* Tablet & Mobile Breakpoints */
        @media (max-width: 860px) {
            .email-body {
                grid-template-columns: 1fr;
                gap: 18px;
            }
        }

        @media (max-width: 600px) {
            .email-hero-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px 18px;
            }
            .email-hero-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .email-icon-badge {
                width: 56px;
                height: 56px;
            }
            .email-body {
                padding: 18px 16px;
            }
            .stepper-label {
                font-size: 0.68rem;
            }
            .stepper-circle {
                width: 34px;
                height: 34px;
            }
            .stepper-line-bg,
            .stepper-line-fill {
                top: 16px;
            }
            .btn-email-primary {
                width: 100%;
            }
            .email-footer {
                padding: 14px 16px;
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        <div class="notif-top-bar">
            <a href="{{ route('notifications.index') }}" class="btn-back-notifications">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>Back to Notifications</span>
            </a>
        </div>

        @php
            $payload = is_array($notification->data) ? $notification->data : [];
            $title = $notification->title ?? $payload['title'] ?? 'Notification';
            $message = $notification->message ?? $payload['message'] ?? '';
            $details = $notification->details ?? $payload['details'] ?? $message;

            $lowerTitle = strtolower($title);
            $lowerBody = strtolower($details);

            // Extract Ticket ID if mentioned in message/title (e.g. #dgYt5puMrY0 or #24)
            $ticketRef = null;
            if (preg_match('/#([A-Za-z0-9\-]+)/', $title . ' ' . $details, $matches)) {
                $ticketRef = $matches[1];
            }

            $actionUrl = $notification->link
                ?? $notification->action_url
                ?? $payload['action_url']
                ?? ($ticketRef ? route('orders.ticket', $ticketRef) : route('orders.index'));

            // Determine Notification Template Type & Status Bar Animation Steps
            $notifType = 'general';
            $badgeLabel = 'Bakery Update';
            $ctaLabel = $ticketRef ? 'View Order Ticket' : 'Go to My Orders';
            $ctaClass = '';
            $showTracker = false;
            $fromStep = 0;
            $toStep = 0;
            $statusSummary = 'Updated';

            if (str_contains($lowerTitle, 'received') || str_contains($lowerTitle, 'submitted') || str_contains($lowerBody, 'submitted')) {
                $notifType = 'submitted';
                $badgeLabel = 'Official Inquiry Receipt';
                $ctaLabel = 'View Order Ticket';
                $statusSummary = 'Pending Staff Audit';
            } elseif (str_contains($lowerTitle, 'review') || str_contains($lowerBody, 'under active staff review')) {
                $notifType = 'review';
                $badgeLabel = 'Staff Audit in Progress';
                $ctaLabel = 'Open Ticket Details';
                $statusSummary = 'Under Staff Review';
            } elseif (str_contains($lowerTitle, 'approved')) {
                $notifType = 'approved';
                $badgeLabel = 'Quote Approved';
                $ctaLabel = 'Proceed to Payment';
                $ctaClass = 'btn-emerald';
                $showTracker = true;
                $fromStep = 0;
                $toStep = 1;
                $statusSummary = 'Approved for Payment';
            } elseif (str_contains($lowerTitle, 'payment') || str_contains($lowerTitle, 'downpayment') || str_contains($lowerBody, 'in production')) {
                $notifType = 'payment';
                $badgeLabel = 'Payment Verified';
                $ctaLabel = 'View Live Order Status';
                $showTracker = true;
                $fromStep = 1;
                $toStep = 2;
                $statusSummary = 'In Kitchen Production';
            } elseif (str_contains($lowerTitle, 'ready') || str_contains($lowerBody, 'ready for pickup')) {
                $notifType = 'ready';
                $badgeLabel = 'Freshly Baked & Ready';
                $ctaLabel = 'Open Pickup QR Pass';
                $ctaClass = 'btn-emerald';
                $showTracker = true;
                $fromStep = 2;
                $toStep = 3;
                $statusSummary = 'Ready for Pickup';
            } elseif (str_contains($lowerTitle, 'complete')) {
                $notifType = 'completed';
                $badgeLabel = 'Order Completed';
                $ctaLabel = 'View Receipt';
                $showTracker = true;
                $fromStep = 2;
                $toStep = 3;
                $statusSummary = 'Completed';
            }

            $trackerSteps = [
                ['label' => 'Ticket Submitted'],
                ['label' => 'Approved'],
                ['label' => 'In Production'],
                ['label' => 'Ready for Pickup'],
            ];
        @endphp

        <div class="notif-detail-container">
            <div class="email-card">

                <!-- Full-Width Responsive Hero Header -->
                <div class="email-hero-header">
                    <div class="email-hero-left">
                        <div class="email-icon-badge">
                            @if($notifType === 'submitted' || $notifType === 'review')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 34px; height: 34px;">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <polyline points="9 15 11 17 15 13"></polyline>
                                </svg>
                            @elseif($notifType === 'approved' || $notifType === 'payment' || $notifType === 'completed')
                                <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width: 36px; height: 36px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="9 12 11.5 14.5 16 9.5"></polyline>
                                </svg>
                            @elseif($notifType === 'ready')
                                <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 34px; height: 34px;">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 34px; height: 34px;">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                            @endif
                        </div>

                        <div>
                            <div class="email-brand-pill">
                                <span>{{ $badgeLabel }}</span>
                            </div>
                            <h1 class="email-hero-title">{{ $title }}</h1>
                            <div class="email-hero-timestamp">
                                Received {{ $notification->created_at->format('F j, Y \a\t g:i A') }} ({{ $notification->created_at->diffForHumans() }})
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balanced Responsive Body -->
                <div class="email-body">
                    <!-- Left Column: Greeting, Message & Action Button -->
                    <div>
                        <p class="email-greeting">
                            Hello, {{ Auth::user()->name ?? 'Valued Customer' }}!
                        </p>

                        <p class="email-message-text">
                            {{ $details }}
                        </p>

                        <div class="email-cta-area">
                            <a href="{{ $actionUrl }}" class="btn-email-primary {{ $ctaClass }}">
                                <span>{{ $ctaLabel }}</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 17px; height: 17px;">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Structured Summary Receipt Card -->
                    <div class="email-info-card">
                        @if($ticketRef)
                            <div class="email-info-row">
                                <span class="email-info-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                        <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                                        <path d="M13 5v2"></path>
                                        <path d="M13 17v2"></path>
                                        <path d="M13 11v2"></path>
                                    </svg>
                                    Order Ticket Reference
                                </span>
                                <span class="email-info-value" style="color: #7e22ce;">#{{ $ticketRef }}</span>
                            </div>
                        @endif

                        <div class="email-info-row">
                            <span class="email-info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                Current Stage
                            </span>
                            <span class="email-info-value" style="color: #059669;">{{ $statusSummary }}</span>
                        </div>

                        <div class="email-info-row">
                            <span class="email-info-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                Bakery Store
                            </span>
                            <span class="email-info-value" style="font-family: 'Recoleta-Bold', serif;">Masaganang Pagpapala Pasalubong</span>
                        </div>
                    </div>

                    <!-- Full-Width Animated Status Stepper (for Approved, Paid, Ready) -->
                    @if($showTracker)
                        <div class="notif-tracker-box" id="notifStatusTracker" data-from-step="{{ $fromStep }}" data-to-step="{{ $toStep }}">
                            <div class="notif-tracker-header">
                                <span class="notif-tracker-title">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; color: #8a38f5;">
                                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                    </svg>
                                    Order Progress Update
                                </span>
                                <span class="notif-live-pill" id="trackerStagePill">Updating status...</span>
                            </div>

                            <div class="stepper-track-wrapper">
                                <div class="stepper-line-bg"></div>
                                <div class="stepper-line-fill" id="trackerLineFill"></div>

                                @foreach($trackerSteps as $idx => $step)
                                    <div class="stepper-node" data-step-index="{{ $idx }}">
                                        <div class="stepper-circle">
                                            @if($idx === 0)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                </svg>
                                            @elseif($idx === 1)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            @elseif($idx === 2)
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                                    <path d="M12 2v4"></path>
                                                    <path d="M8 4v2"></path>
                                                    <path d="M16 4v2"></path>
                                                    <rect x="4" y="8" width="16" height="12" rx="2"></rect>
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <span class="stepper-label">{{ $step['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Compact Footer -->
                <div class="email-footer">
                    <span><span style="font-family: 'Recoleta-Bold', serif; color: #1e293b;">Masaganang Pagpapala Pasalubong</span> &bull; Freshly Baked Custom Orders &amp; Delicacies</span>
                    <span>Need help? Use Contact Us in the header above.</span>
                </div>

            </div>
        </div>
    </div> 

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("globalLoader");
            if (loader) {
                loader.classList.add("fade-out");
                setTimeout(() => loader.remove(), 600);
            }

            // Animate Status Stepper from previous step to new current step
            const tracker = document.getElementById('notifStatusTracker');
            if (tracker) {
                const fromStep = parseInt(tracker.dataset.fromStep || '0', 10);
                const toStep = parseInt(tracker.dataset.toStep || '0', 10);
                const nodes = tracker.querySelectorAll('.stepper-node');
                const lineFill = document.getElementById('trackerLineFill');
                const stagePill = document.getElementById('trackerStagePill');
                const totalSegments = Math.max(1, nodes.length - 1);

                const stepNames = [
                    'Ticket Submitted',
                    'Approved for Payment',
                    'In Production',
                    'Ready for Pickup'
                ];

                function renderStepState(activeStep, isFinal) {
                    const pct = Math.min(100, Math.max(0, (activeStep / totalSegments) * 75));
                    if (lineFill) {
                        lineFill.style.width = `${pct}%`;
                    }

                    nodes.forEach((node, index) => {
                        node.classList.remove('is-completed', 'is-current');
                        if (index < activeStep) {
                            node.classList.add('is-completed');
                        } else if (index === activeStep) {
                            node.classList.add(isFinal ? 'is-current' : 'is-completed');
                        }
                    });

                    if (stagePill) {
                        stagePill.textContent = stepNames[activeStep] || 'Updated';
                        if (isFinal) {
                            stagePill.style.background = '#d1fae5';
                            stagePill.style.color = '#065f46';
                        }
                    }
                }

                renderStepState(fromStep, false);

                setTimeout(() => {
                    renderStepState(toStep, true);
                }, 450);
            }
        });
    </script>
</body>
</html>