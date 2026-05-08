<?php
require_once __DIR__ . '/../../api/helpers.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    jsonResponse(false, null, 'ID manquant.', 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare(
    'SELECT l.*, u.name AS owner_name, u.phone AS owner_phone, u.email AS owner_email
     FROM listings l
     JOIN users u ON u.id = l.owner_id
     WHERE l.id = :id'
);
$stmt->execute([':id' => $id]);
$listing = $stmt->fetch();

if (!$listing) {
    jsonResponse(false, null, 'Annonce introuvable.', 404);
}

$imgStmt = $pdo->prepare('SELECT image_path, is_main FROM listing_images WHERE listing_id = :id ORDER BY is_main DESC');
$imgStmt->execute([':id' => $id]);
$listing['images'] = $imgStmt->fetchAll();

jsonResponse(true, $listing);
