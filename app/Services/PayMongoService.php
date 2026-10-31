<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayMongoService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl = "https://api.paymongo.com/v1";

    public function __construct()
    {
        $this->secretKey = config("services.paymongo.secret_key");
        $this->publicKey = config("services.paymongo.public_key");
    }

    public function createPaymentIntent(array $data): array
    {
        $amount = (int) ($data["amount"] * 100);

        $payload = [
            "data" => [
                "attributes" => [
                    "amount" => $amount,
                    "currency" => "PHP",
                    "payment_method_allowed" => ["card", "gcash", "paymaya", "grabpay"],
                    "payment_method_options" => [
                        "card" => [
                            "request_three_d_secure" => "any",
                        ],
                    ],
                    "description" => $data["description"] ?? "Order Payment",
                    "metadata" => [
                        "order_id" => $data["order_id"] ?? null,
                        "user_id" => $data["user_id"] ?? null,
                        "items" => json_encode($data["items"] ?? []),
                    ],
                ],
            ],
        ];

        $response = $this->post("/payment_intents", $payload);

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "payment_intent_id" => $result["data"]["id"],
                "client_key" => $result["data"]["attributes"]["client_key"],
                "amount" => $result["data"]["attributes"]["amount"] / 100,
                "currency" => $result["data"]["attributes"]["currency"],
                "status" => $result["data"]["attributes"]["status"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to create payment intent",
        ];
    }

    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        $response = $this->get("/payment_intents/{$paymentIntentId}");

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "data" => $result["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to retrieve payment intent",
        ];
    }

    public function createPaymentMethod(array $data): array
    {
        $payload = [
            "data" => [
                "attributes" => [
                    "type" => $data["type"] ?? "card",
                    "details" => $data["details"] ?? [],
                    "billing" => $data["billing"] ?? null,
                ],
            ],
        ];

        $response = $this->post("/payment_methods", $payload);

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "payment_method_id" => $result["data"]["id"],
                "type" => $result["data"]["attributes"]["type"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to create payment method",
        ];
    }

    public function attachPaymentMethod(string $paymentIntentId, string $paymentMethodId): array
    {
        $payload = [
            "data" => [
                "attributes" => [
                    "payment_method" => $paymentMethodId,
                ],
            ],
        ];

        $response = $this->post("/payment_intents/{$paymentIntentId}/attach", $payload);

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "data" => $result["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to attach payment method",
        ];
    }

    public function createCheckoutSession(array $data): array
    {
        $lineItems = [];
        foreach ($data["items"] as $item) {
            $lineItems[] = [
                "currency" => "PHP",
                "amount" => (int) ($item["price"] * 100),
                "name" => $item["name"],
                "quantity" => $item["quantity"],
                "description" => $item["description"] ?? "",
            ];
        }

        $payload = [
            "data" => [
                "attributes" => [
                    "line_items" => $lineItems,
                    "payment_method_types" => ["card", "gcash", "paymaya", "grabpay"],
                    "success_url" => $data["success_url"] ?? route("checkout.success"),
                    "cancel_url" => $data["cancel_url"] ?? route("checkout.cancel"),
                    "description" => $data["description"] ?? "Order Payment",
                    "metadata" => [
                        "order_id" => $data["order_id"] ?? null,
                        "user_id" => $data["user_id"] ?? null,
                    ],
                ],
            ],
        ];

        $response = $this->post("/checkout_sessions", $payload);

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "checkout_session_id" => $result["data"]["id"],
                "checkout_url" => $result["data"]["attributes"]["checkout_url"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to create checkout session",
        ];
    }

    public function retrieveCheckoutSession(string $checkoutSessionId): array
    {
        $response = $this->get("/checkout_sessions/{$checkoutSessionId}");

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "data" => $result["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to retrieve checkout session",
        ];
    }

    public function handleWebhook(array $payload, string $signature): array
    {
        $webhookSecret = config("services.paymongo.webhook_secret");

        if ($webhookSecret) {
            $expectedSignature = hash_hmac("sha256", json_encode($payload), $webhookSecret);
            if (!hash_equals($expectedSignature, $signature)) {
                return ["success" => false, "error" => "Invalid webhook signature"];
            }
        }

        $eventType = $payload["data"]["attributes"]["type"] ?? null;
        $eventData = $payload["data"]["attributes"]["data"] ?? null;

        Log::info("PayMongo webhook received", [
            "type" => $eventType,
            "data" => $eventData,
        ]);

        return [
            "success" => true,
            "event_type" => $eventType,
            "event_data" => $eventData,
        ];
    }

    public function createRefund(string $paymentIntentId, ?int $amount = null): array
    {
        $payload = [];
        if ($amount) {
            $payload["data"]["attributes"]["amount"] = $amount * 100;
        }

        $response = $this->post("/payment_intents/{$paymentIntentId}/refund", $payload);

        if ($response->successful()) {
            $result = $response->json();
            return [
                "success" => true,
                "data" => $result["data"],
            ];
        }

        return [
            "success" => false,
            "error" => $response->json("errors.0.detail") ?? "Failed to create refund",
        ];
    }

    protected function get(string $endpoint): Response
    {
        return Http::withBasicAuth($this->secretKey, "")
            ->acceptJson()
            ->timeout(30)
            ->get($this->baseUrl . $endpoint);
    }

    protected function post(string $endpoint, array $payload): Response
    {
        return Http::withBasicAuth($this->secretKey, "")
            ->acceptJson()
            ->timeout(30)
            ->post($this->baseUrl . $endpoint, $payload);
    }
}

