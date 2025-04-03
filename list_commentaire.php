<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session au début du fichier, avant toute sortie
session_start();

// Connexion à la base de données
include('config/db.php'); 
include('include/header.php'); // Inclut le fichier header avec Bootstrap
include('include/navbar.php'); 
// Ici, on ne met pas la deuxième inclusion de sidebar.php, pour éviter la duplication.
include('include/sidebar.php'); 

// Requête pour récupérer les commentaires
$query_comments = "
    SELECT 
        comments.content AS comment_content,
        comments.created_at AS comment_date,
        users.username AS comment_author
    FROM comments
    LEFT JOIN users ON comments.user_id = users.id
    ORDER BY comments.created_at DESC;
";

// Préparation et exécution de la requête
$stmt_comments = $pdo->prepare($query_comments);

try {
    $stmt_comments->execute();
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des commentaires : " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Gestion_projet</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Inclure les autres fichiers CSS ici -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/bootstrap/dist/css/bootstrap.min.css">
  <style>
   
   


    



  </style>
</head>
<body>
  
  <div class="content-wrapper">
    <!-- Contenu principal -->
    <div class="main-content container mt-5">
      <h1 class="text-center mb-5">avis</h1>

      <div class="card">
          <div class="card-body">
              <ul class="list-group">
                  <?php if (!empty($comments)) : ?>
                      <?php foreach ($comments as $comment) : ?>
                          <li class="list-group-item">
                              <strong><?= htmlspecialchars($comment['comment_author'] ?? 'Utilisateur inconnu'); ?> :</strong>
                              <p><?= nl2br(htmlspecialchars($comment['comment_content'])); ?></p>
                              <small class="text-muted"><?= htmlspecialchars($comment['comment_date']); ?></small>
                          </li>
                      <?php endforeach; ?>
                  <?php else : ?>
                      <li class="list-group-item">Aucun commentaire trouvé.</li>
                  <?php endif; ?>
              </ul>
          </div>
      </div>
    </div>
  </div>

  <?php include('include/footer.php'); ?>

</body>
</html>
