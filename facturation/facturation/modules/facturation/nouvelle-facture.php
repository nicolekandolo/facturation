<?php

/**
 * API - Créer une nouvelle facture
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $user = auth_api_check();

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || empty($data['items']) || empty($data['caissier'])) {
        echo json_encode(['success' => false, 'error' => 'Données manquantes']);
        exit;
    }

    // Create invoice
    $invoice = invoice_create(
        $data['items'],
        $user['username'],
        $data['customer_name'] ?? null
    );

    // Save invoice
    invoice_save($invoice);

    // Update stock
    foreach ($data['items'] as $item) {
        $product = product_get_by_barcode($item['code_barre']);
        if ($product) {
            product_update_stock($product['id'], $item['quantite']);
        }
    }

    echo json_encode(['success' => true, 'invoice' => $invoice]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
