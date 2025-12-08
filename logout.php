<?php
session_start();

// Supprimer toutes les sessions
session_unset();
session_destroy();

// Empêcher revenir avec le bouton Retour
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

header("Location: pageLogin.php");
exit;
