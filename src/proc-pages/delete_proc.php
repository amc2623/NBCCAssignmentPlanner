<!-- delete_proc.php -->
<!-- Purpose: Proc to delete an assignment from the database. -->
<?php

require_once '../connect.php';

session_start();

// Check if the assignment ID is set and is a numeric value
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ID.";
    exit; // Stop the script if the ID is not valid
}

$assignment_id = $_GET['id']; // Assign the assignment ID from the URL parameter

// Assuming $conn is your database connection
$stmt = $conn->prepare("CALL removeAssignment(?)");
$stmt->bind_param("i", $assignment_id); // 'i' specifies the variable type is integer

if ($stmt->execute()) {
    echo "Record deleted successfully";
    $stmt->close();
    $conn->close();
    header("Location: ../../manage_assignments.php"); // Redirect user to the assignment viewing page
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
