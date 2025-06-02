package ligue1;

import db.DBConnection;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.io.FileWriter;
import java.io.IOException;

public class GenerateClubReport {
    public static void main(String[] args) {
        String query = """
            SELECT c.nom_club, s.nom AS nom_stade, s.ville, s.capacite
            FROM club c
            LEFT JOIN stade s ON c.id_stade = s.id_stade
            ORDER BY c.nom_club;
        """;

        // Use an absolute path to ensure the file is written to the correct location
        String outputFilePath = "C:/xampp/htdocs/ligue1/public/rapport.txt";

        try (Connection conn = DBConnection.getConnection();
             PreparedStatement stmt = conn.prepareStatement(query);
             ResultSet rs = stmt.executeQuery();
             FileWriter writer = new FileWriter(outputFilePath)) {

            // Debug: Print the file path being written to
            System.out.println("Tentative d'écriture dans : " + outputFilePath);

            // Write the report header
            writer.write("Nom du Club,Nom du Stade,Ville,Capacité\n");

            // Write data rows
            while (rs.next()) {
                String club = rs.getString("nom_club");
                String stade = rs.getString("nom_stade");
                String ville = rs.getString("ville");
                int capacite = rs.getInt("capacite");

                writer.write(String.format("%s,%s,%s,%d\n",
                        club,
                        stade != null ? stade : "Aucun stade",
                        ville != null ? ville : "N/A",
                        capacite));
            }

            System.out.println("✅ Rapport généré avec succès : " + outputFilePath);

        } catch (IOException e) {
            System.err.println("Erreur d'écriture : " + e.getMessage());
        } catch (Exception e) {
            System.err.println("Erreur lors de la génération du rapport : " + e.getMessage());
        }
    }
}
