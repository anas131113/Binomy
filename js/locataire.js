/**
 * BINOMY — locataire.js
 * CRUD annonces du propriétaire.
 */

// ============================================================
// Charger les annonces du propriétaire
// ============================================================
async function loadMyListings() {
    const container = document.getElementById('my-listings-container');
    if (!container) return;
    container.innerHTML = '<div class="spinner"></div>';
    try {
        const res = await fetch(apiUrl('api/listings/get_mine.php?page=1'));
        const data = await res.json();

        if (!data.success || !data.data.listings.length) {
            container.innerHTML = `<div class="empty-state">
                <div class="empty-state-icon">📋</div><h3>Aucune annonce publiée</h3>
                <p>Publiez votre première annonce !</p>
                <button class="btn btn-primary" onclick="showSection('new')" style="margin-top:16px">➕ Publier</button>
            </div>`;
            updateOwnerStats(0, 0, 0); return;
        }

        const listings = data.data.listings;
        const available = listings.filter(l => l.status === 'available').length;
        const rented = listings.filter(l => l.status === 'rented').length;
        updateOwnerStats(listings.length, available, rented);
        renderMyListingsTable(listings);
    } catch (_) { container.innerHTML = '<p class="text-muted text-center">Erreur réseau.</p>'; }
}

function renderMyListingsTable(listings) {
    const container = document.getElementById('my-listings-container');
    if (!container) return;
    container.innerHTML = `
        <div class="table-wrap"><table>
            <thead><tr><th>Titre</th><th>Type</th><th>Ville</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>${listings.map(l => `
                <tr>
                    <td><strong>${escHtml(l.title)}</strong></td>
                    <td>${escHtml(l.type)}</td>
                    <td>📍 ${escHtml(l.city)}</td>
                    <td><strong>${parseFloat(l.price).toLocaleString('fr-TN')} TND</strong></td>
                    <td><span class="badge ${l.status === 'available' ? 'badge-success' : 'badge-warning'}">
                        ${l.status === 'available' ? '✅ Disponible' : '🔑 Louée'}</span></td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap">
                        <button class="btn btn-outline btn-sm" onclick="editListing(${l.id})">✏️ Modifier</button>
                        ${l.status === 'available'
            ? `<button class="btn btn-sm" style="background:#F59E0B;color:#fff" onclick="markRented(${l.id})">🔑 Louer</button>`
            : `<button class="btn btn-sm btn-outline" onclick="markAvailable(${l.id})">✅ Disponible</button>`}
                        <button class="btn btn-danger btn-sm" onclick="deleteListing(${l.id})">🗑️</button>
                    </td>
                </tr>`).join('')}
            </tbody>
        </table></div>`;
}

function updateOwnerStats(total, available, rented) {
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('stat-total', total); set('stat-available', available); set('stat-rented', rented);
}

