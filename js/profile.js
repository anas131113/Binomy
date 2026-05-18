/**
 * BINOMY — profile.js
 * Chargement et sauvegarde du profil étudiant.
 */

async function loadProfile() {
    try {
        const res = await fetch(apiUrl('api/users/get_profile.php'));
        const data = await res.json();
        if (!data.success) return;
        const u = data.data;

        setVal('p-name', u.name);
        setVal('p-phone', u.phone);
        setVal('p-city', u.city);
        setVal('p-bio', u.bio);
        setVal('p-available', u.available_from);
        setVal('p-bmin', u.budget_min);
        setVal('p-bmax', u.budget_max);
        setVal('p-clean', u.cleanliness);
        setVal('p-schedule', u.schedule);
        setVal('p-looking', u.looking_for);
        setVal('p-rules', u.personal_rules);

        const smokingEl = document.getElementById('p-smoking');
        const petsEl = document.getElementById('p-pets');
        if (smokingEl) smokingEl.checked = !!u.smoking;
        if (petsEl) petsEl.checked = !!u.pets_ok;
    } catch (_) {
        showAlert('profile-alert', 'Erreur lors du chargement du profil.', 'error');
    }
}

function setVal(id, value) {
    const el = document.getElementById(id);
    if (el && value !== null && value !== undefined) el.value = value;
}

async function saveProfile(event) {
    event.preventDefault();
    clearAlert('profile-alert');

    const body = {
        name: document.getElementById('p-name')?.value.trim() ?? '',
        phone: document.getElementById('p-phone')?.value.trim() ?? '',
        city: document.getElementById('p-city')?.value.trim() ?? '',
        bio: document.getElementById('p-bio')?.value.trim() ?? '',
        available_from: document.getElementById('p-available')?.value ?? null,
        budget_min: document.getElementById('p-bmin')?.value ?? null,
        budget_max: document.getElementById('p-bmax')?.value ?? null,
        cleanliness: document.getElementById('p-clean')?.value ?? null,
        schedule: document.getElementById('p-schedule')?.value ?? null,
        looking_for: document.getElementById('p-looking')?.value ?? 'both',
        personal_rules: document.getElementById('p-rules')?.value.trim() ?? null,
        smoking: document.getElementById('p-smoking')?.checked ? 1 : 0,
        pets_ok: document.getElementById('p-pets')?.checked ? 1 : 0,
    };

    if (!body.name) { showAlert('profile-alert', 'Le nom est obligatoire.', 'error'); return; }

    try {
        const res = await fetch(apiUrl('api/users/update_profile.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) {
            showAlert('profile-alert', data.message, 'success');
            if (typeof showToast === 'function') showToast(data.message, 'success');
            const nav = document.getElementById('nav-username');
            if (nav) nav.textContent = body.name;
        } else {
            showAlert('profile-alert', data.message, 'error');
        }
    } catch (_) { showAlert('profile-alert', 'Erreur réseau.', 'error'); }
}

function showAlert(id, msg, type = 'error') {
    const el = document.getElementById(id);
    if (el) el.innerHTML = `<div class="alert alert-${type}">${escHtml(msg)}</div>`;
}
function clearAlert(id) { const el = document.getElementById(id); if (el) el.innerHTML = ''; }
function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str; return d.innerHTML;
}
