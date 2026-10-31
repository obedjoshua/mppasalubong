@extends('layouts.app')

@section('title', 'Shopping Cart | Masaganang Pagpapala')

@push('styles')
    <style>
        .cart-page { max-width: 1200px; margin: 0 auto; padding: 40px clamp(20px, 4vw, 80px); font-family: "Manrope", sans-serif; }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 20px; border-bottom: 2px solid #eee; }
        .cart-header h1 { font-family: "modern-bold", sans-serif; font-size: clamp(28px, 4vw, 42px); color: var(--purple-dark); margin: 0; }
        .continue-shopping { color: var(--purple-main); text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .continue-shopping:hover { text-decoration: underline; }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        .cart-table th { text-align: left; padding: 16px 12px; font-family: "modern-bold", sans-serif; font-size: 14px; text-transform: uppercase; color: #666; border-bottom: 2px solid #e0e0e0; }
        .cart-table td { padding: 20px 12px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
        .cart-table tr:last-child td { border-bottom: none; }
        .product-cell { display: flex; align-items: center; gap: 16px; }
        .product-image { width: 80px; height: 80px; object-fit: cover; border-radius: 12px; background: #f5f5f5; }
        .product-info h3 { margin: 0 0 4px; font-size: 16px; font-weight: 700; color: #333; }
        .product-info .category { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; }
        .quantity-selector { display: flex; align-items: center; gap: 8px; }
        .qty-btn { width: 36px; height: 36px; border: 2px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #333; transition: all 0.2s; }
        .qty-btn:hover { border-color: var(--purple-main); color: var(--purple-main); }
        .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .qty-input { width: 50px; text-align: center; border: 2px solid #e0e0e0; border-radius: 8px; padding: 8px; font-size: 16px; font-weight: 600; }
        .price-cell { font-weight: 700; font-size: 16px; color: #333; white-space: nowrap; }
        .line-total { font-weight: 800; font-size: 16px; color: var(--purple-dark); white-space: nowrap; }
        .remove-btn { background: none; border: none; color: #dc3545; cursor: pointer; padding: 8px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
        .remove-btn:hover { transform: scale(1.1); }
        .remove-btn svg { width: 20px; height: 20px; }
        .cart-summary { background: #fafafa; border: 2px solid #e8e8e8; border-radius: 16px; padding: 24px; margin-top: 24px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 16px; }
        .summary-row.total { font-size: 20px; font-weight: 800; color: var(--purple-dark); border-top: 2px solid #e0e0e0; padding-top: 16px; margin-top: 8px; }
        .checkout-btn { width: 100%; margin-top: 24px; padding: 18px; background: var(--purple-main); color: white; border: none; border-radius: 12px; font-family: "modern-bold", sans-serif; font-size: 18px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 0 var(--purple-dark); transition: all 0.2s; }
        .checkout-btn:hover { background: var(--purple-dark); transform: translateY(-2px); box-shadow: 0 6px 0 var(--purple-dark); }
        .checkout-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 var(--purple-dark); }
        .empty-cart { text-align: center; padding: 80px 20px; }
        .empty-cart svg { width: 120px; height: 120px; color: #ddd; margin-bottom: 24px; }
        .empty-cart h2 { font-family: "modern-bold", sans-serif; font-size: 28px; color: #666; margin-bottom: 12px; }
        .empty-cart p { color: #999; margin-bottom: 24px; }
        .empty-cart .btn { background: var(--purple-main); color: white; border: none; border-radius: 12px; padding: 14px 32px; font-family: "modern-bold", sans-serif; font-weight: 800; font-size: 16px; cursor: pointer; box-shadow: 0 4px 0 var(--purple-dark); }
        @media (max-width: 768px) {
            .cart-table th { display: none; }
            .cart-table td { display: block; padding: 16px 0; }
            .cart-table tr { display: flex; flex-wrap: wrap; align-items: center; padding: 16px; background: white; margin-bottom: 12px; border-radius: 12px; border: 1px solid #eee; }
            .product-cell { flex: 1; min-width: 200px; }
            .quantity-selector { margin: 16px 0; }
            .price-cell::before { content: "Price: "; font-weight: 400; color: #888; }
            .line-total::before { content: "Total: "; font-weight: 400; color: #888; }
            .remove-btn { margin-left: auto; }
        }
    </style>
@endpush

@section('content')
    <div class="cart-page">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
            <a href="{{ route('shop') }}" class="continue-shopping">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Continue Shopping
            </a>
        </div>

        @if(empty($cartItems))
            <div class="empty-cart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven&apos;t added any products yet.</p>
                <a href="{{ route('shop') }}" class="btn">Browse Products</a>
            </div>
        @else
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 15%;">Price</th>
                        <th style="width: 15%;">Quantity</th>
                        <th style="width: 15%;">Total</th>
                        <th style="width: 15%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $index => $item)
                        <tr data-product-id="{{ $item['product']->id }}">
                            <td class="product-cell">
                                <img src="{{ $item['product']->image_url ? (str_starts_with($item['product']->image_url, 'http') ? $item['product']->image_url : asset('storage/' . $item['product']->image_url)) : asset('images/logo.png') }}" alt="{{ $item['product']->name }}" class="product-image">
                                <div class="product-info">
                                    <h3>{{ $item['product']->name }}</h3>
                                    <span class="category">{{ $item['product']->category }}</span>
                                </div>
                            </td>
                            <td class="price-cell">&#8369;{{ number_format((float) $item['product']->price, 2) }}</td>
                            <td class="quantity-selector">
                                <button type="button" class="qty-btn qty-decrease" data-index="{{ $index }}" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>&#8211;</button>
                                <input type="number" class="qty-input qty-value" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock_quantity }}" data-index="{{ $index }}">
                                <button type="button" class="qty-btn qty-increase" data-index="{{ $index }}">+</button>
                            </td>
                            <td class="line-total">&#8369;{{ number_format((float) $item['line_total'], 2) }}</td>
                            <td>
                                <button type="button" class="remove-btn" data-index="{{ $index }}" title="Remove">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>&#8369;{{ number_format((float) $subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Calculated at checkout</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>&#8369;{{ number_format((float) $subtotal, 2) }}</span>
                </div>
                <a href="{{ route('checkout') }}" class="checkout-btn">Proceed to Checkout</a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".qty-decrease").forEach(btn => {
                btn.addEventListener("click", function() {
                    const index = this.dataset.index;
                    const input = document.querySelector('.qty-value[data-index="' + index + '"]');
                    const newVal = parseInt(input.value) - 1;
                    if (newVal >= 1) {
                        input.value = newVal;
                        updateCart(index, newVal);
                    }
                });
            });

            document.querySelectorAll(".qty-increase").forEach(btn => {
                btn.addEventListener("click", function() {
                    const index = this.dataset.index;
                    const input = document.querySelector('.qty-value[data-index="' + index + '"]');
                    const max = parseInt(input.max);
                    const newVal = parseInt(input.value) + 1;
                    if (newVal <= max) {
                        input.value = newVal;
                        updateCart(index, newVal);
                    }
                });
            });

            document.querySelectorAll(".qty-value").forEach(input => {
                input.addEventListener("change", function() {
                    const index = this.dataset.index;
                    let val = parseInt(this.value) || 1;
                    const max = parseInt(this.max) || 99;
                    val = Math.max(1, Math.min(val, max));
                    this.value = val;
                    updateCart(index, val);
                });
            });

            document.querySelectorAll(".remove-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    const index = this.dataset.index;
                    if (confirm("Remove this item from cart?")) {
                        removeFromCart(index);
                    }
                });
            });

            function updateCart(index, quantity) {
                const row = document.querySelector('tbody tr:nth-child(' + (parseInt(index) + 1) + ')');
                const productId = row?.dataset.productId;
                if (!productId) return;

                fetch("{{ route('cart.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error(err));
            }

            function removeFromCart(index) {
                const row = document.querySelector('tbody tr:nth-child(' + (parseInt(index) + 1) + ')');
                const productId = row?.dataset.productId;
                if (!productId) return;

                fetch("{{ route('cart.remove') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(err => console.error(err));
            }
        });
    </script>
@endpush
