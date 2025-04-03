<?php
include('config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: register.php'); // Redirection après suppression
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
    }
}
?>
