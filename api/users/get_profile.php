<?php
require_once __DIR__ . '/../../api/helpers.php';

$session = requireAuth();
$userId  = (int) ($session['user_id']);

$pdo  = getDB();
$stmt = $pdo->prepare(
    'SELECT u.id, u.name, u.email, u.role, u.avatar, u.phone, u.city, u.bio, u.created_at,
            sp.budget_min, sp.budget_max, sp.cleanliness, sp.smoking, sp.pets_ok,
            sp.schedule, sp.personal_rules, sp.looking_for, sp.available_from
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     WHERE u.id = :uid'
);
$stmt->execute([':uid' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    jsonResponse(false, null, 'Utilisateur introuvable.', 404);
}

unset($user['password']);
jsonResponse(true, $user);
