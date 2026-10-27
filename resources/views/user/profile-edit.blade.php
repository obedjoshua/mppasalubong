<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Masaganang Pagpapala</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('css/welcome.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/loader.css') }}" as="style">
    <link rel="preload" href="{{ asset('css/dashboard.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" media="print" onload="this.media='all'">

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
    
    <div class="page-wrapper" id="page-wrapper" style="background-color: #FFFFFF;">
        @include('partials.header')

        <div class="dashboard-header-area">
            <h1 class="user-display-name" style="font-size: clamp(35px, 4vw, 50px);">Customize Profile</h1>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="profile-edit-grid">
                
                <!-- LEFT COLUMN: Live Preview & Frames -->
                <div class="profile-preview-card">
                    <div style="text-align: center; margin-bottom: 25px;">
                        
                        <div class="live-preview-wrapper" style="margin: 0 auto;">
                            <!-- PFP Layer -->
                            <img id="preview-avatar" src="{{ !empty($user->avatar) ? asset($user->avatar) : asset('images/default-avatar.jpeg') }}" class="live-preview-avatar" >
                            <!-- Frame Layer -->
                            <img id="preview-frame" src="{{ asset('images/frames/' . ($user->selected_frame ?? 'frame_reg01') . '.png') }}" class="live-preview-frame" >
                        </div>

                    </div>

                    <div class="form-divider" style="margin-bottom: 20px;"></div>

                    <div class="form-group-spaced" style="margin-bottom: 0;">
                        <label class="candy-section-label">Select Frame</label>
                        <div class="visual-picker" style="justify-content: center;">
                            <label class="visual-option">
                                <input type="radio" name="selected_frame" value="frame_reg01" class="frame-radio" data-src="{{ asset('images/frames/frame_reg01.png') }}" @checked($user->selected_frame == 'frame_reg01' || empty($user->selected_frame))>
                                <div class="visual-preview"><img src="{{ asset('images/frames/frame_reg01.png') }}"></div>
                            </label>
                            
                            <label class="visual-option">
                                <input type="radio" name="selected_frame" value="frame_reg02" class="frame-radio" data-src="{{ asset('images/frames/frame_reg02.png') }}" @checked($user->selected_frame == 'frame_reg02')>
                                <div class="visual-preview"><img src="{{ asset('images/frames/frame_reg02.png') }}"></div>
                            </label>

                            @if(Auth::user()->role === 'admin')
                                <label class="visual-option">
                                    <input type="radio" name="selected_frame" value="frame_exclusive" class="frame-radio" data-src="{{ asset('images/frames/frame_exclusive.png') }}" @checked($user->selected_frame == 'frame_exclusive')>
                                    <div class="visual-preview"><img src="{{ asset('images/frames/frame_exclusive.png') }}"></div>
                                </label>
                            @endif
                            
                            @if(in_array('frame_10', $unlockedFrames))
                                <label class="visual-option">
                                    <input type="radio" name="selected_frame" value="frame_10" class="frame-radio" data-src="{{ asset('images/frames/frame_10.png') }}" @checked($user->selected_frame == 'frame_10')>
                                    <div class="visual-preview"><img src="{{ asset('images/frames/frame_10.png') }}"></div>
                                </label>
                            @endif
                            
                            @if(in_array('frame_20', $unlockedFrames))
                                <label class="visual-option">
                                    <input type="radio" name="selected_frame" value="frame_20" class="frame-radio" data-src="{{ asset('images/frames/frame_20.png') }}" @checked($user->selected_frame == 'frame_20')>
                                    <div class="visual-preview"><img src="{{ asset('images/frames/frame_20.png') }}"></div>
                                </label>
                            @endif
                            
                            @if(in_array('frame_100', $unlockedFrames))
                                <label class="visual-option">
                                    <input type="radio" name="selected_frame" value="frame_100" class="frame-radio" data-src="{{ asset('images/frames/frame_100.png') }}" @checked($user->selected_frame == 'frame_100')>
                                    <div class="visual-preview"><img src="{{ asset('images/frames/frame_100.png') }}"></div>
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Name, Avatar, Note, Actions -->
                <div class="profile-form-card">
                    
                    <div class="form-group-spaced">
                        <label class="candy-section-label">Name</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group-spaced">
                        <label class="candy-section-label">Avatar</label>
                        <div class="visual-picker">
                            
                            <!-- 0. THE NEW "+" UPLOAD BUTTON -->
                            <label class="visual-option" title="Upload Custom Photo" style="cursor: pointer;">
                                <!-- The native file input is now hidden inside this label -->
                                <input type="file" name="avatar_file" id="custom-file-input" accept="image/png, image/jpeg, image/jpg" style="display: none;">
                                
                                <div class="visual-preview" style="background: #F9FAFB; border: 2px dashed #B584FF; display: flex; align-items: center; justify-content: center; color: #8a38f5;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 28px; height: 28px;">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </div>
                            </label>

                            @php
                                // Check if user has a custom upload (not a preset or default)
                                $isCustom = $user->avatar && $user->avatar !== '/images/avatars/avatar_001.jpg' && $user->avatar !== '/images/avatars/avatar_002.jpeg';
                            @endphp

                            <!-- 1. Custom Uploaded Photo (Shows if previously uploaded) -->
                            @if($isCustom)
                                <label class="visual-option" title="Your Uploaded Photo">
                                    <input type="radio" name="preset_avatar" value="keep_custom" class="avatar-radio" data-src="{{ asset($user->avatar) }}" checked>
                                    <div class="visual-preview">
                                        <img src="{{ asset($user->avatar) }}">
                                    </div>
                                </label>
                            @endif

                            <!-- 2. BRAND NEW Custom Upload (Hidden by default, shown by JS when a file is selected) -->
                            <label class="visual-option" id="new-upload-option" style="display: none;" title="New Upload">
                                <input type="radio" name="preset_avatar" value="new_upload" id="new-upload-radio" class="avatar-radio">
                                <div class="visual-preview">
                                    <img id="new-upload-img" src="">
                                </div>
                            </label>

                            <!-- 3. Default Silhouette -->
                            <label class="visual-option">
                                <input type="radio" name="preset_avatar" value="default" class="avatar-radio" data-src="{{ asset('images/default-avatar.jpeg') }}" @checked(!$user->avatar)>
                                <div class="visual-preview" style="background: #E5E7EB;">
                                    <img src="{{ asset('images/default-avatar.jpeg') }}">
                                </div>
                            </label>
                            
                            <!-- 4. Preset 1 (.jpg) -->
                            <label class="visual-option">
                                <input type="radio" name="preset_avatar" value="avatar_001.jpg" class="avatar-radio" data-src="{{ asset('images/avatars/avatar_001.jpg') }}" @checked($user->avatar === '/images/avatars/avatar_001.jpg')>
                                <div class="visual-preview">
                                    <img src="{{ asset('images/avatars/avatar_001.jpg') }}">
                                </div>
                            </label>
                            
                            <!-- 5. Preset 2 (.jpeg) -->
                            <label class="visual-option">
                                <input type="radio" name="preset_avatar" value="avatar_002.jpeg" class="avatar-radio" data-src="{{ asset('images/avatars/avatar_002.jpeg') }}" @checked($user->avatar === '/images/avatars/avatar_002.jpeg')>
                                <div class="visual-preview">
                                    <img src="{{ asset('images/avatars/avatar_002.jpeg') }}">
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-spaced">
                        <label class="candy-section-label">Note / Status</label>
                        <input type="text" class="form-input" placeholder="Sweet tooth on a mission!" disabled style="background: #F9FAFB; color: #9CA3AF;">
                    </div>

                    <div class="action-row">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <a href="{{ route('dashboard') }}" class="cancel-link">Cancel</a>
                    </div>

                </div>

            </div>
        </form>
    </div>

    @include('partials.side-menu')
    <script src="{{ asset('js/welcome.js') }}"></script>
    <script>
        const previewAvatar = document.getElementById('preview-avatar');
        const previewFrame = document.getElementById('preview-frame');
        
        const avatarRadios = document.querySelectorAll('.avatar-radio');
        const frameRadios = document.querySelectorAll('.frame-radio');
        const customFileInput = document.getElementById('custom-file-input');

        // Allow clicking between radios to update the preview
        avatarRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if(this.checked) {
                    previewAvatar.src = this.getAttribute('data-src');
                    // We DO NOT clear the file input anymore, so the upload stays!
                }
            });
        });

        frameRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if(this.checked) {
                    previewFrame.src = this.getAttribute('data-src');
                }
            });
        });

        // When a user selects a file, display it in the grid as a new option
        customFileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const newUploadOption = document.getElementById('new-upload-option');
                    const newUploadImg = document.getElementById('new-upload-img');
                    const newUploadRadio = document.getElementById('new-upload-radio');
                    
                    // 1. Unhide the new upload box in the visual grid
                    newUploadOption.style.display = 'block';
                    
                    // 2. Set the image sources
                    newUploadImg.src = e.target.result;
                    newUploadRadio.setAttribute('data-src', e.target.result);
                    previewAvatar.src = e.target.result;
                    
                    // 3. Automatically check the new radio button
                    newUploadRadio.checked = true;
                }
                reader.readAsDataURL(file);
            }
        });

        window.addEventListener('pageshow', function (event) {
            const menuToggle = document.getElementById('menu-toggle');
            if (menuToggle) menuToggle.checked = false;
        });
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("globalLoader");
            if(loader) { setTimeout(() => { loader.classList.add("fade-out"); setTimeout(() => loader.remove(), 600); }, 500); }
        });
    </script>
</body>
</html>