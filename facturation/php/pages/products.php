<!-- ╔══════════════════════════════════════════════════════════════════════╗ -->
<!-- ║  Gestion des produits                                                ║ -->
<!-- ╚══════════════════════════════════════════════════════════════════════╝ -->

<div class="p-6">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h2 class="text-2xl font-black text-white">Gestion des Produits</h2>
      <p class="text-slate-400 text-sm mt-0.5">Gérez votre catalogue et vos stocks</p>
    </div>
    <div class="flex items-center gap-3">
      <span id="low-stock-badge" class="hidden items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-lg text-xs font-semibold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span id="low-stock-text"></span>
      </span>
      <button onclick="openProductModal()"
        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajouter
      </button>
    </div>
  </div>

  <!-- Barre de recherche et filtre -->
  <div class="flex flex-col sm:flex-row gap-3 mb-5">
    <div class="relative flex-1">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input id="prod-search" type="text" placeholder="Rechercher…"
        class="w-full pl-10 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
    </div>
    <select id="prod-cat-filter" class="bg-slate-900 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
      <option value="">Toutes catégories</option>
    </select>
  </div>

  <!-- Tableau -->
  <div class="overflow-x-auto rounded-xl border border-slate-800">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-900 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
          <th class="px-4 py-3">Produit</th>
          <th class="px-4 py-3">Catégorie</th>
          <th class="px-4 py-3">Code-barres</th>
          <th class="px-4 py-3 text-right">Prix HT</th>
          <th class="px-4 py-3 text-right">Stock</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody id="products-tbody" class="divide-y divide-slate-800/50"></tbody>
    </table>
    <div id="no-products" class="hidden text-center text-slate-500 py-10 text-sm">Aucun produit trouvé</div>
  </div>
</div>

