<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Garage") {
    header("Location: pageLogin.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "testdb");
$garageId = $_SESSION["user_id"];

// Récupération des RDVs du garage
$sql = "
SELECT r.id, r.date_rdv, r.heure, r.status,
       v.marque, v.modele, v.immatriculation,
       u.fullName AS client_name
FROM rendezvous r
JOIN vehicles v ON r.vehicle_id = v.id
JOIN users u ON r.client_id = u.id
WHERE r.garage_id = ?
ORDER BY r.date_rdv ASC, r.heure ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $garageId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Garagiste</title>
    <link rel="stylesheet" href="style/garage.css">
</head>

<body>

<h2 class="title">Bienvenue Garagiste</h2>
<a href="logout.php" class="logout">Déconnexion</a>

<section class="rdvSection">

    <h3>Rendez-vous reçus</h3>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty">
            <img src="img/rdv.png">
            <p>Aucun rendez-vous reçu</p>
        </div>
    <?php else: ?>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="rdvCard">

                <h4><?= $row['marque'] . " " . $row['modele'] ?></h4>

                <p><strong>Client :</strong> <?= $row['client_name'] ?></p>
                <p><strong>Immatriculation :</strong> <?= $row['immatriculation'] ?></p>
                <p><strong>Date :</strong> <?= $row['date_rdv'] ?></p>
                <p><strong>Heure :</strong> <?= $row['heure'] ?></p>

                <p><strong>Statut :</strong>
                    <span class="status <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span>
                </p>

                <form action="updateRdvStatus.php" method="POST">
                    <input type="hidden" name="rdvId" value="<?= $row['id'] ?>">

                    <select name="newStatus" required>
                        <option value="">Changer statut…</option>
                        <option value="Valide">Valider</option>
                        <option value="En attente">En attente</option>
                        <option value="Refuse">Refuser</option>
                    </select>

                    <button type="submit" class="btnUpdate">Confirmer</button>
                </form>

            </div>
        <?php endwhile; ?>

    <?php endif; ?>

</section>

</body>
</html>
