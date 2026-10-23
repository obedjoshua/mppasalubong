@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/walkin.css') }}">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card walkin-form-card p-4 border-0 shadow-sm">
            <h1 class="h4 fw-bold mb-1">Walk-In Order Intake (Groq AI Vision)</h1>
            <p class="text-muted small mb-4">Attach photo or scan of the physical order form. Groq Vision AI parses specifications, downpayment receipts, and remaining balances automatically.</p>

            <form action="{{ route('staff.walkin.process') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-uppercase small">Assigned Branch</label>
                        <select name="pickup_branch" required class="form-select">
                            <option value="main_shop">Main Shop</option>
                            <option value="san_jose">San Jose Branch</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-uppercase small">Physical Form Image</label>
                        <input type="file" name="order_form_image" accept="image/*" required class="form-control">
                    </div>
                </div>

                <div class="ocr-info-box p-3 mb-4">
                    <p class="small mb-0"><strong>Groq AI Vision Detection:</strong> Downpayments and remaining balance due amounts will be parsed from the physical slip directly into the order ticket.</p>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                    Process Order Form with Groq AI
                </button>
            </form>
        </div>
    </div>
</div>
@endsection