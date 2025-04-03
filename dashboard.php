<?php
// Démarrer la session au tout début
session_start();
include 'config/db.php'; // Connexion à la base de données

// Récupérer les projets récents
$stmt_projects = $pdo->query("SELECT title, start_date, status, files FROM projects ORDER BY start_date DESC LIMIT 5");
$projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

// Statistiques des utilisateurs
$stmt_users = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $stmt_users->fetch()['total_users'];

// Nombre de messages non lus
// Correction : Suppression de la condition sur 'is_read' car la colonne n'existe pas
$stmt_messages = $pdo->query("SELECT COUNT(*) AS unread_messages FROM messages");
$unread_messages = $stmt_messages->fetch()['unread_messages'];

// Graphique des projets créés par mois
$stmt_graph = $pdo->query("SELECT MONTH(start_date) AS month, COUNT(*) AS projects FROM projects GROUP BY MONTH(start_date)");
$monthly_projects = $stmt_graph->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <!-- Inclure les fichiers CSS de Bootstrap et AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="path_to_adminlte.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Supprimer la marge à gauche du conteneur principal */
        
    </style>
</head>
<body>
    <!-- En-tête -->
    <?php include 'include/header.php'; ?>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/sidebar.php'; ?>

    <!-- Contenu principal -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- Section des projets récents -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Projets récents</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Date de début</th>
                                            <th>Statut</th>
                                            <th>Fichier</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($projects as $project): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($project['title']); ?></td>
                                                <td><?= htmlspecialchars($project['start_date']); ?></td>
                                                <td><?= htmlspecialchars($project['status']); ?></td>
                                                <td>
                                                    <?php if (!empty($project['files'])): ?>
                                                        <a href="<?= htmlspecialchars($project['files']); ?>" target="_blank">Voir le fichier</a>
                                                    <?php else: ?>
                                                        Aucun fichier
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Section des statistiques -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistiques</h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Utilisateurs:</strong> <?= $total_users; ?></p>
                                <p><strong>Messages non lus:</strong> <?= $unread_messages; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Section des graphiques -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Projets créés par mois</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="projectsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php include 'include/footer.php'; ?>
    <!-- Graphique avec Chart.js -->
    <script>
        var ctx = document.getElementById('projectsChart').getContext('2d');
        var projectsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($month) {
                    $months = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    ];
                    return $months[$month['month']];
                }, $monthly_projects)); ?>,
                datasets: [{
                    label: 'Projets créés',
                    data: <?= json_encode(array_column($monthly_projects, 'projects')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 3
                }]
            }
        });
    </script>
</body>
</html>
