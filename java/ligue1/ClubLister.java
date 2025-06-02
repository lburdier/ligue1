package ligue1;

import db.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

public class ClubLister {
    public static void main(String[] args) {
        String query = """
            SELECT c.nom_club, s.nom AS nom_stade, s.ville, s.capacite
            FROM club c
            LEFT JOIN stade s ON c.id_stade = s.id_stade
            ORDER BY c.nom_club;
        """;

        try (Connection conn = DBConnection.getConnection();
             PreparedStatement stmt = conn.prepareStatement(query);
             ResultSet rs = stmt.executeQuery()) {

            while (rs.next()) {
                String club = rs.getString("nom_club");
                String stade = rs.getString("nom_stade");
                String ville = rs.getString("ville");
                int capacite = rs.getInt("capacite");

                System.out.println("🏟️ " + club + " — " +
                        (stade != null ? stade + " (" + ville + ", " + capacite + " places)" : "Aucun stade"));
            }

        } catch (Exception e) {
            System.err.println("Erreur lors de la récupération des clubs : " + e.getMessage());
        }
    }
}
