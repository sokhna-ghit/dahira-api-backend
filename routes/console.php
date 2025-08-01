<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Dahira;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('create:super-admin', function () {
    $this->info('🚀 Création du Super Admin...');
    
    // Créer les rôles s'ils n'existent pas
    $roles = [
        'super_admin' => 'Super Administrateur',
        'président' => 'Président',
        'secrétaire général' => 'Secrétaire Général', 
        'trésorier' => 'Trésorier',
        'membre' => 'Membre'
    ];
    
    foreach ($roles as $roleName => $roleDescription) {
        Role::firstOrCreate(['name' => $roleName]);
        $this->info('✅ Rôle créé/vérifié: ' . $roleName);
    }
    
    // Récupérer l'ID du rôle super_admin
    $superAdminRole = Role::where('name', 'super_admin')->first();
    
    // Vérifier si le Super Admin existe déjà
    $existingUser = User::where('email', 'sokhna@admin.com')->first();
    
    if ($existingUser) {
        $this->info('✅ Super Admin déjà existant : ' . $existingUser->name);
        $this->info('📧 Email: ' . $existingUser->email);
        $this->info('👑 Rôle: ' . ($existingUser->role ? $existingUser->role->name : 'Non défini'));
    } else {
        // Créer le Super Admin
        $superAdmin = User::create([
            'name' => 'Sokhna',
            'email' => 'sokhna@admin.com',
            'password' => Hash::make('password123'),
            'telephone' => '+221771234567',
            'role_id' => $superAdminRole->id,
            'dahira_id' => null,
        ]);
        
        $this->info('✅ Super Admin créé avec succès !');
        $this->info('📧 Email: sokhna@admin.com');
        $this->info('🔐 Mot de passe: password123');
        $this->info('👑 Rôle: super_admin');
    }
    
    // Créer quelques dahiras de test
    $dahirasTest = [
        [
            'nom' => 'Dahira Touba Médina',
            'region' => 'Dakar',
            'communaute' => 'Mourides',
            'description' => 'Dahira principal de la communauté mouride à Médina',
            'adresse' => 'Médina, Dakar',
            'statut' => 'actif',
        ],
        [
            'nom' => 'Dahira Tidiane Liberté',
            'region' => 'Dakar',
            'communaute' => 'Tidianes',
            'description' => 'Dahira de la confrérie tidiane à Liberté',
            'adresse' => 'Liberté 6, Dakar',
            'statut' => 'actif',
        ],
        [
            'nom' => 'Dahira Layène Yoff',
            'region' => 'Dakar',
            'communaute' => 'Layènes',
            'description' => 'Dahira de la communauté layène à Yoff',
            'adresse' => 'Yoff, Dakar',
            'statut' => 'actif',
        ],
    ];
    
    foreach ($dahirasTest as $dahiraData) {
        $existing = Dahira::where('nom', $dahiraData['nom'])->first();
        if (!$existing) {
            Dahira::create($dahiraData);
            $this->info('✅ Dahira créé: ' . $dahiraData['nom']);
        }
    }
    
    // Afficher les statistiques avec les relations
    $stats = [
        'total_users' => User::count(),
        'super_admins' => User::whereHas('role', function($q) { $q->where('name', 'super_admin'); })->count(),
        'presidents' => User::whereHas('role', function($q) { $q->where('name', 'président'); })->count(),
        'secretaires' => User::whereHas('role', function($q) { $q->where('name', 'secrétaire général'); })->count(),
        'tresoriers' => User::whereHas('role', function($q) { $q->where('name', 'trésorier'); })->count(),
        'membres' => User::whereHas('role', function($q) { $q->where('name', 'membre'); })->count(),
        'total_dahiras' => Dahira::count(),
    ];
    
    $this->info('');
    $this->info('📊 STATISTIQUES ACTUELLES:');
    $this->info('👥 Total utilisateurs: ' . $stats['total_users']);
    $this->info('👑 Super Admins: ' . $stats['super_admins']);
    $this->info('🟢 Présidents: ' . $stats['presidents']);
    $this->info('🔵 Secrétaires: ' . $stats['secretaires']);
    $this->info('🟠 Trésoriers: ' . $stats['tresoriers']);
    $this->info('🟣 Membres: ' . $stats['membres']);
    $this->info('🕌 Total Dahiras: ' . $stats['total_dahiras']);
    
    $this->info('');
    $this->info('🎉 Super Admin prêt !');
    $this->info('Vous pouvez maintenant vous connecter avec:');
    $this->info('Email: sokhna@admin.com');
    $this->info('Mot de passe: password123');
    
})->purpose('Créer le compte Super Admin et des données de test');

