<?php

// Script pour créer un utilisateur Super Admin
// À exécuter une seule fois pour initialiser le système

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Configuration de la base de données (ajustez selon vos paramètres)
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'dahira_api_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Créer le Super Admin (Sokhna)
    $existingUser = DB::table('users')->where('email', 'sokhna@admin.com')->first();
    
    if ($existingUser) {
        echo "✅ Super Admin déjà existant : " . $existingUser->name . "\n";
    } else {
        DB::table('users')->insert([
            'name' => 'Sokhna',
            'email' => 'sokhna@admin.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'telephone' => '+221771234567',
            'role' => 'super_admin',
            'dahira_id' => null, // Super admin n'appartient à aucun dahira spécifique
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        echo "✅ Super Admin créé avec succès !\n";
        echo "📧 Email: sokhna@admin.com\n";
        echo "🔐 Mot de passe: password123\n";
        echo "👑 Rôle: super_admin\n";
    }
    
    // Créer quelques dahiras de test si nécessaire
    $dahirasTest = [
        [
            'nom' => 'Dahira Touba Médina',
            'region' => 'Dakar',
            'communaute' => 'Mourides',
            'description' => 'Dahira principal de la communauté mouride à Médina',
            'adresse' => 'Médina, Dakar',
            'statut' => 'actif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'nom' => 'Dahira Tidiane Liberté',
            'region' => 'Dakar',
            'communaute' => 'Tidianes',
            'description' => 'Dahira de la confrérie tidiane à Liberté',
            'adresse' => 'Liberté 6, Dakar',
            'statut' => 'actif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'nom' => 'Dahira Layène Yoff',
            'region' => 'Dakar',
            'communaute' => 'Layènes',
            'description' => 'Dahira de la communauté layène à Yoff',
            'adresse' => 'Yoff, Dakar',
            'statut' => 'actif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
    ];
    
    foreach ($dahirasTest as $dahira) {
        $existing = DB::table('dahiras')->where('nom', $dahira['nom'])->first();
        if (!$existing) {
            DB::table('dahiras')->insert($dahira);
            echo "✅ Dahira créé: " . $dahira['nom'] . "\n";
        }
    }
    
    // Compter les utilisateurs par rôle
    $stats = [
        'total_users' => DB::table('users')->count(),
        'super_admins' => DB::table('users')->where('role', 'super_admin')->count(),
        'presidents' => DB::table('users')->where('role', 'président')->count(),
        'secretaires' => DB::table('users')->where('role', 'secrétaire général')->count(),
        'tresoriers' => DB::table('users')->where('role', 'trésorier')->count(),
        'membres' => DB::table('users')->where('role', 'membre')->count(),
        'total_dahiras' => DB::table('dahiras')->count(),
    ];
    
    echo "\n📊 STATISTIQUES ACTUELLES:\n";
    echo "👥 Total utilisateurs: " . $stats['total_users'] . "\n";
    echo "👑 Super Admins: " . $stats['super_admins'] . "\n";
    echo "🟢 Présidents: " . $stats['presidents'] . "\n";
    echo "🔵 Secrétaires: " . $stats['secretaires'] . "\n";
    echo "🟠 Trésoriers: " . $stats['tresoriers'] . "\n";
    echo "🟣 Membres: " . $stats['membres'] . "\n";
    echo "🕌 Total Dahiras: " . $stats['total_dahiras'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🎉 Script terminé !\n";
echo "Vous pouvez maintenant vous connecter avec:\n";
echo "Email: sokhna@admin.com\n";
echo "Mot de passe: password123\n";

?>
