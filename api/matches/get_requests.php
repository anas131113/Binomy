<?php
require_once __DIR__ . '/../../api/helpers.php';

$session = requireRole('student');
$userId  = (int) $session['user_id'];

$pdo  = getDB();
$stmt = $pdo->prepare(
    "SELECT rr.id, rr.message, rr.status, rr.created_at,
            u.id AS sender_id, u.name AS sender_name, u.avatar AS sender_avatar, u.city AS sender_city
     FROM roommate_requests rr
     JOIN users u ON u.id = rr.sender_id
     WHERE rr.receiver_id = :uid AND rr.status = 'pending'
     ORDER BY rr.created_at DESC"
);
$stmt->execute([':uid' => $userId]);

jsonResponse(true, $stmt->fetchAll());
