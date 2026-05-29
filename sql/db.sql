DROP DATABASE IF EXISTS AsteAutoDB;

CREATE DATABASE IF NOT EXISTS AsteAutoDB
CHARACTER SET="utf8mb4"
COLLATE="utf8mb4_general_ci";

USE AsteAutoDB;

CREATE TABLE Utente(
    id_utente INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    ruolo ENUM('offerente','amministratore') NOT NULL
);

CREATE TABLE Offerente(
    id_utente INT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    FOREIGN KEY(id_utente) REFERENCES Utente(id_utente)
);

CREATE TABLE Amministratore(
    id_utente INT PRIMARY KEY,
    codice_staff VARCHAR(30) NOT NULL UNIQUE,
    FOREIGN KEY(id_utente) REFERENCES Utente(id_utente)
);

CREATE TABLE Auto(
    id_auto INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL UNIQUE,
    descrizione VARCHAR(255) NOT NULL,
    anno INT NOT NULL,
    id_amministratore INT NOT NULL,
    FOREIGN KEY(id_amministratore) REFERENCES Amministratore(id_utente)
);

CREATE TABLE Asta(
    id_asta INT AUTO_INCREMENT PRIMARY KEY,
    prezzo_base DECIMAL(10,2) NOT NULL,
    prezzo_corrente DECIMAL(10,2) NOT NULL,
    data_ora_inizio DATETIME NOT NULL,
    data_ora_fine DATETIME NOT NULL,
    stato ENUM('aperta','chiusa') NOT NULL,
    id_auto INT NOT NULL,
    FOREIGN KEY(id_auto) REFERENCES Auto(id_auto)
);

CREATE TABLE Offerta(
    id_offerta INT AUTO_INCREMENT PRIMARY KEY,
    importo DECIMAL(10,2) NOT NULL,
    data_ora DATETIME NOT NULL,
    id_offerente INT NOT NULL,
    id_asta INT NOT NULL,
    FOREIGN KEY(id_offerente) REFERENCES Offerente(id_utente),
    FOREIGN KEY(id_asta) REFERENCES Asta(id_asta)
);

INSERT INTO Utente(username, password, email, ruolo) VALUES
('admin', '$2y$10$rzHIgRCEdGmGuXy1dSCazuYg8zxVROfijVEyP2miU1QWcJZFDCr.u', 'admin@dsauctions.it', 'amministratore'),
('dsoken', '$2y$10$rzHIgRCEdGmGuXy1dSCazuYg8zxVROfijVEyP2miU1QWcJZFDCr.u', 'diyar.soken@gmail.com', 'offerente'),
('test', '$2y$10$rzHIgRCEdGmGuXy1dSCazuYg8zxVROfijVEyP2miU1QWcJZFDCr.u', 'test.test@gmail.com', 'offerente');

INSERT INTO Amministratore(id_utente, codice_staff) VALUES
(1, 'STAFF001');

INSERT INTO Offerente(id_utente, nome, cognome) VALUES
(2, 'Diyar', 'Soken'),
(3, 'test', 'test');

INSERT INTO Auto(marca, descrizione, anno, id_amministratore) VALUES
('BMW', 'BMW M5 F90 Competition', 2022, 1),
('Porsche', 'Porsche 911', 2023, 1),
('Audi', 'Audi RS6', 2021, 1),
('Fiat', 'Fiat Panda', 2020, 1),
('Volkswagen', 'Volkswagen Golf', 2019, 1);

INSERT INTO Asta(prezzo_base, prezzo_corrente, data_ora_inizio, data_ora_fine, stato, id_auto) VALUES
(60000.00, 60000.00, '2026-01-01 09:00:00', '2026-12-31 18:00:00', 'aperta', 1),
(85000.00, 85000.00, '2026-01-01 09:00:00', '2026-12-31 18:00:00', 'aperta', 2),
(70000.00, 70000.00, '2026-01-01 09:00:00', '2026-12-31 18:00:00', 'aperta', 3),
(5000.00, 5000.00, '2026-01-01 09:00:00', '2026-12-31 18:00:00', 'aperta', 4),
(12000.00, 12000.00, '2026-01-01 09:00:00', '2026-12-31 18:00:00', 'aperta', 5);

INSERT INTO Offerta(importo, data_ora, id_offerente, id_asta) VALUES
(61000.00, '2026-05-01 10:30:00', 2, 1),
(13000.00, '2026-05-01 11:00:00', 3, 5);

UPDATE Asta SET prezzo_corrente=61000.00 WHERE id_asta=1;
UPDATE Asta SET prezzo_corrente=13000.00 WHERE id_asta=5;
