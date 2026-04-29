import React, { useState, useMemo } from 'react';
import { useApp } from '../context/AppContext';
import { BarcodeScanner } from './BarcodeScanner';
import { Search, ShoppingCart, Trash2, Plus, Minus, CreditCard, Banknote, Smartphone, Scan, User, CheckCircle2 } from 'lucide-react';
import confetti from 'canvas-confetti';

export const POS: React.FC = () => {
  const { products, createInvoice } = useApp();
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('Tous');
  const [cart, setCart] = useState<{ productId: string; name: string; price: number; quantity: number; total: number }[]>([]);
  const [customerName, setCustomerName] = useState('');
  const [paymentMethod, setPaymentMethod] = useState<'cash' | 'card' | 'mobile'>('cash');
  const [cashReceived, setCashReceived] = useState<number>(0);
  const [showScanner, setShowScanner] = useState(false);
  const [checkoutInvoice, setCheckoutInvoice] = useState<any>(null);

  // Categories extraction
  const categories = useMemo(() => {
    const list = new Set(products.map(p => p.category));
    return ['Tous', ...Array.from(list)];
  }, [products]);

  // Filtered Products
  const filteredProducts = useMemo(() => {
    return products.filter(p => {
      const matchesSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase()) || p.barcode.includes(searchTerm);
      const matchesCategory = selectedCategory === 'Tous' || p.category === selectedCategory;
      return matchesSearch && matchesCategory;
    });
  }, [products, searchTerm, selectedCategory]);

  const addToCart = (productId: string) => {
    const product = products.find(p => p.id === productId);
    if (!product || product.stock <= 0) return;

    setCart(currentCart => {
      const existing = currentCart.find(item => item.productId === productId);
      if (existing) {
        if (existing.quantity >= product.stock) return currentCart;
        return currentCart.map(item =>
          item.productId === productId
            ? { ...item, quantity: item.quantity + 1, total: (item.quantity + 1) * item.price }
            : item
        );
      }
      return [...currentCart, { productId, name: product.name, price: product.price, quantity: 1, total: product.price }];
    });
  };

  const updateQuantity = (productId: string, quantity: number) => {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    if (quantity <= 0) {
      setCart(cart.filter(item => item.productId !== productId));
      return;
    }

    if (quantity > product.stock) return;

    setCart(cart.map(item =>
      item.productId === productId
        ? { ...item, quantity, total: quantity * item.price }
        : item
    ));
  };

  const handleBarcodeScanned = (barcode: string) => {
    const product = products.find(p => p.barcode === barcode);
    if (product) {
      if (product.stock > 0) {
        addToCart(product.id);
      } else {
        alert(`Le produit "${product.name}" est en rupture de stock.`);
      }
    } else {
      alert(`Aucun produit trouvé avec le code-barres : ${barcode}`);
    }
  };

  const cartSubtotal = cart.reduce((sum, item) => sum + item.total, 0);
  const cartTax = cartSubtotal * 0.20;
  const cartTotal = cartSubtotal + cartTax;

  const handleCheckout = (e: React.FormEvent) => {
    e.preventDefault();
    if (cart.length === 0) return;

    const invoice = createInvoice(cart, paymentMethod, customerName);
    setCheckoutInvoice(invoice);
    confetti({
      particleCount: 100,
      spread: 70,
      origin: { y: 0.6 }
    });

    // Reset Cart
    setCart([]);
    setCustomerName('');
    setCashReceived(0);
  };

  return (
    <div className="flex flex-col lg:flex-row h-[calc(100vh-120px)] gap-6 p-4">
      {/* Product Catalog */}
      <div className="flex-1 flex flex-col bg-slate-900/50 backdrop-blur-sm rounded-xl p-4 border border-slate-800">
        <div className="flex flex-col sm:flex-row gap-4 mb-4">
          <div className="relative flex-1">
            <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
              <Search size={18} />
            </span>
            <input
              type="text"
              placeholder="Rechercher par nom ou code-barres..."
              className="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          <button
            onClick={() => setShowScanner(true)}
            className="flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-all shadow-lg shadow-indigo-500/20"
          >
            <Scan size={18} />
            <span>Scanner</span>
          </button>
        </div>

        {/* Categories Tabs */}
        <div className="flex overflow-x-auto gap-2 pb-3 mb-4 scrollbar-thin">
          {categories.map(cat => (
            <button
              key={cat}
              onClick={() => setSelectedCategory(cat)}
              className={`px-3 py-1.5 rounded-lg text-sm font-medium whitespace-nowrap transition-all ${
                selectedCategory === cat
                  ? 'bg-indigo-500 text-white'
                  : 'bg-slate-800 text-slate-400 hover:bg-slate-700 hover:text-slate-200'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>

        {/* Products Grid */}
        <div className="flex-1 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 pr-1">
          {filteredProducts.map(product => {
            const outOfStock = product.stock <= 0;
            const inCart = cart.find(item => item.productId === product.id);

            return (
              <button
                key={product.id}
                disabled={outOfStock}
                onClick={() => addToCart(product.id)}
                className={`relative p-4 rounded-xl text-left border flex flex-col justify-between transition-all group ${
                  outOfStock
                    ? 'bg-slate-950 border-slate-900 opacity-40 cursor-not-allowed'
                    : inCart
                    ? 'bg-indigo-500/10 border-indigo-500/50 hover:bg-indigo-500/20'
                    : 'bg-slate-800/40 border-slate-800 hover:border-slate-700 hover:bg-slate-800/80 hover:translate-y-[-2px]'
                }`}
              >
                {inCart && (
                  <span className="absolute -top-2 -right-2 bg-indigo-500 text-white text-xs font-bold h-6 w-6 rounded-full flex items-center justify-center shadow-lg">
                    {inCart.quantity}
                  </span>
                )}
                
                <div>
                  <span className="text-xs font-semibold text-slate-500 tracking-wider uppercase">{product.category}</span>
                  <h4 className="text-slate-200 font-bold mt-1 line-clamp-2 group-hover:text-indigo-400 transition-colors">
                    {product.name}
                  </h4>
                </div>

                <div className="mt-4 pt-3 border-t border-slate-800/50 flex items-center justify-between">
                  <span className="text-lg font-black text-white">
                    {product.price.toFixed(2)} €
                  </span>
                  <span className={`text-xs px-1.5 py-0.5 rounded ${
                    product.stock < product.minStockAlert 
                      ? 'bg-red-500/10 text-red-400' 
                      : 'bg-green-500/10 text-green-400'
                  }`}>
                    {outOfStock ? 'Épuisé' : `Stock: ${product.stock}`}
                  </span>
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* Cart & Checkout */}
      <div className="w-full lg:w-96 flex flex-col bg-slate-900 rounded-xl border border-slate-800 shadow-xl overflow-hidden">
        <div className="p-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
          <div className="flex items-center space-x-2 text-indigo-400">
            <ShoppingCart size={20} />
            <h3 className="text-white font-bold">Panier</h3>
          </div>
          <span className="text-xs px-2 py-1 bg-slate-800 rounded-full text-slate-300 font-semibold">
            {cart.reduce((sum, item) => sum + item.quantity, 0)} articles
          </span>
        </div>

        <div className="flex-1 overflow-y-auto p-4 space-y-3">
          {cart.length === 0 ? (
            <div className="h-full flex flex-col items-center justify-center text-slate-500 py-10">
              <ShoppingCart size={48} className="stroke-1 mb-3 opacity-30" />
              <p className="text-sm">Le panier est vide</p>
              <p className="text-xs opacity-60 mt-1">Ajoutez des produits pour facturer</p>
            </div>
          ) : (
            cart.map(item => (
              <div key={item.productId} className="flex items-center justify-between bg-slate-950 p-3 rounded-lg border border-slate-850">
                <div className="flex-1 min-w-0 pr-2">
                  <h5 className="text-slate-200 text-sm font-semibold truncate">{item.name}</h5>
                  <p className="text-xs text-slate-400 mt-0.5">{item.price.toFixed(2)} € x {item.quantity}</p>
                </div>
                <div className="flex items-center space-x-2">
                  <div className="flex items-center bg-slate-800 rounded-lg p-0.5">
                    <button
                      onClick={() => updateQuantity(item.productId, item.quantity - 1)}
                      className="p-1 text-slate-400 hover:text-white rounded"
                    >
                      <Minus size={14} />
                    </button>
                    <span className="text-slate-200 font-bold px-2 text-sm">{item.quantity}</span>
                    <button
                      onClick={() => updateQuantity(item.productId, item.quantity + 1)}
                      className="p-1 text-slate-400 hover:text-white rounded"
                    >
                      <Plus size={14} />
                    </button>
                  </div>
                  <button
                    onClick={() => updateQuantity(item.productId, 0)}
                    className="p-2 text-slate-500 hover:text-red-400 transition-all rounded-lg"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
            ))
          )}
        </div>

        {/* Totals & Actions */}
        <div className="p-4 bg-slate-950 border-t border-slate-800 space-y-4">
          <div className="space-y-1 text-sm border-b border-slate-800/50 pb-3">
            <div className="flex justify-between text-slate-400">
              <span>Sous-total HT</span>
              <span>{cartSubtotal.toFixed(2)} €</span>
            </div>
            <div className="flex justify-between text-slate-400">
              <span>TVA (20%)</span>
              <span>{cartTax.toFixed(2)} €</span>
            </div>
            <div className="flex justify-between text-white font-bold text-base mt-2 pt-1 border-t border-slate-800/50">
              <span>Total TTC</span>
              <span className="text-indigo-400 text-lg">{cartTotal.toFixed(2)} €</span>
            </div>
          </div>

          <form onSubmit={handleCheckout} className="space-y-4">
            {/* Customer info */}
            <div className="relative">
              <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <User size={16} />
              </span>
              <input
                type="text"
                placeholder="Nom client (facultatif)"
                className="w-full pl-9 pr-4 py-2 text-xs bg-slate-800 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none"
                value={customerName}
                onChange={(e) => setCustomerName(e.target.value)}
              />
            </div>

            {/* Payment Method */}
            <div>
              <label className="block text-xs font-semibold text-slate-400 mb-2">Moyen de paiement</label>
              <div className="grid grid-cols-3 gap-2">
                <button
                  type="button"
                  onClick={() => setPaymentMethod('cash')}
                  className={`flex flex-col items-center justify-center py-2 rounded-lg border text-xs font-medium transition-all ${
                    paymentMethod === 'cash'
                      ? 'bg-indigo-500/20 border-indigo-500 text-indigo-300'
                      : 'bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200'
                  }`}
                >
                  <Banknote size={18} className="mb-1" />
                  Espèces
                </button>
                <button
                  type="button"
                  onClick={() => setPaymentMethod('card')}
                  className={`flex flex-col items-center justify-center py-2 rounded-lg border text-xs font-medium transition-all ${
                    paymentMethod === 'card'
                      ? 'bg-indigo-500/20 border-indigo-500 text-indigo-300'
                      : 'bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200'
                  }`}
                >
                  <CreditCard size={18} className="mb-1" />
                  Carte
                </button>
                <button
                  type="button"
                  onClick={() => setPaymentMethod('mobile')}
                  className={`flex flex-col items-center justify-center py-2 rounded-lg border text-xs font-medium transition-all ${
                    paymentMethod === 'mobile'
                      ? 'bg-indigo-500/20 border-indigo-500 text-indigo-300'
                      : 'bg-slate-800 border-slate-700 text-slate-400 hover:text-slate-200'
                  }`}
                >
                  <Smartphone size={18} className="mb-1" />
                  Mobile
                </button>
              </div>
            </div>

            {paymentMethod === 'cash' && cart.length > 0 && (
              <div className="bg-slate-800/40 border border-slate-800 p-2.5 rounded-lg flex items-center justify-between gap-3">
                <div className="flex-1">
                  <label className="block text-[10px] uppercase tracking-wide font-bold text-slate-400 mb-1">Montant perçu</label>
                  <input
                    type="number"
                    step="0.01"
                    className="w-full bg-slate-800 text-sm font-bold text-white px-2 py-1 rounded focus:outline-none"
                    placeholder="Montant reçu"
                    value={cashReceived || ''}
                    onChange={(e) => setCashReceived(parseFloat(e.target.value) || 0)}
                  />
                </div>
                {cashReceived > cartTotal && (
                  <div className="text-right">
                    <span className="block text-[10px] uppercase tracking-wide font-bold text-slate-400">Rendu monnaie</span>
                    <span className="text-base font-black text-green-400">{(cashReceived - cartTotal).toFixed(2)} €</span>
                  </div>
                )}
              </div>
            )}

            <button
              type="submit"
              disabled={cart.length === 0}
              className="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:from-slate-800 disabled:to-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all"
            >
              Encaisser ({cartTotal.toFixed(2)} €)
            </button>
          </form>
        </div>
      </div>

      {/* Invoice Modal after Success */}
      {checkoutInvoice && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl p-6 w-full max-w-sm text-slate-900 shadow-2xl relative">
            <div className="flex flex-col items-center border-b border-slate-200 pb-4 mb-4">
              <CheckCircle2 size={40} className="text-emerald-500 mb-2 animate-bounce" />
              <h3 className="text-xl font-black">Paiement Réussi !</h3>
              <p className="text-xs text-slate-500">Facture N° {checkoutInvoice.invoiceNumber}</p>
            </div>

            <div className="text-xs space-y-1 mb-4">
              <div className="flex justify-between">
                <span className="text-slate-500">Date:</span>
                <span className="font-medium">{new Date(checkoutInvoice.date).toLocaleString()}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-500">Caissier:</span>
                <span className="font-medium">{checkoutInvoice.cashierName}</span>
              </div>
              {checkoutInvoice.customerName && (
                <div className="flex justify-between">
                  <span className="text-slate-500">Client:</span>
                  <span className="font-medium">{checkoutInvoice.customerName}</span>
                </div>
              )}
            </div>

            <div className="border-b border-dashed border-slate-300 pb-3 mb-3 text-xs space-y-2">
              <span className="font-bold block text-slate-700">Articles :</span>
              {checkoutInvoice.items.map((item: any, i: number) => (
                <div key={i} className="flex justify-between text-slate-800">
                  <span>{item.name} (x{item.quantity})</span>
                  <span className="font-semibold">{item.total.toFixed(2)} €</span>
                </div>
              ))}
            </div>

            <div className="text-sm font-bold flex justify-between mb-6">
              <span>TOTAL TTC</span>
              <span className="text-indigo-600 text-lg">{checkoutInvoice.total.toFixed(2)} €</span>
            </div>

            <div className="flex gap-2">
              <button
                onClick={() => setCheckoutInvoice(null)}
                className="flex-1 py-2 text-center text-xs font-bold border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-all"
              >
                Fermer
              </button>
              <button
                onClick={() => {
                  window.print();
                }}
                className="flex-1 py-2 text-center text-xs font-bold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all"
              >
                Imprimer
              </button>
            </div>
          </div>
        </div>
      )}

      {showScanner && (
        <BarcodeScanner
          onScan={handleBarcodeScanned}
          onClose={() => setShowScanner(false)}
        />
      )}
    </div>
  );
};
