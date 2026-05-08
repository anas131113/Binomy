<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'locataire') {
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
    <title>Dashboard Propriétaire — Binomy</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="/" class="navbar-brand">Bin<span>omy</span></a>
    <div class="navbar-links">
        <span style="color:var(--text-muted);font-size:.9rem">Bonjour, <strong><?= $userName ?></strong></span>
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
    <aside class="sidebar">
        <div class="sidebar-section-title">Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="showSection('home')"><span class="sidebar-icon">🏠</span> Accueil</a></li>
            <li><a href="#" onclick="showSection('my-listings')"><span class="sidebar-icon">📋</span> Mes annonces</a></li>
            <li><a href="#" onclick="showSection('new-listing')"><span class="sidebar-icon">➕</span> Publier une annonce</a></li>
            <li><a href="#" onclick="showSection('messages')"><span class="sidebar-icon">💬</span> Messages reçus</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <!-- ACCUEIL -->
        <section id="section-home">
            <div class="page-title">Bonjour, <?= $userName ?> 🏠</div>
            <p class="page-subtitle">Gérez vos annonces et vos messages</p>

            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-card-icon icon-purple">📋</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-listings">—</div>
                        <div class="stat-lbl">Mes annonces</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-green">✅</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-available">—</div>
                        <div class="stat-lbl">Disponibles</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-icon icon-orange">💬</div>
                    <div class="stat-card-info">
                        <div class="stat-num" id="stat-msgs">—</div>
                        <div class="stat-lbl">Messages non lus</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Démarrer</h3></div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="showSection('new-listing')">➕ Publier une annonce</button>
                    <button class="btn btn-outline" onclick="showSection('my-listings')">📋 Voir mes annonces</button>
                    <button class="btn btn-outline" onclick="showSection('messages')">💬 Voir mes messages</button>
                </div>
            </div>
        </section>

        <!-- MES ANNONCES -->
        <section id="section-my-listings" hidden>
            <div class="page-title">Mes annonces</div>
            <div id="my-listings-container"><div class="spinner"></div></div>
        </section>

        <!-- NOUVELLE ANNONCE -->
        <section id="section-new-listing" hidden>
            <div class="page-title">Publier une annonce</div>
            <p class="page-subtitle">Remplissez les informations de votre logement</p>

            <div class="card">
                <div id="new-listing-alert"></div>
                <form id="new-listing-form" onsubmit="submitListing(event)" enctype="multipart/form-data">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Titre de l'annonce *</label>
                            <input name="title" type="text" class="form-control" placeholder="Ex: Studio meublé proche faculté de droit" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type de logement *</label>
                            <select name="type" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <option value="chambre">Chambre</option>
                                <option value="studio">Studio</option>
                                <option value="appartement">Appartement</option>
                                <option value="maison">Maison</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prix mensuel (TND) *</label>
                            <input name="price" type="number" step="0.01" min="0" class="form-control" placeholder="350" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville *</label>
                            <input name="city" type="text" class="form-control" placeholder="Tunis" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input name="address" type="text" class="form-control" placeholder="Rue, quartier...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre de pièces</label>
                            <input name="rooms" type="number" min="1" class="form-control" placeholder="2">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Surface (m²)</label>
                            <input name="surface" type="number" min="1" class="form-control" placeholder="45">
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Décrivez votre logement en détail : équipements, proximité des transports, charges incluses..." required></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Photos (max 5, formats JPG/PNG/WebP, max 5MB chacune)</label>
                            <input name="images[]" type="file" class="form-control" accept="image/jpeg,image/png,image/webp" multiple>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">📤 Publier l'annonce</button>
                </form>
            </div>
        </section>

        <!-- MESSAGES -->
        <section id="section-messages" hidden>
            <div class="page-title">Messages reçus</div>
            <p class="page-subtitle">Conversations avec les étudiants intéressés</p>
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <h3>Messagerie</h3>
                <p>Les messages des étudiants s'afficheront ici.</p>
            </div>
        </section>

    </main>
</div>

<script src="/assets/js/auth.js"></script>
<script src="/assets/js/notifications.js"></script>
<script>
function showSection(name) {
    document.querySelectorAll('[id^="section-"]').forEach(el => el.hidden = true);
    document.querySelector(`#section-${name}`).hidden = false;
    if (name === 'my-listings') loadMyListings();
}

async function loadMyListings() {
    const container = document.getElementById('my-listings-container');
    container.innerHTML = '<div class="spinner"></div>';

    // Appel vers un endpoint dédié "mes annonces" (utilise la session)
    try {
        const res  = await fetch('/api/listings/get_all.php?owner=me');
        const data = await res.json();
        // Pour la démo on affiche simplement un message
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📋</div><h3>Aucune annonce publiée</h3><p>Publiez votre première annonce !</p></div>';
    } catch(_) {}
}

async function submitListing(event) {
    event.preventDefault();
    const form    = document.getElementById('new-listing-form');
    const formData = new FormData(form);
    const alertEl = document.getElementById('new-listing-alert');

    try {
        const res  = await fetch('/api/listings/create.php', { method: 'POST', body: formData });
        const data = await res.json();
        alertEl.innerHTML = `<div class="alert alert-${data.success ? 'success' : 'error'}">${data.message}</div>`;
        if (data.success) { form.reset(); setTimeout(() => showSection('my-listings'), 1200); }
    } catch (_) {
        alertEl.innerHTML = '<div class="alert alert-error">Erreur réseau.</div>';
    }
}
</script>
</body>
</html>
