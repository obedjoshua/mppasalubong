<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masaganang Pagpapala Pasalubong')</title>
    
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    
    @stack('styles')
</head>
<body>
    @include('partials.loader')
    
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper">
        @include('partials.header')

        @yield('content')

    </div>
    
    
    @include('partials.side-menu')
    
    <script src="{{ asset('js/welcome.js') }}"></script>
    @stack('scripts')
    
    <script>
        // Handle Back/Forward browser cache to ensure menu is closed
        window.addEventListener('pageshow', function (event) {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) {
                menuToggle.checked = false;
            }
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            const menuToggle = document.getElementById('menu-toggle');
            
            // 1. Force menu closed on initial load
            if (menuToggle) menuToggle.checked = false;

            // 2. Close menu instantly when any link inside it is clicked
            const menuLinks = document.querySelectorAll('#side-menu a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (menuToggle) menuToggle.checked = false;
                });
            });

            // Loader Script
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
                if (elapsedTime < 1000) {
                    hideLoader();
                } else {
                    const remainingTime = Math.max(0, 3000 - elapsedTime);
                    setTimeout(hideLoader, remainingTime);
                }
            });

            setTimeout(hideLoader, 4000);
        });
    </script>
</body>
</html>