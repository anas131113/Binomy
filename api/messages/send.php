<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session    = requireAuth();
$senderId   = (int) $session['user_id'];
$body       = getJsonBody();
$receiverId = (int) ($body['receiver_id'] ?? 0);
$content    = trim($body['content'] ?? '');
$matchId    = isset($body['match_id'])   ? (int) $body['match_id']   : null;
$listingId  = isset($body['listing_id']) ? (int) $body['listing_id'] : null;

if (!$receiverId || !$content) {
    jsonResponse(false, null, 'Destinataire et contenu obligatoires.', 422);
}
if (!$matchId && !$listingId) {
    jsonResponse(false, null, 'match_id ou listing_id requis.', 422);
}

$pdo = getDB();

// Si chat étudiant/étudiant : vérifier que le match existe entre les deux
if ($matchId) {
    $stmt = $pdo->prepare(
        'SELECT id FROM matches WHERE id = :mid
         AND (user1_id = :uid OR user2_id = :uid)'
    );
    $stmt->execute([':mid' => $matchId, ':uid' => $senderId]);
    if (!$stmt->fetch()) {
        jsonResponse(false, null, 'Accès à cette conversation refusé.', 403);
    }
}

$pdo->prepare(
    'INSERT INTO messages (sender_id, receiver_id, match_id, listing_id, content)
     VALUES (:sid, :rid, :mid, :lid, :content)'
)->execute([
    ':sid'     => $senderId,
    ':rid'     => $receiverId,
    ':mid'     => $matchId,
    ':lid'     => $listingId,
    ':content' => $content,
]);

createNotification(
    $receiverId,
    'new_message',
    (int) $pdo->lastInsertId(),
    $session['name'] . ' vous a envoyé un message.'
);

jsonResponse(true, null, 'Message envoyé.', 201);
