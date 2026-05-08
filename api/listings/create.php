<?php
require_once __DIR__ . '/../../api/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Méthode non autorisée.', 405);
}

$session = requireRole('locataire', 'admin');
$userId  = (int) $session['user_id'];

// Les données viennent en multipart/form-data (car upload d'images)
$title       = trim($_POST['title']       ?? '');
$description = trim($_POST['description'] ?? '');
$type        = $_POST['type']             ?? '';
$price       = (float) ($_POST['price']   ?? 0);
$city        = trim($_POST['city']        ?? '');
$address     = trim($_POST['address']     ?? '');
$rooms       = (int) ($_POST['rooms']     ?? 0) ?: null;
$surface     = (int) ($_POST['surface']   ?? 0) ?: null;

if (!$title || !$description || !$type || $price <= 0 || !$city) {
    jsonResponse(false, null, 'Champs obligatoires manquants.', 422);
}
if (!in_array($type, ['chambre', 'studio', 'appartement', 'maison'], true)) {
    jsonResponse(false, null, 'Type de logement invalide.', 422);
}

$pdo  = getDB();
$stmt = $pdo->prepare(
    'INSERT INTO listings (owner_id, title, description, type, price, city, address, rooms, surface)
     VALUES (:oid, :title, :desc, :type, :price, :city, :addr, :rooms, :surface)'
);
$stmt->execute([
    ':oid'     => $userId,
    ':title'   => $title,
    ':desc'    => $description,
    ':type'    => $type,
    ':price'   => $price,
    ':city'    => $city,
    ':addr'    => $address ?: null,
    ':rooms'   => $rooms,
    ':surface' => $surface,
]);

$listingId = (int) $pdo->lastInsertId();

// Gestion des images uploadées
$allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
$maxSize     = 5 * 1024 * 1024; // 5 MB
$uploadDir   = __DIR__ . '/../../uploads/listings/';
$imageStmt   = $pdo->prepare(
    'INSERT INTO listing_images (listing_id, image_path, is_main) VALUES (:lid, :path, :main)'
);

if (!empty($_FILES['images'])) {
    $files   = $_FILES['images'];
    $isFirst = true;

    foreach ($files['tmp_name'] as $i => $tmpName) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
        if ($files['size'][$i]  > $maxSize) continue;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMime, true)) continue;

        $ext      = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . strtolower($ext);

        if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
            $imageStmt->execute([
                ':lid'  => $listingId,
                ':path' => 'uploads/listings/' . $filename,
                ':main' => $isFirst ? 1 : 0,
            ]);
            $isFirst = false;
        }
    }
}

jsonResponse(true, ['id' => $listingId], 'Annonce publiée avec succès.', 201);
