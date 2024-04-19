<!-- assignment_preview.php -->
<!-- Purpose: Display the details of an assignment. -->
<?php include './assets/includes/topBar.php'; 
    session_start();
    include './src/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Preview</title>
    <link rel="stylesheet" href="./assets/css/previewStyle.css">
</head>
<body>
    <div class="container">
        

        <form>
            <div class="form-group">
            <h1>Assignment Preview</h1></BR>
                <?php

                
                $assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;

                        // Get the assignment from the database
                        $stmt_assignment = $conn->prepare("CALL getAssignmentDetails(?)");
                        $stmt_assignment->bind_param("i", $assignment_id);
                        $stmt_assignment->execute();
                        $result_assignment = $stmt_assignment->get_result();

                        // If there is an assignment, display the details
                        if ($result_assignment->num_rows > 0) {
                            $row_assignment = $result_assignment->fetch_assoc();
                            echo "<img src='" . $row_assignment['thumbnail_path'] . "' alt='Thumbnail'><br><br>";
                            echo "<label for='assignment_title'>Assignment: </label><br><br>";
                            echo "<input type='text' id='assignment_title' name='assignment_title' value='" . htmlspecialchars($row_assignment["title"]) . "' readonly><br><br>";
                            echo "<label for='assignment_description'>Description: </label><br><br>";
                            echo "<textarea id='assignment_description' name='assignment_description' rows='4' cols='80' readonly style='resize: none;'>" . $row_assignment['description'] . "</textarea><br><br>";
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
                            echo '<hr>';
                            echo "<h2>Assignment Steps</h2>";
                            while ($row_step = $result_steps->fetch_assoc()) {
                                echo '<div class="previewclass">';
                                echo "<h3 class='step-header'>Step " . $row_step['step_number'] . ":  " . $row_step['title'] . "</h3>";
                                echo "<div id='step-content'class='step-content'>";
                                echo "<div class='step-content" . $row_step['step_number'] . "'>"; // Add a class for each step content based on step number                       
                                echo "</div>";
                                echo "<div><b>Percent: </b>" . $row_step['percentage'] . "%</b></div>";
                                echo "<div></BR> " . $row_step['description'] . "</div>";
                                if (!empty($row_step['url'])) {
                                    echo "<p><a href='" . $row_step['url'] . "'>" . $row_step['url'] . "</a></p>";
                                }
                                echo "</div>";
                                echo "</div></BR>";
                            }
                        } else {
                            // Otherwise, display a message
                            echo "<p>No steps found for this assignment.</p>";
                        }

                        $stmt_steps->close();
                        $conn->close();
                    ?>
                </div>
            </form>
                    </BR>
            <div class="buttons">
                <button onclick="window.location.href='./checklist.php?assignmentId=<?php echo $assignment_id; ?>'">Create Checklist</button></BR></BR>
                <button onclick="window.location.href='./edit_assignment.php'">Edit Assignment</button><br><br>

                <button onclick="window.location.href='./dashboard.php'">Back to Dashboard</button>
                
            </div>
        </div>
    </body>
    <footer>
        <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>
</html>
