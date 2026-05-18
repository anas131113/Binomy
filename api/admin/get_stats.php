<?php
require_once __DIR__ . '/../../api/helpers.php';

requireRole('admin');

$pdo = getDB();

$stats = [
    'total_users'     => (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_students'  => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn(),
    'total_locataires'=> (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role='locataire'")->fetchColumn(),
    'total_listings'  => (int) $pdo->query("SELECT COUNT(*) FROM listings")->fetchColumn(),
    'available_listings' => (int) $pdo->query("SELECT COUNT(*) FROM listings WHERE status='available'")->fetchColumn(),
    'total_matches'   => (int) $pdo->query("SELECT COUNT(*) FROM matches")->fetchColumn(),
    'total_messages'  => (int) $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn(),
    'pending_requests'=> (int) $pdo->query("SELECT COUNT(*) FROM roommate_requests WHERE status='pending'")->fetchColumn(),
];

// Inscriptions des 7 derniers jours
$stmt = $pdo->query(
    "SELECT DATE(created_at) AS day, COUNT(*) AS count
     FROM users
     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     GROUP BY DATE(created_at)
     ORDER BY day ASC"
);
$stats['registrations_week'] = $stmt->fetchAll();

jsonResponse(true, $stats);
