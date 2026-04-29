import React, { useState, useMemo } from 'react';
import { useApp } from '../context/AppContext';
import { Invoice } from '../types';
import { Search, FileText, Printer, BarChart3, TrendingUp, ShoppingBag, CreditCard } from 'lucide-react';

export const InvoicesList: React.FC = () => {
  const { invoices } = useApp();
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedInvoice, setSelectedInvoice] = useState<Invoice | null>(null);

  const filteredInvoices = useMemo(() => {
    return invoices.filter(inv => 
      inv.invoiceNumber.toLowerCase().includes(searchTerm.toLowerCase()) || 
      inv.customerName?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      inv.cashierName.toLowerCase().includes(searchTerm.toLowerCase())
    );
  }, [invoices, searchTerm]);

  // Statistics calculations
  const stats = useMemo(() => {
    const totalSales = invoices.reduce((sum, inv) => sum + inv.total, 0);
    const invoiceCount = invoices.length;
    const itemsSold = invoices.reduce((sum, inv) => 
      sum + inv.items.reduce((acc, item) => acc + item.quantity, 0), 0
    );

    const methods = { cash: 0, card: 0, mobile: 0 };
    invoices.forEach(inv => {
      methods[inv.paymentMethod] = (methods[inv.paymentMethod] || 0) + inv.total;
    });

    return { totalSales, invoiceCount, itemsSold, methods };
  }, [invoices]);

  return (
    <div className="p-4 space-y-6">
      <div>
        <h2 className="text-2xl font-black text-white">Factures & Rapports</h2>
        <p className="text-slate-400 text-sm mt-0.5">Suivez les ventes et générez des récapitulatifs.</p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span className="text-xs font-bold text-slate-500 uppercase tracking-wider">Chiffre d'Affaires</span>
            <h3 className="text-2xl font-black text-white mt-1">{stats.totalSales.toFixed(2)} €</h3>
          </div>
          <div className="p-3 bg-indigo-500/10 rounded-xl text-indigo-400">
            <TrendingUp size={24} />
          </div>
        </div>

        <div className="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span className="text-xs font-bold text-slate-500 uppercase tracking-wider">Factures</span>
            <h3 className="text-2xl font-black text-white mt-1">{stats.invoiceCount}</h3>
          </div>
          <div className="p-3 bg-emerald-500/10 rounded-xl text-emerald-400">
            <FileText size={24} />
          </div>
        </div>

        <div className="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span className="text-xs font-bold text-slate-500 uppercase tracking-wider">Articles vendus</span>
            <h3 className="text-2xl font-black text-white mt-1">{stats.itemsSold}</h3>
          </div>
          <div className="p-3 bg-amber-500/10 rounded-xl text-amber-400">
            <ShoppingBag size={24} />
          </div>
        </div>

        <div className="bg-slate-900 border border-slate-800 p-4 rounded-xl flex items-center justify-between">
          <div>
            <span className="text-xs font-bold text-slate-500 uppercase tracking-wider">Paiement CB / Mobile</span>
            <h3 className="text-2xl font-black text-white mt-1">
              {(stats.methods.card + stats.methods.mobile).toFixed(2)} €
            </h3>
          </div>
          <div className="p-3 bg-violet-500/10 rounded-xl text-violet-400">
            <CreditCard size={24} />
          </div>
        </div>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        {/* Invoices Table */}
        <div className="flex-1 bg-slate-900 border border-slate-800 rounded-xl p-4">
          <div className="mb-4 relative">
            <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
              <Search size={18} />
            </span>
            <input
              type="text"
              placeholder="Rechercher par N° facture, caissier ou client..."
              className="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>

          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-950 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800">
                  <th className="px-4 py-3">N° Facture</th>
                  <th className="px-4 py-3">Date</th>
                  <th className="px-4 py-3">Caissier</th>
                  <th className="px-4 py-3">Mode</th>
                  <th className="px-4 py-3">Total</th>
                  <th className="px-4 py-3 text-right">Détails</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/50 text-sm text-slate-300">
                {filteredInvoices.map(invoice => (
                  <tr key={invoice.id} className="hover:bg-slate-850/30 transition-colors">
                    <td className="px-4 py-3 font-semibold text-white">{invoice.invoiceNumber}</td>
                    <td className="px-4 py-3 text-slate-400 text-xs">
                      {new Date(invoice.date).toLocaleString()}
                    </td>
                    <td className="px-4 py-3 text-slate-400">{invoice.cashierName}</td>
                    <td className="px-4 py-3">
                      <span className="text-xs uppercase tracking-wider bg-slate-800 px-2 py-0.5 rounded text-slate-300 border border-slate-700">
                        {invoice.paymentMethod}
                      </span>
                    </td>
                    <td className="px-4 py-3 font-bold text-indigo-400">{invoice.total.toFixed(2)} €</td>
                    <td className="px-4 py-3 text-right">
                      <button
                        onClick={() => setSelectedInvoice(invoice)}
                        className="px-3 py-1 bg-slate-800 hover:bg-indigo-600 text-slate-300 hover:text-white rounded transition-all text-xs font-semibold"
                      >
                        Visualiser
                      </button>
                    </td>
                  </tr>
                ))}

                {filteredInvoices.length === 0 && (
                  <tr>
                    <td colSpan={6} className="px-4 py-10 text-center text-slate-500">
                      <FileText size={40} className="mx-auto stroke-1 mb-2 opacity-30" />
                      Aucune facture trouvée.
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Breakdown side section */}
        <div className="w-full lg:w-80 bg-slate-900 border border-slate-800 p-4 rounded-xl h-fit">
          <h4 className="text-sm font-bold text-white mb-4 flex items-center space-x-2">
            <BarChart3 size={18} className="text-indigo-400" />
            <span>Répartition Paiements</span>
          </h4>
          <div className="space-y-4">
            <div>
              <div className="flex justify-between text-xs font-bold text-slate-400 mb-1">
                <span>ESPECES</span>
                <span className="text-slate-200">{stats.methods.cash.toFixed(2)} €</span>
              </div>
              <div className="h-2 bg-slate-950 rounded-full overflow-hidden">
                <div 
                  className="h-full bg-emerald-500 rounded-full" 
                  style={{ width: `${stats.totalSales ? (stats.methods.cash / stats.totalSales) * 100 : 0}%` }}
                ></div>
              </div>
            </div>

            <div>
              <div className="flex justify-between text-xs font-bold text-slate-400 mb-1">
                <span>CARTE BANCAIRE</span>
                <span className="text-slate-200">{stats.methods.card.toFixed(2)} €</span>
              </div>
              <div className="h-2 bg-slate-950 rounded-full overflow-hidden">
                <div 
                  className="h-full bg-indigo-500 rounded-full" 
                  style={{ width: `${stats.totalSales ? (stats.methods.card / stats.totalSales) * 100 : 0}%` }}
                ></div>
              </div>
            </div>

            <div>
              <div className="flex justify-between text-xs font-bold text-slate-400 mb-1">
                <span>MOBILE</span>
                <span className="text-slate-200">{stats.methods.mobile.toFixed(2)} €</span>
              </div>
              <div className="h-2 bg-slate-950 rounded-full overflow-hidden">
                <div 
                  className="h-full bg-violet-500 rounded-full" 
                  style={{ width: `${stats.totalSales ? (stats.methods.mobile / stats.totalSales) * 100 : 0}%` }}
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Detail Invoice Modal */}
      {selectedInvoice && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl p-6 w-full max-w-md text-slate-900 shadow-2xl relative animate-scale">
            <div className="flex justify-between items-start border-b border-slate-200 pb-4 mb-4">
              <div>
                <h3 className="text-lg font-black uppercase tracking-tight">DUPLICATA FACTURE</h3>
                <p className="text-xs text-slate-500 font-medium">N° {selectedInvoice.invoiceNumber}</p>
              </div>
              <button
                onClick={() => setSelectedInvoice(null)}
                className="text-slate-400 hover:text-slate-900 text-lg transition-colors"
              >
                ✕
              </button>
            </div>

            <div className="text-xs space-y-1 mb-4">
              <div className="flex justify-between">
                <span className="text-slate-500">Date:</span>
                <span className="font-semibold">{new Date(selectedInvoice.date).toLocaleString()}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-500">Caissier:</span>
                <span className="font-semibold">{selectedInvoice.cashierName}</span>
              </div>
              {selectedInvoice.customerName && (
                <div className="flex justify-between">
                  <span className="text-slate-500">Client:</span>
                  <span className="font-semibold">{selectedInvoice.customerName}</span>
                </div>
              )}
              <div className="flex justify-between">
                <span className="text-slate-500">Règlement:</span>
                <span className="font-semibold uppercase">{selectedInvoice.paymentMethod}</span>
              </div>
            </div>

            <div className="border-b border-dashed border-slate-300 pb-3 mb-3 text-xs space-y-2">
              <span className="font-bold text-slate-700 block mb-1">Articles :</span>
              {selectedInvoice.items.map((item, i) => (
                <div key={i} className="flex justify-between text-slate-800">
                  <span>{item.name} (x{item.quantity})</span>
                  <span className="font-bold">{item.total.toFixed(2)} €</span>
                </div>
              ))}
            </div>

            <div className="text-xs space-y-1 border-b border-slate-200 pb-3 mb-3 text-slate-500">
              <div className="flex justify-between">
                <span>Sous-total HT</span>
                <span>{selectedInvoice.subtotal.toFixed(2)} €</span>
              </div>
              <div className="flex justify-between">
                <span>TVA (20%)</span>
                <span>{selectedInvoice.tax.toFixed(2)} €</span>
              </div>
            </div>

            <div className="text-base font-black flex justify-between mb-6">
              <span>TOTAL TTC</span>
              <span className="text-indigo-600">{selectedInvoice.total.toFixed(2)} €</span>
            </div>

            <button
              onClick={() => window.print()}
              className="w-full flex items-center justify-center space-x-2 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition-all shadow-lg"
            >
              <Printer size={16} />
              <span>Imprimer</span>
            </button>
          </div>
        </div>
      )}
    </div>
  );
};
