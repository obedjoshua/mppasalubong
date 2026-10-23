<header class="header">
    <a href="{{ route('home') }}" wire:navigate class="header-left" style="text-decoration: none; color: inherit;">
        <img src="{{ asset('images/logo.png') }}" alt="Masaganang Pagpapala Logo" class="logo-img">
        <div class="logo-text-stacked">
            <span>MASAGANANG</span>
            <span>PAGPAPALA</span>
            <span>PASALUBONG</span>
        </div>
    </a>
    
    <div class="header-controls">
        <label for="menu-toggle" class="icon-btn" style="cursor: pointer;" title="Menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width: 65%; height: 65%; fill: currentColor;">
                <path d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/>
            </svg>
        </label>

        @auth
            <a href="{{ route('wishlist.index') }}" wire:navigate class="icon-btn" title="Wishlist" style="text-decoration: none; color: inherit;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 65%; height: 65%;">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </a>

            <a href="{{ route('notifications.index') }}" wire:navigate class="icon-btn" title="Notifications" style="text-decoration: none; color: inherit;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 65%; height: 65%;">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </a>
        @endauth

        <div class="icon-btn" title="Cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 65%; height: 65%;">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
        </div>
        
        <button class="contact-btn">
            CONTACT US
            <svg viewBox="0 0 77 77" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; flex-shrink: 0;">
                <path d="M74.5 2L34.625 41.875M34.625 41.875L2 27.375L74.5 2L49.125 74.5L34.625 41.875Z" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>       
</header>

@auth
    @if(!Auth::user()->hasVerifiedEmail())
        <div style="background-color: #FEF2F2; border-bottom: 2px solid #FCA5A5; padding: 12px 24px; text-align: center; color: #991B1B; font-family: 'Manrope', sans-serif; font-weight: 700; font-size: 0.875rem;">
            Please verify your email address to unlock all account features.
            
            <a href="{{ route('verification.notice') }}" wire:navigate style="color: #7F1D1D; text-decoration: underline; font-weight: 800; margin-left: 8px;">
                Resend verification email
            </a>
        </div>
    @endif
@endauth
