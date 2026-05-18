-- ============================================================
-- BINOMY — Schéma de base de données
-- TEK-UP University | Mini-Projet PHP/JavaScript
-- ============================================================

CREATE DATABASE IF NOT EXISTS binomy
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE binomy;

-- ------------------------------------------------------------
-- TABLE : users
-- Tous les utilisateurs (students, locataires, admins)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : student_profiles
-- Extension du profil pour les étudiants uniquement
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS student_profiles (
    id              INT       PRIMARY KEY AUTO_INCREMENT,
    user_id         INT       NOT NULL UNIQUE,
    budget_min      INT       NULL,
    budget_max      INT       NULL,
    cleanliness     TINYINT   NULL COMMENT '1=minimal, 5=très propre',
    smoking         TINYINT(1) NOT NULL DEFAULT 0,
    pets_ok         TINYINT(1) NOT NULL DEFAULT 0,
    schedule        ENUM('early_bird','night_owl','flexible') NULL,
    personal_rules  TEXT      NULL,
    looking_for     ENUM('roommate','housing','both') NOT NULL DEFAULT 'both',
    available_from  DATE      NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : listings
-- Annonces de logements publiées par les locataires
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS listings (
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
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : listing_images
-- Photos associées aux annonces
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS listing_images (
    id          INT           PRIMARY KEY AUTO_INCREMENT,
    listing_id  INT           NOT NULL,
    image_path  VARCHAR(255)  NOT NULL,
    is_main     TINYINT(1)    NOT NULL DEFAULT 0,
    uploaded_at DATETIME      NOT NULL DEFAULT NOW(),
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : roommate_requests
-- Demandes de colocation entre étudiants
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roommate_requests (
    id            INT    PRIMARY KEY AUTO_INCREMENT,
    sender_id     INT    NOT NULL,
    receiver_id   INT    NOT NULL,
    message       TEXT   NULL,
    status        ENUM('pending','accepted','refused','cancelled') NOT NULL DEFAULT 'pending',
    created_at    DATETIME NOT NULL DEFAULT NOW(),
    responded_at  DATETIME NULL,
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_request (sender_id, receiver_id),
    CHECK (sender_id != receiver_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : matches
-- Créé automatiquement quand une demande est acceptée
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS matches (
    id          INT      PRIMARY KEY AUTO_INCREMENT,
    request_id  INT      NOT NULL UNIQUE,
    user1_id    INT      NOT NULL,
    user2_id    INT      NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (request_id) REFERENCES roommate_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user1_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user2_id)   REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : messages
-- Messagerie (étudiant/étudiant via match, étudiant/locataire via listing)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id          INT      PRIMARY KEY AUTO_INCREMENT,
    sender_id   INT      NOT NULL,
    receiver_id INT      NOT NULL,
    match_id    INT      NULL,
    listing_id  INT      NULL,
    content     TEXT     NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    sent_at     DATETIME NOT NULL DEFAULT NOW(),
    FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id)    REFERENCES matches(id) ON DELETE SET NULL,
    FOREIGN KEY (listing_id)  REFERENCES listings(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLE : notifications
-- Alertes générées côté serveur pour chaque événement clé
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id            INT           PRIMARY KEY AUTO_INCREMENT,
    user_id       INT           NOT NULL,
    type          ENUM('new_request','request_accepted','request_refused','new_message','listing_update') NOT NULL,
    reference_id  INT           NULL,
    content       VARCHAR(255)  NOT NULL,
    is_read       TINYINT(1)    NOT NULL DEFAULT 0,
    created_at    DATETIME      NOT NULL DEFAULT NOW(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
