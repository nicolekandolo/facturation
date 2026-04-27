<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
exiger_role(['super_admin']);

$identifiant = trim($_GET['identifiant'] ?? '');
if ($identifiant !== '' && $identifiant !== 'admin') {
    supprimer_utilisateur($identifiant, $CONFIG['fichier_utilisateurs']);
}

header('Location: /facturation/modules/admin/gestion-comptes.php');
exit;
