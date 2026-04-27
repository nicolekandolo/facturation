<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
exiger_role(['super_admin']);

$utilisateurs = lire_json($CONFIG['fichier_utilisateurs']);
require_once __DIR__ . '/../../includes/header.php';
?>
<h1>Gestion des comptes</h1>
<p><a href="/facturation/modules/admin/ajouter-compte.php">Ajouter un compte</a></p>
<table>
    <tr><th>Identifiant</th><th>Nom complet</th><th>Role</th><th>Actif</th><th>Action</th></tr>
    <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['identifiant']) ?></td>
            <td><?= htmlspecialchars($u['nom_complet']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= $u['actif'] ? 'Oui' : 'Non' ?></td>
            <td>
                <?php if ($u['identifiant'] !== 'admin'): ?>
                    <a href="/facturation/modules/admin/supprimer-compte.php?identifiant=<?= urlencode($u['identifiant']) ?>">Supprimer</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
