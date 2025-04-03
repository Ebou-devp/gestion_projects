<?php
$host = '127.0.0.1';  // hôte de la base de données
$db = 'gestion_projets';  // nom de la base de données
$user = 'root';  // utilisateur de la base de données
$pass = '';  // mot de passe
$charset = 'utf8mb4';  // jeu de caractères

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données: ' . $e->getMessage());
}
?>