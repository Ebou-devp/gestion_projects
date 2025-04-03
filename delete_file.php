<?php
session_start();
include('config/db.php'); // Inclut la configuration de la base de données

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vous devez être connecté pour effectuer cette action.";
    header('Location: login.php');
    exit;
}

// Vérifier si l'ID du projet est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID du projet manquant.";
    header('Location: edit_project.php');
    exit;
}

$project_id = (int)$_GET['id'];

try {
    // Récupérer le fichier associé au projet
    $stmt = $pdo->prepare("SELECT files FROM projects WHERE id = :id");
    $stmt->execute(['id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project && !empty($project['files'])) {
        // Mettre à jour la colonne `deleted_at` avec la date actuelle et vider la colonne `files`
        $stmt = $pdo->prepare("UPDATE projects SET deleted_at = NOW(), files = NULL WHERE id = :id");
        $stmt->execute(['id' => $project_id]);

        $_SESSION['success'] = "Le fichier a été marqué comme supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Aucun fichier trouvé pour ce projet.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la suppression du fichier : " . $e->getMessage();
}

header('Location: edit_project.php?id=' . $project_id);
exit;
?>