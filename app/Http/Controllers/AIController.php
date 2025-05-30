<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
public function ask(Request $request)
{
    $prompt = $request->input('prompt');

    $body = [
        'model' => 'llama3',
        'prompt' => $prompt,
        'stream' => false,
    ];

    // Use Laravel HTTP client (best practice)
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(20)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('http://localhost:11434/api/generate', $body);

        $json = $response->json();
        return response()->json([
            'reply' => $json['response'] ?? 'No response from AI.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'reply' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}


}
