<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: pageLogin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "testdb");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marque = $_POST["marque"];
    $modele = $_POST["modele"];
    $immat  = $_POST["immatriculation"];
    $client_id = $_SESSION["user_id"];

    $sql = $conn->prepare("
        INSERT INTO vehicles (user_id, marque, modele, immatriculation)
        VALUES (?, ?, ?, ?)
    ");
    $sql->bind_param("isss", $client_id, $marque, $modele, $immat);

    if ($sql->execute()) {
        header("Location: clientDashboard.php?tab=vehicles");
        exit;
    } else {
        echo "Erreur SQL : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un véhicule – Autofix</title>
    <link rel="stylesheet" href="style/client.css">
</head>
<body>

<h2>Ajouter un véhicule</h2>

<form method="POST">
    <input type="text" name="marque" placeholder="Marque" required>
    <input type="text" name="modele" placeholder="Modèle" required>
    <input type="text" name="immatriculation" placeholder="Immatriculation" required>
    <button type="submit">Ajouter</button>
</form>

<button onclick="location.href='clientDashboard.php'">Retour</button>

</body>
</html>
