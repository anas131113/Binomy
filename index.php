<?php
// Prototype statique — suivi TEK-UP
$role = $_GET['role'] ?? null;
if ($role === 'student')   { header('Location: pages/dashboard_student.php');   exit; }
if ($role === 'locataire') { header('Location: pages/dashboard_locataire.php'); exit; }
if ($role === 'admin')     { header('Location: pages/dashboard_admin.php');     exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Binomy — Trouve ton colocataire idéal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">Bin<span>omy</span></div>
    <div class="navbar-links">
        <a href="#features">Fonctionnalités</a>
        <a href="#demo">Démo</a>
        <button class="btn btn-outline btn-sm" onclick="document.getElementById('auth-modal').classList.add('open')">Connexion</button>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('auth-modal').classList.add('open')">S'inscrire</button>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div style="display:inline-block;background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);border-radius:999px;padding:6px 16px;font-size:.85rem;margin-bottom:20px;">
                🎓 Plateforme étudiante · Tunisie
            </div>
            <h1>Trouve ton colocataire<br>& logement idéal</h1>
            <p>Binomy connecte les étudiants tunisiens avec des colocataires compatibles et des propriétaires de confiance. Fini les recherches interminables.</p>
            <div class="hero-cta">
                <button class="btn-white" onclick="document.getElementById('auth-modal').classList.add('open')">
                    🚀 Commencer gratuitement
                </button>
                <a href="#demo" class="btn-outline-white">Voir la démo →</a>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div><span class="stat-number">500+</span><span class="stat-label">Étudiants inscrits</span></div>
            <div><span class="stat-number">200+</span><span class="stat-label">Logements disponibles</span></div>
            <div><span class="stat-number">150+</span><span class="stat-label">Matches créés</span></div>
            <div><span class="stat-number">8+</span><span class="stat-label">Villes couvertes</span></div>
        </div>
    </div>
</div>

<!-- FEATURES -->
<section class="features" id="features">
    <div class="container">
        <h2 class="text-center">Tout ce dont vous avez besoin</h2>
        <p class="text-center text-muted mt-4">Une plateforme pensée pour la réalité des étudiants tunisiens</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Matching intelligent</h3>
                <p>Trouvez des colocataires compatibles selon votre budget, style de vie et préférences personnelles.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏠</div>
                <h3>Annonces vérifiées</h3>
                <p>Des logements publiés par des propriétaires sérieux, filtrables par ville, type et budget.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Messagerie intégrée</h3>
                <p>Communiquez directement avec vos matches ou les propriétaires depuis la plateforme.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>Notifications</h3>
                <p>Soyez alerté immédiatement pour chaque demande de colocation, message ou réponse.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Sécurisé</h3>
                <p>Mots de passe bcrypt, sessions PHP, requêtes PDO préparées — aucune faille SQL/XSS.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Dashboard Admin</h3>
                <p>Interface d'administration complète avec statistiques, gestion des comptes et modération.</p>
            </div>
        </div>
    </div>
</section>

<!-- DEMO RAPIDE -->
<section style="padding:80px 0;background:var(--bg)" id="demo">
    <div class="container text-center">
        <h2>Accéder à la démo</h2>
        <p class="text-muted mt-4" style="margin-bottom:40px">Explorez chaque interface selon le rôle</p>
        <div style="display:flex;gap:24px;justify-content:center;flex-wrap:wrap">
            <a href="?role=student" class="card" style="width:220px;cursor:pointer;text-decoration:none;transition:all .2s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                <div style="font-size:48px;margin-bottom:12px">🎓</div>
                <h3>Étudiant</h3>
                <p class="text-muted" style="font-size:.875rem;margin-top:8px">Trouver colocataire &amp; logement</p>
                <div class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;justify-content:center">Voir le dashboard →</div>
            </a>
            <a href="?role=locataire" class="card" style="width:220px;cursor:pointer;text-decoration:none;transition:all .2s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                <div style="font-size:48px;margin-bottom:12px">🏠</div>
                <h3>Propriétaire</h3>
                <p class="text-muted" style="font-size:.875rem;margin-top:8px">Publier &amp; gérer ses annonces</p>
                <div class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;justify-content:center">Voir le dashboard →</div>
            </a>
            <a href="?role=admin" class="card" style="width:220px;cursor:pointer;text-decoration:none;transition:all .2s" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                <div style="font-size:48px;margin-bottom:12px">⚙️</div>
                <h3>Administrateur</h3>
                <p class="text-muted" style="font-size:.875rem;margin-top:8px">Statistiques &amp; modération</p>
                <div class="btn btn-primary btn-sm" style="margin-top:16px;width:100%;justify-content:center">Voir le dashboard →</div>
            </a>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-bottom">
            &copy; 2024 Binomy — Mini-Projet PHP/JavaScript | TEK-UP University
        </div>
    </div>
</footer>

<!-- MODAL AUTH (statique, juste pour montrer l'interface) -->
<div class="modal-overlay" id="auth-modal" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal">
        <div class="modal-tabs">
            <button class="modal-tab active" onclick="switchTab('login')">Connexion</button>
            <button class="modal-tab" onclick="switchTab('register')">Inscription</button>
        </div>

        <div id="panel-login">
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" class="form-control" placeholder="votre@email.com">
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" placeholder="••••••">
            </div>
            <div style="margin-bottom:16px">
                <label class="form-label">Accès rapide (démo)</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="?role=student"   class="btn btn-outline btn-sm">🎓 Étudiant</a>
                    <a href="?role=locataire" class="btn btn-outline btn-sm">🏠 Propriétaire</a>
                    <a href="?role=admin"     class="btn btn-outline btn-sm">⚙️ Admin</a>
                </div>
            </div>
            <button class="btn btn-primary" style="width:100%">Se connecter</button>
        </div>

        <div id="panel-register" hidden>
            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input type="text" class="form-control" placeholder="Ahmed Ben Ali">
            </div>
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" class="form-control" placeholder="votre@email.com">
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" placeholder="Min. 6 caractères">
            </div>
            <div class="form-group">
                <label class="form-label">Je suis…</label>
                <select class="form-control">
                    <option value="">-- Choisir un rôle --</option>
                    <option value="student">🎓 Étudiant</option>
                    <option value="locataire">🏠 Propriétaire</option>
                </select>
            </div>
            <button class="btn btn-primary" style="width:100%">Créer mon compte</button>
        </div>

        <button onclick="document.getElementById('auth-modal').classList.remove('open')"
                style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--text-muted)">✕</button>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.modal-tab').forEach((b,i) => b.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
    document.getElementById('panel-login').hidden    = tab !== 'login';
    document.getElementById('panel-register').hidden = tab !== 'register';
}
</script>
</body>
</html>
