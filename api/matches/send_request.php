<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session    = requireRole('student');
$senderId   = (int) $session['user_id'];
$body       = getJsonBody();
$receiverId = (int) ($body['receiver_id'] ?? 0);
$message    = trim($body['message'] ?? '');

if (!$receiverId || $receiverId === $senderId) {
    jsonResponse(false, null, 'Destinataire invalide.', 422);
}

$pdo = getDB();

// Vérifier que le destinataire est bien un étudiant
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'student' AND is_active = 1");
$stmt->execute([':id' => $receiverId]);
if (!$stmt->fetch()) {
    jsonResponse(false, null, 'Étudiant introuvable.', 404);
}

// Vérifier qu'il n'y a pas déjà une demande
$stmt = $pdo->prepare(
    "SELECT id, status FROM roommate_requests
     WHERE (sender_id = :sid AND receiver_id = :rid)
        OR (sender_id = :rid AND receiver_id = :sid)"
);
$stmt->execute([':sid' => $senderId, ':rid' => $receiverId]);
$existing = $stmt->fetch();

if ($existing) {
    if ($existing['status'] === 'pending') {
        jsonResponse(false, null, 'Une demande est déjà en attente.', 409);
    }
    if ($existing['status'] === 'accepted') {
        jsonResponse(false, null, 'Vous êtes déjà en match avec cet étudiant.', 409);
    }
}

// Insérer la demande
$stmt = $pdo->prepare(
    'INSERT INTO roommate_requests (sender_id, receiver_id, message) VALUES (:sid, :rid, :msg)
     ON DUPLICATE KEY UPDATE status="pending", message=:msg, responded_at=NULL, created_at=NOW()'
);
$stmt->execute([':sid' => $senderId, ':rid' => $receiverId, ':msg' => $message ?: null]);

$requestId = (int) $pdo->lastInsertId();

createNotification(
    $receiverId,
    'new_request',
    $requestId,
    $session['name'] . ' vous a envoyé une demande de colocation.'
);

jsonResponse(true, null, 'Demande envoyée avec succès.', 201);
