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