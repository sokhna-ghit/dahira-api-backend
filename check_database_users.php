<?php
/**
 * Script pour vérifier les utilisateurs dans la base de données dahira_api_db
 */

// Configuration de la base de données
$host = 'localhost';
$dbname = 'dahira_api_db';  // Nom correct de votre base
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base de données '$dbname' réussie\n\n";
    
    // 1. Vérifier les tables existantes
    echo "📋 Tables dans la base de données :\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    echo "\n";
    
    // 2. Vérifier les rôles
    echo "👤 Rôles disponibles :\n";
    try {
        $stmt = $pdo->query("SELECT id, name, description FROM roles");
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($roles) {
            foreach ($roles as $role) {
                echo "   - ID: {$role['id']}, Nom: {$role['name']}, Description: {$role['description']}\n";
            }
        } else {
            echo "   ❌ Aucun rôle trouvé\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erreur table roles: " . $e->getMessage() . "\n";
    }
    echo "\n";
    
    // 3. Vérifier les utilisateurs
    echo "👥 Utilisateurs dans la base :\n";
    try {
        $stmt = $pdo->query("
            SELECT u.id, u.name, u.email, u.status, u.is_approved, 
                   r.name as role_name, u.created_at
            FROM users u 
            LEFT JOIN roles r ON u.role_id = r.id 
            ORDER BY u.id
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($users) {
            echo "┌─────┬─────────────────────────┬─────────────────┬──────────────┬─────────┬──────────┐\n";
            echo "│ ID  │ Email                   │ Nom             │ Rôle         │ Statut  │ Approuvé │\n";
            echo "├─────┼─────────────────────────┼─────────────────┼──────────────┼─────────┼──────────┤\n";
            
            foreach ($users as $user) {
                $email = substr($user['email'], 0, 23);
                $name = substr($user['name'], 0, 15);
                $role = substr($user['role_name'] ?? 'N/A', 0, 12);
                $status = substr($user['status'] ?? 'N/A', 0, 7);
                $approved = $user['is_approved'] ? 'Oui' : 'Non';
                
                printf("│ %-3d │ %-23s │ %-15s │ %-12s │ %-7s │ %-8s │\n", 
                    $user['id'], $email, $name, $role, $status, $approved
                );
            }
            echo "└─────┴─────────────────────────┴─────────────────┴──────────────┴─────────┴──────────┘\n";
            
            echo "\n📧 Emails complets trouvés :\n";
            foreach ($users as $user) {
                echo "   - {$user['email']} (Rôle: {$user['role_name']}, Statut: {$user['status']})\n";
            }
        } else {
            echo "   ❌ Aucun utilisateur trouvé\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erreur table users: " . $e->getMessage() . "\n";
    }
    
    // 4. Vérifier spécifiquement les emails recherchés
    echo "\n🔍 Vérification d'emails spécifiques :\n";
    $emailsToCheck = [
        'sokhna@admin.sn',
        'admin@dahira.sn', 
        'test@admin.sn',
        'membre@example.com',
        'aminata@president.sn'
    ];
    
    foreach ($emailsToCheck as $email) {
        try {
            $stmt = $pdo->prepare("SELECT name, email, status FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "   ✅ $email -> Trouvé ({$user['name']}, statut: {$user['status']})\n";
            } else {
                echo "   ❌ $email -> Non trouvé\n";
            }
        } catch (Exception $e) {
            echo "   ❌ $email -> Erreur: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎯 RÉSUMÉ :\n";
    echo "Base de données: $dbname\n";
    echo "Nombre de tables: " . count($tables) . "\n";
    echo "Nombre d'utilisateurs: " . count($users ?? []) . "\n";
    echo "Nombre de rôles: " . count($roles ?? []) . "\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à '$dbname': " . $e->getMessage() . "\n";
    echo "\nVérifiez :\n";
    echo "1. Que la base '$dbname' existe\n";
    echo "2. Que MySQL/XAMPP est démarré\n";
    echo "3. Les informations de connexion (host, user, password)\n";
}
?>
