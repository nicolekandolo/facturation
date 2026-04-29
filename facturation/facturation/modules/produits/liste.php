<?php

/**
 * Liste des produits
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'manager', 'super_admin');

$products = product_get_all();
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-black text-white mb-2">Produits</h1>
            <p class="text-slate-400">Gestion des stocks et des produits</p>
        </div>
        <a href="enregistrer.php"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition-all">
            + Nouveau produit
        </a>
    </div>

    <!-- Tableau des produits -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-950 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Nom</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Code-barres</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Catégorie</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-slate-300">Prix HT</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-slate-300">Stock</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-slate-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                Aucun produit
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                                <td class="px-4 py-3 font-semibold text-white"><?= htmlspecialchars($product['name']) ?></td>
                                <td class="px-4 py-3 text-slate-400 font-mono text-sm"><?= htmlspecialchars($product['barcode'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= htmlspecialchars($product['category'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-right text-indigo-400 font-bold"><?= format_price($product['price']) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2 py-1 bg-slate-800 rounded text-sm font-semibold <?= $product['stock'] < $product['min_stock_alert'] ? 'text-orange-400' : 'text-slate-300' ?>">
                                        <?= $product['stock'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="text-slate-400 hover:text-slate-200" title="Éditer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>