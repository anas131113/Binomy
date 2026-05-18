<?php
require_once __DIR__ . '/../../api/helpers.php';

requireRole('admin');

$role   = $_GET['role']   ?? '';
$search = trim($_GET['q'] ?? '');
$page   = max(1, (int) ($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$pdo    = getDB();
$where  = ['1=1'];
$params = [];

if ($role && in_array($role, ['student', 'locataire', 'admin'], true)) {
    $where[]         = 'role = :role';
    $params[':role'] = $role;
}
if ($search) {
    $where[]           = '(name LIKE :q OR email LIKE :q)';
    $params[':q']      = "%{$search}%";
}

$whereClause = implode(' AND ', $where);

$stmt = $pdo->prepare(
    "SELECT id, name, email, role, city, is_active, created_at
     FROM users WHERE {$whereClause}
     ORDER BY created_at DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE {$whereClause}");
$countStmt->execute($params);

jsonResponse(true, [
    'users'      => $stmt->fetchAll(),
    'total'      => (int) $countStmt->fetchColumn(),
    'page'       => $page,
    'totalPages' => ceil($countStmt->fetchColumn() / $limit),
]);
