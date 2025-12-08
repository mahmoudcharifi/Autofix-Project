<?php
session_start();

// Sécurité : uniquement les clients
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "Client") {
    header("Location: pageLogin.php");
    exit;
}

$clientId = $_SESSION["user_id"];

$conn = new mysqli("localhost", "root", "", "testdb");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

/* 1) Récupérer les véhicules du client */
$vehStmt = $conn->prepare("SELECT id, marque, modele, immatriculation FROM vehicles WHERE user_id = ?");
$vehStmt->bind_param("i", $clientId);
$vehStmt->execute();
$vehiclesResult = $vehStmt->get_result();

/* 2) Récupérer les rendez-vous du client (avec véhicule + garage) */
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
    <title>Autofix – Espace client</title>
    <script defer src="clientDashboard.js"></script>
    <link rel="stylesheet" href="style/client.css">
    <style>
        /* ------------------------------- */
/*      AUTOFiX – Dashboard Client */
/* ------------------------------- */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Inter", sans-serif;
}

body {
    background: #f5f7fa;
    color: #333;
}

/* ------------------------------- */
/*             TOP BAR             */
/* ------------------------------- */

.topbar {
    width: 100%;
    background: #0b1e39;
    padding: 15px 30px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.topbar .logo {
    font-size: 22px;
    font-weight: 700;
}

.logout {
    background: #cc0000;
    padding: 8px 16px;
    border-radius: 6px;
    color: white;
    border: none;
    cursor: pointer;
}
.logout:hover {
    background: #a30000;
}

/* ------------------------------- */
/*           NAV TABS              */
/* ------------------------------- */

.tabs {
    display: flex;
    justify-content: center;
    background: white;
    border-bottom: 1px solid #ddd;
}

.tabButton {
    padding: 12px 25px;
    cursor: pointer;
    border: none;
    background: transparent;
    font-size: 15px;
    transition: 0.2s;
}

.tabButton:hover {
    background: #eef1f5;
}

.tabButton.active {
    border-bottom: 3px solid #0b5cff;
    font-weight: bold;
    color: #0b5cff;
}

/* ------------------------------- */
/*          GLOBAL LAYOUT          */
/* ------------------------------- */

main {
    width: 100%;
    max-width: 960px;
    margin: 30px auto;
    padding: 0 20px;
}

.tabContent {
    display: block;
}

.hidden {
    display: none !important;
}

.title {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 20px;
}

/* ------------------------------- */
/*     SECTION – MES VÉHICULES     */
/* ------------------------------- */

.sectionHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.vehicleList {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.vehicleCard {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.08);
}

.vehicleCard h3 {
    font-size: 18px;
    margin-bottom: 8px;
}

.primary {
    background: #0b5cff;
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    color: white;
    cursor: pointer;
}
.primary:hover {
    background: #0849c7;
}

/* ------------------------------- */
/*       SECTION – EMPTY BOX       */
/* ------------------------------- */

.emptyBox {
    background: #fff;
    text-align: center;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.1);
}

.emptyBox img {
    width: 70px;
    opacity: 0.7;
}

/* ------------------------------- */
/*     NOUVEAU RENDEZ-VOUS FORM    */
/* ------------------------------- */

.rdvForm {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.08);
    display: grid;
    gap: 15px;
    max-width: 450px;
}

.rdvForm label {
    font-weight: 600;
}

.rdvForm input, .rdvForm select {
    padding: 10px;
    width: 100%;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* ------------------------------- */
/*     SECTION – MES RENDEZ-VOUS   */
/* ------------------------------- */

.rdvList {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

.rdvCard {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 7px rgba(0,0,0,0.08);
}

.rdvCard h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

/* ------------------------------- */
/*         STATUS COLORS           */
/* ------------------------------- */

.status {
    padding: 4px 10px;
    font-size: 13px;
    border-radius: 8px;
    font-weight: bold;
    color: white;
}

.status.en-attente {
    background: #f1c40f;
}

.status.validé {
    background: #2ecc71;
}

.status.refusé {
    background: #e74c3c;
}

    </style>
</head>
<body>

<header class="topbar">
    <div class="logo">Autofix</div>
    <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
</header>

<nav class="tabs">
    <button class="tabButton active" data-target="vehicles">Mes véhicules</button>
    <button class="tabButton" data-target="newRdv">Nouveau rendez-vous</button>
    <button class="tabButton" data-target="rdv">Mes rendez-vous</button>
</nav>

<main>

    <!-- 🟦 Onglet : MES VÉHICULES -->
    <section id="vehicles" class="tabContent">
        <div class="sectionHeader">
            <h2 class="title">Mes véhicules</h2>
            <button class="primary" onclick="location.href='addVehicle.php'">
                + Ajouter un véhicule
            </button>
        </div>

        <?php if ($vehiclesResult->num_rows === 0): ?>
            <div class="emptyBox">
                <img src="img/car.png" alt="">
                <p>Aucun véhicule enregistré</p>
            </div>
        <?php else: ?>
            <div class="vehicleList">
                <?php while ($v = $vehiclesResult->fetch_assoc()): ?>
                    <article class="vehicleCard">
                        <h3><?= htmlspecialchars($v['marque'] . " " . $v['modele']) ?></h3>
                        <p><strong>Immatriculation :</strong>
                            <?= htmlspecialchars($v['immatriculation']) ?>
                        </p>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- 🟦 Onglet : NOUVEAU RENDEZ-VOUS -->
    <section id="newRdv" class="tabContent hidden">
        <h2 class="title">Nouveau rendez-vous</h2>

        <?php
        // Véhicules pour le select
        $vehStmt2 = $conn->prepare("SELECT id, marque, modele, immatriculation FROM vehicles WHERE user_id = ?");
        $vehStmt2->bind_param("i", $clientId);
        $vehStmt2->execute();
        $vehForSelect = $vehStmt2->get_result();

        // Garages pour le select
        $garStmt = $conn->prepare("SELECT id, garage_name, garage_location FROM users WHERE type = 'Garage'");
        $garStmt->execute();
        $garages = $garStmt->get_result();
        ?>

        <form action="saveRdv.php" method="POST" class="rdvForm">
            <label>Véhicule</label>
            <select name="vehicle_id" required>
                <option value="">-- Sélectionner --</option>
                <?php while ($v = $vehForSelect->fetch_assoc()): ?>
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

            <button type="submit" class="primary">Créer le rendez-vous</button>
        </form>
    </section>

    <!-- 🟦 Onglet : MES RENDEZ-VOUS -->
    <section id="rdv" class="tabContent hidden">
        <h2 class="title">Mes rendez-vous</h2>

        <?php if ($result->num_rows === 0): ?>

            <div class="emptyBox">
                <img src="img/rdv.png" alt="">
                <p>Aucun rendez-vous pour le moment</p>
            </div>

        <?php else: ?>

            <div class="rdvList">

                <?php while ($row = $result->fetch_assoc()): ?>
                    <article class="rdvCard">
                        <h3><?= htmlspecialchars($row['marque'] . " " . $row['modele']) ?></h3>

                        <p><strong>Garage :</strong>
                            <?= htmlspecialchars($row['garage_name']) ?>
                        </p>
                        <p><strong>Date :</strong>
                            <?= htmlspecialchars($row['date_rdv']) ?>
                        </p>
                        <p><strong>Heure :</strong>
                            <?= htmlspecialchars(substr($row['heure'], 0, 5)) ?>
                        </p>

                        <p><strong>Statut :</strong>
                            <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </p>
                    </article>
                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
