<?php
ob_start();

// Assurez-vous que la session est démarrée

// Inclure le fichier de configuration de la base de  données
include('config/db.php');  
// Inclure les autres fichiers nécessaires
include('include/header.php');
include('include/navbar.php');
include('include/sidebar.php');
include('include/functions.php');

// Récupérer les projets avec leurs auteurs
try {
    $stmt = $pdo->query("
        SELECT 
            p.id, 
            p.title, 
            GROUP_CONCAT(pa.author_name SEPARATOR ', ') AS authors, 
            p.start_date, 
            p.end_date, 
            p.status 
        FROM projects p
        LEFT JOIN project_authors pa ON p.id = pa.project_id
        GROUP BY p.id
    ");
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Erreur lors de la récupération des projets : ' . $e->getMessage();
    $projets = [];
}
?>
<?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
    <div class="alert alert-success">Le projet a été supprimé avec succès.</div>
<?php endif; ?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-wrapper">
                    <section class="content">
                        <div class="container mt-4" style="max-width: 1200px;">
                            <h1 class="text-center mb-4">Liste des Projets</h1>
                            <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                            <a href="create_project.php" class="btn btn-primary mb-3">Créer un Projet</a>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table id="projectsTable" class="display table table-bordered mx-auto">
                                    <thead>
                                        <tr>
                                            <th>Titre</th>
                                            <th>Auteurs</th>
                                            <th>Dates</th>
                                            <th>Statut</th> <!-- Nouvelle colonne pour le statut -->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($projets)): ?>
                                            <?php foreach ($projets as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                                    <td><?= htmlspecialchars($row['authors']) ?></td>
                                                    <td><?= htmlspecialchars($row['start_date']) ?> - <?= htmlspecialchars($row['end_date']) ?></td>
                                                    <td><?= htmlspecialchars($row['status']) ?></td> <!-- Affichage du statut -->
                                                    <td>
                                                        <a href="avis.php?project_id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm me-2">
                                                            <i class="fas fa-comments"></i> 
                                                        </a>
                                                        <a href="view_project.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm me-2">
                                                            <i class="fas fa-eye"></i> 
                                                        </a>
                                                        <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                                                            <a href="update_status.php?id=<?= $row['id'] ?>" class="btn btn-sm me-2">
                                                                <i class="fas fa-sync"></i> <!-- Nouvelle icône -->
                                                            </a>

                                                        
                                                            <a href="edit_project.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm me-2">
                                                                <i class="fas fa-edit"></i> 
                                                            </a>
                                                            <?php endif; ?>
                                                            <?php if (isset($_SESSION['user_id']) && hasRole($_SESSION['user_id'], "1", "2")) : ?>
                                                            <a href="delete_project.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Confirmer la suppression ?');" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5">Aucun projet trouvé.</td></tr> <!-- Ajuster le colspan ici -->
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('include/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#projectsTable').DataTable({
            "language": {
                "sProcessing": "Traitement en cours...",
                "sSearch": "Rechercher&nbsp;: ",
                "sLengthMenu": "Afficher _MENU_ éléments",
                "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                "sInfoEmpty": "Affichage de 0 à 0 sur 0 éléments",
                "sInfoFiltered": "(filtré de _MAX_ éléments au total)",
                "sInfoPostFix": "",
                "sLoadingRecords": "Chargement...",
                "sZeroRecords": "Aucun résultat trouvé",
                "sEmptyTable": "Aucune donnée disponible dans le tableau",
                "oPaginate": {
                    "sFirst": "Premier",
                    "sPrevious": "Précédent",
                    "sNext": "Suivant",
                    "sLast": "Dernier"
                },
                "oAria": {
                    "sSortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
    });
</script>