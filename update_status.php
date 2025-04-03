<?php
session_start(); // Assurez-vous que la session est démarrée
ob_start();
include 'config/db.php';  // Connexion à la base de données

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Vérification de la présence de l'ID dans l'URL
if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
} else {
    echo "Aucun ID reçu.";
    exit;
}

// Si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    try {
        // Mise à jour du statut du projet
        $stmt = $pdo->prepare("UPDATE projects SET status = :status, start_date = :start_date, end_date = :end_date WHERE id = :id");
        $stmt->execute([
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'id' => $project_id
        ]);

        header('Location: list_project.php');
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du projet : " . $e->getMessage() . "</div>";
    }
}

// Récupérer les informations du projet
$query = "SELECT id, title, status, start_date, end_date FROM projects WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

include 'include/header.php';
include 'include/sidebar.php'; 
include 'include/navbar.php';
?>

<div class="content-wrapper">
    <section class="content mt-5">
        <div class="container d-flex justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Mettre à jour le statut du projet</h3>
                    </div>
                    <form method="POST">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="col-form-label">Titre du projet</label>
                                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="col-form-label">Statut</label>
                                <select id="status" name="status" class="form-select" required>
                                    <!--<option value="En Attente de Validation" <?= $project['status'] == 'En Attente de Validation' ? 'selected' : '' ?>> </option>-->
                                    <option value="En Cours" <?= $project['status'] == 'En Cours' ? 'selected' : '' ?>>En Cours</option>
                                    <option value="Terminé" <?= $project['status'] == 'Terminé' ? 'selected' : '' ?>>Terminé</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="col-form-label">Date de début</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($project['start_date']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="col-form-label">Date de fin</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($project['end_date']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-check-circle"></i> Mettre à jour
                            </button>
                            <a href="list_project.php" class="btn btn-secondary">
                                <i class="fas fa-times-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include('include/footer.php'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        // Lorsque la date de début est sélectionnée
        startDateInput.addEventListener('change', function () {
            const startDate = startDateInput.value;
            if (startDate) {
                // Définir la date minimale pour la date de fin
                endDateInput.min = startDate;
            }
        });

        // Lorsque la date de fin est sélectionnée
        endDateInput.addEventListener('change', function () {
            const endDate = endDateInput.value;
            if (endDate) {
                // Définir la date maximale pour la date de début
                startDateInput.max = endDate;
            }
        });
    });
    </script>