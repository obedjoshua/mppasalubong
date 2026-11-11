<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    
    <style>
        /* Exact Recoleta / Bold Font Setup */
        @font-face {
            font-family: 'Recoleta';
            src: url('/fonts/Recoleta-Bold.woff2') format('woff2');
            font-weight: bold;
        }

        .page-header-title {
            font-family: 'Recoleta', 'Recoleta-Bold', 'Georgia', serif;
            font-size: 3.5rem;
            font-weight: 800;
            color: #8a38f5;
            margin: 0;
            line-height: 1;
        }

        /* Custom Purple Checkbox */
        .custom-checkbox-container {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            z-index: 10;
        }
        .custom-checkbox-container input[type="checkbox"] {
            display: none;
        }
        .checkmark-box {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e1;
            border-radius: 8px;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .custom-checkbox-container input[type="checkbox"]:checked + .checkmark-box {
            background-color: #8a38f5;
            border-color: #8a38f5;
        }
        .checkmark-box svg {
            display: none;
            stroke: #ffffff;
            stroke-width: 3.5;
        }
        .custom-checkbox-container input[type="checkbox"]:checked + .checkmark-box svg {
            display: block;
        }

        /* 1:1 Exact Pill Notification Banner Card */
        .notif-card-banner {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: #ffffff;
            overflow: hidden;
            height: 96px;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .notif-card-banner:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(138, 56, 245, 0.08);
        }

        /* Left Content Area */
        .notif-text-area {
            position: relative;
            z-index: 3;
            padding: 0 0 0 28px;
            max-width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .notif-title {
            margin: 0 0 4px 0;
            color: #8a38f5;
            font-family: 'Recoleta', 'Recoleta-Bold', 'Georgia', serif;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.01em;
        }
        .notif-subtitle {
            margin: 0;
            color: #64748b;
            font-family: 'Manrope', sans-serif;
            font-size: 0.925rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Gradient Mask Overlay & Banner Background */
        .notif-banner-right {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 55%;
            background-size: cover;
            background-position: center right;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 24px;
        }
        .notif-banner-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: -1px;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, #ffffff 0%, rgba(255, 255, 255, 0.85) 30%, transparent 100%);
        }

        /* Badge Icon Circle Positioning */
        .status-badge {
            position: relative;
            z-index: 4;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        /* Unread Badge: Solid Purple Circle with White Chevron */
        .badge-unread {
            background: #8a38f5;
            color: #ffffff;
        }

        /* Read Badge: White Circle with Green Checkmark */
        .badge-read {
            background: #ffffff;
            color: #10b981;
            border: 2px solid #10b981;
        }
    </style>
</head>
<body>
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        @php
            $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
            $allNotifications = auth()->user()->notifications;
        @endphp

        <!-- Top Header & Action Buttons -->
        <div class="dashboard-header-area" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <h1 class="page-header-title">Notifications</h1>
            
            <div style="display: flex; align-items: center; gap: 16px;">
                @if($unreadCount > 0)
                    <form action="{{ route('user.notifications.mark-all-read') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #8a38f5; font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 13px; text-transform: uppercase; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>MARK ALL AS READ</span>
                        </button>
                    </form>
                @endif

                <button id="btn-delete-selected" onclick="submitBatchDelete()" disabled style="background: #ef4444; color: #ffffff; border: none; padding: 10px 20px; border-radius: 10px; font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 13px; text-transform: uppercase; cursor: not-allowed; opacity: 0.5; transition: all 0.2s ease;">
                    DELETE SELECTED (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>

        <div class="dashboard-content-grid" style="grid-template-columns: 1fr; margin-top: 10px;">
            <div class="dash-card" style="padding: 0; background: transparent; border: none; box-shadow: none;">

                @if($allNotifications->isEmpty())
                    <p class="empty-state-text">You have no new notifications.</p>
                @else
                    <form id="batch-delete-form" action="{{ route('user.notifications.batch-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <!-- Select All Header Bar -->
                        <div style="padding: 14px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px;">
                            <label class="custom-checkbox-container">
                                <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this)">
                                <div class="checkmark-box">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                            </label>
                            <span style="font-family: 'Manrope', sans-serif; font-weight: 800; font-size: 14px; color: #334155;">Select All Notifications</span>
                        </div>

                        <!-- Pill Notification Card List -->
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($allNotifications as $notification)
                                @php
                                    $payload = is_array($notification->data) ? $notification->data : [];
                                    
                                    $title = $notification->title 
                                        ?? $payload['title'] 
                                        ?? 'Notification Title';
                                        
                                    $message = $notification->message 
                                        ?? $payload['message'] 
                                        ?? $payload['body'] 
                                        ?? '';

                                    $targetUrl = $notification->action_url 
                                        ?? $payload['action_url'] 
                                        ?? route('notifications.show', $notification->id);

                                    $isRead = (bool) ($notification->is_read ?? false);
                                    
                                    $bannerImage = $notification->banner_image 
                                        ?? $payload['banner_image'] 
                                        ?? asset('images/community_header_01.png');
                                @endphp

                                <div style="display: flex; align-items: center; gap: 16px;">
                                    
                                    <!-- Custom Purple Checkbox -->
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="notification_ids[]" value="{{ $notification->id }}" class="notif-checkbox" onchange="updateSelectionState()">
                                        <div class="checkmark-box">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </div>
                                    </label>

                                    <!-- Pill Card Banner -->
                                    <a href="{{ $targetUrl }}" class="notif-card-banner" style="flex: 1;">
                                        
                                        <!-- Text Area -->
                                        <div class="notif-text-area">
                                            <h3 class="notif-title">{{ $title }}</h3>
                                            @if(!empty($message))
                                                <p class="notif-subtitle">{{ $message }}</p>
                                            @endif
                                        </div>

                                        <!-- Banner Image & Action Badge -->
                                        <div class="notif-banner-right" style="background-image: url('{{ $bannerImage }}');">
                                            @if($isRead)
                                                <!-- Read: White Circle with Green Checkmark -->
                                                <div class="status-badge badge-read">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </div>
                                            @else
                                                <!-- Unread: Solid Purple Circle with White Chevron -->
                                                <div class="status-badge badge-unread">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                                                        <polyline points="9 18 15 12 9 6"></polyline>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div> 

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        function toggleSelectAll(selectAllEl) {
            const checkboxes = document.querySelectorAll('.notif-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAllEl.checked);
            updateSelectionState();
        }

        function updateSelectionState() {
            const checkboxes = document.querySelectorAll('.notif-checkbox');
            const selected = document.querySelectorAll('.notif-checkbox:checked');
            const selectAllEl = document.getElementById('select-all-checkbox');
            const btnDelete = document.getElementById('btn-delete-selected');
            const countSpan = document.getElementById('selected-count');

            if (countSpan) countSpan.textContent = selected.length;

            if (selectAllEl && checkboxes.length > 0) {
                selectAllEl.checked = checkboxes.length === selected.length;
            }

            if (btnDelete) {
                if (selected.length > 0) {
                    btnDelete.disabled = false;
                    btnDelete.style.opacity = '1';
                    btnDelete.style.cursor = 'pointer';
                } else {
                    btnDelete.disabled = true;
                    btnDelete.style.opacity = '0.5';
                    btnDelete.style.cursor = 'not-allowed';
                }
            }
        }

        function submitBatchDelete() {
            if (confirm('Are you sure you want to delete the selected notification(s)?')) {
                document.getElementById('batch-delete-form').submit();
            }
        }

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