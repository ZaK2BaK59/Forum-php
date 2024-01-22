<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin2.css">

</head>
<body>
    <div class="head">
          <a href="index.php"> <img  class="index" src="home.png" alt="adminn"> </a><a href="logout.php"> <img  class="logout" src="logout.png" alt="logout"> </a>
    </div>
    <h1 class="cool">BlaBlaYnov</h1>
    <hr>
</body>
</html>



<?php
// Inclure le fichier de connexion à la base de données
require_once 'db.php';


session_start();


$sqlCategories = "SELECT * FROM category";
$resultCategories = $conn->query($sqlCategories);
$categories = $resultCategories->fetch_all(MYSQLI_ASSOC);

$sqlAuthors = "SELECT * FROM author";
$resultAuthors = $conn->query($sqlAuthors);
$authors = $resultAuthors->fetch_all(MYSQLI_ASSOC);



// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: login.php");
    exit;
}


// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $postId = $_POST['update'];

    // Rediriger vers la page de modification avec l'ID de l'article
    header("Location: admin.php?edit=$postId");
    exit;
}

// Traitement de la soumission du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $postId = $_POST['Id'];
    $newTitle = $_POST['title'];
    $newContents = $_POST['contents'];
    $newCategory = $_POST['category'];

    // Mettre à jour les détails du post dans la base de données
    // Utilisation de requête préparée pour éviter les problèmes de syntaxe SQL et les attaques par injection
$updateSql = $conn->prepare("UPDATE post SET Title = ?, Contents = ?, Category_Id = ? WHERE Id = ?");
$updateSql->bind_param("ssii", $newTitle, $newContents, $newCategory, $postId);

// Exécution de la requête
if ($updateSql->execute()) {
    echo "Article mis à jour avec succès.";
    // Rafraîchir la page après la mise à jour
    header("Location: admin.php");
    exit;
} else {
    echo "Erreur lors de la mise à jour de l'article : " . $conn->error;
}

// Fermer la requête préparée
$updateSql->close();


    if ($conn->query($updateSql) === TRUE) {
        echo "Article mis à jour avec succès.";
        // Rafraîchir la page après la mise à jour
        header("Location: admin.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour de l'article : " . $conn->error;
    }
}

// Traitement de la suppression d'article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $deleteId = $_POST['delete'];

    if (is_numeric($deleteId)) {
        // Supprimer le post de la base de données
        $deleteSql = "DELETE FROM post WHERE Id = $deleteId";

        if ($conn->query($deleteSql) === TRUE) {
            echo "Article supprimé avec succès.";
            // Rafraîchir la page après la suppression
            header("Location: admin.php");
            exit;
        } else {
            echo "Erreur lors de la suppression de l'article : " . $conn->error;
        }
    } else {
        echo "ID invalide pour la suppression.";
    }
}

// Récupérer tous les articles depuis la base de données
$sql = "SELECT * FROM post";
$result = $conn->query($sql);

// Afficher la liste des articles
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $postId = $row['Id'];
        $title = $row['Title'];
        $content = $row['Contents'];
        $category = $row['Category_Id'];

        echo "<div class='article'>";
        echo "<h3>$title</h3>";
        echo "<p>$content</p>";
        echo "<p>Catégorie : $category</p>";
        echo "<form action='admin.php' method='post'>";
        echo "<input type='hidden' name='update' value='$postId'>";
        echo "<input type='submit' value='Modifier'>";
        echo "</form>";
        echo "<form action='' method='post'>";
        echo "<input type='hidden' name='delete' value='$postId'>";
        echo "<input type='submit' value='Supprimer'>";
        echo "</form>";
        echo "<hr>";
        echo "</div>";
    }
} else {
    echo "Aucun article trouvé.";
}

