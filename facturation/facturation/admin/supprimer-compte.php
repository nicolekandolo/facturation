<?php

/**
 * Supprimer un compte utilisateur
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'super_admin');
?>

<div class="p-6 max-w-2xl">
    <a href="gestion-comptes.php" class="text-indigo-400 hover:text-indigo-300 mb-4 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Retour
    </a>
    <h1 class="text-3xl font-black text-white mb-6">Supprimer un compte</h1>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <p class="text-slate-400 mb-4">Page de suppression de compte utilisateur</p>
        <!-- TODO: Implémenter la suppression de compte -->
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>