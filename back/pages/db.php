<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "blablaynov";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
