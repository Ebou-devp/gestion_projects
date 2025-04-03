<?php
include('config/db.php'); // Inclure la connexion à la base de données

function deleteComment($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Vous devez être connecté pour supprimer un commentaire.";
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Vérifier si l'utilisateur est un administrateur ou s'il est l'auteur du commentaire
if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];

    // Récupérer l'utilisateur qui a posté ce commentaire
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
    $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) {
        echo "Commentaire introuvable.";
        exit();
    }

    // Si l'utilisateur est un administrateur ou l'auteur du commentaire
    if ($_SESSION['role'] == 1 ||  $_SESSION['role'] == 2 || $comment['user_id'] == $user_id) {
        try {
            // Supprimer le commentaire
            deleteComment($comment_id);
            
            // Rediriger vers la liste des commentaires
            header('Location: view_avis.php');
            exit();
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    } else {
        echo "Accès refusé. Vous ne pouvez supprimer que vos propres commentaires.";
        exit();
    }
} else {
    echo 'Aucun ID de avis fourni.';
}
?>
