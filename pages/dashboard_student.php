<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
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
    <title>Dashboard Étudiant — Binomy</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="/" class="navbar-brand">Bin<span>omy</span></a>
    <div class="navbar-links" style="align-items:center">
        <span style="color:var(--text-muted);font-size:.9rem">Bonjour, <strong><?= $userName ?></strong></span>

        <!-- Notification Bell -->
        <div style="position:relative">
            <button class="notif-btn" onclick="toggleNotifPanel()" title="Notifications">🔔
                <span class="notif-badge" id="notif-badge" hidden>0</span>
            </button>
            <div id="notif-panel" style="display:none;position:absolute;right:0;top:48px;width:320px;background:var(--white);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-lg);z-index:200;overflow:hidden">
                <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-weight:600;font-size:.9rem">Notifications</div>
                <div id="notif-list" style="max-height:320px;overflow-y:auto"></div>
            </div>
        </div>

        <button class="btn btn-outline btn-sm" onclick="handleLogout()">Déconnexion</button>
    </div>
</nav>

<div class="dashboard-layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-section-title">Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="showSection('home')"><span class="sidebar-icon">🏠</span> Accueil</a></li>
            <li><a href="#" onclick="showSection('students')"><span class="sidebar-icon">👥</span> Étudiants</a></li>
            <li><a href="#" onclick="showSection('requests')"><span class="sidebar-icon">📬</span> Demandes reçues</a></li>
            <li><a href="#" onclick="showSection('chat')"><span class="sidebar-icon">💬</span> Mes chats</a></li>
        </ul>
        <div class="sidebar-section-title" style="margin-top:16px">Logements</div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="showSection('listings')"><span class="sidebar-icon">🏡</span> Annonces</a></li>
        </ul>
        <div class="sidebar-section-title" style="margin-top:16px">Compte</div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="showSection('profile')"><span class="sidebar-icon">👤</span> Mon profil</a></li>
        </ul>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- SECTION : ACCUEIL -->
        <section id="section-home">
            <div class="page-title">Bonjour, <?= $userName ?> 👋</div>
            <p class="page-subtitle">Bienvenue sur Binomy. Voici votre résumé.</p>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-card-icon icon-purple">💌</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-pending">—</div>
                        <div class="stat-lbl">Demandes envoyées</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-green">🤝</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-matches">—</div>
                        <div class="stat-lbl">Matches actifs</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-blue">💬</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-messages">—</div>
                        <div class="stat-lbl">Messages non lus</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-orange">🏡</div>
                    <div class="stat-card-info">
                        <div class="stat-num">—</div>
                        <div class="stat-lbl">Annonces disponibles</div>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
                <div class="card">
                    <div class="card-header"><h3>Actions rapides</h3></div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <button class="btn btn-primary" onclick="showSection('students')">👥 Trouver un colocataire</button>
                        <button class="btn btn-outline" onclick="showSection('listings')">🏡 Voir les logements</button>
                        <button class="btn btn-outline" onclick="showSection('requests')">📬 Voir mes demandes</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Conseil du jour</h3></div>
                    <p style="color:var(--text-muted);font-size:.9rem;line-height:1.7">
                        💡 Complétez votre profil pour augmenter vos chances de trouver un colocataire compatible.
                        Un profil avec une bio et vos préférences obtient <strong>3× plus de demandes</strong>.
                    </p>
                    <button class="btn btn-outline btn-sm" style="margin-top:12px" onclick="showSection('profile')">
                        Compléter mon profil →
                    </button>
                </div>
            </div>
        </section>

        <!-- SECTION : ÉTUDIANTS -->
        <section id="section-students" hidden>
            <div class="page-title">Trouver un colocataire</div>
            <p class="page-subtitle">Parcourez les profils d'étudiants disponibles</p>

            <div class="card" style="margin-bottom:24px">
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    <div class="form-group" style="margin:0;flex:1;min-width:200px">
                        <label class="form-label">Filtrer par ville</label>
                        <input id="filter-city" type="text" class="form-control" placeholder="Ex: Tunis, Sfax...">
                    </div>
                    <button class="btn btn-primary" onclick="loadStudents()">🔍 Rechercher</button>
                </div>
            </div>

            <div class="students-grid" id="students-grid">
                <div class="spinner"></div>
            </div>
        </section>

        <!-- SECTION : DEMANDES REÇUES -->
        <section id="section-requests" hidden>
            <div class="page-title">Demandes reçues</div>
            <p class="page-subtitle">Acceptez ou refusez les demandes de colocation</p>
            <div id="requests-container"><div class="spinner"></div></div>
        </section>

        <!-- SECTION : CHAT -->
        <section id="section-chat" hidden>
            <div class="page-title">Mes conversations</div>
            <p class="page-subtitle">Chattez avec vos matches</p>

            <div class="chat-layout">
                <div class="chat-list" id="chat-list">
                    <div class="spinner"></div>
                </div>
                <div class="chat-area">
                    <div style="padding:16px;border-bottom:1px solid var(--border);font-weight:600" id="chat-partner-name">
                        Sélectionnez une conversation
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <div class="empty-state">
                            <div class="empty-state-icon">💬</div>
                            <h3>Aucune conversation sélectionnée</h3>
                        </div>
                    </div>
                    <form class="chat-input-area" id="chat-form">
                        <input id="chat-input" type="text" class="form-control" placeholder="Écrire un message...">
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- SECTION : LOGEMENTS -->
        <section id="section-listings" hidden>
            <div class="page-title">Annonces de logements</div>
            <p class="page-subtitle">Trouvez un logement adapté à votre budget</p>

            <div class="card" style="margin-bottom:24px">
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    <div class="form-group" style="margin:0;flex:1;min-width:150px">
                        <label class="form-label">Ville</label>
                        <input id="filter-city" type="text" class="form-control" placeholder="Tunis...">
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:150px">
                        <label class="form-label">Type</label>
                        <select id="filter-type" class="form-control">
                            <option value="">Tous</option>
                            <option value="chambre">Chambre</option>
                            <option value="studio">Studio</option>
                            <option value="appartement">Appartement</option>
                            <option value="maison">Maison</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:150px">
                        <label class="form-label">Prix max (TND)</label>
                        <input id="filter-price" type="number" class="form-control" placeholder="500">
                    </div>
                    <button class="btn btn-primary" onclick="loadListings()">🔍 Rechercher</button>
                </div>
            </div>

            <div class="listings-grid" id="listings-grid"></div>
            <div id="pagination"></div>

            <!-- Modal détail annonce -->
            <div class="modal-overlay" id="listing-modal">
                <div class="modal" style="max-width:600px">
                    <div class="modal-body"></div>
                    <button onclick="document.getElementById('listing-modal').classList.remove('open')"
                            style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:1.5rem;cursor:pointer">✕</button>
                </div>
            </div>
        </section>

        <!-- SECTION : PROFIL -->
        <section id="section-profile" hidden>
            <div class="page-title">Mon profil</div>
            <p class="page-subtitle">Complétez votre profil pour augmenter vos chances</p>

            <div id="profile-alert"></div>
            <div class="card">
                <form id="profile-form" onsubmit="saveProfile(event)">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input id="p-name" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Téléphone</label>
                            <input id="p-phone" type="text" class="form-control" placeholder="+216 XX XXX XXX">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville actuelle</label>
                            <input id="p-city" type="text" class="form-control" placeholder="Tunis, Sfax...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Disponible à partir de</label>
                            <input id="p-available" type="date" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Biographie</label>
                        <textarea id="p-bio" class="form-control" placeholder="Parlez de vous, de vos études, de vos centres d'intérêt..."></textarea>
                    </div>

                    <h3 style="margin:24px 0 16px;color:var(--primary)">Préférences de colocation</h3>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
                        <div class="form-group">
                            <label class="form-label">Budget min (TND)</label>
                            <input id="p-bmin" type="number" class="form-control" placeholder="200">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Budget max (TND)</label>
                            <input id="p-bmax" type="number" class="form-control" placeholder="600">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Propreté (1–5)</label>
                            <input id="p-clean" type="number" min="1" max="5" class="form-control" placeholder="3">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Rythme de vie</label>
                            <select id="p-schedule" class="form-control">
                                <option value="">-- Choisir --</option>
                                <option value="early_bird">🌅 Lève-tôt</option>
                                <option value="night_owl">🦉 Couche-tard</option>
                                <option value="flexible">🔄 Flexible</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Je cherche</label>
                            <select id="p-looking" class="form-control">
                                <option value="both">Les deux</option>
                                <option value="roommate">Un colocataire</option>
                                <option value="housing">Un logement</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex;gap:24px;margin-bottom:20px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input id="p-smoking" type="checkbox"> Fumeur
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input id="p-pets" type="checkbox"> Accepte les animaux
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Règles personnelles (allergies, habitudes…)</label>
                        <textarea id="p-rules" class="form-control" placeholder="Ex: Allergie aux chats, végétarien, prières 5 fois/jour..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
                </form>
            </div>
        </section>

    </main>
