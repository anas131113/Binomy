# BINOMY — Cahier des Charges
**Spécifications Produit & Architecture Système**  
Version 1.0 | Mini-Projet PHP/JavaScript | TEK-UP University

---

## PARTIE 1 — VUE D'ENSEMBLE

### 1.1 Présentation

Binomy est une plateforme web dynamique destinée aux étudiants tunisiens. Elle résout deux problèmes liés : trouver un colocataire compatible, et trouver un logement adapté à un budget étudiant. Le nom "Binomy" évoque le binôme — la paire, l'association.

Le projet s'inscrit dans le cadre du mini-projet PHP/JavaScript de TEK-UP. Il utilise une architecture classique Frontend (HTML/CSS/JS) + Backend (PHP + PDO) avec communication JSON asynchrone via l'API Fetch.

### 1.2 Problème résolu

Les étudiants tunisiens qui quittent leur ville pour étudier font face à :

- Difficulté à trouver des colocataires de confiance et compatibles
- Listings de logements dispersés, peu fiables, sans vérification
- Absence d'un outil centralisé adapté au contexte tunisien

### 1.3 Objectifs principaux

| # | Objectif | Priorité |
|---|----------|----------|
| 1 | Permettre aux étudiants de trouver des colocataires compatibles | Haute |
| 2 | Permettre aux propriétaires de publier et gérer leurs annonces | Haute |
| 3 | Offrir un système de messagerie entre les utilisateurs | Haute |
| 4 | Fournir une interface d'administration complète | Moyenne |
| 5 | Respecter les contraintes techniques du mini-projet | Obligatoire |

### 1.4 Contraintes techniques imposées

- **Backend :** PHP avec accès base de données exclusivement via PDO
- **Frontend :** JavaScript avec API Fetch obligatoire pour les échanges asynchrones
- **Format d'échange :** JSON (`json_encode` côté PHP)
- **Versioning :** Git obligatoire avec commits réguliers
- **UML :** Diagramme de classes requis avant développement
- **Taille du groupe :** 4 étudiants → minimum 4 entités gérées (hors User)

---

## PARTIE 2 — UTILISATEURS CIBLES

Le système repose sur une seule entité `User` avec un champ `role` qui détermine les accès.

### Rôle `student` — L'Étudiant
- Étudiant universitaire tunisien, souvent venu d'une autre ville
- Cherche un colocataire compatible ou un logement abordable
- Budget limité, priorité à la compatibilité humaine

### Rôle `locataire` — Le Propriétaire
- Particulier possédant un logement à louer
- Souhaite publier ses annonces et contacter des étudiants sérieux
- Peut marquer un logement comme loué une fois occupé

### Rôle `admin` — L'Administrateur
- Gestionnaire de la plateforme
- Accès à toutes les données, capacité de modération
- Visualise les statistiques globales d'utilisation

---

## PARTIE 3 — EXIGENCES FONCTIONNELLES

### 3.1 Authentification (tous les rôles)

| ID | Fonctionnalité | Description |
|----|---------------|-------------|
| AUTH-01 | Inscription | Formulaire avec email, mot de passe, nom, rôle choisi |
| AUTH-02 | Connexion | Authentification par email/mot de passe |
| AUTH-03 | Déconnexion | Destruction de session |
| AUTH-04 | Consultation profil | Voir son propre profil |
| AUTH-05 | Modification profil | Mettre à jour ses informations |

### 3.2 Fonctionnalités Student

| ID | Fonctionnalité | Description |
|----|---------------|-------------|
| STU-01 | Compléter profil | Bio, préférences, règles personnelles, localisation, budget |
| STU-02 | Parcourir étudiants | Voir les étudiants disponibles dans la même zone |
| STU-03 | Envoyer une demande | Envoyer une demande de colocation à un autre étudiant |
| STU-04 | Gérer demandes reçues | Accepter ou refuser une demande de colocation |
| STU-05 | Chat | Messagerie activée après match mutuel |
| STU-06 | Parcourir annonces | Voir les logements disponibles |
| STU-07 | Contacter propriétaire | Envoyer un message à un locataire depuis une annonce |
| STU-08 | Notifications | Recevoir des alertes sur les événements importants |

### 3.3 Fonctionnalités Locataire

