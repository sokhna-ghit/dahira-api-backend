-- Script SQL pour créer l'utilisateur admin sokhna@admin.sn
-- À exécuter dans phpMyAdmin ou votre interface MySQL

-- 1. Créer le rôle admin s'il n'existe pas
INSERT IGNORE INTO roles (name, description, created_at, updated_at) 
VALUES ('admin', 'Administrateur du système', NOW(), NOW());

-- 2. Créer l'utilisateur sokhna@admin.sn
-- Mot de passe: admin123 (hashé avec bcrypt)
INSERT IGNORE INTO users (
    name, 
    email, 
    password, 
    role_id, 
    email_verified_at, 
    status, 
    is_approved, 
    approved_at,
    created_at, 
    updated_at
) VALUES (
    'Sokhna Admin',
    'sokhna@admin.sn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password = 'password'
    (SELECT id FROM roles WHERE name = 'admin' LIMIT 1),
    NOW(),
    'approved',
    1,
    NOW(),
    NOW(),
    NOW()
);

-- 3. Créer aussi un membre de test
INSERT IGNORE INTO users (
    name, 
    email, 
    password, 
    role_id, 
    email_verified_at, 
    status, 
    is_approved, 
    approved_at,
    created_at, 
    updated_at
) VALUES (
    'Membre Test',
    'membre@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password = 'password'
    (SELECT id FROM roles WHERE name = 'membre' LIMIT 1),
    NOW(),
    'approved',
    1,
    NOW(),
    NOW(),
    NOW()
);

-- Vérifier les utilisateurs créés
SELECT u.id, u.name, u.email, r.name as role, u.status, u.is_approved 
FROM users u 
LEFT JOIN roles r ON u.role_id = r.id 
WHERE u.email IN ('sokhna@admin.sn', 'membre@example.com');
