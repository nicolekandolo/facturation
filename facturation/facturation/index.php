<?php

/**
 * Page principale - Facturation / POS
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/fonctions-auth.php';
require_once __DIR__ . '/includes/fonctions-produits.php';
require_once __DIR__ . '/includes/fonctions-factures.php';
require_once __DIR__ . '/includes/header.php';

$products = product_get_all();
$categories = [];

foreach ($products as $p) {
    if (!empty($p['category'])) {
        $categories[$p['category']] = true;
    }
}
$categories = array_keys($categories);
?>

<!-- Scanner Modal -->
<div id="scanner-modal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">Scanner un produit</h3>
            <button onclick="closeScanner()" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="space-y-3">
            <label class="block text-sm font-semibold text-slate-300">Code-barres</label>
            <input id="barcode-input" type="text" placeholder="Scannez le code-barres..."
                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">

            <p class="text-xs text-slate-400 mt-4">Le lecteur attend automatiquement la lecture du code-barres</p>
        </div>

        <button onclick="closeScanner()"
            class="w-full mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg font-semibold transition-all">
            Fermer
        </button>
    </div>
</div>

<!-- POS Layout -->
<div id="pos-app" class="flex flex-col lg:flex-row h-[calc(100vh-112px)] gap-4 p-4">

    <!-- Catalogue Produits -->
    <div class="flex-1 flex flex-col bg-slate-900/50 rounded-xl border border-slate-800 p-4 min-h-0">
        <div class="flex gap-3 mb-4 flex-wrap">
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input id="search" type="text" placeholder="Rechercher par nom ou code-barres…"
                    class="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
            </div>
            <button onclick="openScanner()"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2 rounded-lg font-semibold transition-all shadow-lg shadow-indigo-500/20 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                Scanner
            </button>
        </div>

        <div id="cat-filters" class="flex gap-2 flex-wrap mb-4">
            <button onclick="filterByCategory('')" class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg">Tous</button>
            <?php foreach ($categories as $cat): ?>
                <button onclick="filterByCategory('<?= htmlspecialchars($cat) ?>')"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-lg transition-all">
                    <?= htmlspecialchars($cat) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="product-grid" class="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 content-start"></div>
    </div>

    <!-- Panier & Paiement -->
    <div class="w-full lg:w-96 flex flex-col bg-slate-900 rounded-xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2 text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="text-white font-bold">Panier</h3>
            </div>
            <span id="cart-count" class="text-xs px-2 py-1 bg-slate-800 rounded-full text-slate-300 font-semibold">0 article</span>
        </div>

        <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-3">
            <div id="cart-empty" class="h-full flex flex-col items-center justify-center text-slate-500 py-10">
                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M3 3h2l.4 2M7 13h10l4-4H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-sm">Le panier est vide</p>
                <p class="text-xs opacity-60 mt-1">Scannez ou cliquez un produit</p>
            </div>
        </div>

        <div class="p-4 bg-slate-950 border-t border-slate-800 space-y-4">
            <!-- Résumé -->
            <div class="space-y-1 text-sm border-b border-slate-800/50 pb-3">
                <div class="flex justify-between text-slate-400">
                    <span>Sous-total HT</span>
                    <span id="subtotal">0,00 CDF</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>TVA (<?= VAT_PERCENTAGE ?>%)</span>
                    <span id="tax">0,00 CDF</span>
                </div>
                <div class="flex justify-between font-bold text-base mt-2 pt-1 border-t border-slate-800/50">
                    <span class="text-white">Total TTC</span>
                    <span id="total" class="text-indigo-400 text-lg">0,00 CDF</span>
                </div>
            </div>

            <!-- Informations Client -->
            <div>
                <input id="customer-name" type="text" placeholder="Nom client (facultatif)"
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:outline-none mb-3">

                <label class="block text-xs font-semibold text-slate-400 mb-2">Moyen de paiement</label>
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <button onclick="setPayment('cash')" data-pm="cash"
                        class="pm-btn flex flex-col items-center py-2 rounded-lg border text-xs font-medium transition-all bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>Espèces
                    </button>
                    <button onclick="setPayment('card')" data-pm="card"
                        class="pm-btn flex flex-col items-center py-2 rounded-lg border text-xs font-medium transition-all bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>Carte
                    </button>
                    <button onclick="setPayment('mobile')" data-pm="mobile"
                        class="pm-btn flex flex-col items-center py-2 rounded-lg border text-xs font-medium transition-all bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>Mobile
                    </button>
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
                        <span id="change-amount" class="text-base font-black text-green-400">0,00 CDF</span>
                    </div>
                </div>

                <button id="checkout-btn" onclick="checkout()" disabled
                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700
                        disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed
                        text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 active:scale-[.98] transition-all text-sm">
                    Encaisser (0,00 CDF)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoice-modal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-xl border border-slate-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Facture</h3>
            <button onclick="closeInvoiceModal()" class="text-slate-400 hover:text-slate-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="invoice-content" class="p-6 text-sm font-mono whitespace-pre-wrap text-slate-300">
            <!-- Generated invoice will be here -->
        </div>

        <div class="p-6 bg-slate-950 border-t border-slate-800 flex gap-3">
            <button onclick="printInvoice()"
                class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-all">
                Imprimer
            </button>
            <button onclick="closeInvoiceModal()"
                class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg transition-all">
                Fermer
            </button>
        </div>
    </div>
</div>

<script src="/facturation/assets/js/scanner.js"></script>
<script>
    const VAT_RATE = <?= VAT_RATE ?>;
    const VAT_PERCENTAGE = <?= VAT_PERCENTAGE ?>;
    const CURRENCY_SYMBOL = '<?= CURRENCY_SYMBOL ?>';

    let cart = [];
    let currentPaymentMethod = null;
    const products = <?= json_encode($products) ?>;

    // Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(price) + ' ' + CURRENCY_SYMBOL;
    }

    // Filter products by category
    function filterByCategory(category) {
        displayProducts(category);
    }

    // Display products
    function displayProducts(category = '') {
        const grid = document.getElementById('product-grid');
        grid.innerHTML = '';

        const filtered = category ?
            products.filter(p => p.category === category) :
            products;

        filtered.forEach(product => {
            const card = document.createElement('div');
            card.className = 'bg-slate-800 border border-slate-700 rounded-lg p-3 hover:border-indigo-500 cursor-pointer transition-all hover:shadow-lg hover:shadow-indigo-500/10';
            card.onclick = () => addToCart(product);

            card.innerHTML = `
                <div class="mb-2">
                    <h4 class="font-semibold text-slate-100 text-sm line-clamp-2">${product.name}</h4>
                    <p class="text-xs text-slate-400">${product.category || 'N/A'}</p>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-lg font-bold text-indigo-400">${formatPrice(product.price)}</span>
                    <span class="text-xs bg-slate-900 px-2 py-1 rounded text-slate-300">${product.stock} stock</span>
                </div>
            `;

            grid.appendChild(card);
        });
    }

    // Add to cart
    function addToCart(product) {
        const existing = cart.find(p => p.id === product.id);
        if (existing) {
            if (existing.quantity < product.stock) {
                existing.quantity++;
            }
        } else {
            if (product.stock > 0) {
                cart.push({
                    ...product,
                    quantity: 1
                });
            }
        }
        updateCart();
    }

    // Update cart display
    function updateCart() {
        const cartItems = document.getElementById('cart-items');
        const empty = document.getElementById('cart-empty');
        const count = document.getElementById('cart-count');

        let totalItems = 0;
        let subtotalHT = 0;

        if (cart.length === 0) {
            empty.classList.remove('hidden');
        } else {
            empty.classList.add('hidden');
        }

        cartItems.innerHTML = '';

        cart.forEach((item, idx) => {
            totalItems += item.quantity;
            const subtotal = item.price * item.quantity;
            subtotalHT += subtotal;

            const row = document.createElement('div');
            row.className = 'bg-slate-800/50 rounded-lg p-3 flex justify-between items-center';
            row.innerHTML = `
                <div class="flex-1">
                    <div class="font-semibold text-slate-100 text-sm">${item.name}</div>
                    <div class="text-xs text-slate-400">${formatPrice(item.price)} × ${item.quantity}</div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-indigo-400">${formatPrice(subtotal)}</div>
                    <button onclick="removeFromCart(${idx})" class="text-xs text-red-400 hover:text-red-300 mt-1">Retirer</button>
                </div>
            `;
            cartItems.appendChild(row);
        });

        // Update summary
        const tva = subtotalHT * VAT_RATE;
        const total = subtotalHT + tva;

        document.getElementById('subtotal').textContent = formatPrice(subtotalHT);
        document.getElementById('tax').textContent = formatPrice(tva);
        document.getElementById('total').textContent = formatPrice(total);

        count.textContent = totalItems + ' article' + (totalItems > 1 ? 's' : '');

        // Enable/disable checkout
        document.getElementById('checkout-btn').disabled = cart.length === 0;
        document.getElementById('checkout-btn').textContent = cart.length > 0 ?
            `Encaisser (${formatPrice(total)})` :
            'Encaisser (0,00 CDF)';
    }

    // Remove from cart
    function removeFromCart(idx) {
        cart.splice(idx, 1);
        updateCart();
    }

    // Set payment method
    function setPayment(method) {
        currentPaymentMethod = method;

        document.querySelectorAll('.pm-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-500');
            btn.classList.add('bg-slate-800', 'text-slate-400', 'border-slate-700');
        });

        document.querySelector(`[data-pm="${method}"]`).classList.add('bg-indigo-600', 'text-white', 'border-indigo-500');

        const cashBlock = document.getElementById('cash-block');
        if (method === 'cash') {
            cashBlock.classList.remove('hidden');
        } else {
            cashBlock.classList.add('hidden');
        }
    }

    // Update change
    function updateChange() {
        const total = parseFloat(document.getElementById('total').textContent.replace(/[^0-9.,]/g, '').replace(',', '.'));
        const received = parseFloat(document.getElementById('cash-received').value) || 0;

        if (received >= total) {
            const change = received - total;
            document.getElementById('change-block').classList.remove('hidden');
            document.getElementById('change-amount').textContent = formatPrice(change);
        } else {
            document.getElementById('change-block').classList.add('hidden');
        }
    }

    // Checkout
    function checkout() {
        if (cart.length === 0 || !currentPaymentMethod) {
            alert('Veuillez sélectionner un moyen de paiement');
            return;
        }

        if (currentPaymentMethod === 'cash') {
            const received = parseFloat(document.getElementById('cash-received').value) || 0;
            const total = parseFloat(document.getElementById('total').textContent.replace(/[^0-9.,]/g, '').replace(',', '.'));
            if (received < total) {
                alert('Montant insuffisant');
                return;
            }
        }

        // Create invoice
        const customerName = document.getElementById('customer-name').value || 'Client';

        const invoiceData = {
            items: cart.map(item => ({
                code_barre: item.barcode || '',
                nom: item.name,
                prix_unitaire_ht: item.price,
                quantite: item.quantity
            })),
            caissier: '<?= $user['username'] ?>',
            customer_name: customerName,
            payment_method: currentPaymentMethod
        };

        // Send to server
        fetch('/facturation/modules/facturation/nouvelle-facture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(invoiceData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    displayInvoice(data.invoice);
                    cart = [];
                    currentPaymentMethod = null;
                    document.getElementById('customer-name').value = '';
                    document.getElementById('cash-received').value = '';
                    document.querySelectorAll('.pm-btn').forEach(btn => {
                        btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-500');
                        btn.classList.add('bg-slate-800', 'text-slate-400', 'border-slate-700');
                    });
                    updateCart();
                } else {
                    alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erreur lors de la sauvegarde de la facture');
            });
    }

    // Display invoice
    function displayInvoice(invoice) {
        const content = document.getElementById('invoice-content');

        let text = '═════════════════════════════════════════════════════════════\n';
        text += 'FACTURE\n';
        text += '═════════════════════════════════════════════════════════════\n\n';

        text += 'N° Facture: ' + invoice.id_facture + '\n';
        text += 'Date: ' + invoice.date + '\n';
        text += 'Heure: ' + invoice.heure + '\n';
        text += 'Caissier: ' + invoice.caissier + '\n';
        if (invoice.client) {
            text += 'Client: ' + invoice.client + '\n';
        }
        text += '\n';

        text += '───────────────────────────────────────────────────────────\n';
        text += 'Désignation                    Prix Unit.    Qté   Sous-total\n';
        text += '───────────────────────────────────────────────────────────\n';

        invoice.articles.forEach(item => {
            text += String(item.nom).padEnd(30).substring(0, 30);
            text += ' ' + formatPrice(item.prix_unitaire_ht).padStart(12);
            text += ' ' + String(item.quantite).padStart(6);
            text += ' ' + formatPrice(item.sous_total_ht).padStart(15) + '\n';
        });

        text += '───────────────────────────────────────────────────────────\n';
        text += String('TOTAL HT').padEnd(30) + ' ' + ' '.repeat(12) + ' '.repeat(6) + ' ' + formatPrice(invoice.total_ht).padStart(15) + '\n';
        text += String('TVA (' + VAT_PERCENTAGE + '%)').padEnd(30) + ' ' + ' '.repeat(12) + ' '.repeat(6) + ' ' + formatPrice(invoice.tva).padStart(15) + '\n';
        text += '═════════════════════════════════════════════════════════════\n';
        text += String('NET À PAYER').padEnd(30) + ' ' + ' '.repeat(12) + ' '.repeat(6) + ' ' + formatPrice(invoice.total_ttc).padStart(15) + '\n';
        text += '═════════════════════════════════════════════════════════════\n';

        content.textContent = text;
        document.getElementById('invoice-modal').classList.remove('hidden');
    }

    function closeInvoiceModal() {
        document.getElementById('invoice-modal').classList.add('hidden');
    }

    function printInvoice() {
        window.print();
    }

    function closeScanner() {
        document.getElementById('scanner-modal').classList.add('hidden');
        document.getElementById('barcode-input').value = '';
    }

    function openScanner() {
        document.getElementById('scanner-modal').classList.remove('hidden');
        document.getElementById('barcode-input').focus();
    }

    // Initialize
    displayProducts();
    updateCart();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>