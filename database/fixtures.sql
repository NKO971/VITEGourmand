-- =====================================================================
-- VITEGourmand - DONNEES (a importer APRES schema.sql)
-- Source : vitegourmand (2).sql du 16/09/2026 (copie a l'identique).
-- Contenu : references + seul compte admin en base.
-- Ordre : references -> utilisateur admin.
-- Menus / plats / commandes / suivi / avis : vides dans vitegourmand (2).sql.
-- Encodage : utf8mb4.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 1. Rôles (référencés par utilisateur.role_id)
-- 1 = admin, 2 = employe, 3 = utilisateur (ids fixés car le code
-- contrôle les accès avec requireRole([1, 2]) et role_id = 3)
-- ---------------------------------------------------------------------
INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'admin'),
(2, 'employe'),
(3, 'utilisateur');

-- ---------------------------------------------------------------------
-- 2. Thèmes et régimes (référencés par menu.theme_id / menu.regime_id)
-- ---------------------------------------------------------------------
INSERT INTO `theme` (`theme_id`, `libelle`) VALUES
(1, 'Tradition & Terroir'),
(2, 'Retour de Pêche'),
(3, 'Saveurs Exotiques');

INSERT INTO `regime` (`regime_id`, `libelle`) VALUES
(1, 'Omnivore'),
(2, 'Végétarien'),
(3, 'Sans Gluten');

-- ---------------------------------------------------------------------
-- 3. Horaires d'ouverture (affiches dans le footer public)
-- Donnees reelles du dump : Vendredi/Samedi 23:00, Dimanche 14:00-17:00
-- ---------------------------------------------------------------------
INSERT INTO `horaire` (`horaire_id`, `jour`, `heure_ouverture`, `heure_fermeture`) VALUES
(1, 'Lundi', 'Fermé', 'Fermé'),
(2, 'Mardi', '10:00', '22:00'),
(3, 'Mercredi', '10:00', '22:00'),
(4, 'Jeudi', '10:00', '22:00'),
(5, 'Vendredi', '10:00', '23:00'),
(6, 'Samedi', '10:00', '23:00'),
(7, 'Dimanche', '14:00', '17:00');

-- ---------------------------------------------------------------------
-- 4. Zones de livraison (tarification selon distance_km)
-- ---------------------------------------------------------------------
INSERT INTO `zone_livraison` (`zone_id`, `code_postal`, `ville`, `distance_km`) VALUES
(1, '33000', 'Bordeaux Centre', 0),
(2, '33200', 'Bordeaux Caudéran', 3),
(3, '33600', 'Pessac', 8),
(4, '33400', 'Talence', 6),
(5, '33130', 'Bègles', 7),
(6, '33700', 'Mérignac', 12);

-- ---------------------------------------------------------------------
-- 5. Utilisateur (copie de vitegourmand (2).sql : seul compte en base)
-- id 4 = admin (hash bcrypt d'origine conserve).
-- ---------------------------------------------------------------------
INSERT INTO `utilisateur` (`utilisateur_id`, `email`, `gsm`, `adresse`, `password`, `prenom`, `nom`, `role_id`, `is_active`) VALUES
(4, 'admin@vitegourmand.fr', '0600000000', 'Siège social', '$2y$10$.5C5UwyiAy.p5TnOICWlKOnY.sSKu6p3bSfkRw6uanUpQT3/PAeVq', 'VITEGourmand', 'Admin', 1, 1);

-- ---------------------------------------------------------------------
-- Fin des donnees (copie de vitegourmand (2).sql : references + admin).
-- ---------------------------------------------------------------------