| ID | Fonctionnalité | Description |
|----|---------------|-------------|
| LOC-01 | Publier annonce | Titre, description, prix, localisation, photos, disponibilité |
| LOC-02 | Modifier annonce | Mettre à jour les informations d'une annonce |
| LOC-03 | Supprimer annonce | Retirer une annonce de la plateforme |
| LOC-04 | Marquer comme loué | Changer le statut d'un logement à "loué" |
| LOC-05 | Messagerie | Recevoir et répondre aux messages des étudiants |
| LOC-06 | Voir mes annonces | Liste de toutes ses annonces avec statuts |

### 3.4 Fonctionnalités Admin

| ID | Fonctionnalité | Description |
|----|---------------|-------------|
| ADM-01 | Dashboard statistiques | Nombre d'utilisateurs, annonces, matches, messages |
| ADM-02 | Gestion utilisateurs | Lister, voir, suspendre ou supprimer des comptes |
| ADM-03 | Gestion annonces | Lister, voir, supprimer des annonces |
| ADM-04 | Modération | Signaler et traiter des contenus problématiques |

---

## PARTIE 4 — EXIGENCES NON-FONCTIONNELLES

### 4.1 Sécurité

| Mesure | Implémentation |
|--------|---------------|
| Mots de passe | Hachés avec `password_hash()` (bcrypt) — jamais en clair |
| Sessions | Régénération d'ID après connexion (`session_regenerate_id()`) |
| Injections SQL | PDO exclusivement avec requêtes préparées (`:param`) |
| XSS | Échappement avec `htmlspecialchars()` sur tous les outputs |
| Autorisation | Chaque endpoint PHP vérifie le rôle en session |
| Upload images | Validation MIME, taille max 5MB, renommage `uniqid()` |

### 4.2 Performance

- Pagination sur toutes les listes (étudiants, annonces, messages)
- Images compressées côté serveur avant stockage
- Requêtes SQL optimisées avec JOIN

### 4.3 Architecture

- Séparation claire : logique métier (PHP), données (MySQL), présentation (HTML/JS)
- Code organisé en fichiers séparés par entité / rôle
- Base de données normalisée (3NF minimum)

---

## PARTIE 5 — FLUX UTILISATEURS

### 5.1 Étudiant — Trouver un colocataire

```
1. S'inscrire → choisir le rôle "student"
2. Compléter le profil : bio, ville, budget, préférences (propreté, fumeur, animaux, rythme)
3. Consulter la liste des étudiants disponibles dans sa zone
4. Envoyer une demande de colocation
5. L'autre étudiant reçoit une notification
6. Il accepte ou refuse la demande
7. Si accepté → un "Match" est créé → le chat s'ouvre
8. Discuter des détails via la messagerie
```

### 5.2 Étudiant — Trouver un logement

```
1. Naviguer vers la section "Logements"
2. Filtrer par ville, budget max, disponibilité
3. Consulter une annonce (photos, description, prix)
4. Cliquer "Contacter le propriétaire"
5. La conversation continue dans la messagerie
```

### 5.3 Locataire — Publier une annonce

```
1. S'inscrire → choisir le rôle "locataire"
2. Cliquer "Publier une annonce"
3. Remplir : titre, description, type, prix, localisation, photos
4. L'annonce est publiée et visible pour les étudiants
5. Recevoir des messages d'étudiants intéressés
6. Marquer l'annonce comme "louée" quand occupée
```

### 5.4 Admin — Modération

```
1. Se connecter → redirigé vers le dashboard
2. Voir les statistiques globales en temps réel
3. Filtrer les utilisateurs par rôle/date
4. Suspendre un compte abusif
5. Supprimer une annonce inappropriée
```

---

## PARTIE 6 — ARCHITECTURE SYSTÈME

### 6.1 Vue d'ensemble

```
[ Navigateur — HTML/CSS/JS Vanilla ]
            ↕  Fetch API (JSON)
[ Serveur PHP — Logique métier + PDO ]
            ↕  Requêtes SQL préparées
[ Base de données MySQL ]
```

### 6.2 Structure des fichiers

