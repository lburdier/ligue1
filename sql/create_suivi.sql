CREATE TABLE IF NOT EXISTS suivi (
    id_suivi INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_club INT NOT NULL,
    date_suivi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_uti) ON DELETE CASCADE,
    FOREIGN KEY (id_club) REFERENCES club(id_club) ON DELETE CASCADE,
    UNIQUE (id_utilisateur, id_club)
);
