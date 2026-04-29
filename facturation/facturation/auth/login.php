<?php

/**
 * Page de connexion
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions-auth.php';

session_start_safe();

if (!empty($_SESSION['user'])) {
    header('Location: /facturation/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Données de test (à remplacer par une vraie authentification)
    $users = [
        ['username' => 'dan.mbo', 'password' => 'password', 'name' => 'Dan MBO', 'role' => 'caissier'],
        ['username' => 'manager', 'password' => 'password', 'name' => 'Manager', 'role' => 'manager'],
        ['username' => 'admin', 'password' => 'password', 'name' => 'Admin', 'role' => 'super_admin'],
    ];

    $user_found = null;
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $user_found = $user;
            break;
        }
    }

    if ($user_found) {
        auth_login($username, $password, $user_found);
        header('Location: /facturation/index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturation PRO - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-slate-950 text-slate-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="p-3 bg-indigo-600/20 rounded-xl">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-black text-white text-center mb-1">FACTURATION PRO</h1>
            <p class="text-center text-slate-400 text-sm mb-6">Gestion des factures en CDF</p>

            <?php if ($error): ?>
                <div class="bg-red-950/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg mb-4 text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-300 mb-2">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="dan.mbo">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">Mot de passe</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-lg shadow-lg shadow-indigo-500/20 active:scale-[.98] transition-all mt-6">
                    Se connecter
                </button>
            </form>

            <p class="text-center text-slate-500 text-xs mt-6">
                <strong>Démo:</strong> dan.mbo / password
            </p>
        </div>
    </div>
</body>

</html>