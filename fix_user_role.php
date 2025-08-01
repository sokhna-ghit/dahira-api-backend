<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

try {
    echo "🔧 Correction du rôle de l'utilisateur test@example.com...\n";
    
    $user = User::where('email', 'test@example.com')->first();
    
    if ($user) {
        // Trouver le rôle membre
        $membreRole = Role::where('name', 'membre')->first();
        
        if ($membreRole) {
            $user->role_id = $membreRole->id;
            $user->save();
            
            echo "✅ Rôle 'membre' (ID: {$membreRole->id}) assigné à l'utilisateur {$user->name}\n";
            
            // Vérifier la correction
            $user = User::where('email', 'test@example.com')->with('role')->first();
            echo "🔍 Vérification:\n";
            echo "   - User ID: {$user->id}\n";
            echo "   - Role ID: {$user->role_id}\n";
            echo "   - Role name: {$user->role->name}\n";
        } else {
            echo "❌ Rôle 'membre' non trouvé\n";
        }
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

use App\Models\User;
use App\Models\Role;

echo "=== DIAGNOSTIC UTILISATEUR ET ROLE ===\n";

// 1. Vérifier l'utilisateur
$user = User::where('email', 'aminata@president.sn')->first();
if (!$user) {
    echo "❌ Utilisateur aminata@president.sn non trouvé\n";
    exit;
}

echo "✅ Utilisateur trouvé: {$user->name}\n";
echo "📧 Email: {$user->email}\n";
echo "🆔 Role ID: " . ($user->role_id ?? 'NULL') . "\n";
echo "🏢 Dahira ID: " . ($user->dahira_id ?? 'NULL') . "\n";

// 2. Vérifier les rôles disponibles
echo "\n=== ROLES DISPONIBLES ===\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "• ID: {$role->id}, Nom: {$role->nom}\n";
}

// 3. Essayer de récupérer le rôle de l'utilisateur
try {
    $userRole = $user->role;
    if ($userRole) {
        echo "\n✅ Rôle de l'utilisateur: {$userRole->nom}\n";
    } else {
        echo "\n❌ Aucun rôle associé à l'utilisateur\n";
    }
} catch (Exception $e) {
    echo "\n❌ Erreur lors de la récupération du rôle: " . $e->getMessage() . "\n";
}

// 4. Assigner le rôle président si nécessaire
$rolePresident = Role::where('name', 'président')->first();
if (!$rolePresident) {
    echo "\n⚠️ Rôle 'président' non trouvé, création...\n";
    $rolePresident = Role::create([
        'name' => 'président'
    ]);
    echo "✅ Rôle président créé avec ID: {$rolePresident->id}\n";
}

// 5. Mettre à jour l'utilisateur avec le bon rôle
if (!$user->role_id || $user->role_id != $rolePresident->id) {
    echo "\n🔄 Mise à jour du rôle de l'utilisateur...\n";
    $user->role_id = $rolePresident->id;
    $user->save();
    echo "✅ Utilisateur mis à jour avec le rôle président\n";
}

// 6. Vérification finale
$user->refresh();
$finalRole = $user->role;
echo "\n=== VERIFICATION FINALE ===\n";
echo "👤 Utilisateur: {$user->name}\n";
echo "🎭 Rôle: " . ($finalRole ? $finalRole->nom : 'NULL') . "\n";
echo "🆔 Role ID: {$user->role_id}\n";
echo "🏢 Dahira ID: {$user->dahira_id}\n";
