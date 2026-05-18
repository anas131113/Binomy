/**
 * BINOMY — auth.js
 * Gère l'inscription, la connexion et la déconnexion via Fetch API.
 * Utilisé par index.html (landing page).
 *
 * Chemins API : absolus depuis la racine XAMPP (/binomy/api/...)
 * Compatible avec : http://localhost/binomy/index.html
 */

// ============================================================
// Helpers UI
// ============================================================

function showAlert(containerId, message, type = 'error') {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = `<div class="alert alert-${type}" role="alert">${escHtml(message)}</div>`;
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function clearAlert(containerId) {
    const el = document.getElementById(containerId);
    if (el) el.innerHTML = '';
}

function setLoading(btnId, loading) {
    const btn = document.getElementById(btnId);
    if (!btn) return;
    btn.disabled = loading;
    btn.textContent = loading ? 'Chargement…' : (btn.dataset.label || btn.textContent);
}

function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

// ============================================================
// Inscription
// ============================================================

async function handleRegister(event) {
    event.preventDefault();
    clearAlert('register-alert');

    const name = document.getElementById('reg-name').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const password = document.getElementById('reg-password').value;
    const role = document.getElementById('reg-role').value;

    if (!name || !email || !password || !role) {
        showAlert('register-alert', 'Veuillez remplir tous les champs.');
        return;
    }
    if (password.length < 6) {
        showAlert('register-alert', 'Le mot de passe doit contenir au moins 6 caractères.');
        return;
    }

    setLoading('register-btn', true);

    try {
        const res = await fetch(apiUrl('api/auth/register.php'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password, role }),
        });
        const data = await parseJsonResponse(res);

        if (data.success) {
            showAlert('register-alert', data.message, 'success');
            setTimeout(() => redirectToDashboard(data.data.role), 800);
        } else {
            showAlert('register-alert', data.message);
        }
    } catch (err) {
        showAlert('register-alert', 'Erreur réseau. Vérifiez que XAMPP est démarré et que la base de données est importée.');
    } finally {
        setLoading('register-btn', false);
    }
}

// ============================================================
// Connexion
// ============================================================

async function handleLogin(event) {
    event.preventDefault();
    clearAlert('login-alert');

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
        showAlert('login-alert', 'Email et mot de passe requis.');
        return;
    }

    setLoading('login-btn', true);

    try {
        const res = await fetch(apiUrl('api/auth/login.php'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password }),
        });
        const data = await parseJsonResponse(res);

        if (data.success) {
            showAlert('login-alert', `Bienvenue, ${escHtml(data.data.name)} !`, 'success');
            setTimeout(() => redirectToDashboard(data.data.role), 600);
        } else {
            showAlert('login-alert', data.message);
        }
    } catch (err) {
        showAlert('login-alert', 'Erreur réseau. Vérifiez que XAMPP est démarré et que la base de données est importée.');
    } finally {
        setLoading('login-btn', false);
    }
}

// ============================================================
// Déconnexion
// ============================================================

async function handleLogout() {
    try {
        await fetch(apiUrl('api/auth/logout.php'), { method: 'POST' });
    } finally {
        window.location.href = appUrl('index.html');
    }
}

// ============================================================
// Redirection selon le rôle
// ============================================================

function redirectToDashboard(role) {
    const routes = {
        student: appUrl('html/dashboard_student.html'),
        locataire: appUrl('html/dashboard_locataire.html'),
        admin: appUrl('html/dashboard_admin.html'),
    };
    window.location.href = routes[role] ?? appUrl('index.html');
}

// ============================================================
// Modal Auth
// ============================================================

function openModal(tab = 'login') {
    const modal = document.getElementById('auth-modal');
    if (modal) {
        modal.classList.add('open');
        switchTab(tab);
    }
}

function closeModal() {
    const modal = document.getElementById('auth-modal');
    if (modal) modal.classList.remove('open');
}

function switchTab(tab) {
    document.querySelectorAll('.modal-tab').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    document.querySelectorAll('.auth-panel').forEach(panel => {
        panel.hidden = panel.id !== `panel-${tab}`;
    });
}

// ============================================================
// Init
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-label]').forEach(el => {
        el.textContent = el.dataset.label;
    });

    const overlay = document.getElementById('auth-modal');
    if (overlay) {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal();
        });
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
