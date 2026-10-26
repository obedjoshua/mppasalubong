<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masagana Staff Portal</title>

    <!-- Preload critical fonts for faster text rendering -->
    <link rel="preload" href="{{ asset('fonts/samsungsharpsans-bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Recoleta Bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Jomhuria-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/MangoGrotesque-Regular.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('Aloevera.ttf') }}" as="font" type="font/ttf" crossorigin>

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('css/staff/layout.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">

    <!-- Load CSS with print media trick for non-blocking -->
    <link rel="stylesheet" href="{{ asset('css/staff/layout.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
<body class="staff-body">

    @include('partials.loader')

    <!-- Hidden Checkbox Toggle for Side Menu Drawer -->
    <input type="checkbox" id="menu-toggle" class="d-none">

    <!-- Include Standalone Staff Header -->
    @include('partials.staff-header')
    
    <!-- Include Staff Side Menu Navigation Drawer -->
    @include('partials.staff-side-menu')

    <!-- Main Content Wrapper -->
    <div class="staff-content-wrapper">
        
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main View Outlet -->
        <main class="container-fluid p-4 flex-fill">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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