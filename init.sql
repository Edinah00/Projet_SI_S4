CREATE DATABASE IF NOT EXISTS Regime_sport;
use Regime_sport;

-- 1. UTILISATEURS ET AUTHENTIFICATION
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    genre CHAR(1) NOT NULL, -- 'M' ou 'F'
    role VARCHAR(20) DEFAULT 'user', -- 'user' ou 'admin'
    is_gold BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. INFORMATIONS DE SANTÉ (SÉPARÉES POUR L'INSCRIPTION EN 2 PAGES)
CREATE TABLE user_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    taille FLOAT NOT NULL, -- en mètres (ex: 1.75)
    poids_actuel FLOAT NOT NULL, -- en kg
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. BIBLIOTHÈQUE D'ALIMENTS
CREATE TABLE aliments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_aliment VARCHAR(100) NOT NULL,
    type_aliment VARCHAR(50) NOT NULL -- 'viande', 'poisson', 'volaille', 'legume'
);

-- 4. CATALOGUE DES RÉGIMES
CREATE TABLE regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_regime VARCHAR(100) NOT NULL,
    poids_impact FLOAT NOT NULL, -- ex: -2.0 (perdre 2kg) ou +1.5 (gagner 1.5kg)
    duree_jours INT NOT NULL,
    prix_journalier FLOAT NOT NULL,
    description TEXT
);

-- 5. COMPOSITION DU RÉGIME (Liaison Régime-Aliment)
CREATE TABLE regime_aliments (
    id_regime INT,
    id_aliment INT,
    pourcentage FLOAT NOT NULL, -- ex: 25.0 pour 25%
    PRIMARY KEY (id_regime, id_aliment),
    FOREIGN KEY (id_regime) REFERENCES regimes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_aliment) REFERENCES aliments(id)
);

-- 6. CATALOGUE DES ACTIVITÉS SPORTIVES
CREATE TABLE activites_sportives (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_activite VARCHAR(100) NOT NULL,
    poids_impact FLOAT NOT NULL, -- impact par jour/session
    duree_jours INT NOT NULL
);

-- 7. GESTION DU PORTE-MONNAIE ET CODES
CREATE TABLE codes_recharge (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(15) UNIQUE NOT NULL,
    valeur FLOAT NOT NULL,
    est_utilise BOOLEAN DEFAULT FALSE,
    est_valide BOOLEAN DEFAULT FALSE -- Validé par l'admin en Back Office
);

CREATE TABLE portemonnaie (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    solde FLOAT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. SUIVI DES ACHATS ET PROGRAMMES CHOISIS (Pour le PDF)
CREATE TABLE user_programmes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    id_regime INT,
    id_activite INT,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    poids_objectif_vise FLOAT,
    prix_total_paye FLOAT, -- Prix au moment de l'achat (remise Gold incluse ou non)
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (id_regime) REFERENCES regimes(id),
    FOREIGN KEY (id_activite) REFERENCES activites_sportives(id)
);