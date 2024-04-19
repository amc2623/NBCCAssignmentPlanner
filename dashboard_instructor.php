<!-- dashboard_instructor.php -->
<!-- Purpose: Instructor dashboard to manage assignments. -->
<?php include './assets/includes/topBar.php'; ?>
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Instructor") {
    header("Location: ./login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/dashboardStyle.css">
    <title>Instructor Dashboard</title>
</head>
<body>
<form>
    <h2>Instructor Dashboard</h2>
    <a href="./create_assignment.php">Create Assignment</a><br>
    <a href="./manage_assignments.php">Manage Assignments</a><br>
    <a href="./logout.php">Logout</a>
</form>
</body>

  <footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
  </footer>
</html>