```
binomy/
├── index.php                     ← Point d'entrée / Landing page
├── config/db.php                 ← Connexion PDO centralisée
├── api/
│   ├── helpers.php               ← jsonResponse(), requireAuth(), requireRole()
│   ├── auth/                     ← register.php, login.php, logout.php
│   ├── users/                    ← get_profile.php, update_profile.php, get_students.php
│   ├── listings/                 ← get_all.php, get_one.php, create.php, update.php, delete.php
│   ├── matches/                  ← send_request.php, respond.php, get_requests.php, get_matches.php
│   ├── messages/                 ← send.php, get_conversation.php
│   ├── notifications/            ← get.php, mark_read.php
│   └── admin/                    ← get_stats.php, get_users.php, suspend_user.php
├── pages/
│   ├── dashboard_student.php
│   ├── dashboard_locataire.php
│   └── dashboard_admin.php
├── assets/
│   ├── css/style.css
│   └── js/                       ← auth.js, listings.js, matches.js, chat.js, notifications.js
├── db/
│   ├── schema.sql                ← Script de création des tables
│   └── seed.php                  ← Données initiales (compte admin)
└── uml/class_diagram.puml        ← Diagramme de classes PlantUML
```

### 6.3 Technologies

| Couche | Technologie | Justification |
|--------|-------------|---------------|
| Frontend | HTML5 + CSS3 + JavaScript vanilla | Exigé par le mini-projet |
| Communication async | Fetch API | Obligatoire selon le cahier de charges |
| Backend | PHP 8.1 | Exigé par le mini-projet |
| Base de données | MySQL 8 | Standard, compatible PDO |
| Accès BDD | PDO avec requêtes préparées | Obligatoire |
| Hachage MDP | `password_hash()` bcrypt | Sécurité standard PHP |
| Sessions | Sessions PHP natives | Simple, adapté au contexte |
| Versioning | Git + GitHub | Obligatoire |

### 6.4 Format des réponses API

Tous les endpoints retournent systématiquement :

```json
{
  "success": true,
  "data": { "..." },
  "message": "Description du résultat"
}
```

---

## PARTIE 7 — BASE DE DONNÉES

### 7.1 Entités et relations

```
User (1) ──────── (0..1) StudentProfile
User (1) ──────── (0..*) Listing
User (1) ──────── (0..*) RoommateRequest  [comme sender]
User (1) ──────── (0..*) RoommateRequest  [comme receiver]
User (1) ──────── (0..*) Message
User (1) ──────── (0..*) Notification
Listing (1) ───── (1..*) ListingImage
RoommateRequest (1) ── (0..1) Match
Match (1) ──────── (0..*) Message
```

### 7.2 Table : `users`

| Champ | Type | Contrainte | Description |
|-------|------|-----------|-------------|
| id | INT | PK AUTO | Identifiant unique |
| name | VARCHAR(100) | NOT NULL | Nom complet |
| email | VARCHAR(150) | UNIQUE NOT NULL | Email de connexion |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt |
| role | ENUM | NOT NULL | student / locataire / admin |
| avatar | VARCHAR(255) | NULL | Photo de profil |
| phone | VARCHAR(20) | NULL | Téléphone |
| city | VARCHAR(100) | NULL | Ville actuelle |
| bio | TEXT | NULL | Description personnelle |
| is_active | TINYINT(1) | DEFAULT 1 | Compte actif ou suspendu |
| created_at | DATETIME | DEFAULT NOW() | Date d'inscription |

### 7.3 Table : `student_profiles`

| Champ | Type | Description |
|-------|------|-------------|
| user_id | INT FK | Référence vers users.id |
| budget_min | INT | Budget minimum (TND) |
| budget_max | INT | Budget maximum (TND) |
| cleanliness | TINYINT | Niveau de propreté (1–5) |
| smoking | TINYINT(1) | Fumeur (0/1) |
| pets_ok | TINYINT(1) | Accepte les animaux (0/1) |
| schedule | ENUM | early_bird / night_owl / flexible |
| personal_rules | TEXT | Allergies, habitudes… |
| looking_for | ENUM | roommate / housing / both |
| available_from | DATE | Disponible à partir de |

### 7.4 Table : `listings`

| Champ | Type | Description |
|-------|------|-------------|
| owner_id | INT FK | Propriétaire (users.id) |
| title | VARCHAR(200) | Titre de l'annonce |
| type | ENUM | chambre / studio / appartement / maison |
| price | DECIMAL(8,2) | Loyer mensuel (TND) |
| city | VARCHAR(100) | Ville |
| status | ENUM | available / rented |

### 7.5 Table : `roommate_requests`

