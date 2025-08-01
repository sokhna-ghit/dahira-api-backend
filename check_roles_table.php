<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "🔍 Vérification de la structure de la table roles...\n";
    
    // Obtenir la structure de la table roles
    $columns = DB::select("DESCRIBE roles");
    
    echo "📋 Colonnes de la table roles:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n📋 Contenu de la table roles:\n";
    $roles = DB::table('roles')->get();
    foreach ($roles as $role) {
        echo "   - " . json_encode($role) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
