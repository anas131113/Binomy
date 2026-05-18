<?php
require_once __DIR__ . '/../../api/helpers.php';

$session = requireAuth();

$city   = trim($_GET['city']   ?? '');
$page   = max(1, (int) ($_GET['page'] ?? 1));
$limit  = 12;
$offset = ($page - 1) * $limit;

$pdo = getDB();

$where = "u.role = 'student' AND u.is_active = 1 AND u.id != :uid";
$params = [':uid' => $session['user_id']];

if ($city) {
    $where .= ' AND u.city = :city';
    $params[':city'] = $city;
}

$stmt = $pdo->prepare(
    "SELECT u.id, u.name, u.avatar, u.city, u.bio,
            sp.budget_min, sp.budget_max, sp.cleanliness,
            sp.smoking, sp.pets_ok, sp.schedule, sp.looking_for
     FROM users u
     LEFT JOIN student_profiles sp ON sp.user_id = u.id
     WHERE {$where}
     ORDER BY u.created_at DESC
     LIMIT :limit OFFSET :offset"
);

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$students = $stmt->fetchAll();

// Comptage total pour la pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

jsonResponse(true, [
    'students'   => $students,
    'total'      => $total,
    'page'       => $page,
    'totalPages' => ceil($total / $limit),
]);
