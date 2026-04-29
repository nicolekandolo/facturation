<?php

/**
 * Fonctions utilitaires communes
 */

// Réponse JSON
function json_out($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error($message, $code = 400)
{
    json_out(['success' => false, 'error' => $message], $code);
}

// Redirection sécurisée
function redirect($url, $permanent = false)
{
    http_response_code($permanent ? 301 : 302);
    header('Location: ' . $url);
    exit;
}

// Formatage de prix (utilitaire supplémentaire)
if (!function_exists('format_price')) {
    function format_price($price)
    {
        return number_format($price, 2, DECIMAL_SEPARATOR, THOUSANDS_SEPARATOR) . ' ' . CURRENCY_SYMBOL;
    }
}

// Calcul TVA
if (!function_exists('calculate_tva')) {
    function calculate_tva($amount_ht)
    {
        return round($amount_ht * VAT_RATE, 2);
    }
}

// Calcul TTC
if (!function_exists('calculate_ttc')) {
    function calculate_ttc($amount_ht)
    {
        return round($amount_ht + calculate_tva($amount_ht), 2);
    }
}