// Affichage du formulaire d'édition si edit est présent dans l'URL
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editSql = "SELECT * FROM post WHERE Id = $editId";
    $editResult = $conn->query($editSql);

    if ($editResult->num_rows > 0) {
        $editRow = $editResult->fetch_assoc();
        $editTitle = $editRow['Title'];
        $editContents = $editRow['Contents'];
        $editCategory = $editRow['Category_Id'];

        // Afficher le formulaire d'édition
        echo "<h2>Modifier l'article</h2>";
        echo "<form action='admin.php' method='post'>";
        echo "<input type='hidden' name='Id' value='$editId'>        "; 
        echo "Titre: <input type='text' name='title' value='$editTitle'><br>";
        echo "Contenu: <textarea name='contents'>$editContents</textarea><br>";
        echo "Catégorie: <select name='category'>";
foreach ($categories as $category) {
    $categoryId = $category['Id'];
    $categoryName = $category['Name'];
    $selected = ($categoryId == $editCategory) ? 'selected' : '';
    echo "<option value='$categoryId' $selected>$categoryName</option>";
}
echo "</select><br>";
        echo "<input type='submit' name='submit' value='Enregistrer'>";
        echo "</form>";
    } else {
        echo "Article non trouvé pour l'édition.";
    }
}

// Affichage du formulaire d'ajout d'article
echo "<h2>Ajouter un nouvel article</h2>";
echo "<form action='admin.php' method='post'>";
echo "<label for='new_title'>Titre:</label> <input type='text' name='new_title'><br>";
echo "<label for='new_contents'>Contenu:</label> <textarea name='new_contents'></textarea><br>";
echo "<label for='new_category'>Catégorie:</label> <select name='new_category'>";
foreach ($categories as $category) {
    $categoryId = $category['Id'];
    $categoryName = $category['Name'];
    echo "<option value='$categoryId'>$categoryName</option>";
}
echo "</select><br>";


echo "<label for='new_author_first_name'>Prénom:</label> <input type='text' name='new_author_first_name'><br>";
echo "<label for='new_author_last_name'>Nom:</label> <input type='text' name='new_author_last_name'><br>";

echo "<input type='submit' name='add' value='Ajouter'>";
echo "</form>";



// Traitement de la soumission du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $newTitle = $_POST['new_title'];
    $newContents = $_POST['new_contents'];
    $newCategory = $_POST['new_category'];
    $existingAuthor = $_POST['new_author']; // Sélection d'un auteur existant
    $newAuthorFirstName = $_POST['new_author_first_name']; // Nouvel auteur - Prénom
    $newAuthorLastName = $_POST['new_author_last_name']; // Nouvel auteur - Nom

    // Vérifier si l'utilisateur a sélectionné un auteur existant ou a saisi les détails d'un nouvel auteur
    if (!empty($existingAuthor)) {
        $authorId = $existingAuthor;
    } else {
        // Insérer le nouvel auteur dans la base de données
        $insertAuthorSql = $conn->prepare("INSERT INTO author (FirstName, LastName) VALUES (?, ?)");
        $insertAuthorSql->bind_param("ss", $newAuthorFirstName, $newAuthorLastName);

        // Exécuter la requête d'insertion de l'auteur
        if ($insertAuthorSql->execute()) {
            $authorId = $insertAuthorSql->insert_id; // Récupérer l'ID du nouvel auteur
        } else {
            echo "Erreur lors de l'ajout de l'auteur : " . $conn->error;
            exit;
        }

        // Fermer la requête préparée pour l'ajout de l'auteur
        $insertAuthorSql->close();
    }
// Insérer le nouvel article dans la base de données
$insertPostSql = $conn->prepare("INSERT INTO post (Title, Contents, Category_Id, Author_Id, CreationTimestamp) VALUES (?, ?, ?, ?, NOW())");
$insertPostSql->bind_param("ssii", $newTitle, $newContents, $newCategory, $authorId);

// Exécuter la requête d'insertion de l'article
if ($insertPostSql->execute()) {
    echo "Nouvel article ajouté avec succès.";
    // Rafraîchir la page après l'ajout
    header("Location: admin.php");
    exit;
} else {
    echo "Erreur lors de l'ajout de l'article : " . $conn->error;
}

// Fermer la requête préparée pour l'ajout de l'article
$insertPostSql->close();
}

?>

