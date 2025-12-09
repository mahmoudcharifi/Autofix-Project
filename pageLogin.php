<?php 
session_start();
$conn = new mysqli("localhost", "root", "", "testdb");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<form action="pageLogin.php" method="POST" id="loginForm">
     <h2>Se connecter</h2>

    <label for="Email">Email :</label>
    <input type="text" placeholder="...........@gmail.com" name="email" id="Email">

    <label for="Password">Mot de passe</label>
    <input type="password" id="PassCode" name="password" placeholder="**********">

    <button type="submit" id="SubmitButton">Connecter</button>

    <a href="pageRegistre.php" class="connectionLink">Register</a>
</form>


<?php
session_start();
$conn = new mysqli("localhost", "root", "", "testdb");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $pass = $_POST["password"];

    $sql = $conn->prepare("SELECT id, password, type FROM users WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $sql->store_result();

    if ($sql->num_rows === 1) {

        $sql->bind_result($id, $hashedPass, $type);
        $sql->fetch();

        if (password_verify($pass, $hashedPass)) {

            $_SESSION["user_id"] = $id;
            $_SESSION["user_type"] = $type;

            if ($type === "Garage") {
                header("Location: garageDashboard.php");
                exit;
            } else if($type === "Garage") {
                header("Location: garageDashboard.php");
                exit;
            }else{
                header("Location: clientDashboard.php");
                exit;
            }

        } else {
            echo "<br><p style='color:red;'>Mot de passe incorrect</p>";
        }

    } else {
        echo "<br><p style='color:red;'>Email introuvable</p>";
    }
}
?>


</body>
</html>
