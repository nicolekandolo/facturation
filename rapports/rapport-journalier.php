<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-produits.php';
exiger_role(['manager', 'super_admin']);

$factures = lire_json($CONFIG['fichier_factures']);
$aujourdhui = date('Y-m-d');
$total = 0;
$nombre = 0;

foreach ($factures as $f) {
    if ($f['date'] === $aujourdhui) {
        $nombre++;
        $total = $total + $f['total_ttc'];
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<h1>Rapport journalier</h1>
<p>Date : <?= htmlspecialchars($aujourdhui) ?></p>
<p>Nombre de factures : <?= $nombre ?></p>
<p>Chiffre d affaires TTC : <?= number_format($total, 2, ',', ' ') ?> CDF</p>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
