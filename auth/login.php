<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

    if ($identifiant === '' || $mot_de_passe === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } else {
        $utilisateur = verifier_connexion($identifiant, $mot_de_passe, $CONFIG['fichier_utilisateurs']);
        if ($utilisateur === null) {
            $erreur = 'Identifiant ou mot de passe incorrect.';
        } else {
            $_SESSION['utilisateur'] = $utilisateur;
            header('Location: /facturation/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="/facturation/assets/css/style.css">
</head>
<body>
<div class="container petit">
    <h1>Connexion</h1>
    <?php if ($erreur !== ''): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Identifiant</label>
        <input type="text" name="identifiant" required>

        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" required>

        <button type="submit">Se connecter</button>
    </form>
    <p><strong>Compte initial :</strong> identifiant <code>admin</code> / mot de passe <code>admin123</code></p>
</div>
</body>
</html>
