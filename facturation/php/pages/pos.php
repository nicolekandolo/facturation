<!-- ╔══════════════════════════════════════════════════════════════════════╗ -->
<!-- ║  POS — Point de Vente avec Scanner de codes-barres amélioré          ║ -->
<!-- ╚══════════════════════════════════════════════════════════════════════╝ -->

<div id="pos-app" class="flex flex-col lg:flex-row h-[calc(100vh-112px)] gap-4 p-4">

  <!-- ── CATALOGUE ─────────────────────────────────────────────────────── -->
  <div class="flex-1 flex flex-col bg-slate-900/50 rounded-xl border border-slate-800 p-4 min-h-0">
    <div class="flex gap-3 mb-4">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input id="search" type="text" placeholder="Rechercher par nom ou code-barres…"
          class="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
      </div>
      <button onclick="openScanner()"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2 rounded-lg font-semibold transition-all shadow-lg shadow-indigo-500/20 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        Scanner
      </button>
    </div>
    <div id="cat-filters" class="flex gap-2 flex-wrap mb-4"></div>
    <div id="product-grid" class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 content-start"></div>
  </div>

  <!-- ── PANIER ────────────────────────────────────────────────────────── -->
  <div class="w-full lg:w-96 flex flex-col bg-slate-900 rounded-xl border border-slate-800 shadow-xl overflow-hidden">
    <div class="p-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
      <div class="flex items-center gap-2 text-indigo-400">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <h3 class="text-white font-bold">Panier</h3>
      </div>
      <span id="cart-count" class="text-xs px-2 py-1 bg-slate-800 rounded-full text-slate-300 font-semibold">0 article</span>
    </div>

    <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-3">
      <div id="cart-empty" class="h-full flex flex-col items-center justify-center text-slate-500 py-10">
        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
            d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-sm">Le panier est vide</p>
        <p class="text-xs opacity-60 mt-1">Scannez ou cliquez un produit</p>
      </div>
    </div>

    <div class="p-4 bg-slate-950 border-t border-slate-800 space-y-4">
      <div class="space-y-1 text-sm border-b border-slate-800/50 pb-3">
        <div class="flex justify-between text-slate-400"><span>Sous-total HT</span><span id="subtotal">0,00 €</span></div>
        <div class="flex justify-between text-slate-400"><span>TVA (20%)</span><span id="tax">0,00 €</span></div>
        <div class="flex justify-between font-bold text-base mt-2 pt-1 border-t border-slate-800/50">
          <span class="text-white">Total TTC</span>
          <span id="total" class="text-indigo-400 text-lg">0,00 €</span>
        </div>
      </div>
      <div>
        <input id="customer-name" type="text" placeholder="Nom client (facultatif)"
          class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none mb-3">

        <label class="block text-xs font-semibold text-slate-400 mb-2">Moyen de paiement</label>
        <div class="grid grid-cols-3 gap-2 mb-4">
          <?php foreach ([['cash','Espèces','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],['card','Carte','M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],['mobile','Mobile','M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z']] as [$val,$lbl,$ico]): ?>
          <button onclick="setPayment('<?= $val ?>')" data-pm="<?= $val ?>"
            class="pm-btn flex flex-col items-center py-2 rounded-lg border text-xs font-medium transition-all bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $ico ?>"/>
            </svg><?= $lbl ?>
          </button>
          <?php endforeach; ?>
        </div>

        <div id="cash-block" class="hidden mb-3 bg-slate-800/40 border border-slate-800 p-2.5 rounded-lg flex items-center justify-between gap-3">
          <div class="flex-1">
            <label class="block text-[10px] uppercase tracking-wide font-bold text-slate-400 mb-1">Montant perçu</label>
            <input id="cash-received" type="number" step="0.01" min="0" placeholder="0,00"
              class="w-full bg-slate-800 text-sm font-bold text-white px-2 py-1 rounded focus:outline-none"
              oninput="updateChange()">
          </div>
          <div id="change-block" class="hidden text-right">
            <span class="block text-[10px] uppercase tracking-wide font-bold text-slate-400">Rendu</span>
            <span id="change-amount" class="text-base font-black text-green-400">0,00 €</span>
          </div>
        </div>

        <button id="checkout-btn" onclick="checkout()" disabled
          class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700
            disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed
            text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 active:scale-[.98] transition-all text-sm">
          Encaisser (0,00 €)
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════ TOAST NOTIFICATIONS ══════════════════════════════ -->
<div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"></div>

