<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Challenge</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* (Insert ALL the exact same CSS from verify-email.blade.php above) */
        body { margin: 0; padding: 0; background-color: #803df5; font-family: 'Manrope', sans-serif; color: #FFFFFF; height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        .back-btn { position: absolute; top: 40px; left: 40px; background: #FFFFFF; width: 48px; height: 48px; border-radius: 10px; border: 3px solid #000000; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; }
        .back-btn svg { width: 24px; height: 24px; stroke: #000000; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; }
        .mascot-img { position: absolute; bottom: -20px; left: -20px; width: clamp(280px, 35vw, 420px); z-index: 10; pointer-events: none; }
        .content-box { text-align: center; z-index: 20; max-width: 500px; margin-top: -40px; }
        .brand-logo { width: 120px; margin-bottom: 40px; }
        h1 { font-size: 32px; font-weight: 800; margin: 0 0 16px 0; letter-spacing: -0.5px; }
        p.subtitle { font-size: 14px; font-weight: 500; line-height: 1.5; margin: 0 auto 30px auto; opacity: 0.95; }
        .btn-gold { background-color: #D39F24; color: #FFFFFF; font-weight: 800; font-size: 11px; padding: 16px 54px; border: none; border-radius: 50px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; transition: transform 0.1s; margin-bottom: 40px; }
        
        /* New Input Styling for Challenge */
        .code-input {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            letter-spacing: 8px;
            padding: 12px;
            border-radius: 12px;
            width: 200px;
            margin-bottom: 24px;
            outline: none;
        }
        .code-input::placeholder { color: rgba(255,255,255,0.4); }
        .code-input:focus { border-color: #FFFFFF; }
        
        .bottom-text { font-size: 13px; font-weight: 500; line-height: 1.6; }
        .bottom-text form { display: inline; }
        .bottom-text button { background: none; border: none; color: #FFFFFF; font-weight: 800; text-decoration: underline; cursor: pointer; font-family: inherit; font-size: inherit; padding: 0; }
        
        .error-msg { background-color: #FEF2F2; color: #991B1B; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 700; margin-bottom: 20px; border: 1px solid #FCA5A5;}
    </style>
</head>
<body>

    <button class="back-btn" onclick="document.getElementById('logout-form').submit();">
        <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    </button>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <img src="{{ asset('images/v600_2.png') }}" alt="Mascot" class="mascot-img">

    <div class="content-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">

        <h1>Security Challenge</h1>
        
        <p class="subtitle">
            We noticed unusual login activity on your account.<br>
            Please enter the 6-digit code we just sent to your<br>
            email address to continue.
        </p>

        @if ($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('challenge.verify') }}">
            @csrf
            <input type="text" name="code" class="code-input" placeholder="000000" maxlength="6" required autocomplete="off">
            <br>
            <button type="submit" class="btn-gold">VERIFY CODE</button>
        </form>

        <div class="bottom-text">
            <form method="POST" action="{{ route('challenge.resend') }}">
                @csrf
                <button type="submit">Click here</button>
            </form>
            to resend the security code to your email
        </div>
    </div>

</body>
</html>