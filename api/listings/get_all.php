<?php
require_once __DIR__ . '/../../api/helpers.php';

$city     = trim($_GET['city']      ?? '');
$type     = trim($_GET['type']      ?? '');
$maxPrice = (float) ($_GET['max_price'] ?? 0);
$page     = max(1, (int) ($_GET['page'] ?? 1));
$limit    = 10;
$offset   = ($page - 1) * $limit;

$pdo    = getDB();
$where  = ["l.status = 'available'"];
$params = [];

if ($city) {
    $where[]         = 'l.city = :city';
    $params[':city'] = $city;
}
if ($type) {
    $where[]         = 'l.type = :type';
    $params[':type'] = $type;
}
if ($maxPrice > 0) {
    $where[]             = 'l.price <= :max_price';
    $params[':max_price'] = $maxPrice;
}

$whereClause = implode(' AND ', $where);

$stmt = $pdo->prepare(
    "SELECT l.id, l.title, l.type, l.price, l.city, l.rooms, l.surface, l.created_at,
            u.name AS owner_name,
            (SELECT image_path FROM listing_images WHERE listing_id = l.id AND is_main = 1 LIMIT 1) AS main_image
     FROM listings l
     JOIN users u ON u.id = l.owner_id
     WHERE {$whereClause}
     ORDER BY l.created_at DESC
     LIMIT :limit OFFSET :offset"
);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$listings = $stmt->fetchAll();

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM listings l WHERE {$whereClause}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

jsonResponse(true, [
    'listings'   => $listings,
    'total'      => $total,
    'page'       => $page,
    'totalPages' => ceil($total / $limit),
]);
