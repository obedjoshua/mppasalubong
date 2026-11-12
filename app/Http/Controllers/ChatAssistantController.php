<?php

namespace App\Http\Controllers;

use App\Models\OrderTicket;
use App\Models\Product;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class ChatAssistantController extends Controller
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    public function page()
    {
        return view('chat.full_page');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string|in:user,assistant',
            'messages.*.content' => 'required|string|max:1000',
        ]);

        $userMessages = $request->input('messages');
        $latestUserMessage = end($userMessages)['content'] ?? '';
        $latestLower = strtolower(trim($latestUserMessage));
        $userId = Auth::id();

        // =========================================================================
        // TIER 1: STORE PROCESS & BRANCH GUIDANCE INTENTS ("HOW TO" QUESTIONS)
        // =========================================================================
        $isProcessQuery = preg_match('/\b(how|hoe|haw)\s+to\b/', $latestLower) 
            || str_contains($latestLower, 'how do i') 
            || str_contains($latestLower, 'process') 
            || str_contains($latestLower, 'steps')
            || str_contains($latestLower, 'instruction')
            || str_contains($latestLower, 'guide');

        if ($isProcessQuery) {
            if (str_contains($latestLower, 'pay') || str_contains($latestLower, 'payment') || str_contains($latestLower, 'deposit') || str_contains($latestLower, 'downpayment')) {
                return response()->json([
                    'success' => true,
                    'reply'   => "<div style='line-height: 1.6; font-size: 14px;'>"
                        . "<p style='margin-bottom: 8px;'>💳 <strong>Payment & Deposit Process:</strong></p>"
                        . "<ol style='margin: 4px 0 12px 18px; padding: 0;'>"
                        . "<li>Go to <strong>My Orders</strong> and select your approved order ticket.</li>"
                        . "<li>Review the total quote and deposit requirement set by staff.</li>"
                        . "<li>Send payment via GCash, Maya, or Bank Transfer.</li>"
                        . "<li>Upload your receipt screenshot directly on the ticket page.</li>"
                        . "<li>Once staff verifies your receipt, status changes to <strong>In Production</strong>!</li>"
                        . "</ol>"
                        . "<div style='margin-top: 12px;'>"
                        . "<a href='/orders' style='display: inline-block; padding: 8px 14px; background-color: #7c3aed; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>View My Orders &rarr;</a>"
                        . "</div></div>"
                ]);
            }

            if (str_contains($latestLower, 'pickup') || str_contains($latestLower, 'claim') || str_contains($latestLower, 'collect')) {
                return response()->json([
                    'success' => true,
                    'reply'   => "<div style='line-height: 1.6; font-size: 14px;'>"
                        . "<p style='margin-bottom: 8px;'>🏪 <strong>Store Pickup Instructions:</strong></p>"
                        . "<ol style='margin: 4px 0 12px 18px; padding: 0;'>"
                        . "<li>Wait until your ticket status updates to <strong>Ready for Pickup</strong>.</li>"
                        . "<li>Visit your designated branch (Salacot or San Jose) during store hours (8:00 AM – 5:30 PM, Mon-Sat).</li>"
                        . "<li>Show your digital QR code or Ticket ID to our staff counter.</li>"
                        . "<li>Pay any remaining balance and claim your items!</li>"
                        . "</ol>"
                        . "<div style='margin-top: 12px;'>"
                        . "<a href='/orders' style='display: inline-block; padding: 8px 14px; background-color: #7c3aed; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>Check Active Tickets &rarr;</a>"
                        . "</div></div>"
                ]);
            }

            if (str_contains($latestLower, 'cancel') || str_contains($latestLower, 'change') || str_contains($latestLower, 'edit')) {
                return response()->json([
                    'success' => true,
                    'reply'   => "<div style='line-height: 1.6; font-size: 14px;'>"
                        . "<p style='margin-bottom: 8px;'>🚫 <strong>Ticket Cancellation & Edit Guide:</strong></p>"
                        . "<ul style='margin: 4px 0 12px 18px; padding: 0; list-style-type: disc;'>"
                        . "<li>Tickets in <strong>Pending Staff Review</strong> can be edited or cancelled directly from your ticket details page.</li>"
                        . "<li>Tickets currently <strong>Under Review</strong> or <strong>In Production</strong> require staff approval for cancellation.</li>"
                        . "</ul>"
                        . "<div style='margin-top: 12px;'>"
                        . "<a href='/orders' style='display: inline-block; padding: 8px 14px; background-color: #7c3aed; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>Manage My Tickets &rarr;</a>"
                        . "</div></div>"
                ]);
            }

            if (str_contains($latestLower, 'shop') || str_contains($latestLower, 'order') || str_contains($latestLower, 'buy') || str_contains($latestLower, 'place')) {
                return response()->json([
                    'success' => true,
                    'reply'   => "<div style='line-height: 1.6; font-size: 14px;'>"
                        . "<p style='margin-bottom: 8px;'>🛒 <strong>How to Shop & Order Online:</strong></p>"
                        . "<ol style='margin: 4px 0 12px 18px; padding: 0;'>"
                        . "<li>Browse our <strong>Shop Catalog</strong> or create a <strong>Custom Order Ticket</strong>.</li>"
                        . "<li>Select your items, quantity, pickup date, time slot, and preferred branch (Salacot or San Jose).</li>"
                        . "<li>Submit your order ticket for staff review.</li>"
                        . "<li>Once approved and quoted, pay the deposit to lock in your order!</li>"
                        . "</ol>"
                        . "<div style='margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;'>"
                        . "<a href='/shop' style='display: inline-block; padding: 8px 14px; background-color: #7c3aed; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>Browse Catalog &rarr;</a>"
                        . "<a href='/orders/create' style='display: inline-block; padding: 8px 14px; background-color: #f3e8ff; color: #7c3aed; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>Custom Order Ticket</a>"
                        . "</div></div>"
                ]);
            }
        }

        // Branch Info & Operating Hours
        $isBranchQuery = (str_contains($latestLower, 'branch') 
            || str_contains($latestLower, 'location') 
            || str_contains($latestLower, 'hours') 
            || str_contains($latestLower, 'open') 
            || str_contains($latestLower, 'schedule')
            || str_contains($latestLower, 'salacot')
            || str_contains($latestLower, 'san jose'))
            && !str_contains($latestLower, 'ticket') 
            && !str_contains($latestLower, 'order');

        if ($isBranchQuery) {
            return response()->json([
                'success' => true,
                'reply'   => "<div style='line-height: 1.6; font-size: 14px;'>"
                    . "<p style='margin-bottom: 8px;'>🏪 <strong>Masaganang Pagpapala Pasalubong Branches:</strong></p>"
                    . "<ul style='margin: 4px 0 12px 18px; padding: 0; list-style-type: disc;'>"
                    . "<li><strong>Main Branch:</strong> Salacot, San Ildefonso, Bulacan</li>"
                    . "<li><strong>Second Branch:</strong> San Jose, San Ildefonso, Bulacan</li>"
                    . "</ul>"
                    . "<p style='margin-bottom: 8px;'>⏰ <strong>Operating Hours:</strong><br>Monday to Saturday: <strong>8:00 AM – 5:30 PM</strong> (Closed Sundays)</p>"
                    . "<div style='margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap;'>"
                    . "<a href='/orders/create' style='display: inline-block; padding: 8px 14px; background-color: #7c3aed; color: #ffffff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px;'>Create Order Ticket &rarr;</a>"
                    . "</div></div>"
            ]);
        }

        // =========================================================================
        // TIER 2: DIRECT DATABASE USER TICKETS & ORDERS LOOKUP
        // =========================================================================
        $extractedId = null;
        if (preg_match('/#([A-Za-z0-9_-]{1,64})/', $latestUserMessage, $matches)) {
            $extractedId = $matches[1];
        }

        $isApprovedQuery = str_contains($latestLower, 'approve') 
            || str_contains($latestLower, 'paym') 
            || str_contains($latestLower, 'paid');
            
        $isPendingQuery = str_contains($latestLower, 'review') 
            || str_contains($latestLower, 'pending');

        $isReadyQuery = str_contains($latestLower, 'ready') 
            || str_contains($latestLower, 'completed');

        $isTicketIntent = $extractedId !== null 
            || preg_match('/\b(ticket|tickets|order|orders|status|quote|history)\b/i', $latestLower)
            || $isApprovedQuery
            || $isPendingQuery
            || $isReadyQuery;

        if ($isTicketIntent) {
            if (! $userId) {
                return response()->json([
                    'success' => true,
                    'reply'   => "Please <a href='/login' style='color:#7c3aed; font-weight:600;'>log in</a> to your account so I can retrieve your order tickets from our database!"
                ]);
            }

            try {
                // 2A. Specific Ticket ID / Number / QR Token Search
                if ($extractedId) {
                    $cleanId = ltrim($extractedId, '#');
                    $specificTicket = OrderTicket::where('user_id', $userId)
                        ->where(function ($q) use ($cleanId) {
                            $q->where('id', $cleanId)
                              ->orWhere('qr_token', $cleanId);

                            $table = $q->getModel()->getTable();
                            foreach (['ticket_number', 'ticket_code', 'reference_number', 'order_number'] as $col) {
                                if (Schema::hasColumn($table, $col)) {
                                    $q->orWhere($col, $cleanId)
                                      ->orWhere($col, '#' . $cleanId);
                                }
                            }
                        })
                        ->with('items')
                        ->first();

                    if ($specificTicket) {
                        return response()->json([
                            'success' => true,
                            'reply'   => $this->buildSingleTicketSummary($specificTicket)
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'reply'   => "I couldn't find ticket <strong>#{$cleanId}</strong> under your account. You can view all your tickets on the <a href='/orders' style='color:#7c3aed; font-weight:600;'>My Orders</a> page."
                    ]);
                }

                // 2B. Status-Filtered Database Query for Logged-In User
                $query = OrderTicket::where('user_id', $userId)->with('items');
                $hasStatusFilter = false;
                $statusLabel = '';

                if ($isApprovedQuery) {
                    $query->where('status', 'like', '%approved%');
                    $hasStatusFilter = true;
                    $statusLabel = 'Approved for Payment';
                } elseif ($isPendingQuery) {
                    $query->where(function ($q) {
                        $q->where('status', 'like', '%pending%')
                          ->orWhere('status', 'like', '%review%');
                    });
                    $hasStatusFilter = true;
                    $statusLabel = 'Pending Staff Review';
                } elseif ($isReadyQuery) {
                    $query->where(function ($q) {
                        $q->where('status', 'like', '%ready%')
                          ->orWhere('status', 'like', '%complete%');
                    });
                    $hasStatusFilter = true;
                    $statusLabel = 'Ready / Completed';
                }

                $userTickets = $query->orderBy('created_at', 'desc')->get();

                if ($userTickets->isEmpty() && $hasStatusFilter) {
                    return response()->json([
                        'success' => true,
                        'reply'   => "You currently have no order tickets marked as <strong>{$statusLabel}</strong>."
                    ]);
                }

                if ($userTickets->isEmpty()) {
                    $userTickets = OrderTicket::where('user_id', $userId)
                        ->with('items')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }

                if ($userTickets->count() === 1) {
                    return response()->json([
                        'success' => true,
                        'reply'   => $this->buildSingleTicketSummary($userTickets->first())
                    ]);
                } elseif ($userTickets->count() > 1) {
                    $ticketsText = "Here are your order tickets from our database:<br><br>";
                    foreach ($userTickets as $t) {
                        $ticketsText .= $this->formatTicketCard($t) . "<br><br>";
                    }
                    return response()->json([
                        'success' => true,
                        'reply'   => trim($ticketsText)
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'reply'   => "You don't have any order tickets in your account yet. <a href='/orders/create' style='color:#7c3aed; font-weight:600;'>Create one here &rarr;</a>"
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('ChatAssistant Ticket Query Error: ' . $e->getMessage());
                return response()->json([
                    'success' => true,
                    'reply'   => "We encountered an issue reading your tickets from the database. Please check the <a href='/orders' style='color:#7c3aed; font-weight:600;'>My Orders</a> page directly."
                ]);
            }
        }

        // =========================================================================
        // TIER 3: DIRECT DATABASE SHOP CATALOG LOOKUP
        // =========================================================================
        $isProductQuery = preg_match('/\b(product|products|item|items|catalog|shop|store|sell|selling|menu|available|availb|pasalubong|roll|cake|cookie|package|wedding)\b/i', $latestLower)
            || str_contains($latestLower, 'what do you')
            || str_contains($latestLower, 'chocovron')
            || str_contains($latestLower, 'polvoron');

        if ($isProductQuery) {
            try {
                $products = Product::where(function ($q) {
                        $q->whereNull('stock_status')
                          ->orWhere('stock_status', '!=', 'Out of Stock');
                    })
                    ->orWhere('stock_quantity', '>', 0)
                    ->get();

                if ($products->isEmpty()) {
                    $products = Product::all();
                }

                if ($products->count() > 0) {
                    $productOutput = "Here are the products currently available in our shop:<br><br>";
                    foreach ($products as $prod) {
                        $categoryName = ucwords($prod->category ?? 'Pasalubong');
                        $statusNote = ($prod->stock_quantity > 0 && $prod->stock_status !== 'Out of Stock')
                            ? 'Available to order'
                            : 'Check availability';
                        $desc = "{$categoryName} • {$statusNote}";
                        $productUrl = Route::has('shop.product')
                            ? route('shop.product', $prod->slug)
                            : "/shop/{$prod->slug}";
                        
                        $productOutput .= "[PRODUCT_CARD]\n"
                            . "Name: {$prod->name}\n"
                            . "Price: ₱" . number_format((float) $prod->price, 2) . "\n"
                            . "Branch: Salacot & San Jose\n"
                            . "Description: {$desc}\n"
                            . "Link: {$productUrl}\n"
                            . "[END_PRODUCT_CARD]\n\n";
                    }

                    return response()->json([
                        'success' => true,
                        'reply'   => trim($productOutput)
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('ChatAssistant Shop Query Error: ' . $e->getMessage());
            }
        }

        // =========================================================================
        // TIER 4: OPENROUTER LLM FALLBACK WITH FULL LIVE DB CONTEXT INJECTION
        // =========================================================================
        $dbContext = $this->buildLiveDatabaseContext($userId);

        $systemPrompt = <<<EOT
You are Jive, the virtual AI assistant for Masaganang Pagpapala Pasalubong.
Answer the user's questions warmly, accurately, and concisely using the live database records below.

{$dbContext}

STORE INFORMATION:
- Main Branch: Salacot, San Ildefonso, Bulacan
- Second Branch: San Jose, San Ildefonso, Bulacan
- Operating Hours: Monday to Saturday, 8:00 AM – 5:30 PM (Closed Sundays).

STRICT GUARDRAILS:
- ONLY reference products and user tickets listed in the LIVE DATABASE RECORDS above.
- NEVER invent, guess, or fabricate ticket numbers, prices, or [TICKET_CARD] blocks.
- Use clean HTML formatting (<br>, <strong>) if needed, never raw Markdown asterisks (**).
EOT;

        try {
            $reply = $this->openRouter->ask($latestUserMessage, $systemPrompt);

            return response()->json([
                'success' => true,
                'reply'   => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('OpenRouter Chat Assistant Error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'reply'   => "Hello! I'm Jive, your AI assistant for Masaganang Pagpapala Pasalubong. How can I help you with our products or your order tickets today? 🧁",
            ]);
        }
    }

    /**
     * Reads live Products and logged-in User's OrderTickets from DB to inject into LLM context
     */
    private function buildLiveDatabaseContext(?int $userId): string
    {
        $context = "=== LIVE DATABASE RECORDS ===\n\n";

        // 1. Live Shop Products
        $context .= "SHOP PRODUCTS:\n";
        try {
            $products = Product::all();
            if ($products->isEmpty()) {
                $context .= "- No products currently listed in the database.\n";
            } else {
                foreach ($products as $p) {
                    $stock = ($p->stock_quantity > 0 && $p->stock_status !== 'Out of Stock') ? 'In Stock' : 'Out of Stock';
                    $context .= "- {$p->name} | Price: ₱" . number_format((float) $p->price, 2) . " | Category: {$p->category} | Status: {$stock} | URL: /shop/{$p->slug}\n";
                }
            }
        } catch (\Exception $e) {
            $context .= "- Unable to load product table.\n";
        }

        // 2. Logged-In User's Real Order Tickets
        $context .= "\nCURRENT USER'S ORDER TICKETS:\n";
        if (! $userId) {
            $context .= "- User is NOT logged in (Guest). No personal tickets accessible.\n";
        } else {
            try {
                $user = Auth::user();
                $context .= "Logged-in User: " . ($user->name ?? "User #{$userId}") . "\n";

                $tickets = OrderTicket::where('user_id', $userId)
                    ->with('items')
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($tickets->isEmpty()) {
                    $context .= "- This user has 0 order tickets in the database.\n";
                } else {
                    foreach ($tickets as $t) {
                        $cleanId = $this->getCleanTicketId($t);
                        $status = strtoupper(str_replace('_', ' ', $t->status ?? 'PENDING STAFF REVIEW'));
                        $branch = ucwords(str_replace(['_', '-'], ' ', $t->pickup_branch ?? 'Main Shop'));
                        $date = $t->pickup_date ?? 'N/A';
                        $time = $t->pickup_time ?? 'N/A';
                        $quote = ($t->total_price && $t->total_price > 0) ? '₱' . number_format((float) $t->total_price, 2) : 'Awaiting Quote';

                        $itemsArr = [];
                        foreach ($t->items as $item) {
                            $itemsArr[] = "{$item->quantity}x " . ucwords(str_replace(['_', '-'], ' ', $item->item_type));
                        }
                        $itemsSummary = count($itemsArr) > 0 ? implode(', ', $itemsArr) : 'General Inquiry';

                        $context .= "- Ticket #{$cleanId} | Status: {$status} | Items: [{$itemsSummary}] | Branch: {$branch} | Pickup: {$date} {$time} | Quote: {$quote}\n";
                    }
                }
            } catch (\Exception $e) {
                $context .= "- Error loading user tickets from database.\n";
            }
        }

        return $context;
    }

    /**
     * Returns the exact ticket identifier stored in your database without modifying/slicing it
     */
    private function getCleanTicketId(OrderTicket $t): string
    {
        foreach (['ticket_number', 'ticket_code', 'reference_number', 'order_number'] as $col) {
            if (!empty($t->{$col})) {
                return ltrim((string) $t->{$col}, '#');
            }
        }

        return (string) $t->id;
    }

    private function formatTicketCard(OrderTicket $t): string
    {
        $displayId = $this->getCleanTicketId($t);

        $firstItem = $t->items->first();
        $rawItemType = $firstItem->item_type ?? $t->additional_notes ?? "Pasalubong Order";
        
        $subject = ucwords(str_replace(['_', '-'], ' ', $rawItemType));
        $statusStr = strtoupper(str_replace('_', ' ', $t->status ?? 'PENDING STAFF REVIEW'));
        $dateStr = $t->pickup_date 
            ? \Carbon\Carbon::parse($t->pickup_date)->format('M d, Y') 
            : ($t->created_at ? $t->created_at->format('M d, Y') : date('M d, Y'));

        $ticketUrl = Route::has('orders.show')
            ? route('orders.show', $t)
            : "/orders/{$t->getRouteKey()}";

        return "[TICKET_CARD]\n"
            . "ID: #{$displayId}\n"
            . "Subject: {$subject}\n"
            . "Status: {$statusStr}\n"
            . "Date: {$dateStr}\n"
            . "Link: {$ticketUrl}\n"
            . "[END_TICKET_CARD]";
    }

    private function buildSingleTicketSummary(OrderTicket $t): string
    {
        $displayId = $this->getCleanTicketId($t);
        $statusStr = strtoupper(str_replace('_', ' ', $t->status ?? 'PENDING STAFF REVIEW'));
        $branch = ucwords(str_replace(['_', '-'], ' ', $t->pickup_branch ?? 'Main Shop'));
        $pickupDate = $t->pickup_date ? \Carbon\Carbon::parse($t->pickup_date)->format('M d, Y') : 'N/A';
        $pickupTime = $t->pickup_time ?? 'N/A';
        
        $itemDetails = [];
        foreach ($t->items as $item) {
            $formattedName = ucwords(str_replace(['_', '-'], ' ', $item->item_type));
            $itemDetails[] = "<li><strong>{$item->quantity}x</strong> {$formattedName}</li>";
        }
        $itemListHtml = count($itemDetails) > 0 
            ? "<ul style='margin: 4px 0 8px 18px; padding: 0; list-style-type: disc;'>" . implode('', $itemDetails) . "</ul>" 
            : "<div style='margin-left: 10px;'>• 1x Order Inquiry</div>";

        $priceStr = ($t->total_price && $t->total_price > 0) 
            ? "₱" . number_format((float) $t->total_price, 2) 
            : "Awaiting Quote";

        return "<div style='line-height: 1.6; font-size: 14px;'>"
            . "<p style='margin-bottom: 8px;'>Here are the details for ticket <strong>#{$displayId}</strong>:</p>"
            . "<div>📌 <strong>Status:</strong> {$statusStr}</div>"
            . "<div>📦 <strong>Items:</strong></div>{$itemListHtml}"
            . "<div>🏪 <strong>Pickup Branch:</strong> {$branch}</div>"
            . "<div>📅 <strong>Pickup Schedule:</strong> {$pickupDate} at {$pickupTime}</div>"
            . "<div style='margin-bottom: 12px;'>💰 <strong>Total Quote:</strong> {$priceStr}</div>"
            . "</div>"
            . $this->formatTicketCard($t);
    }
}