<!-- ── MODAL PRODUIT ─────────────────────────────────────────────────────── -->
<div id="product-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-slate-900 rounded-2xl w-full max-w-md border border-slate-800 shadow-2xl">
    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
      <h3 id="modal-title" class="text-white font-bold text-lg">Ajouter un produit</h3>
      <button onclick="closeProductModal()" class="text-slate-400 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="product-form" class="p-5 space-y-4">
      <input type="hidden" id="edit-id">
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nom *</label>
          <input id="f-name" type="text" required placeholder="Nom du produit"
            class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Prix HT (€) *</label>
          <input id="f-price" type="number" step="0.01" min="0" required placeholder="0,00"
            class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Stock</label>
          <input id="f-stock" type="number" min="0" placeholder="0"
            class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Code-barres</label>
          <div class="flex gap-2">
            <input id="f-barcode" type="text" placeholder="EAN-13…"
              class="flex-1 bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
            <!-- Bouton scanner avec caméra -->
            <button type="button" onclick="openProductScanner()"
              class="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition-all">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
              </svg>
              Scanner
            </button>
            <!-- Bouton générer aléatoire -->
            <button type="button" onclick="genBarcode()"
              class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg text-xs font-semibold transition-all">
              Générer
            </button>
          </div>
          <p id="barcode-hint" class="hidden text-[10px] mt-1.5 font-medium text-amber-400"></p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Alerte stock min.</label>
          <input id="f-min" type="number" min="0" placeholder="5"
            class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Catégorie</label>
          <input id="f-cat" type="text" placeholder="Alimentation, Boissons…"
            class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
        </div>
      </div>
      <div id="form-error" class="hidden p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-xs"></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeProductModal()"
          class="flex-1 py-2.5 border border-slate-700 text-slate-400 rounded-xl font-semibold text-sm hover:bg-slate-800 transition-all">
          Annuler
        </button>
        <button type="submit"
          class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition-all">
          Enregistrer
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ── MODAL SCANNER PRODUIT ──────────────────────────────────────────────── -->
<div id="pscan-modal" class="hidden fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center z-[60] p-4">
  <div class="bg-slate-900 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-800">

    <!-- En-tête -->
    <div class="px-4 py-3 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        <h3 class="text-white font-bold">Scanner un code-barres</h3>
        <span id="pengine-badge" class="hidden text-[10px] px-1.5 py-0.5 rounded-full font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"></span>
      </div>
      <button onclick="closePScanner()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Zone caméra -->
    <div id="pcamera-area" class="relative bg-black overflow-hidden" style="aspect-ratio:16/9">
      <video id="pscanner-video" class="w-full h-full object-cover" playsinline muted></video>
      <div id="pquagga-zone" class="hidden absolute inset-0 overflow-hidden"></div>
      <div id="pscan-flash" class="p-scan-flash"></div>

      <!-- Viseur -->
      <div id="pscanner-reticle" class="hidden absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
        <div class="relative w-64 h-32">
          <div class="absolute top-0 left-0 w-5 h-5 border-t-2 border-l-2 border-indigo-400 rounded-tl-sm"></div>
          <div class="absolute top-0 right-0 w-5 h-5 border-t-2 border-r-2 border-indigo-400 rounded-tr-sm"></div>
          <div class="absolute bottom-0 left-0 w-5 h-5 border-b-2 border-l-2 border-indigo-400 rounded-bl-sm"></div>
          <div class="absolute bottom-0 right-0 w-5 h-5 border-b-2 border-r-2 border-indigo-400 rounded-br-sm"></div>
          <div class="scan-line-p"></div>
          <div class="absolute inset-0 bg-indigo-500/5 rounded-sm"></div>
        </div>
        <p id="pscan-status" class="text-white text-xs mt-3 bg-black/70 px-3 py-1 rounded-full">
          Centrez le code-barres dans le cadre
        </p>
      </div>

      <!-- Chargement -->
      <div id="pscan-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950/90">
        <svg class="w-8 h-8 text-indigo-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p id="pscan-loading-text" class="text-slate-300 text-sm">Démarrage de la caméra…</p>
      </div>

      <!-- Erreur -->
      <div id="pscanner-error" class="hidden absolute inset-0 flex flex-col items-center justify-center p-6 bg-slate-950">
        <svg class="w-10 h-10 text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 10l4.553-2.069A1 1 0 0121 8.878V15.12a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
        </svg>
        <p id="pscanner-error-msg" class="text-red-400 text-sm text-center mb-3"></p>
        <button onclick="retryPScanner()"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-all">
          Réessayer
        </button>
      </div>
    </div>

    <!-- Sélection caméra -->
    <div id="pcamera-toolbar" class="hidden px-4 py-2 bg-slate-950/80 border-t border-slate-800/50 flex items-center gap-2">
      <select id="pcamera-select"
        class="flex-1 bg-slate-800 border border-slate-700 text-slate-300 text-xs rounded-lg px-2 py-1.5 focus:outline-none">
      </select>
      <button id="ptorch-btn" onclick="togglePTorch()"
        class="hidden p-1.5 bg-slate-800 hover:bg-amber-500/20 text-slate-400 hover:text-amber-400 rounded-lg border border-slate-700 transition-all" title="Lampe torche">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
      </button>
    </div>

    <!-- Résultat détecté -->
    <div id="pscan-result" class="hidden px-4 py-3 bg-emerald-900/40 border-t border-emerald-700/40 flex items-center justify-between gap-3">
      <div class="flex items-center gap-2 min-w-0">
        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-mono font-bold text-emerald-300 truncate" id="pscan-result-code"></span>
      </div>
      <button onclick="confirmPScan()"
        class="shrink-0 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition-all active:scale-95">
        Utiliser ce code
      </button>
    </div>

    <!-- Saisie manuelle -->
    <div class="p-4 bg-slate-950 border-t border-slate-800 flex gap-2">
      <input id="pmanual-barcode" type="text" inputmode="numeric" placeholder="Saisir un code-barres manuellement…"
        class="flex-1 bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
        onkeydown="if(event.key==='Enter'){ pManualScan(); event.preventDefault(); }">
      <button onclick="pManualScan()"
        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-all active:scale-95">OK</button>
    </div>
  </div>
