-- Add the 'adresse' column to the 'stade' table
ALTER TABLE stade ADD COLUMN adresse VARCHAR(255) DEFAULT NULL;

-- Insert sample data for the 'adresse' column
UPDATE stade
SET adresse = CASE id_stade
    WHEN 1 THEN '10 Rue des Stades, Paris'
    WHEN 2 THEN '25 Avenue des Champs, Lyon'
    WHEN 3 THEN '50 Boulevard des Sports, Marseille'
    ELSE 'Adresse inconnue'
END;
