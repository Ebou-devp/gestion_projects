<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Démarre la session uniquement si aucune session n'est active
}
include('config/db.php'); // Connexion à la base de données
require_once('include/functions.php'); // Inclut les fonctions nécessaires
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link d-flex align-items-center">
        <img src="assets/images/logo.png"
             alt="logo"
             class="brand-image img-fluid img-circle elevation-5"
             style="max-width: 100%; height: auto; opacity: .8; margin-right: 10px;">
        <span class="brand-text font-weight-light" style="font-size: 1.2rem;">Gestion Projets</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <!-- Lien vers la page Projets -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Projets</p>
                    </a>
                </li>

                <!-- Afficher Créer un Projet si l'utilisateur a le rôle Admin -->
                <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                <li class="nav-item">
                    <a href="create_project.php" class="nav-link">
                        <i class="nav-icon fas fa-plus"></i>
                        <p>Créer un Projet</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Lien vers le Tableau de Bord -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Tableau de Bord</p>
                    </a>
                </li>

                <!-- Lien Modifier (visible uniquement pour les Admins) 
                <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                <li class="nav-item">
                    <a href="list_project.php" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>Modifier</p>
                    </a>
                </li>
                <?php endif; ?>-->

                <!-- Dropdown Paramètres (visible uniquement pour les Admins) -->
                <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1")) : ?>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownSettings" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="nav-icon fas fa-tools"></i>
                        <p>Paramètres</p>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownSettings">
                        <li><a class="dropdown-item" href="register.php">Créer un compte</a></li>
                        <li><a class="dropdown-item" href="login.php">Connexion</a></li>
                        <li><a class="dropdown-item" href="trash.php">suprimer</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>

