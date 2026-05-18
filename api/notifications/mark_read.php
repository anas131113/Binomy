<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session = requireAuth();
$userId  = (int) $session['user_id'];

getDB()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :uid')
       ->execute([':uid' => $userId]);

jsonResponse(true, null, 'Notifications marquées comme lues.');
