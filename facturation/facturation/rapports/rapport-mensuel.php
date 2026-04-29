<?php

/**
 * Rapport mensuel
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'manager', 'super_admin');
?>

<div class="p-6">
    <h1 class="text-3xl font-black text-white mb-6">Rapport mensuel</h1>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <p class="text-slate-400">Page de rapport mensuel des ventes</p>
        <!-- TODO: Implémenter le rapport mensuel -->
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>