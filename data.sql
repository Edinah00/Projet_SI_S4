-- ============================================================
-- PROJET RÉGIME SPORT - Base de données complète
-- ============================================================
DROP DATABASE IF EXISTS Regime_sport;
CREATE DATABASE IF NOT EXISTS Regime_sport CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Regime_sport;

-- 1. UTILISATEURS ET AUTHENTIFICATION
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    genre CHAR(1) NOT NULL,         -- 'M' ou 'F'
    role VARCHAR(20) DEFAULT 'user', -- 'user' ou 'admin'
    is_gold BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. INFORMATIONS DE SANTÉ
CREATE TABLE user_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    taille FLOAT NOT NULL,          -- en mètres (ex: 1.75)
    poids_actuel FLOAT NOT NULL,    -- en kg
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. BIBLIOTHÈQUE D'ALIMENTS
CREATE TABLE aliments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_aliment VARCHAR(100) NOT NULL,
    type_aliment VARCHAR(50) NOT NULL  -- 'viande','poisson','volaille','legume'
);

-- 4. CATALOGUE DES RÉGIMES
CREATE TABLE regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_regime VARCHAR(100) NOT NULL,
    poids_impact FLOAT NOT NULL,       -- ex: -2.0 ou +1.5
    duree_jours INT NOT NULL,
    prix_journalier FLOAT NOT NULL,
    description TEXT
);

-- 5. COMPOSITION DU RÉGIME
CREATE TABLE regime_aliments (
    id_regime INT,
    id_aliment INT,
    pourcentage FLOAT NOT NULL,
    PRIMARY KEY (id_regime, id_aliment),
    FOREIGN KEY (id_regime) REFERENCES regimes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_aliment) REFERENCES aliments(id)
);

-- 6. CATALOGUE DES ACTIVITÉS SPORTIVES
CREATE TABLE activites_sportives (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_activite VARCHAR(100) NOT NULL,
    poids_impact FLOAT NOT NULL,
    duree_jours INT NOT NULL
);

-- 7. GESTION DU PORTE-MONNAIE ET CODES
CREATE TABLE codes_recharge (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(15) UNIQUE NOT NULL,
    valeur FLOAT NOT NULL,
    est_utilise BOOLEAN DEFAULT FALSE,
    est_valide BOOLEAN DEFAULT FALSE
);

CREATE TABLE portemonnaie (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    solde FLOAT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. PROGRAMMES CHOISIS
CREATE TABLE user_programmes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    id_regime INT,
    id_activite INT,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    poids_objectif_vise FLOAT,
    prix_total_paye FLOAT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (id_regime) REFERENCES regimes(id),
    FOREIGN KEY (id_activite) REFERENCES activites_sportives(id)
);

-- ============================================================
-- DONNÉES DE DÉMO
-- ============================================================

-- Admin (password: admin123)
INSERT INTO users (nom, email, password, genre, role, is_gold) VALUES
('Administrateur', 'admin@regime.mg', '$2y$10$k.LJvi4e/Vf1Io8WUcIjhO7khupYauubu.Iry65m2bedDtajUK1ny', 'M', 'admin', TRUE);

-- Aliments
INSERT INTO aliments (nom_aliment, type_aliment) VALUES
('Bœuf maigre', 'viande'), ('Poulet grillé', 'volaille'), ('Saumon', 'poisson'),
('Thon', 'poisson'), ('Dinde', 'volaille'), ('Agneau', 'viande');

-- Régimes
INSERT INTO regimes (nom_regime, poids_impact, duree_jours, prix_journalier, description) VALUES
('Régime Hyperprotéiné', -3.0, 30, 8500, 'Riche en protéines pour la perte de masse grasse'),
('Régime Méditerranéen', -1.5, 45, 7000, 'Équilibré, basé sur les bonnes graisses et fibres'),

('Programme Masse Musculaire', 2.5, 60, 9500, 'Apport calorique élevé pour la prise de masse'),
('Régime Détox', -2.0, 21, 6500, 'Nettoyage de lorganisme, légumes et poissons'),
('Programme Équilibre', 0.0, 30, 6000, 'Maintien du poids idéal et bien-être général');

-- Composition des régimes
INSERT INTO regime_aliments (id_regime, id_aliment, pourcentage) VALUES
(1,1,35),(1,2,25),(1,3,20),
(2,3,30),
(3,1,40),(3,2,30),(3,5,20),
(4,3,30),(4,4,25),
(5,2,25),(5,6,20);

-- Activités sportives
INSERT INTO activites_sportives (nom_activite, poids_impact, duree_jours) VALUES
('Course à pied', -0.05, 30),
('Natation', -0.06, 30),
('Musculation', 0.08, 45),
('Yoga', -0.02, 30),
('Cyclisme', -0.04, 30),
('HIIT', -0.07, 21),
('Marche rapide', -0.03, 45);

-- Codes de recharge
INSERT INTO codes_recharge (code, valeur, est_valide) VALUES
('GOLD2024SPORT', 50000, TRUE),
('BIENV2024MG', 25000, TRUE),
('PROMO15SPORT', 15000, TRUE),
('TESTCODE999', 10000, FALSE);

INSERT INTO portemonnaie (user_id, solde) VALUES (1, 100000);
