<?php
session_start(); // Assurez-vous que la session est démarrée
ob_start();
include 'config/db.php';  // Connexion à la base de données

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $authors = $_POST['authors'];  // Récupère tous les auteurs
    $features = htmlspecialchars($_POST['features']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $department_id = $_POST['department_id'];  // Récupère le département

    $file_dest = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];

        $max_file_size = 10000000; // 10 MB
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (in_array($file['type'], $allowed_types)) {
            if ($file_size <= $max_file_size) {
                $new_file_name = uniqid('', true) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
                $file_dest = 'uploads/' . $new_file_name;
                if (!move_uploaded_file($file_tmp_name, $file_dest)) {
                    echo "<div class='alert alert-danger'>Erreur lors du téléchargement du fichier.</div>";
                    $file_dest = null;
                }
            } else {
                echo "<div class='alert alert-warning'>Fichier trop grand. Taille maximale autorisée : 10 MB.</div>";
                $file_dest = null;
            }
        } else {
            echo "<div class='alert alert-warning'>Type de fichier non autorisé. Seuls les fichiers PDF, JPG et PNG sont autorisés.</div>";
            $file_dest = null;
        }
    }

    try {
        // Insérer le projet
        $stmt = $pdo->prepare("INSERT INTO projects (title, features, start_date, end_date, status, files, department_id) 
        VALUES (:title, :features, :start_date, :end_date, :status, :files, :department_id)");
        $stmt->execute([
            'title' => $title,
            'features' => $features,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'files' => $file_dest,
            'department_id' => $department_id
        ]);

        // Récupérer l'ID du projet inséré
        $project_id = $pdo->lastInsertId();

        // Insérer les auteurs associés au projet
        foreach ($authors as $author) {
            $stmt_author = $pdo->prepare("INSERT INTO project_authors (project_id, author_name) VALUES (:project_id, :author_name)");
            $stmt_author->execute([
                'project_id' => $project_id,
                'author_name' => $author
            ]);
        }

        header('Location: list_project.php');
        exit;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout du projet : " . $e->getMessage() . "</div>";
    }
}
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
                        <h3 class="card-title">Ajouter un Nouveau Projet</h3>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="col-form-label">Titre du projet</label>
                                        <input type="text" id="title" name="title" class="form-control" placeholder="Titre du projet" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="authors" class="col-form-label">Auteurs</label>
                                        <div id="authors-container">
                                            <!-- Un auteur au départ -->
                                            <div class="author-group">
                                                <input type="text" name="authors[]" class="form-control mb-2" placeholder="Nom de l'auteur" required>
                                            </div>
                                        </div>
                                        <button type="button" id="add-author" class="btn btn-primary mt-3">Ajouter un auteur</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="features" class="col-form-label">Description des fonctionnalités</label>
                                        <textarea id="features" name="features" class="form-control" rows="3" placeholder="Description des fonctionnalités" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="col-form-label">Date de début</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="col-form-label">Date de fin</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="col-form-label">Statut</label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="">Sélectionner un statut</option>
                                    <option value="In Progress">En Cours</option>
                                    <option value="Completed">Terminé</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="department_id" class="col-form-label">Département</label>
                                <select id="department_id" name="department_id" class="form-select" required>
                                    <option value="">Sélectionner un département</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT id, name FROM departments");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file" class="col-form-label">Télécharger un fichier</label>
                                    <input type="file" id="file" name="file" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times-circle"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-success ms-2">
                                <i class="fas fa-check-circle"></i> Ajouter le Projet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include('include/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Ajout d'un auteur
        $('#add-author').click(function() {
            var authorCount = $('.author-group').length; // Compte le nombre de champs d'auteurs existants
            if (authorCount < 2) { // Limite à 2 auteurs
                var newAuthorGroup = $('<div class="author-group"><input type="text" name="authors[]" class="form-control mb-2" placeholder="Nom de l\'auteur" required></div>');
                $('#authors-container').append(newAuthorGroup);
            } else {
                alert('Vous ne pouvez ajouter que 2 auteurs maximum.');
            }
        });
    });
    
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
