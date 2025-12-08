<?php
session_start();

if (!isset($_SESSION["pending_garage"])) {
    header("Location: pageLogin.php");
    exit;
}

$garageId = $_SESSION["pending_garage"];

$conn = new mysqli("localhost", "root", "", "testdb");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["garage_name"];
    $loc = $_POST["garage_location"];

    $update = $conn->prepare("UPDATE users SET garage_name = ?, garage_location = ? WHERE id = ?");
    $update->bind_param("ssi", $name, $loc, $garageId);

    if ($update->execute()) {

        // On supprime la variable temporaire
        unset($_SESSION["pending_garage"]);

        // GARAGISTE PRÊT → REDIRECTION VERS LOGIN
        header("Location: pageLogin.php");
        exit;

    } else {
        echo "error";
    }
}
?>
