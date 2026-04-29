import React, { useState } from 'react';
import { FileCode, Clipboard, Check, HardDrive } from 'lucide-react';

interface PHPFile {
  name: string;
  path: string;
  content: string;
  category: 'core' | 'data' | 'pages';
}

export const PHPSourceView: React.FC = () => {
  const [copied, setCopied] = useState<string | null>(null);
  const [activeFile, setActiveFile] = useState<number>(0);

  const phpFiles: PHPFile[] = [
    {
      category: 'core',
      name: 'config.php',
      path: 'config/config.php',
      content: `<?php
// Configuration globale
session_start();

$base_url = '/facturation'; // Ajustez si le projet est à la racine ('')
$data_dir = __DIR__ . '/../data/';

// Vérification de connexion
function check_auth($required_role = null) {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . $GLOBALS['base_url'] . '/index.php');
        exit();
    }
    
    if ($required_role) {
        $roles_hierarchy = ['caissier' => 1, 'manager' => 2, 'super_admin' => 3];
        $user_role = $_SESSION['user']['role'];
        
        if ($roles_hierarchy[$user_role] < $roles_hierarchy[$required_role]) {
            die("Accès refusé : Autorisations insuffisantes.");
        }
    }
}
?>`
    },
    {
      category: 'data',
      name: 'produits.json',
      path: 'data/produits.json',
      content: `[
  {
    "id": "p1",
    "name": "Bouteille d'eau 1.5L",
    "price": 1.5,
    "stock": 120,
    "barcode": "3017620422003",
    "category": "Boissons",
    "minStockAlert": 20
  },
  {
    "id": "p2",
    "name": "Pain de Mie complet",
    "price": 2.2,
    "stock": 45,
    "barcode": "5449000000996",
    "category": "Alimentation",
    "minStockAlert": 10
  }
]`
    },
    {
      category: 'pages',
      name: 'index.php (Login & POS)',
      path: 'index.php',
      content: `<?php
require_once 'config/config.php';

// Traitement Connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $users = json_decode(file_get_contents($data_dir . 'utilisateurs.json'), true);
    
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password && $user['isActive']) {
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit();
        }
    }
    $error = "Identifiants invalides.";
}

// Si connecté, charger le POS
if (isset($_SESSION['user'])) {
    $products = json_decode(file_get_contents($data_dir . 'produits.json'), true);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>POS - Système de Facturation</title>
        <script src="https://unpkg.com/@ericblade/quagga2/dist/quagga.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-dark bg-dark px-3">
            <span class="navbar-brand">Facturation PHP (<?= $_SESSION['user']['name'] ?>)</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
        </nav>
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-md-8">
                    <h4>Catalogue Produits</h4>
                    <div class="row" id="product-list">
                        <?php foreach ($products as $p): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $p['name'] ?></h5>
                                        <p class="card-text">Prix: <?= $p['price'] ?>€ | Stock: <?= $p['stock'] ?></p>
                                        <button class="btn btn-primary btn-sm" onclick="addToCart('<?= $p['id'] ?>', '<?= addslashes($p['name']) ?>', <?= $p['price'] ?>)">Ajouter</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-4 bg-white p-3 border">
                    <h4>Panier</h4>
                    <table class="table text-sm">
                        <thead><tr><th>Nom</th><th>Qte</th><th>Prix</th></tr></thead>
                        <tbody id="cart-items"></tbody>
                    </table>
                    <button class="btn btn-success w-100 mt-2">Encaisser</button>
                </div>
            </div>
        </div>
        <script>
            let cart = [];
            function addToCart(id, name, price) {
                cart.push({id, name, price});
                renderCart();
            }
            function renderCart() {
                const tbody = document.getElementById('cart-items');
                tbody.innerHTML = cart.map(i => \`<tr><td>\${i.name}</td><td>1</td><td>\${i.price}€</td></tr>\`).join('');
            }
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
<!-- Formulaire Login initial -->
<!DOCTYPE html>
<html>
<head><title>Connexion</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-secondary d-flex align-items-center justify-content-center" style="height:100vh;">
    <div class="card p-4" style="width:350px;">
        <h4 class="text-center mb-3">Connexion</h4>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <input type="hidden" name="login" value="1">
            <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Identifiant" required></div>
            <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Mot de passe" required></div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</body>
</html>`
    },
    {
      category: 'pages',
      name: 'produits.php (CRUD)',
      path: 'produits.php',
      content: `<?php
require_once 'config/config.php';
check_auth('manager');

$products_file = $data_dir . 'produits.json';
$products = json_decode(file_get_contents($products_file), true);

// Ajouter / Modifier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? uniqid('p-');
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $barcode = $_POST['barcode'] ?? '';
    $category = $_POST['category'] ?? '';
    
    $found = false;
    foreach ($products as &$p) {
        if ($p['id'] === $id) {
            $p = compact('id', 'name', 'price', 'stock', 'barcode', 'category');
            $found = true;
            break;
        }
    }
    if (!$found) {
        $products[] = compact('id', 'name', 'price', 'stock', 'barcode', 'category');
    }
    
    file_put_contents($products_file, json_encode($products, JSON_PRETTY_PRINT));
    header('Location: produits.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Produits</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
    <div class="container py-4">
        <h2>Gestion des Produits</h2>
        <form method="POST" class="row g-3 bg-white p-3 border mb-4">
            <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Nom" required></div>
            <div class="col-md-2"><input type="number" step="0.01" name="price" class="form-control" placeholder="Prix" required></div>
            <div class="col-md-2"><input type="number" name="stock" class="form-control" placeholder="Stock" required></div>
            <div class="col-md-2"><input type="text" name="barcode" class="form-control" placeholder="Code-barres"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-success w-100">Sauvegarder</button></div>
        </form>
        <table class="table table-bordered bg-white">
            <thead><tr><th>Nom</th><th>Prix</th><th>Stock</th><th>Code-barres</th></tr></thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr><td><?= $p['name'] ?></td><td><?= $p['price'] ?>€</td><td><?= $p['stock'] ?></td><td><?= $p['barcode'] ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>`
    }
  ];

  const handleCopy = (content: string, name: string) => {
    navigator.clipboard.writeText(content);
    setCopied(name);
    setTimeout(() => setCopied(null), 2000);
  };

  return (
    <div className="p-4 space-y-6">
      <div className="flex items-center space-x-3">
        <div className="p-2 bg-indigo-500/20 rounded-xl text-indigo-400">
          <HardDrive size={24} />
        </div>
        <div>
          <h2 className="text-2xl font-black text-white">Fichiers Sources PHP</h2>
          <p className="text-slate-400 text-sm mt-0.5">Pour un déploiement Apache/XAMPP/Laragon (Travaux Pratiques).</p>
        </div>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        {/* Files Selection */}
        <div className="w-full lg:w-72 bg-slate-900 border border-slate-800 rounded-xl p-3 h-fit flex flex-col gap-1">
          {phpFiles.map((file, idx) => (
            <button
              key={file.path}
              onClick={() => setActiveFile(idx)}
              className={`flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-bold transition-all text-left ${
                activeFile === idx
                  ? 'bg-indigo-600 text-white'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
              }`}
            >
              <FileCode size={16} className={activeFile === idx ? 'text-white' : 'text-slate-500'} />
              <div className="truncate">
                <div className="font-mono">{file.name}</div>
                <span className="text-[10px] opacity-60 font-medium">{file.path}</span>
              </div>
            </button>
          ))}
        </div>

        {/* Code Output */}
        <div className="flex-1 bg-slate-950 border border-slate-850 rounded-xl overflow-hidden relative flex flex-col">
          <div className="px-4 py-3 bg-slate-900 flex justify-between items-center border-b border-slate-850">
            <span className="text-xs font-mono text-indigo-400 font-bold">{phpFiles[activeFile].path}</span>
            <button
              onClick={() => handleCopy(phpFiles[activeFile].content, phpFiles[activeFile].name)}
              className="flex items-center space-x-1.5 px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-xs font-bold transition-all"
            >
              {copied === phpFiles[activeFile].name ? (
                <>
                  <Check size={14} className="text-emerald-400" />
                  <span className="text-emerald-400">Copié!</span>
                </>
              ) : (
                <>
                  <Clipboard size={14} />
                  <span>Copier</span>
                </>
              )}
            </button>
          </div>
          <div className="p-4 overflow-x-auto font-mono text-xs text-slate-300 leading-relaxed max-h-[500px]">
            <pre className="whitespace-pre">{phpFiles[activeFile].content}</pre>
          </div>
        </div>
      </div>
    </div>
  );
};
