-- =====================================================================
-- VITEGourmand - Création de la base de données
-- Fichier 1/2 : STRUCTURE (création des tables)
-- Ordre de création : tables sans dépendances d'abord,
-- puis tables avec clés étrangères.
-- Moteur InnoDB obligatoire (clés étrangères), encodage utf8mb4.
-- Base cible : MariaDB 10.4 / MySQL 5.7+ (compatible Heroku JawsDB)
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. Tables de référence (aucune dépendance)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `role` (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `theme` (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `regime` (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `horaire` (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(50) NOT NULL,
    heure_ouverture VARCHAR(50) NOT NULL,
    heure_fermeture VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `zone_livraison` (
    zone_id INT AUTO_INCREMENT PRIMARY KEY,
    code_postal VARCHAR(5) NOT NULL,
    ville VARCHAR(50) NOT NULL,
    distance_km INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `allergene` (
    allergene_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Seed idempotent des 14 allergenes majeurs (reglement UE 1169/2011).
-- INSERT IGNORE : sans danger a rejouer sur une base existante.
INSERT IGNORE INTO `allergene` (`allergene_id`, `libelle`) VALUES
(1, 'Gluten'),
(2, 'Crustacés'),
(3, 'Œufs'),
(4, 'Poissons'),
(5, 'Arachides'),
(6, 'Soja'),
(7, 'Lait'),
(8, 'Fruits à coque'),
(9, 'Céleri'),
(10, 'Moutarde'),
(11, 'Sésame'),
(12, 'Sulfites'),
(13, 'Lupin'),
(14, 'Mollusques');

-- ---------------------------------------------------------------------
-- 2. Utilisateurs (dépend de role)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `utilisateur` (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    gsm VARCHAR(20) DEFAULT NULL,
    adresse TEXT DEFAULT NULL,
    password CHAR(255) NOT NULL,
    prenom VARCHAR(50) DEFAULT NULL,
    nom VARCHAR(50) DEFAULT NULL,
    role_id INT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 0,
    FOREIGN KEY (role_id) REFERENCES role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ---------------------------------------------------------------------
-- 3. Carte : plats et menus (menus dépendent de theme et regime)
-- La composition du menu est stockée en JSON (hybride SQL / NoSQL) :
-- {"entree": {"plat_id": 1, "nom": "..."}, "plat": {...}, "dessert": {...}}
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `plat` (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    titre_plat VARCHAR(100) NOT NULL,
    photo LONGBLOB DEFAULT NULL,
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Association many-to-many plat <-> allergene (un plat peut avoir
-- plusieurs allergenes, un allergene concerne plusieurs plats).
CREATE TABLE IF NOT EXISTS `plat_allergene` (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plat (plat_id) ON DELETE CASCADE,
    FOREIGN KEY (allergene_id) REFERENCES allergene (allergene_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `menu` (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    prix_par_personne DOUBLE NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    nombre_personne_minimum INT NOT NULL,
    quantite_restante INT DEFAULT 0,
    theme_id INT DEFAULT NULL,
    regime_id INT DEFAULT NULL,
    composition LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`composition`)),
    conditions_stockage LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions_stockage`)),
    image VARCHAR(255) DEFAULT NULL,
    actif TINYINT(1) DEFAULT 1,
    FOREIGN KEY (theme_id) REFERENCES theme (theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime (regime_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- 4. Galerie d'images par menu (URLs externes, pas d'upload fichier :
-- filesystem éphémère sur Heroku). menu.image reste la vignette.
-- Suppression en cascade : effacer un menu efface sa galerie.
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `menu_images` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    ordre INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES menu (menu_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Patch idempotent : ajoute la colonne ordre si menu_images existe deja
-- sans elle (utilisee par Menu::saveGallery/getImagesByMenuId/getAllImagesByMenuIds).
-- Sans danger a rejouer sur une base existante (IF NOT EXISTS), y compris en prod.
ALTER TABLE `menu_images` ADD COLUMN IF NOT EXISTS `ordre` INT DEFAULT 0 AFTER `image_url`;

-- ---------------------------------------------------------------------
-- 5. Commandes et suivi (dépendent de utilisateur et menu)
-- ---------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `commande` (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL,
    date_commande DATE NOT NULL,
    date_prestation DATE NOT NULL,
    heure_livraison VARCHAR(50) DEFAULT NULL,
    adresse_livraison VARCHAR(255) DEFAULT NULL,
    code_postal_livraison VARCHAR(5) DEFAULT NULL,
    prix_menu DOUBLE NOT NULL,
    nombre_personne INT NOT NULL,
    prix_livraison DOUBLE DEFAULT NULL,
    statut VARCHAR(50) DEFAULT 'En attente',
    pret_materiel TINYINT(1) DEFAULT 0,
    restitution_materiel TINYINT(1) DEFAULT 0,
    mode_contact VARCHAR(20) DEFAULT NULL,
    motif_annulation TEXT DEFAULT NULL,
    utilisateur_id INT DEFAULT NULL,
    menu_id INT DEFAULT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `suivi_commande` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_suivi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commande (commande_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- 6. Tables annexes
-- ---------------------------------------------------------------------

-- Avis SQL historique (les avis applicatifs sont stockés en MongoDB,
-- collection "avis" : voir app/models/AvisModel.php)
CREATE TABLE IF NOT EXISTS `avis` (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    statut VARCHAR(50) DEFAULT 'En attente',
    utilisateur_id INT DEFAULT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (utilisateur_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
