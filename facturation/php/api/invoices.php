<?php
require_once __DIR__ . '/../includes/functions.php';

$user   = auth_api_check();
$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;

// ─── GET  →  liste ou détail ─────────────────────────────────────────────────
if ($method === 'GET') {
    require_role($user, 'manager', 'super_admin');
    $invoices = data_read('invoices');
    if ($id) {
        foreach ($invoices as $inv) {
            if ($inv['id'] === $id) json_out($inv);
        }
        json_error('Facture introuvable', 404);
    }
    // Plus récente en premier
    usort($invoices, fn($a, $b) => strcmp($b['date'], $a['date']));
    json_out($invoices);
}

// ─── POST  →  créer une facture ───────────────────────────────────────────────
if ($method === 'POST') {
    $body  = get_json_body();
    $items = $body['items'] ?? [];

    if (empty($items)) json_error('Panier vide');

    $payment_method = $body['payment_method'] ?? 'cash';
    $customer_name  = trim($body['customer_name'] ?? '');

    $valid_methods = ['cash', 'card', 'mobile'];
    if (!in_array($payment_method, $valid_methods)) json_error('Moyen de paiement invalide');

    // Charger produits pour vérifier stock
    $products = data_read('products');
    $prod_map = [];
    foreach ($products as $p) {
        $prod_map[$p['id']] = $p;
    }

    $invoice_items = [];
    $subtotal = 0;

    foreach ($items as $item) {
        $pid = $item['product_id'] ?? '';
        $qty = (int)($item['quantity'] ?? 1);

        if (!isset($prod_map[$pid])) json_error("Produit $pid introuvable");
        $prod = $prod_map[$pid];

        if ($prod['stock'] < $qty) {
            json_error("Stock insuffisant pour : {$prod['name']} (stock: {$prod['stock']})");
        }

        $line_total = round($prod['price'] * $qty, 2);
        $subtotal  += $line_total;

        $invoice_items[] = [
            'product_id' => $pid,
            'name'       => $prod['name'],
            'price'      => $prod['price'],
            'quantity'   => $qty,
            'total'      => $line_total,
        ];
    }

    $tax   = round($subtotal * 0.20, 2);
    $total = round($subtotal + $tax, 2);

    $invoice = [
        'id'             => gen_id('inv'),
        'invoice_number' => next_invoice_number(),
        'date'           => date('c'),
        'cashier_id'     => $user['id'],
        'cashier_name'   => $user['name'],
        'items'          => $invoice_items,
        'subtotal'       => $subtotal,
        'tax'            => $tax,
        'total'          => $total,
        'payment_method' => $payment_method,
        'customer_name'  => $customer_name ?: null,
    ];

    // Décrémenter le stock
    foreach ($invoice_items as $item) {
        foreach ($products as &$p) {
            if ($p['id'] === $item['product_id']) {
                $p['stock'] -= $item['quantity'];
                break;
            }
        }
        unset($p);
    }

    data_write('products', $products);

    $invoices   = data_read('invoices');
    $invoices[] = $invoice;
    data_write('invoices', $invoices);

    json_out($invoice, 201);
}

json_error('Méthode non autorisée', 405);
