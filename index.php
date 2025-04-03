<?php
ob_start();
session_start();

include('config/db.php'); 
include('include/header.php'); 
require_once('include/functions.php');
include('include/navbar.php'); 
include('include/sidebar.php'); 

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Pagination setup
$limit = 6; // Nombre de projets par page
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Validation de la page
$offset = ($page - 1) * $limit;

try {
    // Récupération des projets avec leur dernier commentaire et auteur
    $query = "
        SELECT 
            p.id AS id,
            p.title AS titre,
            GROUP_CONCAT(pa.author_name SEPARATOR ', ') AS auteurs_projet,
            COALESCE(c.content, '') AS dernier_commentaire,
            COALESCE(u.username, 'Anonyme') AS auteur_commentaire
        FROM 
            projects p
        LEFT JOIN project_authors pa ON p.id = pa.project_id
        LEFT JOIN (
            SELECT c1.project_id, c1.content, c1.user_id
            FROM comments c1
            INNER JOIN (
                SELECT project_id, MAX(created_at) AS max_created
                FROM comments
                GROUP BY project_id
            ) c2 
            ON c1.project_id = c2.project_id AND c1.created_at = c2.max_created
        ) c ON p.id = c.project_id
        LEFT JOIN users u ON c.user_id = u.id
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset
    ";

    // Préparez la requête
    $stmt = $pdo->prepare($query);

    // Liez les paramètres avec bindValue() au lieu de bindParam()
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Exécutez la requête
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul du nombre total de projets
    $query_projects_count = "SELECT COUNT(*) FROM projects";
    $stmt_projects_count = $pdo->prepare($query_projects_count);
    $stmt_projects_count->execute();
    $projects_count = $stmt_projects_count->fetchColumn();

    // Calcul du nombre total de pages
    $total_pages = ceil($projects_count / $limit);

    // Récupérer le nombre de commentaires, utilisateurs, et projets
    $query_comments_count = "SELECT COUNT(*) FROM comments";
    $stmt_comments_count = $pdo->prepare($query_comments_count);
    $stmt_comments_count->execute();
    $comments_count = $stmt_comments_count->fetchColumn();

    $query_users_count = "SELECT COUNT(*) FROM users";
    $stmt_users_count = $pdo->prepare($query_users_count);
    $stmt_users_count->execute();
    $users_count = $stmt_users_count->fetchColumn();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}

$current_page = basename($_SERVER['PHP_SELF']); // Récupère le nom du fichier actuel
?>

<div id="preloader">
    <img src="assets/images/logo.png" alt="Loading..." class="img-fluid">
</div>
<div id="content">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-wrapper">
                        <div class="container mt-5">
                           <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                                <div class="d-flex justify-content-end mb-4">
                                    <a href="create_project.php" class="btn btn-success btn-lg"><i class="fas fa-plus-circle"></i> Créer un Projet</a>
                                </div> 
                           <?php endif; ?>

                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= htmlspecialchars($comments_count) ?></h3>
                                            <p>Commentaires</p>
                                        </div>
                                        <a href="avis.php" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= htmlspecialchars($users_count) ?></h3>
                                            <p>Utilisateurs inscrits</p>
                                        </div> 
                                        <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                                        <a href="register.php" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><?= htmlspecialchars($projects_count) ?></h3>
                                            <p>Projets</p>
                                        </div>
                                        <a href="list_project.php" class="small-box-footer">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>

                                <!-- Affichage des projets -->
                                <div class="row">
    <?php foreach ($projects as $project) : ?>
        <div class="col-12 col-sm-6 col-md-4 mb-4"> <!-- Colonne bien définie -->
            <div class="card shadow-sm h-100"> <!-- Carte avec ombre et hauteur uniforme -->
                <div class="card-body d-flex flex-column">
                    <!-- Titre du projet -->
                    <h5 class="card-title"><?= htmlspecialchars($project['titre'] ?? 'Sans titre'); ?></h5>

                    <!-- Auteurs du projet -->
                    <?php if (!empty($project['auteurs_projet'])) : ?>
                        <h6 class="card-subtitle text-muted mb-4 mt-2">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($project['auteurs_projet']); ?>
                        </h6>
                    <?php endif; ?>

                    <!-- Dernier commentaire -->
                    <?php if (!empty($project['auteur_commentaire']) && !empty($project['dernier_commentaire'])) : ?>
                        <div class="comment-container mt-3 p-3 border rounded bg-light">
                            <p class="fw-bold"><?= htmlspecialchars($project['auteur_commentaire']); ?></p>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($project['dernier_commentaire'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Bouton pour voir le projet -->
                    <a href="view_project.php?id=<?= htmlspecialchars($project['id']); ?>" 
                       class="btn btn-primary mt-auto">
                        Voir le Projet
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

                                <!-- Pagination -->
                                <nav aria-label="Pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1) : ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages) : ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>

                            <!-- Ajout du calendrier -->
                            <div id="calendar" class="mt-5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include('include/footer.php'); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        let preloader = document.getElementById("preloader");
        let content = document.getElementById("content");
        if (preloader) {
            preloader.classList.add("hidden");
        }
        if (content) {
            content.style.display = "block";
        }
    }, 1000); // Réduit le délai à 1 seconde
});
</script>