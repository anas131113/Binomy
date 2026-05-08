<?php
// Prototype suivi — données statiques
$users = [
    ['id'=>1,'name'=>'Ahmed Trabelsi',  'email'=>'ahmed@enit.tn',   'role'=>'student',  'city'=>'Tunis',  'active'=>true, 'date'=>'2024-03-01'],
    ['id'=>2,'name'=>'Mariam Chaouachi','email'=>'mariam@fsb.tn',   'role'=>'student',  'city'=>'Sfax',   'active'=>true, 'date'=>'2024-03-03'],
    ['id'=>3,'name'=>'Kamel Gharbi',    'email'=>'kamel@gmail.com', 'role'=>'locataire','city'=>'Tunis',  'active'=>true, 'date'=>'2024-02-20'],
    ['id'=>4,'name'=>'Youssef Mejri',   'email'=>'youssef@iset.tn', 'role'=>'student',  'city'=>'Sousse', 'active'=>true, 'date'=>'2024-03-10'],
    ['id'=>5,'name'=>'Sonia Maaloul',   'email'=>'sonia@gmail.com', 'role'=>'locataire','city'=>'Sousse', 'active'=>false,'date'=>'2024-02-15'],
    ['id'=>6,'name'=>'Admin Binomy',    'email'=>'admin@binomy.tn', 'role'=>'admin',    'city'=>'Tunis',  'active'=>true, 'date'=>'2024-01-01'],
];
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
        <span style="color:var(--text-muted);font-size:.9rem">Admin Binomy</span>
        <a href="/" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-section-title">Administration</div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" onclick="show('stats')">    <span class="sidebar-icon">📊</span> Statistiques</a></li>
            <li><a href="#" onclick="show('users')">                   <span class="sidebar-icon">👥</span> Utilisateurs</a></li>
            <li><a href="#" onclick="show('listings')">                <span class="sidebar-icon">🏡</span> Annonces</a></li>
        </ul>
    </aside>

    <main class="main-content">

        <!-- STATS -->
        <div id="s-stats">
            <div class="page-title">Dashboard Administrateur</div>
            <p class="page-subtitle">Vue d'ensemble de la plateforme Binomy</p>

            <div class="stats-cards">
                <div class="stat-card"><div class="stat-card-icon icon-purple">👥</div><div class="stat-card-info"><div class="stat-num">47</div><div class="stat-lbl">Utilisateurs total</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-blue">🎓</div><div class="stat-card-info"><div class="stat-num">34</div><div class="stat-lbl">Étudiants</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-orange">🏠</div><div class="stat-card-info"><div class="stat-num">12</div><div class="stat-lbl">Propriétaires</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-green">📋</div><div class="stat-card-info"><div class="stat-num">28</div><div class="stat-lbl">Annonces publiées</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-green">✅</div><div class="stat-card-info"><div class="stat-num">19</div><div class="stat-lbl">Logements disponibles</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-purple">🤝</div><div class="stat-card-info"><div class="stat-num">15</div><div class="stat-lbl">Matches créés</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-blue">💬</div><div class="stat-card-info"><div class="stat-num">142</div><div class="stat-lbl">Messages échangés</div></div></div>
                <div class="stat-card"><div class="stat-card-icon icon-orange">⏳</div><div class="stat-card-info"><div class="stat-num">6</div><div class="stat-lbl">Demandes en attente</div></div></div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:8px">
                <div class="card">
                    <div class="card-header"><h3>Inscriptions — 7 derniers jours</h3></div>
                    <?php
                    $week = [['Lun','3'],['Mar','5'],['Mer','2'],['Jeu','7'],['Ven','4'],['Sam','1'],['Dim','2']];
                    $max  = 7;
                    ?>
                    <div style="display:flex;align-items:flex-end;gap:8px;height:100px;padding-top:12px">
                        <?php foreach ($week as [$day, $val]): ?>
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                            <span style="font-size:.7rem;font-weight:600;color:var(--primary)"><?= $val ?></span>
                            <div style="width:100%;background:var(--primary);border-radius:4px 4px 0 0;height:<?= round($val/$max*70) ?>px;opacity:.85"></div>
                            <span style="font-size:.7rem;color:var(--text-muted)"><?= $day ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Répartition des utilisateurs</h3></div>
                    <div style="display:flex;flex-direction:column;gap:12px;padding-top:8px">
                        <?php
                        $roles = [['Étudiants','34','72%','var(--primary)'],['Propriétaires','12','26%','var(--accent)'],['Admins','1','2%','var(--danger)']];
                        foreach ($roles as [$r,$n,$pct,$color]): ?>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:.875rem">
                                <span><?= $r ?></span><span><strong><?= $n ?></strong> (<?= $pct ?>)</span>
                            </div>
                            <div style="background:var(--border);border-radius:999px;height:8px">
                                <div style="background:<?= $color ?>;width:<?= $pct ?>;height:8px;border-radius:999px"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- UTILISATEURS -->
        <div id="s-users" hidden>
            <div class="page-title">Gestion des utilisateurs</div>
            <div class="card" style="margin-bottom:20px">
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <input type="text" class="form-control" placeholder="Rechercher par nom ou email..." style="flex:1;min-width:200px">
                    <select class="form-control" style="width:180px">
                        <option>Tous les rôles</option><option>Étudiants</option><option>Propriétaires</option><option>Admins</option>
                    </select>
                    <button class="btn btn-primary">🔍 Filtrer</button>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Ville</th><th>Statut</th><th>Inscrit le</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                            <td><?= $u['email'] ?></td>
                            <td>
                                <?php
                                $badge = ['student'=>'badge-primary','locataire'=>'badge-info','admin'=>'badge-danger'];
                                echo '<span class="badge ' . $badge[$u['role']] . '">' . $u['role'] . '</span>';
                                ?>
                            </td>
                            <td><?= $u['city'] ?></td>
                            <td>
                                <span class="badge <?= $u['active'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $u['active'] ? 'Actif' : 'Suspendu' ?>
                                </span>
                            </td>
                            <td><?= $u['date'] ?></td>
                            <td>
                                <?php if ($u['role'] !== 'admin'): ?>
                                <button class="btn btn-sm <?= $u['active'] ? 'btn-danger' : 'btn-success' ?>"
                                    onclick="showToast('<?= $u['active'] ? 'Compte suspendu.' : 'Compte réactivé.' ?>')">
                                    <?= $u['active'] ? '🚫 Suspendre' : '✅ Réactiver' ?>
                                </button>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ANNONCES ADMIN -->
        <div id="s-listings" hidden>
            <div class="page-title">Gestion des annonces</div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Titre</th><th>Propriétaire</th><th>Ville</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $adm = [
                            [1,'Studio meublé près ENIT','Kamel Gharbi','Tunis','450','available'],
                            [2,'Chambre dans appart 3 pièces','Hassen Jelassi','Sfax','250','rented'],
                            [3,'Appartement F2 proche fac','Sonia Maaloul','Sousse','600','available'],
                            [4,'Studio tout équipé centre-ville','Nizar Fakhfakh','Monastir','380','available'],
                        ];
                        foreach ($adm as [$id,$title,$owner,$city,$price,$status]): ?>
                        <tr>
                            <td>#<?= $id ?></td>
                            <td><strong><?= $title ?></strong></td>
                            <td><?= $owner ?></td>
                            <td>📍 <?= $city ?></td>
                            <td><?= $price ?> TND</td>
                            <td><span class="badge <?= $status==='available'?'badge-success':'badge-warning' ?>"><?= $status==='available'?'Disponible':'Louée' ?></span></td>
                            <td><button class="btn btn-danger btn-sm" onclick="showToast('Annonce supprimée (modération).')">🗑️ Supprimer</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
