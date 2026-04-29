import React, { useState, useMemo } from 'react';
import { useApp } from '../context/AppContext';
import { Product } from '../types';
import { Plus, Edit2, Trash2, Search, AlertTriangle, PackagePlus, FileText, Check } from 'lucide-react';

export const Products: React.FC = () => {
  const { products, addProduct, updateProduct, deleteProduct } = useApp();
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('Tous');
  
  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingProduct, setEditingProduct] = useState<Product | null>(null);
  
  // Form State
  const [name, setName] = useState('');
  const [price, setPrice] = useState<number>(0);
  const [stock, setStock] = useState<number>(0);
  const [barcode, setBarcode] = useState('');
  const [category, setCategory] = useState('Alimentation');
  const [minStockAlert, setMinStockAlert] = useState<number>(5);

  const categories = useMemo(() => {
    const list = new Set(products.map(p => p.category));
    return ['Tous', ...Array.from(list)];
  }, [products]);

  const filteredProducts = useMemo(() => {
    return products.filter(p => {
      const matchesSearch = p.name.toLowerCase().includes(searchTerm.toLowerCase()) || p.barcode.includes(searchTerm);
      const matchesCategory = selectedCategory === 'Tous' || p.category === selectedCategory;
      return matchesSearch && matchesCategory;
    });
  }, [products, searchTerm, selectedCategory]);

  const lowStockCount = useMemo(() => {
    return products.filter(p => p.stock <= p.minStockAlert).length;
  }, [products]);

  const openAddModal = () => {
    setEditingProduct(null);
    setName('');
    setPrice(0);
    setStock(0);
    setBarcode(generateBarcode());
    setCategory('Alimentation');
    setMinStockAlert(5);
    setIsModalOpen(true);
  };

  const openEditModal = (product: Product) => {
    setEditingProduct(product);
    setName(product.name);
    setPrice(product.price);
    setStock(product.stock);
    setBarcode(product.barcode);
    setCategory(product.category);
    setMinStockAlert(product.minStockAlert);
    setIsModalOpen(true);
  };

  const generateBarcode = () => {
    // Generate a simple EAN-13 looking number
    const prefix = '3000000';
    const rand = Math.floor(100000 + Math.random() * 900000).toString();
    return prefix + rand;
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || price < 0 || stock < 0 || !barcode) return;

    if (editingProduct) {
      updateProduct({
        ...editingProduct,
        name,
        price,
        stock,
        barcode,
        category,
        minStockAlert
      });
    } else {
      addProduct({
        name,
        price,
        stock,
        barcode,
        category,
        minStockAlert
      });
    }
    setIsModalOpen(false);
  };

  return (
    <div className="p-4 space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-2xl font-black text-white">Gestion des Produits</h2>
          <p className="text-slate-400 text-sm mt-0.5">Administrez l'inventaire et surveillez les stocks.</p>
        </div>
        <button
          onClick={openAddModal}
          className="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-indigo-500/20"
        >
          <Plus size={18} />
          <span>Ajouter un Produit</span>
        </button>
      </div>

      {lowStockCount > 0 && (
        <div className="bg-amber-500/10 border border-amber-500/30 text-amber-300 p-4 rounded-xl flex items-center gap-3">
          <AlertTriangle className="shrink-0 text-amber-500" size={24} />
          <div className="text-sm">
            <span className="font-bold">Attention stock critique :</span> {lowStockCount} produit(s) ont atteint le seuil minimum d'alerte.
          </div>
        </div>
      )}

      {/* Toolbar */}
      <div className="flex flex-col sm:flex-row gap-4 bg-slate-900/50 p-4 rounded-xl border border-slate-800">
        <div className="relative flex-1">
          <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
            <Search size={18} />
          </span>
          <input
            type="text"
            placeholder="Rechercher un produit ou scanner..."
            className="w-full pl-10 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
        <div className="flex gap-2 overflow-x-auto scrollbar-thin">
          {categories.map(cat => (
            <button
              key={cat}
              onClick={() => setSelectedCategory(cat)}
              className={`px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all ${
                selectedCategory === cat
                  ? 'bg-slate-800 text-indigo-400 border border-indigo-500/50'
                  : 'bg-slate-950 text-slate-400 border border-slate-850 hover:border-slate-700'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </div>

      {/* Table */}
      <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-slate-950 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800">
                <th className="px-6 py-4">Nom</th>
                <th className="px-6 py-4">Catégorie</th>
                <th className="px-6 py-4">Code-barres</th>
                <th className="px-6 py-4">Prix</th>
                <th className="px-6 py-4">Stock</th>
                <th className="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/50 text-sm text-slate-200">
              {filteredProducts.map(product => {
                const isLowStock = product.stock <= product.minStockAlert;
                return (
                  <tr key={product.id} className="hover:bg-slate-850/40 transition-colors">
                    <td className="px-6 py-4 font-semibold text-slate-100">{product.name}</td>
                    <td className="px-6 py-4 text-slate-400">
                      <span className="px-2 py-1 bg-slate-800 border border-slate-700 rounded-md text-xs">
                        {product.category}
                      </span>
                    </td>
                    <td className="px-6 py-4 font-mono text-xs text-slate-400">{product.barcode}</td>
                    <td className="px-6 py-4 font-bold text-white">{product.price.toFixed(2)} €</td>
                    <td className="px-6 py-4">
                      <span className={`font-bold ${isLowStock ? 'text-red-400' : 'text-slate-300'}`}>
                        {product.stock}
                      </span>
                      {isLowStock && (
                        <span className="ml-2 text-[10px] bg-red-500/10 border border-red-500/30 text-red-400 px-1.5 py-0.5 rounded animate-pulse">
                          Alerte
                        </span>
                      )}
                    </td>
                    <td className="px-6 py-4 text-right flex justify-end space-x-2">
                      <button
                        onClick={() => openEditModal(product)}
                        className="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-colors"
                      >
                        <Edit2 size={16} />
                      </button>
                      <button
                        onClick={() => deleteProduct(product.id)}
                        className="p-2 bg-slate-800 hover:bg-red-900/30 text-slate-400 hover:text-red-400 rounded-lg transition-colors"
                      >
                        <Trash2 size={16} />
                      </button>
                    </td>
                  </tr>
                );
              })}

              {filteredProducts.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-6 py-10 text-center text-slate-500">
                    <PackagePlus size={40} className="mx-auto stroke-1 mb-2 opacity-30" />
                    Aucun produit trouvé.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Add / Edit Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
          <div className="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden animate-scale">
            <div className="p-4 border-b border-slate-800 bg-slate-950 flex justify-between items-center">
              <h3 className="text-white font-bold flex items-center space-x-2">
                <FileText size={18} className="text-indigo-400" />
                <span>{editingProduct ? 'Modifier le produit' : 'Nouveau produit'}</span>
              </h3>
              <button
                onClick={() => setIsModalOpen(false)}
                className="text-slate-400 hover:text-white rounded-full p-1 transition-all"
              >
                ✕
              </button>
            </div>

            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nom du produit</label>
                <input
                  type="text"
                  required
                  className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Ex: Savon Liquide"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Prix (€)</label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value={price || ''}
                    onChange={(e) => setPrice(parseFloat(e.target.value) || 0)}
                  />
                </div>
                <div>
                  <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Stock initial</label>
                  <input
                    type="number"
                    min="0"
                    required
                    className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value={stock || ''}
                    onChange={(e) => setStock(parseInt(e.target.value) || 0)}
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Code-barres</label>
                <div className="flex gap-2">
                  <input
                    type="text"
                    required
                    className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono"
                    value={barcode}
                    onChange={(e) => setBarcode(e.target.value)}
                  />
                  <button
                    type="button"
                    onClick={() => setBarcode(generateBarcode())}
                    className="px-3 bg-slate-800 text-slate-300 text-xs rounded-lg hover:text-white hover:bg-slate-700 transition-all border border-slate-700"
                  >
                    Générer
                  </button>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catégorie</label>
                  <select
                    className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                  >
                    <option value="Alimentation">Alimentation</option>
                    <option value="Boissons">Boissons</option>
                    <option value="Entretien">Entretien</option>
                    <option value="Hygiène">Hygiène</option>
                    <option value="Divers">Divers</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Seuil d'alerte</label>
                  <input
                    type="number"
                    min="1"
                    required
                    className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    value={minStockAlert || ''}
                    onChange={(e) => setMinStockAlert(parseInt(e.target.value) || 0)}
                  />
                </div>
              </div>

              <div className="pt-4 flex gap-3">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="flex-1 py-2 text-center text-xs font-bold border border-slate-800 rounded-lg text-slate-400 hover:bg-slate-800 transition-all"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  className="flex-1 py-2 text-center text-xs font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 flex items-center justify-center space-x-1"
                >
                  <Check size={14} />
                  <span>Enregistrer</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
