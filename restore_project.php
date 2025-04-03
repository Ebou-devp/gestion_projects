<?php
session_start(); // Démarre la session pour gérer les données utilisateur et les messages d'erreur
include('config/db.php'); // Inclut le fichier de configuration pour la connexion à la base de données
include('include/functions.php'); // Inclut les fonctions utilitaires

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, définir un message d'erreur et rediriger vers la page de connexion
    $_SESSION['error'] = "Vous devez être connecté pour effectuer cette action.";
    header('Location: login.php'); // Redirige vers la page de connexion
    exit;
}

// Vérifier si l'utilisateur a le rôle approprié
if (!hasRole($_SESSION['user_id'], "1", "2")) {
    // Si l'utilisateur n'a pas les permissions nécessaires, définir un message d'erreur et rediriger vers la corbeille
    $_SESSION['error'] = "Vous n'avez pas les permissions nécessaires pour restaurer ce projet.";
    header('Location: trash.php'); // Redirige vers la corbeille
    exit;
}

// Vérifier si l'ID du projet est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si l'ID du projet n'est pas fourni dans l'URL, définir un message d'erreur et rediriger vers la corbeille
    $_SESSION['error'] = "ID du projet manquant.";
    header('Location: trash.php'); // Redirige vers la corbeille
    exit;
}

try {
    // Restaurer le projet
    // Préparer une requête SQL pour mettre à jour la colonne `deleted_at` à NULL
    $stmt = $pdo->prepare("UPDATE projects SET deleted_at = NULL WHERE id = :id");
    $stmt->execute(['id' => (int)$_GET['id']]); // Exécuter la requête avec l'ID du projet passé en paramètre
    echo "Projet restauré.<br>"; // Afficher un message de confirmation (utile pour le débogage)

    // Rediriger vers la corbeille après la restauration
    header('Location: trash.php'); // Redirige vers la corbeille
    exit;
} catch (PDOException $e) {
    // En cas d'erreur SQL, afficher le message d'erreur pour le débogage
    echo "Erreur SQL : " . $e->getMessage();
    // Définir un message d'erreur pour l'utilisateur et rediriger vers la corbeille
    $_SESSION['error'] = "Erreur lors de la restauration du projet.";
    header('Location: trash.php'); // Redirige vers la corbeille
    exit;
}
?>