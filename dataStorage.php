<?php
session_start();

$conn = new mysqli("localhost", "root", "", "testdb");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = $_POST["fullName"];
    $email = $_POST["email"];
    $pass = $_POST["password"];
    $type = $_POST["type"];

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "email_exists";
        exit;
    }
    $password = password_hash($pass, PASSWORD_DEFAULT);

    $insert = $conn->prepare("INSERT INTO users (fullName, email, password, type) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $fullName, $email, $password, $type);

    if ($insert->execute()) {

        $userId = $insert->insert_id;

        // Si c'est un garage → il doit compléter ses informations
        if ($type === "Garage") {
            $_SESSION["pending_garage"] = $userId;
            echo "garage_incomplete";
        } else {
            echo "success";
        }

    } else {
        echo "error";
    }
}
?>
