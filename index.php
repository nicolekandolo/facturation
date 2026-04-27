<?php
require_once __DIR__ . '/auth/session.php';
exiger_connexion();
require_once __DIR__ . '/includes/header.php';

if (isset($_SESSION['message_erreur'])) {
    echo '<p class="erreur">' . htmlspecialchars($_SESSION['message_erreur']) . '</p>';
    unset($_SESSION['message_erreur']);
}
?>
<h1>Accueil</h1>
<p>Bienvenue dans le systeme de facturation.</p>
<ul>
    <li>Utilisez le menu pour enregistrer des produits, faire des factures et gerer les comptes selon votre role.</li>
</ul>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
