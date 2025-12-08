<?php
session_start();
if (!isset($_SESSION["pending_garage"])) {
    header("Location: pageLogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <form action="saveGarageInfo.php" method="POST" id="loginForm">
         <h2>Comlete info</h2>
        <input type="text" name="garage_name" placeholder="Nom du garage" required id="Email">
        <input type="text" name="garage_location" placeholder="Localisation" required id="PassCode">
        <button type="submit" id="SubmitButton">Valider</button>
    </form>
</body>
</html>

