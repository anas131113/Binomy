<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /');
    exit;
}
$userName = htmlspecialchars($_SESSION['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Binomy</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="/" class="navbar-brand">Bin<span>omy</span></a>
    <div class="navbar-links">
        <span class="badge badge-danger">ADMIN</span>
        <span style="color:var(--text-muted);font-size:.9rem"><?= $userName ?></span>
        <button class="btn btn-outline btn-sm" onclick="handleLogout()">Déconnexion</button>
    </div>
</nav>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section-title">Administration</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="showSection('stats')"><span class="sidebar-icon">📊</span> Statistiques</a></li>
            <li><a href="#" onclick="showSection('users')"><span class="sidebar-icon">👥</span> Utilisateurs</a></li>
            <li><a href="#" onclick="showSection('listings')"><span class="sidebar-icon">🏡</span> Annonces</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <!-- STATS -->
        <section id="section-stats">
            <div class="page-title">Dashboard Administrateur</div>
            <p class="page-subtitle">Vue d'ensemble de la plateforme Binomy</p>
            <div id="admin-stats-container"><div class="spinner"></div></div>
        </section>

        <!-- USERS -->
        <section id="section-users" hidden>
            <div class="page-title">Gestion des utilisateurs</div>

            <div class="card" style="margin-bottom:20px">
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <input id="search-users" type="text" class="form-control" placeholder="Rechercher..." style="flex:1;min-width:200px">
                    <select id="filter-role" class="form-control" style="width:180px">
                        <option value="">Tous les rôles</option>
                        <option value="student">Étudiants</option>
                        <option value="locataire">Propriétaires</option>
                        <option value="admin">Admins</option>
                    </select>
                    <button class="btn btn-primary" onclick="loadUsers()">🔍 Chercher</button>
                </div>
            </div>

            <div class="table-wrap" id="users-table-wrap"><div class="spinner"></div></div>
        </section>

        <!-- LISTINGS ADMIN -->
        <section id="section-listings" hidden>
            <div class="page-title">Gestion des annonces</div>
            <div class="table-wrap" id="admin-listings-wrap"><div class="spinner"></div></div>
        </section>

    </main>
</div>

<script src="/assets/js/auth.js"></script>
<script>
document.addEventListener('DOMContentLoaded', loadStats);

function showSection(name) {
    document.querySelectorAll('[id^="section-"]').forEach(el => el.hidden = true);
    document.querySelector(`#section-${name}`).hidden = false;
    if (name === 'users')    loadUsers();
    if (name === 'listings') loadAdminListings();
}

// ---- STATS ----
async function loadStats() {
    const container = document.getElementById('admin-stats-container');
    try {
        const res  = await fetch('/api/admin/get_stats.php');
        const data = await res.json();
        if (!data.success) return;
        const s = data.data;

        container.innerHTML = `
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-card-icon icon-purple">👥</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_users}</div>
                        <div class="stat-lbl">Utilisateurs total</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-blue">🎓</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_students}</div>
                        <div class="stat-lbl">Étudiants</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-orange">🏠</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_locataires}</div>
                        <div class="stat-lbl">Propriétaires</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-green">📋</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_listings}</div>
                        <div class="stat-lbl">Annonces publiées</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-green">✅</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.available_listings}</div>
                        <div class="stat-lbl">Logements disponibles</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-purple">🤝</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_matches}</div>
                        <div class="stat-lbl">Matches créés</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-blue">💬</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.total_messages}</div>
                        <div class="stat-lbl">Messages échangés</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-orange">⏳</div>
                    <div class="stat-card-info">
                        <div class="stat-num">${s.pending_requests}</div>
                        <div class="stat-lbl">Demandes en attente</div>
                    </div>
                </div>
            </div>`;
    } catch(_) {
        container.innerHTML = '<div class="alert alert-error">Erreur de chargement des statistiques.</div>';
    }
}

// ---- USERS ----
async function loadUsers() {
    const wrap   = document.getElementById('users-table-wrap');
    const search = document.getElementById('search-users').value;
    const role   = document.getElementById('filter-role').value;
    wrap.innerHTML = '<div class="spinner"></div>';

    const params = new URLSearchParams({ page: 1 });
    if (search) params.set('q', search);
    if (role)   params.set('role', role);

    try {
        const res  = await fetch(`/api/admin/get_users.php?${params}`);
        const data = await res.json();
        if (!data.success || !data.data.users.length) {
            wrap.innerHTML = '<div class="empty-state" style="padding:40px"><h3>Aucun utilisateur trouvé</h3></div>';
            return;
        }

        wrap.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th>
                        <th>Ville</th><th>Statut</th><th>Inscrit le</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.data.users.map(u => `
                        <tr>
                            <td>#${u.id}</td>
                            <td><strong>${escHtml(u.name)}</strong></td>
                            <td>${escHtml(u.email)}</td>
                            <td><span class="badge ${roleBadge(u.role)}">${u.role}</span></td>
                            <td>${escHtml(u.city || '—')}</td>
                            <td><span class="badge ${u.is_active ? 'badge-success' : 'badge-danger'}">${u.is_active ? 'Actif' : 'Suspendu'}</span></td>
                            <td>${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
                            <td>
                                <button class="btn btn-sm ${u.is_active ? 'btn-danger' : 'btn-success'}"
                                    onclick="toggleUser(${u.id}, ${u.is_active ? 0 : 1})">
                                    ${u.is_active ? '🚫 Suspendre' : '✅ Réactiver'}
                                </button>
                            </td>
                        </tr>`).join('')}
                </tbody>
            </table>`;
    } catch(_) {
        wrap.innerHTML = '<div class="alert alert-error">Erreur de chargement.</div>';
    }
}

async function toggleUser(userId, activate) {
    if (!confirm(`Êtes-vous sûr ?`)) return;
    try {
        const res  = await fetch('/api/admin/suspend_user.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ user_id: userId, is_active: activate }),
        });
        const data = await res.json();
        alert(data.message);
        loadUsers();
    } catch(_) {}
}

function loadAdminListings() {
    const wrap = document.getElementById('admin-listings-wrap');
    wrap.innerHTML = '<div class="empty-state" style="padding:40px"><div class="empty-state-icon">🏡</div><h3>Liste des annonces</h3><p>Fonctionnalité disponible dans la version complète.</p></div>';
}

function roleBadge(role) {
    const m = { admin: 'badge-danger', student: 'badge-primary', locataire: 'badge-info' };
    return m[role] ?? 'badge-primary';
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>
