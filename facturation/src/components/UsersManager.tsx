import React, { useState } from 'react';
import { useApp } from '../context/AppContext';
import { User, Role } from '../types';
import { UserPlus, ShieldAlert, Edit2, ToggleLeft, ToggleRight, UserCircle, Check } from 'lucide-react';

export const UsersManager: React.FC = () => {
  const { users, addUser, updateUser, currentUser } = useApp();
  
  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingUser, setEditingUser] = useState<User | null>(null);

  // Form State
  const [name, setName] = useState('');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [role, setRole] = useState<Role>('caissier');

  const openAddModal = () => {
    setEditingUser(null);
    setName('');
    setUsername('');
    setPassword('');
    setRole('caissier');
    setIsModalOpen(true);
  };

  const openEditModal = (user: User) => {
    setEditingUser(user);
    setName(user.name);
    setUsername(user.username);
    setPassword(user.password || '');
    setRole(user.role);
    setIsModalOpen(true);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || !username) return;

    if (editingUser) {
      updateUser({
        ...editingUser,
        name,
        username,
        password: password || editingUser.password,
        role
      });
    } else {
      addUser({
        name,
        username,
        password: password || '1234',
        role,
        isActive: true
      });
    }
    setIsModalOpen(false);
  };

  const toggleStatus = (user: User) => {
    if (user.id === currentUser?.id) {
      alert("Vous ne pouvez pas désactiver votre propre compte !");
      return;
    }
    updateUser({
      ...user,
      isActive: !user.isActive
    });
  };

  const getRoleBadge = (r: Role) => {
    switch (r) {
      case 'super_admin':
        return <span className="px-2 py-0.5 rounded text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20">Super Admin</span>;
      case 'manager':
        return <span className="px-2 py-0.5 rounded text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Manager</span>;
      case 'caissier':
        return <span className="px-2 py-0.5 rounded text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Caissier</span>;
    }
  };

  return (
    <div className="p-4 space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h2 className="text-2xl font-black text-white">Gestion Utilisateurs</h2>
          <p className="text-slate-400 text-sm mt-0.5">Créez et configurez les droits d'accès du personnel.</p>
        </div>
        <button
          onClick={openAddModal}
          className="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-lg shadow-indigo-500/20"
        >
          <UserPlus size={18} />
          <span>Créer un Compte</span>
        </button>
      </div>

      <div className="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-slate-950 text-slate-400 text-xs font-bold uppercase tracking-wider border-b border-slate-800">
                <th className="px-6 py-4">Nom Complet</th>
                <th className="px-6 py-4">Identifiant</th>
                <th className="px-6 py-4">Rôle</th>
                <th className="px-6 py-4">Statut</th>
                <th className="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800/50 text-sm text-slate-200">
              {users.map(user => (
                <tr key={user.id} className="hover:bg-slate-850/40 transition-colors">
                  <td className="px-6 py-4 font-semibold text-white flex items-center space-x-3">
                    <div className="p-1.5 bg-slate-800 rounded-lg text-slate-400">
                      <UserCircle size={18} />
                    </div>
                    <span>{user.name}</span>
                  </td>
                  <td className="px-6 py-4 font-mono text-slate-400">{user.username}</td>
                  <td className="px-6 py-4">{getRoleBadge(user.role)}</td>
                  <td className="px-6 py-4">
                    {user.isActive ? (
                      <span className="inline-flex items-center text-xs font-semibold text-green-400">
                        Actif
                      </span>
                    ) : (
                      <span className="inline-flex items-center text-xs font-semibold text-slate-500">
                        Inactif
                      </span>
                    )}
                  </td>
                  <td className="px-6 py-4 text-right flex justify-end space-x-2">
                    <button
                      onClick={() => openEditModal(user)}
                      className="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-colors"
                    >
                      <Edit2 size={16} />
                    </button>
                    <button
                      onClick={() => toggleStatus(user)}
                      className={`p-2 bg-slate-800 rounded-lg transition-colors ${
                        user.isActive 
                          ? 'text-green-400 hover:bg-green-900/20' 
                          : 'text-slate-500 hover:bg-slate-700'
                      }`}
                      title={user.isActive ? 'Désactiver le compte' : 'Activer le compte'}
                    >
                      {user.isActive ? <ToggleRight size={20} /> : <ToggleLeft size={20} />}
                    </button>
                  </td>
                </tr>
              ))}
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
                <ShieldAlert size={18} className="text-indigo-400" />
                <span>{editingUser ? 'Modifier l\'utilisateur' : 'Créer un utilisateur'}</span>
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
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nom Complet</label>
                <input
                  type="text"
                  required
                  className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Ex: Jean Dupont"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Identifiant de connexion</label>
                <input
                  type="text"
                  required
                  className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  placeholder="Ex: j.dupont"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                  Mot de passe {editingUser && <span className="text-slate-500 lowercase font-normal">(laisser vide pour inchangé)</span>}
                </label>
                <input
                  type="password"
                  required={!editingUser}
                  className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Rôle</label>
                <select
                  className="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  value={role}
                  onChange={(e) => setRole(e.target.value as Role)}
                >
                  <option value="caissier">Caissier</option>
                  <option value="manager">Manager</option>
                  <option value="super_admin">Super Administrateur</option>
                </select>
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
                  <span>Sauvegarder</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
