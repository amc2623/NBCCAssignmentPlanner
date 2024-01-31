<?php
include 'connect.php';

session_start();

$assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use a prepared statement to call the InsertStep stored procedure
    $stmt = $conn->prepare("CALL InsertStep(?, ?, ?, ?, ?)");

    for ($i = 1; isset($_POST['step_title_' . $i]); $i++) {
        $step_title = $_POST['step_title_' . $i];
        $step_description = $_POST['step_description_' . $i];
        $step_percentage = $_POST['step_percentage_' . $i];
        $step_url = $_POST['step_url_' . $i];

        // Bind parameters and execute the statement
        $stmt->bind_param("issss", $assignment_id, $step_title, $step_description, $step_percentage, $step_url);
        $stmt->execute();

        // Check for errors
        if ($stmt->error) {
            echo "Error inserting step $i: " . $stmt->error . "<br>";
        } else {
            echo "Step $i inserted successfully!<br>";
        }
    }

    $stmt->close();

    $conn->close();

    header("Location: assignment_preview.php");
    exit();
} else {
    header("Location: create_steps.php");
    exit();
}
?>
