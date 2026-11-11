<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketReviewController extends Controller
{
    public function dashboard()
    {
        $pendingCount = OrderTicket::whereIn('status', ['Pending Staff Review', 'Under Review'])->count();
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
            'Under Review',
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

    /**
     * Recommended Next-Step Workflow Handler with Automatic Customer Notifications
     */
    public function updateQuote(Request $request, $id)
    {
        $ticket = OrderTicket::findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:mark_review,approve_quote,start_production,mark_ready,complete_order,reject',
            'quoted_price' => 'required_if:action,approve_quote|nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'staff_notes' => 'nullable|string|max:1000',
        ]);

        $action = $validated['action'];
        $staffNotes = $validated['staff_notes'] ?? $ticket->staff_notes;

        switch ($action) {
            case 'mark_review':
                $ticket->update([
                    'status' => 'Under Review',
                    'staff_notes' => $staffNotes,
                ]);
                
                $this->notifyUser(
                    $ticket,
                    'Ticket Under Review',
                    "Your ticket #{$ticket->id} is now under active staff review. Customer edits are temporarily locked."
                );

                return back()->with('success', "Ticket #{$ticket->id} marked as Under Review. Customer editing is now locked.");

            case 'approve_quote':
                $quotedPrice = (float) $validated['quoted_price'];
                $depositAmount = (float) ($validated['deposit_amount'] ?? 0);

                $ticket->update([
                    'total_price' => $quotedPrice,
                    'deposit_required' => $depositAmount,
                    'staff_notes' => $staffNotes,
                    'status' => 'Approved for Payment',
                ]);

                $this->notifyUser(
                    $ticket,
                    'Order Approved for Payment!',
                    "Great news! Your ticket #{$ticket->id} has been approved for ₱" . number_format($quotedPrice, 2) . ". Click to complete payment."
                );

                return redirect()->route('staff.dashboard')
                    ->with('success', "Ticket #{$ticket->id} approved and quoted at ₱" . number_format($quotedPrice, 2));

            case 'start_production':
                $ticket->update([
                    'status' => 'In Production',
                    'staff_notes' => $staffNotes,
                ]);

                $this->notifyUser(
                    $ticket,
                    'Order in Production',
                    "Your order ticket #{$ticket->id} has been moved to kitchen production."
                );

                return back()->with('success', "Ticket #{$ticket->id} moved to In Production.");

            case 'mark_ready':
                $ticket->update([
                    'status' => 'Ready for Pickup',
                    'staff_notes' => $staffNotes,
                ]);

                $this->notifyUser(
                    $ticket,
                    'Order Ready for Pickup!',
                    "Your order ticket #{$ticket->id} is freshly baked and ready for pickup!"
                );

                return back()->with('success', "Ticket #{$ticket->id} marked as Ready for Pickup.");

            case 'complete_order':
                $ticket->update([
                    'status' => 'Completed',
                    'staff_notes' => $staffNotes,
                ]);

                $this->notifyUser(
                    $ticket,
                    'Order Completed!',
                    "Thank you for your purchase! Order ticket #{$ticket->id} has been completed."
                );

                return back()->with('success', "Ticket #{$ticket->id} marked as Completed.");

            case 'reject':
                $ticket->update([
                    'staff_notes' => $staffNotes,
                    'status' => 'Cancelled',
                ]);

                $this->notifyUser(
                    $ticket,
                    'Ticket Cancelled',
                    "Your order ticket #{$ticket->id} was reviewed and cancelled by staff."
                );

                return redirect()->route('staff.dashboard')
                    ->with('info', "Ticket #{$ticket->id} was cancelled/rejected.");
        }

        return back();
    }

    /**
     * Helper to dispatch database notifications safely
     */
    private function notifyUser(OrderTicket $ticket, string $title, string $message)
    {
        if ($ticket->user_id) {
            Notification::create([
                'user_id' => $ticket->user_id,
                'title' => $title,
                'message' => $message,
                'link' => route('orders.ticket', $ticket->id),
                'is_read' => false,
            ]);
        }
    }
}