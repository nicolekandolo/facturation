<?php
require_once __DIR__ . '/includes/functions.php';
session_start_safe();

// Déjà connecté → redirige vers l'app
if (!empty($_SESSION['user'])) {
    header('Location: app.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Facturation PRO — Connexion</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="dark bg-slate-950 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">
  <div class="text-center mb-8">
    <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600/20 rounded-2xl mb-4">
      <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
    </div>
    <h1 class="text-2xl font-black text-white">FACTURATION PRO</h1>
    <p class="text-sm text-slate-400 mt-1">Système de point de vente</p>
  </div>

  <div class="bg-slate-900 rounded-2xl p-6 border border-slate-800 shadow-2xl">
    <div id="error-msg" class="hidden mb-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-sm"></div>

    <form id="login-form" class="space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nom d'utilisateur</label>
        <input id="username" type="text" autocomplete="username" placeholder="admin"
          class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Mot de passe</label>
        <input id="password" type="password" autocomplete="current-password" placeholder="••••••••"
          class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
      </div>
      <button type="submit"
        class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-[.98] text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20">
        Se connecter
      </button>
    </form>

    <div class="mt-5 pt-4 border-t border-slate-800 grid grid-cols-3 gap-2">
      <?php
      $hints = [
        ['admin','admin123','Super Admin'],
        ['manager','manager123','Manager'],
        ['caissier','caissier123','Caissier'],
      ];
      foreach ($hints as $h): ?>
      <button onclick="fillLogin('<?= $h[0] ?>','<?= $h[1] ?>')"
        class="text-[11px] text-slate-400 hover:text-slate-200 bg-slate-800 hover:bg-slate-700 rounded-lg px-2 py-1.5 transition-all text-center leading-tight">
        <span class="font-bold block"><?= $h[2] ?></span>
        <span class="opacity-70"><?= $h[0] ?></span>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function fillLogin(u, p) {
  document.getElementById('username').value = u;
  document.getElementById('password').value = p;
}

document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const errEl = document.getElementById('error-msg');
  errEl.classList.add('hidden');

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  if (!username || !password) {
    errEl.textContent = 'Remplissez tous les champs.';
    errEl.classList.remove('hidden');
    return;
  }

  const btn = this.querySelector('button[type=submit]');
  btn.disabled = true;
  btn.textContent = 'Connexion…';

  try {
    const res = await fetch('api/auth.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({username, password})
    });
    const data = await res.json();
    if (res.ok) {
      window.location.href = 'app.php';
    } else {
      errEl.textContent = data.error || 'Erreur de connexion.';
      errEl.classList.remove('hidden');
      btn.disabled = false;
      btn.textContent = 'Se connecter';
    }
  } catch {
    errEl.textContent = 'Erreur réseau. Vérifiez le serveur PHP.';
    errEl.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = 'Se connecter';
  }
});
</script>
</body>
</html>
