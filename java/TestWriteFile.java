import java.io.FileWriter;
import java.io.IOException;

public class TestWriteFile {
    public static void main(String[] args) {
        String path = "C:/xampp/htdocs/ligue1/public/rapport.txt";

        try (FileWriter writer = new FileWriter(path)) {
            writer.write("✅ Fichier généré avec succès depuis Java !");
            System.out.println("✔ Fichier écrit dans : " + path);
        } catch (IOException e) {
            System.err.println("❌ Erreur d'écriture : " + e.getMessage());
        }
    }
}
