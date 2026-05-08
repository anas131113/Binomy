<?php
// Prototype suivi — données statiques
$students = [
    ['name'=>'Ahmed Trabelsi',   'city'=>'Tunis',   'budget'=>'300–500', 'schedule'=>'Lève-tôt',   'smoking'=>false, 'pets'=>false, 'clean'=>4, 'bio'=>'Étudiant en informatique à l\'ENIT. Calme, sérieux, cherche colocataire sympa.'],
    ['name'=>'Mariam Chaouachi', 'city'=>'Sfax',    'budget'=>'200–400', 'schedule'=>'Flexible',    'smoking'=>false, 'pets'=>true,  'clean'=>3, 'bio'=>'En master gestion à Sfax. Sociable, aime la cuisine.'],
    ['name'=>'Youssef Mejri',    'city'=>'Sousse',  'budget'=>'250–450', 'schedule'=>'Couche-tard', 'smoking'=>true,  'pets'=>false, 'clean'=>2, 'bio'=>'Ingénieur génie civil. Cherche quelqu\'un de tolérant.'],
    ['name'=>'Sarra Hamdi',      'city'=>'Tunis',   'budget'=>'350–600', 'schedule'=>'Lève-tôt',   'smoking'=>false, 'pets'=>false, 'clean'=>5, 'bio'=>'Étudiante en médecine. Très organisée, cherche colocataire sérieuse.'],
    ['name'=>'Amine Bouazizi',   'city'=>'Monastir','budget'=>'200–350', 'schedule'=>'Flexible',    'smoking'=>false, 'pets'=>true,  'clean'=>3, 'bio'=>'Licence économie. Sportif, sort souvent le week-end.'],
    ['name'=>'Rania Kasmi',      'city'=>'Bizerte', 'budget'=>'300–500', 'schedule'=>'Couche-tard', 'smoking'=>false, 'pets'=>false, 'clean'=>4, 'bio'=>'Master droit. Studieuse, cherche ambiance calme.'],
];

$listings = [
    ['title'=>'Studio meublé près ENIT',           'type'=>'Studio',      'city'=>'Tunis',   'price'=>'450', 'rooms'=>1, 'surface'=>28, 'owner'=>'Kamel Gharbi'],
    ['title'=>'Chambre dans appart 3 pièces',       'type'=>'Chambre',     'city'=>'Sfax',    'price'=>'250', 'rooms'=>1, 'surface'=>15, 'owner'=>'Hassen Jelassi'],
    ['title'=>'Appartement F2 proche fac',          'type'=>'Appartement', 'city'=>'Sousse',  'price'=>'600', 'rooms'=>2, 'surface'=>55, 'owner'=>'Sonia Maaloul'],
    ['title'=>'Studio tout équipé centre-ville',    'type'=>'Studio',      'city'=>'Monastir','price'=>'380', 'rooms'=>1, 'surface'=>32, 'owner'=>'Nizar Fakhfakh'],
];
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

