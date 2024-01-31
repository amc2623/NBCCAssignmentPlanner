<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Instructor") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
</head>
<body>
    <a href="create_assignment.php">Create Assignment</a>
    <a href="logout.php">Logout</a>
</body>
</html>
