<!-- edit_proc.php -->
<!-- Purpose: Proc to edit an assignment and its steps. -->
<?php
// Include database connection code
include("../connect.php");

// Start the session
session_start();

// Check if assignment ID is set in session
if (!isset($_SESSION['assignment_id'])) {
    echo "<p>Assignment ID not set.</p>";
    exit();
}

// Retrieve assignment ID from session
$assignment_id = $_SESSION['assignment_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle assignment data
    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['thumbnail_path']) && isset($_POST['course_id'])) {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $thumbnail_path = $_POST["thumbnail_path"];
        $course_id = $_POST["course_id"];

        // Prepare and execute the editAssignment stored procedure
        $stmt_edit_assignment = $conn->prepare("CALL editAssignment(?, ?, ?, ?, ?)");
        $stmt_edit_assignment->bind_param("issss", $assignment_id, $title, $description, $thumbnail_path, $course_id);
        $stmt_edit_assignment->execute();
        $stmt_edit_assignment->close();
    }

    // Handle steps data
    foreach ($_POST as $key => $value) {
        // Check if the key is related to step data
        if (strpos($key, 'step_title_') !== false) {
            // Extract step ID from the key
            $step_id = substr($key, strlen('step_title_'));

            // Retrieve step data from corresponding fields
            $step_number = $_POST["step_number_$step_id"];
            $step_title = $_POST["step_title_$step_id"];
            $step_description = $_POST["step_description_$step_id"];
            $step_url = $_POST["step_url_$step_id"]; // Retrieve URL
            $step_percentage = $_POST["step_percentage_$step_id"]; // Retrieve percentage
    
            // Prepare and execute the editStep stored procedure
            $stmt_edit_step = $conn->prepare("CALL editStep(?, ?, ?, ?, ?, ?)");
            $stmt_edit_step->bind_param("iissss", $step_id, $step_number, $step_title, $step_description, $step_url, $step_percentage);
            $stmt_edit_step->execute();
            $stmt_edit_step->close();
        }
    }

    // Check if a new step is added
    if (isset($_POST['new_step_title']) && isset($_POST['new_step_description']) && isset($_POST['new_step_percentage'])) {
        $new_step_title = $_POST['new_step_title'];
        $new_step_description = $_POST['new_step_description'];
        $new_step_percentage = $_POST['new_step_percentage'];

        // Prepare and execute the addNewStep stored procedure
        $stmt_add_new_step = $conn->prepare("CALL addNewStep(?, ?, ?, ?)");
        $stmt_add_new_step->bind_param("issi", $assignment_id, $new_step_title, $new_step_description, $new_step_percentage);
        $stmt_add_new_step->execute();
        $stmt_add_new_step->close();
    }

    $conn->close();

    // Redirect to assignment preview page or another appropriate page
    header("Location: ../../manage_assignments.php");
    exit();
}
?>