| Champ | Type | Description |
|-------|------|-------------|
| sender_id | INT FK | Étudiant qui envoie |
| receiver_id | INT FK | Étudiant qui reçoit |
| message | TEXT | Message d'introduction |
| status | ENUM | pending / accepted / refused / cancelled |
| responded_at | DATETIME | Date de réponse |

**Contraintes :** `sender_id ≠ receiver_id` + `UNIQUE(sender_id, receiver_id)`

### 7.6 Table : `matches`

Créée automatiquement quand une demande est acceptée.

| Champ | Type | Description |
|-------|------|-------------|
| request_id | INT FK UNIQUE | Demande à l'origine |
| user1_id | INT FK | Premier étudiant |
| user2_id | INT FK | Deuxième étudiant |

### 7.7 Table : `messages`

| Champ | Type | Description |
|-------|------|-------------|
| sender_id | INT FK | Expéditeur |
| receiver_id | INT FK | Destinataire |
| match_id | INT FK NULL | Si chat étudiant/étudiant |
| listing_id | INT FK NULL | Si contact propriétaire |
| content | TEXT | Contenu du message |
| is_read | TINYINT(1) | Lu ou non |

> **Règle :** `match_id` ou `listing_id` est renseigné, jamais les deux.

### 7.8 Table : `notifications`

| Champ | Type | Description |
|-------|------|-------------|
| user_id | INT FK | Destinataire |
| type | ENUM | new_request / request_accepted / request_refused / new_message / listing_update |
| reference_id | INT | ID de l'entité liée |
| content | VARCHAR(255) | Texte de la notification |
| is_read | TINYINT(1) | Lue ou non |

---

## PARTIE 8 — LOGIQUE MÉTIER

### 8.1 Système de matching

**Score de compatibilité (indicatif) :**

| Critère | Points |
|---------|--------|
| Même ville | +25 pts |
| Budgets compatibles | +20 pts |
| Niveau de propreté (±1) | +15 pts |
| Même rythme de vie | +20 pts |
| Compatibilité fumeur | +10 pts |
| Compatibilité animaux | +10 pts |
| **Total possible** | **100 pts** |

**Flux :**
1. Vérification : les deux sont étudiants, pas de demande existante
2. Insertion dans `roommate_requests` (status = pending)
3. Notification au destinataire
4. Le destinataire accepte ou refuse
5. Si accepté → ligne dans `matches` + chat activé

### 8.2 Messagerie

- **Chat étudiant/étudiant :** accessible uniquement si un match existe (`match_id` renseigné)
- **Contact propriétaire :** depuis une annonce, sans match préalable (`listing_id` renseigné)
- Marquage `is_read = 1` quand le destinataire charge la conversation

### 8.3 Notifications (polling)

```javascript
setInterval(() => {
    fetch('/api/notifications/get.php')
        .then(res => res.json())
        .then(data => updateNotificationBadge(data.unread_count))
}, 30000) // toutes les 30 secondes
```

| Événement | Notification générée pour |
|-----------|--------------------------|
| Envoi d'une demande | Le receiver |
| Acceptation | Le sender |
| Refus | Le sender |
| Nouveau message | Le receiver |

---

## PARTIE 9 — PLAN DE DÉVELOPPEMENT

### Répartition des tâches (groupe de 4)

| Étudiant | Module | Entités couvertes |
|----------|--------|------------------|
| Étudiant A | Authentification + Profils + Admin | `users`, `student_profiles` |
| Étudiant B | Listings + Upload images | `listings`, `listing_images` |
| Étudiant C | Matching + Notifications | `roommate_requests`, `matches`, `notifications` |
| Étudiant D | Messagerie + Dashboard intégration | `messages` |

### Livrables requis

- [ ] Diagramme de classes UML (fichier : `uml/class_diagram.puml`)
- [ ] Application web fonctionnelle (minimum 4 entités actives)
- [ ] Git avec historique de commits (chaque membre identifiable)
- [ ] Démonstration par module lors de la soutenance

### Workflow Git recommandé

```bash
# Créer une branche par fonctionnalité
git checkout -b feature/listings

# Committer régulièrement
git add api/listings/create.php
git commit -m "feat(listings): add create endpoint with image upload"

# Fusionner dans main
git checkout main
git merge feature/listings
git push origin main
```

---

*Binomy — Cahier des Charges v1.0 | TEK-UP University | Mini-Projet PHP/JavaScript*
