<?php

// Script pour tester l'API des membres
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

// Démarrer Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simuler une requête
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

// Test de l'API membres
echo "=== TEST API MEMBRES ===\n";

// 1. Obtenir un utilisateur président pour les tests
$president = User::where('email', 'aminata@president.sn')->first();
if (!$president) {
    echo "❌ Utilisateur président non trouvé\n";
    exit;
}

echo "✅ Utilisateur président trouvé: {$president->email}\n";

// 2. Créer un token pour les tests
$token = $president->createToken('test-token')->plainTextToken;
echo "✅ Token créé: " . substr($token, 0, 20) . "...\n";

// 3. Simuler une requête GET /api/membres
echo "\n=== TEST GET /api/membres ===\n";

// Importer les classes nécessaires
use App\Http\Controllers\MembreController;
use App\Models\Membre;

// Compter les membres
$totalMembres = Membre::count();
echo "📊 Total membres en DB: $totalMembres\n";

// Obtenir les membres du dahira du président
$dahiraId = $president->dahira_id;
echo "🏢 Dahira ID du président: $dahiraId\n";

$membresParDahira = Membre::where('dahira_id', $dahiraId)->count();
echo "👥 Membres dans ce dahira: $membresParDahira\n";

// Lister quelques membres pour debug
echo "\n=== EXEMPLES DE MEMBRES ===\n";
$exemplesMembres = Membre::where('dahira_id', $dahiraId)->take(3)->get();
foreach ($exemplesMembres as $membre) {
    echo "• {$membre->prenom} {$membre->nom} - {$membre->email}\n";
}

// Test du contrôleur
echo "\n=== TEST CONTROLEUR ===\n";
try {
    $controller = new MembreController();
    
    // Simuler une requête authentifiée
    $request = new Illuminate\Http\Request();
    $request->setUserResolver(function () use ($president) {
        return $president;
    });
    
    // Appeler la méthode index
    $response = $controller->index($request);
    $responseData = $response->getData(true);
    
    echo "✅ Réponse du contrôleur:\n";
    echo "Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "Message: " . ($responseData['message'] ?? 'N/A') . "\n";
    echo "Nombre de membres retournés: " . count($responseData['data'] ?? []) . "\n";
    
    if (isset($responseData['data']) && count($responseData['data']) > 0) {
        echo "Premier membre: " . json_encode($responseData['data'][0], JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur du contrôleur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
