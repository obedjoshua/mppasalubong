@extends('layouts.app')

@section('title', 'Payment Cancelled | Masaganang Pagpapala')

@push('styles')
    <style>
        .cancel-page { max-width: 600px; margin: 80px auto; padding: 40px clamp(20px, 4vw, 80px); font-family: "Manrope", sans-serif; text-align: center; }
        .cancel-icon { width: 120px; height: 120px; margin: 0 auto 32px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 30px rgba(245, 158, 11, 0.3); }
        .cancel-icon svg { width: 60px; height: 60px; color: white; }
        .cancel-page h1 { font-family: "modern-bold", sans-serif; font-size: clamp(32px, 5vw, 48px); color: var(--purple-dark); margin: 0 0 12px; }
        .cancel-page .subtitle { font-size: 18px; color: #666; margin-bottom: 40px; }
        .cancel-message { background: #fef3c7; border: 2px solid #fde68a; border-radius: 16px; padding: 24px; margin-bottom: 32px; text-align: left; }
        .cancel-message p { margin: 0 0 12px; color: #92400e; }
        .cancel-message p:last-child { margin-bottom: 0; }
        .action-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { background: var(--purple-main); color: white; border: none; border-radius: 12px; padding: 16px 32px; font-family: "modern-bold", sans-serif; font-size: 16px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 0 var(--purple-dark); transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background: var(--purple-dark); transform: translateY(-2px); box-shadow: 0 6px 0 var(--purple-dark); text-decoration: none; color: white; }
        .btn-secondary { background: white; color: var(--purple-main); border: 2px solid var(--purple-main); border-radius: 12px; padding: 16px 32px; font-family: "modern-bold", sans-serif; font-size: 16px; font-weight: 800; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-secondary:hover { background: var(--purple-main); color: white; text-decoration: none; }
    </style>
@endpush

@section('content')
    <div class="cancel-page">
        <div class="cancel-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>

        <h1>Payment Cancelled</h1>
        <p class="subtitle">Your payment was not completed. Your cart is still saved.</p>

        <div class="cancel-message">
            <p><strong>What happened?</strong> You were redirected back from the payment page without completing the transaction.</p>
            <p>Your cart items have been preserved. You can try checking out again or continue shopping.</p>
        </div>

        <div class="action-buttons">
            <a href="{{ route('checkout') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <path d="M12 2v4"></path>
                    <path d="M12 18v4"></path>
                    <path d="M4.93 4.93l2.83 2.83"></path>
                    <path d="M16.24 16.24l2.83 2.83"></path>
                    <path d="M2 12h4"></path>
                    <path d="M18 12h4"></path>
                    <path d="M4.93 19.07l2.83-2.83"></path>
                    <path d="M16.24 7.76l2.83-2.83"></path>
                </svg>
                Try Checkout Again
            </a>
            <a href="{{ route('cart') }}" class="btn-secondary">View Cart</a>
        </div>
    </div>
@endsection
