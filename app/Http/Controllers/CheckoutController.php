<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected PayMongoService $payMongo;

    public function __construct(PayMongoService $payMongo)
    {
        $this->payMongo = $payMongo;
    }

    public function cart(): View
    {
        $cart = Session::get("cart", []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = Product::find($item["product_id"]);
            if ($product) {
                $lineTotal = $product->price * $item["quantity"];
                $subtotal += $lineTotal;
                $cartItems[] = [
                    "product" => $product,
                    "quantity" => $item["quantity"],
                    "line_total" => $lineTotal,
                ];
            }
        }

        return view("checkout.cart", compact("cartItems", "subtotal"));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:1",
        ]);

        $cart = Session::get("cart", []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            $cart[$productId]["quantity"] += $request->quantity;
        } else {
            $cart[$productId] = [
                "product_id" => $productId,
                "quantity" => $request->quantity,
            ];
        }

        Session::put("cart", $cart);

        return response()->json([
            "success" => true,
            "cart_count" => array_sum(array_column($cart, "quantity")),
            "message" => "Product added to cart",
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:0",
        ]);

        $cart = Session::get("cart", []);
        $productId = $request->product_id;

        if ($request->quantity > 0) {
            $cart[$productId]["quantity"] = $request->quantity;
        } else {
            unset($cart[$productId]);
        }

        Session::put("cart", $cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item["product_id"]);
            if ($product) {
                $subtotal += $product->price * $item["quantity"];
            }
        }

        return response()->json([
            "success" => true,
            "cart_count" => array_sum(array_column($cart, "quantity")),
            "subtotal" => $subtotal,
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            "product_id" => "required|exists:products,id",
        ]);

        $cart = Session::get("cart", []);
        unset($cart[$request->product_id]);
        Session::put("cart", $cart);

        return response()->json([
            "success" => true,
            "cart_count" => array_sum(array_column($cart, "quantity")),
        ]);
    }

    public function checkout(): View
    {
        $cart = Session::get("cart", []);
        if (empty($cart)) {
            return redirect()->route("shop")->with("error", "Your cart is empty");
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $product = Product::find($item["product_id"]);
            if ($product) {
                $lineTotal = $product->price * $item["quantity"];
                $subtotal += $lineTotal;
                $cartItems[] = [
                    "product" => $product,
                    "quantity" => $item["quantity"],
                    "line_total" => $lineTotal,
                ];
            }
        }

        return view("checkout.checkout", compact("cartItems", "subtotal"));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            "customer_name" => "required|string|max:255",
            "customer_email" => "required|email|max:255",
            "customer_phone" => "required|string|max:20",
            "customer_address" => "required|string",
            "pickup_branch" => "required|in:main_shop,san_jose",
            "pickup_date" => "required|date|after_or_equal:today",
            "pickup_time" => "required|string",
        ]);

        $cart = Session::get("cart", []);
        if (empty($cart)) {
            return back()->with("error", "Your cart is empty");
        }

        $items = [];
        $totalAmount = 0;
        $productIds = [];

        foreach ($cart as $item) {
            $product = Product::find($item["product_id"]);
            if ($product) {
                $lineTotal = $product->price * $item["quantity"];
                $totalAmount += $lineTotal;
                $items[] = [
                    "name" => $product->name,
                    "price" => (float) $product->price,
                    "quantity" => $item["quantity"],
                    "description" => $product->category,
                ];
                $productIds[] = $product->id;
            }
        }

        $checkoutData = [
            "items" => $items,
            "success_url" => route("checkout.success"),
            "cancel_url" => route("checkout.cancel"),
            "description" => "Order from Masaganang Pagpapala",
            "order_id" => null,
            "user_id" => Auth::id(),
        ];

        $result = $this->payMongo->createCheckoutSession($checkoutData);

        if (!$result["success"]) {
            return back()->with("error", "Payment initialization failed: " . $result["error"]);
        }

        Session::put("pending_checkout", [
            "checkout_session_id" => $result["checkout_session_id"],
            "customer_data" => $request->only([
                "customer_name",
                "customer_email",
                "customer_phone",
                "customer_address",
                "pickup_branch",
                "pickup_date",
                "pickup_time",
            ]),
            "items" => $items,
            "total_amount" => $totalAmount,
            "product_ids" => $productIds,
        ]);

        return redirect($result["checkout_url"]);
    }

    public function success(Request $request)
    {
        $pendingCheckout = Session::get("pending_checkout");

        if (!$pendingCheckout) {
            return redirect()->route("shop")->with("error", "No pending checkout found");
        }

        $sessionResult = $this->payMongo->retrieveCheckoutSession($pendingCheckout["checkout_session_id"]);

        if (!$sessionResult["success"]) {
            return redirect()->route("shop")->with("error", "Failed to verify payment");
        }

        $checkoutSession = $sessionResult["data"];
        $paymentStatus = $checkoutSession["attributes"]["payment_status"] ?? null;

        if ($paymentStatus !== "paid") {
            return redirect()->route("checkout.cancel")->with("error", "Payment was not completed");
        }

        $purchase = Purchase::create([
            "user_id" => Auth::id(),
            "product_id" => $pendingCheckout["product_ids"][0] ?? null,
            "quantity" => array_sum(array_column($pendingCheckout["items"], "quantity")),
            "total_price" => $pendingCheckout["total_amount"],
            "status" => "completed",
        ]);

        Session::forget("cart");
        Session::forget("pending_checkout");

        return view("checkout.success", compact("purchase", "pendingCheckout"));
    }

    public function cancel(): View
    {
        Session::forget("pending_checkout");
        return view("checkout.cancel");
    }

    public function webhook(Request $request)
    {
        $signature = $request->header("Paymongo-Signature");
        $payload = $request->all();

        $result = $this->payMongo->handleWebhook($payload, $signature);

        if (!$result["success"]) {
            Log::warning("PayMongo webhook verification failed", ["error" => $result["error"]]);
            return response()->json(["error" => "Invalid signature"], 400);
        }

        Log::info("PayMongo webhook processed", $result);

        return response()->json(["status" => "ok"]);
    }
}