</div>

<!-- Hidden field for current user ID (used by chat.js) -->
<input type="hidden" id="current-user-id" value="<?= (int) $_SESSION['user_id'] ?>">

<script src="/assets/js/auth.js"></script>
<script src="/assets/js/notifications.js"></script>
<script src="/assets/js/matches.js"></script>
<script src="/assets/js/listings.js"></script>
<script src="/assets/js/chat.js"></script>
<script>
// ---- Section switcher ----
function showSection(name) {
    document.querySelectorAll('[id^="section-"]').forEach(el => el.hidden = true);
    document.querySelector(`#section-${name}`).hidden = false;
    document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));

    if (name === 'students') loadStudents();
    if (name === 'requests') loadRequests();
    if (name === 'chat')     loadMatches();
    if (name === 'listings') loadListings();
    if (name === 'profile')  loadProfile();
}

// ---- Profile load/save ----
async function loadProfile() {
    try {
        const res  = await fetch('/api/users/get_profile.php');
        const data = await res.json();
        if (!data.success) return;
        const p = data.data;
        document.getElementById('p-name').value     = p.name      || '';
        document.getElementById('p-phone').value    = p.phone     || '';
        document.getElementById('p-city').value     = p.city      || '';
        document.getElementById('p-bio').value      = p.bio       || '';
        document.getElementById('p-bmin').value     = p.budget_min || '';
        document.getElementById('p-bmax').value     = p.budget_max || '';
        document.getElementById('p-clean').value    = p.cleanliness || '';
        document.getElementById('p-available').value= p.available_from || '';
        document.getElementById('p-smoking').checked= p.smoking == 1;
        document.getElementById('p-pets').checked   = p.pets_ok  == 1;
        if (p.schedule) document.getElementById('p-schedule').value = p.schedule;
        if (p.looking_for) document.getElementById('p-looking').value = p.looking_for;
        document.getElementById('p-rules').value = p.personal_rules || '';
    } catch (_) {}
}

async function saveProfile(event) {
    event.preventDefault();
    const body = {
        name: document.getElementById('p-name').value,
        phone: document.getElementById('p-phone').value,
        city: document.getElementById('p-city').value,
        bio: document.getElementById('p-bio').value,
        budget_min: document.getElementById('p-bmin').value || null,
        budget_max: document.getElementById('p-bmax').value || null,
        cleanliness: document.getElementById('p-clean').value || null,
        available_from: document.getElementById('p-available').value || null,
        smoking: document.getElementById('p-smoking').checked ? 1 : 0,
        pets_ok: document.getElementById('p-pets').checked ? 1 : 0,
        schedule: document.getElementById('p-schedule').value,
        looking_for: document.getElementById('p-looking').value,
        personal_rules: document.getElementById('p-rules').value,
    };

    try {
        const res  = await fetch('/api/users/update_profile.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(body),
        });
        const data = await res.json();
        const el   = document.getElementById('profile-alert');
        el.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'error'}">${data.message}</div>`;
    } catch (_) {}
}
</script>
</body>
</html>
