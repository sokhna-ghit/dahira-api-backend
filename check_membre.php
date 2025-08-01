<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\Membre;

echo "🔍 VÉRIFICATION MEMBRE ID 2\n";
echo "===========================\n\n";

$membre = Membre::find(2);

if ($membre) {
    echo "✅ Membre 2 trouvé:\n";
    echo "   - Nom: {$membre->nom}\n";
    echo "   - Prénom: {$membre->prenom}\n";
    echo "   - Email: {$membre->email}\n";
    echo "   - Dahira ID: {$membre->dahira_id}\n";
} else {
    echo "❌ Membre 2 non trouvé!\n";
    
    // Lister tous les membres disponibles
    $membres = Membre::all(['id', 'nom', 'prenom']);
    echo "\n📋 Membres disponibles:\n";
    foreach ($membres as $m) {
        echo "   - ID {$m->id}: {$m->nom} {$m->prenom}\n";
    }
}
