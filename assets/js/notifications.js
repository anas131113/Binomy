/**
 * BINOMY — notifications.js
 * Polling des notifications toutes les 30 secondes via Fetch API
 */

let notifPanel = null;

async function fetchNotifications() {
    try {
        const res  = await fetch('/api/notifications/get.php');
        const data = await res.json();
        if (!data.success) return;

        updateBadge(data.data.unread_count);
        renderNotifications(data.data.notifications);
    } catch (_) { /* réseau indisponible */ }
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

    list.innerHTML = notifications.map(n => `
        <div class="notif-item ${n.is_read ? '' : 'unread'}" style="
            padding:12px 16px;
            border-bottom:1px solid var(--border);
            background:${n.is_read ? 'transparent' : 'var(--primary-l)'};
        ">
            <div style="font-size:.875rem;margin-bottom:4px">${escapeHtml(n.content)}</div>
            <div style="font-size:.75rem;color:var(--text-muted)">${formatDate(n.created_at)}</div>
        </div>
    `).join('');
}

async function markAllRead() {
    await fetch('/api/notifications/mark_read.php', { method: 'POST' });
    updateBadge(0);
    document.querySelectorAll('.notif-item.unread').forEach(el => {
        el.classList.remove('unread');
        el.style.background = 'transparent';
    });
}

function toggleNotifPanel() {
    const panel = document.getElementById('notif-panel');
    if (!panel) return;
    const open = panel.style.display !== 'block';
    panel.style.display = open ? 'block' : 'none';
    if (open) markAllRead();
}

function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
}

document.addEventListener('DOMContentLoaded', () => {
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});
