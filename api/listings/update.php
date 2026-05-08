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
    jsonResponse(false, null, 'ID de l\'annonce manquant.', 422);
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

$pdo->prepare(
    'UPDATE listings SET title=:title, description=:desc, price=:price,
     city=:city, address=:addr, rooms=:rooms, surface=:surface,
     status=:status, updated_at=NOW()
     WHERE id=:id'
)->execute([
    ':title'   => $body['title']       ?? '',
    ':desc'    => $body['description'] ?? '',
    ':price'   => (float) ($body['price'] ?? 0),
    ':city'    => $body['city']        ?? '',
    ':addr'    => $body['address']     ?? null,
    ':rooms'   => $body['rooms']       ?? null,
    ':surface' => $body['surface']     ?? null,
    ':status'  => in_array($body['status'] ?? '', ['available', 'rented']) ? $body['status'] : 'available',
    ':id'      => $id,
]);

jsonResponse(true, null, 'Annonce mise à jour.');
