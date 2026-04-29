<?php
require_once __DIR__ . '/includes/functions.php';
$user = auth_check();

$page = $_GET['page'] ?? 'pos';

// Permissions par page
$access = [
    'pos'      => ['caissier','manager','super_admin'],
    'products' => ['manager','super_admin'],
    'invoices' => ['manager','super_admin'],
    'users'    => ['super_admin'],
];

if (!isset($access[$page]) || !in_array($user['role'], $access[$page])) {
    $page = 'pos';
}

$role_label = match($user['role']) {
    'super_admin' => 'Super Administrateur',
    'manager'     => 'Manager',
    default       => 'Caissier',
};
?>
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Facturation PRO</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex flex-col">

<!-- ── HEADER ──────────────────────────────────────────────────────────── -->
<header class="bg-slate-900 border-b border-slate-800 px-5 py-3 flex items-center justify-between">
  <div class="flex items-center gap-3">
    <div class="p-2 bg-indigo-600/20 rounded-xl">
      <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
    </div>
    <div>
      <div class="font-black text-white tracking-wide">FACTURATION PRO</div>
      <div class="text-[10px] text-indigo-400 font-semibold tracking-widest uppercase"><?= htmlspecialchars($role_label) ?></div>
    </div>
  </div>
  <div class="flex items-center gap-3">
    <div class="hidden sm:flex items-center gap-2 bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-700">
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      <span class="text-sm font-semibold text-slate-200"><?= htmlspecialchars($user['name']) ?></span>
    </div>
    <a href="logout.php" title="Déconnexion"
      class="p-2 bg-slate-800 hover:bg-red-950/40 text-slate-400 hover:text-red-400 rounded-xl border border-slate-700 transition-all">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
      </svg>
    </a>
  </div>
</header>

<div class="flex flex-1 flex-col lg:flex-row">
  <!-- ── SIDEBAR ────────────────────────────────────────────────────────── -->
  <nav class="bg-slate-900/60 lg:w-60 border-b lg:border-b-0 lg:border-r border-slate-800 p-3 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible">
    <?php
    $nav_items = [
      ['pos',      'Facturation / POS',   'M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
      ['products', 'Produits / Stocks',   'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
      ['invoices', 'Factures / Rapports', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
      ['users',    'Gérer Comptes',       'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
    ];
    foreach ($nav_items as [$key, $label, $path]):
      if (!isset($access[$key]) || !in_array($user['role'], $access[$key])) continue;
      $active = $page === $key;
    ?>
    <a href="?page=<?= $key ?>"
      class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap
        <?= $active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
      <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $path ?>"/>
      </svg>
      <span class="hidden sm:inline"><?= $label ?></span>
    </a>
    <?php endforeach; ?>
  </nav>

  <!-- ── CONTENU ───────────────────────────────────────────────────────── -->
  <main class="flex-1 overflow-y-auto">
    <?php
    $page_file = __DIR__ . '/pages/' . $page . '.php';
    if (file_exists($page_file)) {
        include $page_file;
    }
    ?>
  </main>
</div>
</body>
</html>
