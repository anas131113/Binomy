<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session = requireAuth();
$userId  = (int) $session['user_id'];
$body    = getJsonBody();

$pdo = getDB();

// Mise à jour des champs utilisateur de base
$pdo->prepare(
    'UPDATE users SET name=:name, phone=:phone, city=:city, bio=:bio WHERE id=:uid'
)->execute([
    ':name'  => trim($body['name']  ?? ''),
    ':phone' => trim($body['phone'] ?? ''),
    ':city'  => trim($body['city']  ?? ''),
    ':bio'   => trim($body['bio']   ?? ''),
    ':uid'   => $userId,
]);

// Mise à jour du profil étudiant si applicable
if ($session['role'] === 'student') {
    $pdo->prepare(
        'UPDATE student_profiles SET
            budget_min=:bmin, budget_max=:bmax,
            cleanliness=:clean, smoking=:smoking,
            pets_ok=:pets, schedule=:schedule,
            personal_rules=:rules, looking_for=:looking,
            available_from=:avail
         WHERE user_id=:uid'
    )->execute([
        ':bmin'     => $body['budget_min']     ?? null,
        ':bmax'     => $body['budget_max']     ?? null,
        ':clean'    => $body['cleanliness']    ?? null,
        ':smoking'  => (int) ($body['smoking'] ?? 0),
        ':pets'     => (int) ($body['pets_ok'] ?? 0),
        ':schedule' => $body['schedule']       ?? null,
        ':rules'    => $body['personal_rules'] ?? null,
        ':looking'  => $body['looking_for']    ?? 'both',
        ':avail'    => $body['available_from'] ?? null,
        ':uid'      => $userId,
    ]);
}

jsonResponse(true, null, 'Profil mis à jour avec succès.');
