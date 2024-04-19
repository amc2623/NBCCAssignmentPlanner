<!-- verify_proc.php -->
<!-- Purpose: Proc to verify an assignment. -->
<?php

include '../connect.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION["role"] !== "Admin")) {
    header("Location: ./login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "<p>Assignment ID not provided in the URL.</p>";
    exit();
}

// Retrieve assignment ID from URL parameter
$assignment_id = $_GET['id'];

$stmt_verify_assignment = $conn->prepare("CALL verifyAssignment(?)");
$stmt_verify_assignment->bind_param("i", $assignment_id);
$stmt_verify_assignment->execute();
$stmt_verify_assignment->close();

$conn->close();
header("Location: ../../manage_assignments.php");
exit();
?>


