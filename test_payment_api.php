<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

echo "=== TEST DE L'API DE PAIEMENT ===\n\n";

// Configuration
$base_url = 'http://localhost:8000/api';
$test_data = [
    'membre_id' => 1,
    'montant' => 5000,
    'telephone' => '771234567',
    'operateur' => 'orange',
    'type_cotisation' => 'mensuelle',
    'description' => 'Test paiement cotisation janvier 2025'
];

echo "1. Test d'initiation de paiement\n";
echo "Données: " . json_encode($test_data, JSON_PRETTY_PRINT) . "\n";

// Simuler une requête POST
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . '/paiements/initier');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . 'test-token' // À remplacer par un vrai token
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "Envoi de la requête...\n";
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $http_code\n";
echo "Réponse: $response\n\n";

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['reference'])) {
        $reference = $data['reference'];
        
        echo "2. Test de vérification de statut\n";
        echo "Référence: $reference\n";
        
        // Vérifier le statut
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $base_url . '/paiements/statut/' . $reference);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . 'test-token'
        ]);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 30);
        
        $response2 = curl_exec($ch2);
        $http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        curl_close($ch2);
        
        echo "Code HTTP: $http_code2\n";
        echo "Réponse: $response2\n\n";
        
        echo "3. Test d'historique\n";
        
        // Historique
        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_URL, $base_url . '/paiements/historique?membre_id=1');
        curl_setopt($ch3, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . 'test-token'
        ]);
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch3, CURLOPT_TIMEOUT, 30);
        
        $response3 = curl_exec($ch3);
        $http_code3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
        curl_close($ch3);
        
        echo "Code HTTP: $http_code3\n";
        echo "Réponse: $response3\n\n";
    }
}

// Test des méthodes du service de paiement
echo "4. Test des méthodes du PaymentService Flutter\n";

// Simulation des fonctions PHP équivalentes
function estTelephoneValide($telephone) {
    // Nettoyer le numéro
    $tel = preg_replace('/[^0-9]/', '', $telephone);
    
    // Vérifier le format sénégalais
    return preg_match('/^(77|78|70|76|33)[0-9]{7}$/', $tel);
}

function detecterOperateur($telephone) {
    $tel = preg_replace('/[^0-9]/', '', $telephone);
    $prefixe = substr($tel, 0, 2);
    
    $operateurs = [
        'orange' => ['77', '78'],
        'free' => ['76', '70'],
        'wave' => ['33']
    ];
    
    foreach ($operateurs as $op => $prefixes) {
        if (in_array($prefixe, $prefixes)) {
            return $op;
        }
    }
    
    return null;
}

function formaterTelephone($telephone) {
    $tel = preg_replace('/[^0-9]/', '', $telephone);
    
    if (strlen($tel) === 9) {
        return '+221' . $tel;
    } elseif (strlen($tel) === 12 && substr($tel, 0, 3) === '221') {
        return '+' . $tel;
    }
    
    return $tel;
}

// Tests
$test_phones = ['771234567', '781234567', '701234567', '761234567', '331234567', '123456789'];

foreach ($test_phones as $phone) {
    $valide = estTelephoneValide($phone) ? 'Valide' : 'Invalide';
    $operateur = detecterOperateur($phone) ?? 'Inconnu';
    $formate = formaterTelephone($phone);
    
    echo "Téléphone: $phone | $valide | Opérateur: $operateur | Formaté: $formate\n";
}

echo "\n=== FIN DES TESTS ===\n";
