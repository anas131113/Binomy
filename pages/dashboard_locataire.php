<?php
// Prototype suivi — données statiques
$listings = [
    ['id'=>1,'title'=>'Studio meublé près ENIT',        'type'=>'Studio',      'city'=>'Tunis',   'price'=>'450','status'=>'available','msgs'=>3,'views'=>24,'created'=>'2024-03-10'],
    ['id'=>2,'title'=>'Chambre dans appart 3 pièces',    'type'=>'Chambre',     'city'=>'Sfax',    'price'=>'250','status'=>'rented',   'msgs'=>1,'views'=>18,'created'=>'2024-02-28'],
    ['id'=>3,'title'=>'Appartement F2 proche fac',       'type'=>'Appartement', 'city'=>'Sousse',  'price'=>'600','status'=>'available','msgs'=>5,'views'=>37,'created'=>'2024-03-15'],
];
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
        <span style="color:var(--text-muted);font-size:.9rem">Bonjour, <strong>Kamel Gharbi</strong></span>
        <span class="badge badge-info">Propriétaire</span>
        <a href="/" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section-title">Navigation</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="show('home')">      <span class="sidebar-icon">🏠</span> Accueil</a></li>
            <li><a href="#" onclick="show('listings')">                 <span class="sidebar-icon">📋</span> Mes annonces</a></li>
            <li><a href="#" onclick="show('new')">                      <span class="sidebar-icon">➕</span> Publier une annonce</a></li>
            <li><a href="#" onclick="show('messages')">                 <span class="sidebar-icon">💬</span> Messages reçus</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <!-- ACCUEIL -->
        <div id="s-home">
            <div class="page-title">Bonjour, Kamel 🏠</div>
            <p class="page-subtitle">Gérez vos annonces et vos messages</p>
            <div class="stats-cards">
                <div class="stat-card"><div class="stat-card-icon icon-purple">📋</div><div class="stat-card-info"><div class="stat-num">3</div><div class="stat-lbl">Mes annonces</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-green">✅</div><div class="stat-card-info"><div class="stat-num">2</div><div class="stat-lbl">Disponibles</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-orange">🔑</div><div class="stat-card-info"><div class="stat-num">1</div><div class="stat-lbl">Louée</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-blue">💬</div><div class="stat-card-info"><div class="stat-num">8</div><div class="stat-lbl">Messages reçus</div></div></div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Actions rapides</h3></div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="show('new')">➕ Publier une annonce</button>
                    <button class="btn btn-outline" onclick="show('listings')">📋 Voir mes annonces</button>
                    <button class="btn btn-outline" onclick="show('messages')">💬 Voir les messages</button>
                </div>
            </div>
        </div>

        <!-- MES ANNONCES -->
        <div id="s-listings" hidden>
            <div class="page-title">Mes annonces</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Titre</th><th>Type</th><th>Ville</th><th>Prix</th><th>Statut</th><th>Vues</th><th>Messages</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $l): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($l['title']) ?></strong></td>
                            <td><?= $l['type'] ?></td>
                            <td>📍 <?= $l['city'] ?></td>
                            <td><strong><?= $l['price'] ?> TND</strong></td>
                            <td>
                                <?php if ($l['status'] === 'available'): ?>
                                    <span class="badge badge-success">✅ Disponible</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">🔑 Louée</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $l['views'] ?></td>
                            <td><?= $l['msgs'] ?></td>
                            <td>
                                <button class="btn btn-outline btn-sm" onclick="showToast('Modification en cours...')">✏️ Modifier</button>
                                <?php if ($l['status'] === 'available'): ?>
                                <button class="btn btn-sm" style="background:#F59E0B;color:#fff" onclick="showToast('Annonce marquée comme louée !')">🔑 Marquer louée</button>
                                <?php endif; ?>
                                <button class="btn btn-danger btn-sm" onclick="showToast('Annonce supprimée.')">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- NOUVELLE ANNONCE -->
        <div id="s-new" hidden>
            <div class="page-title">Publier une annonce</div>
            <p class="page-subtitle">Remplissez les informations de votre logement</p>
            <div class="card">
                <form onsubmit="event.preventDefault();showToast('Annonce publiée avec succès !')">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Titre de l'annonce *</label>
                            <input type="text" class="form-control" placeholder="Ex: Studio meublé proche faculté de droit">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type de logement *</label>
                            <select class="form-control"><option>Chambre</option><option>Studio</option><option>Appartement</option><option>Maison</option></select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prix mensuel (TND) *</label>
                            <input type="number" class="form-control" placeholder="350">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville *</label>
                            <input type="text" class="form-control" placeholder="Tunis">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" class="form-control" placeholder="Rue, quartier...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre de pièces</label>
                            <input type="number" class="form-control" placeholder="2">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Surface (m²)</label>
                            <input type="number" class="form-control" placeholder="45">
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" rows="4" placeholder="Décrivez votre logement..."></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1/-1">
                            <label class="form-label">Photos (max 5)</label>
                            <input type="file" class="form-control" accept="image/*" multiple>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">📤 Publier l'annonce</button>
                </form>
            </div>
        </div>

        <!-- MESSAGES -->
        <div id="s-messages" hidden>
            <div class="page-title">Messages reçus</div>
            <div style="display:flex;flex-direction:column;gap:16px">
                <?php
                $msgs = [
                    ['from'=>'Ahmed Trabelsi','listing'=>'Studio meublé près ENIT','msg'=>'Bonjour, votre studio est encore disponible ? Je suis étudiant à l\'ENIT.','time'=>'Il y a 1h'],
                    ['from'=>'Mariam Chaouachi','listing'=>'Studio meublé près ENIT','msg'=>'Bonjour ! Est-il possible de visiter ce week-end ?','time'=>'Il y a 3h'],
                    ['from'=>'Youssef Mejri','listing'=>'Appartement F2 proche fac','msg'=>'Bonjour, le logement est-il disponible pour septembre ?','time'=>'Hier'],
                ];
                foreach ($msgs as $m): ?>
                <div class="card">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                        <div class="student-avatar" style="width:40px;height:40px;font-size:.875rem;flex-shrink:0">
                            <?= strtoupper(substr($m['from'],0,1).substr(strstr($m['from'],' '),1,1)) ?>
                        </div>
                        <div>
                            <strong><?= $m['from'] ?></strong>
                            <div style="font-size:.8rem;color:var(--text-muted)">📋 <?= $m['listing'] ?> · <?= $m['time'] ?></div>
                        </div>
                    </div>
                    <p style="color:var(--text-muted);margin-bottom:12px;font-size:.9rem">"<?= $m['msg'] ?>"</p>
                    <button class="btn btn-primary btn-sm" onclick="showToast('Réponse envoyée !')">💬 Répondre</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </main>
</div>

<div id="toast" style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;min-width:260px"></div>
<script>
function show(name) {
    document.querySelectorAll('[id^="s-"]').forEach(el => el.hidden = true);
    document.getElementById('s-' + name).hidden = false;
}
function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}
</script>
</body>
</html>
