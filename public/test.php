<?php
$ch = curl_init('http://localhost:11434/api/generate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => 'llama3',
    'prompt' => 'Hello from test.php',
    'stream' => false
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo "<pre>";
echo "RESPONSE:\n$response\n\n";
echo "ERROR:\n$error\n";
echo "</pre>";
?>
