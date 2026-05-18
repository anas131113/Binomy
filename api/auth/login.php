<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$body  = getJsonBody();
$email = trim($body['email']    ?? '');
$pass  = $body['password'] ?? '';

if (!$email || !$pass) {
    jsonResponse(false, null, 'Email et mot de passe requis.', 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare('SELECT id, name, email, password, role, is_active FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($pass, $user['password'])) {
    jsonResponse(false, null, 'Email ou mot de passe incorrect.', 401);
}
if (!$user['is_active']) {
    jsonResponse(false, null, 'Compte suspendu. Contactez l\'administrateur.', 403);
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

jsonResponse(true, ['role' => $user['role'], 'name' => $user['name']], 'Connexion réussie.');
