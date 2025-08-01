<?php

require_once 'vendor/autoload.php';

use App\Services\PaydunyaService;

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 VÉRIFICATION STATUT PAIEMENT\n";
echo "===============================\n\n";

// Token de la dernière facture générée
$invoiceToken = 'test_1XrWt2MaGj'; // Le token du test précédent

try {
    $paydunyaService = new PaydunyaService();
    
    echo "📋 Vérification du statut pour : $invoiceToken\n\n";
    
    $result = $paydunyaService->checkPaymentStatus($invoiceToken);
    
    if ($result['success']) {
        echo "✅ STATUT RÉCUPÉRÉ :\n";
        echo "- Statut PayDunya: {$result['paydunya_status']}\n";
        echo "- Statut interne: {$result['status']}\n";
        echo "- Message: {$result['message']}\n\n";
        
        switch ($result['paydunya_status']) {
            case 'pending':
                echo "⏳ Paiement en attente - allez sur l'URL pour payer\n";
                break;
            case 'completed':
                echo "✅ PAIEMENT RÉUSSI ! Le client fictif a payé avec succès\n";
                break;
            case 'cancelled':
                echo "❌ Paiement annulé\n";
                break;
            case 'failed':
                echo "❌ Paiement échoué\n";
                break;
        }
    } else {
        echo "❌ Erreur : {$result['message']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

echo "\n===============================\n";
echo "🎯 PROCHAINES ÉTAPES :\n";
echo "1. Si statut = 'pending' → Payez sur l'URL\n";
echo "2. Si statut = 'completed' → Test réussi !\n";
echo "3. Ensuite → Intégration Flutter\n";
