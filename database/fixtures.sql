-- Remplir les tables de référence
INSERT INTO `role` (`role_id`, `libelle`) VALUES (1, 'Administrateur'), (2, 'Employé'), (3, 'Client');
INSERT INTO `theme` (`theme_id`, `libelle`) VALUES (1, 'Soleil et apéros'), (2, 'Noël'), (3, 'Pâques');
INSERT INTO `regime` (`regime_id`, `libelle`) VALUES (1, 'Classique'), (2, 'Végétarien'), (3, 'Vegan');

-- Test pour le visuel du rendu bootsrap css

-- Insertion des Thèmes (Orange et Bleu / Terroir et Mer)
INSERT INTO `theme` (`theme_id`, `libelle`) VALUES
(1, 'Tradition & Terroir'),
(2, 'Retour de Pêche'),
(3, 'Saveurs Exotiques');

-- Insertion des Régimes
INSERT INTO `regime` (`regime_id`, `libelle`) VALUES
(1, 'Omnivore'),
(2, 'Végétarien'),
(3, 'Sans Gluten');

-- Insertion de quelques Menus de test
INSERT INTO `menu` (`titre`, `prix_par_personne`, `theme_id`, `regime_id`, `nombre_personne_minimum`, `quantite_restante`) VALUES
('Le Gascon', 29.00, 1, 1, 2, 15),
('Le Potager du Chef', 24.50, 1, 2, 2, 10),
('L''Océanique', 34.00, 2, 1, 4, 8),
('Fraîcheur Marine', 31.00, 2, 3, 2, 12),
('Le Voyageur', 27.00, 3, 1, 2, 20);

UPDATE menu 
SET composition = '{"entree": {"nom": "Foie gras de canard maison", "allergenes": ["Sulfites"]}, "plat": {"nom": "Confit de canard et pommes sarladaises", "allergenes": []}, "dessert": {"nom": "Croustade aux pommes et Armagnac", "allergenes": ["Gluten"]}}'
WHERE menu_id = 1;

INSERT INTO role (role_id, libelle) VALUES 
(1, 'admin'),
(2, 'employe'),
(3, 'utilisateur')
ON DUPLICATE KEY UPDATE libelle=VALUES(libelle);

-- Insertion de quelques distances de test
INSERT INTO `zone_livraison` (`code_postal`, `ville`, `distance_km`) VALUES
('33000', 'Bordeaux Centre', 0),
('33200', 'Bordeaux Caudéran', 3),
('33600', 'Pessac', 8),
('33400', 'Talence', 6),
('33130', 'Bègles', 7),
('33700', 'Mérignac', 12);

// Insertion de test 
INSERT INTO suivi_commande (commande_id, statut, date_suivi) VALUES 
(6, 'En attente', '2026-06-19 10:00:00'),
(6, 'Acceptée', '2026-06-19 11:00:00'),
(6, 'En préparation', '2026-06-19 11:30:00'),
(6, 'Terminée', '2026-06-19 12:24:00');

// Ajout une colonne a la table plat
ALTER TABLE plat ADD COLUMN IF NOT EXISTS actif TINYINT(1) DEFAULT 1; 

// Insertion de quelques plats de test
INSERT INTO plat (titre_plat, actif) VALUES 
('Foie gras de canard maison', 1),
('Croustade aux pommes et Armagnac', 1);

// Insertion des horraires 
INSERT INTO `horaire` (`jour`, `heure_ouverture`, `heure_fermeture`) VALUES
('Lundi', 'Fermé', 'Fermé'),
('Mardi', '10:00', '22:00'),
('Mercredi', '10:00', '22:00'),
('Jeudi', '10:00', '22:00'),
('Vendredi', '10:00', '22:00'),
('Samedi', '10:00', '22:00'),
('Dimanche', '10:00', '14:00');