// ============================================================
// Publier une annonce
// ============================================================
async function submitListing(event) {
    event.preventDefault();
    clearAlert('new-listing-alert');

    const formData = new FormData();
    formData.set('title', document.getElementById('nl-title').value.trim());
    formData.set('description', document.getElementById('nl-desc').value.trim());
    formData.set('type', document.getElementById('nl-type').value);
    formData.set('price', document.getElementById('nl-price').value);
    formData.set('city', document.getElementById('nl-city').value.trim());
    formData.set('address', document.getElementById('nl-address').value.trim());
    formData.set('rooms', document.getElementById('nl-rooms').value);
    formData.set('surface', document.getElementById('nl-surface').value);

    const imagesInput = document.getElementById('nl-images');
    if (imagesInput?.files) {
        Array.from(imagesInput.files).forEach(file => formData.append('images[]', file));
    }

    const btn = document.getElementById('nl-submit-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Publication…'; }

    try {
        const res = await fetch(apiUrl('api/listings/create.php'), { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            showAlert('new-listing-alert', data.message, 'success');
            if (typeof showToast === 'function') showToast(data.message, 'success');
            document.getElementById('new-listing-form').reset();
            setTimeout(() => showSection('listings'), 1500);
        } else { showAlert('new-listing-alert', data.message, 'error'); }
    } catch (_) { showAlert('new-listing-alert', 'Erreur réseau.', 'error'); }
    finally { if (btn) { btn.disabled = false; btn.textContent = '📤 Publier l\'annonce'; } }
}

// ============================================================
// Modifier une annonce
// ============================================================
async function editListing(id) {
    try {
        const res = await fetch(apiUrl('api/listings/get_one.php?id=' + id));
        const data = await res.json();
        if (!data.success) return;
        const l = data.data;
        document.getElementById('el-id').value = l.id;
        document.getElementById('el-title').value = l.title;
        document.getElementById('el-price').value = l.price;
        document.getElementById('el-city').value = l.city;
        document.getElementById('el-address').value = l.address || '';
        document.getElementById('el-rooms').value = l.rooms || '';
        document.getElementById('el-surface').value = l.surface || '';
        document.getElementById('el-status').value = l.status;
        document.getElementById('el-desc').value = l.description;
        showSection('edit');
    } catch (_) { showToast('Erreur lors du chargement.', 'error'); }
}

async function updateListing(event) {
    event.preventDefault();
    clearAlert('edit-listing-alert');
    const body = {
        id: parseInt(document.getElementById('el-id').value),
        title: document.getElementById('el-title').value.trim(),
        description: document.getElementById('el-desc').value.trim(),
        price: parseFloat(document.getElementById('el-price').value),
        city: document.getElementById('el-city').value.trim(),
        address: document.getElementById('el-address').value.trim(),
        rooms: parseInt(document.getElementById('el-rooms').value) || null,
        surface: parseInt(document.getElementById('el-surface').value) || null,
        status: document.getElementById('el-status').value,
    };
    try {
        const res = await fetch(apiUrl('api/listings/update.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) { showToast(data.message, 'success'); showSection('listings'); }
        else showAlert('edit-listing-alert', data.message, 'error');
    } catch (_) { showAlert('edit-listing-alert', 'Erreur réseau.', 'error'); }
}

// ============================================================
// Changer le statut
// ============================================================
async function markRented(id) { await changeStatus(id, 'rented', '🔑 Annonce marquée comme louée.'); }
async function markAvailable(id) { await changeStatus(id, 'available', '✅ Annonce remise en disponible.'); }

async function changeStatus(id, status, msg) {
    try {
        const getRes = await fetch(apiUrl('api/listings/get_one.php?id=' + id));
        const getData = await getRes.json();
        if (!getData.success) return;
        const l = getData.data;
        const res = await fetch(apiUrl('api/listings/update.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: l.id, title: l.title, description: l.description,
                price: l.price, city: l.city, address: l.address, rooms: l.rooms, surface: l.surface, status
            }),
        });
        const data = await res.json();
        if (data.success) { showToast(msg, 'success'); loadMyListings(); }
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

// ============================================================
// Supprimer
// ============================================================
async function deleteListing(id) {
    if (!confirm('Supprimer cette annonce ? Action irréversible.')) return;
    try {
        const res = await fetch(apiUrl('api/listings/delete.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }),
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) loadMyListings();
    } catch (_) { showToast('Erreur réseau.', 'error'); }
}

// ============================================================
// Conversations reçues
// ============================================================
async function loadOwnerConversations() {
    const list = document.getElementById('owner-chat-list');
    if (!list) return;
    list.innerHTML = `<div style="padding:16px;font-size:.875rem;color:var(--text-muted)">
        <p>Les étudiants vous contactent depuis vos annonces.</p>
        <p style="margin-top:8px">Les conversations apparaîtront ici.</p></div>`;
    const msgStat = document.getElementById('stat-msgs');
    if (msgStat) msgStat.textContent = '—';
}

// ============================================================
// Helpers
// ============================================================
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
