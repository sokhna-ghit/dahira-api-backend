<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Membre;
use App\Models\Role;
use App\Models\Dahira;

echo "🔐 DÉMONSTRATION SYSTÈME DE VALIDATION DES COMPTES\n";
echo "================================================\n\n";

// 1. Simuler une demande d'inscription
echo "1️⃣ Simulation: Demande d'inscription d'un nouveau membre...\n";

// Récupérer le rôle membre et une dahira
$roleMembre = Role::where('name', 'membre')->first();
$dahira = Dahira::first();

if (!$roleMembre || !$dahira) {
    echo "❌ Données manquantes (rôle membre ou dahira)\n";
    exit(1);
}

try {
    // Créer l'utilisateur en attente de validation
    $user = User::create([
        'name' => 'Fatou Diop Test',
        'email' => 'fatou.test.' . time() . '@example.com',
        'password' => \Hash::make('password123'),
        'role_id' => $roleMembre->id,
        'dahira_id' => $dahira->id,
        'status' => 'pending',
        'is_approved' => false,
    ]);

    // Créer le profil membre associé
    $membre = Membre::create([
        'nom' => 'Diop',
        'prenom' => 'Fatou',
        'email' => $user->email,
        'telephone' => '771234' . rand(100, 999),
        'adresse' => 'Dakar, Sénégal',
        'genre' => 'F',
        'date_naissance' => '1995-03-15',
        'dahira_id' => $dahira->id,
        'profession' => 'Enseignante',
        'statut' => 'en_attente_validation',
        'user_id' => $user->id,
    ]);

    echo "✅ Demande d'inscription créée !\n";
    echo "   User ID: {$user->id}\n";
    echo "   Email: {$user->email}\n";
    echo "   Statut: {$user->status}\n";
    echo "   Approuvé: " . ($user->is_approved ? 'Oui' : 'Non') . "\n";
    echo "   Membre ID: {$membre->id}\n";
    echo "   Statut membre: {$membre->statut}\n\n";

    // 2. Simuler la validation par un admin
    echo "2️⃣ Simulation: Validation par un administrateur...\n";

    $admin = User::whereHas('role', function($query) {
        $query->whereIn('name', ['admin', 'super_admin']);
    })->first();

    if ($admin) {
        // Approuver le compte
        $user->update([
            'status' => 'approved',
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
            'approval_notes' => 'Membre validé après vérification des documents',
            'email_verified_at' => now(),
        ]);

        // Activer le membre
        $membre->update([
            'statut' => 'actif',
            'date_inscription' => now(),
        ]);

        echo "✅ Compte approuvé par {$admin->name} !\n";
        echo "   Nouveau statut: {$user->fresh()->status}\n";
        echo "   Approuvé: " . ($user->fresh()->is_approved ? 'Oui' : 'Non') . "\n";
        echo "   Date d'approbation: {$user->fresh()->approved_at}\n";
        echo "   Statut membre: {$membre->fresh()->statut}\n";
        echo "   Date d'inscription: {$membre->fresh()->date_inscription}\n\n";

        // 3. Test de connexion
        echo "3️⃣ Test: Le membre peut maintenant se connecter...\n";
        
        if ($user->fresh()->isApproved() && $user->fresh()->hasVerifiedEmail()) {
            echo "✅ Le membre peut se connecter !\n";
            echo "   - Compte approuvé: ✓\n";
            echo "   - Email vérifié: ✓\n";
            echo "   - Statut: {$user->fresh()->status}\n";
        } else {
            echo "❌ Le membre ne peut pas encore se connecter\n";
            echo "   - Approuvé: " . ($user->fresh()->is_approved ? '✓' : '✗') . "\n";
            echo "   - Email vérifié: " . ($user->fresh()->hasVerifiedEmail() ? '✓' : '✗') . "\n";
        }

    } else {
        echo "❌ Aucun administrateur trouvé\n";
    }

} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n📊 STATISTIQUES DES COMPTES\n";
echo "===========================\n";

$stats = [
    'pending' => User::where('status', 'pending')->count(),
    'approved' => User::where('status', 'approved')->count(),
    'rejected' => User::where('status', 'rejected')->count(),
    'total' => User::count(),
];

echo "• En attente: {$stats['pending']}\n";
echo "• Approuvés: {$stats['approved']}\n";
echo "• Rejetés: {$stats['rejected']}\n";
echo "• Total: {$stats['total']}\n\n";

// Afficher les dernières demandes
echo "📋 DERNIÈRES DEMANDES EN ATTENTE\n";
echo "===============================\n";

$demandes = User::with(['role', 'dahira', 'membre'])
    ->where('status', 'pending')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($demandes->count() > 0) {
    foreach ($demandes as $demande) {
        echo "• {$demande->name} ({$demande->email})\n";
        echo "  Dahira: " . ($demande->dahira->nom ?? 'Non définie') . "\n";
        echo "  Demande le: {$demande->created_at->format('d/m/Y H:i')}\n";
        if ($demande->membre) {
            echo "  Téléphone: {$demande->membre->telephone}\n";
            echo "  Profession: " . ($demande->membre->profession ?? 'Non précisée') . "\n";
        }
        echo "\n";
    }
} else {
    echo "Aucune demande en attente\n\n";
}

echo "🎯 PROCHAINES ÉTAPES POUR FLUTTER\n";
echo "=================================\n";
echo "1. Créer une page d'inscription avec validation\n";
echo "2. Ajouter une page admin pour gérer les demandes\n";
echo "3. Implémenter les notifications push/email\n";
echo "4. Ajouter un système de workflow d'approbation\n\n";

echo "📱 ENDPOINTS FLUTTER:\n";
echo "====================\n";
echo "• POST /api/demande-inscription - Nouvelle demande\n";
echo "• GET /api/demandes-validation - Liste pour admin\n";
echo "• POST /api/traiter-demande/{id} - Approuver/Rejeter\n";
echo "• POST /api/resend-verification - Renvoyer email\n\n";
