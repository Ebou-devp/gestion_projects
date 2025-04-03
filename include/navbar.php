<?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si les variables de session existent
$username = $_SESSION['username'] ?? 'Utilisateur';
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? 2; // 2 = Utilisateur par défaut
$avatar = $_SESSION['avatar'] ?? 'assets/dist/img/default-avatar.png';

// Inclure la connexion à la base de données
include('config/db.php');
?>

<!-- Début du corps avec la classe nécessaire -->
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <!-- Lien pour réduire la sidebar -->
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="index.php" class="nav-link">Accueil</a>
            </li>
        </ul>

        <!-- Formulaire de recherche -->
        <form action="search.php" method="GET" class="form-inline ml-auto">
            <input class="form-control mr-sm-2" type="text" name="query" placeholder="Rechercher un projet" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
        </form>

        <!-- Liens utilisateur -->
        <ul class="navbar-nav ml-auto">
            <?php if ($user_id) { ?>
                <!-- Bouton Voir Profil 
                <li class="nav-item">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#profilModal">
                        Voir Profil
                    </button>
                </li>-->
                
                <!-- Bouton Déconnexion -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">
                        Déconnecter
                    </a>
                </li>

                

                <!-- Modal Profil -->
                <div class="modal fade" id="profilModal" tabindex="-1" aria-labelledby="profilModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="profilModalLabel">Profil Utilisateur</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Informations utilisateur -->
                                <div class="text-center">
                                    <img class="img-circle elevation-2 mb-3" src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar utilisateur" width="100">
                                    <h3><?php echo htmlspecialchars($username); ?></h3>
                                    <h5><?php echo ($role == 1) ? 'Administrateur' : 'Utilisateur'; ?></h5>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="logout.php" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?');">Se déconnecter</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <li class="nav-item"><a href="login.php" class="nav-link">Se connecter</a></li>
            <?php } ?>
        </ul>
    </nav>
</div>

<!-- Ajoute ce script pour activer Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>