<?php
ob_start(); 
include('config/db.php'); // Connexion à la base de données
include('include/header.php');
include('include/sidebar.php'); 

// Vérification de la présence de l'ID dans l'URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo "Aucun ID reçu.";
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Requête SQL pour récupérer les informations du projet
$query = "SELECT id, title, features, start_date, end_date, files FROM projects WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les auteurs du projet
$query_authors = "SELECT author_name FROM project_authors WHERE project_id = ?";
$stmt_authors = $pdo->prepare($query_authors);
$stmt_authors->execute([$id]);
$authors = $stmt_authors->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les commentaires liés au projet
$query_comments = "SELECT c.*, u.username FROM comments c 
                   LEFT JOIN users u ON c.user_id = u.id 
                   WHERE c.project_id = ? 
                   ORDER BY c.created_at DESC";
$stmt_comments = $pdo->prepare($query_comments);
$stmt_comments->execute([$id]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <fieldset class="border p-3 mb-4">
                        <legend class="float-none w-auto px-3">Détails du Projet</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-body">
                                        <form>
                                            <div class="mb-3 row">
                                                <label for="title" class="col-md-4 col-form-label"><strong>Titre :</strong></label>
                                                <div class="col-md-8">
                                                    <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars($project['title']) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="authors" class="col-md-4 col-form-label"><strong>Auteurs :</strong></label>
                                                <div class="col-md-8">
                                                    <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars(implode(', ', $authors)) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="features" class="col-md-4 col-form-label"><strong>Fonctionnalités :</strong></label>
                                                <div class="col-md-8">
                                                    <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars($project['features']) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="start_date" class="col-md-4 col-form-label"><strong>Date de Début :</strong></label>
                                                <div class="col-md-8">
                                                    <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars($project['start_date']) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 row">
                                                <label for="end_date" class="col-md-4 col-form-label"><strong>Date de Fin :</strong></label>
                                                <div class="col-md-8">
                                                    <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                        <?= htmlspecialchars($project['end_date']) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Section téléchargement fichier -->
                                            <?php if (isset($_SESSION['user_id']) && $project['files'] && file_exists($project['files'])): ?>
                                                <div class="row mb-3">
                                                    <label for="download_file" class="col-md-4 col-form-label"><strong>Téléchargement du fichier :</strong></label>
                                                    <div class="col-md-8">
                                                        <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                            <a href="<?= htmlspecialchars($project['files']) ?>" download target="_blank" class="btn btn-link">
                                                                Télécharger le fichier
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="row mb-3">
                                                    <label for="no_file" class="col-md-4 col-form-label"><strong>Aucun fichier disponible :</strong></label>
                                                    <div class="col-md-8">
                                                        <div class="text-muted border rounded p-2" style="font-size: 0.8rem;">
                                                            Aucun fichier disponible ou accès restreint.
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5><strong>avis</strong></h5>
                                        <form>
                                            <fieldset>
                                                <?php if ($comments): ?>
                                                    <?php foreach ($comments as $comment): ?>
                                                        <div class="border rounded p-2 mb-2" style="font-size: 0.9rem;">
                                                            <strong><?= htmlspecialchars($comment['content']) ?></strong>
                                                            <div class="text-muted" style="font-size: 0.8rem;">Publié le <?= htmlspecialchars($comment['created_at']) ?></div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted">Aucun avis pour ce projet.</p>
                                                <?php endif; ?>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire pour ajouter un commentaire -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form action="add_avis.php" method="post">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($project['id']) ?>">
                                        <div class="mb-3">
                                            <textarea class="form-control" name="content" rows="4" placeholder="Écrivez votre commentaire ici..." required></textarea>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                        <a href="index.php" class="btn btn-secondary">Retour</a>
                                            <button type="submit" class="btn btn-primary">Ajouter</button>
                                            
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<?php include('include/footer.php'); ?>