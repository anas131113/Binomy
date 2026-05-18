/**
 * BINOMY — admin.js
 * Dashboard administrateur.
 */

// ============================================================
// Statistiques
// ============================================================
async function loadAdminStats() {
    try {
        const res = await fetch(apiUrl('api/admin/get_stats.php'));
        const data = await res.json();
        if (!data.success) return;
        const s = data.data;

        const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v ?? '—'; };
        set('st-users', s.total_users);
        set('st-students', s.total_students);
        set('st-locataires', s.total_locataires);
        set('st-listings', s.total_listings);
        set('st-available', s.available_listings);
        set('st-matches', s.total_matches);
        set('st-messages', s.total_messages);
        set('st-pending', s.pending_requests);

        renderRegistrationsChart(s.registrations_week);
        renderRolesChart(s.total_students, s.total_locataires, s.total_users);
    } catch (_) { showToast('Erreur lors du chargement des statistiques.', 'error'); }
}

function renderRegistrationsChart(weekData) {
    const container = document.getElementById('registrations-chart');
    if (!container) return;
    if (!weekData?.length) { container.innerHTML = '<p class="text-muted" style="font-size:.875rem">Aucune inscription cette semaine</p>'; return; }
    const max = Math.max(...weekData.map(d => d.count), 1);
    container.innerHTML = weekData.map(d => {
        const h = Math.round((d.count / max) * 70);
        const label = new Date(d.day).toLocaleDateString('fr-FR', { weekday: 'short' });
        return `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
            <span style="font-size:.7rem;font-weight:600;color:var(--primary)">${d.count}</span>
            <div style="width:100%;background:var(--primary);border-radius:4px 4px 0 0;height:${h}px;opacity:.85;min-height:4px"></div>
            <span style="font-size:.7rem;color:var(--text-muted)">${label}</span>
        </div>`;
    }).join('');
}

function renderRolesChart(students, locataires, total) {
    const container = document.getElementById('roles-chart');
    if (!container) return;
    const admins = total - students - locataires;
    const roles = [
        { label: 'Étudiants', count: students, color: 'var(--primary)' },
        { label: 'Propriétaires', count: locataires, color: 'var(--accent)' },
        { label: 'Admins', count: admins, color: 'var(--danger)' },
    ];
    container.innerHTML = roles.map(r => {
        const pct = total > 0 ? Math.round((r.count / total) * 100) : 0;
        return `<div>
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:.875rem">
                <span>${r.label}</span><span><strong>${r.count}</strong> (${pct}%)</span>
            </div>
            <div style="background:var(--border);border-radius:999px;height:8px">
                <div style="background:${r.color};width:${pct}%;height:8px;border-radius:999px;transition:width .5s"></div>
            </div>
        </div>`;
    }).join('');
}

// ============================================================
// Utilisateurs
// ============================================================
let usersCurrentPage = 1;

async function loadUsers(page = 1) {
    usersCurrentPage = page;
    const search = document.getElementById('user-search')?.value.trim() ?? '';
    const role = document.getElementById('user-role-filter')?.value ?? '';
    const params = new URLSearchParams({ page });
    if (search) params.set('q', search);
    if (role) params.set('role', role);

    const container = document.getElementById('users-table-container');
    if (!container) return;
    container.innerHTML = '<div class="spinner"></div>';

    try {
        const res = await fetch(apiUrl('api/admin/get_users.php?' + params));
        const data = await res.json();

        if (!data.success || !data.data.users.length) {
            container.innerHTML = `<div class="empty-state"><div class="empty-state-icon">👥</div><h3>Aucun utilisateur trouvé</h3></div>`;
            return;
        }
        renderUsersTable(data.data.users);
        renderUsersPagination(data.data.page, data.data.totalPages);
    } catch (_) { container.innerHTML = '<p class="text-muted text-center">Erreur réseau.</p>'; }
}

