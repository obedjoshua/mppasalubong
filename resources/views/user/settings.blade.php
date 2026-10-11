<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Masaganang Pagpapala</title>
    <link href="https://fonts.googleapis.com/css?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <style>
        .setting-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 2px dashed #E5E7EB;
        }
        .setting-row:last-child {
            border-bottom: none;
        }
        .setting-label-text {
            font-family: 'modern-bold', sans-serif;
            font-size: 16px;
            color: #1F2937;
        }
        .setting-sub-text {
            font-family: 'Manrope', sans-serif;
            font-size: 13px;
            color: #6B7280;
            font-weight: 500;
            margin-top: 4px;
        }
        
        .candy-toggle {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 30px;
        }
        .candy-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #E5E7EB;
            border-radius: 30px;
            transition: .3s;
            box-shadow: inset 0px 3px 5px rgba(0,0,0,0.1);
            border: 2px solid #D1D5DB;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: .3s;
            box-shadow: 0px 4px 0px #9CA3AF;
        }
        input:checked + .toggle-slider {
            background-color: #10B981;
            border-color: #059669;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
            box-shadow: 0px 4px 0px #059669;
        }
    </style>
</head>
<body>
    @include('partials.loader')
    <input type="checkbox" id="menu-toggle" style="display: none;" autocomplete="off">
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')
        
        <div class="dashboard-header-area">
            <h1 class="user-display-name" style="font-size: clamp(35px, 4vw, 50px);">Settings</h1>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            
            <div class="profile-edit-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
                
                <!-- LEFT COLUMN -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- 1. Profile Information (Read-Only & Redirect) -->
                    <div class="profile-form-card">
                        <label class="candy-section-label">Profile Information</label>
                        
                        <div class="form-group-spaced" style="margin-bottom: 20px;">
                            <label style="font-family: 'modern-bold', sans-serif; font-size: 14px; color: #4B5563; display: block; margin-bottom: 5px;">Registered Email</label>
                            <input type="email" class="form-input" value="{{ Auth::user()->email }}" readonly style="background-color: #F3F4F6; color: #6B7280; cursor: not-allowed; opacity: 0.8;">
                        </div>
                        
                        <!-- Redirect Button to Edit Profile -->
                        <a href="{{ route('profile.edit') }}" style="display: block; width: 100%; text-align: center; text-decoration: none; background: #8a38f5; color: white; border: 3px solid #610DCF; border-radius: 50px; padding: 12px; font-family: 'modern-bold', sans-serif; font-size: 16px; box-shadow: 0px 5px 0px #610DCF; transition: transform 0.1s, box-shadow 0.1s;">
                            Edit Profile Details
                        </a>
                    </div>

                    <!-- 2. Security & Authentication -->
                    <div class="profile-form-card">
                        <label class="candy-section-label">Security</label>
                        <div class="form-group-spaced" style="margin-bottom: 15px;">
                            <input type="password" name="current_password" class="form-input" placeholder="Current Password">
                        </div>
                        <div class="form-group-spaced" style="margin-bottom: 15px;">
                            <input type="password" name="new_password" class="form-input" placeholder="New Password">
                        </div>
                        
                        <div class="form-divider" style="margin: 20px 0; border-top: 2px solid #F3F4F6;"></div>
                        
                        <!-- 2FA Button Logic -->
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">Two-Factor Authentication</div>
                                <div class="setting-sub-text">Require an extra step on login</div>
                            </div>
                            <div>
                                @if(Auth::user()->two_factor_auth)
                                    <form action="{{ route('2fa.disable') }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="this.closest('form').submit()" class="save-btn" style="background: #EF4444; border-color: #991B1B; box-shadow: 0px 5px 0px #991B1B; padding: 8px 16px; font-size: 14px;">DISABLE 2FA</button>
                                    </form>
                                @else
                                    <a href="{{ route('2fa.enable') }}" class="save-btn" style="text-decoration: none; display: inline-block; padding: 8px 16px; font-size: 14px; background: #10B981; border-color: #059669; box-shadow: 0px 5px 0px #059669;">ENABLE 2FA</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- 3. Preferences & Appearance -->
                    <div class="profile-form-card">
                        <label class="candy-section-label">Appearance & Region</label>
                        <div class="form-group-spaced" style="margin-bottom: 15px;">
                            <select name="theme" class="form-input">
                                <option value="light" {{ Auth::user()->theme === 'light' ? 'selected' : '' }}>Light Theme</option>
                                <option value="dark" {{ Auth::user()->theme === 'dark' ? 'selected' : '' }}>Dark Theme</option>
                                <option value="system" {{ Auth::user()->theme === 'system' ? 'selected' : '' }}>Match System</option>
                            </select>
                        </div>
                        <div class="form-group-spaced">
                            <select name="timezone" class="form-input">
                                <option value="Asia/Manila" {{ Auth::user()->timezone === 'Asia/Manila' ? 'selected' : '' }}>Asia / Manila (PST)</option>
                                <option value="America/New_York" {{ Auth::user()->timezone === 'America/New_York' ? 'selected' : '' }}>America / New York (EST)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- 4. Notifications & Marketing -->
                    <div class="profile-form-card">
                        <label class="candy-section-label">Notifications</label>
                        
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">Email Alerts</div>
                                <div class="setting-sub-text">Order receipts and account updates</div>
                            </div>
                            <label class="candy-toggle">
                                <input type="checkbox" name="notify_email" {{ Auth::user()->notify_email ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">Push Notifications</div>
                                <div class="setting-sub-text">Live updates in your browser/app</div>
                            </div>
                            <label class="candy-toggle">
                                <input type="checkbox" name="notify_push" {{ Auth::user()->notify_push ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">SMS Alerts</div>
                                <div class="setting-sub-text">Text messages for delivery tracking</div>
                            </div>
                            <label class="candy-toggle">
                                <input type="checkbox" name="notify_sms" {{ Auth::user()->notify_sms ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="form-divider" style="margin: 20px 0; border-top: 2px solid #F3F4F6;"></div>

                        <label class="candy-section-label" style="font-size: 16px;">Privacy & Marketing</label>
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">Marketing Emails</div>
                                <div class="setting-sub-text">Receive special offers and promos</div>
                            </div>
                            <label class="candy-toggle">
                                <input type="checkbox" name="marketing_emails" {{ Auth::user()->marketing_emails ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div class="setting-row">
                            <div>
                                <div class="setting-label-text">Data Sharing</div>
                                <div class="setting-sub-text">Allow analytics for a better experience</div>
                            </div>
                            <label class="candy-toggle">
                                <input type="checkbox" name="data_sharing" {{ Auth::user()->data_sharing ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Save Action Button -->
                    <div class="action-row" style="padding-top: 10px;">
                        <button type="submit" class="save-btn" style="width: 100%;">Save Settings</button>
                    </div>

                    <!-- 5. Destructive Actions -->
                    <div class="profile-form-card" style="border-color: #FCA5A5; background: #FEF2F2; margin-top: 20px;">
                        <label class="candy-section-label" style="color: #DC2626;">Danger Zone</label>
                        <p style="font-family: 'Manrope', sans-serif; font-size: 13px; color: #991B1B; margin-bottom: 15px;">
                            Once you delete your account, there is no going back. Please be certain.
                        </p>
                        <button type="button" onclick="confirmDelete()" style="width: 100%; background: #EF4444; color: white; border: 3px solid #991B1B; border-radius: 50px; padding: 12px; font-family: 'modern-bold', sans-serif; font-size: 16px; cursor: pointer; box-shadow: 0px 5px 0px #991B1B; transition: transform 0.1s, box-shadow 0.1s;">
                            Delete Account
                        </button>
                    </div>

                </div>
            </div>
        </form>

        <!-- Form specifically for deleting the account -->
        <form id="delete-account-form" action="{{ route('settings.destroy') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    @include('partials.side-menu')
    
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        // Trigger deletion confirmation
        function confirmDelete() {
            if(confirm("Are you absolutely sure you want to delete your account? This action cannot be undone.")) {
                document.getElementById('delete-account-form').submit();
            }
        }

        // Universal loaders and toggle reset
        window.addEventListener('pageshow', function (event) {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;
        });

        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("globalLoader");
            if (loader) { 
                setTimeout(() => { 
                    loader.classList.add("fade-out");
                    setTimeout(() => loader.remove(), 600); 
                }, 500); 
            }
        });
    </script>
</body>
</html>