</div>

<!-- ── MODAL SUPPRESSION ─────────────────────────────────────────────────── -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-slate-900 rounded-2xl w-full max-w-sm border border-slate-800 shadow-2xl p-6 text-center">
    <div class="w-12 h-12 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
      </svg>
    </div>
    <h3 class="text-white font-bold text-lg mb-2">Confirmer la suppression</h3>
    <p class="text-slate-400 text-sm mb-6">Cette action est irréversible.</p>
    <div class="flex gap-3">
      <button onclick="closeDelete()" class="flex-1 py-2.5 border border-slate-700 text-slate-400 rounded-xl font-semibold text-sm hover:bg-slate-800 transition-all">Annuler</button>
      <button id="confirm-delete-btn" onclick="confirmDelete()" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold text-sm transition-all">Supprimer</button>
    </div>
  </div>
</div>

<script>
let products    = [];
let deleteId    = null;

// ── Chargement ────────────────────────────────────────────────────────────────
async function loadProducts() {
  const res = await fetch('api/products.php');
  products  = await res.json();
  renderProducts();
  buildCatFilter();
}

function buildCatFilter() {
  const cats = [...new Set(products.map(p => p.category))].sort();
  const sel  = document.getElementById('prod-cat-filter');
  const cur  = sel.value;
  sel.innerHTML = '<option value="">Toutes catégories</option>' +
    cats.map(c => `<option value="${c}" ${c===cur?'selected':''}>${c}</option>`).join('');
}

