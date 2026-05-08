<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session = requireRole('locataire', 'admin');
$userId  = (int) $session['user_id'];
$body    = getJsonBody();
$id      = (int) ($body['id'] ?? 0);

if (!$id) {
    jsonResponse(false, null, 'ID manquant.', 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare('SELECT owner_id FROM listings WHERE id = :id');
$stmt->execute([':id' => $id]);
$listing = $stmt->fetch();

if (!$listing) {
    jsonResponse(false, null, 'Annonce introuvable.', 404);
}
if ($session['role'] !== 'admin' && $listing['owner_id'] !== $userId) {
    jsonResponse(false, null, 'Accès refusé.', 403);
}

$pdo->prepare('DELETE FROM listings WHERE id = :id')->execute([':id' => $id]);

jsonResponse(true, null, 'Annonce supprimée.');
