<?php
// app/Helpers/ollama_proxy.php

$prompt = $argv[1] ?? 'Hello from Laravel';
$body = json_encode([
    'model' => 'llama3',
    'prompt' => $prompt,
    'stream' => false
]);

$ch = curl_init('http://127.0.0.1:11434/api/generate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['error' => $error]);
} else {
    echo $response;
}
