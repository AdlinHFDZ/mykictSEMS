<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
public function ask(Request $request)
{
    $prompt = $request->input('prompt');
    $escapedPrompt = escapeshellarg($prompt);

    try {
        $output = shell_exec("php app/Helpers/ollama_proxy.php $escapedPrompt");
        $json = json_decode($output, true);

        return response()->json([
            'reply' => $json['response'] ?? 'No response from AI.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'reply' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}
