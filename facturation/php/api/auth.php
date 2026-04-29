<?php
require_once __DIR__ . '/../includes/functions.php';

session_start_safe();
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// ─── POST /api/auth.php  →  login ─────────────────────────────────────────────
if ($method === 'POST') {
    $body = get_json_body();
    $username = trim($body['username'] ?? '');
    $password  = $body['password'] ?? '';

    if (!$username || !$password) {
        json_error('Identifiant et mot de passe requis');
    }

    $users = data_read('users');
    $found = null;
    foreach ($users as $u) {
        if ($u['username'] === $username && !empty($u['is_active'])) {
            if (password_verify($password, $u['password'])) {
                $found = $u;
                break;
            }
        }
    }

    if (!$found) {
        json_error('Identifiant ou mot de passe incorrect', 401);
    }

    // Stocker en session sans le mot de passe
    unset($found['password']);
    $_SESSION['user'] = $found;

    json_out(['success' => true, 'user' => $found]);
}

// ─── DELETE /api/auth.php  →  logout ─────────────────────────────────────────
if ($method === 'DELETE') {
    session_destroy();
    json_out(['success' => true]);
}

json_error('Méthode non autorisée', 405);
