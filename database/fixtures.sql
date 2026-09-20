-- =====================================================================
-- VITEGourmand - DONNEES (a importer APRES schema.sql)
-- Contenu : references + seul compte admin en base.
-- Ordre : references -> utilisateur admin.
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
-- 2. Utilisateurs (référencés par utilisateur.utilisateur_id)
-- ---------------------------------------------------------------------
INSERT INTO `utilisateur` (`utilisateur_id`, `email`, `gsm`, `adresse`, `password`, `prenom`, `nom`, `role_id`, `is_active`) VALUES
	(1, 'admin@vitegourmand.fr', '0600000000', 'Siège social', '$2y$10$.5C5UwyiAy.p5TnOICWlKOnY.sSKu6p3bSfkRw6uanUpQT3/PAeVq', 'VITEGourmand', 'Admin', 1, 1),
	(2, 'julie@vitegourmand.fr', NULL, NULL, '$2y$12$UyNnUUEpJc3p4OgkXgx/fOEZJUdEYdGrUJ8iFDCOkN/5ts30Tbz6W', NULL, NULL, 2, 1),
	(3, 'ruben@hotmail.fr', '0635182022', '10 Rue Louis Blériot 33130 Bègles', '$2y$12$vrZX86QUUnjVD2wqJ1QLd.eDknoAPSfBM5FXJxcMp2/EfGMeYN1t6', 'Ruben', 'Elmudésie', 3, 0);

-- ---------------------------------------------------------------------
-- 3. Thèmes et régimes (référencés par menu.theme_id / menu.regime_id)
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
-- 4. Allergènes (référencés par plat.allergenes JSON, plat.allergenes = [allergene_id, ...])
-- ---------------------------------------------------------------------
INSERT INTO `allergene` (`allergene_id`, `libelle`) VALUES
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
-- 5. Plats (référencés par menu.plats JSON, menu.plats = [plat_id, ...])
-- ---------------------------------------------------------------------
INSERT INTO `plat` (`plat_id`, `titre_plat`, `photo`, `actif`) VALUES
	(1, 'Velouté de potimarron aux noisettes, base bouillon de légumes', NULL, 1),
	(2, 'Salade de chèvre chaud, noix et miel', NULL, 1),
	(3, 'Asperges vertes, sauce mousseline', NULL, 1),
	(4, 'Bœuf bourguignon, pommes vapeur', NULL, 1),
	(5, 'Chapon rôti aux marrons', NULL, 1),
	(6, 'Gigot d\'agneau aux herbes de Provence', NULL, 1),
	(7, 'Risotto aux champignons et parmesan', NULL, 1),
	(8, 'Curry de légumes et tofu', NULL, 1),
	(9, 'Tarte Tatin, crème fraîche', NULL, 1),
	(10, 'Bûche de Noël chocolat-marron', NULL, 1),
	(11, 'Nid de Pâques, mousse chocolat', NULL, 1),
	(12, 'Salade d\'agrumes et sorbet vegan', NULL, 1);

-- ---------------------------------------------------------------------
-- 6.Plat Allergènes (association many-to-many plat <-> allergene)
-- Un plat peut avoir plusieurs allergènes, un allergène concerne plusieurs plats.
-- ---------------------------------------------------------------------
INSERT INTO `plat_allergene` (`plat_id`, `allergene_id`) VALUES
	(1, 8),
	(2, 7),
	(2, 8),
	(3, 3),
	(4, 12),
	(7, 7),
	(8, 6),
	(9, 1),
	(9, 7),
	(10, 1),
	(10, 3),
	(10, 7),
	(11, 3),
	(11, 7);

