<?php
include ('config/db.php'); // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id = $_POST['id']; // ID du projet
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $features = trim($_POST['features']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validation des données
    if (empty($title) || empty($author) || empty($features) || empty($start_date) || empty($end_date)) {
        die('Tous les champs sont obligatoires.');
    }

    if (strtotime($start_date) > strtotime($end_date)) {
        die('La date de fin doit être après la date de début.');
    }

    // Gestion des fichiers
    $file_dest = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];

        // Définir une taille maximale (par exemple, 10 MB)
        $max_file_size = 10 * 1024 * 1024; // 10 MB

        if ($file_size <= $max_file_size) {
            // Générer un nom de fichier unique
            $new_file_name = uniqid('', true) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
            $file_dest = 'uploads/' . $new_file_name;

            // Déplacer le fichier uploadé dans le dossier "uploads"
            if (!move_uploaded_file($file_tmp_name, $file_dest)) {
                die("Erreur lors du téléchargement du fichier.");
            }
        } else {
            die("Le fichier est trop volumineux. Taille maximale autorisée : 10 MB.");
        }
    }

    try {
        // Préparer la requête de mise à jour
        $stmt = $pdo->prepare("UPDATE projects SET title = ?, author = ?, features = ?, start_date = ?, end_date = ?, files = ? WHERE id = ?");
        $stmt->execute([$title, $author, $features, $start_date, $end_date, $file_dest, $id]);

        // Redirection après mise à jour réussie
        header('Location: index.php?success=1');
        exit;

    } catch (Exception $e) {
        die("Erreur lors de la mise à jour du projet : " . $e->getMessage());
    }
} else {
    die('Requête non valide.');
}
?>