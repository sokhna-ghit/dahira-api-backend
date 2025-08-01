<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User as AppUser;
use App\Models\Dahira;

try {
    echo "🔍 Vérification de l'utilisateur test@example.com...\n";
    
    $user = AppUser::where('email', 'test@example.com')->with('role', 'dahira')->first();
    
    if ($user) {
        echo "✅ Utilisateur trouvé:\n";
        echo "   - User ID: {$user->id}\n";
        echo "   - Role: {$user->role->name}\n";
        echo "   - Dahira ID: " . ($user->dahira_id ?? 'NULL') . "\n";
        echo "   - Dahira: " . ($user->dahira->nom ?? 'Aucun dahira') . "\n";
        
        if (!$user->dahira_id) {
            echo "\n🔧 Assignation d'un dahira...\n";
            
            // Trouver le premier dahira disponible
            $dahira = Dahira::first();
            if ($dahira) {
                $user->dahira_id = $dahira->id;
                $user->save();
                echo "✅ Dahira '{$dahira->nom}' (ID: {$dahira->id}) assigné à l'utilisateur\n";
            } else {
                echo "❌ Aucun dahira trouvé\n";
            }
        }
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
