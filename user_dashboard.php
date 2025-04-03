<?php
session_start();
include('config/db.php'); // Connexion à la base de données
include('include/functions.php');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header('Location: login.php'); // Rediriger vers la page de connexion
    exit;
}

echo '<pre>';
print_r($_SESSION);
echo '</pre>';
exit;


// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tableau de bord Utilisateur</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.0.5/dist/css/adminlte.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Déconnexion</a>
        </li>
      </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Tableau de bord Utilisateur</span>
      </a>
      <div class="sidebar">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="profile.php" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>Mon Profil</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="tasks.php" class="nav-link">
              <i class="nav-icon fas fa-tasks"></i>
              <p>Mes Tâches</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="settings.php" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>Paramètres</p>
            </a>
          </li>
        </ul>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <div class="container mt-4">
        <h1 class="text-center">Bienvenue, <?= htmlspecialchars($user['username']); ?> !</h1>
        <p class="lead text-center">Voici votre tableau de bord personnel.</p>

        <!-- Section d'Informations de l'Utilisateur -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Mon Profil</h3>
              </div>
              <div class="card-body">
                <p><strong>Nom :</strong> <?= htmlspecialchars($user['username']); ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($user['email']); ?></p>
                <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']); ?></p>
                <a href="profile.php" class="btn btn-primary w-100">Modifier mon Profil</a>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Mes Tâches</h3>
              </div>
              <div class="card-body">
                <p>Consultez et gérez vos tâches personnelles ou vos projets en cours.</p>
                <a href="tasks.php" class="btn btn-primary w-100">Voir mes tâches</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Section Paramètres -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Paramètres</h3>
              </div>
              <div class="card-body">
                <p>Accédez à vos paramètres personnels et ajustez vos préférences.</p>
                <a href="settings.php" class="btn btn-primary w-100">Accéder aux paramètres</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.0.5/dist/js/adminlte.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
