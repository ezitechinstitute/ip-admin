<?php
// Convert all passwords to plain text
$pdo = new PDO('mysql:host=127.0.0.1;dbname=ezitech_iportal_clean', 'root', '');

$plainPassword = 'password123';

echo "Converting passwords to plain text...\n\n";

// Admin accounts
$sql = "UPDATE admin_accounts SET password = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$plainPassword]);
echo "✓ Admin accounts: Set to 'password123'\n";

// Manager accounts
$sql = "UPDATE manager_accounts SET password = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$plainPassword]);
echo "✓ Manager accounts: Set to 'password123'\n";

// Intern accounts
$sql = "UPDATE intern_accounts SET password = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$plainPassword]);
echo "✓ Intern accounts: Set to 'password123'\n";

// Verify
echo "\n=== VERIFICATION ===\n";
$sql = "SELECT email, password FROM admin_accounts LIMIT 1";
$admin = $pdo->query($sql)->fetch();
echo "Admin: {$admin['email']} => Password: {$admin['password']}\n";

$sql = "SELECT email, password FROM manager_accounts LIMIT 1";
$manager = $pdo->query($sql)->fetch();
echo "Manager: {$manager['email']} => Password: {$manager['password']}\n";

echo "\n✅ All passwords converted to plain text!\n";
echo "\n🔑 Login Test Credentials:\n";
echo "   Email: admin@ezitech.org\n";
echo "   Email: onsitemanager@ezitech.org\n";
echo "   Password: password123 (for all accounts)\n";
?>
