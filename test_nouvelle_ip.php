<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔄 TEST RAPIDE AVEC NOUVELLE IP: 192.168.1.11\n";
echo "============================================\n\n";

// 1. Test base de données
echo "1️⃣ Test connexion base de données...\n";
try {
    $usersCount = DB::table('users')->count();
    echo "✅ Base de données connectée - $usersCount utilisateurs\n\n";
} catch (\Exception $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "\n\n";
    exit(1);
}

// 2. Créer token de test
echo "2️⃣ Création token utilisateur...\n";
$user = DB::table('users')->where('email', 'admin@example.com')->first();
if (!$user) {
    $user = DB::table('users')->first();
}

if ($user) {
    $token = DB::table('personal_access_tokens')->insertGetId([
        'tokenable_type' => 'App\\Models\\User',
        'tokenable_id' => $user->id,
        'name' => 'test-token',
        'token' => hash('sha256', $plainTextToken = \Illuminate\Support\Str::random(40)),
        'abilities' => '["*"]',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fullToken = $token . '|' . $plainTextToken;
    echo "✅ Token créé: " . substr($fullToken, 0, 20) . "...\n";
    echo "📧 Utilisateur: {$user->email}\n\n";
} else {
    echo "❌ Aucun utilisateur trouvé\n\n";
    exit(1);
}

// 3. Test avec curl local
echo "3️⃣ Test endpoint avec nouvelle IP...\n";
$baseUrl = 'http://192.168.1.11:8000/api';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/me");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $fullToken,
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "URL: $baseUrl/me\n";
echo "Status: $httpCode\n";

if ($httpCode == 200) {
    echo "✅ API accessible avec la nouvelle IP !\n";
    $data = json_decode($response, true);
    if ($data && isset($data['name'])) {
        echo "Response: Utilisateur connecté - {$data['name']}\n";
    }
} else {
    if ($error) {
        echo "❌ Erreur curl: $error\n";
    } else {
        echo "❌ Erreur HTTP: $httpCode\n";
        echo "Response: $response\n";
    }
}

echo "\n🎯 RÉSUMÉ:\n";
echo "=========\n";
echo "• Flutter doit utiliser: 192.168.1.11:8000\n";
echo "• Serveur Laravel: php artisan serve --host=192.168.1.11 --port=8000\n";
echo "• Token pour tests: $fullToken\n\n";

echo "📱 Prochaines étapes:\n";
echo "1. Redémarrer le serveur Laravel sur 192.168.1.11\n";
echo "2. Vérifier que Flutter utilise la nouvelle IP\n";
echo "3. Tester les fonctionnalités depuis l'app\n";
