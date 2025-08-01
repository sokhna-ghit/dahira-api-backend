<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\User;
use App\Models\Role;

echo "=== TEST AUTHENTIFICATION DEBUG ===\n\n";

try {
    // 1. Tester la récupération de l'utilisateur avec relation
    echo "1. TEST USER AVEC RELATION :\n";
    $user = User::with('role')->where('email', 'aminata@president.sn')->first();
    
    if ($user) {
        echo "✅ Utilisateur trouvé: {$user->email}\n";
        echo "   ID: {$user->id}\n";
        echo "   Role ID: {$user->role_id}\n";
        echo "   Name: {$user->name}\n";
        
        // Test de la relation
        echo "\n2. TEST RELATION ROLE :\n";
        echo "   method_exists(user, 'role'): " . (method_exists($user, 'role') ? 'TRUE' : 'FALSE') . "\n";
        echo "   user->role existe: " . (isset($user->role) ? 'TRUE' : 'FALSE') . "\n";
        
        if ($user->role) {
            echo "   user->role est objet: " . (is_object($user->role) ? 'TRUE' : 'FALSE') . "\n";
            echo "   user->role->name: " . ($user->role->name ?? 'NULL') . "\n";
            echo "   user->role->id: " . ($user->role->id ?? 'NULL') . "\n";
        } else {
            echo "   ❌ user->role est NULL\n";
            
            // Essayer de charger manuellement
            echo "\n3. CHARGEMENT MANUEL DU RÔLE :\n";
            $role = Role::find($user->role_id);
            if ($role) {
                echo "   ✅ Rôle trouvé manuellement: {$role->name} (ID: {$role->id})\n";
            } else {
                echo "   ❌ Rôle introuvable avec ID: {$user->role_id}\n";
            }
        }
        
        // Test lazy loading
        echo "\n4. TEST LAZY LOADING :\n";
        $freshUser = User::find($user->id);
        echo "   User trouvé: " . ($freshUser ? 'TRUE' : 'FALSE') . "\n";
        if ($freshUser) {
            echo "   Accès à freshUser->role...\n";
            $roleFromLazy = $freshUser->role;
            echo "   Lazy loading role: " . ($roleFromLazy ? $roleFromLazy->name : 'NULL') . "\n";
        }
        
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
    echo "\n5. TEST REQUÊTE DIRECTE :\n";
    $directQuery = \DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->where('users.email', 'aminata@president.sn')
        ->select('users.*', 'roles.name as role_name')
        ->first();
    
    if ($directQuery) {
        echo "   ✅ Requête directe réussie\n";
        echo "   Email: {$directQuery->email}\n";
        echo "   Role name: {$directQuery->role_name}\n";
        echo "   Role ID: {$directQuery->role_id}\n";
    } else {
        echo "   ❌ Requête directe échouée\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN TEST ===\n";
