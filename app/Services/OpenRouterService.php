<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key', env('OPENROUTER_API_KEY'));
        $this->baseUrl = 'https://openrouter.ai/api/v1/chat/completions';
    }

    public function ask(string $userPrompt, string $systemPrompt = ''): string
    {
        if (empty($this->apiKey)) {
            Log::error('OpenRouter Service: API Key is missing.');
            return "I'm having trouble connecting right now. Please try again in a moment!";
        }

        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => config('app.url', 'http://localhost'),
                'X-Title'       => 'Masaganang Pagpapala Chat Assistant',
            ])->timeout(15)->post($this->baseUrl, [
                'model'    => 'nvidia/nemotron-3.5-super-vl:free', // or your preferred model
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['choices'][0]['message']['content'] ?? "How can I help you with your pasalubong orders today?";
            }

            Log::error('OpenRouter API Error Response: ' . $response->body());
            return "I'm Jive, your AI assistant! How can I help you with your order or questions today?";

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Exception: ' . $e->getMessage());
            return "Hello! How can I assist you with your pasalubong orders today?";
        }
    }
}