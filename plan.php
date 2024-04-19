<!-- plan.php -->
<!-- Purpose: Display the assignment plan for a user. -->
<?php include './assets/includes/topBar.php'; 
    session_start();
    include './src/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assignment Plan</title>
        <link rel="stylesheet" href="./assets/css/previewStyle.css">
    </head>

    <body>
        <div class="container">
            <?php
                if (isset($_SESSION['assignment_id']) && $_SESSION['start_date'] && $_SESSION['end_date']) {
                    // Get the assignment ID from the session
                    $assignment_id = $_SESSION['assignment_id'];

                    // Get the assignment from the database
                    $stmt_assignment = $conn->prepare("CALL getAssignmentDetails(?)");
                    $stmt_assignment->bind_param("i", $assignment_id);
                    $stmt_assignment->execute();
                    $result_assignment = $stmt_assignment->get_result();

                    // If there is an assignment, display the details
                    if ($result_assignment->num_rows > 0) {
                        $row_assignment = $result_assignment->fetch_assoc();
                        echo "<label for='assignment_title'>Assignment: </label><br><br>";
                        echo "<input type='text' id='assignment_title' name='assignment_title' value='" . $row_assignment['title'] . "' readonly><br><br>";
                        echo "<img src='" . $row_assignment['thumbnail_path'] . "' alt='Thumbnail'><br><br>";
                        echo "<label for='assignment_description'>Description: </label><br><br>";
                        echo "<textarea id='assignment_description' name='assignment_description' rows='10' cols='40' readonly style='resize: none;'>" . $row_assignment['description'] . "</textarea><br><br>";
                        echo "<label for='assignment_deadline'>Deadline: </label><br><br>";
                        echo "<input type='text' id='assignment_deadline' name='assignment_deadline' value='" . $row_assignment['deadline'] . "' readonly><br>";
                
                        // Display the start and end dates
                        echo "<p>Start Date: " . $_SESSION['start_date'] . "</p>";
                        echo "<p>End Date: " . $_SESSION['end_date'] . "</p>";
                        
                        // Calculate the total hours between the start and end dates
                        $start_date = new DateTime($_SESSION['start_date']);
                        $end_date = new DateTime($_SESSION['end_date']);
                        $interval = $start_date->diff($end_date);
                        $total_hours = $interval->days * 24;
                        echo "<p>Total Hours: " . $total_hours . "</p>";


                         } else {
                        // Otherwise, display an error message
                        echo "<p>Assignment not found.</p>";
                    }

                    $stmt_assignment->close();

                    // Get the steps for the assignment from the database
                    $stmt_steps = $conn->prepare("CALL getAssignmentSteps(?)");
                    $stmt_steps->bind_param("i", $assignment_id);
                    $stmt_steps->execute();
                    $result_steps = $stmt_steps->get_result();

                    // If there are steps, display them
                    if ($result_steps->num_rows > 0) {
                        echo "<h2>Assignment Steps</h2>";
                        while ($row_step = $result_steps->fetch_assoc()) {
                            // Display each step with a breakdown of how much of the allocated time it should take
                            echo "<div>";
                            echo "<h3>Step " . $row_step['step_number'] . ": " . $row_step['title'] . "</h3>";

                            echo "<p>Time Required: " . ($row_step['total_hours'] * $row_step['percentage'] / 100) . " hours</p>";
                            echo "<p>" . $row_step['description'] . "</p>";
                            echo "</div>";
                        }
                    } else {
                        // Otherwise, display a message
                        echo "<p>No steps found for this assignment.</p>";
                    }

                    $stmt_steps->close();
                } else {
                    // If the session variables are not set, display an error message
                    echo "<p>Assignment not found.</p>";
                }

                $conn->close();

            ?>
        </div>
    </body>

    <footer>
        <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
    </footer>
    
</html>
