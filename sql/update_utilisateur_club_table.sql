-- Create the 'utilisateur_club' table if it does not exist
CREATE TABLE IF NOT EXISTS utilisateur_club (
    id_utilisateur INT NOT NULL,
    id_club INT NOT NULL,
    etat BOOLEAN DEFAULT TRUE NOT NULL,
    PRIMARY KEY (id_utilisateur, id_club),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_uti) ON DELETE CASCADE,
    FOREIGN KEY (id_club) REFERENCES club(id_club) ON DELETE CASCADE
);

-- Ensure the 'etat' column exists and is not null
ALTER TABLE utilisateur_club ADD COLUMN IF NOT EXISTS etat BOOLEAN DEFAULT TRUE;
ALTER TABLE utilisateur_club ALTER COLUMN etat SET NOT NULL;