<!-- ══════════════════ MODAL SCANNER ════════════════════════════════════ -->
<div id="scanner-modal" class="hidden fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-slate-900 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl border border-slate-800">

    <!-- En-tête -->
    <div class="px-4 py-3 border-b border-slate-800 bg-slate-950 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        <h3 class="text-white font-bold">Lecteur Code-barres</h3>
        <!-- Badge moteur -->
        <span id="engine-badge" class="hidden text-[10px] px-1.5 py-0.5 rounded-full font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"></span>
      </div>
      <button onclick="closeScanner()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Zone caméra -->
    <div id="camera-area" class="relative bg-black overflow-hidden" style="aspect-ratio:16/9">

      <!-- Vidéo BarcodeDetector -->
      <video id="scanner-video" class="w-full h-full object-cover" playsinline muted></video>

      <!-- Conteneur Quagga2 (remplace la vidéo) -->
      <div id="quagga-zone" class="hidden absolute inset-0 overflow-hidden"></div>

      <!-- Flash de détection -->
      <div id="scan-flash" class="scan-flash"></div>

      <!-- Viseur de scan -->
      <div id="scanner-reticle" class="hidden absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
        <div class="relative w-72 h-40">
          <!-- Coins du viseur -->
          <div class="absolute top-0 left-0 w-6 h-6 border-t-2 border-l-2 border-indigo-400 rounded-tl-sm"></div>
          <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-indigo-400 rounded-tr-sm"></div>
          <div class="absolute bottom-0 left-0 w-6 h-6 border-b-2 border-l-2 border-indigo-400 rounded-bl-sm"></div>
          <div class="absolute bottom-0 right-0 w-6 h-6 border-b-2 border-r-2 border-indigo-400 rounded-br-sm"></div>
          <!-- Ligne de scan animée -->
          <div class="scan-line"></div>
          <!-- Overlay semi-transparent -->
          <div class="absolute inset-0 bg-indigo-500/5 rounded-sm"></div>
        </div>
        <p id="scan-status" class="text-white text-xs mt-3 bg-black/70 px-3 py-1 rounded-full backdrop-blur-sm">
          Centrez le code-barres dans le cadre
        </p>
      </div>

      <!-- Spinner chargement -->
      <div id="scan-loading" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-950/80">
        <svg class="w-10 h-10 text-indigo-400 animate-spin mb-3" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p id="scan-loading-text" class="text-slate-300 text-sm">Démarrage de la caméra…</p>
      </div>

      <!-- Erreur caméra -->
      <div id="scanner-error" class="hidden absolute inset-0 flex flex-col items-center justify-center p-6 bg-slate-950">
        <svg class="w-12 h-12 text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 10l4.553-2.069A1 1 0 0121 8.878V15.12a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
        </svg>
        <p id="scanner-error-msg" class="text-red-400 text-sm text-center font-medium mb-1"></p>
        <p class="text-slate-500 text-xs text-center mb-4">Utilisez la saisie manuelle ou le simulateur ci-dessous</p>
        <button onclick="retryCamera()"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-all">
          Réessayer
        </button>
      </div>
    </div>

    <!-- Barre d'outils caméra -->
    <div id="camera-toolbar" class="hidden px-4 py-2 bg-slate-950/80 border-t border-slate-800/50 flex items-center gap-2 flex-wrap">
      <!-- Sélection caméra -->
      <select id="camera-select"
        class="flex-1 min-w-0 bg-slate-800 border border-slate-700 text-slate-300 text-xs rounded-lg px-2 py-1.5 focus:outline-none truncate">
      </select>
      <!-- Torch -->
      <button id="torch-btn" onclick="toggleTorch()"
        class="hidden p-1.5 bg-slate-800 hover:bg-amber-500/20 text-slate-400 hover:text-amber-400 rounded-lg border border-slate-700 transition-all" title="Lampe torche">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
      </button>
      <!-- Mode multi-scan -->
      <button id="multiscan-btn" onclick="toggleMultiScan()"
        class="flex items-center gap-1.5 px-2 py-1.5 bg-slate-800 text-slate-400 border border-slate-700 rounded-lg text-xs font-semibold transition-all hover:text-slate-200"
        title="Scanner plusieurs articles sans fermer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Multi-scan
      </button>
    </div>

    <!-- Pied du modal -->
    <div class="p-4 bg-slate-950 space-y-3">
      <!-- Saisie manuelle -->
      <div class="flex gap-2">
        <input id="manual-barcode" type="text" inputmode="numeric" placeholder="Saisir un code-barres manuellement…"
          class="flex-1 bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          onkeydown="if(event.key==='Enter'){ manualScan(); event.preventDefault(); }">
        <button onclick="manualScan()"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-all active:scale-95">OK</button>
      </div>

      <!-- Simulateur -->
      <div>
        <p class="text-xs font-semibold text-slate-500 mb-2">Simulateur — cliquez pour tester :</p>
        <div id="sim-buttons" class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto"></div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════ MODAL SUCCÈS ═════════════════════════════════════ -->
