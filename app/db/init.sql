DROP DATABASE IF EXISTS Gestion_note;
CREATE DATABASE Gestion_note
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE Gestion_note;

DROP TABLE IF EXISTS notes;
DROP TABLE IF EXISTS ue_matieres;
DROP TABLE IF EXISTS ues;
DROP TABLE IF EXISTS matieres;
DROP TABLE IF EXISTS etudiants;

CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    parcours ENUM('dev', 'bddres', 'web') NOT NULL,
    email VARCHAR(150) NOT NULL
);

CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE,
    intitule VARCHAR(255) NOT NULL,
    credits INT NOT NULL,
    semestre INT NOT NULL
);

CREATE TABLE ues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(255) NOT NULL,
    semestre INT NOT NULL,
    parcours ENUM('tronc_commun', 'dev', 'bddres', 'web') NOT NULL,
    type_ue ENUM('single', 'option') NOT NULL,
    credits INT NOT NULL
);

CREATE TABLE ue_matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ue_id INT NOT NULL,
    matiere_id INT NOT NULL,
    CONSTRAINT fk_ue_matieres_ue FOREIGN KEY (ue_id) REFERENCES ues(id) ON DELETE CASCADE,
    CONSTRAINT fk_ue_matieres_matiere FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id INT NOT NULL,
    matiere_id INT NOT NULL,
    note DECIMAL(4,2) NOT NULL,
    date_saisie DATE NOT NULL,
    CONSTRAINT fk_notes_etudiant FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE,
    CONSTRAINT fk_notes_matiere FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);
