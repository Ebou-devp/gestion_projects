<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $targetDir = "uploads/";  // Dossier où le fichier sera stocké
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $targetDir . $fileName;
    header("Location: index.php");
    // Vérifier si le fichier a bien été téléchargé
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo "Erreur lors du téléchargement du fichier. Code d'erreur: " . $_FILES['file']['error'];
    } else {
        // Vérifier le type de fichier autorisé
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (in_array($_FILES['file']['type'], $allowedTypes)) {
            // Tenter de déplacer le fichier vers le dossier cible
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
                // Mettre à jour la base de données avec le chemin du fichier
                $stmt = $pdo->prepare("UPDATE projects SET files = :path WHERE id = :id");
                $stmt->execute([
                    'path' => $targetFilePath,
                    'id' => $_POST['project_id']  // Assurez-vous que l'ID du projet est correct
                ]);
                echo "Fichier uploadé et lié au projet avec succès.";
            } else {
                echo "Erreur lors de l'upload du fichier. Impossible de déplacer le fichier.";
            }
        } else {
            echo "Type de fichier non autorisé.";
        }
    }
} else {
    echo "Aucun fichier n'a été téléchargé.";
}


?>
