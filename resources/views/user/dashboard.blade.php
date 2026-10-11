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
            
            // Direct exclusive account assignment
            if (Auth::user()->email === 'obeddanieljoshua@gmail.com') {
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

                <!-- Text block stacked horizontally next to avatar -->
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
            
            <!-- Left Column: Pending Tickets, Approved Orders, and Purchase History -->
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

            <!-- Right Column: Address & Links -->
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

                        <!-- Phone OTP -->
                        <div class="linked-account" style="border-bottom: none; flex-direction: column; align-items: flex-start; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                                <div class="account-type">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    Phone
                                </div>
                                
                                @if(Auth::user()->phone)
                                    <span class="account-status status-connected">+63{{ Auth::user()->phone }}</span>
                                @else
                                    <form action="{{ route('profile.link.phone') }}" method="POST" style="display: flex; gap: 8px;">
                                        @csrf
                                        <div class="phone-input-wrapper">
                                            <input type="text" name="phone" class="phone-input-ph" placeholder="0912 345 6789" maxlength="11" inputmode="numeric" value="{{ old('phone') }}" required>
                                        </div>
                                        <button type="submit" class="account-status status-disconnected">Send OTP</button>
                                    </form>
                                @endif
                            </div>

                            <!-- Success & Error Messages -->
                            @if(session('success'))
                                <div style="color: #10B981; font-size: 13px; font-weight: 700; background: #DEF7EC; padding: 6px 12px; border-radius: 6px; width: 100%;">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @error('phone')
                                <div style="color: #E92028; font-size: 13px; font-weight: 700; background: #FDE8E8; padding: 6px 12px; border-radius: 6px; width: 100%;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('partials.side-menu')

    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        window.addEventListener('pageshow', function (event) {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;

            const menuLinks = document.querySelectorAll('#side-menu a, #side-menu div.login-box');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (menuToggle) menuToggle.checked = false;
                });
            });

            // Global Loader logic
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