<?php
require_once __DIR__ . '/../../api/helpers.php';

$session = requireAuth();
$userId  = (int) $session['user_id'];

$pdo  = getDB();
$stmt = $pdo->prepare(
    'SELECT id, type, reference_id, content, is_read, created_at
     FROM notifications
     WHERE user_id = :uid
     ORDER BY created_at DESC
     LIMIT 30'
);
$stmt->execute([':uid' => $userId]);
$notifications = $stmt->fetchAll();

$unreadStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0'
);
$unreadStmt->execute([':uid' => $userId]);
$unreadCount = (int) $unreadStmt->fetchColumn();

jsonResponse(true, ['notifications' => $notifications, 'unread_count' => $unreadCount]);
