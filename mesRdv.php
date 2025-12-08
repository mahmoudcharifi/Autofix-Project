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

$rdvStmt = $conn->prepare("
    SELECT  r.id,
            r.date_rdv,
            r.heure,
            r.status,
            v.marque,
            v.modele,
            v.immatriculation,
            g.garage_name
    FROM rendezvous r
    JOIN vehicles v ON r.vehicle_id = v.id
    JOIN users g ON r.garage_id = g.id
    WHERE r.client_id = ?
    ORDER BY r.date_rdv DESC, r.heure DESC
");
$rdvStmt->bind_param("i", $clientId);
$rdvStmt->execute();
$result = $rdvStmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes rendez-vous – Autofix</title>
    <link rel="stylesheet" href="style/client.css">
</head>
<body>

<h2>Mes rendez-vous</h2>

<?php if ($result->num_rows === 0): ?>
    <p>Aucun rendez-vous pour le moment.</p>
<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="rdvCard">
            <h3><?= htmlspecialchars($row['marque'] . " " . $row['modele']) ?></h3>
            <p><strong>Garage :</strong> <?= htmlspecialchars($row['garage_name']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($row['date_rdv']) ?></p>
            <p><strong>Heure :</strong> <?= htmlspecialchars(substr($row['heure'], 0, 5)) ?></p>
            <p><strong>Statut :</strong> <?= htmlspecialchars($row['status']) ?></p>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<button onclick="location.href='clientDashboard.php'">Retour</button>

</body>
</html>
