CREATE DATABASE gestion_modules;
USE gestion_modules;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    description TEXT
);

CREATE TABLE user_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    module_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (module_id) REFERENCES modules(id)
);

INSERT INTO users (username, password)
VALUES ('aissa', '123');
INSERT INTO users (username, password)
VALUES ('zahoum', '123');

INSERT INTO modules (nom, description)
VALUES 
('PHP', 'Module PHP pour le web'),
('SQL', 'Base de données relationnelle'),
('JavaScript', 'Site web dinamique'),
('HTML/CSS', 'language de balisage'),
('Python', 'plusieur etulisateur'),
('Java', 'Poo');

INSERT INTO user_modules (user_id, module_id)
VALUES (1,1),(1,2);
ALTER TABLE user_modules ADD COLUMN est_coche TINYINT(1) DEFAULT 0;
UPDATE user_modules SET est_coche = 1 WHERE user_id = 1;

-- samme modification
-- ADD Modules
ALTER TABLE modules ADD COLUMN masse_horaire INT DEFAULT 0;
ALTER TABLE modules ADD COLUMN nombre_cours INT DEFAULT 0;

-- UPDATE THE NEW INFORMATION
UPDATE modules SET masse_horaire = 45, nombre_cours = 15 WHERE nom = 'PHP';
UPDATE modules SET masse_horaire = 30, nombre_cours = 10 WHERE nom = 'SQL';
UPDATE modules SET masse_horaire = 60, nombre_cours = 20 WHERE nom = 'JavaScript';
UPDATE modules SET masse_horaire = 40, nombre_cours = 12 WHERE nom = 'HTML/CSS';
UPDATE modules SET masse_horaire = 50, nombre_cours = 16 WHERE nom = 'Python';
UPDATE modules SET masse_horaire = 55, nombre_cours = 18 WHERE nom = 'Java';