<div id="success-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl p-6 w-full max-w-sm text-slate-900 shadow-2xl">
    <div class="flex flex-col items-center border-b border-slate-200 pb-4 mb-4">
      <svg class="w-12 h-12 text-emerald-500 mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <h3 class="text-xl font-black">Paiement Réussi !</h3>
      <p id="modal-inv-num" class="text-xs text-slate-500 mt-1"></p>
    </div>
    <div id="modal-details" class="text-xs space-y-1 mb-4"></div>
    <div id="modal-items" class="border-t border-dashed border-slate-300 pt-3 pb-3 mb-3 text-xs space-y-1.5"></div>
    <div class="flex justify-between font-bold text-sm mb-5">
      <span>TOTAL TTC</span>
      <span id="modal-total" class="text-indigo-600 text-base"></span>
    </div>
    <div class="flex gap-2">
      <button onclick="closeSuccess()" class="flex-1 py-2 text-xs font-bold border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50">Fermer</button>
      <button onclick="window.print()" class="flex-1 py-2 text-xs font-bold bg-slate-900 text-white rounded-lg hover:bg-slate-800">Imprimer</button>
    </div>
  </div>
</div>

<!-- ══════════════════ JAVASCRIPT ════════════════════════════════════════ -->
<script>
'use strict';

// ── État global ───────────────────────────────────────────────────────────────
let products     = [];
let cart         = {};
let payment      = 'cash';
let activeCategory = 'Tous';

// ── État scanner ──────────────────────────────────────────────────────────────
let scanMode      = null;      // 'native' | 'quagga' | null
let scanStream    = null;
let scanInterval  = null;
let quaggaActive  = false;
let torchOn       = false;
let torchTrack    = null;
let multiScan     = false;
let cameras       = [];
let selectedCamId = null;

// Anti-doublon : même code pendant 2 s = ignoré
let lastCode     = '';
let lastCodeTime = 0;
const COOLDOWN   = 2000;

// ── Produits ─────────────────────────────────────────────────────────────────
async function loadProducts() {
  const res = await fetch('api/products.php');
  products  = await res.json();
  renderCategories();
  renderProducts();
  renderSimButtons();
}

function renderCategories() {
  const cats = ['Tous', ...new Set(products.map(p => p.category))];
  document.getElementById('cat-filters').innerHTML = cats.map(c => `
    <button onclick="filterCat('${c}')" data-cat="${c}"
      class="cat-btn px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all
        ${c === activeCategory ? 'bg-indigo-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-slate-200'}">
      ${c}
    </button>`).join('');
}

function filterCat(cat) {
  activeCategory = cat;
  renderCategories();
  renderProducts();
}

function renderProducts() {
  const search = document.getElementById('search').value.toLowerCase();
  const grid   = document.getElementById('product-grid');
  const filtered = products.filter(p =>
    (p.name.toLowerCase().includes(search) || (p.barcode||'').includes(search)) &&
    (activeCategory === 'Tous' || p.category === activeCategory)
  );

  if (!filtered.length) {
    grid.innerHTML = `<div class="col-span-full text-center text-slate-500 py-12 text-sm">Aucun produit trouvé</div>`;
    return;
  }

  grid.innerHTML = filtered.map(p => {
    const out    = p.stock <= 0;
    const inCart = cart[p.id];
    const low    = p.stock > 0 && p.stock <= p.min_stock_alert;
    return `
    <button onclick="addToCart('${p.id}')" ${out ? 'disabled' : ''}
      class="relative p-4 rounded-xl text-left border flex flex-col justify-between transition-all group
        ${out   ? 'bg-slate-950 border-slate-900 opacity-40 cursor-not-allowed'
        : inCart ? 'bg-indigo-500/10 border-indigo-500/50 hover:bg-indigo-500/20'
                 : 'bg-slate-800/40 border-slate-800 hover:border-slate-700 hover:bg-slate-800/80 hover:-translate-y-0.5'}">
      ${inCart ? `<span class="absolute -top-2 -right-2 bg-indigo-500 text-white text-xs font-bold h-6 w-6 rounded-full flex items-center justify-center shadow-lg">${inCart.quantity}</span>` : ''}
      <div>
        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">${p.category}</span>
        <h4 class="text-slate-200 font-bold mt-1 line-clamp-2 text-sm group-hover:text-indigo-400 transition-colors">${p.name}</h4>
      </div>
      <div class="mt-3 pt-2 border-t border-slate-800/50 flex items-center justify-between">
        <span class="text-base font-black text-white">${fmt(p.price)}</span>
        <span class="text-[10px] px-1.5 py-0.5 rounded ${out ? 'bg-red-500/10 text-red-400' : low ? 'bg-amber-500/10 text-amber-400' : 'bg-green-500/10 text-green-400'}">
          ${out ? 'Épuisé' : 'Stock: '+p.stock}
        </span>
      </div>
    </button>`;
  }).join('');
}

