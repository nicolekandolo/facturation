<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_role(['manager', 'super_admin']);

$produits = lire_json($CONFIG['fichier_produits']);
require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Liste des produits</h1>
<table>
    <tr>
        <th>Code-barres</th><th>Nom</th><th>Prix HT</th><th>Expiration</th><th>Stock</th>
    </tr>
    <?php foreach ($produits as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['code_barre']) ?></td>
            <td><?= htmlspecialchars($p['nom']) ?></td>
            <td><?= number_format($p['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</td>
            <td><?= htmlspecialchars($p['date_expiration']) ?></td>
            <td><?= (int)$p['quantite_stock'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
