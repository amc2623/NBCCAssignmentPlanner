<!-- dashboard.php -->
<!-- Purpose: Redirect the user to the appropriate dashboard based on their role. -->
<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

// Determine the user's role
$userRole = $_SESSION['role']; // Assuming you store the role in the session

// Redirect based on user role
switch ($userRole) {
    case 'Admin':
        // Redirect to Admin Dashboard
        header("Location: ./dashboard_admin.php");
        exit();
    case 'Instructor':
        // Redirect to Instructor Dashboard
        header("Location: ./dashboard_instructor.php");
        exit();
    case 'Student':
        // Redirect to Student Dashboard
        header("Location: ./index.php");
        exit();
    default:
        // Unknown Role - Redirect to Main with Popup
        echo '<script type="text/javascript">
                alert("Unknown Role!");
                window.location.href = "./login.php";
              </script>';
        exit();
}


?>
