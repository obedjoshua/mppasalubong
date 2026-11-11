<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\OrderTicket;
use App\Models\OrderTicketItem;
use App\Models\Product;
use App\Services\PayMongoService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Shop Coordinates Constants for Haversine Distance Calculation
    const MAIN_SHOP = ['lat' => 15.186235193924075, 'lng' => 120.95964131143174];
    const SAN_JOSE  = ['lat' => 15.147108923134585, 'lng' => 120.97994960855327];

    /**
     * Display a listing of user orders and tickets.
     */
    public function index()
    {
        $allTickets = OrderTicket::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->get();

        $orders = $allTickets->filter(function ($t) {
            return in_array(strtolower($t->status), ['approved', 'approved for payment', 'in production', 'ready for pickup', 'completed']);
        });

        $tickets = $allTickets->filter(function ($t) {
            return !in_array(strtolower($t->status), ['approved', 'approved for payment', 'in production', 'ready for pickup', 'completed']);
        });

        return view('orders.index', compact('tickets', 'orders'));
    }

    /**
     * Secondary ticket index view.
     */
    public function ticketsIndex()
    {
        return view('tickets.index');
    }

    /**
     * Show form for creating a new custom order ticket.
     */
    public function create(Request $request)
    {
        $catalogProduct = Product::query()
            ->where('slug', $request->query('product'))
            ->where('is_active', true)
            ->first();

        return view('orders.create', [
            'catalogProduct'  => $catalogProduct,
            'catalogQuantity' => max(1, min(99, (int) $request->query('quantity', 1))),
        ]);
    }

    /**
     * Store a newly created order ticket in the database.
     */
    public function store(Request $request)
    {
        // Default customer_name to authenticated user's name if not provided
        if (!$request->has('customer_name') && Auth::check()) {
            $request->merge(['customer_name' => Auth::user()->name ?? 'Valued Customer']);
        }

        $validated = $request->validate([
            'customer_name'             => 'required|string|max:255',
            'customer_address'          => 'required|string',
            'pickup_branch'             => 'required|string|in:main_shop,san_jose',
            'pickup_date'               => 'required|date',
            'pickup_time'               => 'required|string',
            'customer_lat'              => 'nullable|numeric',
            'customer_lng'              => 'nullable|numeric',
            'additional_notes'          => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.item_type'         => 'required|string',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.reference_image'   => 'nullable|image|max:10240',
        ]);

        $lat = $request->input('customer_lat');
        $lng = $request->input('customer_lng');
        $distKm = null;

        if ($lat && $lng) {
            $targetBranch = $validated['pickup_branch'] === 'san_jose'
                ? self::SAN_JOSE
                : self::MAIN_SHOP;

            $distKm = $this->calculateHaversineDistance(
                $lat, $lng,
                $targetBranch['lat'], $targetBranch['lng']
            );
        }

        $ticket = DB::transaction(function () use ($request, $validated, $lat, $lng, $distKm) {
            $user = Auth::user();
            $messengerId = $user ? ($user->facebook_messenger_id ?? null) : null;
            $preferredChannel = $messengerId ? 'messenger' : 'email';

            $ticket = OrderTicket::create([
                'user_id'                         => Auth::id(),
                'customer_name'                   => $validated['customer_name'],
                'customer_email'                  => $user ? $user->email : null,
                'customer_phone'                  => $user ? ($user->phone ?? null) : null,
                'facebook_messenger_id'           => $messengerId,
                'pickup_branch'                   => $validated['pickup_branch'],
                'location_address'                => $validated['customer_address'],
                'customer_latitude'               => $lat,
                'customer_longitude'              => $lng,
                'calculated_distance_km'          => $distKm,
                'pickup_date'                     => $validated['pickup_date'],
                'pickup_time'                     => $validated['pickup_time'],
                'additional_notes'                => $validated['additional_notes'] ?? null,
                'status'                          => 'Pending Staff Review',
                'qr_token'                        => Str::random(64),
                'preferred_notification_channel' => $preferredChannel,
                'is_testing'                      => false,
                'payment_status'                  => 'pending',
            ]);

            foreach ($request->input('items') as $index => $itemData) {
                $customTagUrl = null;
                if ($request->hasFile("items.{$index}.tag_custom_image")) {
                    try {
                        $path = $request->file("items.{$index}.tag_custom_image")->store('orders/tags', 'r2');
                        $baseUrl = rtrim((string) config('filesystems.disks.r2.url'), '/');
                        $customTagUrl = "{$baseUrl}/{$path}";
                    } catch (\Exception $e) {
                        Log::error('Tag R2 Upload Error: ' . $e->getMessage());
                    }
                }

                $imageDataUri = null;
                $r2Url = null;
                $storedPath = null;

                if ($request->hasFile("items.{$index}.reference_image")) {
                    $file = $request->file("items.{$index}.reference_image");

                    // 1. Store locally in storage/app/public/tickets
                    $storedPath = $file->store('tickets/' . $ticket->id, 'public');

                    // 2. Base64 Data URI Backup
                    $base64 = base64_encode(file_get_contents($file->getRealPath()));
                    $mime = $file->getMimeType();
                    $imageDataUri = "data:{$mime};base64,{$base64}";

                    // 3. Resilient R2 Upload
                    try {
                        $r2Path = $file->store('tickets/' . $ticket->id, 'r2');
                        if ($r2Path) {
                            $baseUrl = rtrim((string) config('filesystems.disks.r2.url'), '/');
                            $r2Url = "{$baseUrl}/{$r2Path}";
                        }
                    } catch (\Exception $e) {
                        Log::error('R2 Image Upload Failed for ticket #' . $ticket->id . ': ' . $e->getMessage());
                    }
                }

                $customOptions = $itemData;
                unset($customOptions['item_type'], $customOptions['quantity']);

                if ($customTagUrl) {
                    $customOptions['custom_tag_path'] = $customTagUrl;
                }

                OrderTicketItem::create([
                    'order_ticket_id'      => $ticket->id,
                    'item_type'            => $itemData['item_type'],
                    'quantity'             => $itemData['quantity'],
                    'image_data'           => $imageDataUri,
                    'image_path'           => $storedPath,
                    'r2_url'               => $r2Url,
                    'reference_image_path' => $storedPath,
                    'custom_options'       => $customOptions,
                ]);
            }

            // Create notification for initial ticket submission
            if ($ticket->user_id) {
                Notification::create([
                    'user_id' => $ticket->user_id,
                    'title'   => 'Order Ticket Received',
                    'message' => "Your order ticket #{$ticket->id} has been submitted and is pending staff audit.",
                    'link'    => route('orders.ticket', $ticket->id),
                    'is_read' => false,
                ]);
            }

            return $ticket;
        });

        return redirect()->route('orders.ticket', ['orderTicket' => $ticket->id])
            ->with('just_submitted', true)
            ->with('success', 'Order ticket created successfully!');
    }

    /**
     * Display single order ticket with QR code generation.
     */
    public function showTicket(OrderTicket $orderTicket, QrCodeService $qrService)
    {
        $orderTicket->load('items');
        $qrData = $qrService->generateTicketQr($orderTicket->id, $orderTicket->qr_token ?? $orderTicket->id);

        return view('tickets.show', [
            'ticket'          => $orderTicket,
            'qrData'          => $qrData,
            'isDashboardView' => request()->routeIs('dashboard.*') || request()->has('from_dashboard'),
        ]);
    }

    /**
     * Privacy-Hashed Public Guest View suitable for Facebook Messenger sharing.
     */
    public function guestView(string $id, QrCodeService $qrService)
    {
        $ticket = OrderTicket::with('items')->findOrFail($id);
        $qrData = $qrService->generateTicketQr($ticket->id, $ticket->qr_token ?? $ticket->id);

        $rawTicketNum = $ticket->ticket_number ?? (string) $ticket->id;
        $ticket->hashed_ticket_number = substr($rawTicketNum, 0, 4) . '****' . substr($rawTicketNum, -2);
        
        $name = $ticket->customer_name ?? 'Guest User';
        $ticket->hashed_customer_name = str_repeat('*', max(1, strlen($name) - 3)) . substr($name, -3);
        
        $email = $ticket->customer_email ?? 'customer@example.com';
        $emailParts = explode('@', $email);
        $ticket->hashed_email = substr($emailParts[0], 0, 2) . '***@' . ($emailParts[1] ?? 'domain.com');

        $phone = $ticket->customer_phone ?? '09000000000';
        $ticket->hashed_phone = substr($phone, 0, 4) . '****' . substr($phone, -2);

        return view('tickets.guest_view', compact('ticket', 'qrData'));
    }

    /**
     * Toggle Testing Mode (Dummy Gateway vs Live Gateway).
     */
    public function toggleTesting(OrderTicket $orderTicket)
    {
        $orderTicket->update([
            'is_testing' => !$orderTicket->is_testing,
        ]);

        $statusText = $orderTicket->is_testing ? 'ENABLED (Dummy Payment Active)' : 'DISABLED (Live PayMongo Active)';
        return back()->with('success', "Testing Mode for Ticket #{$orderTicket->id} is now {$statusText}.");
    }

    /**
     * Toggle "Under Review" mode to lock customer edits.
     */
    public function toggleReviewMode(OrderTicket $orderTicket)
    {
        $isCurrentlyReview = in_array(strtolower($orderTicket->status), ['under review', 'in review', 'review mode']);
        
        $newStatus = $isCurrentlyReview ? 'Pending Staff Review' : 'Under Review';
        $orderTicket->update(['status' => $newStatus]);

        $message = $isCurrentlyReview 
            ? 'Ticket customer editing unlocked (Set back to Pending Staff Review).' 
            : 'Ticket locked into Under Review mode (Customer edits disabled).';

        return back()->with('success', $message);
    }

    /**
     * Staff workflow method: update price quote and advance status step with auto-notifications.
     */
    public function updateQuote(Request $request, OrderTicket $orderTicket)
    {
        $validated = $request->validate([
            'total_price' => 'nullable|numeric|min:0',
            'action'      => 'required|string|in:set_review,approve_payment,start_production,mark_ready,complete',
        ]);

        if (isset($validated['total_price'])) {
            $orderTicket->total_price = $validated['total_price'];
        }

        switch ($validated['action']) {
            case 'set_review':
                $orderTicket->status = 'Under Review';
                $msg = 'Ticket set to Under Review. Customer editing is now locked.';
                break;

            case 'approve_payment':
                $orderTicket->status = 'Approved for Payment';
                $msg = 'Ticket approved for payment!';

                // AUTOMATED NOTIFICATION ON PAYMENT APPROVAL
                if ($orderTicket->user_id) {
                    Notification::create([
                        'user_id' => $orderTicket->user_id,
                        'title'   => 'Order Ticket Approved!',
                        'message' => "Your ticket #{$orderTicket->id} has been approved for payment! Click here to complete payment.",
                        'link'    => route('orders.ticket', $orderTicket->id),
                        'is_read' => false,
                    ]);
                }
                break;

            case 'start_production':
                $orderTicket->status = 'In Production';
                $msg = 'Order moved to kitchen production status.';
                break;

            case 'mark_ready':
                $orderTicket->status = 'Ready for Pickup';
                $msg = 'Order marked as Ready for Pickup!';

                // AUTOMATED NOTIFICATION ON PICKUP READY
                if ($orderTicket->user_id) {
                    Notification::create([
                        'user_id' => $orderTicket->user_id,
                        'title'   => 'Order Ready for Pickup!',
                        'message' => "Your order ticket #{$orderTicket->id} is freshly baked and ready for pickup!",
                        'link'    => route('orders.ticket', $orderTicket->id),
                        'is_read' => false,
                    ]);
                }
                break;

            case 'complete':
                $orderTicket->status = 'Completed';
                $msg = 'Order completed and handed off.';
                break;
        }

        $orderTicket->save();

        return back()->with('success', $msg);
    }

    /**
     * Initiate Payment Gateway Redirection (PayMongo Live or Simulated Dummy Gateway).
     */
    public function processPayment(OrderTicket $orderTicket, PayMongoService $payMongo)
    {
        if (!in_array(strtolower($orderTicket->status), ['approved for payment', 'approved'])) {
            return back()->with('error', 'Order ticket must be approved by staff before payment.');
        }

        if ($orderTicket->is_testing) {
            return view('orders.dummy_payment', ['ticket' => $orderTicket]);
        }

        $items = [];
        $totalItemsCount = max(1, $orderTicket->items->count());
        $totalPrice = $orderTicket->total_price ?? $orderTicket->total_amount ?? 0;
        
        foreach ($orderTicket->items as $item) {
            $items[] = [
                'name'        => $item->item_type ?? 'Custom Item',
                'price'       => (float) ($item->price ?? ($totalPrice / $totalItemsCount)),
                'quantity'    => $item->quantity ?? 1,
                'description' => 'Custom Bakery Order Item',
            ];
        }

        if (empty($items)) {
            $items[] = [
                'name'        => "Custom Order #{$orderTicket->id}",
                'price'       => (float) $totalPrice,
                'quantity'    => 1,
                'description' => 'Bakery Order Payment',
            ];
        }

        $checkoutData = [
            'items'       => $items,
            'success_url' => route('orders.payment-success', $orderTicket->id),
            'cancel_url'  => route('orders.ticket', $orderTicket->id),
            'description' => "Order Ticket #{$orderTicket->id} Payment",
            'order_id'    => $orderTicket->id,
            'user_id'     => Auth::id(),
        ];

        $result = $payMongo->createCheckoutSession($checkoutData);

        if (!$result['success']) {
            return back()->with('error', 'Payment Initialization Failed: ' . $result['error']);
        }

        return redirect($result['checkout_url']);
    }

    /**
     * Process Completed Simulation / Live Payment Callback & Trigger Automated Production.
     */
    public function completeDummyPayment(OrderTicket $orderTicket)
    {
        $orderTicket->update([
            'status'                 => 'In Production',
            'payment_status'         => 'paid',
            'payment_gateway'        => $orderTicket->is_testing ? 'dummy_test' : 'paymongo',
            'payment_transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
        ]);

        // AUTOMATED NOTIFICATION FOR COMPLETED PAYMENT
        if ($orderTicket->user_id) {
            Notification::create([
                'user_id' => $orderTicket->user_id,
                'title'   => 'Payment Confirmed!',
                'message' => "Payment for ticket #{$orderTicket->id} has been processed. Your order is now In Production!",
                'link'    => route('orders.ticket', $orderTicket->id),
                'is_read' => false,
            ]);
        }

        return redirect()->route('orders.ticket', $orderTicket->id)
            ->with('success', 'Payment completed successfully! Your order is now in production.');
    }

    /**
     * Show form for editing an un-reviewed ticket.
     */
    public function edit(OrderTicket $orderTicket)
    {
        if (in_array(strtolower($orderTicket->status), ['under review', 'in review', 'review mode'])) {
            return redirect()->route('orders.ticket', $orderTicket->id)
                ->with('error', 'This ticket is currently under review by staff and cannot be edited.');
        }

        return view('tickets.edit', ['ticket' => $orderTicket]);
    }

    /**
     * Update an existing ticket details.
     */
    public function update(Request $request, OrderTicket $orderTicket)
    {
        if (in_array(strtolower($orderTicket->status), ['under review', 'in review', 'review mode'])) {
            return redirect()->route('orders.ticket', $orderTicket->id)
                ->with('error', 'This ticket is currently under review by staff and cannot be edited.');
        }

        $validated = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'pickup_branch'    => 'required|string|in:main_shop,san_jose',
            'pickup_date'      => 'required|date',
            'pickup_time'      => 'required|string',
            'location_address' => 'required|string',
            'additional_notes' => 'nullable|string',
        ]);

        $orderTicket->update($validated);

        return redirect()->route('orders.ticket', $orderTicket->id)
            ->with('success', 'Order ticket updated successfully!');
    }

    /**
     * Cancellation request form view.
     */
    public function cancelRequestForm(OrderTicket $orderTicket)
    {
        return view('tickets.cancel-request', ['ticket' => $orderTicket]);
    }

    /**
     * Submit ticket cancellation request.
     */
    public function submitCancelRequest(Request $request, OrderTicket $orderTicket)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $orderTicket->update([
            'status'           => 'Cancellation Requested',
            'additional_notes' => ($orderTicket->additional_notes ? $orderTicket->additional_notes . "\n\n" : '') .
                '[Cancellation Request Reason]: ' . $request->input('cancellation_reason'),
        ]);

        return redirect()->route('orders.ticket', $orderTicket->id)
            ->with('success', 'Cancellation request submitted to staff for review.');
    }

    /**
     * Soft delete an active order ticket.
     */
    public function destroy(OrderTicket $orderTicket)
    {
        if (!in_array(strtolower($orderTicket->status), ['pending staff review', 'pending', 'under review'])) {
            return back()->with('error', 'Only pending tickets can be cancelled.');
        }

        session()->flash('ticket_cancelled_id', $orderTicket->id);
        $orderTicket->delete();

        return redirect()->route('tickets.index')->with('ticket_cancelled', 'Ticket #' . $orderTicket->id . ' has been cancelled.');
    }

    /**
     * Restore a soft-deleted order ticket.
     */
    public function restore($id)
    {
        $ticket = OrderTicket::withTrashed()->find($id);

        if ($ticket && $ticket->trashed()) {
            $ticket->restore();
            return redirect()->route('tickets.index')->with('success', 'Ticket #' . $id . ' has been restored successfully!');
        }

        return redirect()->route('tickets.index')->with('error', 'Unable to restore ticket.');
    }

    /**
     * Haversine formula to compute distance (in kilometers) between two geolocation points.
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in KM
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}