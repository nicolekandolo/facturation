<?php
// ─── Chemins des données ──────────────────────────────────────────────────────
define('DATA_DIR', __DIR__ . '/../data/');

// ─── Lecture/Écriture JSON ────────────────────────────────────────────────────
function data_read($name) {
    $file = DATA_DIR . $name . '.json';
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    return json_decode($content, true) ?? [];
}

function data_write($name, $data) {
    $file = DATA_DIR . $name . '.json';
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// ─── Réponse JSON ─────────────────────────────────────────────────────────────
function json_out($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error($message, $code = 400) {
    json_out(['error' => $message], $code);
}

// ─── Authentification ─────────────────────────────────────────────────────────
function session_start_safe() {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function auth_check() {
    session_start_safe();
    if (empty($_SESSION['user'])) {
        header('Location: /php/index.php');
        exit;
    }
    return $_SESSION['user'];
}

function auth_api_check() {
    session_start_safe();
    if (empty($_SESSION['user'])) {
        json_error('Non authentifié', 401);
    }
    return $_SESSION['user'];
}

function require_role($user, ...$roles) {
    if (!in_array($user['role'], $roles)) {
        json_error('Accès refusé', 403);
    }
}

// ─── Génération ID ────────────────────────────────────────────────────────────
function gen_id($prefix = '') {
    return $prefix . uniqid('', true);
}

// ─── Numéro de facture ────────────────────────────────────────────────────────
function next_invoice_number() {
    $invoices = data_read('invoices');
    $year = date('Y');
    $count = count(array_filter($invoices, function($inv) use ($year) {
        return strpos($inv['invoice_number'], 'FAC-' . $year) === 0;
    }));
    return 'FAC-' . $year . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
}

// ─── Entrée utilisateur ───────────────────────────────────────────────────────
function get_json_body() {
    $body = file_get_contents('php://input');
    return json_decode($body, true) ?? [];
}
