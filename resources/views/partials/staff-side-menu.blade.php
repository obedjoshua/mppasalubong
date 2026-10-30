<nav class="side-menu-reveal" id="side-menu">
    <!-- Close Menu Toggle Button -->
    <label for="menu-toggle" class="close-btn" style="cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </label>
    
    <!-- Staff Operations Navigation Links -->
    <div class="menu-card-box">
        <div class="side-menu-links">
            @if(in_array(Auth::user()->role, ['admin', 'developer'], true))
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('developer.dashboard') }}">overview</a>
                <a href="{{ route('admin.users.index') }}">users & roles</a>
                <a href="{{ route('admin.store.index') }}">shop content</a>
                <a href="{{ route('developer.api-requests.index') }}">api requests</a>
                <a href="{{ route('developer.posts.index') }}">post review</a>
                <a href="{{ route('staff.dashboard') }}">staff operations</a>
            @else
                <a href="{{ route('staff.dashboard') }}">dashboard</a>
                <a href="{{ route('staff.qr.index') }}">qr scanner</a>
                <a href="{{ route('staff.walkin.create') }}">walk-in ocr</a>
                <a href="{{ route('staff.schedule.index') }}">production schedule</a>
                <a href="{{ route('staff.transfers.index') }}">stock transfers & chat</a>
            @endif
        </div>
    </div>
    
    @auth
        <!-- Staff Identity Info & Quick Lock Screen -->
        <div style="display: flex; gap: 6px; width: 100%; max-width: 400px; margin-bottom: -5px; box-sizing: border-box;">
            
            <!-- Staff Identity Card (No customer profile link) -->
            <div class="menu-card-box profile-preview-card" style="flex: 1; min-width: 0; padding: 0; margin: 0; box-sizing: border-box;">
                <div class="profile-preview-inner" style="display: flex; align-items: center; gap: 10px; padding: 12px; height: 100%; box-sizing: border-box;">
                    <div style="position: relative; width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ !empty(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('images/default-avatar.jpeg') }}" alt="Staff Avatar" style="width: 32px; height: 32px; border-radius: 25%; object-fit: cover;">
                        <div style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; background-color: #22c55e; border: 2px solid white; border-radius: 50%;" title="Staff Active"></div>
                    </div>
                    <div class="profile-info" style="flex: 1; min-width: 0; overflow: hidden;">
                        <span class="profile-name" style="font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; font-weight: bold;">{{ Auth::user()->name }}</span>
                        <span class="profile-action" style="font-size: 10px; white-space: nowrap; color: #6366f1;">{{ ucfirst(Auth::user()->role) }} Portal</span>
                    </div>
                </div>
            </div>

            <!-- Lock Terminal Button (Locks High-Privilege Session) -->
            @if(Auth::user()->role === 'staff')
                <form action="{{ route('staff.session.lock') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="menu-card-box profile-preview-card" title="Lock Staff Terminal" style="flex: 0 0 52px; width: 52px; height: 100%; padding: 0; margin: 0; box-sizing: border-box; border-radius: 12px !important; border: none; cursor: pointer; background: transparent;">
                        <div class="profile-preview-inner" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 0; border-radius: 12px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px; color: var(--text-black);">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                    </button>
                </form>
            @endif

            <!-- Settings Button -->
            <a href="{{ route('staff.settings') }}" class="menu-card-box profile-preview-card" title="Staff Settings" style="text-decoration: none; flex: 0 0 52px; width: 52px; padding: 0; margin: 0; box-sizing: border-box; border-radius: 12px !important;">
                <div class="profile-preview-inner" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 0; border-radius: 12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px; color: var(--text-black);">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
            </a>
        </div>
        
        <!-- Standard 3D Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="logout-form-wrapper">
            @csrf
            <button type="submit" class="logout-3d-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>log out</span>
            </button>
        </form>
    @else
        <div class="menu-card-box login-box">
            <a href="{{ route('login') }}">staff log in</a>
        </div>
    @endauth
</nav>