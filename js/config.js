/**
 * BINOMY — config.js
 * Chemins API / pages relatifs à la page courante.
 * Compatible sous-dossier XAMPP (/binomy/) et virtual host (racine /).
 */

function isInHtmlFolder() {
    return /\/html\//i.test(window.location.pathname);
}

function pagePrefix() {
    return isInHtmlFolder() ? '../' : '';
}

/** Endpoint API (ex. api/auth/login.php). */
function apiUrl(endpoint) {
    return pagePrefix() + String(endpoint).replace(/^\//, '');
}

/** Page ou fichier à la racine du projet (index.html, html/..., uploads/...). */
function appUrl(relativePath) {
    return pagePrefix() + String(relativePath).replace(/^\//, '');
}

/** Images / uploads renvoyés par l'API. */
function assetUrl(path) {
    return appUrl(path);
}

async function parseJsonResponse(res) {
    const text = await res.text();
    try {
        return JSON.parse(text);
    } catch {
        throw new Error('invalid_json');
    }
}
