@extends('layouts.app')

@section('title', 'Checkout | Masaganang Pagpapala')

@push('styles')
    <style>
        .checkout-page { max-width: 1000px; margin: 0 auto; padding: 40px clamp(20px, 4vw, 80px); font-family: "Manrope", sans-serif; }
        .checkout-header { margin-bottom: 32px; }
        .checkout-header h1 { font-family: "modern-bold", sans-serif; font-size: clamp(28px, 4vw, 42px); color: var(--purple-dark); margin: 0 0 8px; }
        .checkout-header p { color: #666; margin: 0; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 420px; gap: 40px; }
        .checkout-form { background: white; border: 2px solid #e8e8e8; border-radius: 16px; padding: 32px; }
        .form-section { margin-bottom: 32px; }
        .form-section h2 { font-family: "modern-bold", sans-serif; font-size: 20px; color: var(--purple-dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-family: inherit; font-size: 16px; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--purple-main); box-shadow: 0 0 0 3px rgba(138, 56, 245, 0.15); }
        .form-group input.error, .form-group select.error { border-color: #dc3545; }
        .error-message { color: #dc3545; font-size: 13px; margin-top: 6px; display: none; }
        .form-group input.error + .error-message { display: block; }
        .order-summary { background: #fafafa; border: 2px solid #e8e8e8; border-radius: 16px; padding: 24px; height: fit-content; position: sticky; top: 100px; }
        .order-summary h2 { font-family: "modern-bold", sans-serif; font-size: 20px; color: var(--purple-dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 2px solid #e0e0e0; }
        .summary-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
        .summary-item:last-of-type { border-bottom: none; }
        .summary-item .item-info { display: flex; flex-direction: column; gap: 4px; }
        .summary-item .item-name { font-weight: 600; color: #333; }
        .summary-item .item-qty { font-size: 13px; color: #888; }
        .summary-item .item-price { font-weight: 700; color: var(--purple-dark); white-space: nowrap; }
        .summary-totals { margin-top: 16px; padding-top: 16px; border-top: 2px solid #e0e0e0; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }
        .summary-row.total { font-size: 20px; font-weight: 800; color: var(--purple-dark); margin-top: 8px; }
        .pay-btn { width: 100%; margin-top: 24px; padding: 18px; background: var(--purple-main); color: white; border: none; border-radius: 12px; font-family: "modern-bold", sans-serif; font-size: 18px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 0 var(--purple-dark); transition: all 0.2s; }
        .pay-btn:hover { background: var(--purple-dark); transform: translateY(-2px); box-shadow: 0 6px 0 var(--purple-dark); }
        .pay-btn:active { transform: translateY(2px); box-shadow: 0 2px 0 var(--purple-dark); }
        .pay-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .payment-methods { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
        .payment-method { flex: 1; min-width: 140px; padding: 14px; border: 2px solid #e0e0e0; border-radius: 10px; cursor: pointer; text-align: center; transition: all 0.2s; }
        .payment-method:hover { border-color: var(--purple-main); }
        .payment-method.selected { border-color: var(--purple-main); background: rgba(138, 56, 245, 0.05); }
        .payment-method input { display: none; }
        .payment-method svg { width: 32px; height: 24px; margin-bottom: 8px; }
        .payment-method span { font-weight: 600; font-size: 14px; color: #333; }
        .required::after { content: " *"; color: #dc3545; }
        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; }
            .order-summary { position: static; }
        }
        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    <div class="checkout-page">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Enter your details to complete your order</p>
        </div>

        <form id="checkoutForm" method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <div class="checkout-grid">
                <div class="checkout-form">
                    <div class="form-section">
                        <h2>Contact Information</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="customer_name" class="required">Full Name</label>
                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', Auth::user()->name ?? '') }}" required autocomplete="name">
                                <div class="error-message">Please enter your full name</div>
                            </div>
                            <div class="form-group">
                                <label for="customer_email" class="required">Email Address</label>
                                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', Auth::user()->email ?? '') }}" required autocomplete="email">
                                <div class="error-message">Please enter a valid email</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone" class="required">Phone Number</label>
                            <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" required autocomplete="tel" placeholder="+63 9XX XXX XXXX">
                            <div class="error-message">Please enter your phone number</div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Delivery Address</h2>
                        <div class="form-group">
                            <label for="customer_address" class="required">Full Address</label>
                            <textarea id="customer_address" name="customer_address" rows="3" required placeholder="House/Building, Street, Barangay, City, Province">{{ old('customer_address') }}</textarea>
                            <div class="error-message">Please enter your delivery address</div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Pickup Details</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pickup_branch" class="required">Pickup Branch</label>
                                <select id="pickup_branch" name="pickup_branch" required>
                                    <option value="main_shop" {{ old('pickup_branch') === 'main_shop' ? 'selected' : '' }}>Main Branch - Poblacion, San Miguel, Bulacan</option>
                                    <option value="san_jose" {{ old('pickup_branch') === 'san_jose' ? 'selected' : '' }}>San Jose Branch - Norberto St., San Jose, San Miguel</option>
                                </select>
                                <div class="error-message">Please select a pickup branch</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pickup_date" class="required">Pickup Date</label>
                                <input type="date" id="pickup_date" name="pickup_date" value="{{ old('pickup_date') }}" required min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                <div class="error-message">Please select a pickup date</div>
                            </div>
                            <div class="form-group">
                                <label for="pickup_time" class="required">Pickup Time</label>
                                <select id="pickup_time" name="pickup_time" required>
                                    <option value="">Select time</option>
                                    <option value="08:00 AM" {{ old('pickup_time') === '08:00 AM' ? 'selected' : '' }}>08:00 AM</option>
                                    <option value="09:00 AM" {{ old('pickup_time') === '09:00 AM' ? 'selected' : '' }}>09:00 AM</option>
                                    <option value="10:00 AM" {{ old('pickup_time') === '10:00 AM' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="11:00 AM" {{ old('pickup_time') === '11:00 AM' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="12:00 PM" {{ old('pickup_time') === '12:00 PM' ? 'selected' : '' }}>12:00 PM</option>
                                    <option value="01:00 PM" {{ old('pickup_time') === '01:00 PM' ? 'selected' : '' }}>01:00 PM</option>
                                    <option value="02:00 PM" {{ old('pickup_time') === '02:00 PM' ? 'selected' : '' }}>02:00 PM</option>
                                    <option value="03:00 PM" {{ old('pickup_time') === '03:00 PM' ? 'selected' : '' }}>03:00 PM</option>
                                    <option value="04:00 PM" {{ old('pickup_time') === '04:00 PM' ? 'selected' : '' }}>04:00 PM</option>
                                    <option value="05:00 PM" {{ old('pickup_time') === '05:00 PM' ? 'selected' : '' }}>05:00 PM</option>
                                    <option value="06:00 PM" {{ old('pickup_time') === '06:00 PM' ? 'selected' : '' }}>06:00 PM</option>
                                </select>
                                <div class="error-message">Please select a pickup time</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <label class="payment-method selected" data-method="card">
                                <input type="radio" name="payment_method" value="card" checked>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                <span>Credit/Debit Card</span>
                            </label>
                            <label class="payment-method" data-method="gcash">
                                <input type="radio" name="payment_method" value="gcash">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                <span>GCash</span>
                            </label>
                            <label class="payment-method" data-method="paymaya">
                                <input type="radio" name="payment_method" value="paymaya">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                <span>Maya</span>
                            </label>
                            <label class="payment-method" data-method="grabpay">
                                <input type="radio" name="payment_method" value="grabpay">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                                <span>GrabPay</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="pay-btn" id="payBtn">
                        <span class="btn-text">Pay ₱{{ number_format((float) $subtotal, 2) }}</span>
                        <span class="btn-loading" style="display:none;">Processing...</span>
                    </button>
                </div>

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    @foreach($cartItems as $item)
                        <div class="summary-item">
                            <div class="item-info">
                                <span class="item-name">{{ $item['product']->name }}</span>
                                <span class="item-qty">Qty: {{ $item['quantity'] }} × ₱{{ number_format((float) $item['product']->price, 2) }}</span>
                            </div>
                            <span class="item-price">₱{{ number_format((float) $item['line_total'], 2) }}</span>
                        </div>
                    @endforeach
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₱{{ number_format((float) $subtotal, 2) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>₱{{ number_format((float) $subtotal, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Payment method selection
            document.querySelectorAll(".payment-method").forEach(method => {
                method.addEventListener("click", function() {
                    document.querySelectorAll(".payment-method").forEach(m => m.classList.remove("selected"));
                    this.classList.add("selected");
                    this.querySelector("input").checked = true;
                });
            });

            // Form validation
            const form = document.getElementById("checkoutForm");
            const payBtn = document.getElementById("payBtn");

            form.addEventListener("submit", function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll("[required]");

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add("error");
                        isValid = false;
                    } else {
                        field.classList.remove("error");
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    return;
                }

                // Show loading state
                payBtn.disabled = true;
                payBtn.querySelector(".btn-text").style.display = "none";
                payBtn.querySelector(".btn-loading").style.display = "inline";
            });

            // Remove error on input
            form.querySelectorAll("input, select, textarea").forEach(field => {
                field.addEventListener("input", function() {
                    this.classList.remove("error");
                });
            });
        });
    </script>
@endpush
