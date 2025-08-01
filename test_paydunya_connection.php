<?php

require_once 'vendor/autoload.php';

use App\Services\PaydunyaService;
use Illuminate\Support\Facades\Log;

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 TEST CONNEXION PAYDUNYA\n";
echo "================================\n\n";

try {
    $paydunyaService = new PaydunyaService();
    
    echo "📋 Configuration PayDunya:\n";
    echo "- Mode: " . config('services.paydunya.mode') . "\n";
    echo "- Master Key: " . substr(config('services.paydunya.master_key'), 0, 10) . "...\n";
    echo "- Public Key: " . substr(config('services.paydunya.public_key'), 0, 15) . "...\n";
    echo "- Private Key: " . substr(config('services.paydunya.private_key'), 0, 15) . "...\n";
    echo "- Token: " . substr(config('services.paydunya.token'), 0, 8) . "...\n\n";
    
    echo "🔍 Test détection opérateurs:\n";
    $testNumbers = [
        '771234567' => 'Orange Money',
        '781234567' => 'Orange Money', 
        '701234567' => 'Free Money',
        '761234567' => 'Free Money',
        '331234567' => 'Wave'
    ];
    
    foreach ($testNumbers as $number => $expected) {
        $detected = $paydunyaService->detectOperator($number);
        $status = ($detected === $expected) ? '✅' : '❌';
        echo "  $status $number -> $detected (attendu: $expected)\n";
    }
    
    echo "\n📱 Opérateurs supportés:\n";
    $operators = $paydunyaService->getSupportedOperators();
    foreach ($operators as $op) {
        echo "  {$op['logo']} {$op['nom']} ({$op['prefixes']})\n";
    }
    
    echo "\n🧪 Test paiement simulation:\n";
    $testPayment = [
        'membre_id' => 15,
        'reference' => 'TEST_' . time(),
        'telephone' => '771234567',
        'montant' => 1000,
        'type_cotisation' => 'test',
        'description' => 'Test PayDunya depuis PHP'
    ];
    
    echo "Données test: " . json_encode($testPayment, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "🚀 Envoi vers PayDunya...\n";
    $result = $paydunyaService->initiatePayment($testPayment);
    
    if ($result['success']) {
        echo "✅ SUCCÈS! PayDunya répond correctement\n";
        echo "- Opérateur détecté: {$result['operateur']}\n";
        echo "- Mode paiement: {$result['mode_paiement']}\n";
        
        if (isset($result['invoice_token'])) {
            echo "- Token facture: {$result['invoice_token']}\n";
        }
        
        if (isset($result['invoice_url'])) {
            echo "- URL paiement: {$result['invoice_url']}\n";
        }
    } else {
        echo "❌ ERREUR PayDunya:\n";
        echo "- Message: {$result['message']}\n";
        
        if (isset($result['error_code'])) {
            echo "- Code erreur: {$result['error_code']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n================================\n";
echo "🎯 Test terminé!\n";
