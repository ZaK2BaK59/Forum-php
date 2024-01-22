<?php
session_start();

require_once 'db.php';

// Vérifier si le formulaire de connexion est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier les identifiants (remplacez les valeurs par vos propres identifiants)
    if ($username === 'admin' && $password === 'azerty') {
        // Authentification réussie
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit;
    } else {
        // Authentification échouée
        $error_message = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <h2>Connexion</h2>

    <?php
    // Afficher un message d'erreur s'il y en a un
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form action="login.php" method="post">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required>
        <br>
        <input class="btn" type="submit" name="submit" value="Se connecter">
    </form>

</body>
</html>
