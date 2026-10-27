<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $statuses = [
            'Pending Staff Review',
            'Approved for Payment',
            'In Production',
            'Ready for Pickup',
            'Completed',
            'Cancelled',
        ];

        $filters = $request->validate([
            'status' => ['nullable', Rule::in($statuses)],
            'branch' => ['nullable', Rule::in(['main_shop', 'san_jose'])],
        ]);
        $status = $filters['status'] ?? null;
        $branch = $filters['branch'] ?? null;

        $tickets = OrderTicket::with(['items', 'user'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($branch, fn ($query) => $query->where('pickup_branch', $branch))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.tickets.index', compact('tickets', 'statuses', 'status', 'branch'));
    }

    public function show(string $id)
    {
        $ticket = OrderTicket::with(['items', 'user'])->findOrFail($id);

        return view('staff.tickets.show', compact('ticket'));
    }

    public function updateQuote(Request $request, $id)
    {
        $ticket = OrderTicket::findOrFail($id);

        if ($ticket->status !== 'Pending Staff Review') {
            return back()->with('error', "Ticket #{$ticket->id} is no longer awaiting review.");
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'quoted_price' => 'required_if:action,approve|nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        if ($validated['action'] === 'approve') {
            $ticket->update([
                'total_price' => $validated['quoted_price'],
                'deposit_required' => $validated['deposit_amount'] ?? 0,
                'staff_notes' => $validated['staff_notes'],
                'status' => 'Approved for Payment',
            ]);

            return redirect()->route('staff.dashboard')
                ->with('success', "Ticket #{$ticket->id} approved and quoted at ₱".number_format($validated['quoted_price'], 2));
        }

        $ticket->update([
            'staff_notes' => $validated['staff_notes'],
            'status' => 'Cancelled',
        ]);

        return redirect()->route('staff.dashboard')
            ->with('info', "Ticket #{$ticket->id} was rejected.");
    }
}