document.getElementById('search').addEventListener('input', renderProducts);

// ── Panier ────────────────────────────────────────────────────────────────────
function addToCart(id) {
  const prod = products.find(p => p.id === id);
  if (!prod || prod.stock <= 0) return;
  if (!cart[id]) cart[id] = { ...prod, quantity: 1 };
  else if (cart[id].quantity < prod.stock) cart[id].quantity++;
  else return;
  renderCart();
  renderProducts();
}

function updateQty(id, delta) {
  if (!cart[id]) return;
  const newQty = cart[id].quantity + delta;
  if (newQty <= 0) { delete cart[id]; }
  else {
    const prod = products.find(p => p.id === id);
    if (newQty > prod.stock) return;
    cart[id].quantity = newQty;
  }
  renderCart();
  renderProducts();
}

function renderCart() {
  const items   = Object.values(cart);
  const totalQ  = items.reduce((s,i) => s+i.quantity, 0);
  const emptyEl = document.getElementById('cart-empty');
  const itemsEl = document.getElementById('cart-items');
  const btnEl   = document.getElementById('checkout-btn');

  document.getElementById('cart-count').textContent = totalQ + (totalQ > 1 ? ' articles' : ' article');

  if (!items.length) {
    emptyEl.classList.remove('hidden');
    itemsEl.innerHTML = '';
    itemsEl.prepend(emptyEl);
    btnEl.disabled    = true;
    btnEl.textContent = 'Encaisser (0,00 €)';
    ['subtotal','tax','total'].forEach(id => document.getElementById(id).textContent = '0,00 €');
    return;
  }

  emptyEl.classList.add('hidden');
  itemsEl.innerHTML = items.map(item => `
    <div class="flex items-center justify-between bg-slate-950 p-3 rounded-lg border border-slate-800">
      <div class="flex-1 min-w-0 pr-2">
        <h5 class="text-slate-200 text-sm font-semibold truncate">${item.name}</h5>
        <p class="text-xs text-slate-400 mt-0.5">${fmt(item.price)} × ${item.quantity}</p>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex items-center bg-slate-800 rounded-lg p-0.5">
          <button onclick="updateQty('${item.id}',-1)" class="p-1 text-slate-400 hover:text-white rounded">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
          </button>
          <span class="text-slate-200 font-bold px-2 text-sm">${item.quantity}</span>
          <button onclick="updateQty('${item.id}',1)" class="p-1 text-slate-400 hover:text-white rounded">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          </button>
        </div>
        <button onclick="updateQty('${item.id}',-999)" class="p-1.5 text-slate-500 hover:text-red-400 rounded-lg">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>`).join('');

  const sub   = items.reduce((s,i) => s + i.price*i.quantity, 0);
  const tax   = sub * 0.20;
  const total = sub + tax;

  document.getElementById('subtotal').textContent = fmt(sub);
  document.getElementById('tax').textContent      = fmt(tax);
  document.getElementById('total').textContent    = fmt(total);
  btnEl.disabled    = false;
  btnEl.textContent = `Encaisser (${fmt(total)})`;
  updateChange();
}

// ── Paiement ──────────────────────────────────────────────────────────────────
function setPayment(pm) {
  payment = pm;
  document.querySelectorAll('.pm-btn').forEach(b => {
    const active = b.dataset.pm === pm;
    b.className = b.className
      .replace(/bg-indigo-500\/20\s?border-indigo-500\s?text-indigo-300/g, '')
      .replace(/bg-slate-800\s?border-slate-700\s?text-slate-400/g, '');
    b.classList.add(...(active
      ? ['bg-indigo-500/20','border-indigo-500','text-indigo-300']
      : ['bg-slate-800','border-slate-700','text-slate-400']));
  });
  document.getElementById('cash-block').classList.toggle('hidden', pm !== 'cash');
}

