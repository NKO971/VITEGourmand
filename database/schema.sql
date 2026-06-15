-- Tables de base (sans dépendances)
CREATE TABLE IF NOT EXISTS `role` (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `theme` (
    theme_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `regime` (
    regime_id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tables avec dépendances
CREATE TABLE IF NOT EXISTS `utilisateur` (
    utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    gsm VARCHAR(20) NOT NULL,
    adresse TEXT NOT NULL,
    mot_de_passe CHAR(255) NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES role(role_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table hybride SQL / NoSQL (composition en JSON)
CREATE TABLE IF NOT EXISTS `menu` (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    prix_par_personne DOUBLE NOT NULL,
    nombre_personne_minimum INT NOT NULL,
    quantite_restante INT DEFAULT 0,
    theme_id INT,
    regime_id INT,
    composition JSON,
    conditions_stockage JSON,
    FOREIGN KEY (theme_id) REFERENCES theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES regime(regime_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `plat` (
    plat_id INT AUTO_INCREMENT PRIMARY KEY,
    titre_plat VARCHAR(100) NOT NULL,
    photo LONGBLOB
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `avis` (
    avis_id INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL, -- On passe en INT pour pouvoir faire des calculs de moyenne plus tard
    description VARCHAR(255),
    statut VARCHAR(50) DEFAULT 'En attente', -- Pour la modération des avis
    utilisateur_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `commande` (
    commande_id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(50) NOT NULL,
    date_commande DATE NOT NULL,
    date_prestation DATE NOT NULL,
    heure_livraison VARCHAR(50),
    prix_menu DOUBLE NOT NULL,
    nombre_personne INT NOT NULL,
    prix_livraison DOUBLE,
    statut VARCHAR(50) DEFAULT 'En attente',
    pret_materiel BOOLEAN DEFAULT FALSE,
    restitution_materiel BOOLEAN DEFAULT FALSE,
    utilisateur_id INT,
    menu_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `horaire` (
    horaire_id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(50) NOT NULL,
    heure_ouverture VARCHAR(50) NOT NULL,
    heure_fermeture VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS `zone_livraison` (
    zone_id INT AUTO_INCREMENT PRIMARY KEY,
    code_postal VARCHAR(5) NOT NULL,
    ville VARCHAR(50) NOT NULL,
    distance_km INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;