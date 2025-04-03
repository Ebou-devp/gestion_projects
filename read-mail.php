<?php
// Assurez-vous que la connexion à la base de données est bien établie
include('config/db.php'); // $pdo doit être défini dans ce fichier

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si les champs sont remplis
    if (isset($_POST['subject'], $_POST['content'], $_POST['message_id'])) {
        $sender_id = 1; // Exemple : ID de l'utilisateur connecté
        $receiver_id = 2; // Exemple d'ID de destinataire
        $subject = $_POST['subject'];
        $content = $_POST['content'];
        $message_id = $_POST['message_id']; // ID du message auquel on répond

        try {
            // Préparation de la requête d'insertion dans la table messages
            $query = "INSERT INTO messages (sender_id, receiver_id, subject, content, reply_to) 
                      VALUES (:sender_id, :receiver_id, :subject, :content, :reply_to)";
            $stmt = $pdo->prepare($query);

            // Liaison des paramètres
            $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
            $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
            $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':reply_to', $message_id, PDO::PARAM_INT);

            // Exécution de la requête d'insertion
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Message envoyé avec succès!</div>";
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de l'envoi du message.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
        }
    }
}

// Récupération des messages pour le destinataire avec ID = 2
try {
    $receiver_id = 2; // Exemple d'ID
    $query = "SELECT * FROM messages WHERE receiver_id = :receiver_id ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailbox - Send Message</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Fixer le contenu pour éviter qu'il ne se cache sous le sidebar */
        .content-wrapper { 
            margin-left: 250px; /* Si la sidebar a une largeur de 250px, ajustez selon vos besoins */
        }
        /* Assurer que le contenu dans la colonne droite est bien aligné */
        .col-md-3 {
            padding-right: 0;
        }
        .col-md-9 {
            padding-left: 0;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('include/navbar.php'); ?>
    <?php include('include/sidebar.php'); ?>

    <div class="content-wrapper">
        <div class="container mt-5">
            <h1 class="mb-4">Mailbox</h1>
            <div class="row">
                <div class="col-md-3">
                    <!-- Navigation du menu gauche -->
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active"><i class="fas fa-inbox"></i> Inbox</a>
                        <a href="#" class="list-group-item list-group-item-action"><i class="far fa-envelope"></i> Sent</a>
                        <a href="#" class="list-group-item list-group-item-action"><i class="far fa-file-alt"></i> Drafts</a>
                        <a href="#" class="list-group-item list-group-item-action"><i class="far fa-trash-alt"></i> Trash</a>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Formulaire d'envoi de message -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Compose a Message</h3>
                        </div>
                        <div class="card-body">
                            <form action="read-mail.php" method="post">
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" name="subject" class="form-control" id="subject" placeholder="Enter subject" required>
                                </div>
                                <div class="form-group">
                                    <label for="content">Message</label>
                                    <textarea name="content" class="form-control" id="content" rows="5" placeholder="Write a message" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Message</button>
                            </form>
                        </div>
                    </div>

                    <!-- Liste des messages reçus -->
                    <h3 class="mt-5">Received Messages</h3>
                    <div class="list-group">
                        <?php if (!empty($messages)) {
                            foreach ($messages as $row) { ?>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <strong>From:</strong> <?php echo htmlspecialchars($row['sender_id']); ?> - 
                                    <strong>Subject:</strong> <?php echo htmlspecialchars($row['subject']); ?>
                                    <br>
                                    <strong>Message:</strong> <?php echo htmlspecialchars($row['content']); ?>
                                </a>

                                <!-- Formulaire de réponse sous chaque message -->
                                <form action="read-mail.php" method="post">
                                    <div class="form-group mt-3">
                                        <label for="subject">Subject</label>
                                        <input type="text" name="subject" class="form-control" id="subject" value="Re: <?php echo htmlspecialchars($row['subject']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="content">Reply</label>
                                        <textarea name="content" class="form-control" id="content" rows="5" placeholder="Your message" required></textarea>
                                    </div>
                                    <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>"> <!-- ID du message auquel on répond -->
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send Reply</button>
                                </form>
                            <?php }
                        } else { ?>
                            <p></p>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php include('include/footer.php'); ?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