function renderUsersTable(users) {
    const container = document.getElementById('users-table-container');
    if (!container) return;
    const roleBadge = { student: 'badge-primary', locataire: 'badge-info', admin: 'badge-danger' };
    container.innerHTML = `
        <div class="table-wrap"><table>
            <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Ville</th><th>Statut</th><th>Inscrit le</th><th>Actions</th></tr></thead>
            <tbody>${users.map(u => `
                <tr>
                    <td>#${u.id}</td>
                    <td><strong>${escHtml(u.name)}</strong></td>
                    <td>${escHtml(u.email)}</td>
                    <td><span class="badge ${roleBadge[u.role] || 'badge-primary'}">${u.role}</span></td>
                    <td>${escHtml(u.city || '—')}</td>
                    <td><span class="badge ${u.is_active ? 'badge-success' : 'badge-danger'}">${u.is_active ? 'Actif' : 'Suspendu'}</span></td>
                    <td>${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
                    <td>${u.role !== 'admin'
            ? `<button class="btn btn-sm ${u.is_active ? 'btn-danger' : 'btn-success'}"
                               onclick="toggleUserStatus(${u.id},${u.is_active ? 0 : 1})">
                               ${u.is_active ? '🚫 Suspendre' : '✅ Réactiver'}</button>`
            : '<span class="text-muted" style="font-size:.8rem">—</span>'}</td>
                </tr>`).join('')}
            </tbody>
        </table></div>`;
}

async function toggleUserStatus(userId, newStatus) {
    if (!confirm(newStatus === 0 ? 'Suspendre ce compte ?' : 'Réactiver ce compte ?')) return;
    try {
        const res = await fetch(apiUrl('api/admin/suspend_user.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId, is_active: newStatus }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) loadUsers(usersCurrentPage);
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

function renderUsersPagination(page, total) {
    const el = document.getElementById('users-pagination');
    if (!el || total <= 1) { if (el) el.innerHTML = ''; return; }
    el.innerHTML = `<div style="display:flex;gap:8px;justify-content:center;margin-top:24px">
        ${page > 1 ? `<button class="btn btn-outline btn-sm" onclick="loadUsers(${page - 1})">← Précédent</button>` : ''}
        <span style="padding:8px 16px;color:var(--text-muted)">Page ${page} / ${total}</span>
        ${page < total ? `<button class="btn btn-outline btn-sm" onclick="loadUsers(${page + 1})">Suivant →</button>` : ''}
    </div>`;
}

// ============================================================
// Annonces admin
// ============================================================
let adminListingsPage = 1;

async function loadAdminListings(page = 1) {
    adminListingsPage = page;
    const container = document.getElementById('admin-listings-container');
    if (!container) return;
    container.innerHTML = '<div class="spinner"></div>';
    try {
        const res = await fetch(apiUrl('api/listings/get_all.php?page=' + page));
        const data = await res.json();

        if (!data.success || !data.data.listings.length) {
            container.innerHTML = `<div class="empty-state"><div class="empty-state-icon">🏡</div><h3>Aucune annonce</h3></div>`;
            return;
        }
        renderAdminListingsTable(data.data.listings);
        renderAdminListingsPagination(data.data.page, data.data.totalPages);
    } catch (_) { container.innerHTML = '<p class="text-muted text-center">Erreur réseau.</p>'; }
}

function renderAdminListingsTable(listings) {
    const container = document.getElementById('admin-listings-container');
    if (!container) return;
    container.innerHTML = `
        <div class="table-wrap"><table>
            <thead><tr><th>ID</th><th>Titre</th><th>Propriétaire</th><th>Ville</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>${listings.map(l => `
                <tr>
                    <td>#${l.id}</td>
                    <td><strong>${escHtml(l.title)}</strong></td>
                    <td>${escHtml(l.owner_name)}</td>
                    <td>📍 ${escHtml(l.city)}</td>
                    <td>${parseFloat(l.price).toLocaleString('fr-TN')} TND</td>
                    <td><span class="badge ${l.status === 'available' ? 'badge-success' : 'badge-warning'}">
                        ${l.status === 'available' ? 'Disponible' : 'Louée'}</span></td>
                    <td><button class="btn btn-danger btn-sm" onclick="adminDeleteListing(${l.id})">🗑️ Supprimer</button></td>
                </tr>`).join('')}
            </tbody>
        </table></div>`;
}

async function adminDeleteListing(id) {
    if (!confirm('Supprimer cette annonce ? (Modération)')) return;
    try {
        const res = await fetch(apiUrl('api/listings/delete.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) loadAdminListings(adminListingsPage);
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

function renderAdminListingsPagination(page, total) {
    const el = document.getElementById('admin-listings-pagination');
    if (!el || total <= 1) { if (el) el.innerHTML = ''; return; }
    el.innerHTML = `<div style="display:flex;gap:8px;justify-content:center;margin-top:24px">
        ${page > 1 ? `<button class="btn btn-outline btn-sm" onclick="loadAdminListings(${page - 1})">← Précédent</button>` : ''}
        <span style="padding:8px 16px;color:var(--text-muted)">Page ${page} / ${total}</span>
        ${page < total ? `<button class="btn btn-outline btn-sm" onclick="loadAdminListings(${page + 1})">Suivant →</button>` : ''}
    </div>`;
}

// ============================================================
// Helpers
// ============================================================
function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str; return d.innerHTML;
}
