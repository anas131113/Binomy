<?php
/**
 * BINOMY — Fonctions utilitaires partagées par tous les endpoints
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

// ----------------------------------------------------------------
// Réponse JSON standardisée
// ----------------------------------------------------------------
function jsonResponse(bool $success, $data = null, string $message = '', int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// ----------------------------------------------------------------
// Vérifier que l'utilisateur est connecté
// ----------------------------------------------------------------
function requireAuth(): array
{
    if (empty($_SESSION['user_id'])) {
        jsonResponse(false, null, 'Non authentifié. Veuillez vous connecter.', 401);
    }
    return $_SESSION;
}

// ----------------------------------------------------------------
// Vérifier le rôle de l'utilisateur connecté
// ----------------------------------------------------------------
function requireRole(string ...$roles): array
{
    $session = requireAuth();
    if (!in_array($session['role'], $roles, true)) {
        jsonResponse(false, null, 'Accès refusé : rôle insuffisant.', 403);
    }
    return $session;
}

// ----------------------------------------------------------------
// Lire le body JSON d'une requête POST
// ----------------------------------------------------------------
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// ----------------------------------------------------------------
// Créer une notification pour un utilisateur
// ----------------------------------------------------------------
function createNotification(int $userId, string $type, int $referenceId, string $content): void
{
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO notifications (user_id, type, reference_id, content)
         VALUES (:uid, :type, :ref, :content)'
    );
    $stmt->execute([
        ':uid'     => $userId,
        ':type'    => $type,
        ':ref'     => $referenceId,
        ':content' => $content,
    ]);
}

// ----------------------------------------------------------------
// Sécuriser l'affichage d'une chaîne (anti-XSS)
// ----------------------------------------------------------------
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
