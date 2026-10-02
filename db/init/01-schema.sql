-- Schéma initial de la base "banque".
-- Exécuté automatiquement par MySQL au premier démarrage du conteneur db (volume vide).
-- Doit rester aligné avec les CREATE TABLE des Pdo*Repository de chaque service.

USE banque;

-- auth-service
CREATE TABLE IF NOT EXISTS utilisateur (
    id VARCHAR(255) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    roles JSON NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- client-service
CREATE TABLE IF NOT EXISTS client (
    id VARCHAR(50) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- compte-service
CREATE TABLE IF NOT EXISTS comptes (
    id VARCHAR(255) PRIMARY KEY,
    client_id VARCHAR(255) NOT NULL,
    solde DECIMAL(10, 2) NOT NULL,
    est_bloque TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- transaction-service
CREATE TABLE IF NOT EXISTS transaction (
    id VARCHAR(255) PRIMARY KEY,
    compte_source_id VARCHAR(255) NOT NULL,
    compte_destination_id VARCHAR(255) NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    date_creation DATETIME NOT NULL,
    statut VARCHAR(50) NOT NULL,
    motif_echec TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
