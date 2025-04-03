<?php
session_start();
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la connexion à la base de données
include('config/db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Erreur : Vous devez être connecté pour ajouter un commentaire.");
}

$error_message = "";

// Vérifier si l'ID du projet est présent dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = (int)$_GET['id'];

    // Vérifier si l'ID du projet existe dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :project_id");
    $stmt->execute(['project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $error_message = "Erreur : Projet introuvable.";
    }
} else {
    $error_message = "Erreur : ID de projet invalide.";
}

// Vérifier si le formulaire est soumis et que le contenu du commentaire est valide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['content']) && is_numeric($_POST['id']) && !empty($_POST['content'])) {
    $project_id = (int)$_POST['id'];
    $content = trim($_POST['content']);

    // Vérifier si le projet existe
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :project_id");
    $stmt->execute(['project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        try {
            // Insérer le commentaire
            $stmt = $pdo->prepare("
                INSERT INTO comments (project_id, content, user_id, created_at) 
                VALUES (:project_id, :content, :user_id, NOW())
            ");
            $stmt->execute([
                'project_id' => $project_id,
                'content' => htmlspecialchars($content),
                'user_id' => $_SESSION['user_id']
            ]);

            // Redirection après succès
            header("Location: view_project.php?id=" . $project_id);
            exit();
        } catch (PDOException $e) {
            $error_message = "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
        }
    } else {
        $error_message = "Erreur : Projet introuvable.";
    }
}
$user_id = $_SESSION['user_id']; // Assure-toi que l'utilisateur est bien connecté

// Vérifie que l'utilisateur existe avant d'ajouter un commentaire
$query = "SELECT id FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // L'utilisateur n'existe pas, afficher un message d'erreur
    echo "Erreur : L'utilisateur n'existe pas.";
} else {
    // L'utilisateur existe, tu peux procéder à l'ajout du commentaire
    $query = "INSERT INTO comments (project_id, user_id, content) VALUES (:project_id, :user_id, :content)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->execute();
}
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo "Erreur : Vous devez être connecté pour ajouter un commentaire.";
    exit; // Arrête le script si l'utilisateur n'est pas connecté
}

$user_id = $_SESSION['user_id'];

 // Assure-toi d'avoir une connexion PDO déjà établie
$query = "SELECT id FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Si aucun utilisateur n'est trouvé avec cet ID, l'utilisateur est invalide
if ($stmt->rowCount() == 0) {
    echo "Erreur : L'utilisateur n'existe pas.";
    exit;
}


?>

<!-- Intégration de Bootstrap -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Ajouter un Commentaire</title>
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Ajouter un Commentaire au Projet</h1>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (isset($project)): ?>
        <h2><?= htmlspecialchars($project['title']) ?></h2>
        <p><strong>Auteur :</strong> <?= htmlspecialchars($project['author']) ?></p>
        <p><strong>Fonctionnalités :</strong> <?= nl2br(htmlspecialchars($project['features'])) ?></p>

        <h3>Ajouter un Commentaire</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($project_id) ?>">
            <div class="mb-3">
                <textarea name="content" class="form-control" placeholder="Votre commentaire ici..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Soumettre</button>
        </form>
    <?php endif; ?>

    <a href="view_project.php?id=<?= htmlspecialchars($project_id) ?>" class="btn btn-secondary mt-4">Retour au projet</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
