<?php

require_once 'vendor/autoload.php';

// Initialiser l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\PaydunyaService;

echo "🧪 TEST CORRECTION PAYDUNYA\n";
echo "================================\n\n";

try {
    $paydunyaService = new PaydunyaService();
    
    echo "✅ Service PayDunya initialisé\n\n";
    
    // Test 1: Vérifier la configuration
    echo "1️⃣ Configuration PayDunya:\n";
    echo "- Mode: " . config('services.paydunya.mode') . "\n";
    echo "- Base URL: " . config('services.paydunya.base_url') . "\n";
    echo "- Master Key: " . (config('services.paydunya.master_key') ? 'Configuré ✅' : 'Manquant ❌') . "\n";
    echo "- Private Key: " . (config('services.paydunya.private_key') ? 'Configuré ✅' : 'Manquant ❌') . "\n";
    echo "- Public Key: " . (config('services.paydunya.public_key') ? 'Configuré ✅' : 'Manquant ❌') . "\n";
    echo "- Token: " . (config('services.paydunya.token') ? 'Configuré ✅' : 'Manquant ❌') . "\n\n";
    
    // Test 2: Test de validation de numéro
    echo "2️⃣ Test validation numéros:\n";
    $numeros = ['77123456', '781234567', '221771234567', '701234567', '33123456'];
    
    foreach ($numeros as $numero) {
        $isValid = $paydunyaService->validatePhoneNumber($numero);
        $operateur = $paydunyaService->detectOperator($numero);
        $formatted = $paydunyaService->formatPhoneNumber($numero);
        
        echo "- $numero: " . ($isValid ? "✅ Valide" : "❌ Invalide") . " | Opérateur: $operateur | Formaté: $formatted\n";
    }
    
    echo "\n3️⃣ Test opérateurs supportés:\n";
    $operateurs = $paydunyaService->getSupportedOperators();
    foreach ($operateurs as $op) {
        echo "- {$op['logo']} {$op['nom']} ({$op['prefixes']}) - Mode: {$op['paydunya_mode']}\n";
    }
    
    echo "\n4️⃣ Test initiation paiement:\n";
    
    $paymentData = [
        'membre_id' => 15,
        'reference' => 'TEST_DAHIRA_' . time(),
        'telephone' => '221771234567', // Orange Money
        'montant' => 1000,
        'type_cotisation' => 'test',
        'description' => 'Test correction PayDunya'
    ];
    
    echo "Données de test: " . json_encode($paymentData, JSON_PRETTY_PRINT) . "\n\n";
    
    $result = $paydunyaService->initiatePayment($paymentData);
    
    echo "Résultat:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
    if ($result['success']) {
        echo "\n🎉 SUCCESS! PayDunya fonctionne correctement!\n";
        echo "- Token facture: " . ($result['invoice_token'] ?? 'N/A') . "\n";
        echo "- URL paiement: " . ($result['invoice_url'] ?? 'N/A') . "\n";
        echo "- Opérateur: " . ($result['operateur'] ?? 'N/A') . "\n";
    } else {
        echo "\n❌ ÉCHEC PayDunya:\n";
        echo "- Message: " . ($result['message'] ?? 'N/A') . "\n";
        echo "- Code erreur: " . ($result['error_code'] ?? 'N/A') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n================================\n";
echo "🏁 Test terminé\n";
