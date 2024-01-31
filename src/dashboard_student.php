<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Student") {
    header("Location: login.php");
    exit();
}
?>

you are on the student dashboard<br>

<a href="logout.php">Logout</a>