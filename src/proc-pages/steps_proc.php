<!-- steps_proc.php -->
<!-- Purpose: Proc to insert steps for an assignment. -->
<?php
include '../connect.php';

session_start();

$assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use a prepared statement to call the InsertStep stored procedure
    $stmt = $conn->prepare("CALL insertStep(?, ?, ?, ?, ?, ?)");

    // Initialize step counter
    $step_number = 1;

    // Loop through the POST data to find step values
    foreach ($_POST as $key => $value) {
        // Check if the key starts with 'step_title_'
        if (strpos($key, 'step_title_') === 0) {
            $step_title = $_POST[$key];
            $step_description = $_POST['step_description_' . $step_number];
            $step_percentage = $_POST['step_percentage_' . $step_number];
            $step_url = $_POST['step_url_' . $step_number];

            // Bind parameters and execute the statement
            $stmt->bind_param("issssi", $assignment_id, $step_title, $step_description, $step_percentage, $step_url, $step_number);
            $stmt->execute();

            // Check for errors
            if ($stmt->error) {
                echo "Error inserting step $step_number: " . $stmt->error . "<br>";
            } else {
                echo "Step $step_number inserted successfully!<br>";
            }

            // Increment step counter
            $step_number++;
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: ../../assignment_preview.php");
    exit();
} else {
    header("Location: ../../create_steps.php");
    exit();
}
?>
