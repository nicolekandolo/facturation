<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';
exiger_role(['caissier', 'manager', 'super_admin']);

$articles = $_SESSION['facture_articles'] ?? [];
if (count($articles) === 0) {
    header('Location: /facturation/modules/facturation/nouvelle-facture.php');
    exit;
}

$totaux = calculer_totaux_facture($articles, $CONFIG['tva']);
$id_facture = generer_id_facture($CONFIG['fichier_factures']);
$user = utilisateur_actuel();

$facture = [
    'id_facture' => $id_facture,
    'date' => date('Y-m-d'),
    'heure' => date('H:i:s'),
    'caissier' => $user['identifiant'],
    'articles' => $articles,
    'total_ht' => $totaux['total_ht'],
    'tva' => $totaux['tva'],
    'total_ttc' => $totaux['total_ttc']
];

ajouter_facture($facture, $CONFIG['fichier_factures']);

foreach ($articles as $article) {
    mettre_a_jour_stock($article['code_barre'], $article['quantite'], $CONFIG['fichier_produits']);
}

$_SESSION['derniere_facture'] = $facture;
$_SESSION['facture_articles'] = [];

header('Location: /facturation/modules/facturation/afficher-facture.php');
exit;
