<?php
include('config/db.php');
include('include/header.php');
include('include/navbar.php');
include('include/sidebar.php'); 

// Vérifier si l'ID du commentaire est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID du commentaire manquant ou invalide.');
}

// Récupérer l'ID du commentaire
$comment_id = (int)$_GET['id'];

// Récupérer le commentaire correspondant
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    die('Commentaire introuvable.');
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);

    if (!empty($content)) {
        // Mettre à jour le commentaire
        $stmt = $pdo->prepare("UPDATE comments SET content = ?, created_at = NOW() WHERE id = ?");
        $stmt->execute([$content, $comment_id]);

        // Rediriger vers la liste des commentaires du projet
        $project_id = $comment['project_id']; // Récupérer l'ID du projet à partir du commentaire
        header("Location: view_comments.php?project_id=" . $project_id);
        exit;
    } else {
        $error = "Le commentaire ne peut pas être vide.";
    }
}
?>


<div class="container mt-5">
    <h1>Modifier le Commentaire</h1>
    <a href="view_comments.php?project_id=<?= htmlspecialchars($comment['project_id']) ?>" class="btn btn-secondary mb-3">Retour à la Liste des Commentaires</a>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="content">Commentaire</label>
            <textarea name="content" id="content" class="form-control" rows="5" required><?= htmlspecialchars($comment['content']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Modifier</button>
    </form>
    <?php include('include/footer.php'); ?>
</div>

