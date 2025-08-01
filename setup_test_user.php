<?php

require_once 'vendor/autoload.php';

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Dahira;

echo "=== VÉRIFICATION DES UTILISATEURS ===\n\n";

// Vérifier les utilisateurs existants
$users = User::select('id', 'name', 'email')->get();

if ($users->count() > 0) {
    echo "Utilisateurs existants:\n";
    foreach ($users as $user) {
        echo "ID: {$user->id} - {$user->name} ({$user->email})\n";
    }
} else {
    echo "Aucun utilisateur trouvé.\n";
}

echo "\n=== VÉRIFICATION DES DAHIRAS ===\n\n";

// Vérifier les dahiras existants
$dahiras = Dahira::select('id', 'nom')->get();

if ($dahiras->count() > 0) {
    echo "Dahiras existants:\n";
    foreach ($dahiras as $dahira) {
        echo "ID: {$dahira->id} - {$dahira->nom}\n";
    }
} else {
    echo "Aucune dahira trouvée.\n";
}

echo "\n=== CRÉATION D'UN UTILISATEUR DE TEST ===\n\n";

// Créer un utilisateur de test s'il n'existe pas
$testUser = User::where('email', 'test@membre.com')->first();

if (!$testUser) {
    $testUser = User::create([
        'name' => 'Membre Test',
        'email' => 'test@membre.com',
        'password' => bcrypt('password123'),
        'telephone' => '771234567',
        'adresse' => 'Dakar, Sénégal',
        'genre' => 'masculin',
        'date_naissance' => '1990-01-01',
        'profession' => 'Développeur',
        'statut' => 'actif',
    ]);
    
    echo "✅ Utilisateur de test créé:\n";
    echo "ID: {$testUser->id}\n";
    echo "Nom: {$testUser->name}\n";
    echo "Email: {$testUser->email}\n";
    echo "Téléphone: {$testUser->telephone}\n";
} else {
    echo "✅ Utilisateur de test existant:\n";
    echo "ID: {$testUser->id}\n";
    echo "Nom: {$testUser->name}\n";
    echo "Email: {$testUser->email}\n";
    echo "Téléphone: {$testUser->telephone}\n";
}

// Créer une dahira de test si nécessaire
$testDahira = Dahira::first();

if (!$testDahira) {
    $testDahira = Dahira::create([
        'nom' => 'Dahira Test',
        'description' => 'Dahira de test pour les paiements',
        'president_id' => $testUser->id,
    ]);
    
    echo "\n✅ Dahira de test créée:\n";
    echo "ID: {$testDahira->id}\n";
    echo "Nom: {$testDahira->nom}\n";
}

// Associer l'utilisateur à la dahira si ce n'est pas déjà fait
if ($testUser && $testDahira) {
    $testUser->update(['dahira_id' => $testDahira->id]);
    echo "\n✅ Utilisateur associé à la dahira\n";
}

echo "\n=== INFORMATIONS POUR LES TESTS ===\n\n";
echo "🔹 Utilisateur de test:\n";
echo "   - ID: {$testUser->id}\n";
echo "   - Email: {$testUser->email}\n";
echo "   - Mot de passe: password123\n";
echo "   - Téléphone: {$testUser->telephone}\n";
echo "   - Dahira ID: " . ($testUser->dahira_id ?? $testDahira->id) . "\n";

echo "\n🔹 Pour tester les paiements, utilisez:\n";
echo "   - membre_id: {$testUser->id}\n";
echo "   - dahira_id: " . ($testUser->dahira_id ?? $testDahira->id) . "\n";
echo "   - telephone: {$testUser->telephone} (Orange Money)\n";
echo "   - montant: 5000 FCFA (minimum)\n";

echo "\n=== GÉNÉRATION D'UN TOKEN D'AUTHENTIFICATION ===\n\n";

// Générer un token pour les tests API
$token = $testUser->createToken('test-payment-token')->plainTextToken;
echo "🔑 Token d'authentification (à utiliser dans les en-têtes):\n";
echo "Authorization: Bearer {$token}\n";

echo "\n=== INSTRUCTIONS DE TEST ===\n\n";
echo "1. Utilisez ce token dans vos requêtes API\n";
echo "2. L'API de paiement est disponible sur: http://localhost:8000/api/paiements/\n";
echo "3. Endpoints disponibles:\n";
echo "   - POST /api/paiements/initier (initier un paiement)\n";
echo "   - GET /api/paiements/statut/{reference} (vérifier le statut)\n";
echo "   - GET /api/paiements/historique (historique des paiements)\n";
echo "   - GET /api/paiements/recu/{reference} (télécharger le reçu)\n";
echo "   - GET /api/paiements/statistiques (statistiques)\n";

echo "\n✅ PRÊT POUR LES TESTS !\n";