Artisan::command('debug:user {email}', function ($email) {
    $this->info('🔍 DEBUG UTILISATEUR: ' . $email);
    
    $user = User::where('email', $email)->first();
    
    if (!$user) {
        $this->error('❌ Utilisateur non trouvé');
        return;
    }
    
    $this->info('👤 Utilisateur trouvé:');
    $this->info('  ID: ' . $user->id);
    $this->info('  Nom: ' . $user->name);
    $this->info('  Email: ' . $user->email);
    $this->info('  Role ID: ' . ($user->role_id ?? 'NULL'));
    
    // Charger la relation
    $user->load('role');
    
    if ($user->role) {
        $this->info('🎭 Rôle associé:');
        $this->info('  ID: ' . $user->role->id);
        $this->info('  Nom: ' . $user->role->name);
    } else {
        $this->error('❌ Aucun rôle associé !');
        
        // Lister tous les rôles disponibles
        $this->info('📋 Rôles disponibles:');
        $roles = Role::all();
        foreach ($roles as $role) {
            $this->info('  ' . $role->id . ': ' . $role->name);
        }
    }
    
})->purpose('Débugger un utilisateur spécifique');

Artisan::command('debug:users', function () {
    $this->info('👥 TOUS LES UTILISATEURS:');
    
    $users = User::with('role')->get();
    
    if ($users->isEmpty()) {
        $this->error('❌ Aucun utilisateur trouvé dans la base de données');
        return;
    }
    
    foreach ($users as $user) {
        $this->info('---------------------------');
        $this->info('ID: ' . $user->id);
        $this->info('Nom: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('Role ID: ' . ($user->role_id ?? 'NULL'));
        $this->info('Rôle: ' . ($user->role ? $user->role->name : 'Aucun'));
    }
    
})->purpose('Lister tous les utilisateurs');

Artisan::command('fix:super-admin', function () {
    $this->info('🔧 CORRECTION DU SUPER ADMIN...');
    
    // Supprimer tous les utilisateurs Sokhna existants
    $deletedUsers = User::where('name', 'LIKE', '%sokhna%')->orWhere('name', 'LIKE', '%Sokhna%')->delete();
    $this->info("🗑️ Supprimé $deletedUsers utilisateur(s) Sokhna existant(s)");
    
    // Vérifier/créer le rôle super_admin
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if (!$superAdminRole) {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrateur',
            'description' => 'Accès complet à toutes les fonctionnalités'
        ]);
        $this->info('✅ Rôle super_admin créé avec ID: ' . $superAdminRole->id);
    } else {
        $this->info('✅ Rôle super_admin existant avec ID: ' . $superAdminRole->id);
    }
    
    // Créer le nouveau Super Admin
    $superAdmin = User::create([
        'name' => 'Sokhna Super Admin',
        'email' => 'sokhna@dahira.sn',
        'password' => Hash::make('password123'),
        'role_id' => $superAdminRole->id
    ]);
    
    $this->info('🎉 Super Admin créé avec succès !');
    $this->info('  ID: ' . $superAdmin->id);
    $this->info('  Nom: ' . $superAdmin->name);
    $this->info('  Email: ' . $superAdmin->email);
    $this->info('  Role ID: ' . $superAdmin->role_id);
    
    // Vérifier la relation
    $superAdmin->load('role');
    if ($superAdmin->role) {
        $this->info('  Rôle: ' . $superAdmin->role->name);
    }
    
})->purpose('Corriger et recréer le Super Admin');

