INSERT INTO users (nom, email, password, genre, role, is_gold) VALUES
('Admin S4', 'admin@fit.mg', 'admin123', 'M', 'admin', FALSE),
('Edinah', 'edinah@gmail.com', 'user123', 'F', 'user', TRUE),
('Willy', 'willy@gmail.com', 'user123', 'M', 'user', FALSE),
('Rakoto', 'rakoto@yahoo.fr', 'user123', 'M', 'user', FALSE),
('Rasoa', 'rasoa@gmail.com', 'user123', 'F', 'user', FALSE);

-- Profils de santé initiaux pour les utilisateurs
INSERT INTO user_details (user_id, taille, poids_actuel) VALUES
(2, 1.65, 65.0), -- Edinah
(3, 1.80, 85.0), -- Willy
(4, 1.70, 55.0), -- Rakoto
(5, 1.60, 75.0); -- Rasoa

-- 2. Insertion des Aliments (pour la flexibilité)
INSERT INTO aliments (nom_aliment, type_aliment) VALUES
('Blanc de poulet', 'volaille'),
('Steak de boeuf', 'viande'),
('Filet de Colin', 'poisson'),
('Salade verte', 'legume'),
('Riz blanc', 'autre');

-- 3. Insertion des 5 Régimes
INSERT INTO regimes (nom_regime, poids_impact, duree_jours, prix_journalier, description) VALUES
('Régime Kéto', -3.5, 7, 15000, 'Régime riche en graisses et très pauvre en glucides.'),
('Prise de masse XL', 2.0, 14, 20000, 'Surplus calorique pour augmenter la masse musculaire.'),
('Équilibre Océan', -1.5, 10, 18000, 'Basé sur la consommation de poisson et de légumes.'),
('Végétarien Soft', -1.0, 7, 12000, 'Perte de poids légère sans viande ni poisson.'),
('Sèche Intense', -5.0, 21, 25000, 'Réduction calorique forte pour une perte rapide.');

-- Composition des régimes (Liaison regime_aliments)
INSERT INTO regime_aliments (id_regime, id_aliment, pourcentage) VALUES
(1, 1, 50.0), (1, 2, 50.0), -- Kéto: 50% Volaille, 50% Viande
(3, 3, 70.0), (3, 4, 30.0), -- Océan: 70% Poisson, 30% Légume
(5, 1, 30.0), (5, 3, 40.0), (5, 4, 30.0); -- Sèche: 30% Volaille, 40% Poisson, 30% Légume

-- 4. Insertion des 5 Activités Sportives
INSERT INTO activites_sportives (nom_activite, poids_impact, duree_jours) VALUES
('Running Matinal', -0.5, 7),
('Natation', -0.8, 10),
('Musculation', 0.2, 14),
('Yoga', -0.2, 7),
('Cyclisme', -0.6, 10);

-- 5. Insertion des 15 Codes de recharge
-- Format : 10 chiffres/lettres mélangés
INSERT INTO codes_recharge (code, valeur, est_utilise, est_valide) VALUES
('RECH-001-XYZ', 10000, FALSE, TRUE),
('RECH-002-XYZ', 10000, FALSE, TRUE),
('RECH-003-XYZ', 20000, FALSE, TRUE),
('RECH-004-XYZ', 20000, TRUE, TRUE), -- Déjà utilisé
('RECH-005-XYZ', 50000, FALSE, TRUE),
('RECH-006-XYZ', 50000, FALSE, FALSE), -- Pas encore validé par l'admin
('RECH-007-XYZ', 100000, FALSE, TRUE),
('RECH-008-ABC', 15000, FALSE, TRUE),
('RECH-009-ABC', 25000, FALSE, TRUE),
('RECH-010-ABC', 5000, FALSE, TRUE),
('RECH-011-JKL', 10000, FALSE, FALSE),
('RECH-012-JKL', 20000, FALSE, TRUE),
('RECH-013-JKL', 30000, FALSE, TRUE),
('RECH-014-JKL', 40000, FALSE, TRUE),
('RECH-015-JKL', 50000, FALSE, TRUE);