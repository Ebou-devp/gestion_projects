<?php
session_start(); // Démarre la session pour gérer les données utilisateur et les messages d'erreur
include('config/db.php'); // Inclut le fichier de configuration pour la connexion à la base de données
include('include/functions.php'); // Inclut les fonctions utilitaires

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Si l'utilisateur n'est pas connecté, définir un message d'erreur et rediriger vers la page de connexion
    $_SESSION['error'] = "Vous devez être connecté pour accéder à la corbeille.";
    header('Location: login.php'); // Redirige vers la page de connexion
    exit;
}

// Récupérer les projets supprimés
// Préparer une requête SQL pour sélectionner les projets dont la colonne `deleted_at` n'est pas NULL
$stmt = $pdo->prepare("SELECT * FROM projects WHERE deleted_at IS NOT NULL");
$stmt->execute(); // Exécuter la requête
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats sous forme de tableau associatif
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corbeille</title>
</head>
<body>
    <h1>Projets dans la corbeille</h1>
    <table border="1"> <!-- Table pour afficher les projets supprimés -->
    <thead>
        <tr>
            <th>ID</th> <!-- Colonne pour l'ID du projet -->
            <th>Nom du projet</th> <!-- Colonne pour le nom du projet -->
            <th>Date de suppression</th> <!-- Colonne pour la date de suppression -->
            <th>Fichier</th> <!-- Colonne pour le fichier associé -->
            <th>Actions</th> <!-- Colonne pour les actions (restaurer ou supprimer définitivement) -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($projects as $project): ?> <!-- Boucle pour afficher chaque projet supprimé -->
            <tr>
                <td><?= $project['id'] ?></td> <!-- Affiche l'ID du projet -->
                <td><?= htmlspecialchars($project['title']) ?></td> <!-- Affiche le titre du projet avec protection contre les caractères spéciaux -->
                <td><?= $project['deleted_at'] ?></td> <!-- Affiche la date de suppression -->
                <td>
                    <?php if (!empty($project['files'])): ?>
                        <a href="<?= htmlspecialchars($project['files']); ?>" target="_blank">Voir le fichier</a>
                    <?php else: ?>
                        Aucun fichier
                    <?php endif; ?>
                </td>
                <td>
                    <!-- Lien pour restaurer le projet -->
                    <a href="restore_project.php?id=<?= $project['id'] ?>">Restaurer</a>
                    <!-- Lien pour supprimer définitivement le projet avec une confirmation -->
                    <a href="delete_project_permanently.php?id=<?= $project['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce projet ?');">Supprimer définitivement</a>
                    <!-- Lien pour supprimer uniquement le fichier -->
                    <?php if (!empty($project['files'])): ?>
                        <a href="delete_file.php?id=<?= $project['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');" class="btn btn-danger">Supprimer le fichier</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?> <!-- Fin de la boucle -->
    </tbody>
</table>
</body>
</html>