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

        <div class="dashboard-header-area">
            <h1 class="user-display-name" style="font-size: clamp(40px, 5vw, 60px);">Notifications</h1>
        </div>

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="dash-card">
                @if($notifications->isEmpty())
                    <p class="empty-state-text">You have no new notifications.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($notifications as $notification)
                            <div class="{{ $notification->is_read ? 'notify-read' : 'notify-unread' }}" style="padding: 15px; border: 2px solid #E5E7EB; border-radius: 12px;">
                                <h4 style="margin: 0 0 5px 0; color: #310669; font-family: 'modern-bold', sans-serif;">{{ $notification->title }}</h4>
                                <p style="margin: 0 0 10px 0; color: #6B7280; font-size: 14px;">{{ $notification->message }}</p>
                                
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: var(--purple-main, #8a38f5); font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; cursor: pointer; padding: 0;">Mark as Read &rarr;</button>
                                    </form>
                                @endif
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
        // Ensure side menu is closed on load
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

            // This is the missing logic that hides the loader!
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
            // Failsafe: force hide after 4 seconds
            setTimeout(hideLoader, 4000);
        });
    </script>
</body>
</html>