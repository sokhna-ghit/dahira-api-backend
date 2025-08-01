<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "🧪 TEST ENDPOINTS PAYDUNYA\n";
echo "================================\n\n";

// Clés PayDunya de test
$masterKey = config('services.paydunya.master_key');
$privateKey = config('services.paydunya.private_key');
$token = config('services.paydunya.token');

echo "✅ Configuration récupérée:\n";
echo "- Master Key: " . ($masterKey ? 'OK' : 'MANQUANT') . "\n";
echo "- Private Key: " . ($privateKey ? 'OK' : 'MANQUANT') . "\n";
echo "- Token: " . ($token ? 'OK' : 'MANQUANT') . "\n\n";

// Headers standard PayDunya
$headers = [
    'PAYDUNYA-MASTER-KEY' => $masterKey,
    'PAYDUNYA-PRIVATE-KEY' => $privateKey,
    'PAYDUNYA-TOKEN' => $token,
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
];

// URLs à tester
$urlsToTest = [
    'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create',
    'https://app.paydunya.com/api/v1/checkout-invoice/create',
    'https://sandbox-api.paydunya.com/v1/checkout-invoice/create',
    'https://api.paydunya.com/v1/checkout-invoice/create',
];

// Données de test minimales
$testData = [
    'invoice' => [
        'total_amount' => 1000,
        'description' => 'Test API PayDunya'
    ],
    'store' => [
        'name' => 'Test Store',
        'tagline' => 'Test',
        'phone' => '+221701234567',
        'postal_address' => 'Dakar',
        'website_url' => 'http://localhost'
    ]
];

foreach ($urlsToTest as $index => $url) {
    echo ($index + 1) . "️⃣ Test URL: $url\n";
    
    try {
        $response = Http::withHeaders($headers)
            ->timeout(15)
            ->post($url, $testData);
        
        echo "- Status: " . $response->status() . "\n";
        echo "- Headers: " . json_encode($response->headers()) . "\n";
        
        $body = $response->body();
        $contentType = $response->header('content-type');
        
        echo "- Content-Type: $contentType\n";
        
        if (str_contains($contentType, 'application/json')) {
            $json = $response->json();
            echo "- JSON Response: " . json_encode($json, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($json['response_code'])) {
                echo "- Response Code: " . $json['response_code'] . "\n";
                
                if ($json['response_code'] == '00') {
                    echo "🎉 SUCCESS! Cette URL fonctionne!\n";
                }
            }
        } else {
            echo "- Body (first 200 chars): " . substr($body, 0, 200) . "...\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "🏁 Test terminé\n";
