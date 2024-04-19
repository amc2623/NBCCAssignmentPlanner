<?php
session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    // Ensure $_SESSION['user_id'] is set
    $user_id = $_SESSION['user_id'];

    // Retrieve assignment ID from the URL parameters
    $assignment_id = isset($_GET['assignment_id']) ? $_GET['assignment_id'] : null;

    // Ensure other form data are set
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

    if ($assignment_id && $start_date && $end_date) {  
        // Convert dates to MySQL format
        $mysql_start_date = date('Y-m-d H:i:s', strtotime($start_date));
        $mysql_end_date = date('Y-m-d H:i:s', strtotime($end_date));

        // Insert assignment plan into the database
        $stmt = $conn->prepare("CALL assignmentplanner.insertAssignmentPlan(?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $assignment_id, $mysql_start_date, $mysql_end_date);
        if (!$stmt->execute()) {
            // Handle errors
            echo "Error creating assignment plan: " . $stmt->error;
            $stmt->close();
            $conn->close();
            exit();
        } else {
            // Success message or redirection
            header("Location: {$_SERVER['HTTP_REFERER']}?success=true&assignment_id={$assignment_id}&start_date={$start_date}&end_date={$end_date}");
            exit(); 
        }
    } else {
        // Missing data
        echo "Missing data for creating assignment plan";
    }
} else {
    // Redirect or handle case where user is not logged in
    header("Location: {$_SERVER['HTTP_REFERER']}?success=true&assignment_id={$assignment_id}");
    exit();
}