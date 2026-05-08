<?php
session_start();

// Rediriger vers le dashboard si déjà connecté
if (!empty($_SESSION['user_id'])) {
    $routes = ['student' => 'pages/dashboard_student.php', 'locataire' => 'pages/dashboard_locataire.php', 'admin' => 'pages/dashboard_admin.php'];
    header('Location: ' . ($routes[$_SESSION['role']] ?? '/'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Binomy — Trouve ton colocataire idéal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .hero-badge { display: inline-block; background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.3); border-radius: 999px; padding: 6px 16px; font-size: .85rem; margin-bottom: 20px; backdrop-filter: blur(8px); }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-brand">Bin<span>omy</span></div>
    <div class="navbar-links">
        <a href="#features">Fonctionnalités</a>
        <a href="#about">À propos</a>
        <button class="btn btn-outline btn-sm" onclick="openModal('login')">Connexion</button>
        <button class="btn btn-primary btn-sm" onclick="openModal('register')">S'inscrire</button>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">🎓 Plateforme étudiante · Tunisie</div>
            <h1>Trouve ton colocataire <br>& logement idéal</h1>
            <p>Binomy connecte les étudiants tunisiens avec des colocataires compatibles et des propriétaires de confiance. Fini les recherches interminables.</p>
            <div class="hero-cta">
                <button class="btn-white" onclick="openModal('register')">
                    🚀 Commencer gratuitement
                </button>
                <button class="btn-outline-white" onclick="openModal('login')">
                    J'ai déjà un compte
                </button>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div>
                <span class="stat-number">500+</span>
                <span class="stat-label">Étudiants inscrits</span>
            </div>
            <div>
                <span class="stat-number">200+</span>
                <span class="stat-label">Logements disponibles</span>
            </div>
            <div>
                <span class="stat-number">150+</span>
                <span class="stat-label">Matches créés</span>
            </div>
            <div>
                <span class="stat-number">8+</span>
                <span class="stat-label">Villes couvertes</span>
            </div>
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
                <h3>Notifications temps réel</h3>
                <p>Soyez alerté immédiatement pour chaque demande de colocation, message ou réponse.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Sécurisé</h3>
                <p>Vos données sont protégées. Mots de passe hachés, sessions sécurisées, requêtes préparées.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📱</div>
                <h3>Responsive</h3>
                <p>Interface adaptée à tous les appareils. Utilisez Binomy depuis votre mobile ou ordinateur.</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section style="padding:80px 0;background:var(--bg)" id="about">
    <div class="container">
        <h2 class="text-center">Comment ça marche ?</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin-top:48px">
            <?php
            $steps = [
                ['1', '📝', 'Créez votre profil', 'Inscrivez-vous en tant qu\'étudiant ou propriétaire et complétez votre profil.'],
                ['2', '🔍', 'Explorez', 'Parcourez les profils d\'étudiants ou les annonces de logements dans votre ville.'],
                ['3', '💌', 'Connectez-vous', 'Envoyez une demande de colocation ou contactez un propriétaire.'],
                ['4', '🏡', 'Trouvez votre match', 'Après acceptation, chattez et organisez votre emménagement !'],
            ];
            foreach ($steps as $s): ?>
            <div style="text-align:center;padding:24px">
                <div style="width:48px;height:48px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;margin:0 auto 12px">
                    <?= $s[0] ?>
                </div>
                <div style="font-size:32px;margin-bottom:12px"><?= $s[1] ?></div>
                <h3 style="margin-bottom:8px"><?= $s[2] ?></h3>
                <p style="color:var(--text-muted);font-size:.9rem"><?= $s[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8">
            <button class="btn btn-primary btn-lg" onclick="openModal('register')">
                Rejoindre Binomy gratuitement →
            </button>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">Binomy</div>
                <p style="font-size:.9rem;line-height:1.7">La plateforme qui connecte les étudiants tunisiens avec des colocataires compatibles et des logements abordables.</p>
            </div>
            <div>
                <div style="color:#fff;font-weight:600;margin-bottom:12px">Plateforme</div>
                <ul class="footer-links">
                    <li><a href="#features">Fonctionnalités</a></li>
                    <li><a href="#" onclick="openModal('register')">S'inscrire</a></li>
                    <li><a href="#" onclick="openModal('login')">Se connecter</a></li>
                </ul>
            </div>
            <div>
                <div style="color:#fff;font-weight:600;margin-bottom:12px">Projet</div>
                <ul class="footer-links">
                    <li><a href="#">TEK-UP University</a></li>
                    <li><a href="#">Mini-Projet PHP/JS</a></li>
                    <li><a href="#">GitHub</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Binomy — Mini-Projet TEK-UP · PHP + PDO + Fetch API
        </div>
    </div>
</footer>

<!-- MODAL AUTH -->
<div class="modal-overlay" id="auth-modal">
    <div class="modal">
        <div class="modal-tabs">
            <button class="modal-tab active" data-tab="login"    onclick="switchTab('login')">Connexion</button>
            <button class="modal-tab"         data-tab="register" onclick="switchTab('register')">Inscription</button>
        </div>

        <!-- LOGIN -->
        <div id="panel-login" class="auth-panel">
            <div id="login-alert"></div>
            <form onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label class="form-label">Adresse email</label>
                    <input id="login-email" type="email" class="form-control" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input id="login-password" type="password" class="form-control" placeholder="••••••" required>
                </div>
                <button id="login-btn" class="btn btn-primary" style="width:100%" type="submit"
                        data-label="Se connecter">Se connecter</button>
            </form>
            <p class="text-center text-muted mt-4" style="font-size:.875rem">
                Pas encore de compte ? <a href="#" onclick="switchTab('register')">S'inscrire</a>
            </p>
        </div>

        <!-- REGISTER -->
        <div id="panel-register" class="auth-panel" hidden>
            <div id="register-alert"></div>
            <form onsubmit="handleRegister(event)">
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input id="reg-name" type="text" class="form-control" placeholder="Ahmed Ben Ali" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Adresse email</label>
                    <input id="reg-email" type="email" class="form-control" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mot de passe</label>
                    <input id="reg-password" type="password" class="form-control" placeholder="Min. 6 caractères" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Je suis…</label>
                    <select id="reg-role" class="form-control" required>
                        <option value="">-- Choisir un rôle --</option>
                        <option value="student">🎓 Étudiant — je cherche un colocataire / logement</option>
                        <option value="locataire">🏠 Propriétaire — je veux publier une annonce</option>
                    </select>
                </div>
                <button id="register-btn" class="btn btn-primary" style="width:100%" type="submit"
                        data-label="Créer mon compte">Créer mon compte</button>
            </form>
            <p class="text-center text-muted mt-4" style="font-size:.875rem">
                Déjà inscrit ? <a href="#" onclick="switchTab('login')">Se connecter</a>
            </p>
        </div>

        <button onclick="closeModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--text-muted)">✕</button>
    </div>
</div>

<script src="/assets/js/auth.js"></script>
</body>
</html>
