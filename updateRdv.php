<?php
session_start();
require("db.php");

if (!isset($_SESSION["id"])) {
    header("Location: pageLogin.php");
    exit();
}

$rdv_id = $_POST["rdv_id"];
$action = $_POST["action"];

$sql = "UPDATE rendezvous SET status='$action' WHERE id=$rdv_id";
$conn->query($sql);

header("Location: garageDashboard.php");
exit();
?>
