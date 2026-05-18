-- ============================================================
-- BINOMY — Script SQL complet
-- TEK-UP University | Mini-Projet PHP/JavaScript
-- ============================================================
-- Instructions :
--   1. Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
--   2. Onglet "SQL" → coller ce fichier → Exécuter
--   OU via terminal : mysql -u root -p < sql/binomy.sql
-- ============================================================

-- Supprimer et recréer la base proprement
DROP DATABASE IF EXISTS binomy;

CREATE DATABASE binomy
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE binomy;

-- ============================================================
-- TABLE : users
-- Tous les utilisateurs (students, locataires, admins)
-- ============================================================
CREATE TABLE users (
    id          INT           PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('student','locataire','admin') NOT NULL,
    avatar      VARCHAR(255)  NULL,
    phone       VARCHAR(20)   NULL,
    city        VARCHAR(100)  NULL,
    bio         TEXT          NULL,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  DATETIME      NOT NULL DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : student_profiles
-- Extension du profil pour les étudiants uniquement
-- ============================================================
CREATE TABLE student_profiles (
    id              INT        PRIMARY KEY AUTO_INCREMENT,
    user_id         INT        NOT NULL UNIQUE,
    budget_min      INT        NULL,
    budget_max      INT        NULL,
    cleanliness     TINYINT    NULL COMMENT '1=minimal, 5=très propre',
    smoking         TINYINT(1) NOT NULL DEFAULT 0,
    pets_ok         TINYINT(1) NOT NULL DEFAULT 0,
    schedule        ENUM('early_bird','night_owl','flexible') NULL,
    personal_rules  TEXT       NULL,
    looking_for     ENUM('roommate','housing','both') NOT NULL DEFAULT 'both',
    available_from  DATE       NULL,
    CONSTRAINT fk_sp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : listings
-- Annonces de logements publiées par les locataires
-- ============================================================
CREATE TABLE listings (
    id          INT              PRIMARY KEY AUTO_INCREMENT,
    owner_id    INT              NOT NULL,
    title       VARCHAR(200)     NOT NULL,
    description TEXT             NOT NULL,
    type        ENUM('chambre','studio','appartement','maison') NOT NULL,
    price       DECIMAL(8,2)     NOT NULL,
    city        VARCHAR(100)     NOT NULL,
    address     VARCHAR(255)     NULL,
    rooms       TINYINT          NULL,
    surface     INT              NULL,
    status      ENUM('available','rented') NOT NULL DEFAULT 'available',
    created_at  DATETIME         NOT NULL DEFAULT NOW(),
    updated_at  DATETIME         NULL,
    CONSTRAINT fk_listing_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : listing_images
-- Photos associées aux annonces
-- ============================================================
CREATE TABLE listing_images (
    id          INT           PRIMARY KEY AUTO_INCREMENT,
    listing_id  INT           NOT NULL,
    image_path  VARCHAR(255)  NOT NULL,
    is_main     TINYINT(1)    NOT NULL DEFAULT 0,
    uploaded_at DATETIME      NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_img_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : roommate_requests
-- Demandes de colocation entre étudiants
-- ============================================================
CREATE TABLE roommate_requests (
    id            INT      PRIMARY KEY AUTO_INCREMENT,
    sender_id     INT      NOT NULL,
    receiver_id   INT      NOT NULL,
    message       TEXT     NULL,
    status        ENUM('pending','accepted','refused','cancelled') NOT NULL DEFAULT 'pending',
    created_at    DATETIME NOT NULL DEFAULT NOW(),
    responded_at  DATETIME NULL,
    CONSTRAINT fk_req_sender   FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_req_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT uq_request      UNIQUE (sender_id, receiver_id),
    CONSTRAINT chk_no_self     CHECK (sender_id != receiver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : matches
-- Créé automatiquement quand une demande est acceptée
-- ============================================================
CREATE TABLE matches (
    id          INT      PRIMARY KEY AUTO_INCREMENT,
    request_id  INT      NOT NULL UNIQUE,
    user1_id    INT      NOT NULL,
    user2_id    INT      NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_match_request FOREIGN KEY (request_id) REFERENCES roommate_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_match_user1   FOREIGN KEY (user1_id)   REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_match_user2   FOREIGN KEY (user2_id)   REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : messages
-- Messagerie (étudiant/étudiant via match, étudiant/locataire via listing)
-- ============================================================
CREATE TABLE messages (
    id          INT        PRIMARY KEY AUTO_INCREMENT,
    sender_id   INT        NOT NULL,
    receiver_id INT        NOT NULL,
    match_id    INT        NULL,
    listing_id  INT        NULL,
    content     TEXT       NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    sent_at     DATETIME   NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_msg_sender   FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_match    FOREIGN KEY (match_id)    REFERENCES matches(id) ON DELETE SET NULL,
    CONSTRAINT fk_msg_listing  FOREIGN KEY (listing_id)  REFERENCES listings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : notifications
-- Alertes générées côté serveur pour chaque événement clé
-- ============================================================
CREATE TABLE notifications (
    id            INT           PRIMARY KEY AUTO_INCREMENT,
    user_id       INT           NOT NULL,
    type          ENUM('new_request','request_accepted','request_refused','new_message','listing_update') NOT NULL,
    reference_id  INT           NULL,
    content       VARCHAR(255)  NOT NULL,
    is_read       TINYINT(1)    NOT NULL DEFAULT 0,
    created_at    DATETIME      NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Compte administrateur par défaut
-- Email : admin@binomy.tn | Mot de passe : admin123
INSERT INTO users (name, email, password, role, city, bio) VALUES
(
    'Admin Binomy',
    'admin@binomy.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'Tunis',
    'Administrateur de la plateforme Binomy'
);

-- Comptes étudiants de démonstration
-- Mot de passe pour tous : password
INSERT INTO users (name, email, password, role, city, bio, phone) VALUES
(
    'Ahmed Trabelsi',
    'ahmed@enit.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Tunis',
    'Étudiant en informatique à l\'ENIT. Calme, sérieux, cherche colocataire sympa.',
    '+216 55 123 456'
),
(
    'Mariam Chaouachi',
    'mariam@fsb.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Sfax',
    'En master gestion à Sfax. Sociable, aime la cuisine.',
    '+216 22 456 789'
),
(
    'Youssef Mejri',
    'youssef@iset.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Sousse',
    'Ingénieur génie civil. Cherche quelqu\'un de tolérant.',
    '+216 98 789 012'
),
(
    'Sarra Hamdi',
    'sarra@fmst.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Tunis',
    'Étudiante en médecine. Très organisée, cherche colocataire sérieuse.',
    '+216 55 321 654'
),
(
    'Amine Bouazizi',
    'amine@fseg.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Monastir',
    'Licence économie. Sportif, sort souvent le week-end.',
    '+216 20 654 321'
),
(
    'Rania Kasmi',
    'rania@fds.tn',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'student',
    'Bizerte',
    'Master droit. Studieuse, cherche ambiance calme.',
    '+216 25 987 654'
);

-- Comptes propriétaires de démonstration
INSERT INTO users (name, email, password, role, city, phone) VALUES
(
    'Kamel Gharbi',
    'kamel@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'locataire',
    'Tunis',
    '+216 71 234 567'
),
(
    'Hassen Jelassi',
    'hassen@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'locataire',
    'Sfax',
    '+216 74 345 678'
),
(
    'Sonia Maaloul',
    'sonia@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'locataire',
    'Sousse',
    '+216 73 456 789'
);

-- Profils étudiants
INSERT INTO student_profiles (user_id, budget_min, budget_max, cleanliness, smoking, pets_ok, schedule, looking_for, available_from) VALUES
(2, 300, 500, 4, 0, 0, 'early_bird',  'both',     '2025-09-01'),
(3, 200, 400, 3, 0, 1, 'flexible',    'both',     '2025-09-01'),
(4, 250, 450, 2, 1, 0, 'night_owl',   'roommate', '2025-10-01'),
(5, 350, 600, 5, 0, 0, 'early_bird',  'both',     '2025-09-01'),
(6, 200, 350, 3, 0, 1, 'flexible',    'housing',  '2025-09-15'),
(7, 300, 500, 4, 0, 0, 'night_owl',   'roommate', '2025-10-01');

-- Annonces de logements
INSERT INTO listings (owner_id, title, description, type, price, city, address, rooms, surface, status) VALUES
(
    8,
    'Studio meublé près ENIT',
    'Beau studio entièrement meublé, situé à 5 minutes à pied de l\'ENIT. Cuisine équipée, salle de bain privée, WiFi inclus. Idéal pour étudiant sérieux.',
    'studio', 450.00, 'Tunis', 'Rue de l\'ENIT, El Manar', 1, 28, 'available'
),
(
    9,
    'Chambre dans appartement 3 pièces',
    'Chambre meublée dans un appartement partagé avec 2 autres étudiants. Ambiance calme et studieuse. Proche de la faculté des sciences de Sfax.',
    'chambre', 250.00, 'Sfax', 'Avenue Habib Bourguiba, Sfax', 1, 15, 'rented'
),
(
    10,
    'Appartement F2 proche faculté',
    'Appartement 2 pièces lumineux, entièrement rénové. Salon, chambre, cuisine équipée, salle de bain. Parking disponible. Proche de l\'université de Sousse.',
    'appartement', 600.00, 'Sousse', 'Rue Ibn Khaldoun, Sousse', 2, 55, 'available'
),
(
    8,
    'Studio tout équipé centre-ville',
    'Studio moderne au cœur de Monastir. Tout équipé : lit, bureau, armoire, cuisine, WiFi. Accès facile aux transports en commun.',
    'studio', 380.00, 'Monastir', 'Avenue de la République, Monastir', 1, 32, 'available'
),
(
    9,
    'Maison 4 pièces à louer',
    'Grande maison familiale disponible pour colocation étudiante. 4 chambres, grand salon, jardin. Idéale pour groupe de 3-4 étudiants.',
    'maison', 900.00, 'Sfax', 'Cité El Habib, Sfax', 4, 120, 'available'
);
