echo "Verification des utilisateurs dans dahira_api_db..."

echo "
SELECT 'USERS:' as info;
SELECT u.id, u.name, u.email, r.name as role, u.status, u.is_approved 
FROM users u 
LEFT JOIN roles r ON u.role_id = r.id;

SELECT 'ROLES:' as info;
SELECT * FROM roles;
" | mysql -u root dahira_api_db
