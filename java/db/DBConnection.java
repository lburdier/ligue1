package db;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.*;

public class DBConnection {
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection("jdbc:postgresql://localhost:5432/BD_ligue1", "postgres", "mot_de_passe"); // Replace with your credentials
    }
}
