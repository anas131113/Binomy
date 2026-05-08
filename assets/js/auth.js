/**
 * BINOMY — auth.js
 * Gère l'inscription, la connexion et la déconnexion via Fetch API
 */

// ---------- Helpers ----------

function showAlert(containerId, message, type = 'error') {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
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
    btn.textContent = loading ? 'Chargement...' : btn.dataset.label;
}

// ---------- Inscription ----------

async function handleRegister(event) {
    event.preventDefault();
    clearAlert('register-alert');

    const name     = document.getElementById('reg-name').value.trim();
    const email    = document.getElementById('reg-email').value.trim();
    const password = document.getElementById('reg-password').value;
    const role     = document.getElementById('reg-role').value;

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
        const res  = await fetch('/api/auth/register.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ name, email, password, role }),
        });
        const data = await res.json();

        if (data.success) {
            showAlert('register-alert', data.message, 'success');
            setTimeout(() => redirectToDashboard(data.data.role), 800);
        } else {
            showAlert('register-alert', data.message);
        }
    } catch (err) {
        showAlert('register-alert', 'Erreur réseau. Veuillez réessayer.');
    } finally {
        setLoading('register-btn', false);
    }
}

// ---------- Connexion ----------

async function handleLogin(event) {
    event.preventDefault();
    clearAlert('login-alert');

    const email    = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
        showAlert('login-alert', 'Email et mot de passe requis.');
        return;
    }

    setLoading('login-btn', true);

    try {
        const res  = await fetch('/api/auth/login.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ email, password }),
        });
        const data = await res.json();

        if (data.success) {
            showAlert('login-alert', `Bienvenue, ${data.data.name} !`, 'success');
            setTimeout(() => redirectToDashboard(data.data.role), 600);
        } else {
            showAlert('login-alert', data.message);
        }
    } catch (err) {
        showAlert('login-alert', 'Erreur réseau. Veuillez réessayer.');
    } finally {
        setLoading('login-btn', false);
    }
}

// ---------- Déconnexion ----------

async function handleLogout() {
    try {
        await fetch('/api/auth/logout.php', { method: 'POST' });
    } finally {
        window.location.href = '/';
    }
}

// ---------- Redirection selon le rôle ----------

function redirectToDashboard(role) {
    const routes = {
        student:   '/pages/dashboard_student.php',
        locataire: '/pages/dashboard_locataire.php',
        admin:     '/pages/dashboard_admin.php',
    };
    window.location.href = routes[role] ?? '/';
}

// ---------- Modal Auth (landing page) ----------

function openModal(tab = 'login') {
    document.getElementById('auth-modal').classList.add('open');
    switchTab(tab);
}

function closeModal() {
    document.getElementById('auth-modal').classList.remove('open');
}

function switchTab(tab) {
    document.querySelectorAll('.modal-tab').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.tab === tab);
    });
    document.querySelectorAll('.auth-panel').forEach(panel => {
        panel.hidden = panel.id !== `panel-${tab}`;
    });
}

// ---------- Init ----------

document.addEventListener('DOMContentLoaded', () => {
    // Store button labels for the loading state helper
    document.querySelectorAll('[data-label]').forEach(el => {
        el.textContent = el.dataset.label;
    });

    // Close modal on overlay click
    const overlay = document.getElementById('auth-modal');
    if (overlay) {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeModal();
        });
    }

    // Close modal with Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
