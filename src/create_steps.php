<?php
include 'connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Instructor") {
    header("Location: login.php");
    exit();
}

$assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Steps</title>
    <link rel="stylesheet" href="stepsstyle.css">
</head>
<body>
    <form action="steps_proc.php" method="post">
        <h2>Create Steps</h2>

        <div>
            <label for="remaining_percentage">Remaining Percentage:</label>
            <span id="remaining_percentage">100</span>% 
            <div id="percentage_warning" style="color: red;"></div>
        </div>

        <div id="steps-container">
            <div class="step" data-step-index="1">
                <label for="step_title_1">Step Title:</label>
                <input type="text" name="step_title_1" required>

                <label for="step_description_1">Step Description:</label>
                <textarea name="step_description_1" required></textarea>

                <label for="step_percentage_1">Step Percentage:</label>
                <input type="number" name="step_percentage_1" min="1" max="100" required>

                <label for="step_url_1">Step URL:</label>
                <input type="url" name="step_url_1">


                <button type="button" onclick="removeStep(1)">Remove Step</button>
            </div>
        </div>

        <button type="button" onclick="addStep()">Add Another Step</button>
        <br>

        <button type="submit" onclick="return validateTotalPercentage()">Submit Steps</button>

        <script src="functions.js"></script>
    </form>
</body>
</html>
