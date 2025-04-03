<?php
session_start();
include('config/db.php'); // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = intval($_POST['role_id']);
    $department_id = intval($_POST['department_id']);

    // Validation des champs
    if (empty($username) || empty($email) || $role_id === 0 || $department_id === 0) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: register.php');
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'adresse email n'est pas valide.";
        header('Location: register.php');
        exit;
    } else {
        // Mettre à jour l'utilisateur dans la base de données
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $id
            ]);

            $stmt = $pdo->prepare("UPDATE user_role SET role_id = :role_id, department_id = :department_id WHERE user_id = :user_id");
            $stmt->execute([
                'role_id' => $role_id,
                'department_id' => $department_id,
                'user_id' => $id
            ]);
            $pdo->commit();
            $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
            header('Location: register.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
            header('Location: register.php');
            exit;
        }
    }
}
?>