<?php

require_once 'vendor/autoload.php';

// Démarrer Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

use App\Models\User;
use App\Models\Dahira;

echo "=== ASSOCIATION UTILISATEUR AU DAHIRA ===\n";

// Trouver l'utilisateur président
$user = User::where('email', 'aminata@president.sn')->first();
if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit;
}

echo "✅ Utilisateur trouvé: {$user->email}\n";
echo "📍 Dahira ID actuel: " . ($user->dahira_id ?? 'NULL') . "\n";

// Trouver un dahira disponible
$dahira = Dahira::first();
if (!$dahira) {
    echo "❌ Aucun dahira trouvé\n";
    exit;
}

echo "🏢 Premier dahira disponible: {$dahira->id} - {$dahira->nom}\n";

// Associer l'utilisateur au dahira
$user->dahira_id = $dahira->id;
$user->save();

echo "✅ Utilisateur associé au dahira !\n";
echo "📍 Nouveau dahira ID: {$user->dahira_id}\n";

// Vérifier les membres de ce dahira
$nombreMembres = App\Models\Membre::where('dahira_id', $dahira->id)->count();
echo "👥 Nombre de membres dans ce dahira: $nombreMembres\n";

// Si aucun membre, associer quelques membres existants
if ($nombreMembres == 0) {
    echo "\n=== ASSOCIATION DES MEMBRES AU DAHIRA ===\n";
    $membres = App\Models\Membre::whereNull('dahira_id')->orWhere('dahira_id', '!=', $dahira->id)->take(10)->get();
    
    foreach ($membres as $membre) {
        $membre->dahira_id = $dahira->id;
        $membre->save();
        echo "✅ Membre associé: {$membre->prenom} {$membre->nom}\n";
    }
    
    $nouveauNombre = App\Models\Membre::where('dahira_id', $dahira->id)->count();
    echo "👥 Nouveau nombre de membres dans le dahira: $nouveauNombre\n";
}