-- ---------------------------------------------------------------------
-- 7. Menus (référencés par menu.menu_id, menu.plats JSON, menu.theme_id / menu.regime_id)
-- ---------------------------------------------------------------------
INSERT INTO `menu` (`menu_id`, `titre`, `prix_par_personne`, `description`, `nombre_personne_minimum`, `quantite_restante`, `theme_id`, `regime_id`, `composition`, `conditions_stockage`, `delai_commande_valeur`, `delai_commande_unite`, `image`, `actif`) VALUES
	(1, 'Menu Classique Bistrot', 96, 'Une formule intemporelle pour un repas convivial entre amis ou en famille.', 4, 39, 1, 1, '{"entree":{"plat_id":2,"nom":"Salade de chèvre chaud, noix et miel"},"plat":{"plat_id":4,"nom":"Bœuf bourguignon, pommes vapeur"},"dessert":{"plat_id":9,"nom":"Tarte Tatin, crème fraîche"}}', '{"conservation":"À conserver au frais."}', 3, 'jours', NULL, 1),
	(2, 'Menu de Noël', 35, 'Une formule festive pour sublimer votre réveillon', 6, 25, 1, 1, '{"entree":{"plat_id":1,"nom":"Velouté de potimarron aux noisettes, base bouillon de légumes"},"plat":{"plat_id":5,"nom":"Chapon rôti aux marrons"},"dessert":{"plat_id":10,"nom":"Bûche de Noël chocolat-marron"}}', '{"conservation":"À conserver au frais, réchauffer avant le service"}', 14, 'jours', NULL, 0),
	(3, 'Menu Végétarien Évènement', 30, 'Une formule végétarienne raffinée pour vos évènements professionnels ou privés', 5, 29, 1, 2, '{"entree":{"plat_id":2,"nom":"Salade de chèvre chaud, noix et miel"},"plat":{"plat_id":7,"nom":"Risotto aux champignons et parmesan"},"dessert":{"plat_id":9,"nom":"Tarte Tatin, crème fraîche"}}', '{"conservation":"À conserver au frais, réchauffer avant le service"}', 5, 'jours', NULL, 1),
	(4, 'Vegan Signature', 30, 'Une formule 100% végan et sans gluten pour un évènement engagé et gourmand', 6, 47, 3, 3, '{"entree":{"plat_id":1,"nom":"Velouté de potimarron aux noisettes, base bouillon de légumes"},"plat":{"plat_id":8,"nom":"Curry de légumes et tofu"},"dessert":{"plat_id":12,"nom":"Salade d''agrumes et sorbet vegan"}}', '{"conservation":"À conserver au frais, réchauffer avant le service"}', 5, 'jours', NULL, 1);

