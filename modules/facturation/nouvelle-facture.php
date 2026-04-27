<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_role(['caissier', 'manager', 'super_admin']);

if (!isset($_SESSION['facture_articles'])) {
    $_SESSION['facture_articles'] = [];
}

$message = '';
$erreur = '';
$code_barre = trim($_POST['code_barre'] ?? '');
$quantite = trim($_POST['quantite'] ?? '1');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit = trouver_produit_par_code($code_barre, $CONFIG['fichier_produits']);

    if ($produit === null) {
        $erreur = 'Produit inconnu. Demandez au manager de l enregistrer.';
    } elseif ($quantite === '' || !ctype_digit($quantite) || (int)$quantite <= 0) {
        $erreur = 'La quantite doit etre un entier positif.';
    } elseif ((int)$quantite > (int)$produit['quantite_stock']) {
        $erreur = 'Quantite demandee superieure au stock disponible.';
    } else {
        $ligne = [
            'code_barre' => $produit['code_barre'],
            'nom' => $produit['nom'],
            'prix_unitaire_ht' => (float)$produit['prix_unitaire_ht'],
            'quantite' => (int)$quantite,
            'sous_total_ht' => (float)$produit['prix_unitaire_ht'] * (int)$quantite
        ];
        $_SESSION['facture_articles'][] = $ligne;
        $message = 'Article ajoute a la facture.';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Nouvelle facture</h1>
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/facturation/assets/js/scanner.js"></script>

<?php if ($message !== ''): ?><p class="succes"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<?php if ($erreur !== ''): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>

<form method="post" class="bloc">
    <label>Code-barres</label>
    <input type="text" id="code_barre" name="code_barre" value="<?= htmlspecialchars($code_barre) ?>" required>
    <button type="button" onclick="demarrerScanner()">Scanner avec camera</button>

    <label>Quantite vendue</label>
    <input type="text" name="quantite" value="<?= htmlspecialchars($quantite) ?>" required>

    <button type="submit">Ajouter la ligne</button>
</form>
<div id="zone-scanner" class="scanner"></div>

<h3>Lignes de la facture en cours</h3>
<table>
    <tr><th>Designation</th><th>PU HT</th><th>Qte</th><th>Sous-total HT</th></tr>
    <?php foreach ($_SESSION['facture_articles'] as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['nom']) ?></td>
            <td><?= number_format($a['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</td>
            <td><?= (int)$a['quantite'] ?></td>
            <td><?= number_format($a['sous_total_ht'], 2, ',', ' ') ?> CDF</td>
        </tr>
    <?php endforeach; ?>
</table>

<form method="post" action="/facturation/modules/facturation/calcul.php">
    <button type="submit" <?= count($_SESSION['facture_articles']) === 0 ? 'disabled' : '' ?>>Valider la facture</button>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
