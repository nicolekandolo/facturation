<?php
require_once __DIR__ . '/../../auth/session.php';
exiger_role(['caissier', 'manager', 'super_admin']);

$facture = $_SESSION['derniere_facture'] ?? null;
if ($facture === null) {
    header('Location: /facturation/modules/facturation/nouvelle-facture.php');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Facture enregistree</h1>
<p><strong>ID :</strong> <?= htmlspecialchars($facture['id_facture']) ?></p>
<p><strong>Date :</strong> <?= htmlspecialchars($facture['date']) ?> <?= htmlspecialchars($facture['heure']) ?></p>
<p><strong>Caissier :</strong> <?= htmlspecialchars($facture['caissier']) ?></p>

<table>
    <tr><th>Designation</th><th>Prix unit. HT</th><th>Qte</th><th>Sous-total HT</th></tr>
    <?php foreach ($facture['articles'] as $ligne): ?>
        <tr>
            <td><?= htmlspecialchars($ligne['nom']) ?></td>
            <td><?= number_format($ligne['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</td>
            <td><?= (int)$ligne['quantite'] ?></td>
            <td><?= number_format($ligne['sous_total_ht'], 2, ',', ' ') ?> CDF</td>
        </tr>
    <?php endforeach; ?>
</table>

<p><strong>Total HT :</strong> <?= number_format($facture['total_ht'], 2, ',', ' ') ?> CDF</p>
<p><strong>TVA (18%) :</strong> <?= number_format($facture['tva'], 2, ',', ' ') ?> CDF</p>
<p><strong>Net a payer :</strong> <?= number_format($facture['total_ttc'], 2, ',', ' ') ?> CDF</p>

<p><a href="/facturation/modules/facturation/nouvelle-facture.php">Faire une autre facture</a></p>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
