<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use Illuminate\Http\Request;

class TicketReviewController extends Controller
{
    public function dashboard()
    {
        $pendingCount = OrderTicket::where('status', 'Pending Staff Review')->count();
        $approvedCount = OrderTicket::where('status', 'Approved for Payment')->count();
        $inProductionCount = OrderTicket::where('status', 'In Production')->count();

        $todayPickups = OrderTicket::whereDate('pickup_date', today())
            ->whereIn('status', ['In Production', 'Ready for Pickup'])
            ->get();

        return view('staff.dashboard', compact('pendingCount', 'approvedCount', 'inProductionCount', 'todayPickups'));
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'Pending Staff Review');
        $branch = $request->get('branch');

        $tickets = OrderTicket::with('items')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($branch, fn ($q) => $q->where('pickup_branch', $branch))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('staff.tickets.index', compact('tickets', 'status', 'branch'));
    }

    public function show($id)
    {
        $ticket = OrderTicket::with(['items', 'user'])->findOrFail($id);

        return view('staff.tickets.show', compact('ticket'));
    }

    public function updateQuote(Request $request, $id)
    {
        $ticket = OrderTicket::findOrFail($id);

        $validated = $request->validate([
            'quoted_price' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string|max:1000',
            'action' => 'required|in:approve,reject',
        ]);

        if ($validated['action'] === 'approve') {
            $ticket->update([
                'total_price' => $validated['quoted_price'],
                'deposit_required' => $validated['deposit_amount'] ?? 0,
                'staff_notes' => $validated['staff_notes'],
                'status' => 'Approved for Payment',
            ]);

            return redirect()->route('staff.tickets.index')
                ->with('success', "Ticket #{$ticket->id} approved and quoted at ₱".number_format($validated['quoted_price'], 2));
        }

        $ticket->update([
            'staff_notes' => $validated['staff_notes'],
            'status' => 'Cancelled',
        ]);

        return redirect()->route('staff.tickets.index')
            ->with('info', "Ticket #{$ticket->id} was rejected.");
    }
}
