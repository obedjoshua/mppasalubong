"<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <title>Log In | Masaganang Pagpapala Pasalubong</title>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('css/login.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">

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

    <button class="back-btn" id="backBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
            <line x1="20" y1="12" x2="4" y2="12"></line>
            <polyline points="10 18 4 12 10 6"></polyline>
        </svg>
    </button>

    <div class="login-wrapper">
        <div class="illustration-side">
            <img src="{{ asset('images/v600_1.png') }}" alt="Mascots and Logo Structure" class="mascot-img" >
        </div>

        <div class="form-side">
            <div class="form-container">
                <div class="brand-header">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-icon" >
                    <div class="brand-text">
                        <span>MASAGANANG</span>
                        <span>PAGPAPALA</span>
                        <span>PASALUBONG</span>
                    </div>
                </div>

                <h1 class="login-title">Log in</h1>

                @if ($errors->any())
                    <div style="background-color: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 700;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="login-form">
                    @csrf
                    <input type="email" name="email" class="input-field" placeholder="Email" value="{{ old('email') }}" required>

                    <div class="password-wrapper">
                        <input type="password" name="password" class="input-field" placeholder="Password" required>
                        <a href="#" class="forgot-link">FORGOT?</a>
                    </div>

                    <button type="submit" class="primary-btn">LOG IN</button>
                </form>

                <div class="divider">
                    <span>OR</span>
                </div>

                <div class="social-login">
                    <a href="{{ route('auth.facebook') }}" class="social-btn" style="text-decoration: none;">
                        <svg viewBox="0 0 48 48" width="18" height="18">
                            <defs>
                                <linearGradient id="fb-grad" x1="50%" y1="0%" x2="50%" y2="100%">
                                    <stop offset="0%" stop-color="#18acfe" />
                                    <stop offset="100%" stop-color="#0163e0" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#fb-grad)" d="M24 4C12.954 4 4 12.954 4 24c0 10.028 7.378 18.337 17.062 19.789v-13.68h-4.856V24h4.856v-4.498c0-5.112 3.036-7.859 7.643-7.859 2.228 0 4.551.398 4.551.398v5h-2.564c-2.525 0-3.313 1.568-3.313 3.176V24h5.672l-.907 6.109h-4.765v13.68C36.622 42.337 44 34.028 44 24 44 12.954 35.046 4 24 4z"/>
                        </svg>
                        FACEBOOK
                    </a>

                    <a href="{{ route('auth.google') }}" class="social-btn" style="text-decoration: none;">
                        <svg viewBox="0 0 48 48" width="18" height="18">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        GOOGLE
                    </a>
                </div>
                
                <div class="signup-prompt">
                    <p>Don't have an account yet?</p>
                    <a href="{{ route('register') }}" class="signup-btn">SIGN UP</a>
                </div>

                <div class="legal-text">
                    By signing in to Masaganang Pagpapala, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.<br><br>
                    This site is protected by reCAPTCHA Enterprise and the Google <a href="#">Privacy Policy</a> and <a href="#">Terms of Service</a> apply.
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const backButton = document.getElementById("backBtn");
            if (backButton) {
                backButton.addEventListener("click", function() {
                    window.history.back();
                });
            }

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