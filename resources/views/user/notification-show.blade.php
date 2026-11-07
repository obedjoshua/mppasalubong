<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title ?? 'Notification Details' }} | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <style>
        .notif-banner-img {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            border-radius: 12px 12px 0 0;
            display: block;
        }
        .notif-card-container {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-top: 20px;
        }
        .notif-body-content {
            padding: 24px;
        }
    </style>
</head>
<body>
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        <div class="dashboard-header-area" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="{{ route('notifications.index') }}" style="text-decoration: none; color: var(--purple-main, #8a38f5); font-weight: 800; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Notifications
            </a>
        </div>

        @php
            $payload = is_array($notification->data) ? $notification->data : [];
            $title = $notification->title ?? $payload['title'] ?? 'Notification';
            $message = $notification->message ?? $payload['message'] ?? '';
            $details = $notification->details ?? $payload['details'] ?? $message;
            $actionUrl = $notification->action_url ?? $payload['action_url'] ?? null;
            $bannerPath = $notification->banner_path ?? $payload['banner_path'] ?? null;
            
            if (!$bannerPath) {
                $defaultBanners = ['images/community_header_01.png', 'images/notif_header_01.png'];
                $bannerPath = $defaultBanners[array_rand($defaultBanners)];
            }
        @endphp

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr;">
            <div class="notif-card-container">
                
                <!-- Notification Banner Header Image -->
                <img src="{{ asset($bannerPath) }}" 
                     alt="Notification Banner" 
                     class="notif-banner-img" 
                     onerror="this.onerror=null; this.src='{{ asset('images/notif_header_01.png') }}';" />

                <div class="notif-body-content">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <h2 style="font-family: 'Manrope', sans-serif; font-weight: 800; color: #310669; margin: 0; font-size: 1.5rem;">
                            {{ $title }}
                        </h2>
                        <small style="color: #9ca3af; font-size: 0.8rem; font-weight: 600; white-space: nowrap; margin-left: 12px;">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <p style="color: #4b5563; font-size: 1rem; line-height: 1.6; margin-bottom: 20px;">
                        {{ $details }}
                    </p>

                    @if(!empty($actionUrl))
                        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                            <a href="{{ $actionUrl }}" style="display: inline-block; background: var(--purple-main, #8a38f5); color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 800; font-size: 0.9rem; text-transform: uppercase;">
                                View Details &rarr;
                            </a>
                        </div>
                    @endif
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
        });
    </script>
</body>
</html>