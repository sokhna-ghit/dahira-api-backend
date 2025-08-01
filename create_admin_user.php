<?php
/**
 * Script simple pour créer un utilisateur admin
 */

// Configuration de la base de données
$host = 'localhost';
$dbname = 'dahira_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base de données réussie\n";
    
    // Vérifier les utilisateurs existants
    $stmt = $pdo->query("SELECT u.id, u.name, u.email, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n👥 Utilisateurs existants :\n";
    foreach ($users as $user) {
        echo "- {$user['email']} ({$user['name']}) - Rôle: {$user['role_name']}\n";
    }
    
    // Vérifier si sokhna@admin.sn existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['sokhna@admin.sn']);
    $sokhnaUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($sokhnaUser) {
        echo "\n✅ L'utilisateur sokhna@admin.sn existe déjà !\n";
    } else {
        echo "\n❌ L'utilisateur sokhna@admin.sn n'existe pas. Création...\n";
        
        // Récupérer l'ID du rôle admin
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin'");
        $stmt->execute();
        $adminRole = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$adminRole) {
            // Créer le rôle admin s'il n'existe pas
            $stmt = $pdo->prepare("INSERT INTO roles (name, description, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute(['admin', 'Administrateur du système']);
            $adminRoleId = $pdo->lastInsertId();
            echo "✅ Rôle admin créé avec ID: $adminRoleId\n";
        } else {
            $adminRoleId = $adminRole['id'];
            echo "✅ Rôle admin trouvé avec ID: $adminRoleId\n";
        }
        
        // Créer l'utilisateur admin
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role_id, email_verified_at, status, is_approved, approved_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), 'approved', 1, NOW(), NOW(), NOW())
        ");
        
        $stmt->execute([
            'Sokhna Admin',
            'sokhna@admin.sn',
            $hashedPassword,
            $adminRoleId
        ]);
        
        echo "✅ Utilisateur admin créé avec succès !\n";
        echo "   📧 Email: sokhna@admin.sn\n";
        echo "   🔑 Mot de passe: admin123\n";
    }
    
    echo "\n🎯 Vous pouvez maintenant vous connecter avec :\n";
    echo "Email: sokhna@admin.sn\n";
    echo "Mot de passe: admin123\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage() . "\n";
}
?>
