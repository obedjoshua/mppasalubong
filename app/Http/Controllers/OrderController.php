<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use App\Models\OrderTicketItem;
use App\Models\Product;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Shop Coordinates Constants
    const MAIN_SHOP = ['lat' => 15.186235193924075, 'lng' => 120.95964131143174];

    const SAN_JOSE = ['lat' => 15.147108923134585, 'lng' => 120.97994960855327];

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

    public function ticketsIndex()
    {
        return view('tickets.index');
    }

    public function create(Request $request)
    {
        $catalogProduct = Product::query()
            ->where('slug', $request->query('product'))
            ->where('is_active', true)
            ->first();

        return view('orders.create', [
            'catalogProduct' => $catalogProduct,
            'catalogQuantity' => max(1, min(99, (int) $request->query('quantity', 1))),
        ]);
    }

    public function store(Request $request)
    {
        // Default customer_name to authenticated user's name if not provided
        if (! $request->has('customer_name') && Auth::check()) {
            $request->merge(['customer_name' => Auth::user()->name ?? 'Valued Customer']);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'pickup_branch' => 'required|string|in:main_shop,san_jose',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'customer_lat' => 'nullable|numeric',
            'customer_lng' => 'nullable|numeric',
            'additional_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reference_image' => 'nullable|image|max:10240',
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
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $user ? $user->email : null,
                'customer_phone' => $user ? ($user->phone ?? null) : null,
                'facebook_messenger_id' => $messengerId,
                'pickup_branch' => $validated['pickup_branch'],
                'location_address' => $validated['customer_address'],
                'customer_latitude' => $lat,
                'customer_longitude' => $lng,
                'calculated_distance_km' => $distKm,
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
                    try {
                        $path = $request->file("items.{$index}.tag_custom_image")->store('orders/tags', 'r2');
                        $baseUrl = rtrim((string) config('filesystems.disks.r2.url'), '/');
                        $customTagUrl = "{$baseUrl}/{$path}";
                    } catch (\Exception $e) {
                        Log::error('Tag R2 Upload Error: '.$e->getMessage());
                    }
                }

                $imageDataUri = null;

                if ($request->hasFile("items.{$index}.reference_image")) {
                    $file = $request->file("items.{$index}.reference_image");

                    $base64 = base64_encode(file_get_contents($file->getRealPath()));
                    $mime = $file->getMimeType();
                    $imageDataUri = "data:{$mime};base64,{$base64}";
                }

                $customOptions = $itemData;
                unset($customOptions['item_type'], $customOptions['quantity']);

                if ($customTagUrl) {
                    $customOptions['custom_tag_path'] = $customTagUrl;
                }

                OrderTicketItem::create([
                    'order_ticket_id' => $ticket->id,
                    'item_type' => $itemData['item_type'],
                    'quantity' => $itemData['quantity'],
                    'image_data' => $imageDataUri,
                    'r2_url' => null,
                    'reference_image_path' => null,
                    'custom_options' => $customOptions,
                ]);
            }

            return $ticket;
        });

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
            'isDashboardView' => request()->routeIs('dashboard.*') || request()->has('from_dashboard'),
        ]);
    }

    public function destroy(OrderTicket $orderTicket)
    {
        if (! in_array(strtolower($orderTicket->status), ['pending staff review', 'pending', 'under review'])) {
            return back()->with('error', 'Only pending tickets can be cancelled.');
        }

        session()->flash('ticket_cancelled_id', $orderTicket->id);
        $orderTicket->delete();

        return redirect()->route('tickets.index')->with('ticket_cancelled', 'Ticket #'.$orderTicket->id.' has been cancelled.');
    }

    public function restore($id)
    {
        $ticket = OrderTicket::withTrashed()->find($id);

        if ($ticket && $ticket->trashed()) {
            $ticket->restore();

            return redirect()->route('tickets.index')->with('success', 'Ticket #'.$id.' has been restored successfully!');
        }

        return redirect()->route('tickets.index')->with('error', 'Unable to restore ticket.');
    }

    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