Artisan::command('create:admin-sokhna', function () {
    $this->info('👤 CRÉATION SUPER ADMIN AVEC EMAIL sokhna@admin.sn...');
    
    // Vérifier/créer le rôle super_admin
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if (!$superAdminRole) {
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrateur',
            'description' => 'Accès complet à toutes les fonctionnalités'
        ]);
        $this->info('✅ Rôle super_admin créé avec ID: ' . $superAdminRole->id);
    } else {
        $this->info('✅ Rôle super_admin existant avec ID: ' . $superAdminRole->id);
    }
    
    // Supprimer l'utilisateur existant s'il existe
    User::where('email', 'sokhna@admin.sn')->delete();
    
    // Créer le nouveau Super Admin
    $superAdmin = User::create([
        'name' => 'Sokhna Super Admin',
        'email' => 'sokhna@admin.sn',
        'password' => Hash::make('password123'),
        'role_id' => $superAdminRole->id
    ]);
    
    $this->info('🎉 Super Admin créé avec succès !');
    $this->info('  Email: ' . $superAdmin->email);
    $this->info('  Mot de passe: password123');
    $this->info('  Role ID: ' . $superAdmin->role_id);
    
    // Vérifier la relation
    $superAdmin->load('role');
    if ($superAdmin->role) {
        $this->info('  Rôle: ' . $superAdmin->role->name);
    }
    
})->purpose('Créer Super Admin avec email sokhna@admin.sn');

Artisan::command('check:data', function () {
    $this->info('📊 VÉRIFICATION DES DONNÉES...');
    
    // Compter dahiras
    $dahirasCount = \App\Models\Dahira::count();
    $this->info("📋 Dahiras dans la DB: $dahirasCount");
    
    if ($dahirasCount > 0) {
        $dahiras = \App\Models\Dahira::all();
        foreach ($dahiras as $dahira) {
            $this->info("  - {$dahira->nom} (ID: {$dahira->id}, Statut: {$dahira->statut})");
        }
    }
    
    // Compter utilisateurs
    $usersCount = \App\Models\User::count();
    $this->info("👥 Utilisateurs dans la DB: $usersCount");
    
    if ($usersCount > 0) {
        $users = \App\Models\User::with('role')->get();
        foreach ($users as $user) {
            $role = $user->role ? $user->role->name : 'Aucun';
            $this->info("  - {$user->name} ({$user->email}) - Rôle: {$role}");
        }
    }
    
})->purpose('Vérifier les données dans la base');

Artisan::command('test:api {endpoint}', function ($endpoint) {
    $this->info('🧪 TEST API: ' . $endpoint);
    
    // Créer un utilisateur super admin temporaire pour le test
    $user = \App\Models\User::where('email', 'sokhna@admin.sn')->first();
    if (!$user) {
        $this->error('❌ Utilisateur super admin non trouvé');
        return;
    }
    
    // Simuler une requête authentifiée
    $token = $user->createToken('test-token')->plainTextToken;
    $this->info('🔑 Token créé: ' . substr($token, 0, 20) . '...');
    
    try {
        if ($endpoint === 'dahiras') {
            $dahiras = \App\Models\Dahira::with('membres')->get();
            $this->info('📋 Dahiras trouvées: ' . $dahiras->count());
            foreach ($dahiras as $dahira) {
                $this->info("  - {$dahira->nom} (Membres: {$dahira->membres->count()})");
            }
        } elseif ($endpoint === 'users') {
            $users = \App\Models\User::with('role')->get();
            $this->info('👥 Utilisateurs trouvés: ' . $users->count());
            foreach ($users as $user) {
                $role = $user->role ? $user->role->name : 'Aucun';
                $this->info("  - {$user->name} - {$role}");
            }
        }
    } catch (\Exception $e) {
        $this->error('❌ Erreur: ' . $e->getMessage());
    }
    
})->purpose('Tester les endpoints API');

Artisan::command('seed:membres', function () {
    $this->info('👥 CRÉATION DE MEMBRES POUR LES DAHIRAS...');
    
    $dahiras = \App\Models\Dahira::where('id', '>', 1)->get(); // Tous sauf Dahira Test
    
    foreach ($dahiras as $dahira) {
        $this->info("📋 Ajout de membres pour: {$dahira->nom}");
        
        // Créer 2-3 membres par dahira
        for ($i = 1; $i <= 3; $i++) {
            $membre = \App\Models\Membre::create([
                'nom' => "Membre {$i}",
                'prenom' => "Prenom {$i}",
                'email' => strtolower(str_replace([' ', 'é', 'è'], ['', 'e', 'e'], $dahira->nom)) . ".membre{$i}@test.sn",
                'telephone' => "77" . rand(1000000, 9999999),
                'adresse' => "Adresse test {$i}, Dakar",
                'genre' => $i % 2 == 0 ? 'F' : 'M',
                'date_naissance' => now()->subYears(rand(20, 60))->format('Y-m-d'),
                'dahira_id' => $dahira->id,
                'active' => true,
            ]);
            $this->info("  ✅ {$membre->prenom} {$membre->nom} créé");
        }
    }
    
    $this->info('🎉 Membres créés avec succès !');
    
})->purpose('Créer des membres de test pour les dahiras');

