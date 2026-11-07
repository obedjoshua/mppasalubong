@extends('layouts.app')

@section('title', 'Edit Ticket #' . $ticket->id)

@section('content')
<div style="max-width: 700px; margin: 30px auto; padding: 0 16px;">
    <a href="{{ route('orders.ticket', $ticket->id) }}" style="text-decoration: none; color: #7e22ce; font-weight: bold; font-size: 0.9rem;">
        ← Cancel & Return to Ticket
    </a>

    <div style="background: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; margin-top: 16px;">
        <h2 style="font-family: 'Recoleta-Bold', serif; color: #111827; margin-top: 0;">Edit Order Inquiry #{{ $ticket->id }}</h2>
        <p style="color: #64748b; font-size: 0.875rem;">Modify schedule or location preferences for your order ticket.</p>

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('orders.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Customer Name</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', $ticket->customer_name) }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Pickup Branch</label>
                <select name="pickup_branch" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="main_shop" {{ $ticket->pickup_branch === 'main_shop' ? 'selected' : '' }}>Main Branch (Poblacion, San Miguel)</option>
                    <option value="san_jose" {{ $ticket->pickup_branch === 'san_jose' ? 'selected' : '' }}>San Jose Branch (Norberto St., San Jose)</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Pickup Date</label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date', $ticket->pickup_date) }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Pickup Time</label>
                    <input type="time" name="pickup_time" value="{{ old('pickup_time', $ticket->pickup_time) }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Location Address / Notes</label>
                <input type="text" name="location_address" value="{{ old('location_address', $ticket->location_address) }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Additional Instructions</label>
                <textarea name="additional_notes" rows="4" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;">{{ old('additional_notes', $ticket->additional_notes) }}</textarea>
            </div>

            <button type="submit" style="width: 100%; background: #7e22ce; color: #ffffff; border: none; padding: 12px; border-radius: 8px; font-weight: bold; font-size: 0.95rem; cursor: pointer;">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection