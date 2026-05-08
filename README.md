# Binomy — Plateforme de Colocation Étudiante

> Mini-Projet PHP/JavaScript | TEK-UP University  
> Stack : PHP 8 + PDO + MySQL + Fetch API + HTML/CSS/JS Vanilla

---

## Table des matières

1. [Présentation](#présentation)
2. [Installation](#installation-étape-par-étape)
3. [Structure des fichiers](#structure-des-fichiers)
4. [Architecture technique](#architecture-technique)
5. [Diagramme de classes UML](#diagramme-de-classes-uml)
6. [Entités de la base de données](#entités-de-la-base-de-données)
7. [API Endpoints](#api-endpoints)
8. [Flux utilisateurs](#flux-utilisateurs)
9. [Sécurité](#sécurité)
10. [Équipe](#équipe)

---

## Présentation

**Binomy** est une plateforme web dynamique qui connecte les étudiants tunisiens avec :
- Des **colocataires compatibles** (système de matching basé sur les préférences)
- Des **logements abordables** (annonces publiées par des propriétaires)

**Problème résolu :** Les étudiants qui quittent leur ville pour étudier n'ont pas d'outil centralisé pour trouver un colocataire de confiance et un logement adapté.

---

## Installation — Étape par étape

### Prérequis
- PHP >= 8.0
- MySQL >= 8.0
- Serveur Apache/Nginx (XAMPP, WAMP, Laragon, MAMP...)

### Étape 1 — Cloner le projet

```bash
git clone https://github.com/VOTRE_USERNAME/binomy.git
cd binomy
```

### Étape 2 — Configurer la base de données

1. Ouvrez **phpMyAdmin** ou votre client MySQL
2. Exécutez le script SQL :

```sql
-- Dans phpMyAdmin → Onglet SQL
SOURCE db/schema.sql;
```

Ou via terminal :
```bash
mysql -u root -p < db/schema.sql
```

### Étape 3 — Configurer la connexion PDO

Éditez le fichier `config/db.php` :

```php
define('DB_HOST', 'localhost');  // Votre hôte MySQL
define('DB_NAME', 'binomy');     // Nom de la base
define('DB_USER', 'root');       // Votre utilisateur MySQL
define('DB_PASS', '');           // Votre mot de passe MySQL
```

### Étape 4 — Créer le compte admin

```bash
php db/seed.php
```

Cela crée :
- **Email :** `admin@binomy.tn`
- **Mot de passe :** `admin123`

### Étape 5 — Lancer le serveur

**Option A — PHP built-in server (développement) :**
```bash
php -S localhost:8000
```
Puis ouvrir : http://localhost:8000

**Option B — XAMPP :**
Placer le dossier `binomy/` dans `htdocs/`, puis ouvrir `http://localhost/binomy`

---

## Structure des fichiers

```
binomy/
├── index.php                    ← Page d'accueil + formulaires auth
├── config/
│   └── db.php                   ← Connexion PDO centralisée
├── db/
│   ├── schema.sql               ← Script de création des tables
│   └── seed.php                 ← Création du compte admin initial
├── uml/
│   └── class_diagram.puml       ← Diagramme de classes PlantUML
├── api/                         ← Tous les endpoints (appelés par Fetch API)
│   ├── helpers.php              ← Fonctions partagées (jsonResponse, requireAuth...)
│   ├── auth/
│   │   ├── register.php         ← POST : inscription
│   │   ├── login.php            ← POST : connexion
│   │   └── logout.php           ← POST : déconnexion
│   ├── users/
│   │   ├── get_profile.php      ← GET  : profil de l'utilisateur connecté
│   │   ├── update_profile.php   ← POST : mise à jour du profil
│   │   └── get_students.php     ← GET  : liste des étudiants (avec filtres)
│   ├── listings/
│   │   ├── get_all.php          ← GET  : toutes les annonces (filtres + pagination)
│   │   ├── get_one.php          ← GET  : détail d'une annonce
│   │   ├── create.php           ← POST : créer une annonce (multipart/form-data)
│   │   ├── update.php           ← POST : modifier une annonce
│   │   └── delete.php           ← POST : supprimer une annonce
│   ├── matches/
│   │   ├── send_request.php     ← POST : envoyer une demande de colocation
│   │   ├── respond.php          ← POST : accepter ou refuser une demande
│   │   ├── get_requests.php     ← GET  : demandes reçues en attente
│   │   └── get_matches.php      ← GET  : mes matches actifs
│   ├── messages/
│   │   ├── send.php             ← POST : envoyer un message
│   │   └── get_conversation.php ← GET  : messages d'une conversation
│   ├── notifications/
│   │   ├── get.php              ← GET  : mes notifications
│   │   └── mark_read.php        ← POST : marquer toutes comme lues
│   └── admin/
│       ├── get_stats.php        ← GET  : statistiques globales
│       ├── get_users.php        ← GET  : liste des utilisateurs
│       └── suspend_user.php     ← POST : suspendre/réactiver un compte
├── pages/
│   ├── dashboard_student.php    ← Dashboard étudiant (SPA-like)
│   ├── dashboard_locataire.php  ← Dashboard propriétaire
│   └── dashboard_admin.php      ← Dashboard admin
├── assets/
│   ├── css/
│   │   └── style.css            ← Feuille de style complète
│   └── js/
│       ├── auth.js              ← Inscription / Connexion / Déconnexion
│       ├── listings.js          ← Chargement et affichage des annonces
│       ├── matches.js           ← Matching entre étudiants
│       ├── chat.js              ← Messagerie temps réel (polling)
│       └── notifications.js     ← Polling notifications (30 secondes)
├── uploads/
│   ├── listings/                ← Images des annonces uploadées
│   └── avatars/                 ← Photos de profil
└── .gitignore
```

---

## Architecture technique

```
[ Navigateur — HTML/CSS/JS Vanilla ]
            ↕  Fetch API (JSON)
[ Serveur PHP — Logique métier + PDO ]
            ↕  Requêtes SQL préparées
[ Base de données MySQL ]
```

### Principe de fonctionnement des API

Chaque endpoint PHP :
1. Vérifie la session (`requireAuth()` ou `requireRole()`)
2. Reçoit les données (`$_POST`, `$_GET`, ou `php://input` pour JSON)
3. Exécute des requêtes PDO avec des paramètres liés (`:param`)
4. Retourne **toujours** une réponse JSON :

```json
{
  "success": true,
  "data": { ... },
  "message": "Description du résultat"
}
```

### Côté JavaScript — Fetch API

```javascript
// Exemple : envoyer une demande de colocation
const res  = await fetch('/api/matches/send_request.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify({ receiver_id: 5, message: 'Bonjour !' }),
});
const data = await res.json();
console.log(data.success, data.message);
```

---

## Diagramme de classes UML

Le fichier `uml/class_diagram.puml` contient le diagramme complet au format PlantUML.

**Pour le visualiser :**
- En ligne : https://www.plantuml.com/plantuml/uml/ (coller le contenu)
- VS Code : installer l'extension **PlantUML** de jebbs
- IntelliJ : plugin PlantUML intégré

### Entités et relations résumées

| Entité | Rôle |
|--------|------|
| `User` | Tous les utilisateurs (student / locataire / admin) |
| `StudentProfile` | Extension du profil étudiant (budget, préférences...) |
| `Listing` | Annonce de logement publiée par un propriétaire |
| `ListingImage` | Photos associées à une annonce |
| `RoommateRequest` | Demande de colocation entre deux étudiants |
| `Match` | Créé automatiquement quand une demande est acceptée |
| `Message` | Messages entre utilisateurs (après match ou via annonce) |
| `Notification` | Alertes générées côté serveur pour chaque événement clé |

---

## Entités de la base de données

### `users` — Table principale

| Champ | Type | Description |
|-------|------|-------------|
| `id` | INT PK AUTO | Identifiant unique |
| `name` | VARCHAR(100) | Nom complet |
| `email` | VARCHAR(150) UNIQUE | Email de connexion |
| `password` | VARCHAR(255) | Hash bcrypt |
| `role` | ENUM | student / locataire / admin |
| `is_active` | TINYINT(1) | Compte actif ou suspendu |

### `student_profiles` — Extension pour les étudiants

| Champ | Type | Description |
|-------|------|-------------|
| `user_id` | INT FK | Référence vers `users.id` |
| `budget_min/max` | INT | Budget mensuel (TND) |
| `cleanliness` | TINYINT | Niveau de propreté (1–5) |
| `smoking` | TINYINT(1) | Fumeur ou non |
| `schedule` | ENUM | early_bird / night_owl / flexible |

### `roommate_requests` — Demandes de colocation

| Champ | Type | Description |
|-------|------|-------------|
| `sender_id` | INT FK | Étudiant qui envoie |
| `receiver_id` | INT FK | Étudiant qui reçoit |
| `status` | ENUM | pending / accepted / refused / cancelled |

**Contraintes :**
- `sender_id ≠ receiver_id`
- `UNIQUE(sender_id, receiver_id)` — pas de doublon

---

## API Endpoints

### Authentification

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/auth/register.php` | Inscription |
| POST | `/api/auth/login.php` | Connexion |
| POST | `/api/auth/logout.php` | Déconnexion |

### Utilisateurs

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/users/get_profile.php` | Profil de l'utilisateur connecté |
| POST | `/api/users/update_profile.php` | Mise à jour du profil |
| GET | `/api/users/get_students.php` | Liste des étudiants |

### Annonces

| Méthode | Endpoint | Description | Paramètres |
|---------|----------|-------------|------------|
| GET | `/api/listings/get_all.php` | Liste des annonces | `city`, `type`, `max_price`, `page` |
| GET | `/api/listings/get_one.php` | Détail d'une annonce | `id` |
| POST | `/api/listings/create.php` | Créer une annonce | multipart/form-data |
| POST | `/api/listings/update.php` | Modifier | JSON body |
| POST | `/api/listings/delete.php` | Supprimer | `{"id": X}` |

### Matching

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/matches/send_request.php` | Envoyer une demande |
| POST | `/api/matches/respond.php` | Accepter / Refuser |
| GET | `/api/matches/get_requests.php` | Demandes reçues |
| GET | `/api/matches/get_matches.php` | Mes matches |

### Messages

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST | `/api/messages/send.php` | Envoyer un message |
| GET | `/api/messages/get_conversation.php` | Messages d'une conversation |

### Notifications

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/notifications/get.php` | Mes notifications |
| POST | `/api/notifications/mark_read.php` | Marquer toutes comme lues |

### Admin

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/admin/get_stats.php` | Statistiques globales |
| GET | `/api/admin/get_users.php` | Liste des utilisateurs |
| POST | `/api/admin/suspend_user.php` | Suspendre / Réactiver |

---

## Flux utilisateurs

### Étudiant — Trouver un colocataire

```
1. S'inscrire (role: student)
2. Compléter le profil (budget, préférences, rythme de vie)
3. Parcourir les étudiants disponibles dans sa ville
4. Envoyer une demande de colocation
5. L'autre étudiant accepte → Match créé → Chat activé
6. Discuter des détails via la messagerie
```

### Étudiant — Trouver un logement

```
1. Naviguer vers "Logements"
2. Filtrer par ville, budget, type
3. Consulter une annonce
4. Contacter le propriétaire via le formulaire
5. Continuer la conversation dans la messagerie
```

### Propriétaire — Publier une annonce

```
1. S'inscrire (role: locataire)
2. Cliquer "Publier une annonce"
3. Remplir le formulaire + upload de photos
4. Annonce visible pour les étudiants
5. Recevoir des messages d'étudiants
6. Marquer "Loué" quand le logement est occupé
```

---

## Sécurité

| Mesure | Implémentation |
|--------|---------------|
| Hachage des mots de passe | `password_hash($pass, PASSWORD_BCRYPT)` |
| Vérification des mots de passe | `password_verify($input, $hash)` |
| Protection injections SQL | PDO avec requêtes préparées (`:param`) |
| Protection XSS | `htmlspecialchars()` sur tous les outputs |
| Régénération de session | `session_regenerate_id(true)` après login |
| Contrôle d'accès par rôle | `requireRole('student')` sur chaque endpoint |
| Upload d'images | Validation MIME + taille max 5MB + renommage `uniqid()` |
| Autorisation propriétaire | Vérification `owner_id === user_id` avant modification |

---

## Équipe

| Membre | Module | Entités |
|--------|--------|---------|
| Étudiant A | Authentification + Profils + Admin | `users`, `student_profiles` |
| Étudiant B | Annonces + Upload images | `listings`, `listing_images` |
| Étudiant C | Matching + Notifications | `roommate_requests`, `matches`, `notifications` |
| Étudiant D | Messagerie + Dashboard intégration | `messages` |

---

## Workflow Git recommandé

```bash
# Créer une branche de fonctionnalité
git checkout -b feature/listings

# Développer + committer régulièrement
git add api/listings/create.php
git commit -m "feat(listings): add create endpoint with image upload"

# Fusionner dans main
git checkout main
git merge feature/listings
git push origin main
```

---

*Binomy — Mini-Projet PHP/JavaScript | TEK-UP University 2024*
