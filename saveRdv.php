<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Client") {
    header("Location: pageLogin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "testdb");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $clientId  = $_SESSION["user_id"];
    $vehicleId = (int)$_POST["vehicle_id"];
    $garageId  = (int)$_POST["garage_id"];
    $date      = $_POST["date_rdv"];
    $heure     = $_POST["heure"];

    if ($vehicleId && $garageId && $date !== "" && $heure !== "") {
        $stmt = $conn->prepare("
            INSERT INTO rendezvous (client_id, garage_id, vehicle_id, date_rdv, heure, status)
            VALUES (?, ?, ?, ?, ?, 'En attente')
        ");
        $stmt->bind_param("iiiss", $clientId, $garageId, $vehicleId, $date, $heure);

        if (!$stmt->execute()) {
            die("Erreur lors de la création du rendez-vous : " . $conn->error);
        }
    }

    header("Location: clientDashboard.php?tab=rdv");
    exit;
}
?>
