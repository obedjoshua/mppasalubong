<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | Masaganang Pagpapala Pasalubong</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Firebase SDK (v10 Compat) -->
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-auth-compat.js"></script>

    <style>
        /* Firebase Verification Modal Styles */
        .otp-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .otp-modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .otp-modal-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 28px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.2s ease;
        }

        .otp-modal-overlay.active .otp-modal-card {
            transform: scale(1);
        }

        .otp-modal-title {
            font-family: 'Recoleta-Bold', serif;
            font-size: 1.4rem;
            color: #310669;
            margin: 0 0 8px 0;
        }

        .otp-modal-desc {
            font-family: 'Manrope', sans-serif;
            font-size: 0.85rem;
            color: #6B7280;
            margin-bottom: 20px;
        }

        .otp-code-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-family: 'Manrope', sans-serif;
            font-size: 1.2rem;
            text-align: center;
            letter-spacing: 6px;
            font-weight: 800;
            margin-bottom: 16px;
            outline: none;
            box-sizing: border-box;
        }

        .otp-code-input:focus {
            border-color: #8A38F5;
        }

        .btn-modal-verify {
            width: 100%;
            background: #10B981;
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .btn-modal-verify:hover {
            background: #059669;
        }

        .btn-modal-close {
            background: transparent;
            border: none;
            color: #9CA3AF;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 12px;
        }

        .btn-modal-close:hover {
            color: #4B5563;
        }

        .resend-timer-box {
            margin-top: 12px;
            font-size: 0.8rem;
            font-family: 'Manrope', sans-serif;
            color: #6B7280;
        }

        .btn-resend-trigger {
            background: none;
            border: none;
            color: #8A38F5;
            font-weight: 800;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
        }

        #recaptcha-container {
            margin: 10px auto;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        
        @include('partials.header')

        @php
            $hour = date('H');
            $greeting = 'Good evening';
            if ($hour < 12) { $greeting = 'Good morning'; } 
            elseif ($hour < 18) { $greeting = 'Good afternoon'; }
            
            $userTickets = Auth::user()->tickets()->with('items')->latest()->get();

            // Approved tickets move to Order History
            $approvedOrders = $userTickets->filter(function($t) {
                return strtolower($t->status) === 'approved';
            });

            // Pending tickets stay under Order Inquiry Tickets
            $pendingTickets = $userTickets->filter(function($t) {
                return strtolower($t->status) !== 'approved';
            });

            $orderCount = $approvedOrders->count();
            
            if (Auth::user()->role === 'admin') {
                $frameClass = 'frame_exclusive';
            } elseif ($orderCount >= 100) {
                $frameClass = 'frame_100';
            } elseif ($orderCount >= 20) {
                $frameClass = 'frame_20';
            } elseif ($orderCount >= 10) {
                $frameClass = 'frame_10';
            } else {
                $frameClass = 'frame_reg01';
            }
        @endphp

        <div class="dashboard-header-area" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div class="user-greeting-box" style="display: flex; align-items: center; gap: 20px; flex-direction: row;">
                <div class="avatar-wrapper">
                    <img src="{{ !empty(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('images/default-avatar.jpeg') }}" alt="Profile" class="profile-avatar">
                    <img src="{{ asset('images/frames/' . (Auth::user()->selected_frame ?: $frameClass) . '.png') }}" alt="Frame" class="frame-overlay">
                    @if(!Auth::user()->avatar)
                        <div class="add-photo-badge" title="Add Profile Picture">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </div>
                    @endif
                </div>

                <div style="display: flex; flex-direction: column;">
                    <div class="greeting-time">{{ $greeting }}</div>
                    <div class="name-score-wrapper">
                        <h1 class="user-display-name">{{ explode(' ', Auth::user()->name)[0] }}</h1>
                        
                        <div class="order-count-wrapper">
                            <span class="user-score">{{ $orderCount }}</span>
                            
                            <div class="info-icon-wrapper">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <span class="tooltip-text">Total Approved Orders</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <a href="{{ route('profile.edit') }}" class="edit-profile-pill">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                EDIT PROFILE
            </a>
        </div>

        <!-- 2. QUICK ACTION BUTTONS -->
        <div class="dashboard-actions">
            <a href="{{ route('shop') }}" class="dash-action-btn dash-btn-shop">Shop Now</a>
            <a href="{{ route('home') }}" class="dash-action-btn dash-btn-order">Order Now</a>
        </div>

        <div style="margin: 20px; background: #FFFFFF; border: 2.5px solid #E5E7EB; border-radius: 20px; padding: clamp(20px, 3vw, 32px);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-family: 'Recoleta-Bold', serif; font-size: clamp(22px, 2.5vw, 28px); color: #310669; line-height: 1; margin: 0;">
                    Badge Collection
                </h2>
                <span style="font-family: 'Manrope', sans-serif; font-size: 14px; font-weight: 800; color: #6B7280;">
                    {{ Auth::user()->badges()->count() }} / {{ \App\Models\Badge::count() }} Unlocked
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 15px;">
                @php
                    $allBadges = \App\Models\Badge::all();
                    $unlockedIds = Auth::user()->badges->pluck('id')->toArray();
                @endphp

                @forelse($allBadges as $badge)
                    @php 
                        $isUnlocked = in_array($badge->id, $unlockedIds); 
                        $reqParts = explode('_', $badge->slug);
                        $reqType = $reqParts[0];
                        $reqNum = $reqParts[1] ?? 0;
                        $reqText = $reqType === 'order' ? 'Orders' : ($reqType === 'shop' ? 'Purchases' : 'Posts');
                        $statusText = $isUnlocked ? 'Unlocked!' : 'Goal: ' . $reqNum . ' ' . $reqText;
                    @endphp
                    
                    <div class="badge-card {{ $isUnlocked ? 'unlocked' : 'locked' }}" title="{{ $badge->description }}">
                        <img src="{{ asset($badge->icon_path) }}" alt="{{ $badge->name }}">
                        <span class="badge-title">{{ $badge->name }}</span>
                        <span class="badge-status">{{ $statusText }}</span>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; font-family: 'Manrope', sans-serif; color: #9CA3AF; padding: 20px;">
                        No badges found. Please run the Badge Seeder!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. DASHBOARD CONTENT GRID -->
        <div class="dashboard-content-grid">
            
            <!-- Left Column -->
            <div class="dash-column">

                <!-- Order Inquiry Tickets Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h2 class="dash-card-title">Order Inquiry Tickets</h2>
                        <a href="{{ route('tickets.index') }}" class="dash-link">View All ({{ $pendingTickets->count() }}) →</a>
                    </div>
                    @if($pendingTickets->isEmpty())
                        <p class="empty-state-text">You have no pending order inquiry tickets.</p>
                    @else
                        <div class="ticket-preview-list">
                            @foreach($pendingTickets->take(3) as $ticket)
                                <div class="ticket-preview-item">
                                    <div class="ticket-info">
                                        <span class="ticket-id">Ticket #{{ $ticket->id }}</span>
                                        <span class="ticket-details">
                                            {{ $ticket->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('orders.ticket', $ticket->id) }}" class="ticket-view-btn">View Ticket</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Approved Order History Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h2 class="dash-card-title">Order History</h2>
                        @if(!$approvedOrders->isEmpty())
                            <a href="{{ route('orders.index') }}" class="dash-link">View All ({{ $approvedOrders->count() }}) →</a>
                        @endif
                    </div>
                    @if($approvedOrders->isEmpty())
                        <p class="empty-state-text">You have no active or recent approved orders.</p>
                    @else
                        <div class="ticket-preview-list">
                            @foreach($approvedOrders->take(3) as $order)
                                <div class="ticket-preview-item approved-item">
                                    <div class="ticket-info">
                                        <span class="ticket-id approved-id">Order #MP-{{ $order->id }}</span>
                                        <span class="ticket-details">
                                            {{ $order->items->pluck('item_type')->map(fn($t) => ucfirst(str_replace('_', ' ', $t)))->join(', ') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('orders.ticket', $order->id) }}" class="ticket-view-btn approved-btn">Confirmed Receipt</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Purchase History Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h2 class="dash-card-title">Purchase History</h2>
                    </div>
                    <p class="empty-state-text">You have not made any purchases yet.</p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="dash-column">
                
                <!-- Address Book -->
                <div class="dash-card">
                    <div class="info-block" style="margin-bottom: 0;">
                        <div class="dash-card-header" style="margin-bottom: 16px;">
                            <h2 class="dash-card-title">Address Book</h2>
                            <a href="#" class="dash-link">Edit</a>
                        </div>
                        <h4>Default Shipping Address</h4>
                        <p>{{ Auth::user()->name }}<br>
                        123 Pasalubong Street, Brgy. San Jose<br>
                        Natividad, Ilocos Region 2446<br>
                        Philippines</p>
                    </div>
                </div>

                <!-- Linked Accounts -->
                <div class="dash-card">
                    <div class="info-block" style="margin-bottom: 0;">
                        <h2 class="dash-card-title" style="margin-bottom: 16px;">Linked Accounts</h2>
                        
                        <!-- Google -->
                        <div class="linked-account">
                            <div class="account-type">
                                <svg viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                                Google
                            </div>
                            @if(Auth::user()->google_id)
                                <span class="account-status status-connected">Connected</span>
                            @else
                                <a href="{{ route('auth.google') }}" class="account-status status-disconnected">Link Account</a>
                            @endif
                        </div>

                        <!-- Facebook -->
                        <div class="linked-account">
                            <div class="account-type">
                                <svg viewBox="0 0 48 48"><path fill="#18acfe" d="M24 4C12.954 4 4 12.954 4 24c0 10.028 7.378 18.337 17.062 19.789v-13.68h-4.856V24h4.856v-4.498c0-5.112 3.036-7.859 7.643-7.859 2.228 0 4.551.398 4.551.398v5h-2.564c-2.525 0-3.313 1.568-3.313 3.176V24h5.672l-.907 6.109h-4.765v13.68C36.622 42.337 44 34.028 44 24 44 12.954 35.046 4 24 4z"/></svg>
                                Facebook
                            </div>
                            @if(Auth::user()->facebook_id)
                                <span class="account-status status-connected">Connected</span>
                            @else
                                <a href="{{ route('auth.facebook') }}" class="account-status status-disconnected">Link Account</a>
                            @endif
                        </div>

                        <!-- Phone OTP with Firebase Modal Trigger -->
                        <div class="linked-account" style="border-bottom: none; flex-direction: column; align-items: flex-start; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                                <div class="account-type">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    Phone
                                </div>
                                
                                @if(Auth::user()->phone)
                                    <span class="account-status status-connected">+63{{ Auth::user()->phone }}</span>
                                @else
                                    <div style="display: flex; gap: 8px;">
                                        <div class="phone-input-wrapper">
                                            <input type="text" id="dash-phone-input" class="phone-input-ph" placeholder="0912 345 6789" maxlength="11" inputmode="numeric">
                                        </div>
                                        <button type="button" id="trigger-send-otp-btn" class="account-status status-disconnected" style="border: none; cursor: pointer;">Send OTP</button>
                                    </div>
                                @endif
                            </div>

                            <!-- Invisible reCAPTCHA container -->
                            <div id="recaptcha-container"></div>

                            <p id="dash-phone-message" style="display: none; font-size: 13px; font-weight: 700; margin: 0; width: 100%; border-radius: 6px; padding: 6px 12px;"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FIREBASE OTP VERIFICATION MODAL -->
    <div class="otp-modal-overlay" id="otpModal">
        <div class="otp-modal-card">
            <h3 class="otp-modal-title">Enter Verification Code</h3>
            <p class="otp-modal-desc">We sent a 6-digit code to <strong id="modal-phone-display">+63</strong></p>

            <input type="text" id="modal-otp-code" class="otp-code-input" placeholder="000000" maxlength="6" inputmode="numeric">

            <button type="button" id="modal-verify-btn" class="btn-modal-verify">Verify & Link Phone</button>

            <div class="resend-timer-box">
                <span id="modal-timer-text">Resend code in <strong id="modal-countdown">30</strong>s</span>
                <button type="button" id="modal-resend-btn" class="btn-resend-trigger" style="display: none;">Resend Code</button>
            </div>

            <p id="modal-error-msg" style="display: none; color: #DC2626; font-size: 0.8rem; font-weight: 700; margin-top: 10px;"></p>

            <button type="button" class="btn-modal-close" onclick="closeOtpModal()">Cancel</button>
        </div>
    </div>
    
    @include('partials.side-menu')

    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        // 1. Initialize Firebase Config
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };
    
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
    
        let confirmationResultObj = null;
        let countdownTimer = null;
    
        // 2. Explicitly Initialize reCAPTCHA Verifier
        function setupRecaptcha() {
            if (!window.recaptchaVerifier) {
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {
                        // reCAPTCHA solved, proceed with phone auth
                    },
                    'expired-callback': () => {
                        showDashMessage('reCAPTCHA expired. Please try clicking Send OTP again.', '#E92028', '#FDE8E8');
                    }
                });
            }
        }
    
        function openOtpModal(formattedPhone) {
            document.getElementById('modal-phone-display').innerText = formattedPhone;
            document.getElementById('otpModal').classList.add('active');
            startResendTimer();
        }
    
        function closeOtpModal() {
            document.getElementById('otpModal').classList.remove('active');
            clearInterval(countdownTimer);
        }
    
        function startResendTimer() {
            let secondsLeft = 30;
            const timerText = document.getElementById('modal-timer-text');
            const countdownEl = document.getElementById('modal-countdown');
            const resendBtn = document.getElementById('modal-resend-btn');
    
            timerText.style.display = 'inline';
            resendBtn.style.display = 'none';
            countdownEl.innerText = secondsLeft;
    
            clearInterval(countdownTimer);
    
            countdownTimer = setInterval(() => {
                secondsLeft--;
                countdownEl.innerText = secondsLeft;
    
                if (secondsLeft <= 0) {
                    clearInterval(countdownTimer);
                    timerText.style.display = 'none';
                    resendBtn.style.display = 'inline';
                }
            }, 1000);
        }
    
        function requestSmsOtp() {
            const phoneInput = document.getElementById('dash-phone-input');
            const sendBtn = document.getElementById('trigger-send-otp-btn');
            const rawPhone = phoneInput ? phoneInput.value.trim() : '';
    
            if (!rawPhone || rawPhone.length < 10) {
                showDashMessage('Please enter a valid 10-digit mobile number (e.g., 09123456789).', '#E92028', '#FDE8E8');
                return;
            }
    
            const formattedPhone = '+63' + rawPhone.replace(/^0+/, '');
            
            setupRecaptcha();
            const appVerifier = window.recaptchaVerifier;
    
            sendBtn.disabled = true;
            sendBtn.innerText = 'Sending...';
    
            firebase.auth().signInWithPhoneNumber(formattedPhone, appVerifier)
                .then((confirmationResult) => {
                    confirmationResultObj = confirmationResult;
                    sendBtn.disabled = false;
                    sendBtn.innerText = 'Send OTP';
                    showDashMessage('', '', '');
                    openOtpModal(formattedPhone);
                })
                .catch((error) => {
                    console.error("Firebase Auth Error:", error);
                    
                    let userFriendlyError = 'Failed to send SMS code. Please try again.';
                    if (error.code === 'auth/invalid-phone-number') {
                        userFriendlyError = 'Invalid phone number format.';
                    } else if (error.code === 'auth/captcha-check-failed') {
                        userFriendlyError = 'reCAPTCHA verification failed. Refresh the page and try again.';
                    } else if (error.message) {
                        userFriendlyError = error.message;
                    }
    
                    showDashMessage(userFriendlyError, '#E92028', '#FDE8E8');
                    sendBtn.disabled = false;
                    sendBtn.innerText = 'Send OTP';
    
                    // Reset reCAPTCHA on failure
                    if (window.grecaptcha && window.recaptchaVerifier) {
                        window.recaptchaVerifier.render().then(widgetId => {
                            grecaptcha.reset(widgetId);
                        });
                    }
                });
        }
    
        function showDashMessage(msg, textColor, bgColor) {
            const el = document.getElementById('dash-phone-message');
            if (!el) return;
    
            if (!msg) {
                el.style.display = 'none';
                return;
            }
            el.innerText = msg;
            el.style.color = textColor;
            el.style.background = bgColor;
            el.style.display = 'block';
        }
    
        document.addEventListener("DOMContentLoaded", function () {
            // Setup reCAPTCHA container on page load
            setupRecaptcha();
    
            const sendBtn = document.getElementById('trigger-send-otp-btn');
            if (sendBtn) {
                sendBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    requestSmsOtp();
                });
            }
    
            const resendBtn = document.getElementById('modal-resend-btn');
            if (resendBtn) {
                resendBtn.addEventListener('click', function() {
                    requestSmsOtp();
                });
            }
    
            const verifyBtn = document.getElementById('modal-verify-btn');
            if (verifyBtn) {
                verifyBtn.addEventListener('click', function() {
                    const code = document.getElementById('modal-otp-code').value.trim();
                    const errorEl = document.getElementById('modal-error-msg');
    
                    if (!code || code.length !== 6) {
                        errorEl.innerText = 'Please enter the 6-digit code.';
                        errorEl.style.display = 'block';
                        return;
                    }
    
                    this.disabled = true;
                    this.innerText = 'Verifying...';
    
                    confirmationResultObj.confirm(code).then((result) => {
                        result.user.getIdToken().then(idToken => {
                            const rawPhone = document.getElementById('dash-phone-input').value.trim();
    
                            fetch("{{ route('profile.otp.verify') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    phone: rawPhone,
                                    id_token: idToken
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                } else {
                                    errorEl.innerText = data.message || 'Failed to link phone.';
                                    errorEl.style.display = 'block';
                                    this.disabled = false;
                                    this.innerText = 'Verify & Link Phone';
                                }
                            })
                            .catch(err => {
                                console.error("Backend Error:", err);
                                errorEl.innerText = 'Server error processing verification.';
                                errorEl.style.display = 'block';
                                this.disabled = false;
                                this.innerText = 'Verify & Link Phone';
                            });
                        });
                    }).catch((error) => {
                        console.error("Verification Error:", error);
                        errorEl.innerText = 'Invalid verification code. Please try again.';
                        errorEl.style.display = 'block';
                        this.disabled = false;
                        this.innerText = 'Verify & Link Phone';
                    });
                });
            }
    
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