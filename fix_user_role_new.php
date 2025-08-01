<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User as AppUser;
use App\Models\Role;

try {
    echo "🔧 Correction du rôle de l'utilisateur test@example.com...\n";
    
    $user = AppUser::where('email', 'test@example.com')->first();
    
    if ($user) {
        // Trouver le rôle membre
        $membreRole = Role::where('name', 'membre')->first();
        
        if ($membreRole) {
            $user->role_id = $membreRole->id;
            $user->save();
            
            echo "✅ Rôle 'membre' (ID: {$membreRole->id}) assigné à l'utilisateur {$user->name}\n";
            
            // Vérifier la correction
            $user = AppUser::where('email', 'test@example.com')->with('role')->first();
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
