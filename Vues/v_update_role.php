<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mettre à jour un rôle</title>
</head>
<body>
    <h1>Mettre à jour un rôle</h1>
    <form method="post" action="/ligue1/update_role">
        <label for="role_id">ID du rôle:</label>
        <input type="text" id="role_id" name="role_id" required><br><br>
        <label for="role_name">Nom du rôle:</label>
        <input type="text" id="role_name" name="role_name" required><br><br>
        <input type="submit" value="Mettre à jour">
    </form>
</body>
</html>
