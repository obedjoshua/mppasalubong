<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | Masaganang Pagpapala</title>
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
            <h1 class="user-display-name" style="font-size: clamp(40px, 5vw, 60px);">My Wishlist</h1>
        </div>

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="dash-card">
                @if($wishlistItems->isEmpty())
                    <p class="empty-state-text">Your wishlist is currently empty. Start exploring our shop!</p>
                @else
                    <!-- Product Loop will go here once the Products table is established -->
                    <p class="empty-state-text">You have {{ $wishlistItems->count() }} item(s) saved.</p>
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