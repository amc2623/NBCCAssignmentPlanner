<!-- logout.php -->
<!-- Purpose: Log the user out and redirect them to the login page. -->
<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: ./login.php?logout=1");
exit();
?>