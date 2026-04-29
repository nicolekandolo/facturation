<!-- Historique des factures -->
<div class="p-6">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h2 class="text-2xl font-black text-white">Factures & Rapports</h2>
      <p class="text-slate-400 text-sm mt-0.5">Historique de toutes les ventes</p>
    </div>
    <div id="stats-bar" class="flex gap-3 flex-wrap"></div>
  </div>

  <!-- Filtres -->
  <div class="flex flex-col sm:flex-row gap-3 mb-5">
    <input id="inv-search" type="text" placeholder="Rechercher (N° facture, client, caissier)…"
      class="flex-1 bg-slate-900 border border-slate-800 text-slate-200 placeholder-slate-500 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
    <select id="inv-pm-filter" class="bg-slate-900 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
      <option value="">Tous paiements</option>
      <option value="cash">Espèces</option>
      <option value="card">Carte</option>
      <option value="mobile">Mobile</option>
    </select>
  </div>

  <!-- Tableau -->
  <div class="overflow-x-auto rounded-xl border border-slate-800">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-900 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
          <th class="px-4 py-3">N° Facture</th>
          <th class="px-4 py-3">Date</th>
          <th class="px-4 py-3">Client</th>
          <th class="px-4 py-3">Caissier</th>
          <th class="px-4 py-3">Paiement</th>
          <th class="px-4 py-3 text-right">Total TTC</th>
          <th class="px-4 py-3 text-right">Détail</th>
        </tr>
      </thead>
      <tbody id="invoices-tbody" class="divide-y divide-slate-800/50"></tbody>
    </table>
    <div id="no-invoices" class="hidden text-center text-slate-500 py-12 text-sm">Aucune facture pour l'instant</div>
  </div>
</div>

<!-- ── MODAL DÉTAIL FACTURE ──────────────────────────────────────────────── -->
<div id="inv-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden text-slate-900">
    <div class="bg-slate-900 p-4 flex justify-between items-center">
      <span id="inv-modal-num" class="text-white font-bold"></span>
      <button onclick="closeInvModal()" class="text-slate-400 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-6">
      <div id="inv-modal-meta" class="text-xs space-y-1 mb-4 text-slate-600"></div>
      <div id="inv-modal-items" class="border-t border-dashed border-slate-300 pt-3 pb-3 mb-3 text-xs space-y-1.5"></div>
      <div class="flex justify-between font-bold text-sm pt-2 border-t border-slate-200">
        <span>TOTAL TTC</span>
        <span id="inv-modal-total" class="text-indigo-600 text-base"></span>
      </div>
      <button onclick="window.print()" class="mt-4 w-full py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-all">
        Imprimer
      </button>
    </div>
  </div>
</div>

<script>
let invoices = [];

async function loadInvoices() {
  const res = await fetch('api/invoices.php');
  invoices  = await res.json();
  renderStats();
  renderInvoices();
}

function renderStats() {
  const total   = invoices.reduce((s,i) => s + i.total, 0);
  const byPM    = { cash: 0, card: 0, mobile: 0 };
  invoices.forEach(i => { if (byPM[i.payment_method] !== undefined) byPM[i.payment_method]++; });

  document.getElementById('stats-bar').innerHTML = [
    ['Factures',  invoices.length + ' total',   'bg-indigo-500/10 text-indigo-400 border-indigo-500/30'],
    ['CA Total',  fmt(total),                   'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'],
    ['Espèces',   byPM.cash + ' ventes',        'bg-slate-700 text-slate-300 border-slate-600'],
    ['Carte',     byPM.card + ' ventes',        'bg-blue-500/10 text-blue-400 border-blue-500/30'],
  ].map(([label, value, cls]) => `
    <div class="px-3 py-2 rounded-xl border text-xs font-semibold ${cls}">
      <span class="block opacity-70">${label}</span>
      <span class="text-sm font-black">${value}</span>
    </div>`).join('');
}

