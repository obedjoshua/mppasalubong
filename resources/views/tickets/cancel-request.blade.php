@extends('layouts.app')

@section('title', 'Cancel Request #' . $ticket->id)

@section('content')
<div style="max-width: 600px; margin: 40px auto; padding: 0 16px;">
    <a href="{{ route('orders.ticket', $ticket->id) }}" style="text-decoration: none; color: #64748b; font-weight: bold; font-size: 0.9rem;">
        ← Back to Ticket
    </a>

    <div style="background: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #fee2e2; margin-top: 16px;">
        <h2 style="font-family: 'Recoleta-Bold', serif; color: #991b1b; margin-top: 0;">Request Cancellation</h2>
        <p style="color: #4b5563; font-size: 0.875rem;">Because Ticket #{{ $ticket->id }} is currently under review by bakery staff, direct deletion is locked. Submitting this request alerts our team to stop auditing and drop this ticket.</p>

        <form action="{{ route('orders.cancel-request.submit', $ticket->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; font-size: 0.875rem;">Reason for Cancellation Request</label>
                <textarea name="cancellation_reason" rows="4" required placeholder="Please state why you would like to cancel this order ticket..." style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #dc2626; color: #ffffff; border: none; padding: 12px; border-radius: 8px; font-weight: bold; font-size: 0.95rem; cursor: pointer;">
                Submit Cancellation Request
            </button>
        </form>
    </div>
</div>
@endsection