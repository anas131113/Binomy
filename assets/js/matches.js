/**
 * BINOMY — matches.js
 * Gestion du matching entre étudiants via Fetch API
 */

async function loadStudents() {
    const city = document.getElementById('filter-city')?.value ?? '';
    const grid = document.getElementById('students-grid');
    if (!grid) return;

    grid.innerHTML = '<div class="spinner"></div>';

    const params = new URLSearchParams();
    if (city) params.set('city', city);

    try {
        const res  = await fetch(`/api/users/get_students.php?${params}`);
        const data = await res.json();

        if (!data.success || !data.data.students.length) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1">
                    <div class="empty-state-icon">👥</div>
                    <h3>Aucun étudiant trouvé</h3>
                    <p>Modifiez vos filtres ou revenez plus tard</p>
                </div>`;
            return;
        }

        grid.innerHTML = data.data.students.map(renderStudentCard).join('');
    } catch (_) {
        grid.innerHTML = '<p class="text-muted text-center">Erreur réseau.</p>';
    }
}

function renderStudentCard(s) {
    const initials = s.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    const budget   = s.budget_min && s.budget_max
        ? `${s.budget_min}–${s.budget_max} TND/mois`
        : s.budget_max ? `Max ${s.budget_max} TND/mois` : '';

    return `
        <div class="student-card">
            <div class="student-card-header">
                <div class="student-avatar">${initials}</div>
                <div>
                    <div class="student-name">${escHtml(s.name)}</div>
                    <div class="student-city">📍 ${escHtml(s.city || 'Non précisé')}</div>
                </div>
            </div>
            ${s.bio ? `<p class="student-bio">${escHtml(s.bio)}</p>` : ''}
            <div class="student-tags">
                ${s.smoking  ? '<span class="badge badge-warning">🚬 Fumeur</span>'       : '<span class="badge badge-success">🚭 Non-fumeur</span>'}
                ${s.pets_ok  ? '<span class="badge badge-info">🐾 Animaux OK</span>'     : ''}
                ${s.schedule ? `<span class="badge badge-primary">${scheduleLabel(s.schedule)}</span>` : ''}
                ${s.cleanliness ? `<span class="badge badge-primary">🧹 Propreté: ${s.cleanliness}/5</span>` : ''}
            </div>
            ${budget ? `<div class="student-budget">💰 ${budget}</div>` : ''}
            <button class="btn btn-primary btn-sm" style="width:100%;margin-top:14px"
                    onclick="sendRequest(${s.id}, '${escHtml(s.name)}')">
                💌 Envoyer une demande
            </button>
        </div>`;
}

function scheduleLabel(s) {
    const map = { early_bird: '🌅 Lève-tôt', night_owl: '🦉 Couche-tard', flexible: '🔄 Flexible' };
    return map[s] ?? s;
}

async function sendRequest(receiverId, receiverName) {
    const message = prompt(`Message d'introduction à ${receiverName} (optionnel) :`);
    if (message === null) return; // annulé

    try {
        const res  = await fetch('/api/matches/send_request.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ receiver_id: receiverId, message }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

async function loadRequests() {
    const container = document.getElementById('requests-container');
    if (!container) return;

    container.innerHTML = '<div class="spinner"></div>';

    try {
        const res  = await fetch('/api/matches/get_requests.php');
        const data = await res.json();

        if (!data.success || !data.data.length) {
            container.innerHTML = `<div class="empty-state"><div class="empty-state-icon">📭</div><h3>Aucune demande en attente</h3></div>`;
            return;
        }

        container.innerHTML = data.data.map(r => `
            <div class="card mb-4" style="margin-bottom:16px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    <div class="student-avatar" style="width:44px;height:44px;font-size:1rem">
                        ${r.sender_name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase()}
                    </div>
                    <div>
                        <strong>${escHtml(r.sender_name)}</strong>
                        <div class="text-muted" style="font-size:.85rem">📍 ${escHtml(r.sender_city || 'N/A')}</div>
                    </div>
                    <span class="badge badge-warning" style="margin-left:auto">En attente</span>
                </div>
                ${r.message ? `<p style="color:var(--text-muted);margin-bottom:12px">"${escHtml(r.message)}"</p>` : ''}
                <div style="display:flex;gap:8px">
                    <button class="btn btn-success btn-sm" onclick="respondRequest(${r.id}, 'accepted')">✅ Accepter</button>
                    <button class="btn btn-danger  btn-sm" onclick="respondRequest(${r.id}, 'refused')">❌ Refuser</button>
                </div>
            </div>`).join('');
    } catch (_) {}
}

async function respondRequest(requestId, action) {
    try {
        const res  = await fetch('/api/matches/respond.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ request_id: requestId, action }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) loadRequests();
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.textContent = message;
    toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;min-width:260px;animation:fadeIn .3s';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('students-grid'))  loadStudents();
    if (document.getElementById('requests-container')) loadRequests();
});
