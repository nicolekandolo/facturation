<?php
require_once __DIR__ . '/../auth/session.php';
$user = utilisateur_actuel();
$role = role_actuel();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facturation PHP</title>
    <link rel="stylesheet" href="/facturation/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <h2>Systeme de Facturation</h2>
    <?php if ($user): ?>
        <div>
            Connecte : <strong><?= htmlspecialchars($user['nom_complet']) ?></strong> (<?= htmlspecialchars($role) ?>)
            <a class="lien" href="/facturation/auth/logout.php">Deconnexion</a>
        </div>
    <?php endif; ?>
</header>
<nav class="menu">
    <a href="/facturation/index.php">Accueil</a>
    <a href="/facturation/modules/facturation/nouvelle-facture.php">Facturation</a>
    <?php if (in_array($role, ['manager', 'super_admin'])): ?>
        <a href="/facturation/modules/produits/enregistrer.php">Produits</a>
        <a href="/facturation/rapports/rapport-journalier.php">Rapport journalier</a>
        <a href="/facturation/rapports/rapport-mensuel.php">Rapport mensuel</a>
    <?php endif; ?>
    <?php if ($role === 'super_admin'): ?>
        <a href="/facturation/modules/admin/gestion-comptes.php">Comptes</a>
    <?php endif; ?>
</nav>
<main class="container">
