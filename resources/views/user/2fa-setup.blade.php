<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Up 2FA</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body style="background-color: var(--purple-dark); background-image: radial-gradient(var(--purple-main) 20%, transparent 20%), radial-gradient(var(--purple-main) 20%, transparent 20%); background-size: 40px 40px; background-position: 0 0, 20px 20px; font-family: 'Manrope', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; overflow: hidden;">
    
    <div class="profile-form-card" style="max-width: 500px; text-align: center;">
        <h2 style="font-family: 'modern-bold', sans-serif; color: #8a38f5;">Set Up Two-Factor Auth</h2>
        <p style="font-family: 'Manrope', sans-serif; margin-bottom: 20px;">
            1. Scan this QR code with your Google Authenticator or Authy app.
        </p>
        
        <!-- Displays the SVG QR Code -->
        <div style="background: white; padding: 10px; border-radius: 10px; display: inline-block; margin-bottom: 20px;">
            {!! $qrCodeSvg !!}
        </div>

        <p style="font-family: 'Manrope', sans-serif; margin-bottom: 20px;">
            2. Enter the 6-digit pin from the app to verify.
        </p>

        <form action="{{ route('2fa.verify') }}" method="POST">
            @csrf
            <div class="form-group-spaced" style="margin-bottom: 15px;">
                <input type="text" name="code" class="form-input" placeholder="e.g. 123456" required style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                @error('code')
                    <span style="color: red; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="save-btn" style="width: 100%;">Verify & Enable</button>
            <a href="{{ route('settings.index') }}" class="cancel-link" style="display: block; margin-top: 15px;">Cancel</a>
        </form>
    </div>

</body>
</html>