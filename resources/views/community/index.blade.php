<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-status" content="{{ Auth::check() ? 'true' : 'false' }}">
    <meta name="user-avatar" content="{{ Auth::check() && !empty(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('images/default-avatar.jpeg') }}">
    <meta name="user-frame" content="{{ Auth::check() ? asset('images/frames/' . (Auth::user()->selected_frame ?: 'frame_reg01') . '.png') : '' }}">
    <title>Community | Masaganang Pagpapala</title>

    <!-- Preload critical fonts for faster text rendering -->
    <link rel="preload" href="{{ asset('fonts/samsungsharpsans-bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Recoleta Bold.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/Jomhuria-Regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/MangoGrotesque-Regular.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preload" href="{{ asset('Aloevera.ttf') }}" as="font" type="font/ttf" crossorigin>

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('css/welcome.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/dashboard.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/community.css') }}" as="style">

    <!-- Load CSS with print media trick for non-blocking -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/community.css') }}" media="print" onload="this.media='all'">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts (Manrope) - Load with display=swap -->
    <link href="https://fonts.googleapis.com/css?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">

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
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #F9FAFB;">
        @include('partials.header')

        <img src="images/community_header_04.png" class="community-header-image" alt="Welcome to the Community">
        
        <div class="community-layout">
            
            <!-- COLUMN 1: LEFT SIDEBAR (FILTERS) -->
            <aside class="left-sidebar">
                <h2 class="section-title">Feed Filters</h2>
                <a href="{{ route('community.index') }}" class="filter-btn {{ $filter === 'all' ? 'active' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    Everything
                </a>
                <a href="{{ route('community.index', ['filter' => 'announcement']) }}" class="filter-btn {{ $filter === 'announcement' ? 'active' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    Announcements
                </a>
                <a href="{{ route('community.index', ['filter' => 'review']) }}" class="filter-btn {{ $filter === 'review' ? 'active' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    Reviews
                </a>
                <a href="{{ route('community.index', ['filter' => 'thread']) }}" class="filter-btn {{ $filter === 'thread' ? 'active' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 8px;">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Discussions
                </a>
            </aside>

            <!-- COLUMN 2: MIDDLE (MAIN STREAM) -->
            <main class="feed-column">
                @auth
                <a href="{{ route('community.post.create') }}" class="post-card" style="background: #FFFFFF; display: flex; align-items: center; gap: 15px; padding: 15px 20px; text-decoration: none; transition: 0.2s;">
                    <img src="{{ !empty(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('images/default-avatar.jpeg') }}" style="width: 45px; height: 45px; border-radius: 25%; object-fit: cover;">
                    <div style="flex: 1; background: #F3F4F6; border: 2px solid #E5E7EB; border-radius: 25px; padding: 12px 20px; color: #6B7280; font-family: 'Manrope', sans-serif; font-weight: 700;">
                        What's baking, {{ explode(' ', Auth::user()->name)[0] }}?
                    </div>
                </a>
                @endauth

                @forelse($posts as $post)
                    @include('partials.post-card', ['post' => $post])
                @empty
                    <div style="text-align: center; padding: 40px; color: #6B7280; font-family: 'Manrope'; font-weight: 800;">
                        No posts found in this category yet!
                    </div>
                @endforelse

                <div style="margin-top: 20px;">
                    {{ $posts->appends(['filter' => $filter])->links() }}
                </div>
            </main>

            <!-- COLUMN 3: RIGHT SIDEBAR (PROGRESS & STATS) -->
            <aside class="right-sidebar">
                <h2 class="section-title">Your Progress</h2>
                
                @auth
                    <!-- Orders Progress -->
                    <div class="progress-card">
                        <span style="display: block; font-family: 'modern-bold', sans-serif; font-size: 14px; color: #92400E;">Custom Orders</span>
                        <h3 style="margin: 5px 0; font-family: 'modern-bold', sans-serif; font-size: 24px; color: #B45309;">{{ $orderCount }}</h3>
                        @if($nextOrderBadge)
                            @php $ordersNeeded = $nextOrderBadge->order_requirement - $orderCount; @endphp
                            <div style="margin-top: 10px; font-family: 'Manrope', sans-serif; font-size: 12px; font-weight: 800; color: #92400E;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2v20M2 12h20"></path>
                                </svg>
                                Just <strong>{{ $ordersNeeded }}</strong> more to unlock<br>
                                <img src="{{ asset($nextOrderBadge->icon_path) }}" style="width: 24px; height: 24px; vertical-align: middle; margin: 4px;">
                                {{ $nextOrderBadge->name }}
                            </div>
                        @else
                            <div style="margin-top: 10px; font-family: 'Manrope', sans-serif; font-size: 12px; font-weight: 800; color: #059669;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                    <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></path>
                                </svg>
                                You have unlocked all Order Badges!
                            </div>
                        @endif
                    </div>

                    <!-- Posts Progress -->
                    <div class="progress-card" style="background: #E0E7FF; border-color: #4F46E5;">
                        <span style="display: block; font-family: 'modern-bold', sans-serif; font-size: 14px; color: #3730A3;">Community Posts</span>
                        <h3 style="margin: 5px 0; font-family: 'modern-bold', sans-serif; font-size: 24px; color: #4338CA;">{{ $postCount }}</h3>
                        @if($nextPostBadge)
                            @php $postsNeeded = $nextPostBadge->req - $postCount; @endphp
                            <div style="margin-top: 10px; font-family: 'Manrope', sans-serif; font-size: 12px; font-weight: 800; color: #3730A3;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2v20M2 12h20"></path>
                                </svg>
                                <strong>{{ $postsNeeded }}</strong> more posts to unlock<br>
                                <img src="{{ asset($nextPostBadge->badge->icon_path) }}" style="width: 24px; height: 24px; vertical-align: middle; margin: 4px;">
                                {{ $nextPostBadge->badge->name }}
                            </div>
                        @else
                            <div style="margin-top: 10px; font-family: 'Manrope', sans-serif; font-size: 12px; font-weight: 800; color: #059669;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 4px;">
                                    <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></path>
                                </svg>
                                You are a Community Legend!
                            </div>
                        @endif
                    </div>

                    <!-- EXPANDABLE STACKED UNSHARED ACHIEVEMENTS -->
                    @if(isset($unsharedBadges) && $unsharedBadges->count() > 0)
                        <div class="progress-card" style="background: #F0FDF4; border-color: #10B981; text-align: left; padding: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-family: 'modern-bold', sans-serif; font-size: 14px; color: #047857;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;">
                                        <path d="M12 2L15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></path>
                                    </svg>
                                    Unshared Badges ({{ $unsharedBadges->count() }})
                                </span>
                                @if($unsharedBadges->count() > 1)
                                    <button onclick="toggleAchievementStack()" id="stackToggleBtn" style="background: #D1FAE5; border: none; color: #047857; font-family: 'modern-bold'; font-size: 11px; padding: 4px 8px; border-radius: 8px; cursor: pointer;">
                                        Expand All ▾
                                    </button>
                                @endif
                            </div>

                            <!-- Featured Top Badge -->
                            @php $topBadge = $unsharedBadges->first(); @endphp
                            <div style="text-align: center; background: #FFF; border: 2px solid #A7F3D0; border-radius: 12px; padding: 12px; margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                <img src="{{ asset($topBadge->icon_path) }}" style="width: 48px; height: 48px; margin-bottom: 5px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                                <h4 style="margin: 0 0 8px 0; font-family: 'modern-bold'; font-size: 15px; color: #065F46;">{{ $topBadge->name }}</h4>
                                
                                <form action="{{ route('community.post.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="achievement">
                                    <input type="hidden" name="badge_id" value="{{ $topBadge->id }}">
                                    <input type="hidden" name="content" value="I just unlocked the {{ $topBadge->name }} badge!">
                                    <button type="submit" style="background: #10B981; color: #FFF; border: none; border-radius: 10px; padding: 8px 12px; font-family: 'modern-bold'; font-size: 12px; cursor: pointer; width: 100%; box-shadow: 0 3px 0 #059669; transition: 0.1s;">
                                        Share to Feed
                                    </button>
                                </form>
                            </div>

                            <!-- Stacked Collapsible Badges -->
                            @if($unsharedBadges->count() > 1)
                                <div id="achievementStackContainer" style="display: none; flex-direction: column; gap: 8px; border-top: 2px dashed #A7F3D0; padding-top: 10px; margin-top: 10px;">
                                    @foreach($unsharedBadges->skip(1) as $badge)
                                        <div style="display: flex; align-items: center; justify-content: space-between; background: #FFF; border: 1.5px solid #A7F3D0; border-radius: 10px; padding: 8px 10px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="{{ asset($badge->icon_path) }}" style="width: 32px; height: 32px; object-fit: contain;">
                                                <span style="font-family: 'modern-bold'; font-size: 12px; color: #065F46;">{{ $badge->name }}</span>
                                            </div>
                                            
                                            <form action="{{ route('community.post.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="achievement">
                                                <input type="hidden" name="badge_id" value="{{ $badge->id }}">
                                                <input type="hidden" name="content" value="I just unlocked the {{ $badge->name }} badge!">
                                                <button type="submit" style="background: #10B981; color: #FFF; border: none; border-radius: 8px; padding: 5px 10px; font-family: 'modern-bold'; font-size: 11px; cursor: pointer;">
                                                    Share
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="post-card" style="text-align: center; padding: 20px;">
                        <p style="font-family: 'Manrope', sans-serif; font-size: 14px; font-weight: 800;">Log in to track your badge progress!</p>
                        <a href="{{ route('login') }}" class="save-btn" style="text-decoration: none; display: inline-block; margin-top: 10px;">Log In</a>
                    </div>
                @endauth
            </aside>

        </div>
    </div>

    <!-- Facebook-Style FULL VIEW Picture Modal (Theater Mode) -->
    <div class="modal-overlay" id="fbImageModal" style="display: none; position: fixed; top: 0px; left: 0; width: 100vw; height: 100vh; flex-direction: row; align-items: stretch; justify-content: space-between;">
        <div id="fbImageContainer" style="flex: 1; position: relative; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.9); overflow: hidden;">
            <button onclick="closeFbModal()" title="Close" style="position: absolute; top: 20px; left: 20px; background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 20; transition: 0.2s;">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div style="position: absolute; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 20;">
                <button onclick="zoomImage(0.25)" title="Zoom In" style="background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                </button>
                <button onclick="zoomImage(-0.25)" title="Zoom Out" style="background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                </button>
                <button onclick="toggleExpandMode()" title="Expand Image" style="background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </button>
            </div>
            <button id="fbPrevBtn" onclick="navigateImage(-1)" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; z-index: 20; display: none; align-items: center; justify-content: center; transition: 0.2s;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button id="fbNextBtn" onclick="navigateImage(1)" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: none; color: #FFF; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; z-index: 20; display: none; align-items: center; justify-content: center; transition: 0.2s;">
                <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
            </button>
            <img id="fbFullImage" src="" style="max-width: 100%; max-height: 100%; object-fit: contain; z-index: 10; filter: drop-shadow(0 10px 25px rgba(0,0,0,0.5)); transition: transform 0.2s ease;">
        </div>
        <div id="fbSidebar" style="width: 360px; background: #FFFFFF; height: 100%; flex-shrink: 0; display: flex; flex-direction: column; overflow-y: auto; border-left: 2px solid #D1D5DB; box-shadow: -4px 0 15px rgba(0,0,0,0.05); z-index: 15;">
            <div id="fbSidebarContent" style="padding: 20px;"></div>
        </div>
    </div>

    <!-- Centered Box Comment Modal (Facebook feed layout format) -->
    <div class="modal-overlay" id="fbPostModal" onclick="closeCommentModal(event)">
        <div style="background: #FFFFFF; border-radius: 16px; width: 90%; max-width: 650px; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); border: 1px solid #E5E7EB;" onclick="event.stopPropagation()">
            <!-- Header -->
            <div style="padding: 16px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; background: #FFF;">
                <h3 id="modalPostTitle" style="margin: 0; font-family: 'modern-bold'; font-size: 18px; color: #111827; text-align: center; flex: 1;">Post</h3>
                <button onclick="closeCommentModal()" style="background: #F3F4F6; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; font-size: 18px; color: #4B5563; display: flex; align-items: center; justify-content: center;">&times;</button>
            </div>
            <!-- Body -->
            <div id="modalBody" style="padding: 20px; overflow-y: auto; flex: 1;"></div>
            <!-- Form -->
            @auth
            <div style="padding: 15px 20px; border-top: 1px solid #E5E7EB; background: #FFF;">
                <form id="modalCommentForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="parent_id" id="modalParentId" value="">
                    <div id="replyingToIndicator" style="display: none; font-size: 12px; font-family: 'Manrope'; color: #8A38F5; margin-bottom: 6px; font-weight: 700;">
                        Replying to comment... <button type="button" onclick="cancelReplyTarget()" style="background: none; border: none; color: #EF4444; cursor: pointer;">Cancel</button>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="comment" id="modalCommentInput" placeholder="Write a comment..." required style="flex: 1; padding: 10px 16px; border: 1px solid #D1D5DB; border-radius: 20px; font-family: 'Manrope'; font-size: 13px; outline: none; background: #F9FAFB;">
                        <button type="submit" style="background: #8A38F5; color: #FFF; border: none; padding: 0 20px; border-radius: 20px; font-family: 'modern-bold'; font-size: 13px; cursor: pointer;">Send</button>
                    </div>
                </form>
            </div>
            @endauth
        </div>
    </div>

    <!-- Public Profile Modal -->
    <div class="modal-overlay" id="profileModal" onclick="closeProfileModal(event)">
        <div class="profile-pop-box" onclick="event.stopPropagation()">
            <div style="position: relative; width: 70px; height: 70px; margin: 0 auto 12px;">
                <img src="" id="popAvatar" style="width: 56px; height: 56px; border-radius: 25%; object-fit: cover; position: absolute; top: 7px; left: 7px;">
                <img src="" id="popFrame" style="position: absolute; top: 0; left: 0; width: 70px; height: 70px; object-fit: contain;">
            </div>
            <h2 id="popName" style="font-family: 'modern-bold', sans-serif; font-size: 24px; color: #000; margin: 0;"></h2>
            <p id="popOrders" style="font-family: 'Manrope', sans-serif; font-size: 14px; color: #6B7280; font-weight: 800; margin-top: 4px;"></p>
            <div style="border-top: 2px dashed #E5E7EB; margin: 15px 0; padding-top: 10px;">
                <span style="font-family: 'modern-bold', sans-serif; font-size: 15px; color: #1F2937; display: block; margin-bottom: 5px;">Badges Earned</span>
                <div class="badge-grid-pop" id="popBadgeContainer"></div>
            </div>
            <button onclick="closeProfileModal()" class="save-btn" style="width: 100%; padding: 8px;">Close</button>
        </div>
    </div>

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loader') || document.querySelector('.loader-container') || document.querySelector('.preloader') || document.querySelector('.loader-wrapper');
            if (loader) {
                loader.style.transition = 'opacity 0.3s ease';
                loader.style.opacity = '0';
                setTimeout(function() { loader.style.display = 'none'; }, 300);
            }
            document.body.style.overflow = 'auto';
        });

        document.addEventListener('DOMContentLoaded', function() {
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const authMeta = document.querySelector('meta[name="auth-status"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
            const isLoggedIn = authMeta ? authMeta.getAttribute('content') === 'true' : false;

            // Automatically close modals if the side menu is toggled open
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('change', function() {
                    if (this.checked) {
                        closeFbModal();
                        closeCommentModal();
                        closeProfileModal();
                    }
                });
            }

            document.querySelectorAll('.js-reaction-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!isLoggedIn) { alert('You must be logged in to react!'); return; }

                    const postId = this.getAttribute('data-post-id');
                    const emoji = this.getAttribute('data-emoji');
                    const countSpan = this.querySelector('.js-reaction-count');

                    this.classList.remove('pop-anim');
                    void this.offsetWidth;
                    this.classList.add('pop-anim');

                    fetch(`/community/post/${postId}/react`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ emoji: emoji })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (countSpan) countSpan.textContent = data.count;
                            btn.classList.toggle('user-reacted', data.action === 'added');
                        }
                    })
                    .catch(err => console.error('Reaction error:', err));
                });
            });

            // Auto-open Modals based on URL state
            const urlParams = new URLSearchParams(window.location.search);
            const postIdParam = urlParams.get('post');
            const photoIdParam = urlParams.get('photo');

            if (postIdParam) {
                const postCard = document.getElementById('post-' + postIdParam);
                if (postCard) {
                    const authorEl = postCard.querySelector('.post-header span');
                    const authorName = authorEl ? authorEl.innerText.trim() : 'User';
                    openCommentModal('post-' + postIdParam, authorName, true); 
                }
            } else if (photoIdParam) {
                // If it's a photo ID, grab the original Post matching the ID
                const rawId = photoIdParam.replace('fbid_', '');
                const postCard = document.getElementById('post-' + rawId);
                if (postCard) {
                    const firstImg = postCard.querySelector('.fb-image-grid img');
                    if (firstImg) {
                        openFbModal(firstImg.src, 'post-' + rawId, true);
                    }
                }
            }
        });

        // =====================================
        // INLINE REPLY BOX LOGIC
        // =====================================
        function openInlineReplyBox(postId, commentId, authorName) {
            closeInlineReplyBox();

            const targetSlot = document.getElementById('inline-reply-slot-' + commentId);
            if (!targetSlot) return;

            const avatarMeta = document.querySelector('meta[name="user-avatar"]');
            const frameMeta = document.querySelector('meta[name="user-frame"]');

            const userAvatar = avatarMeta ? avatarMeta.getAttribute('content') : '/images/default-avatar.jpeg';
            const userFrame = frameMeta ? frameMeta.getAttribute('content') : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const postRoute = '/community/post/' + postId + '/comment';

            targetSlot.innerHTML = `
                <form action="${postRoute}" method="POST" id="active-inline-reply-form" style="display: flex; flex-direction: column; gap: 6px; background: #F3F4F6; padding: 10px; border-radius: 12px; border: 1.5px solid #E5E7EB;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="parent_id" value="${commentId}">

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="position: relative; width: 32px; height: 32px; flex-shrink: 0;">
                            <img src="${userAvatar}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; position: absolute; top: 4px; left: 4px; z-index: 1;">
                            ${userFrame ? `<img src="${userFrame}" style="position: absolute; top: 0; left: 0; width: 32px; height: 32px; object-fit: contain; z-index: 2; pointer-events: none;">` : ''}
                        </div>
                        <input type="text" name="comment" placeholder="Reply to ${authorName}..." required autofocus style="flex: 1; padding: 8px 12px; border: 1px solid #D1D5DB; border-radius: 18px; font-family: 'Manrope'; font-size: 12px; outline: none; background: #FFF;">
                        <button type="submit" style="background: #8A38F5; color: white; border: none; padding: 6px 14px; border-radius: 16px; font-family: 'modern-bold'; font-size: 11px; cursor: pointer;">Reply</button>
                        <button type="button" onclick="closeInlineReplyBox()" style="background: #E5E7EB; color: #4B5563; border: none; padding: 6px 10px; border-radius: 16px; font-family: 'modern-bold'; font-size: 11px; cursor: pointer;">Cancel</button>
                    </div>
                </form>
            `;
        }

        function closeInlineReplyBox() {
            const existingForm = document.getElementById('active-inline-reply-form');
            if (existingForm) {
                existingForm.parentElement.innerHTML = '';
            }
        }

        // =====================================
        // COMMENT MODAL LOGIC (Centered Modal)
        // =====================================
        function openCommentModal(postId, authorName, skipState = false) {
            const postCard = document.getElementById(postId);
            if (!postCard) return;

            document.getElementById('modalPostTitle').innerText = `${authorName}'s Post`;

            const rawId = postId.replace('post-', '');
            document.getElementById('modalCommentForm').action = `/community/post/${rawId}/comment`;

            const body = document.getElementById('modalBody');
            body.innerHTML = '';

            const header = postCard.querySelector('.post-header');
            const content = postCard.querySelector('.post-text-content');
            const images = postCard.querySelector('.fb-image-grid');
            const comments = postCard.querySelector('.comments-section');

            if (header) body.appendChild(header.cloneNode(true));
            if (content) body.appendChild(content.cloneNode(true));
            if (images) body.appendChild(images.cloneNode(true));
            if (comments) body.appendChild(comments.cloneNode(true));

            document.getElementById('fbPostModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Push URL state so it persists on refresh
            if (!skipState) {
                const url = new URL(window.location);
                url.searchParams.set('post', rawId);
                window.history.pushState({}, '', url);
            }
        }

        function setReplyTarget(postId, commentId, authorName) {
            const modalParentId = document.getElementById('modalParentId');
            const modalIndicator = document.getElementById('replyingToIndicator');
            const modalInput = document.getElementById('modalCommentInput');

            if (modalParentId && document.getElementById('fbPostModal').style.display === 'flex') {
                modalParentId.value = commentId;
                if (modalIndicator) {
                    modalIndicator.style.display = 'block';
                    modalIndicator.childNodes[0].nodeValue = `Replying to ${authorName}... `;
                }
                if (modalInput) modalInput.focus();
                return;
            }

            // 2. Fallback to inline form on the post card
            const inlineParentInput = document.getElementById('parent-id-input-' + postId);
            const inlineIndicator = document.getElementById('reply-indicator-' + postId);
            const inlineAuthorName = document.getElementById('reply-to-name-' + postId);
            const inlineInput = document.getElementById('comment-input-' + postId);

            if (inlineParentInput) inlineParentInput.value = commentId;
            if (inlineAuthorName) inlineAuthorName.textContent = authorName;
            if (inlineIndicator) inlineIndicator.style.display = 'block';
            if (inlineInput) inlineInput.focus();
        }

        function cancelReplyTarget(postId) {
            if (postId) {
                // Clear Inline Form
                const inlineParentInput = document.getElementById('parent-id-input-' + postId);
                const inlineIndicator = document.getElementById('reply-indicator-' + postId);
                if (inlineParentInput) inlineParentInput.value = '';
                if (inlineIndicator) inlineIndicator.style.display = 'none';
            } else {
                // Clear Modal Form
                const modalParentId = document.getElementById('modalParentId');
                const modalIndicator = document.getElementById('replyingToIndicator');
                if (modalParentId) modalParentId.value = '';
                if (modalIndicator) modalIndicator.style.display = 'none';
            }
        }

        function closeCommentModal(e) {
            if (!e || e.target.id === 'fbPostModal' || e.target.tagName === 'BUTTON') {
                document.getElementById('fbPostModal').style.display = 'none';
                document.body.style.overflow = 'auto';
                cancelReplyTarget();

                // Revert URL state
                const url = new URL(window.location);
                url.searchParams.delete('post');
                window.history.pushState({}, '', url);
            }
        }

        function toggleReplies(postId, commentId, postAuthorName) {
            const modal = document.getElementById('fbPostModal');
            const isModalOpen = modal && modal.style.display === 'flex';

            // 1. If modal is not open yet, open the post modal first
            if (!isModalOpen) {
                openCommentModal('post-' + postId, postAuthorName);
            }

            // 2. Target the specific comment's replies inside the active modal
            setTimeout(() => {
                const modalBody = document.getElementById('modalBody');
                const repliesContainer = modalBody.querySelector('#replies-container-' + commentId) || document.getElementById('replies-container-' + commentId);
                const toggleText = modalBody.querySelector('#toggle-text-' + commentId) || document.getElementById('toggle-text-' + commentId);
                const toggleIcon = modalBody.querySelector('#toggle-icon-' + commentId) || document.getElementById('toggle-icon-' + commentId);

                if (repliesContainer) {
                    const isHidden = repliesContainer.style.display === 'none' || repliesContainer.style.display === '';
                    repliesContainer.style.display = isHidden ? 'flex' : 'none';

                    if (toggleText) {
                        const count = repliesContainer.children.length;
                        toggleText.textContent = isHidden ? `Hide ${count} ${count === 1 ? 'reply' : 'replies'}` : `View ${count} ${count === 1 ? 'reply' : 'replies'}`;
                    }
                    if (toggleIcon) {
                        toggleIcon.textContent = isHidden ? '↩' : '↪';
                    }
                }
            }, isModalOpen ? 0 : 50);
        }

        // Toggle Stacked Achievements
        function toggleAchievementStack() {
            const container = document.getElementById('achievementStackContainer');
            const btn = document.getElementById('stackToggleBtn');
            if (container) {
                const isHidden = container.style.display === 'none' || container.style.display === '';
                container.style.display = isHidden ? 'flex' : 'none';
                if (btn) btn.textContent = isHidden ? 'Collapse ▴' : 'Expand All ▾';
            }
        }

        // Profile Modal
        function openProfileModal(userId) {
            fetch(`/community/user/${userId}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('popName').innerText = data.name;
                    document.getElementById('popOrders').innerText = `${data.orders} Total Orders Completed`;
                    document.getElementById('popAvatar').src = data.avatar;
                    document.getElementById('popFrame').src = data.frame_src;

                    const container = document.getElementById('popBadgeContainer');
                    if (container) {
                        container.innerHTML = '';
                        data.badges.forEach(badge => {
                            const slot = document.createElement('div');
                            slot.className = `badge-slot ${badge.unlocked ? '' : 'locked'}`;
                            slot.title = badge.description;
                            slot.innerHTML = `<img src="${badge.icon_path}" alt="${badge.name}" style="width: 42px; height: 42px; object-fit: contain; margin-bottom: 5px;">
                                              <span style="font-size: 11px; font-family: 'Manrope', sans-serif; font-weight: 800; line-height: 1.1;">${badge.name}</span>`;
                            container.appendChild(slot);
                        });
                    }
                    const modal = document.getElementById('profileModal');
                    if (modal) modal.style.display = 'flex';
                });
        }

        function closeProfileModal() { 
            const modal = document.getElementById('profileModal');
            if (modal) modal.style.display = 'none'; 
        }

        // =====================================
        // FULL VIEW PHOTO MODAL LOGIC 
        // =====================================
        let currentImages = [];
        let currentImgIndex = 0;
        let currentZoom = 1;

        function openFbModal(imageSrc, postId, skipState = false) {
            const postCard = document.getElementById(postId);
            if (!postCard) return;

            currentImages = Array.from(postCard.querySelectorAll('.fb-image-grid img')).map(img => img.src);
            currentImgIndex = currentImages.indexOf(imageSrc);
            if (currentImgIndex === -1) currentImgIndex = 0;

            updateModalImage();
            document.getElementById('fbSidebar').style.display = 'flex';

            const sidebar = document.getElementById('fbSidebarContent');
            sidebar.innerHTML = ''; 

            ['.post-header', '.post-text-content', '.reaction-row', '.comments-section'].forEach(cls => {
                const el = postCard.querySelector(cls);
                if (el) sidebar.appendChild(el.cloneNode(true));
            });

            document.getElementById('fbImageModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; 

            // Simulating a separate photo page behavior via URL
            if (!skipState) {
                const rawId = postId.replace('post-', '');
                const url = new URL(window.location);
                url.searchParams.set('photo', 'fbid_' + rawId);
                window.history.pushState({}, '', url);
            }
        }

        function updateModalImage() {
            const imgEl = document.getElementById('fbFullImage');
            imgEl.src = currentImages[currentImgIndex];
            currentZoom = 1;
            imgEl.style.transform = `scale(${currentZoom})`;

            const showArrows = currentImages.length > 1 ? 'flex' : 'none';
            document.getElementById('fbPrevBtn').style.display = showArrows;
            document.getElementById('fbNextBtn').style.display = showArrows;
        }

        function navigateImage(direction) {
            currentImgIndex += direction;
            if (currentImgIndex < 0) currentImgIndex = currentImages.length - 1;
            if (currentImgIndex >= currentImages.length) currentImgIndex = 0;
            updateModalImage();
        }

        function zoomImage(amount) {
            currentZoom += amount;
            if (currentZoom < 0.5) currentZoom = 0.5;
            if (currentZoom > 3) currentZoom = 3;
            document.getElementById('fbFullImage').style.transform = `scale(${currentZoom})`;
        }

        function toggleExpandMode() {
            const sidebar = document.getElementById('fbSidebar');
            sidebar.style.display = sidebar.style.display === 'none' ? 'flex' : 'none';
        }

        function closeFbModal() {
            document.getElementById('fbImageModal').style.display = 'none';
            document.getElementById('fbFullImage').src = '';
            document.getElementById('fbSidebarContent').innerHTML = ''; 
            document.body.style.overflow = 'auto'; 

            // Return to standard feed URL
            const url = new URL(window.location);
            url.searchParams.delete('photo');
            window.history.pushState({}, '', url);
        }
    </script>
</body>
</html>