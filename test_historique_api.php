<?php

echo "🧪 TEST HISTORIQUE PAYDUNYA API\n";
echo "==============================\n\n";

$baseUrl = 'http://192.168.1.24:8000/api';
$token = '71|T1BcgYOSFyZpbvUZNhbdwFaE12iAn47Zssi1ow3se38e780c'; // Token précédent

// Test: Obtenir l'historique
echo "📋 Test: Récupération historique PayDunya pour membre 15\n";
echo "URL: GET $baseUrl/paydunya/historique/15\n\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "$baseUrl/paydunya/historique/15",
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
$error = curl_error($curl);
curl_close($curl);

if ($error) {
    echo "❌ Erreur cURL: $error\n\n";
} else {
    echo "✅ Status: $httpCode\n";
    echo "Response:\n";
    
    // Formatter la réponse JSON pour la lisibilité
    $data = json_decode($response, true);
    if ($data) {
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        if (isset($data['paiements']) && is_array($data['paiements'])) {
            $count = count($data['paiements']);
            echo "📊 Résumé: $count paiement(s) trouvé(s)\n";
            
            foreach ($data['paiements'] as $index => $paiement) {
                echo "   #" . ($index + 1) . " - {$paiement['reference']} : {$paiement['montant']} FCFA ({$paiement['statut']})\n";
            }
        }
    } else {
        echo $response . "\n";
    }
}

echo "\n==============================\n";
echo "🎯 Si des paiements apparaissent, l'historique Flutter fonctionnera!\n";
echo "📱 Sinon, les paiements seront ajoutés automatiquement lors des nouveaux paiements PayDunya.\n";
