<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 === VÉRIFICATION DES UTILISATEURS ===\n\n";

try {
    // Lister tous les utilisateurs
    $users = User::with('role')->get();
    
    if ($users->count() > 0) {
        echo "👥 Utilisateurs existants :\n";
        echo "┌─────┬─────────────────────────┬─────────────────┬──────────────┐\n";
        echo "│ ID  │ Email                   │ Nom             │ Rôle         │\n";
        echo "├─────┼─────────────────────────┼─────────────────┼──────────────┤\n";
        
        foreach ($users as $user) {
            $roleName = $user->role ? $user->role->name : 'N/A';
            printf("│ %-3d │ %-23s │ %-15s │ %-12s │\n", 
                $user->id, 
                substr($user->email, 0, 23), 
                substr($user->name, 0, 15), 
                $roleName
            );
        }
        echo "└─────┴─────────────────────────┴─────────────────┴──────────────┘\n\n";
    } else {
        echo "❌ Aucun utilisateur trouvé dans la base de données.\n\n";
    }
    
    // Vérifier si sokhna@admin.sn existe
    $sokhnaUser = User::where('email', 'sokhna@admin.sn')->first();
    
    if ($sokhnaUser) {
        echo "✅ L'utilisateur sokhna@admin.sn existe !\n";
        echo "   - Nom: {$sokhnaUser->name}\n";
        echo "   - Rôle: " . ($sokhnaUser->role ? $sokhnaUser->role->name : 'N/A') . "\n";
        echo "   - Créé le: {$sokhnaUser->created_at}\n\n";
        
        // Test du mot de passe
        echo "🔐 Test des mots de passe courants :\n";
        $passwords = ['password', 'admin123', 'sokhna123', '123456', 'admin'];
        
        foreach ($passwords as $pwd) {
            if (Hash::check($pwd, $sokhnaUser->password)) {
                echo "✅ Mot de passe trouvé: '$pwd'\n";
                break;
            }
        }
    } else {
        echo "❌ L'utilisateur sokhna@admin.sn n'existe pas.\n";
        echo "🔧 Création de l'utilisateur admin...\n\n";
        
        // Vérifier que le rôle admin existe
        $roleAdmin = Role::where('name', 'admin')->first();
        if (!$roleAdmin) {
            echo "⚠️  Rôle 'admin' non trouvé. Création...\n";
            $roleAdmin = Role::create([
                'name' => 'admin',
                'description' => 'Administrateur du système'
            ]);
        }
        
        // Créer l'utilisateur admin
        $adminUser = User::create([
            'name' => 'Sokhna Admin',
            'email' => 'sokhna@admin.sn',
            'password' => Hash::make('admin123'),
            'role_id' => $roleAdmin->id,
            'email_verified_at' => now(),
            'status' => 'approved',
            'is_approved' => true,
            'approved_at' => now(),
        ]);
        
        echo "✅ Utilisateur admin créé avec succès !\n";
        echo "   📧 Email: sokhna@admin.sn\n";
        echo "   🔑 Mot de passe: admin123\n";
        echo "   👤 Rôle: admin\n\n";
    }
    
    // Créer d'autres utilisateurs de test si nécessaire
    $testUsers = [
        [
            'email' => 'president@dahira.sn',
            'name' => 'Président Test',
            'role' => 'président',
            'password' => 'president123'
        ],
        [
            'email' => 'membre@example.com',
            'name' => 'Membre Test',
            'role' => 'membre',
            'password' => 'password'
        ]
    ];
    
    foreach ($testUsers as $userData) {
        $existingUser = User::where('email', $userData['email'])->first();
        if (!$existingUser) {
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'role_id' => $role->id,
                    'email_verified_at' => now(),
                    'status' => 'approved',
                    'is_approved' => true,
                    'approved_at' => now(),
                ]);
                echo "✅ Utilisateur {$userData['email']} créé (mot de passe: {$userData['password']})\n";
            }
        }
    }
    
    echo "\n🎯 Comptes de test disponibles :\n";
    echo "┌─────────────────────────┬──────────────┬──────────────┐\n";
    echo "│ Email                   │ Mot de passe │ Rôle         │\n";
    echo "├─────────────────────────┼──────────────┼──────────────┤\n";
    echo "│ sokhna@admin.sn         │ admin123     │ admin        │\n";
    echo "│ president@dahira.sn     │ president123 │ président    │\n";
    echo "│ membre@example.com      │ password     │ membre       │\n";
    echo "└─────────────────────────┴──────────────┴──────────────┘\n\n";
    
    echo "✅ Vérification terminée !\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
