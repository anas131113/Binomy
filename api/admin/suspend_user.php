<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

requireRole('admin');
$body   = getJsonBody();
$userId = (int) ($body['user_id'] ?? 0);
$active = isset($body['is_active']) ? (int) $body['is_active'] : null;

if (!$userId || $active === null) {
    jsonResponse(false, null, 'Paramètres manquants.', 422);
}

getDB()->prepare('UPDATE users SET is_active = :active WHERE id = :id')
       ->execute([':active' => $active, ':id' => $userId]);

$msg = $active ? 'Compte réactivé.' : 'Compte suspendu.';
jsonResponse(true, null, $msg);
