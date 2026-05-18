/**
 * BINOMY — chat.js
 * Messagerie via polling Fetch API.
 */

let activeMatchId = null;
let activeListingId = null;
let activeReceiverId = null;
let pollInterval = null;

async function loadMatches() {
    const list = document.getElementById('chat-list');
    if (!list) return;
    list.innerHTML = '<div class="spinner"></div>';
    try {
        const res = await fetch(apiUrl('api/matches/get_matches.php'));
        const data = await res.json();

        if (!data.success || !data.data.length) {
            list.innerHTML = `<div class="empty-state" style="padding:24px">
                <div class="empty-state-icon" style="font-size:40px">💬</div>
                <p style="font-size:.875rem">Aucun match pour l'instant.<br>Envoyez des demandes !</p></div>`;
            return;
        }
        list.innerHTML = data.data.map(m => `
            <div class="chat-list-item" id="chat-item-${m.match_id}"
                 onclick="openMatchChat(${m.match_id},${m.partner_id},'${escHtml(m.partner_name)}')"
                 role="button" tabindex="0">
                <div class="student-avatar" style="width:40px;height:40px;font-size:.9rem;flex-shrink:0">
                    ${m.partner_name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase()}
                </div>
                <div>
                    <div style="font-weight:600;font-size:.9rem">${escHtml(m.partner_name)}</div>
                    <div style="font-size:.8rem;color:var(--text-muted)">📍 ${escHtml(m.partner_city || 'N/A')}</div>
                </div>
            </div>`).join('');
    } catch (_) { list.innerHTML = '<p style="padding:16px;color:var(--text-muted)">Erreur réseau.</p>'; }
}

async function openMatchChat(matchId, partnerId, partnerName) {
    activeMatchId = matchId;
    activeListingId = null;
    activeReceiverId = partnerId;

    document.querySelectorAll('.chat-list-item').forEach(el => el.classList.remove('active'));
    document.getElementById('chat-item-' + matchId)?.classList.add('active');

    const header = document.getElementById('chat-partner-name');
    if (header) header.textContent = partnerName + ' ✅';

    await loadMessages();
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(loadMessages, 5000);
}

async function openListingChat(listingId, partnerId, partnerName) {
    activeMatchId = null;
    activeListingId = listingId;
    activeReceiverId = partnerId;

    document.querySelectorAll('.chat-list-item').forEach(el => el.classList.remove('active'));
    document.getElementById(`owner-chat-item-${listingId}-${partnerId}`)?.classList.add('active');

    const header = document.getElementById('owner-chat-partner') || document.getElementById('chat-partner-name');
    if (header) header.textContent = partnerName;

    await loadMessages();
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(loadMessages, 5000);
}

async function loadMessages() {
    const area = document.getElementById('chat-messages');
    if (!area || (!activeMatchId && !activeListingId)) return;

    const params = activeMatchId ? `match_id=${activeMatchId}` : `listing_id=${activeListingId}`;
    try {
        const res = await fetch(apiUrl('api/messages/get_conversation.php?' + params));
        const data = await res.json();
        if (!data.success) return;

        const currentUserId = parseInt(document.getElementById('current-user-id')?.value ?? 0);

        if (!data.data.length) {
            area.innerHTML = `<div class="empty-state">
                <div class="empty-state-icon">💬</div><p>Aucun message. Dites bonjour !</p></div>`;
            return;
        }
        area.innerHTML = data.data.map(m => `
            <div>
                <div class="chat-bubble ${m.sender_id == currentUserId ? 'sent' : 'recv'}">${escHtml(m.content)}</div>
                <div style="font-size:.7rem;color:var(--text-muted);text-align:${m.sender_id == currentUserId ? 'right' : 'left'};margin-top:2px">
                    ${new Date(m.sent_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>`).join('');
        area.scrollTop = area.scrollHeight;
    } catch (_) { }
}

async function sendMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chat-input');
    const content = input?.value.trim();
    if (!content || !activeReceiverId) return;

    const body = { receiver_id: activeReceiverId, content };
    if (activeMatchId) body.match_id = activeMatchId;
    if (activeListingId) body.listing_id = activeListingId;

    try {
        const res = await fetch(apiUrl('api/messages/send.php'), {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) { input.value = ''; await loadMessages(); }
        else if (typeof showToast === 'function') showToast(data.message, 'error');
    } catch (_) { if (typeof showToast === 'function') showToast('Erreur réseau.', 'error'); }
}

function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str; return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('chat-form');
    if (form) form.addEventListener('submit', sendMessage);
});
