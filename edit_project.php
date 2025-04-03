<?php
ob_start(); // Démarrer le tampon de sortie

include 'config/db.php';  // Connexion à la base de données
include 'include/header.php';  // Inclusion du header
include 'include/navbar.php';  // Inclusion du navbar
include 'include/sidebar.php'; // Inclusion du sidebar

// Vérifier si l'ID du projet est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $project_id = $_GET['id'];

    // Vérifier si l'ID est un nombre valide
    if (!is_numeric($project_id)) {
        die("ID du projet invalide.");
    }

    // Récupérer les informations du projet
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();

    if (!$project) {
        die("Le projet avec cet ID n'existe pas.");
    }

    // Récupérer les auteurs du projet
    $stmt_authors = $pdo->prepare("SELECT author_name FROM project_authors WHERE project_id = ?");
    $stmt_authors->execute([$project_id]);
    $authors = $stmt_authors->fetchAll(PDO::FETCH_COLUMN);

  
    // Si le formulaire a été soumis
    $file_uploaded_message = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $title = $_POST['title'];
        $authors = $_POST['authors'];
        $features = $_POST['features'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];
        $file_dest = $project['files'];

        // Vérification du fichier
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $file_name = $file['name'];
            $file_tmp_name = $file['tmp_name'];
            $file_size = $file['size'];
            $max_file_size = 10000000; // 10 MB

            if ($file_size <= $max_file_size) {
                $new_file_name = uniqid('', true) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
                $file_dest = 'uploads/' . $new_file_name;
                if (!move_uploaded_file($file_tmp_name, $file_dest)) {
                    echo "<div class='alert alert-danger'>Erreur lors du téléchargement du fichier.</div>";
                    $file_dest = null;
                } else {
                    $file_uploaded_message = "<p>Fichier téléchargé : <a href='$file_dest' target='_blank'>Voir le fichier</a></p>";
                }
            } else {
                echo "<div class='alert alert-warning'>Fichier trop grand. Taille maximale autorisée : 10 MB.</div>";
                $file_dest = $project['files'];
            }
        }

        try {
            // Mise à jour du projet avec le statut
            $stmt = $pdo->prepare("UPDATE projects SET title = :title, features = :features, 
                                    start_date = :start_date, end_date = :end_date, status = :status, files = :files 
                                    WHERE id = :id");
            $stmt->execute([
                'title' => $title,
                'features' => $features,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => $status,
                'files' => $file_dest,
                'id' => $project_id
            ]);

            // Supprimer les anciens auteurs
            $stmt_delete_authors = $pdo->prepare("DELETE FROM project_authors WHERE project_id = ?");
            $stmt_delete_authors->execute([$project_id]);

            // Insérer les nouveaux auteurs
            foreach ($authors as $author) {
                $stmt_author = $pdo->prepare("INSERT INTO project_authors (project_id, author_name) VALUES (:project_id, :author_name)");
                $stmt_author->execute([
                    'project_id' => $project_id,
                    'author_name' => $author
                ]);
            }

            //echo "<div class='alert alert-success'>Le projet a été modifié avec succès.</div>";
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur lors de la modification du projet : " . $e->getMessage() . "</div>";
        }
    }
} else {
    die("ID du projet manquant.");
}

ob_end_flush();
?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-wrapper">
                        <section class="content">
                            <div class="container-fluid">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h3 class="card-title">Modifier un Projet</h3>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="edit_project.php?id=<?php echo $project_id; ?>" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Titre</label>
                                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="authors" class="form-label">Auteurs</label>
                                                <div id="authors-container">
                                                    <?php foreach ($authors as $author): ?>
                                                        <div class="author-group">
                                                            <input type="text" name="authors[]" class="form-control mb-2" id="authors" value="<?php echo htmlspecialchars($author); ?>" required>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <button type="button" id="add-author" class="btn btn-primary mt-3">Ajouter un auteur</button>
                                            </div>
                                            <div class="mb-3">
                                                <label for="features" class="form-label">Fonctionnalités</label>
                                                <textarea class="form-control" id="features" name="features" required><?php echo htmlspecialchars($project['features']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Date de Début</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Date de Fin</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Statut</label>
                                                <select class="form-select" id="status" name="status">
                                                    <!--<option value="En Attente" <?php echo ($project['status'] == 'En Attente') ? 'selected' : ''; ?>>En Attente</option>-->
                                                    <option value="En Cours" <?php echo ($project['status'] == 'En Cours') ? 'selected' : ''; ?>>En Cours</option>
                                                    <option value="Terminé" <?php echo ($project['status'] == 'Terminé') ? 'selected' : ''; ?>>Terminé</option>
                                                </select>
                                            </div>
                                          <div class="mb-3">
    <label for="file" class="form-label">Modifier un fichier :</label>
    <input type="file" class="form-control" name="file" id="file">

    <?php if ($file_uploaded_message): ?>
        <div class="form-text"><?php echo $file_uploaded_message; ?></div>
    <?php elseif (!empty($project['files'])): ?> <!-- Vérifie si la colonne 'files' n'est pas vide -->
        <div class="form-text d-flex align-items-center">
            <!-- Lien pour voir le fichier -->
            <a href="<?php echo htmlspecialchars($project['files']); ?>" target="_blank" class="me-3">Voir le fichier</a>

            <!-- Bouton pour supprimer le fichier -->
            <a href="delete_file.php?id=<?php echo htmlspecialchars($project['id']); ?>" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');" 
               class="btn btn-danger btn-sm">
                <i class="fas fa-times"></i> Supprimer
            </a>
        </div>
    <?php else: ?>
        <div class="form-text text-muted">Aucun fichier associé à ce projet.</div>
    <?php endif; ?>
</div>

                                            <button type="submit" class="btn btn-success">Mettre à jour</button>
                                        </form>
                                    </div>
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
<script>
    $(document).ready(function() {
        // Ajout d'un auteur
        $('#add-author').click(function() {
            var newAuthorGroup = $('<div class="author-group"><input type="text" name="authors[]" class="form-control mb-2" placeholder="Nom de l\'auteur" required></div>');
            $('#authors-container').append(newAuthorGroup);
        });
    });
</script>

