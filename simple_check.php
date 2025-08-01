<?php
echo "Verification des utilisateurs...\n";

$host = '127.0.0.1';
$dbname = 'dahira_api_db';
$username = 'root';
$password = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

echo "Utilisateurs trouvés:\n";
$stmt = $pdo->query("SELECT u.email, u.name, r.name as role FROM users u LEFT JOIN roles r ON u.role_id = r.id");
while ($row = $stmt->fetch()) {
    echo "- " . $row['email'] . " (" . $row['name'] . ") - Role: " . ($row['role'] ?? 'N/A') . "\n";
}
?>
