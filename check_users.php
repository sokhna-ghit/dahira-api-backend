<?php

// Test simple SQL direct
$host = '127.0.0.1';
$db = 'dahira_api_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 UTILISATEURS CONNECTÉS\n";
    echo "========================\n\n";
    
    // Lister les users
    $stmt = $pdo->query("SELECT id, name, email FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "User ID {$user['id']}: {$user['name']} ({$user['email']})\n";
    }
    
    echo "\n📋 MEMBRES ASSOCIÉS\n";
    echo "==================\n\n";
    
    // Lister les membres
    $stmt = $pdo->query("SELECT id, nom, prenom, email, user_id FROM membres LIMIT 10");
    $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($membres as $membre) {
        echo "Membre ID {$membre['id']}: {$membre['nom']} {$membre['prenom']} ({$membre['email']}) - User ID: {$membre['user_id']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur DB: " . $e->getMessage() . "\n";
}
