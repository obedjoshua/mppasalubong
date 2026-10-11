<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use App\Models\OrderTicketItem;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function index()
    {
        $allTickets = OrderTicket::where('user_id', Auth::id())
            ->with('items')
            ->latest()
            ->get();

        $orders = $allTickets->filter(function ($t) {
            return strtolower($t->status) === 'approved';
        });

        $tickets = $allTickets->filter(function ($t) {
            return strtolower($t->status) !== 'approved';
        });

        return view('orders.index', compact('tickets', 'orders'));
    }

    /**
     * Display the dedicated tickets index view.
     */
    public function ticketsIndex()
    {
        return view('tickets.index');
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'additional_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reference_image' => 'required_if:items.*.item_type,custom_cake|nullable|image|max:10240',
        ], [
            'items.*.reference_image.required_if' => 'Please upload or select a reference image for your Custom Cake.'
        ]);

        $tempImageBase64 = null;

        $ticket = DB::transaction(function () use ($request, $validated, &$tempImageBase64) {
            $user = Auth::user();

            $messengerId = $user ? ($user->facebook_messenger_id ?? null) : null;
            $preferredChannel = $messengerId ? 'messenger' : 'email';

            $ticket = OrderTicket::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $user ? $user->email : null,
                'customer_phone' => $user ? ($user->phone ?? null) : null,
                'facebook_messenger_id' => $messengerId,
                'location_address' => $request->input('location_address', 'TBD'),
                'pickup_date' => $validated['pickup_date'],
                'pickup_time' => $validated['pickup_time'],
                'additional_notes' => $validated['additional_notes'] ?? null,
                'status' => 'Pending Staff Review',
                'qr_token' => Str::random(64),
                'preferred_notification_channel' => $preferredChannel,
            ]);

            foreach ($request->input('items') as $index => $itemData) {
                $customTagUrl = null;
                if ($request->hasFile("items.{$index}.tag_custom_image")) {
                    $path = $request->file("items.{$index}.tag_custom_image")->store('orders/tags', 'r2');
                    $baseUrl = rtrim(config('filesystems.disks.r2.url', env('CLOUDFLARE_R2_PUBLIC_URL')), '/');
                    $customTagUrl = "{$baseUrl}/{$path}";
                }

                $referenceImgUrl = null;
                if ($request->hasFile("items.{$index}.reference_image")) {
                    $file = $request->file("items.{$index}.reference_image");
                    
                    $tempImageBase64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

                    $path = $file->store('orders/references', 'r2');
                    $baseUrl = rtrim(config('filesystems.disks.r2.url', env('CLOUDFLARE_R2_PUBLIC_URL')), '/');
                    $referenceImgUrl = "{$baseUrl}/{$path}";
                }

                $customOptions = $itemData;
                unset($customOptions['item_type'], $customOptions['quantity']); 

                if ($referenceImgUrl) $customOptions['reference_image'] = $referenceImgUrl;
                if ($customTagUrl) $customOptions['custom_tag_path'] = $customTagUrl;

                OrderTicketItem::create([
                    'order_ticket_id' => $ticket->id,
                    'item_type' => $itemData['item_type'],
                    'quantity' => $itemData['quantity'],
                    'reference_image_path' => $referenceImgUrl,
                    'custom_options' => $customOptions,
                ]);
            }

            return $ticket;
        });

        if ($tempImageBase64) {
            Cache::put('ticket_temp_img_' . $ticket->id, $tempImageBase64, now()->addHours(24));
        }

        return redirect()->route('orders.ticket', ['orderTicket' => $ticket->id])
                         ->with('just_submitted', true)
                         ->with('success', 'Order ticket created successfully!');
    }

    public function showTicket(OrderTicket $orderTicket, QrCodeService $qrService)
    {
        $orderTicket->load('items');
        
        $qrData = $qrService->generateTicketQr($orderTicket->id, $orderTicket->qr_token ?? $orderTicket->id);

        return view('tickets.show', [
            'ticket' => $orderTicket,
            'qrData' => $qrData,
            'isDashboardView' => request()->routeIs('dashboard.*') || request()->has('from_dashboard')
        ]);
    }

    public function destroy(OrderTicket $orderTicket)
    {
        if (!in_array(strtolower($orderTicket->status), ['pending staff review', 'pending', 'under review'])) {
            return back()->with('error', 'Only pending tickets can be cancelled.');
        }
    
        // Store ticket ID in session for potential inline Undo
        session()->flash('ticket_cancelled_id', $orderTicket->id);
        
        Cache::forget('ticket_temp_img_' . $orderTicket->id);
        $orderTicket->delete();
    
        return redirect()->route('tickets.index')->with('ticket_cancelled', 'Ticket #' . $orderTicket->id . ' has been cancelled.');
    }
    
    public function restore($id)
    {
        $ticket = OrderTicket::withTrashed()->find($id);
    
        if ($ticket && $ticket->trashed()) {
            $ticket->restore();
            return redirect()->route('tickets.index')->with('success', 'Ticket #' . $id . ' has been restored successfully!');
        }
    
        return redirect()->route('tickets.index')->with('error', 'Unable to restore ticket.');
    }
}