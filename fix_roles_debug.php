<?php

require_once 'vendor/autoload.php';

// Démarrer Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSTIC ROLES ET UTILISATEURS ===\n";

// 1. Vérifier la structure de la table roles
echo "\n1. STRUCTURE TABLE ROLES :\n";
$rolesColumns = Schema::getColumnListing('roles');
echo "Colonnes dans table roles: " . implode(', ', $rolesColumns) . "\n";

// 2. Vérifier les rôles existants
echo "\n2. ROLES EXISTANTS :\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "ID: {$role->id}";
    if (in_array('name', $rolesColumns)) {
        echo " - Name: {$role->name}";
    }
    if (in_array('nom', $rolesColumns)) {
        echo " - Nom: " . ($role->nom ?? 'NULL');
    }
    if (in_array('libelle', $rolesColumns)) {
        echo " - Libelle: " . ($role->libelle ?? 'NULL');
    }
    echo "\n";
}

// 3. Vérifier l'utilisateur président
echo "\n3. UTILISATEUR PRÉSIDENT :\n";
$user = User::where('email', 'aminata@president.sn')->first();
if ($user) {
    echo "✅ Utilisateur trouvé: {$user->email}\n";
    echo "Role ID: " . ($user->role_id ?? 'NULL') . "\n";
    echo "Dahira ID: " . ($user->dahira_id ?? 'NULL') . "\n";
    
    // Tester la relation role
    try {
        $role = $user->role;
        if ($role) {
            echo "✅ Relation role fonctionne\n";
            echo "Role trouvé - ID: {$role->id}\n";
            if (isset($role->name)) echo "Name: {$role->name}\n";
            if (isset($role->nom)) echo "Nom: {$role->nom}\n";
            if (isset($role->libelle)) echo "Libelle: {$role->libelle}\n";
        } else {
            echo "❌ Relation role retourne null\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur relation role: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Utilisateur non trouvé\n";
}

// 4. Créer les rôles si ils n'existent pas
echo "\n4. CRÉATION/VÉRIFICATION DES RÔLES :\n";
$rolesACreer = [
    ['name' => 'super_admin', 'libelle' => 'Super Administrateur'],
    ['name' => 'président', 'libelle' => 'Président'],
    ['name' => 'secrétaire général', 'libelle' => 'Secrétaire Général'],
    ['name' => 'trésorier', 'libelle' => 'Trésorier'],
    ['name' => 'membre', 'libelle' => 'Membre'],
];

foreach ($rolesACreer as $roleData) {
    $champNom = in_array('name', $rolesColumns) ? 'name' : (in_array('nom', $rolesColumns) ? 'nom' : 'libelle');
    
    $role = Role::where($champNom, $roleData['name'])->first();
    if (!$role) {
        $nouveauRole = new Role();
        if (in_array('name', $rolesColumns)) {
            $nouveauRole->name = $roleData['name'];
        }
        if (in_array('nom', $rolesColumns)) {
            $nouveauRole->nom = $roleData['name'];
        }
        if (in_array('libelle', $rolesColumns)) {
            $nouveauRole->libelle = $roleData['libelle'];
        }
        $nouveauRole->save();
        echo "✅ Rôle créé: {$roleData['name']} (ID: {$nouveauRole->id})\n";
    } else {
        echo "✅ Rôle existe: {$roleData['name']} (ID: {$role->id})\n";
    }
}

// 5. Assigner le rôle président à l'utilisateur
echo "\n5. ASSIGNATION RÔLE PRÉSIDENT :\n";
if ($user) {
    $champNom = in_array('name', $rolesColumns) ? 'name' : (in_array('nom', $rolesColumns) ? 'nom' : 'libelle');
    $rolePresident = Role::where($champNom, 'président')->first();
    
    if ($rolePresident) {
        $user->role_id = $rolePresident->id;
        $user->save();
        echo "✅ Rôle président assigné à l'utilisateur (Role ID: {$rolePresident->id})\n";
        
        // Test final
        $userTest = User::where('email', 'aminata@president.sn')->with('role')->first();
        if ($userTest && $userTest->role) {
            echo "✅ TEST FINAL : Rôle correctement assigné\n";
            echo "Nom du rôle: " . ($userTest->role->name ?? $userTest->role->nom ?? $userTest->role->libelle) . "\n";
        } else {
            echo "❌ TEST FINAL : Problème persiste\n";
        }
    } else {
        echo "❌ Rôle président non trouvé\n";
    }
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";