-- ---------------------------------------------------------------------
-- 8. Images de menus (référencés par menu_images.menu_id)
-- ---------------------------------------------------------------------
INSERT INTO `menu_images` (`id`, `menu_id`, `image_url`, `ordre`, `created_at`) VALUES
	(26, 4, 'https://media.istockphoto.com/id/2228011085/fr/photo/femme-hachant-des-carottes-sur-une-table-pleine-de-fruits-et-l%C3%A9gumes-dautomne-sains-fond-de.jpg?s=2048x2048&w=is&k=20&c=Y7iB4aD_RRS8_3rruCM6S0jtw6SFX113Okr1a9VB1Ag=', 0, '2026-09-17 23:18:35'),
	(27, 4, 'https://media.istockphoto.com/id/1201192282/fr/photo/soupe-ch%C3%A2taigne-potimarron.jpg?s=2048x2048&w=is&k=20&c=j_2IoRTzLXne45tVI5biMMeERq3Qx8-1VnQ8PnCIb0E=', 1, '2026-09-17 23:18:35'),
	(28, 4, 'https://media.istockphoto.com/id/2202947767/fr/photo/nouilles-de-l%C3%A9gumes-au-curry-vert-avec-tofu-po%C3%AAl%C3%A9-cuisine-asiatique-saine.jpg?s=2048x2048&w=is&k=20&c=Tlv3tX6eTiVdtwFwW76pvO5tyi3me7JgQQzf7zfAEgg=', 2, '2026-09-17 23:18:35'),
	(29, 4, 'https://media.istockphoto.com/id/2261470088/fr/photo/gros-plan-de-salade-dagrumes-avec-pistaches-et-cr%C3%A8me-garnie.jpg?s=2048x2048&w=is&k=20&c=vxzYcQHWVpc3t3d5DlmEJr62MjiQALnBa6ojKmKRDls=', 3, '2026-09-17 23:18:35'),
	(30, 3, 'https://media.istockphoto.com/id/1475876736/fr/photo/des-amis-heureux-discutent-dans-un-bar.jpg?s=2048x2048&w=is&k=20&c=jRCtlvuGGRwgg2KBVcSLtThwu46uasF8x9IuvwnpgrU=', 0, '2026-09-17 23:18:47'),
	(31, 3, 'https://media.istockphoto.com/id/1136002862/fr/photo/riz-blanc-cuit-avec-mushroomsin-un-bol-sur-fond-en-bois.jpg?s=2048x2048&w=is&k=20&c=vsBArhn6GysdZn1XT3uvoo12--QWTasm51qig5PoKGE=', 1, '2026-09-17 23:18:47'),
	(32, 3, 'https://media.istockphoto.com/id/182836461/fr/photo/tarte-tatin.jpg?s=2048x2048&w=is&k=20&c=9EYwfHy2usKzbSA5b4VIAxyjHoMP6C2XsXqsbuAxdEk=', 2, '2026-09-17 23:18:47'),
	(33, 2, 'https://images.unsplash.com/photo-1601118964938-228a89955311?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 0, '2026-09-17 23:18:53'),
	(34, 2, 'https://media.istockphoto.com/id/157524477/fr/photo/d%C3%AEner-poulet-r%C3%B4ti.jpg?s=2048x2048&w=is&k=20&c=B6enwV27AgVjj6iQGpGgGQgIdcKKCLV6zvNrPJR2GZU=', 1, '2026-09-17 23:18:53'),
	(35, 2, 'https://media.istockphoto.com/id/1179815029/fr/photo/g%C3%A2teau-de-notation-de-yule-de-noel-dessert-traditionnel-au-chocolat.jpg?s=2048x2048&w=is&k=20&c=2az1rd8bM-p1jW_gxWwaLLU8JGLJYYRTeY69MnccL34=', 2, '2026-09-17 23:18:53'),
	(36, 1, 'https://media.istockphoto.com/id/1475879207/fr/photo/des-amis-heureux-parlent-et-mangent-au-restaurant.jpg?s=2048x2048&w=is&k=20&c=Zn5X6Dx305ZfR8IHhtQ70tuCTyBPQcrvaG8_QDQOypg=', 0, '2026-09-17 23:19:00'),
	(37, 1, 'https://media.istockphoto.com/id/464857000/fr/photo/b%C5%93uf-cuit-avec-des-pommes.jpg?s=2048x2048&w=is&k=20&c=SAU77T4EsHyYE-JrDB0Y7_3YY5ejL05W4rie1nqansQ=', 1, '2026-09-17 23:19:00'),
	(38, 1, 'https://media.istockphoto.com/id/182836461/fr/photo/tarte-tatin.jpg?s=2048x2048&w=is&k=20&c=9EYwfHy2usKzbSA5b4VIAxyjHoMP6C2XsXqsbuAxdEk=', 2, '2026-09-17 23:19:00');

-- ---------------------------------------------------------------------
-- 9. Horaires d'ouverture (affiches dans le footer public)
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
-- 10. Zones de livraison (tarification selon distance_km)
-- ---------------------------------------------------------------------
INSERT INTO `zone_livraison` (`zone_id`, `code_postal`, `ville`, `distance_km`) VALUES
(1, '33000', 'Bordeaux Centre', 0),
(2, '33200', 'Bordeaux Caudéran', 3),
(3, '33600', 'Pessac', 8),
(4, '33400', 'Talence', 6),
(5, '33130', 'Bègles', 7),
(6, '33700', 'Mérignac', 12);


-- ---------------------------------------------------------------------
-- Fin des donnees 
-- ---------------------------------------------------------------------
-- Seed idempotent des 14 allergenes majeurs (reglement UE 1169/2011).
-- INSERT IGNORE : sans danger a rejouer sur une base existante.
