<?php

/**
 * Affichage des factures
 */
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/fonctions-auth.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';
require_once __DIR__ . '/../../includes/header.php';

$user = auth_check();
require_role($user, 'manager', 'super_admin');

$invoices = invoice_get_all();
$filterDate = $_GET['date'] ?? date(DATE_FORMAT);
$filterCaissier = $_GET['caissier'] ?? '';

$filtered = array_filter($invoices, function ($inv) use ($filterDate, $filterCaissier) {
    $dateMatch = $inv['date'] === $filterDate;
    $caissierMatch = empty($filterCaissier) || $inv['caissier'] === $filterCaissier;
    return $dateMatch && $caissierMatch;
});

$totalDay = invoice_get_daily_total($filterDate);
$caissiers = [];
foreach ($invoices as $inv) {
    if ($inv['date'] === $filterDate) {
        $caissiers[$inv['caissier']] = true;
    }
}
$caissiers = array_keys($caissiers);
?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-white mb-2">Factures</h1>
        <p class="text-slate-400">Consultez et gérez les factures du jour</p>
    </div>

    <!-- Filtres -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Date</label>
                <input type="date" id="filter-date" value="<?= $filterDate ?>"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    onchange="applyFilters()">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-300 mb-2">Caissier</label>
                <select id="filter-caissier"
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    onchange="applyFilters()">
                    <option value="">Tous les caissiers</option>
                    <?php foreach ($caissiers as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $c === $filterCaissier ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Résumé -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl p-4 text-white">
            <div class="text-sm opacity-90">Total du jour</div>
            <div class="text-2xl font-black"><?= format_price($totalDay) ?></div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-4 text-white">
            <div class="text-sm opacity-90">Factures</div>
            <div class="text-2xl font-black"><?= count($filtered) ?></div>
        </div>
        <div class="bg-gradient-to-br from-orange-600 to-red-700 rounded-xl p-4 text-white">
            <div class="text-sm opacity-90">Moyenne par facture</div>
            <div class="text-2xl font-black"><?= count($filtered) > 0 ? format_price($totalDay / count($filtered)) : '0,00 CDF' ?></div>
        </div>
    </div>

    <!-- Tableau des factures -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-950 border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">N° Facture</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Client</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Caissier</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-300">Articles</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-slate-300">Total TTC</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-slate-300">Heure</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                Aucune facture pour cette sélection
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered as $inv): ?>
                            <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-all">
                                <td class="px-4 py-3 font-mono font-bold text-indigo-400"><?= htmlspecialchars($inv['id_facture']) ?></td>
                                <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($inv['client'] ?? 'Client') ?></td>
                                <td class="px-4 py-3 text-slate-200"><?= htmlspecialchars($inv['caissier']) ?></td>
                                <td class="px-4 py-3 text-slate-400"><?= count($inv['articles']) ?> article(s)</td>
                                <td class="px-4 py-3 text-right text-indigo-400 font-bold"><?= format_price($inv['total_ttc']) ?></td>
                                <td class="px-4 py-3 text-center text-slate-400 font-mono"><?= substr($inv['heure'], 0, 5) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const date = document.getElementById('filter-date').value;
        const caissier = document.getElementById('filter-caissier').value;

        const params = new URLSearchParams();
        if (date) params.append('date', date);
        if (caissier) params.append('caissier', caissier);

        window.location.search = params.toString();
    }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>