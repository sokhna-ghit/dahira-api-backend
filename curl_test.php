<?php

// Test simple sans Laravel
echo "=== TEST CURL SIMPLE ===\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.11:8000/api/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'sokhna@dahira.sn',
    'password' => 'password123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse: $response\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['access_token'])) {
        $token = $data['access_token'];
        echo "Token obtenu!\n";
        
        // Test validation-status
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, "http://192.168.1.11:8000/api/validation-status");
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        
        $validationResponse = curl_exec($ch2);
        $validationCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);
        
        echo "\n--- Validation Status ---\n";
        echo "Code HTTP: $validationCode\n";
        echo "Réponse: $validationResponse\n";
    }
}

?>
