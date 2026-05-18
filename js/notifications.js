/**
 * BINOMY — notifications.js
 * Polling des notifications toutes les 30 secondes.
 */

async function fetchNotifications() {
    try {
        const res = await fetch(apiUrl('api/notifications/get.php'));
        const data = await res.json();
        if (!data.success) return;
        updateBadge(data.data.unread_count);
        renderNotifications(data.data.notifications);
    } catch (_) { }
}

function updateBadge(count) {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;
    badge.textContent = count;
    badge.hidden = count === 0;
}

function renderNotifications(notifications) {
    const list = document.getElementById('notif-list');
    if (!list) return;
    if (!notifications.length) {
        list.innerHTML = '<p style="padding:16px;color:var(--text-muted);text-align:center">Aucune notification</p>';
        return;
    }
    const icons = { new_request: '💌', request_accepted: '🤝', request_refused: '❌', new_message: '💬', listing_update: '🏠' };
    list.innerHTML = notifications.map(n => `
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);background:${n.is_read ? 'transparent' : 'var(--primary-l)'}">
            <div style="font-size:.875rem;margin-bottom:4px">${icons[n.type] || '🔔'} ${escapeHtml(n.content)}</div>
            <div style="font-size:.75rem;color:var(--text-muted)">${formatDate(n.created_at)}</div>
        </div>`).join('');
}

async function markAllRead() {
    try {
        await fetch(apiUrl('api/notifications/mark_read.php'), { method: 'POST' });
        updateBadge(0);
        document.querySelectorAll('#notif-list > div').forEach(el => el.style.background = 'transparent');
    } catch (_) { }
}

function toggleNotifPanel() {
    const panel = document.getElementById('notif-panel');
    if (!panel) return;
    const isOpen = panel.style.display === 'block';
    panel.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) markAllRead();
}

document.addEventListener('click', e => {
    const panel = document.getElementById('notif-panel');
    const btn = document.getElementById('notif-btn');
    if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target))
        panel.style.display = 'none';
});

function escapeHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str; return d.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}
