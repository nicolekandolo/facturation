<?php

/**
 * Fonctions pour la gestion des factures
 */

function invoice_generate_id()
{
    $date = date('Ymd');
    $invoices = data_read('factures');

    // Compter les factures du jour
    $count = 0;
    foreach ($invoices as $inv) {
        if (strpos($inv['id_facture'], date('Ymd')) === 0) {
            $count++;
        }
    }

    return 'FAC-' . $date . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
}

function invoice_create($items, $caissier, $customer_name = null)
{
    $now = new DateTime();

    $invoice = [
        'id_facture' => invoice_generate_id(),
        'date' => $now->format(DATE_FORMAT),
        'heure' => $now->format(TIME_FORMAT),
        'caissier' => $caissier,
        'client' => $customer_name,
        'articles' => [],
        'total_ht' => 0,
        'tva' => 0,
        'total_ttc' => 0,
    ];

    foreach ($items as $item) {
        $sous_total_ht = round($item['prix_unitaire_ht'] * $item['quantite'], 2);

        $invoice['articles'][] = [
            'code_barre' => $item['code_barre'],
            'nom' => $item['nom'],
            'prix_unitaire_ht' => $item['prix_unitaire_ht'],
            'quantite' => $item['quantite'],
            'sous_total_ht' => $sous_total_ht,
        ];

        $invoice['total_ht'] += $sous_total_ht;
    }

    $invoice['total_ht'] = round($invoice['total_ht'], 2);
    $invoice['tva'] = round($invoice['total_ht'] * VAT_RATE, 2);
    $invoice['total_ttc'] = round($invoice['total_ht'] + $invoice['tva'], 2);

    return $invoice;
}

function invoice_save($invoice)
{
    $invoices = data_read('factures');
    $invoices[] = $invoice;
    data_write('factures', $invoices);
    return $invoice;
}

function invoice_get_all()
{
    return data_read('factures');
}

function invoice_get_by_id($id)
{
    $invoices = invoice_get_all();
    foreach ($invoices as $inv) {
        if ($inv['id_facture'] === $id) {
            return $inv;
        }
    }
    return null;
}

function invoice_format_text($invoice)
{
    $text = "═════════════════════════════════════════════════════════════\n";
    $text .= "FACTURE\n";
    $text .= "═════════════════════════════════════════════════════════════\n\n";

    $text .= "N° Facture: " . $invoice['id_facture'] . "\n";
    $text .= "Date: " . $invoice['date'] . "\n";
    $text .= "Heure: " . $invoice['heure'] . "\n";
    $text .= "Caissier: " . $invoice['caissier'] . "\n";
    if (!empty($invoice['client'])) {
        $text .= "Client: " . $invoice['client'] . "\n";
    }
    $text .= "\n";

    $text .= "───────────────────────────────────────────────────────────\n";
    $text .= sprintf("%-30s %12s %6s %15s\n", 'Désignation', 'Prix Unit.', 'Qté', 'Sous-total');
    $text .= "───────────────────────────────────────────────────────────\n";

    foreach ($invoice['articles'] as $item) {
        $text .= sprintf(
            "%-30s %12s %6d %15s\n",
            substr($item['nom'], 0, 28),
            format_price($item['prix_unitaire_ht']),
            $item['quantite'],
            format_price($item['sous_total_ht'])
        );
    }

    $text .= "───────────────────────────────────────────────────────────\n";
    $text .= sprintf(
        "%-30s %12s %6s %15s\n",
        'TOTAL HT',
        '',
        '',
        format_price($invoice['total_ht'])
    );
    $text .= sprintf(
        "%-30s %12s %6s %15s\n",
        'TVA (' . VAT_PERCENTAGE . '%)',
        '',
        '',
        format_price($invoice['tva'])
    );
    $text .= "═════════════════════════════════════════════════════════════\n";
    $text .= sprintf(
        "%-30s %12s %6s %15s\n",
        'NET À PAYER',
        '',
        '',
        format_price($invoice['total_ttc'])
    );
    $text .= "═════════════════════════════════════════════════════════════\n";

    return $text;
}

function invoice_get_daily_total($date = null)
{
    $date = $date ?? date(DATE_FORMAT);
    $invoices = invoice_get_all();
    $total = 0;

    foreach ($invoices as $inv) {
        if ($inv['date'] === $date) {
            $total += $inv['total_ttc'];
        }
    }

    return $total;
}

function invoice_get_by_cashier($caissier, $date = null)
{
    $date = $date ?? date(DATE_FORMAT);
    $invoices = invoice_get_all();
    $result = [];

    foreach ($invoices as $inv) {
        if ($inv['caissier'] === $caissier && $inv['date'] === $date) {
            $result[] = $inv;
        }
    }

    return $result;
}
