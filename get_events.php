<?php
// Connexion à la base de données
include('config/db.php');

// Requête pour récupérer les événements
$query = "SELECT title, start_date, end_date FROM projects";
$stmt = $pdo->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les données sous forme de JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
