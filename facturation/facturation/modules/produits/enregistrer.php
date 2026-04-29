<?php

/**
 * Enregistrer un nouveau produit
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'manager', 'super_admin');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $barcode = $_POST['barcode'] ?? '';
    $category = $_POST['category'] ?? '';
    $min_stock = (int)($_POST['min_stock_alert'] ?? 0);

    if (empty($name) || $price <= 0) {
        $error = 'Veuillez remplir correctement le formulaire';
    } else {
        $product = product_create([
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'barcode' => $barcode,
            'category' => $category,
            'min_stock_alert' => $min_stock,
        ]);
        $message = 'Produit créé avec succès!';
        header('Location: liste.php?success=1');
        exit;
    }
}
?>

<div class="p-6 max-w-2xl">
    <div class="mb-6">
        <a href="liste.php" class="text-indigo-400 hover:text-indigo-300 mb-4 inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
        <h1 class="text-3xl font-black text-white mb-2">Nouveau produit</h1>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-950/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Nom du produit *</label>
                <input type="text" name="name" required
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Prix HT (CDF) *</label>
                <input type="number" name="price" step="0.01" min="0" required
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Code-barres</label>
                <input type="text" name="barcode"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Catégorie</label>
                <input type="text" name="category"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Stock</label>
                <input type="number" name="stock" min="0" value="0"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Alerte stock min</label>
                <input type="number" name="min_stock_alert" min="0" value="5"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-800">
            <button type="submit"
                class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all">
                Créer le produit
            </button>
            <a href="liste.php"
                class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg transition-all text-center">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>