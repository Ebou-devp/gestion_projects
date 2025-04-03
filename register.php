<?php
session_start();
include('config/db.php'); // Connexion à la base de données
include('include/functions.php');
include('include/header.php');
include('include/navbar.php');
include('include/sidebar.php'); // Fonctions utiles

// Vérifier si l'utilisateur est Super Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) { // 1 : Super Admin
    $_SESSION['error'] = "Accès refusé. Vous devez être Super Admin pour accéder à cette page.";
    header('Location: login.php');
    exit;
}

// Récupérer les utilisateurs de la base de données
try {
    $stmt = $pdo->query("SELECT u.id, u.username, u.email, ur.role, u.department_id, d.name AS department_name 
                         FROM users u 
                         LEFT JOIN user_role ur ON u.id = ur.user_id 
                         LEFT JOIN departments d ON u.department_id = d.id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
    header('Location: login.php');
    exit;
}

// Récupérer les rôles et les départements
$roles = [
    1 => 'Super Admin',
    2 => 'Admin',
    3 => 'User'
];

try {
    $stmt = $pdo->query("SELECT id, name FROM departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la récupération des départements : " . $e->getMessage();
    header('Location: login.php');
    exit;
}

// Gestion de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = (int)$_POST['role'];
    $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;

    // Vérifications des champs
    if (empty($username) || empty($email) || empty($password) || empty($department_id)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'adresse email n'est pas valide.";
        header('Location: register.php');
        exit;
    }

    try {
        // Vérifier si un Admin ou un Utilisateur existe déjà pour ce département
        if (in_array($role, [2, 3])) { // 2: Admin, 3: User
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_role WHERE role = :role AND department_id = :department_id");
            $stmt->execute(['role' => $role, 'department_id' => $department_id]);
            $roleExists = $stmt->fetchColumn();

            if ($roleExists > 0) {
                $_SESSION['error'] = "Ce département a déjà un " . ($role == 2 ? "Admin" : "Utilisateur") . ".";
                header('Location: register.php');
                exit;
            }
        }

        // Hacher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insérer l'utilisateur dans la table `users`
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, department_id) VALUES (:username, :email, :password, :department_id)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'department_id' => $department_id
        ]);

        // Récupérer l'ID de l'utilisateur inséré
        $user_id = $pdo->lastInsertId();

        // Insérer le rôle dans la table `user_role`
        $stmt = $pdo->prepare("INSERT INTO user_role (user_id, role, department_id) VALUES (:user_id, :role, :department_id)");
        $stmt->execute([
            'user_id' => $user_id,
            'role' => $role,
            'department_id' => $department_id
        ]);

        $_SESSION['success'] = "Compte créé avec succès.";
        header('Location: register.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header('Location: register.php');
        exit;
    }
}
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-wrapper">
                    <div class="card">
                        <!-- Modal d'inscription -->
                        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="registerModalLabel">Créer un compte</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="register.php">
                                        <div class="modal-body">
                                            <?php if (isset($_SESSION['error'])): ?>
                                                <div class="alert alert-danger">
                                                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-group">
                                                <label for="username">Nom d'utilisateur</label>
                                                <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Mot de passe</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="role">Rôle</label>
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="2">Admin</option>
                                                    <option value="3" selected>Utilisateur</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="department">Département</label>
                                                <select class="form-select" id="department" name="department_id" required>
                                                    <option value="">Sélectionnez un département</option>
                                                    <?php foreach ($departments as $department): ?>
                                                        <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <button type="submit" class="btn btn-primary">Créer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Liste des utilisateurs -->
                        <div class="card-header">
                            <h3 class="card-title">Liste des utilisateurs</h3>
                            <button type="button" class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#registerModal">
                                Créer un compte
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nom d'utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Département</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($roles[$user['role']] ?? 'Inconnu') ?></td>
                                            <td><?= htmlspecialchars($user['department_name'] ?? 'Aucun') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('include/footer.php'); ?>