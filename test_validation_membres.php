<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Dahira;

echo "🔐 TEST SYSTÈME DE VALIDATION DES COMPTES MEMBRES\n";
echo "================================================\n\n";

$baseUrl = 'http://192.168.1.11:8000/api';

// 1. Test demande d'inscription
echo "1️⃣ Test demande d'inscription d'un nouveau membre...\n";

$nouvelleInscription = [
    'name' => 'Fatou Diop',
    'email' => 'fatou.diop@test.sn',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'telephone' => '771234999',
    'adresse' => 'Médina, Dakar',
    'genre' => 'féminin',
    'date_naissance' => '1995-03-15',
    'dahira_id' => 1, // Dahira existante
    'profession' => 'Enseignante'
];

$response = Http::post("$baseUrl/demande-inscription", $nouvelleInscription);

echo "URL: POST /api/demande-inscription\n";
echo "Status: " . $response->status() . "\n";
echo "Response: " . $response->body() . "\n\n";

if ($response->successful()) {
    $data = $response->json();
    $userId = $data['user_id'] ?? null;
    echo "✅ Demande d'inscription créée avec succès !\n";
    echo "   User ID: $userId\n\n";
} else {
    echo "❌ Erreur lors de la demande d'inscription\n\n";
    $userId = null;
}

// 2. Test connexion avec un admin pour voir les demandes
echo "2️⃣ Test récupération des demandes en attente (Admin)...\n";

// Créer un token admin pour les tests
$admin = User::where('email', 'sokhna@admin.com')->first();
if ($admin) {
    $adminToken = $admin->createToken('admin-test')->plainTextToken;
    echo "✅ Token admin créé: " . substr($adminToken, 0, 20) . "...\n";
    
    $response = Http::withHeaders([
        'Authorization' => "Bearer $adminToken",
        'Accept' => 'application/json'
    ])->get("$baseUrl/demandes-validation");
    
    echo "URL: GET /api/demandes-validation\n";
    echo "Status: " . $response->status() . "\n";
    
    if ($response->successful()) {
        $data = $response->json();
        echo "✅ Demandes récupérées: " . count($data['demandes']) . " en attente\n";
        foreach ($data['demandes'] as $demande) {
            echo "   - {$demande['name']} ({$demande['email']}) - Dahira: {$demande['dahira']}\n";
        }
    } else {
        echo "❌ Erreur: " . $response->body() . "\n";
    }
    echo "\n";
    
    // 3. Test approbation d'une demande
    if ($userId) {
        echo "3️⃣ Test approbation de la demande...\n";
        
        $response = Http::withHeaders([
            'Authorization' => "Bearer $adminToken",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post("$baseUrl/traiter-demande/$userId", [
            'action' => 'approve',
            'notes' => 'Membre approuvé après vérification des documents'
        ]);
        
        echo "URL: POST /api/traiter-demande/$userId\n";
        echo "Data: {\"action\": \"approve\", \"notes\": \"Membre approuvé...\"}\n";
        echo "Status: " . $response->status() . "\n";
        echo "Response: " . $response->body() . "\n\n";
        
        if ($response->successful()) {
            echo "✅ Demande approuvée avec succès !\n";
            
            // Vérifier le statut de l'utilisateur
            $user = User::find($userId);
            echo "   Statut utilisateur: {$user->status}\n";
            echo "   Approuvé: " . ($user->is_approved ? 'Oui' : 'Non') . "\n";
            echo "   Email vérifié: " . ($user->email_verified_at ? 'Oui' : 'Non') . "\n";
        } else {
            echo "❌ Erreur lors de l'approbation\n";
        }
    }
} else {
    echo "❌ Admin non trouvé\n";
}

echo "\n🎯 PROCESSUS DE VALIDATION DES COMPTES\n";
echo "=====================================\n";
echo "1. 📝 Membre fait une demande d'inscription via /api/demande-inscription\n";
echo "2. 📧 Email de confirmation envoyé au membre\n";
echo "3. 🔔 Notification envoyée aux administrateurs\n";
echo "4. 👨‍💼 Président/Admin consulte les demandes via /api/demandes-validation\n";
echo "5. ✅ Approbation/Rejet via /api/traiter-demande/{userId}\n";
echo "6. 📧 Email de validation/rejet envoyé au membre\n";
echo "7. 🎉 Membre peut se connecter si approuvé\n\n";

echo "💡 ENDPOINTS DISPONIBLES:\n";
echo "========================\n";
echo "• POST /api/demande-inscription - Demande d'inscription\n";
echo "• GET /api/demandes-validation - Lister demandes (Admin/Président)\n";
echo "• POST /api/traiter-demande/{id} - Approuver/Rejeter (Admin/Président)\n";
echo "• POST /api/resend-verification - Renvoyer email de vérification\n\n";

echo "🎭 RÔLES ET PERMISSIONS:\n";
echo "======================\n";
echo "• 👤 Membre: Peut faire une demande d'inscription\n";
echo "• 👑 Président: Peut approuver les membres de sa dahira\n";
echo "• 🛡️ Admin/Super Admin: Peut approuver tous les membres\n\n";

echo "📊 STATUTS UTILISATEUR:\n";
echo "======================\n";
echo "• pending: En attente de validation\n";
echo "• approved: Approuvé et actif\n";
echo "• rejected: Rejeté\n";
echo "• suspended: Suspendu\n\n";
