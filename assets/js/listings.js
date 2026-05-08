/**
 * BINOMY — listings.js
 * Chargement et affichage des annonces de logements via Fetch API
 */

let currentPage = 1;

async function loadListings(page = 1) {
    currentPage = page;

    const city     = document.getElementById('filter-city')?.value     ?? '';
    const type     = document.getElementById('filter-type')?.value     ?? '';
    const maxPrice = document.getElementById('filter-price')?.value    ?? '';

    const params = new URLSearchParams({ page });
    if (city)     params.set('city', city);
    if (type)     params.set('type', type);
    if (maxPrice) params.set('max_price', maxPrice);

    const grid = document.getElementById('listings-grid');
    if (!grid) return;
    grid.innerHTML = '<div class="spinner"></div>';

    try {
        const res  = await fetch(`/api/listings/get_all.php?${params}`);
        const data = await res.json();

        if (!data.success) { grid.innerHTML = '<p class="text-muted text-center">Erreur de chargement.</p>'; return; }
        if (!data.data.listings.length) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1">
                    <div class="empty-state-icon">🏠</div>
                    <h3>Aucune annonce trouvée</h3>
                    <p>Essayez de modifier vos filtres</p>
                </div>`;
            return;
        }

        grid.innerHTML = data.data.listings.map(renderListingCard).join('');
        renderPagination(data.data.page, data.data.totalPages);
    } catch (_) {
        grid.innerHTML = '<p class="text-muted text-center">Erreur réseau.</p>';
    }
}

function renderListingCard(l) {
    const imgHtml = l.main_image
        ? `<img src="/${l.main_image}" alt="${escHtml(l.title)}" class="listing-img" style="object-fit:cover">`
        : `<div class="listing-img">🏠</div>`;

    return `
        <div class="listing-card" onclick="viewListing(${l.id})">
            ${imgHtml}
            <div class="listing-body">
                <div class="listing-price">${parseFloat(l.price).toLocaleString('fr-TN')} TND <span>/ mois</span></div>
                <div class="listing-title">${escHtml(l.title)}</div>
                <div class="listing-meta">
                    <span>📍 ${escHtml(l.city)}</span>
                    <span>🏷️ ${escHtml(l.type)}</span>
                    ${l.rooms   ? `<span>🚪 ${l.rooms} pièces</span>` : ''}
                    ${l.surface ? `<span>📐 ${l.surface} m²</span>`   : ''}
                </div>
            </div>
        </div>`;
}

async function viewListing(id) {
    try {
        const res  = await fetch(`/api/listings/get_one.php?id=${id}`);
        const data = await res.json();
        if (!data.success) return;
        showListingModal(data.data);
    } catch (_) {}
}

function showListingModal(l) {
    const modal = document.getElementById('listing-modal');
    if (!modal) return;

    const images = l.images?.length
        ? l.images.map(img => `<img src="/${img.image_path}" style="width:100%;height:250px;object-fit:cover;border-radius:8px;margin-bottom:8px">`).join('')
        : `<div class="listing-img" style="height:200px;border-radius:8px">🏠</div>`;

    modal.querySelector('.modal-body').innerHTML = `
        ${images}
        <h2 style="margin:16px 0 8px">${escHtml(l.title)}</h2>
        <div style="font-size:1.5rem;font-weight:800;color:var(--primary);margin-bottom:12px">
            ${parseFloat(l.price).toLocaleString('fr-TN')} TND / mois
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
            <span class="badge badge-primary">📍 ${escHtml(l.city)}</span>
            <span class="badge badge-info">🏷️ ${escHtml(l.type)}</span>
            ${l.rooms   ? `<span class="badge badge-primary">🚪 ${l.rooms} pièces</span>`  : ''}
            ${l.surface ? `<span class="badge badge-primary">📐 ${l.surface} m²</span>`    : ''}
        </div>
        <p style="color:var(--text-muted);margin-bottom:16px">${escHtml(l.description)}</p>
        <div class="card" style="margin-bottom:16px">
            <strong>Propriétaire :</strong> ${escHtml(l.owner_name)}<br>
            ${l.owner_phone ? `<strong>Téléphone :</strong> ${escHtml(l.owner_phone)}<br>` : ''}
        </div>
        <button class="btn btn-primary btn-lg" onclick="contactOwner(${l.id}, ${l.owner_id})" style="width:100%">
            💬 Contacter le propriétaire
        </button>`;

    modal.classList.add('open');
}

async function contactOwner(listingId, ownerId) {
    const content = prompt('Votre message au propriétaire :');
    if (!content) return;

    try {
        const res  = await fetch('/api/messages/send.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ receiver_id: ownerId, listing_id: listingId, content }),
        });
        const data = await res.json();
        alert(data.message);
    } catch (_) { alert('Erreur réseau.'); }
}

function renderPagination(page, total) {
    const el = document.getElementById('pagination');
    if (!el || total <= 1) return;

    let html = `<div style="display:flex;gap:8px;justify-content:center;margin-top:24px">`;
    if (page > 1) html += `<button class="btn btn-outline btn-sm" onclick="loadListings(${page - 1})">← Précédent</button>`;
    html += `<span style="padding:8px 16px;color:var(--text-muted)">Page ${page} / ${total}</span>`;
    if (page < total) html += `<button class="btn btn-outline btn-sm" onclick="loadListings(${page + 1})">Suivant →</button>`;
    html += '</div>';

    el.innerHTML = html;
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('listings-grid')) loadListings();
});