function updateChange() {
  const items = Object.values(cart);
  if (!items.length) return;
  const total = items.reduce((s,i) => s+i.price*i.quantity, 0) * 1.20;
  const recv  = parseFloat(document.getElementById('cash-received').value) || 0;
  const el    = document.getElementById('change-block');
  if (recv > total) {
    el.classList.remove('hidden');
    document.getElementById('change-amount').textContent = fmt(recv - total);
  } else { el.classList.add('hidden'); }
}

// ── Encaisser ─────────────────────────────────────────────────────────────────
async function checkout() {
  const items = Object.values(cart).map(i => ({ product_id: i.id, quantity: i.quantity }));
  if (!items.length) return;
  const btn = document.getElementById('checkout-btn');
  btn.disabled = true; btn.textContent = 'Traitement…';
  try {
    const res = await fetch('api/invoices.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ items, payment_method: payment, customer_name: document.getElementById('customer-name').value.trim() })
    });
    const inv = await res.json();
    if (!res.ok) { showToast(inv.error||'Erreur', 'error'); btn.disabled=false; renderCart(); return; }
    inv.items.forEach(item => { const p=products.find(p=>p.id===item.product_id); if(p) p.stock-=item.quantity; });
    cart = {};
    document.getElementById('customer-name').value = '';
    document.getElementById('cash-received').value = '';
    renderCart(); renderProducts(); showSuccess(inv);
    playBeep('success');
  } catch { showToast('Erreur réseau', 'error'); btn.disabled=false; renderCart(); }
}

// ── Modal succès ──────────────────────────────────────────────────────────────
function showSuccess(inv) {
  document.getElementById('modal-inv-num').textContent = 'Facture N° ' + inv.invoice_number;
  document.getElementById('modal-total').textContent   = fmt(inv.total);
  document.getElementById('modal-details').innerHTML   = `
    <div class="flex justify-between"><span class="text-slate-500">Date</span><span class="font-medium">${new Date(inv.date).toLocaleString('fr-FR')}</span></div>
    <div class="flex justify-between"><span class="text-slate-500">Caissier</span><span class="font-medium">${inv.cashier_name}</span></div>
    ${inv.customer_name?`<div class="flex justify-between"><span class="text-slate-500">Client</span><span class="font-medium">${inv.customer_name}</span></div>`:''}
    <div class="flex justify-between"><span class="text-slate-500">Paiement</span><span class="font-medium capitalize">${inv.payment_method}</span></div>`;
  document.getElementById('modal-items').innerHTML =
    '<span class="font-bold block text-slate-700 mb-1">Articles :</span>' +
    inv.items.map(i=>`<div class="flex justify-between"><span>${i.name} (×${i.quantity})</span><span class="font-semibold">${fmt(i.total)}</span></div>`).join('');
  document.getElementById('success-modal').classList.remove('hidden');
}
function closeSuccess() { document.getElementById('success-modal').classList.add('hidden'); }

// ═══════════════════════════════════════════════════════════════════════════
// ── SCANNER — logique complète ────────────────────────────────────────────
// ═══════════════════════════════════════════════════════════════════════════

function openScanner() {
  document.getElementById('scanner-modal').classList.remove('hidden');
  resetScannerUI();
  initScanner();
}

function closeScanner() {
  stopScanner();
  document.getElementById('scanner-modal').classList.add('hidden');
}

function retryCamera() {
  resetScannerUI();
  initScanner();
}

function resetScannerUI() {
  document.getElementById('scanner-error').classList.add('hidden');
  document.getElementById('scanner-reticle').classList.add('hidden');
  document.getElementById('camera-toolbar').classList.add('hidden');
  document.getElementById('scan-loading').classList.remove('hidden');
  document.getElementById('scan-loading-text').textContent = 'Démarrage de la caméra…';
  setScanStatus('Centrez le code-barres dans le cadre');
  document.getElementById('engine-badge').classList.add('hidden');
}

// ── Initialisation : choisit le bon moteur ────────────────────────────────
async function initScanner() {
  await enumerateCameras();

  if ('BarcodeDetector' in window) {
    await startNativeScanner();
  } else {
    setLoadingText('Chargement de Quagga2…');
    loadQuagga2(() => startQuagga2());
  }
}

