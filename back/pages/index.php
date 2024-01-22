<?php
// Inclure le fichier de connexion à la base de données
require_once 'db.php';

// Récupérer la catégorie sélectionnée (si elle existe)
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Requête pour récupérer les articles triés par date décroissante et filtrés par catégorie
$sql = "SELECT * FROM post";

if ($categoryFilter != 'all') {
    $sql .= " WHERE Category_Id = $categoryFilter";
}

$sql .= " ORDER BY CreationTimestamp DESC";

$result = $conn->query($sql);

// Récupérer les catégories depuis la base de données
$sqlCategories = "SELECT * FROM category";
$resultCategories = $conn->query($sqlCategories);
$categories = $resultCategories->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlaBlaYnov</title>
    <link rel="stylesheet" href="page.css">
</head>
<body>
    <div class="head">
          <a href="admin.php"> <img  class="admin" src="admin.png" alt="adminn"> </a><a href="logout.php"> <img  class="logout" src="logout.png" alt="logout"> </a>
    </div>
    <h1 class="cool">BlaBlaYnov</h1>
    <hr>
    <br>
    <br>
    <br>
    <br>

    <!-- Formulaire de filtrage par catégorie -->
    <form action="" method="get">
        <label for="category">Filtrer par catégorie :</label>
        <select name="category" id="category">
            <option value="all" <?php echo ($categoryFilter == 'all') ? 'selected' : ''; ?>>Toutes les catégories</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['Id']; ?>" <?php echo ($categoryFilter == $category['Id']) ? 'selected' : ''; ?>><?php echo $category['Name']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Filtrer">
    </form>

    <?php
    // Afficher la liste des articles
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postId = $row['Id'];
            $title = $row['Title'];
            $contentPreview = substr($row['Contents'], 0, 100);
    ?>

            <article>
                <h2><a href="detail.php?id=<?php echo $postId; ?>"><?php echo $title; ?></a></h2>
                <p><?php echo $contentPreview; ?>...</p>
            </article>

    <?php
        }
    } else {
        echo "Aucun article trouvé.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
    ?>

</body>
</html>