// ── Rendu tableau ─────────────────────────────────────────────────────────────
function renderProducts() {
  const search = document.getElementById('prod-search').value.toLowerCase();
  const cat    = document.getElementById('prod-cat-filter').value;

  const filtered = products.filter(p => {
    const matchSearch = p.name.toLowerCase().includes(search) || (p.barcode||'').includes(search);
    const matchCat    = !cat || p.category === cat;
    return matchSearch && matchCat;
  });

  const low = products.filter(p => p.stock > 0 && p.stock <= p.min_stock_alert).length;
  const lowBadge = document.getElementById('low-stock-badge');
  if (low > 0) {
    lowBadge.classList.remove('hidden');
    lowBadge.classList.add('flex');
    document.getElementById('low-stock-text').textContent = low + ' produit(s) en stock bas';
  } else {
    lowBadge.classList.add('hidden');
  }

  const tbody   = document.getElementById('products-tbody');
  const noProd  = document.getElementById('no-products');

  if (filtered.length === 0) {
    tbody.innerHTML = '';
    noProd.classList.remove('hidden');
    return;
  }
  noProd.classList.add('hidden');

  tbody.innerHTML = filtered.map(p => {
    const out   = p.stock <= 0;
    const low   = p.stock > 0 && p.stock <= p.min_stock_alert;
    const badge = out  ? '<span class="px-1.5 py-0.5 bg-red-500/10 text-red-400 rounded text-[10px] font-semibold">Épuisé</span>'
                : low  ? '<span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-400 rounded text-[10px] font-semibold">Stock bas</span>'
                       : '';
    return `
    <tr class="bg-slate-900/30 hover:bg-slate-800/30 transition-colors">
      <td class="px-4 py-3">
        <span class="font-semibold text-slate-200">${p.name}</span>
        ${badge}
      </td>
      <td class="px-4 py-3 text-slate-400">${p.category}</td>
      <td class="px-4 py-3 font-mono text-slate-400 text-xs">${p.barcode || '—'}</td>
      <td class="px-4 py-3 text-right font-semibold text-slate-200">${fmt(p.price)}</td>
      <td class="px-4 py-3 text-right">
        <span class="${out ? 'text-red-400' : low ? 'text-amber-400' : 'text-slate-200'} font-semibold">${p.stock}</span>
      </td>
      <td class="px-4 py-3 text-right">
        <div class="flex justify-end gap-2">
          <button onclick="openProductModal('${p.id}')"
            class="p-1.5 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition-all" title="Modifier">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          <button onclick="openDelete('${p.id}')"
            class="p-1.5 text-slate-400 hover:text-red-400 rounded-lg hover:bg-slate-800 transition-all" title="Supprimer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

document.getElementById('prod-search').addEventListener('input', renderProducts);
document.getElementById('prod-cat-filter').addEventListener('change', renderProducts);

// ── Modal produit ─────────────────────────────────────────────────────────────
function openProductModal(id = null) {
  document.getElementById('form-error').classList.add('hidden');
  if (id) {
    const p = products.find(p => p.id === id);
    document.getElementById('modal-title').textContent = 'Modifier le produit';
    document.getElementById('edit-id').value     = p.id;
    document.getElementById('f-name').value      = p.name;
    document.getElementById('f-price').value     = p.price;
    document.getElementById('f-stock').value     = p.stock;
    document.getElementById('f-barcode').value   = p.barcode || '';
    document.getElementById('f-cat').value       = p.category;
    document.getElementById('f-min').value       = p.min_stock_alert;
  } else {
    document.getElementById('modal-title').textContent = 'Ajouter un produit';
    document.getElementById('product-form').reset();
    document.getElementById('edit-id').value = '';
    genBarcode();
  }
  document.getElementById('product-modal').classList.remove('hidden');
}

function closeProductModal() {
  document.getElementById('product-modal').classList.add('hidden');
}

function genBarcode() {
  document.getElementById('f-barcode').value = '300' + Math.floor(1e9 + Math.random() * 9e9).toString();
}

document.getElementById('product-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id      = document.getElementById('edit-id').value;
  const errEl   = document.getElementById('form-error');
  errEl.classList.add('hidden');

  const body = {
    name            : document.getElementById('f-name').value.trim(),
    price           : parseFloat(document.getElementById('f-price').value),
    stock           : parseInt(document.getElementById('f-stock').value) || 0,
    barcode         : document.getElementById('f-barcode').value.trim(),
    category        : document.getElementById('f-cat').value.trim() || 'Général',
    min_stock_alert : parseInt(document.getElementById('f-min').value) || 5,
  };

  const method = id ? 'PUT' : 'POST';
  const url    = id ? `api/products.php?id=${id}` : 'api/products.php';

  const res  = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
  const data = await res.json();

  if (!res.ok) { errEl.textContent = data.error; errEl.classList.remove('hidden'); return; }

  if (id) {
    const idx = products.findIndex(p => p.id === id);
    if (idx !== -1) products[idx] = data;
  } else {
    products.push(data);
  }
  buildCatFilter();
  renderProducts();
  closeProductModal();
});

// ── Suppression ───────────────────────────────────────────────────────────────
function openDelete(id) {
  deleteId = id;
  document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDelete() {
  deleteId = null;
  document.getElementById('delete-modal').classList.add('hidden');
}

async function confirmDelete() {
  if (!deleteId) return;
  const res = await fetch(`api/products.php?id=${deleteId}`, { method: 'DELETE' });
  if (res.ok) {
    products = products.filter(p => p.id !== deleteId);
    renderProducts();
    buildCatFilter();
  }
  closeDelete();
}

function fmt(n) {
  return Number(n).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

loadProducts();

// ═══════════════════════════════════════════════════════════════════════════
// ── SCANNER CODE-BARRES pour formulaire produit ───────────────────────────
// ═══════════════════════════════════════════════════════════════════════════

let pScanMode     = null;
let pScanStream   = null;
let pScanInterval = null;
let pQuaggaActive = false;
let pTorchOn      = false;
let pTorchTrack   = null;
let pLastCode     = '';
let pLastCodeTime = 0;
let pDetectedCode = '';   // code en attente de confirmation
const P_COOLDOWN  = 1500;

// ── Ouverture / Fermeture ─────────────────────────────────────────────────
function openProductScanner() {
  // Cacher le hint si visible
  document.getElementById('barcode-hint').classList.add('hidden');
  // Reset UI
  document.getElementById('pscanner-error').classList.add('hidden');
  document.getElementById('pscanner-reticle').classList.add('hidden');
  document.getElementById('pcamera-toolbar').classList.add('hidden');
  document.getElementById('pscan-result').classList.add('hidden');
  document.getElementById('pscan-loading').classList.remove('hidden');
  document.getElementById('pscan-loading-text').textContent = 'Démarrage de la caméra…';
  document.getElementById('pengine-badge').classList.add('hidden');
  pDetectedCode = '';
  document.getElementById('pscan-modal').classList.remove('hidden');
  pInitScanner();
}

function closePScanner() {
  pStopScanner();
  document.getElementById('pscan-modal').classList.add('hidden');
}

function retryPScanner() {
  document.getElementById('pscanner-error').classList.add('hidden');
  document.getElementById('pscan-result').classList.add('hidden');
  document.getElementById('pscan-loading').classList.remove('hidden');
  document.getElementById('pscan-loading-text').textContent = 'Démarrage de la caméra…';
  pDetectedCode = '';
  pInitScanner();
}

// ── Init : choisit le bon moteur ──────────────────────────────────────────
async function pInitScanner() {
  await pEnumerateCameras();
  if ('BarcodeDetector' in window) {
    await pStartNative();
  } else {
    document.getElementById('pscan-loading-text').textContent = 'Chargement de Quagga2…';
    pLoadQuagga2(() => pStartQuagga2());
  }
}

// ── Enumération caméras ───────────────────────────────────────────────────
async function pEnumerateCameras() {
  try {
    await navigator.mediaDevices.getUserMedia({ video: true })
      .then(s => s.getTracks().forEach(t => t.stop())).catch(() => {});
    const devices = await navigator.mediaDevices.enumerateDevices();
    const cams    = devices.filter(d => d.kind === 'videoinput');
    const sel     = document.getElementById('pcamera-select');
    const back    = cams.find(c => /back|rear|environment/i.test(c.label));
    const chosen  = back ? back.deviceId : cams[0]?.deviceId || null;
    sel.innerHTML = cams.map(c => `
      <option value="${c.deviceId}" ${c.deviceId===chosen?'selected':''}>
        ${c.label || 'Caméra '+(cams.indexOf(c)+1)}
      </option>`).join('');
    sel.onchange = () => retryPScanner();
    // stocker l'id choisi pour le retrouver
    sel.dataset.chosen = chosen || '';
  } catch { /* pas de caméra, géré dans startNative */ }
}

function pGetSelectedCamId() {
  const sel = document.getElementById('pcamera-select');
  return sel.value || null;
}

// ── Moteur 1 : BarcodeDetector natif ─────────────────────────────────────
async function pStartNative() {
  pScanMode = 'native';
  const video = document.getElementById('pscanner-video');
  const camId = pGetSelectedCamId();
  try {
    const constraints = {
      video: {
        deviceId   : camId ? { exact: camId } : undefined,
        facingMode : camId ? undefined : { ideal: 'environment' },
        width      : { ideal: 1280 }, height: { ideal: 720 },
      }
    };
    pScanStream = await navigator.mediaDevices.getUserMedia(constraints);
    video.srcObject = pScanStream;
    video.classList.remove('hidden');
    document.getElementById('pquagga-zone').classList.add('hidden');
    await video.play();

    pTorchTrack = pScanStream.getVideoTracks()[0];
    const caps  = pTorchTrack?.getCapabilities?.() || {};
    document.getElementById('ptorch-btn').classList.toggle('hidden', !caps.torch);

    pSetEngineLabel('Natif');
    pShowReady();

    let formats = ['ean_13','ean_8','upc_a','upc_e','code_128','code_39','code_93','itf'];
    try { formats = await BarcodeDetector.getSupportedFormats(); } catch(_) {}
    const detector = new BarcodeDetector({ formats });

    pScanInterval = setInterval(async () => {
      if (video.readyState < 2 || video.paused) return;
      try {
        const results = await detector.detect(video);
        if (results.length) pHandleDetected(results[0].rawValue);
      } catch(_) {}
    }, 150);
  } catch(err) {
    pShowError(pFmtCamError(err));
  }
}

// ── Moteur 2 : Quagga2 ────────────────────────────────────────────────────
function pLoadQuagga2(cb) {
  if (window.Quagga) { cb(); return; }
  const s   = document.createElement('script');
  s.src     = 'https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js';
  s.onload  = cb;
  s.onerror = () => pShowError('Impossible de charger Quagga2.');
  document.head.appendChild(s);
}

function pStartQuagga2() {
  pScanMode     = 'quagga';
  pQuaggaActive = false;
  const video = document.getElementById('pscanner-video');
  const zone  = document.getElementById('pquagga-zone');
  video.classList.add('hidden');
  zone.classList.remove('hidden');
  zone.innerHTML = '';
  const camId = pGetSelectedCamId();
  const camC  = camId ? { deviceId: { exact: camId } } : { facingMode: 'environment' };

  Quagga.init({
    inputStream: {
      type: 'LiveStream', target: zone,
      constraints: { ...camC, width: { ideal: 1280 }, height: { ideal: 720 } },
    },
    decoder: {
      readers: ['ean_reader','ean_8_reader','upc_reader','upc_e_reader',
                'code_128_reader','code_39_reader','code_93_reader','i2of5_reader'],
      multiple: false,
    },
    locator: { patchSize: 'medium', halfSample: true },
    numOfWorkers: Math.min(navigator.hardwareConcurrency || 2, 4),
    locate: true,
  }, err => {
    if (err) { pShowError('Quagga2 : ' + err); return; }
    pQuaggaActive = true;
    Quagga.start();
    pSetEngineLabel('Quagga2');
    pShowReady();
  });

  Quagga.onDetected(result => {
    if (!pQuaggaActive) return;
    const { code, decodedCodes } = result.codeResult;
    if (!code) return;
    const errs   = decodedCodes.filter(c => typeof c.error === 'number').map(c => c.error);
    const avgErr = errs.length ? errs.reduce((s,e) => s+e, 0) / errs.length : 0;
    if (avgErr > 0.20) return;
    pHandleDetected(code);
  });
}

// ── Arrêt propre ──────────────────────────────────────────────────────────
function pStopScanner() {
  if (pScanInterval)  { clearInterval(pScanInterval); pScanInterval = null; }
  if (pScanStream)    { pScanStream.getTracks().forEach(t => t.stop()); pScanStream = null; pTorchTrack = null; }
  if (pQuaggaActive)  { try { Quagga.stop(); Quagga.offDetected(); } catch(_) {} pQuaggaActive = false; }
  const video = document.getElementById('pscanner-video');
  video.srcObject = null;
  video.classList.remove('hidden');
  document.getElementById('pquagga-zone').innerHTML = '';
  document.getElementById('pquagga-zone').classList.add('hidden');
  pScanMode = null; pTorchOn = false; pLastCode = '';
}

// ── Callback détection ────────────────────────────────────────────────────
function pHandleDetected(code) {
  const now = Date.now();
  if (code === pLastCode && now - pLastCodeTime < P_COOLDOWN) return;
  pLastCode     = code;
  pLastCodeTime = now;

  // Flash visuel
  const flash = document.getElementById('pscan-flash');
  flash.style.opacity = '1';
  setTimeout(() => { flash.style.opacity = '0'; }, 200);

  // Bip
  pPlayBeep();

  // Stopper la détection en attente de confirmation
  if (pScanInterval)  { clearInterval(pScanInterval); pScanInterval = null; }
  if (pQuaggaActive)  { try { Quagga.stop(); Quagga.offDetected(); } catch(_) {} pQuaggaActive = false; }

  pDetectedCode = code;
  document.getElementById('pscan-result-code').textContent = code;
  document.getElementById('pscan-result').classList.remove('hidden');
  document.getElementById('pscanner-reticle').classList.add('hidden');
  document.getElementById('pscan-status').textContent = '✓ Code détecté !';
}

// ── Confirmation : injecter le code dans le formulaire ────────────────────
function confirmPScan() {
  if (!pDetectedCode) return;
  const code  = pDetectedCode;
  const field = document.getElementById('f-barcode');
  const hint  = document.getElementById('barcode-hint');
  const editId = document.getElementById('edit-id').value;

  field.value = code;

  // Vérifier si ce code existe déjà sur un autre produit
  const dup = products.find(p => p.barcode === code && p.id !== editId);
  if (dup) {
    hint.textContent = `⚠ Ce code est déjà utilisé par "${dup.name}"`;
    hint.classList.remove('hidden');
  } else {
    hint.textContent = '✓ Code-barres enregistré';
    hint.className   = 'text-[10px] mt-1 text-emerald-400';
    hint.classList.remove('hidden');
    setTimeout(() => hint.classList.add('hidden'), 2500);
  }

  closePScanner();
  // Remettre le focus sur le champ nom si vide
  const nameField = document.getElementById('f-name');
  if (!nameField.value) nameField.focus();
}

// ── Saisie manuelle ───────────────────────────────────────────────────────
function pManualScan() {
  const input = document.getElementById('pmanual-barcode');
  const val   = input.value.trim();
  if (!val) return;
  input.value = '';
  pHandleDetected(val);
}

// ── Torch ─────────────────────────────────────────────────────────────────
async function togglePTorch() {
  if (!pTorchTrack) return;
  pTorchOn = !pTorchOn;
  try {
    await pTorchTrack.applyConstraints({ advanced: [{ torch: pTorchOn }] });
    const btn = document.getElementById('ptorch-btn');
    btn.classList.toggle('text-amber-400', pTorchOn);
    btn.classList.toggle('bg-amber-500/20', pTorchOn);
    btn.classList.toggle('text-slate-400', !pTorchOn);
    btn.classList.toggle('bg-slate-800', !pTorchOn);
  } catch { pTorchOn = !pTorchOn; }
}

// ── UI helpers ────────────────────────────────────────────────────────────
function pShowReady() {
  document.getElementById('pscan-loading').classList.add('hidden');
  document.getElementById('pscanner-error').classList.add('hidden');
  document.getElementById('pscanner-reticle').classList.remove('hidden');
  document.getElementById('pcamera-toolbar').classList.remove('hidden');
}

function pShowError(msg) {
  document.getElementById('pscan-loading').classList.add('hidden');
  document.getElementById('pscanner-reticle').classList.add('hidden');
  document.getElementById('pscanner-error-msg').textContent = msg;
  document.getElementById('pscanner-error').classList.remove('hidden');
}

function pSetEngineLabel(label) {
  const el = document.getElementById('pengine-badge');
  el.textContent = label;
  el.classList.remove('hidden');
}

function pFmtCamError(err) {
  if (err.name === 'NotAllowedError')    return 'Permission refusée. Autorisez la caméra dans les paramètres.';
  if (err.name === 'NotFoundError')      return 'Aucune caméra détectée.';
  if (err.name === 'NotReadableError')   return 'Caméra déjà utilisée par une autre application.';
  if (err.name === 'OverconstrainedError') return 'Caméra sélectionnée non disponible.';
  return 'Erreur : ' + (err.message || err);
}

function pPlayBeep() {
  try {
    const ctx  = new (window.AudioContext || window.webkitAudioContext)();
    const osc  = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.value = 1800; osc.type = 'sine';
    gain.gain.setValueAtTime(0.25, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
    osc.start(); osc.stop(ctx.currentTime + 0.12);
  } catch(_) {}
}
</script>
