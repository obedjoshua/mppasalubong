<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $input = trim($validated['ticket_id']);
        $possibleTokens = [$input];

        // Parse URL if full link was scanned
        if (filter_var($input, FILTER_VALIDATE_URL) || str_contains($input, 'http')) {
            $parsedUrl = parse_url($input);

            // Extract query parameters like ?token=... or ?ticket=...
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                if (isset($queryParams['token'])) {
                    $possibleTokens[] = $queryParams['token'];
                }
                if (isset($queryParams['ticket'])) {
                    $possibleTokens[] = $queryParams['ticket'];
                }
            }

            // Extract trailing segment from path (e.g. /orders/verify/JA2X8h-Oa_c)
            if (isset($parsedUrl['path'])) {
                $pathSegments = array_values(array_filter(explode('/', trim($parsedUrl['path'], '/'))));
                if (!empty($pathSegments)) {
                    $possibleTokens[] = end($pathSegments);
                }
            }
        }

        // Clean up candidates
        $possibleTokens = array_unique(array_filter($possibleTokens));

        // Query database against any extracted token match
        $ticket = OrderTicket::with('items')
            ->whereIn('id', $possibleTokens)
            ->orWhereIn('qr_token', $possibleTokens)
            ->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => "Token not found. Tried checking tokens: " . implode(', ', $possibleTokens),
            ], 404);
        }

        if ($ticket->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => "Ticket #{$ticket->id} was ALREADY completed on ".$ticket->completed_at?->format('M d, Y h:i A'),
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
            ],
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
            'message' => "Order #{$ticket->id} successfully fulfilled and completed!",
        ]);
    }
}