// ── Enumération caméras ───────────────────────────────────────────────────
async function enumerateCameras() {
  try {
    // Il faut d'abord demander la permission pour obtenir les labels
    await navigator.mediaDevices.getUserMedia({ video: true }).then(s => s.getTracks().forEach(t => t.stop())).catch(() => {});
    const devices = await navigator.mediaDevices.enumerateDevices();
    cameras = devices.filter(d => d.kind === 'videoinput');
    // Préférer caméra arrière
    const back = cameras.find(c => /back|rear|environment/i.test(c.label));
    selectedCamId = back ? back.deviceId : cameras[0]?.deviceId || null;
    buildCameraSelect();
  } catch { cameras = []; selectedCamId = null; }
}

function buildCameraSelect() {
  const sel = document.getElementById('camera-select');
  sel.innerHTML = cameras.map(c => `
    <option value="${c.deviceId}" ${c.deviceId===selectedCamId?'selected':''}>
      ${c.label || 'Caméra ' + (cameras.indexOf(c)+1)}
    </option>`).join('');
  sel.onchange = () => { selectedCamId = sel.value; retryCamera(); };
}

// ── Moteur 1 : BarcodeDetector (natif Chrome/Edge) ────────────────────────
async function startNativeScanner() {
  scanMode = 'native';
  const video = document.getElementById('scanner-video');

  try {
    const constraints = {
      video: {
        deviceId      : selectedCamId ? { exact: selectedCamId } : undefined,
        facingMode    : selectedCamId ? undefined : { ideal: 'environment' },
        width         : { ideal: 1920 },
        height        : { ideal: 1080 },
        focusMode     : 'continuous',
        exposureMode  : 'continuous',
      }
    };

    scanStream = await navigator.mediaDevices.getUserMedia(constraints);
    video.srcObject = scanStream;
    video.classList.remove('hidden');
    document.getElementById('quagga-zone').classList.add('hidden');
    await video.play();

    // Torch disponible ?
    torchTrack = scanStream.getVideoTracks()[0];
    const caps  = torchTrack?.getCapabilities?.() || {};
    document.getElementById('torch-btn').classList.toggle('hidden', !caps.torch);

    setEngineLabel('Natif');
    showScannerReady();

    // Obtenir les formats supportés dynamiquement
    let formats = ['ean_13','ean_8','upc_a','upc_e','code_128','code_39','code_93','itf'];
    try {
      const supported = await BarcodeDetector.getSupportedFormats();
      formats = supported;
    } catch(_) {}

    const detector = new BarcodeDetector({ formats });

    scanInterval = setInterval(async () => {
      if (video.readyState < 2 || video.paused) return;
      try {
        const results = await detector.detect(video);
        if (results.length) handleDetected(results[0].rawValue);
      } catch(_) {}
    }, 150);  // 150ms = ~6fps detection

  } catch(err) {
    showScanError(formatCameraError(err));
  }
}

// ── Moteur 2 : Quagga2 (fallback universel) ───────────────────────────────
function loadQuagga2(cb) {
  if (window.Quagga) { cb(); return; }
  const s   = document.createElement('script');
  s.src     = 'https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js';
  s.onload  = cb;
  s.onerror = () => showScanError('Impossible de charger Quagga2. Vérifiez votre connexion internet.');
  document.head.appendChild(s);
}

function startQuagga2() {
  scanMode     = 'quagga';
  quaggaActive = false;

  const video   = document.getElementById('scanner-video');
  const zone    = document.getElementById('quagga-zone');
  video.classList.add('hidden');
  zone.classList.remove('hidden');
  zone.innerHTML = '';  // reset au cas d'un second lancement

  const camConstraint = selectedCamId
    ? { deviceId: { exact: selectedCamId } }
    : { facingMode: 'environment' };

  Quagga.init({
    inputStream: {
      type       : 'LiveStream',
      target     : zone,
      constraints: { ...camConstraint, width: { ideal: 1280 }, height: { ideal: 720 } },
    },
    decoder: {
      readers : [
        'ean_reader',
        'ean_8_reader',
        'upc_reader',
        'upc_e_reader',
        'code_128_reader',
        'code_39_reader',
        'code_93_reader',
        'i2of5_reader',
      ],
      multiple: false,
    },
    locator: {
      patchSize  : 'medium',
      halfSample : true,
    },
    numOfWorkers : Math.min(navigator.hardwareConcurrency || 2, 4),
    locate       : true,
  }, function(err) {
    if (err) { showScanError('Quagga2 : ' + err); return; }
    quaggaActive = true;
    Quagga.start();
    setEngineLabel('Quagga2');
    showScannerReady();
  });

  // ── Filtre de confiance ───────────────────────────────────────────────
  Quagga.onDetected(function(result) {
    if (!quaggaActive) return;
    const { code, decodedCodes } = result.codeResult;
    if (!code) return;

    // Calcul taux d'erreur moyen sur les caractères décodés
    const errs = decodedCodes
      .filter(c => typeof c.error === 'number')
      .map(c => c.error);
    const avgErr = errs.length ? errs.reduce((s,e) => s+e, 0) / errs.length : 0;

    if (avgErr > 0.20) return;  // > 20% d'erreur → on rejette
    handleDetected(code);
  });
}

