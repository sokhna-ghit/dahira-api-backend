<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

try {
    echo "🔍 Vérification de l'utilisateur test@example.com...\n";
    
    $user = User::where('email', 'test@example.com')->with('role')->first();
    
    if ($user) {
        echo "✅ Utilisateur trouvé:\n";
        echo "   - ID: {$user->id}\n";
        echo "   - Name: {$user->name}\n";
        echo "   - Email: {$user->email}\n";
        echo "   - Role ID: {$user->role_id}\n";
        
        if ($user->role) {
            echo "   - Role nom: {$user->role->nom}\n";
        } else {
            echo "   - ❌ Pas de rôle associé!\n";
            
            // Vérifier tous les rôles disponibles
            echo "\n📋 Rôles disponibles:\n";
            $roles = Role::all();
            foreach ($roles as $role) {
                echo "   - ID: {$role->id}, Nom: {$role->nom}\n";
            }
            
            // Assigner le rôle membre par défaut
            $membreRole = Role::where('nom', 'membre')->first();
            if ($membreRole) {
                $user->role_id = $membreRole->id;
                $user->save();
                echo "\n✅ Rôle 'membre' assigné à l'utilisateur\n";
            }
        }
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
