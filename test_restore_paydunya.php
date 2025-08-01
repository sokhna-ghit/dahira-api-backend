<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

echo "🧪 TEST PAYDUNYA RESTORATION\n";
echo "================================\n\n";

// Configuration exacte qui marchait ce matin (d'après les logs)
$masterKey = 'qczhhmXn-EbS6-9tjC-7BC1-kxXyIxrWhliv';
$privateKey = 'test_private_Bm9BsyWN2tvpxRxRs4tuRCmWnxf';
$token = 'ByXkU9c4vB8yg9jkqRkU';
$baseUrl = 'https://app.paydunya.com/sandbox-api';

echo "✅ Configuration utilisée:\n";
echo "- Base URL: $baseUrl\n";
echo "- Master Key: $masterKey\n";
echo "- Private Key: $privateKey\n";
echo "- Token: $token\n\n";

// Headers identiques aux logs
$headers = [
    'PAYDUNYA-MASTER-KEY' => $masterKey,
    'PAYDUNYA-PRIVATE-KEY' => $privateKey,
    'PAYDUNYA-TOKEN' => $token,
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
];

// Données identiques aux logs (qui marchaient ce matin)
$testData = [
    'invoice' => [
        'total_amount' => 4000.0,
        'description' => 'Cotisation evenement'
    ],
    'store' => [
        'name' => 'Dahira Management System',
        'tagline' => 'Système de gestion des cotisations',
        'phone' => '+221701234567',
        'postal_address' => 'Dakar, Sénégal',
        'website_url' => 'http://localhost'
    ],
    'custom_data' => [
        'membre_id' => 15,
        'reference' => 'DAHIRA_15_' . time(),
        'type_cotisation' => 'evenement',
        'operateur_detecte' => 'Orange Money'
    ],
    'actions' => [
        'cancel_url' => 'http://localhost/payment/cancel',
        'return_url' => 'http://localhost/payment/success',
        'callback_url' => 'http://localhost/api/paydunya/callback'
    ],
    'mode' => 'orange-money-senegal'
];

echo "🔄 Données de test (identiques à celles qui marchaient):\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

$url = $baseUrl . '/v1/checkout-invoice/create';
echo "🚀 URL complète: $url\n\n";

try {
    echo "📤 Envoi de la requête...\n";
    
    $response = Http::withHeaders($headers)
        ->timeout(30)
        ->post($url, $testData);
    
    echo "📥 Réponse reçue:\n";
    echo "- Status Code: " . $response->status() . "\n";
    echo "- Content-Type: " . $response->header('content-type') . "\n";
    
    $body = $response->body();
    $isJson = str_contains($response->header('content-type'), 'application/json');
    
    if ($isJson) {
        $jsonData = $response->json();
        echo "- JSON Response:\n" . json_encode($jsonData, JSON_PRETTY_PRINT) . "\n";
        
        if (isset($jsonData['response_code'])) {
            echo "- Response Code PayDunya: " . $jsonData['response_code'] . "\n";
            
            if ($jsonData['response_code'] == '00') {
                echo "🎉 SUCCESS! PayDunya fonctionne!\n";
                echo "- Token: " . ($jsonData['token'] ?? 'N/A') . "\n";
                echo "- URL: " . ($jsonData['response_text'] ?? 'N/A') . "\n";
            } else {
                echo "❌ Erreur PayDunya: " . ($jsonData['response_text'] ?? 'Unknown') . "\n";
            }
        }
    } else {
        echo "❌ Réponse non-JSON (HTML error page):\n";
        echo substr($body, 0, 500) . "...\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "🏁 Test terminé\n";
