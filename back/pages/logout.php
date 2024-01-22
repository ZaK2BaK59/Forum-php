<?php
// Démarrez la session si ce n'est pas déjà fait
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion (ou une autre page après la déconnexion)
header("Location: login.php");
exit;
?>
