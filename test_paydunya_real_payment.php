<?php

require_once 'vendor/autoload.php';

use App\Services\PaydunyaService;

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 TEST PAIEMENT RÉEL PAYDUNYA\n";
echo "================================\n\n";

try {
    $paydunyaService = new PaydunyaService();
    
    // REMPLACEZ ces informations par votre client fictif PayDunya
    echo "📝 Informations client fictif (à remplacer par les vôtres) :\n";
    echo "- Nom: Client Test Dahira\n";
    echo "- Email: test.dahira@example.com\n";
    echo "- Téléphone: 771234567\n";
    echo "- Solde: 10000 FCFA (ou votre montant)\n\n";
    
    // Données de test avec client fictif
    $testPayment = [
        'membre_id' => 15,
        'reference' => 'DAHIRA_TEST_' . time(),
        'telephone' => '771234567', // REMPLACEZ par le téléphone de votre client fictif
        'montant' => 2000, // Montant de test (inférieur au solde fictif)
        'type_cotisation' => 'mensuelle',
        'description' => 'Test cotisation mensuelle dahira avec client fictif PayDunya'
    ];
    
    echo "🚀 Test paiement avec client fictif :\n";
    echo json_encode($testPayment, JSON_PRETTY_PRINT) . "\n\n";
    
    echo "💰 Envoi vers PayDunya...\n";
    $result = $paydunyaService->initiatePayment($testPayment);
    
    if ($result['success']) {
        echo "✅ SUCCÈS! PayDunya a créé la facture\n";
        echo "- Opérateur détecté: {$result['operateur']}\n";
        echo "- Mode paiement: {$result['mode_paiement']}\n";
        echo "- Token facture: {$result['invoice_token']}\n";
        echo "- URL de paiement: {$result['invoice_url']}\n\n";
        
        echo "🌐 ÉTAPES SUIVANTES :\n";
        echo "1. Allez sur cette URL dans votre navigateur :\n";
        echo "   {$result['invoice_url']}\n\n";
        echo "2. Sur la page PayDunya :\n";
        echo "   - Sélectionnez 'Orange Money' (ou votre opérateur)\n";
        echo "   - Entrez le téléphone de votre client fictif : 771234567\n";
        echo "   - Validez le paiement\n\n";
        echo "3. PayDunya utilisera le solde de votre client fictif\n";
        echo "4. Vous verrez le statut 'completed' dans votre tableau de bord\n\n";
        
        // Test de vérification du statut
        echo "🔍 Test vérification statut (après quelques secondes) :\n";
        sleep(3);
        
        $statusResult = $paydunyaService->checkPaymentStatus($result['invoice_token']);
        if ($statusResult['success']) {
            echo "✅ Statut récupéré :\n";
            echo "- Statut PayDunya: {$statusResult['paydunya_status']}\n";
            echo "- Statut interne: {$statusResult['status']}\n";
        } else {
            echo "❌ Erreur vérification statut : {$statusResult['message']}\n";
        }
        
    } else {
        echo "❌ ERREUR PayDunya :\n";
        echo "- Message: {$result['message']}\n";
        
        if (isset($result['error_code'])) {
            echo "- Code erreur: {$result['error_code']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR TECHNIQUE: " . $e->getMessage() . "\n";
}

echo "\n================================\n";
echo "📋 RÉSUMÉ DES ACTIONS :\n";
echo "1. ✅ Créer client fictif PayDunya\n";
echo "2. 🧪 Tester paiement avec client fictif (ce script)\n";
echo "3. 🌐 Valider sur l'URL PayDunya\n";
echo "4. 📱 Intégrer dans Flutter\n";
echo "5. 🚀 Déployer en production\n";
