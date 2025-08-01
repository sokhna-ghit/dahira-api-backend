<?php

require_once 'vendor/autoload.php';
use App\Models\PaiementPaydunya;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🗃️ CRÉATION DONNÉES TEST HISTORIQUE\n";
echo "===================================\n\n";

// Créer un paiement de test
$paiement = PaiementPaydunya::create([
    'membre_id' => 15,
    'reference' => 'DAHIRA_15_DEMO_' . time(),
    'invoice_token' => 'test_demo_' . time(),
    'invoice_url' => 'https://paydunya.com/sandbox-checkout/invoice/test_demo',
    'montant' => 15000.00,
    'telephone' => '771234567',
    'operateur' => 'Orange Money',
    'mode_paiement' => 'orange-money-senegal',
    'type_cotisation' => 'mensuelle',
    'description' => 'Cotisation mensuelle Janvier 2025',
    'statut' => 'reussi',
    'statut_paydunya' => 'completed',
    'date_paiement' => now(),
]);

echo "✅ Paiement test créé : {$paiement->reference}\n";
echo "💰 Montant: {$paiement->montant} FCFA\n";
echo "📱 Opérateur: {$paiement->operateur}\n";
echo "📅 Date: {$paiement->created_at}\n\n";

echo "🎯 L'historique PayDunya va maintenant afficher des données!\n";
echo "🔄 Essayez l'historique dans Flutter maintenant.\n";
