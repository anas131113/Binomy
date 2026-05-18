<?php
require_once __DIR__ . '/../../api/helpers.php';

$session  = requireAuth();
$userId   = (int) $session['user_id'];
$matchId  = isset($_GET['match_id'])   ? (int) $_GET['match_id']   : null;
$listingId = isset($_GET['listing_id']) ? (int) $_GET['listing_id'] : null;

if (!$matchId && !$listingId) {
    jsonResponse(false, null, 'match_id ou listing_id requis.', 422);
}

$pdo = getDB();

if ($matchId) {
    // Vérifier l'accès
    $stmt = $pdo->prepare(
        'SELECT id FROM matches WHERE id = :mid AND (user1_id = :uid OR user2_id = :uid)'
    );
    $stmt->execute([':mid' => $matchId, ':uid' => $userId]);
    if (!$stmt->fetch()) {
        jsonResponse(false, null, 'Accès refusé.', 403);
    }

    $stmt = $pdo->prepare(
        'SELECT m.id, m.sender_id, m.content, m.sent_at, m.is_read,
                u.name AS sender_name, u.avatar AS sender_avatar
         FROM messages m
         JOIN users u ON u.id = m.sender_id
         WHERE m.match_id = :mid
         ORDER BY m.sent_at ASC'
    );
    $stmt->execute([':mid' => $matchId]);

    // Marquer comme lus
    $pdo->prepare(
        'UPDATE messages SET is_read = 1 WHERE match_id = :mid AND receiver_id = :uid'
    )->execute([':mid' => $matchId, ':uid' => $userId]);

} else {
    $stmt = $pdo->prepare(
        'SELECT m.id, m.sender_id, m.content, m.sent_at, m.is_read,
                u.name AS sender_name, u.avatar AS sender_avatar
         FROM messages m
         JOIN users u ON u.id = m.sender_id
         WHERE m.listing_id = :lid
           AND (m.sender_id = :uid OR m.receiver_id = :uid)
         ORDER BY m.sent_at ASC'
    );
    $stmt->execute([':lid' => $listingId, ':uid' => $userId]);

    $pdo->prepare(
        'UPDATE messages SET is_read = 1 WHERE listing_id = :lid AND receiver_id = :uid'
    )->execute([':lid' => $listingId, ':uid' => $userId]);
}

jsonResponse(true, $stmt->fetchAll());
