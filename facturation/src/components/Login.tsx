import React, { useState } from 'react';
import { useApp } from '../context/AppContext';
import { KeyRound, UserRound, ShieldCheck } from 'lucide-react';

export const Login: React.FC = () => {
  const { login } = useApp();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    const success = login(username, password);
    if (!success) {
      setError('Identifiant ou mot de passe incorrect.');
    }
  };

  const fillCredentials = (user: string, pass: string) => {
    setUsername(user);
    setPassword(pass);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 flex items-center justify-center p-4">
      <div className="bg-white/10 backdrop-blur-md rounded-2xl p-8 w-full max-w-md shadow-2xl border border-white/20">
        <div className="flex flex-col items-center mb-8">
          <div className="p-3 bg-indigo-500/20 rounded-full mb-3 text-indigo-400">
            <ShieldCheck size={40} className="animate-pulse" />
          </div>
          <h1 className="text-2xl font-bold text-white text-center">SYSTÈME DE FACTURATION</h1>
          <p className="text-slate-400 text-sm mt-1">Saisissez vos paramètres de connexion</p>
        </div>

        {error && (
          <div className="bg-red-500/20 text-red-300 border border-red-500/50 p-3 rounded-lg text-sm mb-6 text-center animate-shake">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-slate-300 text-sm font-medium mb-2" htmlFor="username">
              Identifiant
            </label>
            <div className="relative">
              <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <UserRound size={18} />
              </span>
              <input
                id="username"
                type="text"
                className="w-full pl-10 pr-4 py-3 bg-slate-950/50 border border-slate-700/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                placeholder="Ex: admin"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                required
              />
            </div>
          </div>

          <div>
            <label className="block text-slate-300 text-sm font-medium mb-2" htmlFor="password">
              Mot de passe
            </label>
            <div className="relative">
              <span className="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <KeyRound size={18} />
              </span>
              <input
                id="password"
                type="password"
                className="w-full pl-10 pr-4 py-3 bg-slate-950/50 border border-slate-700/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
          </div>

          <button
            type="submit"
            className="w-full py-3 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-semibold rounded-lg shadow-lg shadow-indigo-500/30 active:scale-[0.98] transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Se Connecter
          </button>
        </form>

        <div className="mt-8 border-t border-slate-700/50 pt-4">
          <p className="text-slate-400 text-xs text-center mb-3">Comptes de test rapides :</p>
          <div className="grid grid-cols-3 gap-2">
            <button
              onClick={() => fillCredentials('caissier', 'caissier123')}
              className="px-2 py-1.5 text-xs bg-slate-800 text-slate-300 rounded hover:bg-slate-700 hover:text-white transition-all text-center"
            >
              Caissier
            </button>
            <button
              onClick={() => fillCredentials('manager', 'manager123')}
              className="px-2 py-1.5 text-xs bg-slate-800 text-slate-300 rounded hover:bg-slate-700 hover:text-white transition-all text-center"
            >
              Manager
            </button>
            <button
              onClick={() => fillCredentials('admin', 'admin123')}
              className="px-2 py-1.5 text-xs bg-slate-800 text-slate-300 rounded hover:bg-slate-700 hover:text-white transition-all text-center"
            >
              Admin
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
