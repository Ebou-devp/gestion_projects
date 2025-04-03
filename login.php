<?php
session_start();

include('config/db.php'); // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Vérifier si les champs sont remplis
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: login.php');
        exit;
    }

    try {
        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {
                // Récupérer le rôle de l'utilisateur
                $stmtRole = $pdo->prepare("SELECT role FROM user_role WHERE user_id = :user_id");
                $stmtRole->execute(['user_id' => $user['id']]);
                $role = $stmtRole->fetch(PDO::FETCH_ASSOC);

                if ($role) {
                    // Stocker les informations dans la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $role['role']; // Rôle de l'utilisateur (1, 2, ou 3)

                    // Rediriger tous les utilisateurs vers index.php
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION['error'] = "Aucun rôle attribué à cet utilisateur.";
                    header('Location: login.php');
                    exit;
                }
            } else {
                $_SESSION['error'] = "Mot de passe incorrect.";
                header('Location: login.php');
               exit;
            }
        } else {
            $_SESSION['error'] = "Nom d'utilisateur incorrect.";
            header('Location: login.php');
           exit;
        }
    }catch (PDOException $e) {
       $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header('Location: login.php');
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS nécessaires -->
    <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Gestion</b>Projets</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Connexion</p>

            <!-- Afficher les messages d'erreur -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                <p class="mb-1 mt-3 text-center">
                <!--<a href="register.php">Créer un compte</a>-->
            </p>
                    <div class="col-5">

                        <button type="submit" class="btn btn-primary btn-block">Connexion</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS nécessaires -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>