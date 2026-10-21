<?php

namespace App\Http\Controllers\Staff;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\OrderTicket;
use Illuminate\Http\Request;

class QrVerificationController extends Controller
{
    public function index()
    {
        return view('staff.qr.scanner');
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|string',
        ]);

        $ticket = OrderTicket::with('items')
            ->where('id', $validated['ticket_id'])
            ->orWhere('qr_token', $validated['ticket_id'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Order ticket token not found in system records.'
            ], 404);
        }

        if ($ticket->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => "Ticket #{$ticket->id} was ALREADY completed on " . $ticket->completed_at?->format('M d, Y h:i A')
            ], 400);
        }

        return response()->json([
            'success' => true,
            'ticket' => [
                'id' => $ticket->id,
                'customer_name' => $ticket->customer_name,
                'customer_phone' => $ticket->customer_phone,
                'pickup_branch' => $ticket->pickup_branch,
                'pickup_date' => $ticket->pickup_date,
                'pickup_time' => $ticket->pickup_time,
                'total_price' => $ticket->total_price,
                'deposit_required' => $ticket->deposit_required,
                'status' => $ticket->status,
                'items' => $ticket->items,
            ]
        ]);
    }

    public function completeOrder(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|string',
        ]);

        $ticket = OrderTicket::where('id', $validated['ticket_id'])
            ->orWhere('qr_token', $validated['ticket_id'])
            ->firstOrFail();

        $ticket->update([
            'status' => 'Completed',
            'completed_at' => now(),
            'handled_by_staff_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order #{$ticket->id} successfully fulfilled and completed!"
        ]);
    }
}