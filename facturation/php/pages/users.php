<!-- Gestion des comptes utilisateurs (super_admin uniquement) -->
<div class="p-6">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
      <h2 class="text-2xl font-black text-white">Gestion des Comptes</h2>
      <p class="text-slate-400 text-sm mt-0.5">Créez et gérez les accès utilisateurs</p>
    </div>
    <button onclick="openUserModal()"
      class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Nouvel utilisateur
    </button>
  </div>

  <div class="overflow-x-auto rounded-xl border border-slate-800">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-900 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
          <th class="px-4 py-3">Nom</th>
          <th class="px-4 py-3">Identifiant</th>
          <th class="px-4 py-3">Rôle</th>
          <th class="px-4 py-3">Statut</th>
          <th class="px-4 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody id="users-tbody" class="divide-y divide-slate-800/50"></tbody>
    </table>
  </div>
</div>

<!-- ── MODAL UTILISATEUR ─────────────────────────────────────────────────── -->
<div id="user-modal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
  <div class="bg-slate-900 rounded-2xl w-full max-w-md border border-slate-800 shadow-2xl">
    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
      <h3 id="user-modal-title" class="text-white font-bold text-lg">Nouvel utilisateur</h3>
      <button onclick="closeUserModal()" class="text-slate-400 hover:text-white transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="user-form" class="p-5 space-y-4">
      <input type="hidden" id="u-edit-id">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nom complet *</label>
        <input id="u-name" type="text" required placeholder="Prénom Nom"
          class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nom d'utilisateur *</label>
        <input id="u-username" type="text" required placeholder="login"
          class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">
          Mot de passe <span id="pwd-hint" class="text-slate-600 font-normal">(laisser vide = inchangé)</span>
        </label>
        <input id="u-password" type="password" placeholder="••••••••"
          class="w-full bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Rôle *</label>
        <select id="u-role" class="w-full bg-slate-800 border border-slate-700 text-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
          <option value="caissier">Caissier</option>
          <option value="manager">Manager</option>
          <option value="super_admin">Super Administrateur</option>
        </select>
      </div>
      <div id="u-error" class="hidden p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-xs"></div>
      <div class="flex gap-3 pt-2">
        <button type="button" onclick="closeUserModal()"
          class="flex-1 py-2.5 border border-slate-700 text-slate-400 rounded-xl font-semibold text-sm hover:bg-slate-800 transition-all">Annuler</button>
        <button type="submit"
          class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-sm transition-all">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
let users = [];

async function loadUsers() {
  const res = await fetch('api/users.php');
  users     = await res.json();
  renderUsers();
}

const roleLabels = { super_admin: 'Super Admin', manager: 'Manager', caissier: 'Caissier' };
const roleColors = {
  super_admin: 'bg-purple-500/10 text-purple-400 border-purple-500/30',
  manager:     'bg-blue-500/10 text-blue-400 border-blue-500/30',
  caissier:    'bg-slate-700 text-slate-300 border-slate-600',
};

function renderUsers() {
  document.getElementById('users-tbody').innerHTML = users.map(u => `
    <tr class="bg-slate-900/30 hover:bg-slate-800/30 transition-colors">
      <td class="px-4 py-3 font-semibold text-slate-200">${u.name}</td>
      <td class="px-4 py-3 font-mono text-slate-400 text-xs">${u.username}</td>
      <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full border text-xs font-semibold ${roleColors[u.role]||''}">
          ${roleLabels[u.role]||u.role}
        </span>
      </td>
      <td class="px-4 py-3">
        <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${u.is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400'}">
          ${u.is_active ? 'Actif' : 'Inactif'}
        </span>
      </td>
      <td class="px-4 py-3 text-right">
        <div class="flex justify-end gap-2">
          <button onclick="openUserModal('${u.id}')"
            class="p-1.5 text-slate-400 hover:text-indigo-400 rounded-lg hover:bg-slate-800 transition-all" title="Modifier">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </button>
          <button onclick="toggleUser('${u.id}', ${u.is_active})"
            class="p-1.5 text-slate-400 ${u.is_active ? 'hover:text-amber-400' : 'hover:text-green-400'} rounded-lg hover:bg-slate-800 transition-all"
            title="${u.is_active ? 'Désactiver' : 'Réactiver'}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="${u.is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/>
            </svg>
          </button>
        </div>
      </td>
    </tr>`).join('');
}

function openUserModal(id = null) {
  const errEl = document.getElementById('u-error');
  errEl.classList.add('hidden');
  document.getElementById('pwd-hint').classList.add('hidden');

  if (id) {
    const u = users.find(u => u.id === id);
    document.getElementById('user-modal-title').textContent = 'Modifier l\'utilisateur';
    document.getElementById('u-edit-id').value   = u.id;
    document.getElementById('u-name').value      = u.name;
    document.getElementById('u-username').value  = u.username;
    document.getElementById('u-password').value  = '';
    document.getElementById('u-role').value      = u.role;
    document.getElementById('pwd-hint').classList.remove('hidden');
  } else {
    document.getElementById('user-modal-title').textContent = 'Nouvel utilisateur';
    document.getElementById('user-form').reset();
    document.getElementById('u-edit-id').value = '';
  }
  document.getElementById('user-modal').classList.remove('hidden');
}

function closeUserModal() {
  document.getElementById('user-modal').classList.add('hidden');
}

document.getElementById('user-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const id    = document.getElementById('u-edit-id').value;
  const errEl = document.getElementById('u-error');
  errEl.classList.add('hidden');

  const body = {
    name     : document.getElementById('u-name').value.trim(),
    username : document.getElementById('u-username').value.trim(),
    password : document.getElementById('u-password').value,
    role     : document.getElementById('u-role').value,
  };

  const method = id ? 'PUT' : 'POST';
  const url    = id ? `api/users.php?id=${id}` : 'api/users.php';
  const res    = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
  const data   = await res.json();

  if (!res.ok) { errEl.textContent = data.error; errEl.classList.remove('hidden'); return; }

  if (id) {
    const idx = users.findIndex(u => u.id === id);
    if (idx !== -1) users[idx] = data;
  } else {
    users.push(data);
  }
  renderUsers();
  closeUserModal();
});

async function toggleUser(id, isActive) {
  const res = await fetch(`api/users.php?id=${id}`, {
    method: 'PUT',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({ is_active: !isActive })
  });
  if (res.ok) {
    const data = await res.json();
    const idx  = users.findIndex(u => u.id === id);
    if (idx !== -1) users[idx] = data;
    renderUsers();
  }
}

loadUsers();
</script>
