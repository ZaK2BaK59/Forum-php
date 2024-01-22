<?php
require_once 'db.php';

// Vérifier si l'ID de l'article est passé dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id'];

    // Requête pour récupérer les détails de l'article
    // Requête pour récupérer les détails de l'article
$postQuery = "SELECT post.*, author.FirstName, author.LastName, DATE_FORMAT(post.CreationTimestamp, '%d/%m/%Y %H:%i:%s') AS FormattedTimestamp FROM post
LEFT JOIN author ON post.Author_Id = author.Id
WHERE post.Id = $postId";

    $postResult = $conn->query($postQuery);

    // Requête pour récupérer les commentaires associés à l'article
    $commentQuery = "SELECT *, DATE_FORMAT(CreationTimestamp, '%d/%m/%Y %H:%i:%s') AS FormattedTimestamp FROM comment WHERE Post_Id = $postId ORDER BY CreationTimestamp DESC";
    $commentResult = $conn->query($commentQuery);

    // Traitement du formulaire d'ajout de commentaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $authorName = $_POST['authorName'];
        $commentContent = $_POST['commentContent'];

        // Insérer le nouveau commentaire dans la base de données
        $insertCommentQuery = "INSERT INTO comment (NickName, Contents, CreationTimestamp, Post_Id)
                               VALUES ('$authorName', '$commentContent', NOW(), $postId)";
        $conn->query($insertCommentQuery);
    }
} else {
    // Rediriger si l'ID de l'article n'est pas fourni dans l'URL
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'article</title>
    <link rel="stylesheet" href="detail.css">
</head>
<body>
<div class="head">
          <a href="index.php"> <img  class="index" src="home.png" alt="adminn"> </a><a href="logout.php"> <img  class="logout" src="logout.png" alt="logout"> </a>
    </div>
    <h1 class="cool">BlaBlaYnov</h1>
    <hr>
    <?php
    // Afficher les détails de l'article
    if ($postResult->num_rows > 0) {
        $post = $postResult->fetch_assoc();
    ?>
        <article>
            <h1><?php echo $post['Title']; ?></h1>
            <p><?php echo $post['Contents']; ?></p>
            <p>Auteur: <?php echo $post['FirstName'] . ' ' . $post['LastName']; ?></p>
            <p>Date de publication: <?php echo $post['FormattedTimestamp']; ?></p>

        </article>
    <?php
    } else {
        echo "Article non trouvé.";
    }
    ?>

    <hr>

    <?php
    // Afficher la liste des commentaires
    if ($commentResult->num_rows > 0) {
        while ($comment = $commentResult->fetch_assoc()) {
    ?>
            <div>
                <p><strong><?php echo $comment['NickName']; ?>:</strong> <?php echo $comment['Contents']; ?></p>
                <p>Date de publication: <?php echo $comment['FormattedTimestamp']; ?></p>
            </div>
    <?php
        }
    } else {
        echo "Aucun commentaire trouvé.";
    }
    ?>

    <hr>

    <!-- Formulaire d'ajout de commentaire -->
    <form method="post" action="">
        <label for="authorName">Pseudo:</label>
        <input type="text" name="authorName" required>

        <label for="commentContent">Commentaire:</label>
        <textarea name="commentContent" rows="4" required></textarea>

        <button type="submit">Valider</button>
    </form>

</body>
</html>
