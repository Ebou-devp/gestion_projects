<?php
ob_start();
// Connexion à la base de données (à ajuster si nécessaire)
include('config/db.php'); 

// Démarrer la session si ce n'est pas déjà fait
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour vérifier si un utilisateur a une permission donnée
if (!function_exists('hasPermission')) {
    function hasPermission($user_id, $permission_name, $pdo) {
        // Rechercher l'ID du rôle de l'utilisateur
        $query = "SELECT role FROM user_role WHERE id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch();
        if (!$user) {
            return false; // Utilisateur introuvable
        }
        $role_id = $user['role'];
        // Rechercher l'ID de la permission
        $query_permission = "SELECT id FROM permissions WHERE permission_name = :permission_name";
        $stmt_permission = $pdo->prepare($query_permission);
        $stmt_permission->execute(['permission_name' => $permission_name]);
        $permission = $stmt_permission->fetch();
        
        if (!$permission) {
            return false; // Permission introuvable
        }
        $permission_id = $permission['id'];
        // Vérifier si cette permission est associée au rôle de l'utilisateur
        $query_check_permission = "SELECT * FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id";
        $stmt_check_permission = $pdo->prepare($query_check_permission);
        $stmt_check_permission->execute(['role_id' => $role_id, 'permission_id' => $permission_id]);
        return $stmt_check_permission->rowCount() > 0;
    }
}

// Fonction d'inscription de l'utilisateur
if (!function_exists('registerUser')) {
    function registerUser($pdo, $username, $email, $password) {
        // Vérifier si l'email ou le nom d'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser) {
            return false; // Utilisateur ou email déjà existant
        }

        // Hacher le mot de passe et insérer l'utilisateur
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        return true; // Inscription réussie
    }
}

// Fonction de connexion de l'utilisateur
if (!function_exists('loginUser')) {
    function loginUser($pdo, $username, $password) {
        // Vérifier les identifiants
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Connexion réussie
        }
        return false; // Mauvais identifiants
    }
}

// Fonction pour vérifier si un utilisateur a un rôle donné
if (!function_exists('hasRole')) {
    function hasRole($userId, ...$roles) {
        global $pdo; // Utilisez la variable globale $pdo
        $placeholders = implode(',', array_fill(0, count($roles), '?'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_role WHERE user_id = ? AND role IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $roles));
        return $stmt->fetchColumn() > 0;
    }
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige vers login
    exit;
}

?>