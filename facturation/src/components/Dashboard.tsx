import React, { useState } from 'react';
import { useApp } from '../context/AppContext';
import { POS } from './POS';
import { Products } from './Products';
import { InvoicesList } from './InvoicesList';
import { UsersManager } from './UsersManager';
import { LogOut, ShoppingCart, Package, FileText, Users, Shield, User, HardDrive } from 'lucide-react';
import { PHPSourceView } from './PHPSourceView';

export const Dashboard: React.FC = () => {
  const { currentUser, logout } = useApp();
  const [activeTab, setActiveTab] = useState<'pos' | 'products' | 'invoices' | 'users' | 'php'>('pos');

  if (!currentUser) return null;

  const role = currentUser.role;

  const canAccessPOS = true; // All roles can sell
  const canAccessProducts = role === 'manager' || role === 'super_admin';
  const canAccessInvoices = role === 'manager' || role === 'super_admin';
  const canAccessUsers = role === 'super_admin';

  return (
    <div className="min-h-screen bg-slate-950 flex flex-col">
      {/* Top Header */}
      <header className="bg-slate-900 border-b border-slate-850 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div className="flex items-center space-x-3">
          <div className="p-2 bg-indigo-600/20 rounded-xl text-indigo-400">
            <Shield size={24} />
          </div>
          <div>
            <h1 className="text-xl font-black text-white tracking-wide">FACTURATION PRO</h1>
            <p className="text-[10px] text-indigo-400 font-semibold tracking-widest uppercase">
              {role === 'super_admin' ? 'Super Administrateur' : role === 'manager' ? 'Manager' : 'Caissier'}
            </p>
          </div>
        </div>

        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-2.5 bg-slate-800/50 px-3.5 py-1.5 rounded-xl border border-slate-800">
            <User size={16} className="text-slate-400" />
            <span className="text-slate-200 text-sm font-semibold">{currentUser.name}</span>
          </div>
          
          <button
            onClick={logout}
            className="p-2.5 bg-slate-800 hover:bg-red-950/40 text-slate-400 hover:text-red-400 rounded-xl border border-slate-700/50 transition-all"
            title="Déconnexion"
          >
            <LogOut size={18} />
          </button>
        </div>
      </header>

      <div className="flex-1 flex flex-col lg:flex-row">
        {/* Navigation Sidebar */}
        <nav className="bg-slate-900/60 lg:w-64 border-b lg:border-b-0 lg:border-r border-slate-850 p-4 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-x-visible">
          {canAccessPOS && (
            <button
              onClick={() => setActiveTab('pos')}
              className={`flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all ${
                activeTab === 'pos'
                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
              }`}
            >
              <ShoppingCart size={18} />
              <span className="hidden sm:inline">Facturation / POS</span>
            </button>
          )}

          {canAccessProducts && (
            <button
              onClick={() => setActiveTab('products')}
              className={`flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all ${
                activeTab === 'products'
                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
              }`}
            >
              <Package size={18} />
              <span className="hidden sm:inline">Produits / Stocks</span>
            </button>
          )}

          {canAccessInvoices && (
            <button
              onClick={() => setActiveTab('invoices')}
              className={`flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all ${
                activeTab === 'invoices'
                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
              }`}
            >
              <FileText size={18} />
              <span className="hidden sm:inline">Factures / Rapports</span>
            </button>
          )}

          {canAccessUsers && (
            <button
              onClick={() => setActiveTab('users')}
              className={`flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all ${
                activeTab === 'users'
                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
              }`}
            >
              <Users size={18} />
              <span className="hidden sm:inline">Gérer Comptes</span>
            </button>
          )}

          <button
            onClick={() => setActiveTab('php')}
            className={`flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all ${
              activeTab === 'php'
                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40'
            }`}
          >
            <HardDrive size={18} />
            <span className="hidden sm:inline">Code PHP (TP)</span>
          </button>
        </nav>

        {/* Content View */}
        <main className="flex-1 bg-slate-950/30 overflow-y-auto">
          {activeTab === 'pos' && canAccessPOS && <POS />}
          {activeTab === 'products' && canAccessProducts && <Products />}
          {activeTab === 'invoices' && canAccessInvoices && <InvoicesList />}
          {activeTab === 'users' && canAccessUsers && <UsersManager />}
          {activeTab === 'php' && <PHPSourceView />}
        </main>
      </div>
    </div>
  );
};
