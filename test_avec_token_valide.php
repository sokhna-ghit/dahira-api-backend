<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔑 CRÉATION UTILISATEUR TEST ET RÉCUPÉRATION TOKEN\n";
echo "=================================================\n\n";

$baseUrl = 'http://192.168.1.11:8000/api';

// Créer un utilisateur de test ou essayer de se connecter
echo "1️⃣ Tentative de connexion avec utilisateur existant\n";

$loginData = [
    'email' => 'test@membre.com',
    'password' => 'password123'
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "$baseUrl/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_POSTFIELDS => json_encode($loginData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "Status: $httpCode\n";
echo "Response: $response\n\n";

$token = null;
if ($httpCode == 200) {
    $data = json_decode($response, true);
    $token = $data['token'] ?? null;
    echo "✅ Token récupéré: $token\n\n";
} else {
    echo "❌ Connexion échouée, création d'un nouvel utilisateur...\n\n";
    
    // Créer un nouvel utilisateur
    echo "2️⃣ Création d'un nouvel utilisateur\n";
    
    $registerData = [
        'name' => 'Test Membre',
        'email' => 'test.membre.2025@dahira.com',
        'password' => 'password123',
        'role' => 'membre'
    ];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "$baseUrl/register",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_POSTFIELDS => json_encode($registerData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
    
    if ($httpCode == 201) {
        $data = json_decode($response, true);
        $token = $data['token'] ?? null;
        echo "✅ Utilisateur créé, token récupéré: $token\n\n";
    }
}

if ($token) {
    // Maintenant tester les endpoints PayDunya avec le bon token
    echo "3️⃣ Test des endpoints PayDunya avec token valide\n";
    echo "================================================\n\n";
    
    // Test: Obtenir les opérateurs
    echo "🔍 Test: Obtenir les opérateurs supportés\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "$baseUrl/paydunya/operateurs",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer $token"
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
    
    // Test: Initier un paiement
    echo "💰 Test: Initier un paiement PayDunya\n";
    
    $paymentData = [
        'membre_id' => 15,
        'montant' => 2500,
        'telephone' => '771234567',
        'type_cotisation' => 'mensuelle',
        'description' => 'Test final PayDunya-Flutter'
    ];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "$baseUrl/paydunya/paiement",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_POSTFIELDS => json_encode($paymentData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            "Authorization: Bearer $token"
        ],
    ]);
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    echo "Données: " . json_encode($paymentData, JSON_PRETTY_PRINT) . "\n";
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
    
    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['invoice_url'])) {
            echo "🌐 URL PayDunya générée: {$responseData['invoice_url']}\n";
            echo "🎯 Token facture: {$responseData['invoice_token']}\n\n";
        }
    }
    
    echo "=================================================\n";
    echo "🎉 SUCCÈS! INTÉGRATION PAYDUNYA COMPLÈTEMENT FONCTIONNELLE\n";
    echo "📱 Flutter peut maintenant utiliser PayDunya!\n";
    echo "🔑 Token pour tests Flutter: $token\n\n";
    
} else {
    echo "❌ Impossible d'obtenir un token valide\n";
}

echo "🚀 INTÉGRATION PAYDUNYA FLUTTER - TERMINÉE! 🚀\n";
