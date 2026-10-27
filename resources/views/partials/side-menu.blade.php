<nav class="side-menu-reveal" id="side-menu">
    <label for="menu-toggle" class="close-btn" style="cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </label>
    
    <div class="menu-card-box">
        <div class="side-menu-links">
            <!-- Dynamic order link based on authentication status -->
            @auth
                <a href="{{ route('orders.index') }}" wire:navigate>order</a>
            @else
                <a href="{{ route('guest.order.prompt') }}" wire:navigate>order</a>
            @endauth
            
            <a href="{{ route('shop') }}" wire:navigate>shop</a>
            <a href="#">about us</a>
            <a href="#">join us</a>
            <a href="{{ route('community.index') }}" wire:navigate>community</a>
        </div>
    </div>
    
    @auth
        @php
            $orderCount = Auth::user()->orders()->count();
            $frameClass = 'frame_reg01'; // Default starting frame
            
            // Check for exclusive account frame first
            if (Auth::user()->role === 'admin' && Auth::user()->selected_frame === 'frame_exclusive') {
                $frameClass = 'frame_exclusive';
            } elseif ($orderCount >= 100) {
                $frameClass = 'frame_100';
            } elseif ($orderCount >= 20) {
                $frameClass = 'frame_20';
            } elseif ($orderCount >= 10) {
                $frameClass = 'frame_10';
            }
        @endphp

        <div style="display: flex; gap: 6px; width: 100%; max-width: 400px; margin-bottom: -5px; box-sizing: border-box;">
            <a href="{{ route('dashboard') }}" wire:navigate class="menu-card-box profile-preview-card" style="text-decoration: none; flex: 1; min-width: 0; padding: 0; margin: 0; box-sizing: border-box;">
                <div class="profile-preview-inner" style="display: flex; align-items: center; gap: 10px; padding: 12px; height: 100%; box-sizing: border-box;">
                    <div style="position: relative; width: 38px; height: 38px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ !empty(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('images/default-avatar.jpeg') }}" alt="Profile" style="width: 30px; height: 30px; border-radius: 25%; object-fit: cover; z-index: 1;">
                        <img src="{{ asset('images/frames/' . (Auth::user()->selected_frame ?: $frameClass) . '.png') }}" alt="Frame" style="position: absolute; top: 0; left: 0; width: 38px; height: 38px; z-index: 2; pointer-events: none; object-fit: contain;">   
                        @if(!Auth::user()->avatar)
                            <div style="position: absolute; bottom: 2px; right: -2px; width: 12px; height: 12px; background-color: #8a38f5; border: 2px solid white; border-radius: 50%; z-index: 3; display: flex; align-items: center; justify-content: center; color: white;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="width: 8px; height: 8px;">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="profile-info" style="flex: 1; min-width: 0; overflow: hidden;">
                        <span class="profile-name" style="font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">{{ Auth::user()->name }}</span>
                        <span class="profile-action" style="font-size: 10px; white-space: nowrap;">View Dashboard &rarr;</span>
                    </div>
                </div>
            </a>

            <!-- Settings Button (Fixed compact size) -->
            <a href="{{ route('settings.index') }}" wire:navigate class="menu-card-box profile-preview-card" title="Settings" style="text-decoration: none; flex: 0 0 52px; width: 52px; padding: 0; margin: 0; box-sizing: border-box; border-radius: 12px !important;">
                <div class="profile-preview-inner" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 0; border-radius: 12px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px; color: var(--text-black);">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l-.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
            </a>
        </div>
        
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
            <a href="{{ route('login') }}" wire:navigate>log in</a>
        </div>
    @endauth
</nav>
