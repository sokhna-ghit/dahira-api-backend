<?php
/**
 * Test de l'API validation-status pour débugger
 */

$baseUrl = 'http://192.168.1.11:8000/api';

echo "🔍 === TEST API VALIDATION STATUS ===\n\n";

// Simuler une connexion pour récupérer un token
echo "1. Test de connexion avec sokhna@dahira.sn...\n";

$loginData = [
    'email' => 'sokhna@dahira.sn',
    'password' => 'password' // ou le bon mot de passe
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status de connexion: $loginHttpCode\n";
echo "Réponse: $loginResponse\n\n";

if ($loginHttpCode == 200) {
    $loginData = json_decode($loginResponse, true);
    
    if (isset($loginData['token'])) {
        $token = $loginData['token'];
        echo "✅ Token récupéré: " . substr($token, 0, 20) . "...\n";
        echo "👤 Utilisateur: " . ($loginData['user']['name'] ?? 'N/A') . "\n";
        echo "🎭 Rôle: " . ($loginData['user']['role'] ?? 'N/A') . "\n\n";
        
        // Test de l'API validation-status
        echo "2. Test de l'API validation-status...\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/validation-status');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $validationResponse = curl_exec($ch);
        $validationHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Status validation: $validationHttpCode\n";
        echo "Réponse validation: $validationResponse\n\n";
        
        if ($validationHttpCode == 200) {
            $validationData = json_decode($validationResponse, true);
            echo "✅ Données de validation reçues !\n";
            print_r($validationData);
        } else {
            echo "❌ Erreur API validation-status\n";
        }
        
    } else {
        echo "❌ Pas de token dans la réponse de connexion\n";
    }
} else {
    echo "❌ Échec de connexion\n";
}

echo "\n=== FIN TEST ===\n";
?>
