@extends('layouts.staff')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/staff/logistics.css') }}">
@endpush

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Inter-Branch Logistics</h1>
    <p class="text-muted small mb-0">Manage stock transfers and dispatch operational communications.</p>
</div>

<div class="row g-4">
    <!-- Stock Transfer Request Form -->
    <div class="col-12 col-lg-6">
        <div class="card logistics-card p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Request Stock Transfer</h5>
            <form action="{{ route('staff.transfers.store') }}" method="POST">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">From Branch</label>
                        <select name="from_branch" class="form-select form-select-sm">
                            <option value="main_shop">Main Shop</option>
                            <option value="san_jose">San Jose Branch</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">To Branch</label>
                        <select name="to_branch" class="form-select form-select-sm">
                            <option value="san_jose">San Jose Branch</option>
                            <option value="main_shop">Main Shop</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Catalog Item (Optional)</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">-- Manual Item / Raw Ingredient --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock_quantity }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Item Description</label>
                    <input type="text" name="item_name" placeholder="e.g. 8-inch Cake Boxes" class="form-control form-control-sm">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Quantity</label>
                    <input type="number" min="1" name="quantity" required class="form-control form-control-sm">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold">Submit Transfer Request</button>
            </form>
        </div>
    </div>

    <!-- Branch Messaging -->
    <div class="col-12 col-lg-6">
        <div class="card logistics-card p-4 border-0 shadow-sm">
            <h5 class="fw-bold mb-3">Dispatch Branch Message</h5>
            <form action="{{ route('staff.messages.send') }}" method="POST">
                @csrf
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-custom">Sender</label>
                        <select name="sender_branch" class="form-select form-select-sm">
                            <option value="main_shop">Main Shop</option>
                            <option value="san_jose">San Jose Branch</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label-custom">Recipient</label>
                        <select name="recipient_branch" class="form-select form-select-sm">
                            <option value="san_jose">San Jose Branch</option>
                            <option value="main_shop">Main Shop</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="Normal">Normal</option>
                        <option value="Urgent">Urgent</option>
                        <option value="High Alert">High Alert</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Message</label>
                    <textarea name="message" rows="3" required placeholder="Type operational message..." class="form-control form-control-sm"></textarea>
                </div>

                <button type="submit" class="btn btn-dark w-100 fw-bold">Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection