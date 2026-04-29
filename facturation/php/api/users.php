<?php
require_once __DIR__ . '/../includes/functions.php';

$user   = auth_api_check();
$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;

// ─── GET  →  liste ────────────────────────────────────────────────────────────
if ($method === 'GET') {
    require_role($user, 'super_admin');
    $users = data_read('users');
    // Ne jamais retourner les mots de passe
    $safe = array_map(function($u) {
        unset($u['password']);
        return $u;
    }, $users);
    json_out($safe);
}

// ─── POST  →  créer ───────────────────────────────────────────────────────────
if ($method === 'POST') {
    require_role($user, 'super_admin');
    $body = get_json_body();

    $username = trim($body['username'] ?? '');
    $password = $body['password'] ?? '';
    $name     = trim($body['name'] ?? '');
    $role     = $body['role'] ?? 'caissier';

    if (!$username || !$password || !$name) json_error('Tous les champs sont requis');

    $valid_roles = ['caissier', 'manager', 'super_admin'];
    if (!in_array($role, $valid_roles)) json_error('Rôle invalide');

    $users = data_read('users');
    foreach ($users as $u) {
        if ($u['username'] === $username) json_error('Nom d\'utilisateur déjà pris');
    }

    $new = [
        'id'        => gen_id('u'),
        'username'  => $username,
        'password'  => password_hash($password, PASSWORD_DEFAULT),
        'role'      => $role,
        'name'      => $name,
        'is_active' => true,
    ];

    $users[] = $new;
    data_write('users', $users);

    unset($new['password']);
    json_out($new, 201);
}

// ─── PUT  →  modifier ────────────────────────────────────────────────────────
if ($method === 'PUT') {
    require_role($user, 'super_admin');
    if (!$id) json_error('ID requis');

    $body    = get_json_body();
    $users   = data_read('users');
    $updated = null;

    foreach ($users as &$u) {
        if ($u['id'] === $id) {
            if (isset($body['name']))      $u['name']      = trim($body['name']);
            if (isset($body['role']))      $u['role']      = $body['role'];
            if (isset($body['is_active'])) $u['is_active'] = (bool)$body['is_active'];
            if (!empty($body['password'])) $u['password']  = password_hash($body['password'], PASSWORD_DEFAULT);
            $updated = $u;
            break;
        }
    }
    unset($u);

    if (!$updated) json_error('Utilisateur introuvable', 404);

    data_write('users', $users);
    unset($updated['password']);
    json_out($updated);
}

// ─── DELETE  →  désactiver (soft delete) ────────────────────────────────────
if ($method === 'DELETE') {
    require_role($user, 'super_admin');
    if (!$id) json_error('ID requis');
    if ($id === $user['id']) json_error('Impossible de supprimer votre propre compte');

    $users = data_read('users');
    foreach ($users as &$u) {
        if ($u['id'] === $id) {
            $u['is_active'] = false;
            break;
        }
    }
    unset($u);
    data_write('users', $users);
    json_out(['success' => true]);
}

json_error('Méthode non autorisée', 405);
