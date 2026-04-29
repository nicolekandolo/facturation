<?php
require_once __DIR__ . '/../includes/functions.php';

$user   = auth_api_check();
$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;

// ─── GET  →  liste ou produit unique ─────────────────────────────────────────
if ($method === 'GET') {
    $products = data_read('products');
    if ($id) {
        foreach ($products as $p) {
            if ($p['id'] === $id) json_out($p);
        }
        json_error('Produit introuvable', 404);
    }
    json_out($products);
}

// ─── POST  →  créer ───────────────────────────────────────────────────────────
if ($method === 'POST') {
    require_role($user, 'manager', 'super_admin');
    $body = get_json_body();

    $name   = trim($body['name'] ?? '');
    $price  = (float)($body['price'] ?? 0);
    $stock  = (int)($body['stock'] ?? 0);
    $barcode = trim($body['barcode'] ?? '');
    $category = trim($body['category'] ?? 'Général');
    $min_stock_alert = (int)($body['min_stock_alert'] ?? 5);

    if (!$name || $price < 0) json_error('Nom et prix requis');

    $products = data_read('products');

    // Vérifier unicité du code-barres
    if ($barcode) {
        foreach ($products as $p) {
            if ($p['barcode'] === $barcode) json_error('Code-barres déjà utilisé');
        }
    }

    $new = compact('name', 'price', 'stock', 'barcode', 'category', 'min_stock_alert');
    $new['id'] = gen_id('p');

    $products[] = $new;
    data_write('products', $products);
    json_out($new, 201);
}

// ─── PUT  →  modifier ────────────────────────────────────────────────────────
if ($method === 'PUT') {
    require_role($user, 'manager', 'super_admin');
    if (!$id) json_error('ID requis');

    $body = get_json_body();
    $products = data_read('products');
    $updated  = null;

    foreach ($products as &$p) {
        if ($p['id'] === $id) {
            if (isset($body['name']))            $p['name']            = trim($body['name']);
            if (isset($body['price']))           $p['price']           = (float)$body['price'];
            if (isset($body['stock']))           $p['stock']           = (int)$body['stock'];
            if (isset($body['barcode']))         $p['barcode']         = trim($body['barcode']);
            if (isset($body['category']))        $p['category']        = trim($body['category']);
            if (isset($body['min_stock_alert'])) $p['min_stock_alert'] = (int)$body['min_stock_alert'];
            $updated = $p;
            break;
        }
    }
    unset($p);

    if (!$updated) json_error('Produit introuvable', 404);

    data_write('products', $products);
    json_out($updated);
}

// ─── DELETE  →  supprimer ────────────────────────────────────────────────────
if ($method === 'DELETE') {
    require_role($user, 'manager', 'super_admin');
    if (!$id) json_error('ID requis');

    $products = data_read('products');
    $filtered = array_filter($products, fn($p) => $p['id'] !== $id);

    if (count($filtered) === count($products)) json_error('Produit introuvable', 404);

    data_write('products', $filtered);
    json_out(['success' => true]);
}

json_error('Méthode non autorisée', 405);
