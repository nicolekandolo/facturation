<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_role(['manager', 'super_admin']);

$code_barre = trim($_GET['code_barre'] ?? $_POST['code_barre'] ?? '');
$erreurs = [];
$succes = '';
$produit_existant = null;

$nom = trim($_POST['nom'] ?? '');
$prix = trim($_POST['prix_unitaire_ht'] ?? '');
$date_expiration = trim($_POST['date_expiration'] ?? '');
$quantite = trim($_POST['quantite_stock'] ?? '');

if ($code_barre !== '') {
    $produit_existant = trouver_produit_par_code($code_barre, $CONFIG['fichier_produits']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer'])) {
    if ($code_barre === '') {
        $erreurs[] = 'Le code-barres est obligatoire.';
    }
    if ($nom === '') {
        $erreurs[] = 'Le nom du produit est obligatoire.';
    }
    if ($prix === '' || !is_numeric($prix) || (float)$prix <= 0) {
        $erreurs[] = 'Le prix doit etre un nombre positif.';
    }
    if ($quantite === '' || !ctype_digit($quantite) || (int)$quantite < 0) {
        $erreurs[] = 'La quantite doit etre un entier positif.';
    }

    $date_ok = DateTime::createFromFormat('m-d-Y', $date_expiration);
    if (!$date_ok || $date_ok->format('m-d-Y') !== $date_expiration) {
        $erreurs[] = 'La date doit etre au format MM-JJ-AAAA.';
    }

    if ($produit_existant !== null) {
        $erreurs[] = 'Ce code-barres existe deja.';
    }

    if (count($erreurs) === 0) {
        $nouveau_produit = [
            'code_barre' => $code_barre,
            'nom' => $nom,
            'prix_unitaire_ht' => (float)$prix,
            'date_expiration' => $date_ok->format('Y-m-d'),
            'quantite_stock' => (int)$quantite,
            'date_enregistrement' => date('Y-m-d')
        ];

        ajouter_produit($nouveau_produit, $CONFIG['fichier_produits']);
        $succes = 'Produit enregistre avec succes.';
        $nom = '';
        $prix = '';
        $date_expiration = '';
        $quantite = '';
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Enregistrement des produits</h1>
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script src="/facturation/assets/js/scanner.js"></script>

<form method="get" class="bloc">
    <label>Code-barres</label>
    <input type="text" id="code_barre" name="code_barre" value="<?= htmlspecialchars($code_barre) ?>" required>
    <button type="button" onclick="demarrerScanner()">Scanner avec camera</button>
    <button type="submit">Verifier</button>
</form>
<div id="zone-scanner" class="scanner"></div>

<?php if ($succes !== ''): ?>
    <p class="succes"><?= htmlspecialchars($succes) ?></p>
<?php endif; ?>

<?php if (count($erreurs) > 0): ?>
    <?php foreach ($erreurs as $e): ?>
        <p class="erreur"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($code_barre !== '' && $produit_existant !== null): ?>
    <h3>Produit deja enregistre</h3>
    <p>Nom : <?= htmlspecialchars($produit_existant['nom']) ?></p>
    <p>Prix HT : <?= number_format($produit_existant['prix_unitaire_ht'], 2, ',', ' ') ?> CDF</p>
    <p>Stock : <?= (int)$produit_existant['quantite_stock'] ?></p>
<?php elseif ($code_barre !== ''): ?>
    <h3>Nouveau produit</h3>
    <form method="post" class="bloc">
        <input type="hidden" name="code_barre" value="<?= htmlspecialchars($code_barre) ?>">

        <label>Nom du produit</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>

        <label>Prix unitaire HT (CDF)</label>
        <input type="text" name="prix_unitaire_ht" value="<?= htmlspecialchars($prix) ?>" required>

        <label>Date expiration (MM-JJ-AAAA)</label>
        <input type="text" name="date_expiration" value="<?= htmlspecialchars($date_expiration) ?>" required>

        <label>Quantite initiale en stock</label>
        <input type="text" name="quantite_stock" value="<?= htmlspecialchars($quantite) ?>" required>

        <button type="submit" name="enregistrer">Enregistrer le produit</button>
    </form>
<?php endif; ?>

<p><a href="/facturation/modules/produits/liste.php">Voir la liste des produits</a></p>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
