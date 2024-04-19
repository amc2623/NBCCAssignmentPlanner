<!-- create_steps.php -->
<!-- Purpose: Page to create steps for an assignment. -->
<?php
include './src/connect.php';
include './assets/includes/topBar.php';

session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION["role"] !== "Instructor")) {
    header('Location: ./login.php'); 
    exit();
}

// Get the assignment ID from the session
$assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Steps</title>
    <link rel="stylesheet" href="./assets/css/stepsStyle.css">
    <style>

        .red {
            color: red;
        }
        .yellow {
            color: yellow;
        }
        .green {
            color: green;
        }
    </style>
</head>
<body>
    <form id="steps-form" action="./src/proc-pages/steps_proc.php" method="post">
        
        <!-- Hidden input to store the assignment ID -->
        <h2>Create Steps</h2>

        <div>
            <label for="remaining_percentage">Remaining Percentage:</label>
            <span id="remaining_percentage" >100 </span>% 
            <div id="percentage_warning" style="color: red;"></div><br>
        </div>

        <div id="steps-container">
            <div class="step" data-step-index="1">
                <label for="step_number_1">Step Number:</label>
                <input type="text" name="step_number_1" value="1">
                
                <label for="step_title_1">Step Title:</label>
                <input type="text" name="step_title_1" required>

                <label for="step_description_1">Step Description:</label>
                <textarea name="step_description_1" required></textarea>

                <label for="step_percentage_1">Step Percentage:</label>
                <input type="text" name="step_percentage_1" min="1" max="100" required>

                <label for="step_url_1">Step URL:</label>
                <input type="url" name="step_url_1">

                <!-- Pass the step index to the removeStep function -->
                <button type="button" onclick="removeStep(this)">Remove Step</button><br><br><hr>
            </div>
        </div></BR>
        <button type="button" onclick="addStep()">Add Another Step</button></BR></BR>

        <button type="submit" onclick="return validateTotalPercentage()">Submit Steps</button></BR></BR>
        
        <!-- Back button to navigate back to create_assignment.php -->
        <button type="button" onclick="goBack()">Back</button>

        <script src="./assets/js/functions.js"></script>
        <script>
            // Call the function initially to set the color
            updatePercentageColor();
        </script>
    </form>
</body>

<footer>
        <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>
</html>



