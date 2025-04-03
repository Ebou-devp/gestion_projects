<?php
session_start();
include('config/db.php'); // Connexion à la base de données

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$query = trim($_GET['query'] ?? '');

if (empty($query)) {
    $_SESSION['error'] = "Veuillez entrer un terme de recherche.";
    header('Location: index.php');
    exit;
}

try {
    // Corrigez la colonne utilisée dans la clause WHERE
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE title LIKE :query OR id IN (SELECT project_id FROM project_authors WHERE author_name LIKE :query)");
    $stmt->execute(['query' => '%' . $query . '%']);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la recherche : " . $e->getMessage();
    header('Location: index.php');
    exit;
}

include('include/header.php');
include('include/navbar.php');
include('include/sidebar.php');
?>

<div class="content-wrapper">
    <section class="content mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Résultats de la recherche pour "<?php echo htmlspecialchars($query); ?>"</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($projects)): ?>
                                <p>Aucun projet trouvé.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Titre</th>
                                                <th>Auteurs</th>
                                                <th>Date de début</th>
                                                <th>Date de fin</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($projects as $project): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($project['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                                                    <td>
                                                        <?php
                                                        $stmt_authors = $pdo->prepare("SELECT author_name FROM project_authors WHERE project_id = :project_id");
                                                        $stmt_authors->execute(['project_id' => $project['id']]);
                                                        $authors = $stmt_authors->fetchAll(PDO::FETCH_COLUMN);
                                                        echo htmlspecialchars(implode(', ', $authors));
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($project['start_date']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['end_date']); ?></td>
                                                    <td><?php echo htmlspecialchars($project['status']); ?></td>
                                                    <td>
                                                        <a href="view_project.php?id=<?php echo htmlspecialchars($project['id']); ?>" class="btn btn-primary btn-sm">Voir</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('include/footer.php'); ?>
