<?php

/**
 * Gestion des comptes utilisateurs
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'super_admin');

// TODO: Implémenter la gestion complète des utilisateurs
// Pour maintenant, afficher une interface de gestion
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-black text-white mb-2">Gestion des comptes</h1>
            <p class="text-slate-400">Gérez les utilisateurs et leurs permissions</p>
        </div>
        <button onclick="openAddUserModal()"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition-all">
            + Nouveau compte
        </button>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-950 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Nom</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Identifiant</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Rôle</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                        <td class="px-4 py-3 font-semibold text-white">Dan MBO</td>
                        <td class="px-4 py-3 text-slate-400">dan.mbo</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 bg-blue-900/30 text-blue-400 rounded-lg text-xs font-semibold">
                                Caissier
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button class="text-slate-400 hover:text-slate-200 mr-2" title="Éditer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button class="text-red-400 hover:text-red-300" title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 bg-slate-900 border border-slate-800 rounded-xl p-4 text-sm text-slate-400">
        <p><strong>Note:</strong> La gestion complète des utilisateurs (ajout, modification, suppression) sera disponible après configuration de la base de données.</p>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>