<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use App\Models\OrderTicketItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WalkInOrderController extends Controller
{
    /**
     * Display the Walk-in Order Intake Form.
     */
    public function create()
    {
        return view('staff.walkin.create');
    }

    /**
     * Process physical order form via Groq AI Vision OCR.
     */
    public function processForm(Request $request)
    {
        $request->validate([
            'order_form_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'pickup_branch' => 'required|string',
        ]);

        $file = $request->file('order_form_image');
        $base64Image = base64_encode(file_get_contents($file->getRealPath()));
        $mimeType = $file->getMimeType();

        // Structured prompt instructing Groq AI Vision to extract fields as JSON
        $prompt = 'Analyze this physical custom cake order form. Extract all visible details accurately and reply strictly with a valid JSON object without markdown formatting. '.
                  'Required JSON keys: customer_name, customer_phone, pickup_date (formatted as YYYY-MM-DD), pickup_time (formatted as HH:MM AM/PM), item_type, theme_description, total_price (numeric), deposit_paid (numeric), balance_remaining (numeric).';

        try {
            // Call Groq Vision API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.groq.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.2-11b-vision-preview',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            [
                                'type' => 'image_url',
                                'image_url' => ['url' => "data:{$mimeType};base64,{$base64Image}"],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Groq API request failed: '.$response->body());
            }

            $jsonString = $response->json('choices.0.message.content');
            $parsedData = json_decode($jsonString, true) ?? [];

            // Store physical form scan image in public storage
            $imagePath = $file->store('walkin_forms', 'public');

            // Create Order Ticket in Database
            $ticket = OrderTicket::create([
                'user_id' => Auth::id(),
                'customer_name' => $parsedData['customer_name'] ?? 'Walk-In Customer',
                'customer_phone' => $parsedData['customer_phone'] ?? null,
                'pickup_branch' => $request->input('pickup_branch'),
                'pickup_date' => $parsedData['pickup_date'] ?? now()->addDays(2)->format('Y-m-d'),
                'pickup_time' => $parsedData['pickup_time'] ?? '10:00 AM',
                'status' => 'In Production',
                'total_price' => $parsedData['total_price'] ?? 0.00,
                'deposit_required' => $parsedData['deposit_paid'] ?? 0.00,
                'staff_notes' => 'Walk-in form processed via Groq AI Vision. Balance remaining: ₱'.number_format($parsedData['balance_remaining'] ?? 0, 2),
                'handled_by_staff_id' => Auth::id(),
            ]);

            // Save Ticket Item Details
            OrderTicketItem::create([
                'order_ticket_id' => $ticket->id,
                'item_type' => $parsedData['item_type'] ?? 'Custom Cake',
                'quantity' => 1,
                'reference_image_path' => Storage::url($imagePath),
                'custom_options' => json_encode([
                    'ai_theme' => $parsedData['theme_description'] ?? 'Walk-In Custom Order',
                    'groq_ocr_extracted' => true,
                ]),
            ]);

            return redirect()->route('staff.dashboard')
                ->with('success', "Walk-in ticket #{$ticket->id} generated successfully! Balance remaining: ₱".number_format($parsedData['balance_remaining'] ?? 0, 2));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process order form with Groq Vision AI: '.$e->getMessage());
        }
    }
}
