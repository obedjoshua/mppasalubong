<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #803df5; /* Exact reference purple */
            font-family: 'Manrope', sans-serif;
            color: #FFFFFF;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* 1:1 Match: White box, black border, thick black arrow */
        .back-btn {
            position: absolute;
            top: 40px;
            left: 40px;
            background: #FFFFFF;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            border: 3px solid #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: transform 0.1s;
        }
        .back-btn:active {
            transform: scale(0.95);
        }
        .back-btn svg {
            width: 24px;
            height: 24px;
            stroke: #000000;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Mascot positioned at bottom left */
        .mascot-img {
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: clamp(280px, 35vw, 420px);
            z-index: 10;
            pointer-events: none;
        }

        .content-box {
            text-align: center;
            z-index: 20;
            max-width: 500px;
            margin-top: -40px;
        }

        .brand-logo {
            width: 120px;
            margin-bottom: 40px;
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
        }

        p.subtitle {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.5;
            margin: 0 auto 40px auto;
            opacity: 0.95;
        }

        /* 1:1 Match: Gold Pill Button */
        .btn-gold {
            background-color: #D39F24;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 11px;
            padding: 16px 54px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.1s;
            margin-bottom: 40px;
        }
        .btn-gold:active {
            transform: translateY(2px);
        }

        .bottom-text {
            font-size: 13px;
            font-weight: 500;
            line-height: 1.6;
        }

        .bottom-text form {
            display: inline;
        }

        .bottom-text button {
            background: none;
            border: none;
            color: #FFFFFF;
            font-weight: 800;
            text-decoration: underline;
            cursor: pointer;
            font-family: inherit;
            font-size: inherit;
            padding: 0;
        }
    </style>
</head>
<body>

    <a href="{{ route('login') }}" class="back-btn" style="text-decoration: none;">
        <svg viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
    </a>

    <img src="{{ asset('images/v600_2.png') }}" alt="Mascot" class="mascot-img">

    <div class="content-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="brand-logo">

        <h1>Verify your email</h1>
        
        <p class="subtitle">
            Thanks for helping us keep your account secure.<br>
            Click the button below to finish verifying your<br>
            email address
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-gold">CONFIRM EMAIL</button>
        </form>

        <div class="bottom-text">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit">Click here</button>
            </form>
            if you didnt receive an email<br>
            or would like to change the email address
        </div>
    </div>

</body>
</html>