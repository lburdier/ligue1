CREATE TABLE IF NOT EXISTS classement (
    id SERIAL PRIMARY KEY,
    position INT NOT NULL,
    nom_club VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    victoires INT NOT NULL,
    defaites INT NOT NULL,
    matchs_nuls INT NOT NULL,
    date_mise_a_jour TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
