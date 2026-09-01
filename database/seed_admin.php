<?php
// database/seed_admin.php — run once to create default admin

require __DIR__ . '/../config/dbConfig.php';

$name = 'System Admin';
$username = 'admin';
$email = 'admin@example.com';
$plainPassword = 'admin123';
$role = 'admin';  // Must be lowercase
$status = 'active';  // Must be lowercase

require __DIR__ . '/../utilities/PasswordValidator.php';
$validation = PasswordValidator::validateStrength($plainPassword);
if (!$validation['valid']) {    
    echo "Password validation failed: " . implode(', ', $validation['message']);
    exit;
}

require __DIR__ . '/../utilities/PasswordHasher.php';
$hash = PasswordHasher::hash($plainPassword);

$conn = DBConfig::getConnection();

$stmt = $conn->prepare(
    "INSERT INTO users (name, username, email, password, role, status)
     VALUES (:name, :username, :email, :password, :role, :status)"
);

$stmt->execute([
    ':name'      => $name,
    ':username'  => $username,
    ':email'     => $email,
    ':password'  => $hash,
    ':role'      => $role,
    ':status'    => $status
]);

echo "Admin user created successfully!\n";
echo "Username: " . $username . "\n";
echo "Password: " . $plainPassword . "\n";
echo "User ID: " . $conn->lastInsertId();