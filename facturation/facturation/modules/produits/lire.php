<?php

/**
 * API - Lire un produit par code-barres
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$barcode = $data['barcode'] ?? '';

if (!$barcode) {
    echo json_encode(['success' => false, 'error' => 'Code-barres manquant']);
    exit;
}

$product = product_get_by_barcode($barcode);

if ($product) {
    echo json_encode(['success' => true, 'product' => $product]);
} else {
    echo json_encode(['success' => false, 'error' => 'Produit non trouvé']);
}
