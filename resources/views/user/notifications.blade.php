<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
</head>
<body>
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        @php
            $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
        @endphp

        <div class="dashboard-header-area" style="display: flex; justify-content: space-between; align-items: center;">
            <h1 class="user-display-name" style="font-size: clamp(40px, 5vw, 60px);">Notifications</h1>
            
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read.all') }}" method="POST">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: var(--purple-main, #8a38f5); font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 13px; text-transform: uppercase; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>Mark all as read</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="dash-card">
                @php
                    $allNotifications = auth()->user()->notifications;
                @endphp

                @if($allNotifications->isEmpty())
                    <p class="empty-state-text">You have no new notifications.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($allNotifications as $notification)
                            @php
                                $payload = is_array($notification->data) ? $notification->data : [];
                                $title = $notification->title ?? $payload['title'] ?? 'Notification';
                                $message = $notification->message ?? $payload['message'] ?? $payload['body'] ?? '';
                                $actionUrl = $notification->action_url ?? $payload['action_url'] ?? null;
                                $type = $notification->type ?? $payload['type'] ?? 'general';
                                $isPromo = $type === 'promotional';
                                $isRead = (bool) ($notification->is_read ?? false);
                            @endphp

                            <div class="{{ $isRead ? 'notify-read' : 'notify-unread' }}" 
                                 style="padding: 16px; border: 2px solid {{ $isPromo ? '#FEF08A' : '#E5E7EB' }}; border-left: 6px solid {{ $isPromo ? '#EAB308' : 'var(--purple-main, #8a38f5)' }}; border-radius: 12px; background: {{ $isRead ? '#FFFFFF' : '#F9FAFB' }};">
                                
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    @if($isPromo)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="#EAB308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <path d="m3 11 18-5v12L3 13v-2z"></path>
                                            <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="var(--purple-main, #8a38f5)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; flex-shrink: 0;">
                                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                            <path d="M12 11h4"></path>
                                            <path d="M12 16h4"></path>
                                        </svg>
                                    @endif
                                    
                                    <h4 style="margin: 0; color: #310669; font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 1.05rem;">
                                        {{ $title }}
                                    </h4>
                                </div>

                                <p style="margin: 0 0 10px 0; color: #4B5563; font-size: 14px; line-height: 1.5;">
                                    {{ $message }}
                                </p>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <small style="color: #9CA3AF; font-size: 12px; font-weight: 500;">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>

                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        @if(!empty($actionUrl))
                                            <a href="{{ $actionUrl }}" style="background: var(--purple-main, #8a38f5); color: #FFFFFF; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 800; text-transform: uppercase;">
                                                View
                                            </a>
                                        @endif

                                        @if(!$isRead)
                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <button type="submit" style="background: none; border: none; color: var(--purple-main, #8a38f5); font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; cursor: pointer; padding: 0; display: inline-flex; align-items: center; gap: 4px;">
                                                    <span>Mark as Read</span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                        <polyline points="12 5 19 12 12 19"></polyline>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
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