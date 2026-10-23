<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masagana Staff Portal</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- External Staff Layout CSS -->
    <link rel="stylesheet" href="{{ asset('css/staff/layout.css') }}">
    @stack('styles')
</head>
<body class="staff-body">

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
</body>
</html>