Artisan::command('show:dahiras', function () {
    $this->info('📋 STRUCTURE DES DAHIRAS...');
    
    $dahiras = \App\Models\Dahira::all();
    foreach ($dahiras as $dahira) {
        $this->info('---------------------------');
        $this->info('ID: ' . $dahira->id);
        $this->info('Nom: ' . $dahira->nom);
        $this->info('Ville: ' . ($dahira->ville ?? 'NULL'));
        $this->info('Adresse: ' . ($dahira->adresse ?? 'NULL'));
        $this->info('Statut: ' . ($dahira->statut ?? 'NULL'));
        $this->info('Créé: ' . $dahira->created_at);
    }
    
})->purpose('Afficher la structure des dahiras');

Artisan::command('update:dahiras-ville', function () {
    $this->info('🏙️ MISE À JOUR DES VILLES DES DAHIRAS...');
    
    $updates = [
        1 => ['ville' => 'Dakar', 'adresse' => 'Plateau, Dakar'],
        2 => ['ville' => 'Dakar', 'adresse' => 'Médina, Dakar'],
        3 => ['ville' => 'Dakar', 'adresse' => 'Liberté 6, Dakar'],
        4 => ['ville' => 'Dakar', 'adresse' => 'Yoff, Dakar'],
        5 => ['ville' => 'Thiès', 'adresse' => 'Sud Stade Lat Dior, Thiès'],
    ];
    
    foreach ($updates as $id => $data) {
        $dahira = \App\Models\Dahira::find($id);
        if ($dahira) {
            $dahira->update($data);
            $this->info("✅ {$dahira->nom} - Ville: {$data['ville']}");
        }
    }
    
    $this->info('🎉 Villes mises à jour !');
    
})->purpose('Mettre à jour les villes des dahiras');

Artisan::command('create:test-users', function () {
    $this->info('👥 CRÉATION D\'UTILISATEURS DE TEST POUR TOUS LES RÔLES...');
    
    // Données des utilisateurs de test
    $testUsers = [
        [
            'name' => 'Aminata Président',
            'email' => 'aminata@president.sn',
            'password' => 'password123',
            'role' => 'président',
        ],
        [
            'name' => 'Omar Secrétaire',
            'email' => 'omar@secretaire.sn',
            'password' => 'password123',
            'role' => 'secrétaire général',
        ],
        [
            'name' => 'Fatima Trésorière',
            'email' => 'fatima@tresorier.sn',
            'password' => 'password123',
            'role' => 'trésorier',
        ],
        [
            'name' => 'Moussa Membre',
            'email' => 'moussa@membre.sn',
            'password' => 'password123',
            'role' => 'membre',
        ],
    ];
    
    foreach ($testUsers as $userData) {
        // Vérifier si l'utilisateur existe déjà
        if (\App\Models\User::where('email', $userData['email'])->exists()) {
            $this->info("⚠️ {$userData['name']} existe déjà");
            continue;
        }
        
        // Trouver le rôle
        $role = \App\Models\Role::where('name', $userData['role'])->first();
        if (!$role) {
            $this->error("❌ Rôle '{$userData['role']}' non trouvé");
            continue;
        }
        
        // Créer l'utilisateur
        $user = \App\Models\User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => \Hash::make($userData['password']),
            'role_id' => $role->id,
        ]);
        
        $this->info("✅ {$user->name} créé avec rôle {$role->name}");
    }
    
    $this->info('🎉 Utilisateurs de test créés !');
    $this->info('');
    $this->info('📧 IDENTIFIANTS DE CONNEXION :');
    $this->info('• Président: aminata@president.sn / password123');
    $this->info('• Secrétaire: omar@secretaire.sn / password123');
    $this->info('• Trésorier: fatima@tresorier.sn / password123');
    $this->info('• Membre: moussa@membre.sn / password123');
    $this->info('• Super Admin: sokhna@admin.sn / password123');
    
})->purpose('Créer des utilisateurs de test pour tous les rôles');
