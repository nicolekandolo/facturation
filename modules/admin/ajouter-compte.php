<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
exiger_role(['super_admin']);

$erreur = '';
$succes = '';
$identifiant = trim($_POST['identifiant'] ?? '');
$nom = trim($_POST['nom_complet'] ?? '');
$role = trim($_POST['role'] ?? 'caissier');
$mot_de_passe = trim($_POST['mot_de_passe'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($identifiant === '' || $nom === '' || $mot_de_passe === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (!in_array($role, ['caissier', 'manager'])) {
        $erreur = 'Role invalide.';
    } else {
        $utilisateurs = lire_json($CONFIG['fichier_utilisateurs']);
        $existe = false;
        foreach ($utilisateurs as $u) {
            if ($u['identifiant'] === $identifiant) {
                $existe = true;
            }
        }

        if ($existe) {
            $erreur = 'Cet identifiant existe deja.';
        } else {
            $nouveau = [
                'identifiant' => $identifiant,
                'mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT),
                'role' => $role,
                'nom_complet' => $nom,
                'date_creation' => date('Y-m-d'),
                'actif' => true
            ];
            ajouter_utilisateur($nouveau, $CONFIG['fichier_utilisateurs']);
            $succes = 'Compte ajoute avec succes.';
            $identifiant = '';
            $nom = '';
            $mot_de_passe = '';
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Ajouter un compte</h1>
<?php if ($erreur !== ''): ?><p class="erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
<?php if ($succes !== ''): ?><p class="succes"><?= htmlspecialchars($succes) ?></p><?php endif; ?>

<form method="post" class="bloc">
    <label>Identifiant</label>
    <input type="text" name="identifiant" value="<?= htmlspecialchars($identifiant) ?>" required>

    <label>Nom complet</label>
    <input type="text" name="nom_complet" value="<?= htmlspecialchars($nom) ?>" required>

    <label>Role</label>
    <select name="role">
        <option value="caissier" <?= $role === 'caissier' ? 'selected' : '' ?>>Caissier</option>
        <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Manager</option>
    </select>

    <label>Mot de passe</label>
    <input type="password" name="mot_de_passe" required>

    <button type="submit">Ajouter</button>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
