<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session    = requireRole('student');
$receiverId = (int) $session['user_id'];
$body       = getJsonBody();
$requestId  = (int) ($body['request_id'] ?? 0);
$action     = $body['action'] ?? ''; // 'accepted' ou 'refused'

if (!$requestId || !in_array($action, ['accepted', 'refused'], true)) {
    jsonResponse(false, null, 'Paramètres invalides.', 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare(
    "SELECT id, sender_id, status FROM roommate_requests
     WHERE id = :id AND receiver_id = :rid AND status = 'pending'"
);
$stmt->execute([':id' => $requestId, ':rid' => $receiverId]);
$request = $stmt->fetch();

if (!$request) {
    jsonResponse(false, null, 'Demande introuvable ou déjà traitée.', 404);
}

$pdo->prepare(
    'UPDATE roommate_requests SET status = :status, responded_at = NOW() WHERE id = :id'
)->execute([':status' => $action, ':id' => $requestId]);

$notifType = ($action === 'accepted') ? 'request_accepted' : 'request_refused';
$notifMsg  = ($action === 'accepted')
    ? $session['name'] . ' a accepté votre demande de colocation ! Vous pouvez maintenant chatter.'
    : $session['name'] . ' a refusé votre demande de colocation.';

createNotification($request['sender_id'], $notifType, $requestId, $notifMsg);

// Créer le match si accepté
if ($action === 'accepted') {
    $pdo->prepare(
        'INSERT INTO matches (request_id, user1_id, user2_id)
         VALUES (:req, :u1, :u2)'
    )->execute([
        ':req' => $requestId,
        ':u1'  => $request['sender_id'],
        ':u2'  => $receiverId,
    ]);
}

jsonResponse(true, null, $action === 'accepted' ? 'Demande acceptée ! Match créé.' : 'Demande refusée.');