const pmLabels = { cash:'Espèces', card:'Carte', mobile:'Mobile' };
const pmColors = {
  cash:   'bg-slate-700 text-slate-300',
  card:   'bg-blue-500/10 text-blue-400',
  mobile: 'bg-purple-500/10 text-purple-400',
};

function renderInvoices() {
  const search = document.getElementById('inv-search').value.toLowerCase();
  const pm     = document.getElementById('inv-pm-filter').value;

  const filtered = invoices.filter(i => {
    const matchS = i.invoice_number.toLowerCase().includes(search) ||
                   (i.customer_name||'').toLowerCase().includes(search) ||
                   i.cashier_name.toLowerCase().includes(search);
    const matchP = !pm || i.payment_method === pm;
    return matchS && matchP;
  });

  const tbody = document.getElementById('invoices-tbody');
  const noEl  = document.getElementById('no-invoices');

  if (filtered.length === 0) {
    tbody.innerHTML = '';
    noEl.classList.remove('hidden');
    return;
  }
  noEl.classList.add('hidden');

  tbody.innerHTML = filtered.map((inv,idx) => `
    <tr class="bg-slate-900/30 hover:bg-slate-800/30 transition-colors">
      <td class="px-4 py-3 font-mono text-indigo-400 font-semibold text-xs">${inv.invoice_number}</td>
      <td class="px-4 py-3 text-slate-400 text-xs">${new Date(inv.date).toLocaleString('fr-FR')}</td>
      <td class="px-4 py-3 text-slate-300">${inv.customer_name || '—'}</td>
      <td class="px-4 py-3 text-slate-400 text-xs">${inv.cashier_name}</td>
      <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${pmColors[inv.payment_method]||''}">
          ${pmLabels[inv.payment_method]||inv.payment_method}
        </span>
      </td>
      <td class="px-4 py-3 text-right font-black text-slate-200">${fmt(inv.total)}</td>
      <td class="px-4 py-3 text-right">
        <button onclick="openInvModal(${idx})"
          class="p-1.5 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>
      </td>
    </tr>`).join('');
}

document.getElementById('inv-search').addEventListener('input', renderInvoices);
document.getElementById('inv-pm-filter').addEventListener('change', renderInvoices);

function openInvModal(idx) {
  const inv = invoices[idx];
  document.getElementById('inv-modal-num').textContent = inv.invoice_number;
  document.getElementById('inv-modal-total').textContent = fmt(inv.total);

  document.getElementById('inv-modal-meta').innerHTML = `
    <div class="flex justify-between"><span>Date</span><span class="font-medium text-slate-800">${new Date(inv.date).toLocaleString('fr-FR')}</span></div>
    <div class="flex justify-between"><span>Caissier</span><span class="font-medium text-slate-800">${inv.cashier_name}</span></div>
    ${inv.customer_name ? `<div class="flex justify-between"><span>Client</span><span class="font-medium text-slate-800">${inv.customer_name}</span></div>` : ''}
    <div class="flex justify-between"><span>Paiement</span><span class="font-medium text-slate-800">${pmLabels[inv.payment_method]||inv.payment_method}</span></div>
    <div class="flex justify-between"><span>Sous-total HT</span><span class="font-medium text-slate-800">${fmt(inv.subtotal)}</span></div>
    <div class="flex justify-between"><span>TVA (20%)</span><span class="font-medium text-slate-800">${fmt(inv.tax)}</span></div>`;

  document.getElementById('inv-modal-items').innerHTML =
    '<p class="font-bold text-slate-700 mb-2">Articles :</p>' +
    inv.items.map(it => `
      <div class="flex justify-between">
        <span>${it.name} (×${it.quantity})</span>
        <span class="font-semibold">${fmt(it.total)}</span>
      </div>`).join('');

  document.getElementById('inv-modal').classList.remove('hidden');
}

function closeInvModal() {
  document.getElementById('inv-modal').classList.add('hidden');
}

function fmt(n) {
  return Number(n).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
}

loadInvoices();
</script>