<nav class="navbar">
    <a href="/" class="navbar-brand">Bin<span>omy</span></a>
    <div class="navbar-links" style="align-items:center">
        <span style="color:var(--text-muted);font-size:.9rem">Bonjour, <strong>Ahmed Trabelsi</strong></span>
        <span class="badge badge-primary">Étudiant</span>
        <button class="notif-btn" onclick="toggleNotif()">🔔
            <span class="notif-badge">3</span>
        </button>
        <a href="/" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section-title">Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="show('home')">       <span class="sidebar-icon">🏠</span> Accueil</a></li>
            <li><a href="#" onclick="show('students')">                  <span class="sidebar-icon">👥</span> Étudiants</a></li>
            <li><a href="#" onclick="show('requests')">                  <span class="sidebar-icon">📬</span> Demandes reçues</a></li>
            <li><a href="#" onclick="show('chat')">                      <span class="sidebar-icon">💬</span> Mes chats</a></li>
        </ul>
        <div class="sidebar-section-title" style="margin-top:16px">Logements</div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="show('listings')">                  <span class="sidebar-icon">🏡</span> Annonces</a></li>
        </ul>
        <div class="sidebar-section-title" style="margin-top:16px">Compte</div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="show('profile')">                   <span class="sidebar-icon">👤</span> Mon profil</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <!-- ACCUEIL -->
        <div id="s-home">
            <div class="page-title">Bonjour, Ahmed 👋</div>
            <p class="page-subtitle">Voici votre résumé d'activité sur Binomy.</p>
            <div class="stats-cards">
                <div class="stat-card"><div class="stat-card-icon icon-purple">💌</div><div class="stat-card-info"><div class="stat-num">2</div><div class="stat-lbl">Demandes envoyées</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-green">🤝</div><div class="stat-card-info"><div class="stat-num">1</div><div class="stat-lbl">Match actif</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-blue">💬</div><div class="stat-card-info"><div class="stat-num">3</div><div class="stat-lbl">Messages non lus</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-orange">🏡</div><div class="stat-card-info"><div class="stat-num">12</div><div class="stat-lbl">Annonces disponibles</div></div></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
                <div class="card">
                    <div class="card-header"><h3>Actions rapides</h3></div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <button class="btn btn-primary" onclick="show('students')">👥 Trouver un colocataire</button>
                        <button class="btn btn-outline" onclick="show('listings')">🏡 Voir les logements</button>
                        <button class="btn btn-outline" onclick="show('requests')">📬 Voir mes demandes</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Dernières notifications</h3></div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <div style="padding:10px;background:var(--primary-l);border-radius:8px;font-size:.875rem">
                            🤝 <strong>Mariam Chaouachi</strong> a accepté votre demande !
                            <div style="color:var(--text-muted);font-size:.75rem;margin-top:4px">Il y a 2 heures</div>
                        </div>
                        <div style="padding:10px;background:var(--bg);border-radius:8px;font-size:.875rem">
                            💬 Nouveau message de <strong>Youssef Mejri</strong>
                            <div style="color:var(--text-muted);font-size:.75rem;margin-top:4px">Il y a 5 heures</div>
                        </div>
                        <div style="padding:10px;background:var(--bg);border-radius:8px;font-size:.875rem">
                            💌 <strong>Sarra Hamdi</strong> vous a envoyé une demande
                            <div style="color:var(--text-muted);font-size:.75rem;margin-top:4px">Hier</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ÉTUDIANTS -->
        <div id="s-students" hidden>
            <div class="page-title">Trouver un colocataire</div>
            <p class="page-subtitle">Parcourez les profils d'étudiants disponibles</p>
            <div class="card" style="margin-bottom:24px">
                <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
                    <div class="form-group" style="margin:0;flex:1;min-width:200px">
                        <label class="form-label">Filtrer par ville</label>
                        <input type="text" class="form-control" placeholder="Ex: Tunis, Sfax..." id="filter-city">
                    </div>
                    <button class="btn btn-primary">🔍 Rechercher</button>
                </div>
            </div>
            <div class="students-grid">
                <?php foreach ($students as $s): ?>
                <div class="student-card">
                    <div class="student-card-header">
                        <div class="student-avatar"><?= strtoupper(substr($s['name'], 0, 1) . substr(strstr($s['name'], ' '), 1, 1)) ?></div>
                        <div>
                            <div class="student-name"><?= htmlspecialchars($s['name']) ?></div>
                            <div class="student-city">📍 <?= $s['city'] ?></div>
                        </div>
                    </div>
                    <p class="student-bio"><?= htmlspecialchars($s['bio']) ?></p>
                    <div class="student-tags">
                        <?= $s['smoking'] ? '<span class="badge badge-warning">🚬 Fumeur</span>' : '<span class="badge badge-success">🚭 Non-fumeur</span>' ?>
                        <?= $s['pets']    ? '<span class="badge badge-info">🐾 Animaux OK</span>' : '' ?>
                        <span class="badge badge-primary"><?= $s['schedule'] ?></span>
                        <span class="badge badge-primary">🧹 Propreté <?= $s['clean'] ?>/5</span>
                    </div>
                    <div class="student-budget">💰 <?= $s['budget'] ?> TND/mois</div>
                    <button class="btn btn-primary btn-sm" style="width:100%;margin-top:14px" onclick="showToast('Demande envoyée à <?= $s['name'] ?> !')">
                        💌 Envoyer une demande
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- DEMANDES REÇUES -->
        <div id="s-requests" hidden>
            <div class="page-title">Demandes reçues</div>
            <p class="page-subtitle">Acceptez ou refusez les demandes de colocation</p>
            <div class="card" style="margin-bottom:16px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    <div class="student-avatar" style="width:44px;height:44px;font-size:1rem">SH</div>
                    <div>
                        <strong>Sarra Hamdi</strong>
                        <div class="text-muted" style="font-size:.85rem">📍 Tunis · Médecine · Propreté 5/5</div>
                    </div>
                    <span class="badge badge-warning" style="margin-left:auto">En attente</span>
                </div>
                <p style="color:var(--text-muted);margin-bottom:12px;font-size:.9rem">"Bonjour ! Je suis étudiante en médecine, très sérieuse et organisée. Je cherche une colocataire calme pour partager un appartement à Tunis."</p>
                <div style="display:flex;gap:8px">
                    <button class="btn btn-success btn-sm" onclick="showToast('Demande acceptée ! Chat ouvert avec Sarra.')">✅ Accepter</button>
                    <button class="btn btn-danger  btn-sm" onclick="showToast('Demande refusée.')">❌ Refuser</button>
                </div>
            </div>
            <div class="card">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    <div class="student-avatar" style="width:44px;height:44px;font-size:1rem">RK</div>
                    <div>
                        <strong>Rania Kasmi</strong>
                        <div class="text-muted" style="font-size:.85rem">📍 Bizerte · Droit · Propreté 4/5</div>
                    </div>
                    <span class="badge badge-warning" style="margin-left:auto">En attente</span>
                </div>
                <p style="color:var(--text-muted);margin-bottom:12px;font-size:.9rem">"Étudiante en master droit. Studieuse et discrète. Budget 300–500 TND/mois."</p>
                <div style="display:flex;gap:8px">
                    <button class="btn btn-success btn-sm" onclick="showToast('Demande acceptée ! Chat ouvert avec Rania.')">✅ Accepter</button>
                    <button class="btn btn-danger  btn-sm" onclick="showToast('Demande refusée.')">❌ Refuser</button>
                </div>
            </div>
        </div>

        <!-- CHAT -->
        <div id="s-chat" hidden>
            <div class="page-title">Mes conversations</div>
            <p class="page-subtitle">Chattez avec vos matches</p>
            <div class="chat-layout">
                <div class="chat-list">
                    <div class="chat-list-item active">
                        <div class="student-avatar" style="width:40px;height:40px;font-size:.9rem;flex-shrink:0">MC</div>
                        <div><div style="font-weight:600;font-size:.9rem">Mariam Chaouachi</div><div style="font-size:.75rem;color:var(--text-muted)">✅ Match actif</div></div>
                    </div>
                    <div class="chat-list-item">
                        <div class="student-avatar" style="width:40px;height:40px;font-size:.9rem;flex-shrink:0">YM</div>
                        <div><div style="font-weight:600;font-size:.9rem">Youssef Mejri</div><div style="font-size:.75rem;color:var(--text-muted)">3 messages non lus</div></div>
                    </div>
                </div>
                <div class="chat-area">
                    <div style="padding:16px;border-bottom:1px solid var(--border);font-weight:600">Mariam Chaouachi ✅</div>
                    <div class="chat-messages">
                        <div><div class="chat-bubble recv">Bonjour Ahmed ! Contente qu'on soit match 😊</div><div style="font-size:.7rem;color:var(--text-muted);margin-top:2px">10:24</div></div>
                        <div><div class="chat-bubble sent">Bonjour Mariam ! Moi aussi. Tu cherches pour quand ?</div><div style="font-size:.7rem;color:var(--text-muted);text-align:right;margin-top:2px">10:26</div></div>
                        <div><div class="chat-bubble recv">Pour septembre, début d'année. Tu es à Tunis ?</div><div style="font-size:.7rem;color:var(--text-muted);margin-top:2px">10:28</div></div>
                        <div><div class="chat-bubble sent">Oui, je cherche dans le quartier El Menzah ou Ariana.</div><div style="font-size:.7rem;color:var(--text-muted);text-align:right;margin-top:2px">10:30</div></div>
                        <div><div class="chat-bubble recv">Parfait ! J'ai vu des annonces intéressantes sur Binomy 🏠</div><div style="font-size:.7rem;color:var(--text-muted);margin-top:2px">10:35</div></div>
                    </div>
                    <div class="chat-input-area">
                        <input type="text" class="form-control" placeholder="Écrire un message..." id="chat-in">
                        <button class="btn btn-primary" onclick="sendMsg()">Envoyer</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- LOGEMENTS -->
        <div id="s-listings" hidden>
            <div class="page-title">Annonces de logements</div>
            <p class="page-subtitle">Trouvez un logement adapté à votre budget</p>
            <div class="card" style="margin-bottom:24px">
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <input type="text" class="form-control" placeholder="Ville..." style="flex:1;min-width:140px">
                    <select class="form-control" style="flex:1;min-width:140px">
                        <option>Tous les types</option><option>Chambre</option><option>Studio</option><option>Appartement</option>
                    </select>
                    <input type="number" class="form-control" placeholder="Prix max (TND)" style="flex:1;min-width:140px">
                    <button class="btn btn-primary">🔍 Rechercher</button>
                </div>
            </div>
            <div class="listings-grid">
                <?php foreach ($listings as $l): ?>
                <div class="listing-card">
                    <div class="listing-img">🏠</div>
                    <div class="listing-body">
                        <div class="listing-price"><?= $l['price'] ?> TND <span>/ mois</span></div>
                        <div class="listing-title"><?= htmlspecialchars($l['title']) ?></div>
                        <div class="listing-meta">
                            <span>📍 <?= $l['city'] ?></span>
                            <span>🏷️ <?= $l['type'] ?></span>
                            <span>🚪 <?= $l['rooms'] ?> pièce(s)</span>
                            <span>📐 <?= $l['surface'] ?> m²</span>
                        </div>
                        <div style="margin-top:10px;font-size:.8rem;color:var(--text-muted)">Par <?= $l['owner'] ?></div>
                        <button class="btn btn-primary btn-sm" style="width:100%;margin-top:12px" onclick="showToast('Message envoyé au propriétaire !')">💬 Contacter</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PROFIL -->
        <div id="s-profile" hidden>
            <div class="page-title">Mon profil</div>
            <p class="page-subtitle">Complétez votre profil pour augmenter vos chances</p>
            <div class="card">
                <form onsubmit="event.preventDefault();showToast('Profil enregistré !')">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                        <div class="form-group"><label class="form-label">Nom complet</label><input type="text" class="form-control" value="Ahmed Trabelsi"></div>
                        <div class="form-group"><label class="form-label">Téléphone</label><input type="text" class="form-control" value="+216 55 123 456"></div>
                        <div class="form-group"><label class="form-label">Ville</label><input type="text" class="form-control" value="Tunis"></div>
                        <div class="form-group"><label class="form-label">Disponible à partir de</label><input type="date" class="form-control" value="2024-09-01"></div>
                    </div>
                    <div class="form-group"><label class="form-label">Bio</label><textarea class="form-control">Étudiant en informatique à l'ENIT. Calme, sérieux, cherche colocataire sympa.</textarea></div>
                    <h3 style="margin:20px 0 16px;color:var(--primary)">Préférences de colocation</h3>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
                        <div class="form-group"><label class="form-label">Budget min (TND)</label><input type="number" class="form-control" value="300"></div>
                        <div class="form-group"><label class="form-label">Budget max (TND)</label><input type="number" class="form-control" value="500"></div>
                        <div class="form-group"><label class="form-label">Propreté (1–5)</label><input type="number" min="1" max="5" class="form-control" value="4"></div>
                        <div class="form-group"><label class="form-label">Rythme de vie</label><select class="form-control"><option selected>🌅 Lève-tôt</option><option>🦉 Couche-tard</option><option>🔄 Flexible</option></select></div>
                        <div class="form-group"><label class="form-label">Je cherche</label><select class="form-control"><option selected>Les deux</option><option>Un colocataire</option><option>Un logement</option></select></div>
                    </div>
                    <div style="display:flex;gap:24px;margin-bottom:20px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox"> Fumeur</label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox"> Accepte les animaux</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer</button>
                </form>
            </div>
        </div>

    </main>
</div>

<!-- Toast notification -->
<div id="toast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;min-width:260px"></div>

<script>
function show(name) {
    document.querySelectorAll('[id^="s-"]').forEach(el => el.hidden = true);
    document.getElementById('s-' + name).hidden = false;
    document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
}
function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}
function sendMsg() {
    const inp = document.getElementById('chat-in');
    if (!inp.value.trim()) return;
    const area = document.querySelector('.chat-messages');
    area.innerHTML += `<div><div class="chat-bubble sent">${inp.value}</div><div style="font-size:.7rem;color:var(--text-muted);text-align:right;margin-top:2px">maintenant</div></div>`;
    inp.value = '';
    area.scrollTop = area.scrollHeight;
}
function toggleNotif() { showToast('3 notifications non lues','info'); }
</script>
</body>
</html>
