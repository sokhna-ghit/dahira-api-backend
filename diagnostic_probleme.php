<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DIAGNOSTIC COMPLET DU PROBLÈME\n";
echo "================================\n\n";

// Étape 1: Vérifier la base de données
echo "1️⃣ Test connexion base de données...\n";
try {
    $userCount = \App\Models\User::count();
    echo "✅ Base de données connectée - $userCount utilisateurs trouvés\n\n";
} catch (Exception $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Étape 2: Créer un token simple
echo "2️⃣ Création token utilisateur...\n";
try {
    $user = \App\Models\User::first();
    if (!$user) {
        echo "❌ Aucun utilisateur trouvé dans la base\n";
        exit(1);
    }
    
    $user->tokens()->delete();
    $token = $user->createToken('diagnostic')->plainTextToken;
    echo "✅ Token créé: " . substr($token, 0, 20) . "...\n";
    echo "📧 Utilisateur: " . $user->email . "\n\n";
} catch (Exception $e) {
    echo "❌ Erreur création token: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Étape 3: Test API simple (endpoint qui ne dépend pas de PayDunya)
echo "3️⃣ Test endpoint simple...\n";
$baseUrl = 'http://192.168.1.11:8000/api';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/me",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ],
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "URL: $baseUrl/me\n";
echo "Status: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode == 200) {
    echo "✅ API de base fonctionne\n\n";
} else {
    echo "❌ Problème API de base\n\n";
    curl_close($ch);
    exit(1);
}

// Étape 4: Test direct PayDunya service (sans API)
echo "4️⃣ Test PayDunya service directement...\n";
try {
    $paydunyaService = new \App\Services\PaydunyaService();
    $operators = $paydunyaService->getSupportedOperators();
    echo "✅ Service PayDunya fonctionne - " . count($operators) . " opérateurs\n\n";
} catch (Exception $e) {
    echo "❌ Erreur service PayDunya: " . $e->getMessage() . "\n\n";
}

// Étape 5: Test endpoint PayDunya
echo "5️⃣ Test endpoint PayDunya...\n";
curl_setopt($ch, CURLOPT_URL, "$baseUrl/paydunya/operateurs");

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "URL: $baseUrl/paydunya/operateurs\n";
echo "Status: $httpCode2\n";
echo "Response: $response2\n\n";

if ($httpCode2 == 200) {
    echo "✅ Endpoint opérateurs fonctionne\n\n";
} else {
    echo "❌ Problème endpoint opérateurs\n\n";
}

// Étape 6: Test paiement direct sans PayDunya
echo "6️⃣ Test paiement simulation directe...\n";

$simplePayment = [
    'amount' => 1000,
    'phone' => '771234567'
];

curl_setopt($ch, CURLOPT_URL, "$baseUrl/paiement/simule");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($simplePayment));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "URL: $baseUrl/paiement/simule\n";
echo "Data: " . json_encode($simplePayment) . "\n";
echo "Status: $httpCode3\n";
echo "Response: $response3\n\n";

curl_close($ch);

echo "================================\n";
echo "🏁 DIAGNOSTIC TERMINÉ\n\n";

// Résumé
echo "📊 RÉSUMÉ:\n";
echo "- Base de données: " . ($userCount > 0 ? "✅" : "❌") . "\n";
echo "- Token: " . (isset($token) ? "✅" : "❌") . "\n";
echo "- API base: " . ($httpCode == 200 ? "✅" : "❌") . "\n";
echo "- Opérateurs: " . ($httpCode2 == 200 ? "✅" : "❌") . "\n";
echo "- Simulation: " . ($httpCode3 == 200 ? "✅" : "❌") . "\n";

if ($httpCode == 200 && $httpCode3 == 200) {
    echo "\n🎯 LE SYSTÈME DE BASE FONCTIONNE!\n";
    echo "Le problème est peut-être dans Flutter ou la configuration.\n";
    echo "\n💡 TOKEN POUR FLUTTER:\n$token\n";
} else {
    echo "\n❌ PROBLÈMES DÉTECTÉS\n";
    echo "Il faut corriger ces erreurs avant de tester Flutter.\n";
}
