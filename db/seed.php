<?php
/**
 * BINOMY — Seed : crée le compte admin initial
 * Exécuter UNE SEULE FOIS : php db/seed.php
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDB();

$adminEmail    = 'admin@binomy.tn';
$adminPassword = 'admin123';
$adminName     = 'Admin Binomy';

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute([':email' => $adminEmail]);

if ($stmt->fetch()) {
    echo "Admin existe déjà : {$adminEmail}\n";
    exit;
}

$hash = password_hash($adminPassword, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'admin')"
);
$stmt->execute([':name' => $adminName, ':email' => $adminEmail, ':password' => $hash]);

echo "Admin créé avec succès !\n";
echo "Email    : {$adminEmail}\n";
echo "Mot de passe : {$adminPassword}\n";
