<?php
function generer_id_facture($fichier) {
    $factures = lire_json($fichier);
    $numero = count($factures) + 1;
    $date = date('Ymd');
    return 'FAC-' . $date . '-' . str_pad((string)$numero, 3, '0', STR_PAD_LEFT);
}

function calculer_totaux_facture($articles, $tva_taux) {
    $total_ht = 0;

    foreach ($articles as $article) {
        $total_ht = $total_ht + $article['sous_total_ht'];
    }

    $tva = $total_ht * $tva_taux;
    $total_ttc = $total_ht + $tva;

    return [
        'total_ht' => $total_ht,
        'tva' => $tva,
        'total_ttc' => $total_ttc
    ];
}

function ajouter_facture($facture, $fichier) {
    $factures = lire_json($fichier);
    $factures[] = $facture;
    ecrire_json($fichier, $factures);
}
