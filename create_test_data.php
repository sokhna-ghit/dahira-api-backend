<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\User;
use App\Models\Role;
use App\Models\Dahira;
use App\Models\Membre;
use Illuminate\Support\Facades\Hash;

echo "=== CRÉATION DE DONNÉES DE TEST ===\n\n";

try {
    // 1. Créer des dahiras de test
    echo "1. CRÉATION DES DAHIRAS :\n";
    
    $dahiras = [
        ['nom' => 'Dahira Touba', 'ville' => 'Touba'],
        ['nom' => 'Dahira Thiès', 'ville' => 'Thiès'],
        ['nom' => 'Dahira Kaolack', 'ville' => 'Kaolack'],
    ];
    
    foreach ($dahiras as $dahiraData) {
        $dahira = Dahira::firstOrCreate(
            ['nom' => $dahiraData['nom']],
            $dahiraData
        );
        echo "   ✅ Dahira: {$dahira->nom} (ID: {$dahira->id})\n";
    }
    
    // 2. Créer des utilisateurs admin de test
    echo "\n2. CRÉATION D'UTILISATEURS ADMIN :\n";
    
    $adminRole = Role::where('name', 'admin')->first();
    if (!$adminRole) {
        $adminRole = Role::create(['name' => 'admin']);
        echo "   ✅ Rôle admin créé\n";
    }
    
    $adminUsers = [
        [
            'name' => 'Admin Principal',
            'email' => 'admin@dahira.sn',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ],
        [
            'name' => 'Admin Test',
            'email' => 'test@admin.sn', 
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
        ]
    ];
    
    foreach ($adminUsers as $userData) {
        $user = User::firstOrCreate(
            ['email' => $userData['email']],
            $userData
        );
        echo "   ✅ Admin: {$user->name} ({$user->email})\n";
    }
    
    // 3. Créer des membres de test
    echo "\n3. CRÉATION DE MEMBRES DE TEST :\n";
    
    $dahirasIds = Dahira::pluck('id')->toArray();
    $membresData = [
        ['nom' => 'Diop', 'prenom' => 'Fatou', 'email' => 'fatou.diop@test.sn', 'telephone' => '771234567'],
        ['nom' => 'Ndiaye', 'prenom' => 'Moussa', 'email' => 'moussa.ndiaye@test.sn', 'telephone' => '772345678'],
        ['nom' => 'Fall', 'prenom' => 'Aïssatou', 'email' => 'aissatou.fall@test.sn', 'telephone' => '773456789'],
        ['nom' => 'Ba', 'prenom' => 'Omar', 'email' => 'omar.ba@test.sn', 'telephone' => '774567890'],
        ['nom' => 'Cissé', 'prenom' => 'Marième', 'email' => 'marieme.cisse@test.sn', 'telephone' => '775678901'],
    ];
    
    foreach ($membresData as $membreData) {
        $membreData['dahira_id'] = $dahirasIds[array_rand($dahirasIds)];
        $membreData['genre'] = rand(0, 1) ? 'masculin' : 'féminin';
        $membreData['date_naissance'] = '1990-01-01';
        $membreData['adresse'] = 'Adresse test';
        $membreData['active'] = rand(0, 1) ? true : false;
        
        $membre = Membre::firstOrCreate(
            ['email' => $membreData['email']],
            $membreData
        );
        echo "   ✅ Membre: {$membre->prenom} {$membre->nom} (Dahira ID: {$membre->dahira_id})\n";
    }
    
    echo "\n✅ DONNÉES DE TEST CRÉÉES AVEC SUCCÈS !\n";
    echo "🔑 Vous pouvez vous connecter avec:\n";
    echo "   - admin@dahira.sn / admin123 (Admin)\n";
    echo "   - test@admin.sn / admin123 (Admin)\n";
    echo "   - aminata@president.sn / password123 (Président)\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN CRÉATION DONNÉES ===\n";
