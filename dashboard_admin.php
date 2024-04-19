<!-- dashboard_admin.php -->
<!-- Purpose: Admin dashboard to manage assignments. -->
<?php
include './assets/includes/topBar.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Admin") {
    header("Location: ./login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plan your NBCC assignments!" />
    <link rel="stylesheet" href="./assets/css/dashboardStyle.css">
    <link rel="icon" href="./assets/img/logos/favicon.png">
    <title>Admin Dashboard</title>
</head>

<body>
<form>
    <!-- links for dashboard -->
    <h2>Admin Dashboard</h2>
    <a href="./manage_users.php">Manage Users</a><br>
    <a href="./manage_courses.php">Manage Courses</a><br>
    <a href="./logout.php">Logout</a>
</form>
</body>
  <footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
  </footer>
</html>