// ── Arrêt propre ──────────────────────────────────────────────────────────
function stopScanner() {
  // Stop BarcodeDetector loop
  if (scanInterval) { clearInterval(scanInterval); scanInterval = null; }

  // Stop stream natif
  if (scanStream) {
    if (torchOn) toggleTorch();
    scanStream.getTracks().forEach(t => t.stop());
    scanStream = null;
    torchTrack = null;
  }

  // Stop Quagga2
  if (quaggaActive) {
    try { Quagga.stop(); Quagga.offDetected(); } catch(_) {}
    quaggaActive = false;
  }

  // Reset DOM
  const video = document.getElementById('scanner-video');
  video.srcObject = null;
  video.classList.remove('hidden');
  document.getElementById('quagga-zone').innerHTML = '';
  document.getElementById('quagga-zone').classList.add('hidden');
  scanMode  = null;
  torchOn   = false;
  lastCode  = '';
}

// ── Callback détection ────────────────────────────────────────────────────
function handleDetected(code) {
  const now = Date.now();
  if (code === lastCode && now - lastCodeTime < COOLDOWN) return;
  lastCode     = code;
  lastCodeTime = now;

  // Feedback
  flashDetected();
  playBeep('scan');
  setScanStatus('✓ Code détecté : ' + code);

  const prod = products.find(p => p.barcode === code);

  if (!prod) {
    showToast('Code inconnu : ' + code, 'warning');
    playBeep('error');
    setScanStatus('Code non trouvé : ' + code);
    return;
  }
  if (prod.stock <= 0) {
    showToast(`"${prod.name}" — rupture de stock`, 'warning');
    playBeep('error');
    return;
  }

  addToCart(prod.id);
  showToast(`✓ ${prod.name} ajouté au panier`, 'success');

  if (!multiScan) {
    // Délai court pour que l'utilisateur voie le flash avant fermeture
    setTimeout(closeScanner, 300);
  } else {
    // En mode multi-scan, reset du statut après 1.5s
    setTimeout(() => setScanStatus('Centrez le code-barres dans le cadre'), 1500);
  }
}

// ── Torch ─────────────────────────────────────────────────────────────────
async function toggleTorch() {
  if (!torchTrack) return;
  torchOn = !torchOn;
  try {
    await torchTrack.applyConstraints({ advanced: [{ torch: torchOn }] });
    const btn = document.getElementById('torch-btn');
    btn.classList.toggle('text-amber-400', torchOn);
    btn.classList.toggle('bg-amber-500/20', torchOn);
    btn.classList.toggle('border-amber-500/50', torchOn);
    btn.classList.toggle('text-slate-400', !torchOn);
    btn.classList.toggle('bg-slate-800', !torchOn);
    btn.classList.toggle('border-slate-700', !torchOn);
  } catch { torchOn = !torchOn; }
}

// ── Multi-scan toggle ─────────────────────────────────────────────────────
function toggleMultiScan() {
  multiScan = !multiScan;
  const btn = document.getElementById('multiscan-btn');
  btn.classList.toggle('bg-indigo-500/20', multiScan);
  btn.classList.toggle('border-indigo-500', multiScan);
  btn.classList.toggle('text-indigo-300', multiScan);
  btn.classList.toggle('bg-slate-800', !multiScan);
  btn.classList.toggle('border-slate-700', !multiScan);
  btn.classList.toggle('text-slate-400', !multiScan);
}

// ── Saisie manuelle ───────────────────────────────────────────────────────
function manualScan() {
  const input = document.getElementById('manual-barcode');
  const val   = input.value.trim();
  if (!val) return;
  input.value = '';
  handleDetected(val);
  if (!multiScan) document.getElementById('scanner-modal').classList.add('hidden');
}

