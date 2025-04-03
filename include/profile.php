<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Inclure la base de données
include('config/db.php');

// Récupérer les données de l'utilisateur
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Lien vers vos styles -->
    <title>Profil Utilisateur</title>
</head>
<body>
    <div class="container">
        <h1>Profil de <?php echo htmlspecialchars($user['username']); ?></h1>
        <p>Email : <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Rôle : <?php echo ($user['role'] == 1) ? 'Administrateur' : 'Utilisateur'; ?></p>
        <p>Date de création : <?php echo htmlspecialchars($user['created_at']); ?></p>
        <a href="edit_profile.php" class="btn btn-warning">Modifier le profil</a>
    </div>
</body>
</html>
