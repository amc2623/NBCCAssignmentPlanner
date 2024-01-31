<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Admin") {
    header("Location: login.php");
    exit();
}
?>

you are on the admin dashboard<br>

<a href="logout.php">Logout</a>