// ── Simulateur ────────────────────────────────────────────────────────────
function renderSimButtons() {
  document.getElementById('sim-buttons').innerHTML = products.map(p => `
    <button onclick="handleDetected('${p.barcode}')"
      class="px-2 py-1 bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white text-xs rounded transition-all flex flex-col items-start border border-slate-700 leading-tight">
      <span class="font-semibold">${p.name}</span>
      <span class="opacity-50 text-[9px] font-mono">${p.barcode}</span>
    </button>`).join('');
}

// ── UI helpers ────────────────────────────────────────────────────────────
function showScannerReady() {
  document.getElementById('scan-loading').classList.add('hidden');
  document.getElementById('scanner-error').classList.add('hidden');
  document.getElementById('scanner-reticle').classList.remove('hidden');
  document.getElementById('camera-toolbar').classList.remove('hidden');
}

function showScanError(msg) {
  document.getElementById('scan-loading').classList.add('hidden');
  document.getElementById('scanner-reticle').classList.add('hidden');
  document.getElementById('scanner-error-msg').textContent = msg;
  document.getElementById('scanner-error').classList.remove('hidden');
}

function setScanStatus(msg) {
  document.getElementById('scan-status').textContent = msg;
}

function setLoadingText(msg) {
  document.getElementById('scan-loading-text').textContent = msg;
  document.getElementById('scan-loading').classList.remove('hidden');
}

function setEngineLabel(label) {
  const el = document.getElementById('engine-badge');
  el.textContent = label;
  el.classList.remove('hidden');
}

function formatCameraError(err) {
  if (err.name === 'NotAllowedError')    return 'Permission refusée. Autorisez l\'accès à la caméra dans les paramètres du navigateur.';
  if (err.name === 'NotFoundError')      return 'Aucune caméra détectée sur cet appareil.';
  if (err.name === 'NotReadableError')   return 'La caméra est déjà utilisée par une autre application.';
  if (err.name === 'OverconstrainedError') return 'Caméra sélectionnée non disponible.';
  return 'Erreur caméra : ' + (err.message || err);
}

// ── Feedback visuel ───────────────────────────────────────────────────────
function flashDetected() {
  const el = document.getElementById('scan-flash');
  el.style.opacity = '1';
  setTimeout(() => { el.style.opacity = '0'; }, 200);
}

// ── Toast notifications ───────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  const colors = {
    success : 'bg-emerald-900 border-emerald-700 text-emerald-200',
    warning : 'bg-amber-900 border-amber-700 text-amber-200',
    error   : 'bg-red-900 border-red-700 text-red-200',
  };
  const icons = {
    success : '✓',
    warning : '⚠',
    error   : '✕',
  };
  const el = document.createElement('div');
  el.className = `toast pointer-events-auto flex items-center gap-2 px-4 py-2.5 rounded-xl border shadow-xl text-sm font-semibold ${colors[type]||colors.success}`;
  el.innerHTML = `<span>${icons[type]||icons.success}</span><span>${msg}</span>`;
  document.getElementById('toast-container').prepend(el);
  setTimeout(() => {
    el.classList.add('hiding');
    setTimeout(() => el.remove(), 400);
  }, 2200);
}

// ── Son (AudioContext) ────────────────────────────────────────────────────
function playBeep(type = 'scan') {
  try {
    const ctx  = new (window.AudioContext || window.webkitAudioContext)();
    const osc  = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);

    if (type === 'scan') {
      osc.frequency.value = 1800;
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.25, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      osc.start(); osc.stop(ctx.currentTime + 0.12);
    } else if (type === 'error') {
      osc.frequency.value = 400;
      osc.type = 'sawtooth';
      gain.gain.setValueAtTime(0.2, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      osc.start(); osc.stop(ctx.currentTime + 0.2);
    } else if (type === 'success') {
      // Double bip montant
      osc.frequency.setValueAtTime(880, ctx.currentTime);
      osc.frequency.setValueAtTime(1320, ctx.currentTime + 0.1);
      osc.type = 'sine';
      gain.gain.setValueAtTime(0.2, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
      osc.start(); osc.stop(ctx.currentTime + 0.25);
    }
  } catch(_) {}  // Silently ignore si AudioContext non supporté
}

// ── Format monnaie ────────────────────────────────────────────────────────
function fmt(n) {
  return Number(n).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

// ── Init ──────────────────────────────────────────────────────────────────
setPayment('cash');
loadProducts();
</script>
