<!-- Purpose: create a checklist based off of the assignment steps -->
<?php
session_start();
include './assets/includes/topBar.php';
include './src/connect.php'; 

if(isset($_GET['assignmentId'])) {
    $assignmentId = $_SESSION['assignment_id'];
//is the id in the session
} else {
    echo "Assignment ID not found. Please go back to the assignment page.";
    exit;
}

//pull assignments from the database
$steps = [];
if ($stmt = $conn->prepare("CALL getAssignmentSteps(?)")) {
    $stmt->bind_param("i", $assignmentId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $steps[] = $row;
    }

    $stmt->close();
    $conn->next_result(); //clear previous results
} else {
    echo "Failed to prepare the query: (" . $conn->errno . ") " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Checklist</title>
    <link rel="stylesheet" href="./assets/css/checklistStyle.css"> 
</head>
<body>
    <!-- container for checklist -->
    <div class="container">
        <h1>Create Checklist for Assignment</h1></BR>
        <form action="./src/proc-pages/checklist_proc.php" method="post">
            <!-- loop through steps and display them -->
            <?php foreach ($steps as $step): ?>
                <div class='step'>
                    <h3><?php echo htmlspecialchars($step['title']); ?></h3>
                    <p><?php echo htmlspecialchars($step['description']); ?></p>
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <input type='text' name='steps[<?php echo $step['steps_id']; ?>][substeps][]' placeholder='Substep' <?php echo $i; ?>'>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="assignmentId" value="<?php echo htmlspecialchars($assignmentId); ?>">
            <button type="submit" name="submit" onclick="return confirmPrompt()">Generate Checklist</button>
            </form>
            <button onclick="window.location.href='<?php echo $_SERVER['HTTP_REFERER']; ?>'">Go Back</button>       
    </div>
</body>
<footer>
    <small>©
        <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.
    </small>
</footer>
</html>
