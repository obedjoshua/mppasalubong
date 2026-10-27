<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Operations Portal') | Masagana</title>
    <link rel="preload" href="{{ asset('css/staff/layout.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/staff/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portals.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="staff-body portal-body">
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" class="d-none">
    @include('partials.staff-side-menu')

    <div class="staff-content-wrapper portal-content-wrapper">
        @include('partials.staff-header')
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
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">{{ $errors->first() }}</div>
        @endif
        <main class="container-fluid p-4 flex-fill portal-main">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        window.addEventListener('pageshow', () => {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;
        });

        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menu-toggle');
            const loaderStartedAt = Date.now();
            document.querySelectorAll('#side-menu a').forEach(link => {
                link.addEventListener('click', () => {
                    if (menuToggle) menuToggle.checked = false;
                });
            });

            const loader = document.getElementById('globalLoader');
            const hideLoader = () => {
                if (!loader) return;
                loader.classList.add('fade-out');
                setTimeout(() => loader.remove(), 600);
            };
            window.addEventListener('load', () => {
                window.setTimeout(hideLoader, Math.max(0, 3000 - (Date.now() - loaderStartedAt)));
            }, { once: true });
            setTimeout(hideLoader, 4000);
        });
    </script>
</body>
</html>
