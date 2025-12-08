<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Client") {
    header("Location: pageLogin.php");
    exit;
}

$clientId = $_SESSION["user_id"];

$conn = new mysqli("localhost", "root", "", "testdb");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Véhicules du client
$vehStmt = $conn->prepare("SELECT id, marque, modele, immatriculation FROM vehicles WHERE user_id = ?");
$vehStmt->bind_param("i", $clientId);
$vehStmt->execute();
$vehicles = $vehStmt->get_result();

// Garages
$garStmt = $conn->prepare("SELECT id, garage_name, garage_location FROM users WHERE type = 'Garage'");
$garStmt->execute();
$garages = $garStmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau rendez-vous – Autofix</title>
    <link rel="stylesheet" href="style/client.css">
</head>
<body>

<h2>Nouveau rendez-vous</h2>

<form action="saveRdv.php" method="POST">

    <label>Véhicule</label>
    <select name="vehicle_id" required>
        <option value="">-- Sélectionner --</option>
        <?php while ($v = $vehicles->fetch_assoc()): ?>
            <option value="<?= $v['id'] ?>">
                <?= htmlspecialchars($v['marque'] . " " . $v['modele'] . " - " . $v['immatriculation']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Garage</label>
    <select name="garage_id" required>
        <option value="">-- Sélectionner --</option>
        <?php while ($g = $garages->fetch_assoc()): ?>
            <option value="<?= $g['id'] ?>">
                <?= htmlspecialchars($g['garage_name'] . " (" . $g['garage_location'] . ")") ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Date</label>
    <input type="date" name="date_rdv" required>

    <label>Heure</label>
    <input type="time" name="heure" required>

    <button type="submit">Créer le rendez-vous</button>
</form>

<button onclick="location.href='clientDashboard.php'">Retour</button>

</body>
</html>
