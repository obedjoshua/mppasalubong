<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masaganang Pagpapala Pasalubong')</title>

    <!-- Preload critical fonts for faster text rendering -->
    <link rel="preload" href="{{ asset('fonts/samsungsharpsans-bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Recoleta Bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Jomhuria-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/MangoGrotesque-Regular.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('Aloevera.ttf') }}" as="font" type="font/ttf" crossorigin>

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('css/welcome.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">

    <!-- Load CSS with print media trick for non-blocking -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">    
    {{-- Inline critical loader CSS to prevent flash of unstyled loader --}}
    <style>
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }
        .svg-wrapper {
            width: min(80vw, 400px);
            height: auto;
        }
        .drawing-svg {
            width: 100%;
            height: auto;
        }
        .drawing-svg path {
            stroke-dasharray: 3500;
            stroke-dashoffset: 3500;
            animation: drawPath 2.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes drawPath {
            0% { stroke-dashoffset: 3500; fill: transparent; }
            70% { stroke-dashoffset: 0; fill: transparent; }
            100% { stroke-dashoffset: 0; fill: #610DCF; }
        }
        .loader-container.fade-out { opacity: 0; visibility: hidden; }
    </style>


    @stack('styles')

    <!-- Register Service Worker for offline capability -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(registration => {
                        console.log('SW registered:', registration.scope);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>
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
