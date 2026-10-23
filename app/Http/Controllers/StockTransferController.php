<?php

namespace App\Http\Controllers;

use App\Models\BranchMessage;
use App\Models\BranchStockTransfer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = BranchStockTransfer::with(['requestedBy', 'fulfilledBy', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $products = Product::where('is_active', true)->get();
        $messages = BranchMessage::with('sender')->orderBy('created_at', 'desc')->take(20)->get();

        return view('staff.logistics.index', compact('transfers', 'products', 'messages'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'from_branch' => 'required|string',
            'to_branch' => 'required|string|different:from_branch',
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required_without:product_id|string|max:255',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $itemName = $validated['item_name'] ?? null;
        if ($validated['product_id']) {
            $product = Product::find($validated['product_id']);
            $itemName = $product->name;
        }

        BranchStockTransfer::create([
            'transfer_code' => 'TR-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'from_branch' => $validated['from_branch'],
            'to_branch' => $validated['to_branch'],
            'product_id' => $validated['product_id'] ?? null,
            'item_name' => $itemName,
            'quantity' => $validated['quantity'],
            'status' => 'Requested',
            'requested_by_staff_id' => Auth::id(),
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Stock transfer request created successfully.');
    }

    public function updateTransferStatus(Request $request, $id)
    {
        $transfer = BranchStockTransfer::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Approved,In Transit,Received,Cancelled',
        ]);

        $transfer->update([
            'status' => $validated['status'],
            'fulfilled_by_staff_id' => Auth::id(),
            'received_at' => $validated['status'] === 'Received' ? now() : $transfer->received_at,
        ]);

        // Auto-adjust inventory stock if tied to catalog product and marked Received
        if ($validated['status'] === 'Received' && $transfer->product_id) {
            $product = Product::find($transfer->product_id);
            if ($product) {
                $product->increment('stock_quantity', $transfer->quantity);
            }
        }

        return back()->with('success', "Transfer {$transfer->transfer_code} updated to {$validated['status']}");
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'sender_branch' => 'required|string',
            'recipient_branch' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000',
            'priority' => 'required|in:Normal,Urgent,High Alert',
            'order_ticket_id' => 'nullable|exists:order_tickets,id',
        ]);

        BranchMessage::create([
            'sender_id' => Auth::id(),
            'sender_branch' => $validated['sender_branch'],
            'recipient_branch' => $validated['recipient_branch'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'order_ticket_id' => $validated['order_ticket_id'] ?? null,
        ]);

        return back()->with('success', 'Inter-branch message sent.');
    }
}
