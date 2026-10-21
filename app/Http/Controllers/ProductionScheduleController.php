<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\OrderTicket;
use Illuminate\Http\Request;

class ProductionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $branch = $request->get('branch');
        $status = $request->get('status');

        $query = OrderTicket::with('items')
            ->whereDate('pickup_date', $date);

        if ($branch) {
            $query->where('pickup_branch', $branch);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->orderBy('pickup_time', 'asc')->get();

        // Group tickets by time slots for calendar grid view
        $scheduledSlots = $tickets->groupBy('pickup_time');

        return view('staff.schedule.index', compact('scheduledSlots', 'date', 'branch', 'status', 'tickets'));
    }

    public function reschedule(Request $request, $id)
    {
        $ticket = OrderTicket::findOrFail($id);

        $validated = $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        $ticket->update([
            'pickup_date' => $validated['new_date'],
            'pickup_time' => $validated['new_time'],
            'is_rescheduled' => true,
            'staff_notes' => ($ticket->staff_notes ? $ticket->staff_notes . "\n" : "") . 
                             "[Rescheduled on " . now()->format('M d, Y h:i A') . "] Reason: " . ($validated['reason'] ?? 'Staff capacity adjustment.'),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Order #{$ticket->id} rescheduled to {$validated['new_date']} at {$validated['new_time']}"
        ]);
    }

    public function updateStage(Request $request, $id)
    {
        $ticket = OrderTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Pending Staff Review,Approved for Payment,In Production,Ready for Pickup,Completed,Cancelled',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return back()->with('success', "Order #{$ticket->id} status updated to {$validated['status']}");
    }
}