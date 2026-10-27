<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VisionController extends Controller
{
    public function extractColors(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:15360',
        ]);

        try {
            $image = $request->file('image');

            // Save the original uploaded image directly to public storage
            $filename = 'cake_'.Str::random(10).'.'.$image->getClientOriginalExtension();
            Storage::disk('public')->put('custom_orders/'.$filename, file_get_contents($image->getRealPath()));
            $imageUrl = asset('storage/custom_orders/'.$filename);

            // --- SEND DIRECTLY TO GROQ FOR VISION ANALYSIS ---
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            $apiKey = config('services.groq.api_key');

            if (! $apiKey) {
                throw new \Exception('Groq API key is missing in the .env file.');
            }

            $prompt = 'You are an expert, highly precise bakery and cake analysis AI vision model.

            STEP 1: VALIDATION
            First, verify if the image is a cake, pastry, or related baking item. If the image is unrelated, explicit, or inappropriate, return EXACTLY this JSON and stop:
            {"success": false, "error": "invalid_content"}

            STEP 2: CAKE ANALYSIS
            If the image IS a valid cake or pastry, analyze it carefully and return ONLY a raw JSON object. Do not include markdown formatting, code blocks (no ```json), or conversational text.

            CRITICAL ANTI-HALLUCINATION RULES:
            1. theme: Identify the specific pop-culture franchise, character, or general aesthetic. DO NOT GUESS or hallucinate franchises. Analyze logos and characters carefully (e.g., do not confuse a red God of War Omega symbol with a car tire). If you are not 100% certain of the exact intellectual property, use a generic descriptive theme (e.g., "Red and Black Graphic Theme", "Floral Minimalist", "Video Game Theme").
            2. description: Provide a literal 1-2 sentence visual description of the cake. Describe exact colors, textures, and visible toppers (e.g., "White frosted cake with red splatter and a character cutout"). Do not invent or assume details that are not clearly visible.
            3. shape: Must be exactly one of: "Round", "Square", or "Rectangle".
            4. height: Estimate the height of the main tier. Must be exactly one of: "4 inches", "5 inches", or "6 inches".
            5. colors: Extract the prominent colors and provide standard 6-character hex codes (#XXXXXX).
            6. messages: Extract any readable text written on the cake, topper, or cake board.

            EXPECTED EXACT JSON STRUCTURE:
            {
              "success": true,
              "theme": "Identified Theme or Generic Fallback",
              "description": "Literal visual description of the design and toppers.",
              "colors": {
                "base": "#HEXVAL",
                "top": "#HEXVAL",
                "border": "#HEXVAL",
                "sprinkles": "#HEXVAL"
              },
              "shape": "Round",
              "height": "4 inches",
              "messages": [
                {"text": "Text or topper 1", "color": "#HEXVAL"}
              ]
            }';

            $groqResponse = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])
                ->retry(3, 2000, function ($exception, $request) {
                    return $exception instanceof RequestException
                        && $exception->response->serverError();
                })
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'qwen/qwen3.8-27b',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $prompt],
                                ['type' => 'image_url', 'image_url' => ['url' => 'data:'.$mimeType.';base64,'.$imageData]],
                            ],
                        ],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1,
                ]);

            if ($groqResponse->failed()) {
                throw new \Exception('Groq API Error: '.$groqResponse->body());
            }

            $responseData = $groqResponse->json();
            $aiOutput = $responseData['choices'][0]['message']['content'] ?? null;

            if (! $aiOutput) {
                throw new \Exception('Could not parse the response from Groq.');
            }

            $cleanJson = trim(str_replace(['```json', '```'], '', $aiOutput));
            $decodedJson = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON returned by Groq: '.json_last_error_msg());
            }

            // Return saved image URL and AI extraction results
            if (isset($decodedJson['success']) && $decodedJson['success'] === true) {
                $decodedJson['processed_image_url'] = $imageUrl;
            }

            return response()->json($decodedJson);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
