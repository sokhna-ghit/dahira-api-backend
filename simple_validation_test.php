<?php

echo "=== TEST API DE VALIDATION ===\n";

// Configuration
$base_url = "http://192.168.1.11:8000/api";
$login_url = "$base_url/login";
$validation_url = "$base_url/validation-status";

echo "URL de base: $base_url\n";

// Fonction pour faire des requêtes curl
function makeRequest($url, $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

// Test 1: Connexion avec sokhna@dahira.sn
echo "\n--- TEST 1: CONNEXION ---\n";
$loginData = [
    'email' => 'sokhna@dahira.sn',
    'password' => 'password123'
];

$loginResult = makeRequest($login_url, $loginData);
echo "Code HTTP: " . $loginResult['http_code'] . "\n";

if ($loginResult['error']) {
    echo "Erreur cURL: " . $loginResult['error'] . "\n";
    exit(1);
}

$loginResponse = json_decode($loginResult['response'], true);
echo "Réponse login:\n";
print_r($loginResponse);

if (!isset($loginResponse['access_token'])) {
    echo "ERREUR: Pas de token d'accès reçu\n";
    exit(1);
}

$token = $loginResponse['access_token'];
echo "\nToken reçu: " . substr($token, 0, 50) . "...\n";

// Test 2: Récupération du statut de validation
echo "\n--- TEST 2: STATUT DE VALIDATION ---\n";
$validationResult = makeRequest($validation_url, null, $token);
echo "Code HTTP: " . $validationResult['http_code'] . "\n";

if ($validationResult['error']) {
    echo "Erreur cURL: " . $validationResult['error'] . "\n";
} else {
    $validationResponse = json_decode($validationResult['response'], true);
    echo "Réponse validation:\n";
    print_r($validationResponse);
}

// Test 3: Vérifier l'endpoint des demandes en attente
echo "\n--- TEST 3: DEMANDES EN ATTENTE ---\n";
$demandesUrl = "$base_url/validation/demandes-en-attente";
$demandesResult = makeRequest($demandesUrl, null, $token);
echo "Code HTTP: " . $demandesResult['http_code'] . "\n";

if ($demandesResult['error']) {
    echo "Erreur cURL: " . $demandesResult['error'] . "\n";
} else {
    $demandesResponse = json_decode($demandesResult['response'], true);
    echo "Réponse demandes:\n";
    print_r($demandesResponse);
}

echo "\n=== FIN DES TESTS ===\n";

?>
