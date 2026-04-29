<?php

/**
 * Fonctions pour la gestion des produits
 */

function data_read($name)
{
    $file = DATA_DIR . $name . '.json';
    if (!file_exists($file)) return [];
    $content = file_get_contents($file);
    return json_decode($content, true) ?? [];
}

function data_write($name, $data)
{
    $file = DATA_DIR . $name . '.json';
    file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function product_get_all()
{
    return data_read('produits');
}

function product_get_by_id($id)
{
    $products = product_get_all();
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

function product_get_by_barcode($barcode)
{
    $products = product_get_all();
    foreach ($products as $product) {
        if (isset($product['barcode']) && $product['barcode'] === $barcode) {
            return $product;
        }
    }
    return null;
}

function product_create($data)
{
    $data['id'] = 'p' . uniqid('', true);
    $data['created_at'] = date('Y-m-d H:i:s');
    $products = product_get_all();
    $products[] = $data;
    data_write('produits', $products);
    return $data;
}

function product_update($id, $data)
{
    $products = product_get_all();
    foreach ($products as &$product) {
        if ($product['id'] === $id) {
            $product = array_merge($product, $data);
            $product['updated_at'] = date('Y-m-d H:i:s');
            data_write('produits', $products);
            return $product;
        }
    }
    return null;
}

function product_delete($id)
{
    $products = product_get_all();
    $products = array_filter($products, fn($p) => $p['id'] !== $id);
    data_write('produits', $products);
    return true;
}

function product_update_stock($id, $quantity)
{
    $product = product_get_by_id($id);
    if (!$product) return false;

    $product['stock'] -= $quantity;
    if ($product['stock'] < 0) {
        return false; // Stock insuffisant
    }

    return product_update($id, ['stock' => $product['stock']]);
}

function format_price($price)
{
    return number_format($price, 2, DECIMAL_SEPARATOR, THOUSANDS_SEPARATOR) . ' ' . CURRENCY_SYMBOL;
}

function calculate_tva($amount_ht)
{
    return round($amount_ht * VAT_RATE, 2);
}

function calculate_ttc($amount_ht)
{
    return round($amount_ht + calculate_tva($amount_ht), 2);
}
