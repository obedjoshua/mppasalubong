@extends('layouts.app')

@section('title', 'Order Confirmed | Masaganang Pagpapala')

@push('styles')
    <style>
        .success-page { max-width: 600px; margin: 80px auto; padding: 40px clamp(20px, 4vw, 80px); font-family: "Manrope", sans-serif; text-align: center; }
        .success-icon { width: 120px; height: 120px; margin: 0 auto 32px; background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 30px rgba(34, 197, 94, 0.3); }
        .success-icon svg { width: 60px; height: 60px; color: white; }
        .success-page h1 { font-family: "modern-bold", sans-serif; font-size: clamp(32px, 5vw, 48px); color: var(--purple-dark); margin: 0 0 12px; }
        .success-page .subtitle { font-size: 18px; color: #666; margin-bottom: 40px; }
        .order-details { background: #fafafa; border: 2px solid #e8e8e8; border-radius: 16px; padding: 24px; margin-bottom: 32px; text-align: left; }
        .order-details h2 { font-family: "modern-bold", sans-serif; font-size: 20px; color: var(--purple-dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e0e0e0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #666; }
        .detail-value { font-weight: 700; color: #333; }
        .detail-value.amount { color: var(--purple-dark); font-size: 18px; }
        .action-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .btn-primary { background: var(--purple-main); color: white; border: none; border-radius: 12px; padding: 16px 32px; font-family: "modern-bold", sans-serif; font-size: 16px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 0 var(--purple-dark); transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background: var(--purple-dark); transform: translateY(-2px); box-shadow: 0 6px 0 var(--purple-dark); text-decoration: none; color: white; }
        .btn-secondary { background: white; color: var(--purple-main); border: 2px solid var(--purple-main); border-radius: 12px; padding: 16px 32px; font-family: "modern-bold", sans-serif; font-size: 16px; font-weight: 800; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-secondary:hover { background: var(--purple-main); color: white; text-decoration: none; }
        .order-items { margin-top: 24px; padding-top: 24px; border-top: 2px solid #e0e0e0; }
        .order-items h3 { font-family: "modern-bold", sans-serif; font-size: 16px; color: var(--purple-dark); margin: 0 0 16px; }
        .order-item { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .order-item-name { color: #333; }
        .order-item-qty { color: #888; }
        .order-item-total { font-weight: 700; color: var(--purple-dark); }
    </style>
@endpush

@section('content')
    <div class="success-page">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>

        <h1>Order Confirmed!</h1>
        <p class="subtitle">Thank you for your order. We\'ll prepare it for pickup.</p>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="detail-row">
                <span class="detail-label">Order Number</span>
                <span class="detail-value">#{{ $purchase->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($purchase->created_at)->format('M d, Y h:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pickup Branch</span>
                <span class="detail-value">{{ $pendingCheckout["customer_data"]["pickup_branch"] === "main_shop" ? "Main Branch" : "San Jose Branch" }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pickup Date</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($pendingCheckout["customer_data"]["pickup_date"])->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pickup Time</span>
                <span class="detail-value">{{ $pendingCheckout["customer_data"]["pickup_time"] }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Paid</span>
                <span class="detail-value amount">₱{{ number_format((float) $purchase->total_price, 2) }}</span>
            </div>

            <div class="order-items">
                <h3>Items Ordered</h3>
                @foreach($pendingCheckout["items"] as $item)
                    <div class="order-item">
                        <div>
                            <div class="order-item-name">{{ $item["name"] }}</div>
                            <div class="order-item-qty">Qty: {{ $item["quantity"] }} × ₱{{ number_format((float) $item["price"], 2) }}</div>
                        </div>
                        <span class="order-item-total">₱{{ number_format((float) ($item["price"] * $item["quantity"]), 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('orders.index') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                View My Orders
            </a>
            <a href="{{ route('shop') }}" class="btn-secondary">Continue Shopping</a>
        </div>
    </div>
@endsection
