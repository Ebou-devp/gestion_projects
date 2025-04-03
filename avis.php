<?php

ob_start();  // Démarrer le tampon de sortie
session_start();

  // Envoyer tout le contenu tamponné
include('config/db.php'); 
// Inclure les autres fichiers nécessaires
include('include/header.php');
include('include/navbar.php');
include('include/sidebar.php');
// Inclure la connexion à la base de données

// Récupérer tous les commentaires et leurs projets associés
// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté

// Vérification du rôle de l'utilisateur
$stmt = $pdo->prepare("SELECT user_role.role FROM user_role WHERE user_role.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_role = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur est admin, il peut voir tous les commentaires
if ($user_role['role'] == 1) {
    // Récupérer tous les commentaires pour l'admin
    $stmt = $pdo->prepare("
        SELECT comments.*, projects.title AS project_title 
        FROM comments 
        JOIN projects ON comments.project_id = projects.id
        ORDER BY comments.created_at DESC
    ");
} else {
    // Récupérer uniquement les commentaires de l'utilisateur connecté
    $stmt = $pdo->prepare("
        SELECT comments.*, projects.title AS project_title 
        FROM comments 
        JOIN projects ON comments.project_id = projects.id
        WHERE comments.user_id = :user_id
        ORDER BY comments.created_at DESC
    ");
    $stmt->execute(['user_id' => $user_id]);
}

// Exécuter la requête et récupérer les commentaires
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
ob_end_flush();
?>



<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-wrapper">
                    <section class="content">
<div class="container mt-5">
    <div class="table-container">
        <h1 class="text-center">Liste des avis</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Projet</th>
                    <th>avis</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= htmlspecialchars($comment['project_title']) ?></td>
                        <td><?= htmlspecialchars($comment['content']) ?></td>
                        <td><?= htmlspecialchars($comment['created_at']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $comment['id'] ?>" ><i class="fas fa-edit"></i></a>
                            <a href="delete_avis.php?id=<?= $comment['id'] ?>" onclick="return confirm('Confirmer la suppression ?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Retour</a>
    </div>
</div>
</section>
</div>
</div>
        </div>
    </div>
</section>
<?php include('include/footer.php'); ?>

