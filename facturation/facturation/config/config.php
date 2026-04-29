<?php

/**
 * Configuration du système de facturation
 * Devise: CDF (Franc Congolais)
 * TVA: 16%
 */

// ─── Chemins des données ──────────────────────────────────────────────────────
define('DATA_DIR', __DIR__ . '/../data/');
define('ROOT_DIR', __DIR__ . '/../');

// ─── Configuration Devise ─────────────────────────────────────────────────────
define('CURRENCY', 'CDF');
define('CURRENCY_SYMBOL', 'CDF');
define('DECIMAL_SEPARATOR', ',');
define('THOUSANDS_SEPARATOR', ' ');

// ─── Configuration TVA ────────────────────────────────────────────────────────
define('VAT_RATE', 0.16);  // 16%
define('VAT_PERCENTAGE', 16);

// ─── Formats de date/heure ───────────────────────────────────────────────────
define('DATE_FORMAT', 'Y-m-d');      // 2026-04-17
define('TIME_FORMAT', 'H:i:s');      // 10:35:22
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// ─── Rôles et permissions ────────────────────────────────────────────────────
define('ROLES', [
    'super_admin' => 'Super Administrateur',
    'manager'     => 'Manager',
    'caissier'    => 'Caissier',
]);

// ─── Paramètres session ────────────────────────────────────────────────────
define('SESSION_TIMEOUT', 3600);  // 1 heure
