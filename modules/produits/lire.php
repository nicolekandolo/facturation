<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_role(['manager', 'super_admin']);

$code = trim($_GET['code_barre'] ?? '');
$produit = null;
if ($code !== '') {
    $produit = trouver_produit_par_code($code, $CONFIG['fichier_produits']);
}

require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Lire un produit</h1>
<form method="get" class="bloc">
    <label>Code-barres</label>
    <input type="text" name="code_barre" value="<?= htmlspecialchars($code) ?>" required>
    <button type="submit">Rechercher</button>
</form>

<?php if ($code !== '' && $produit === null): ?>
    <p class="erreur">Produit non trouve.</p>
<?php elseif ($produit !== null): ?>
    <p>Nom : <?= htmlspecialchars($produit['nom']) ?></p>
    <p>Prix HT : <?= number_format($produit['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</p>
    <p>Expiration : <?= htmlspecialchars($produit['date_expiration']) ?></p>
    <p>Stock : <?= (int)$produit['quantite_stock'] ?></p>
<?php endif; ?>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
