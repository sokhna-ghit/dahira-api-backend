<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User as AppUser;
use App\Models\Membre;

try {
    echo "🔧 Création d'un membre pour l'utilisateur test@example.com...\n";
    
    $user = AppUser::where('email', 'test@example.com')->first();
    
    if ($user) {
        // Vérifier si un membre existe déjà
        $existingMembre = Membre::where('user_id', $user->id)->first();
        
        if ($existingMembre) {
            echo "✅ Membre déjà existant: {$existingMembre->prenom} {$existingMembre->nom}\n";
        } else {
            // Créer un nouveau membre
            $membre = new Membre();
            $membre->user_id = $user->id;
            $membre->dahira_id = $user->dahira_id;
            $membre->nom = 'Test';
            $membre->prenom = 'Utilisateur';
            $membre->email = $user->email;
            $membre->telephone = '+221700000000';
            $membre->adresse = 'Adresse Test, Dakar';
            $membre->genre = 'masculin';
            $membre->date_naissance = '1990-01-01';
            $membre->profession = 'Testeur';
            $membre->statut = 'actif';
            $membre->date_inscription = now();
            $membre->active = 1;
            $membre->commentaires = 'Utilisateur de test créé automatiquement';
            
            $membre->save();
            
            echo "✅ Membre créé: {$membre->prenom} {$membre->nom} (ID: {$membre->id})\n";
            echo "   - Email: {$membre->email}\n";
            echo "   - User ID: {$membre->user_id}\n";
            echo "   - Dahira ID: {$membre->dahira_id}\n";
        }
    } else {
        echo "❌ Utilisateur non trouvé\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
