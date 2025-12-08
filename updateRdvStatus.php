<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Garage") {
    header("Location: pageLogin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "testdb");

$rdvId = $_POST['rdvId'];
$newStatus = $_POST['newStatus'];

$sql = $conn->prepare("UPDATE rendezvous SET status = ? WHERE id = ?");
$sql->bind_param("si", $newStatus, $rdvId);

if ($sql->execute()) {
    header("Location: garageDashboard.php");
    exit;
}

echo "Erreur lors de la mise à jour";
