<?php
// database/seed_admin.php — run once from terminal

require __DIR__ . '/../config/dbConfig.php'; // gives you $pdo

$name = 'System Admin';
$username = 'admin123';
$email = 'admin@example.com';
$plainPassword = 'admin@123!';
$role = 'ADMIN';

require __DIR__ . '/../utilities/PasswordValidator.php';
$validation = PasswordValidator::validateStrength($plainPassword);
if (!$validation['valid']) {    
    echo "Password validation failed: " . implode(', ', $validation['message']);
    exit;
}

require __DIR__ . '/../utilities/PasswordHasher.php';
$hash = PasswordHasher::hash($plainPassword);


$stmt = $pdo->prepare(
    "INSERT INTO users (name, username, email, password, role, status)
     VALUES (:name, :username, :email, :password, :role, :status)"
);

$stmt->execute([
    ':name'          => $name,
    ':username'      => $username,
    ':email'         => $email,
    ':password'      => $hash,
    ':role'          => $role,
    ':status'         => 'ACTIVE'
]);

echo "Admin created with id: " . $pdo->lastInsertId();