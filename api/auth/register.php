<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$body = getJsonBody();

$name  = trim($body['name']  ?? '');
$email = trim($body['email'] ?? '');
$pass  = $body['password']   ?? '';
$role  = $body['role']       ?? '';

// --- Validation ---
if (!$name || !$email || !$pass || !$role) {
    jsonResponse(false, null, 'Tous les champs sont obligatoires.', 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, null, 'Email invalide.', 422);
}
if (strlen($pass) < 6) {
    jsonResponse(false, null, 'Le mot de passe doit contenir au moins 6 caractères.', 422);
}
if (!in_array($role, ['student', 'locataire'], true)) {
    jsonResponse(false, null, 'Rôle invalide. Choisissez student ou locataire.', 422);
}

$pdo = getDB();

// Vérifier si l'email est déjà utilisé
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    jsonResponse(false, null, 'Cet email est déjà utilisé.', 409);
}

// Insérer le nouvel utilisateur
$hash = password_hash($pass, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
);
$stmt->execute([
    ':name'     => $name,
    ':email'    => $email,
    ':password' => $hash,
    ':role'     => $role,
]);

$userId = (int) $pdo->lastInsertId();

// Créer automatiquement le profil étudiant si rôle student
if ($role === 'student') {
    $pdo->prepare('INSERT INTO student_profiles (user_id) VALUES (:uid)')
        ->execute([':uid' => $userId]);
}

// Ouvrir la session directement après l'inscription
session_regenerate_id(true);
$_SESSION['user_id'] = $userId;
$_SESSION['name']    = $name;
$_SESSION['email']   = $email;
$_SESSION['role']    = $role;

jsonResponse(true, ['role' => $role], 'Inscription réussie. Bienvenue sur Binomy !', 201);
