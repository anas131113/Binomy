<?php
require_once __DIR__ . '/../../api/helpers.php';

$session = requireRole('student');
$userId  = (int) $session['user_id'];

$pdo  = getDB();
$stmt = $pdo->prepare(
    "SELECT m.id AS match_id, m.created_at,
            IF(m.user1_id = :uid, u2.id, u1.id)     AS partner_id,
            IF(m.user1_id = :uid, u2.name, u1.name)   AS partner_name,
            IF(m.user1_id = :uid, u2.avatar, u1.avatar) AS partner_avatar,
            IF(m.user1_id = :uid, u2.city, u1.city)   AS partner_city
     FROM matches m
     JOIN users u1 ON u1.id = m.user1_id
     JOIN users u2 ON u2.id = m.user2_id
     WHERE m.user1_id = :uid OR m.user2_id = :uid
     ORDER BY m.created_at DESC"
);
$stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
$stmt->execute();

jsonResponse(true, $stmt->fetchAll());
