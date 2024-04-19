<!-- Purpose: Fetch and display the timeline of an assignment. -->
<?php
include '../src/connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Timeline</title>
    <link rel="stylesheet" href="../assets/css/timelineStyle.css">
</head>

<body>
<div id="container" style="height:420px">
<?php
if (isset($_GET['assignment_id'])) {
    //assignment id from url
    $assignment_id = $_GET['assignment_id'];

    //fetch assignment details
    $stmt_assignment = $conn->prepare("CALL getAssignmentDetails(?)");
    $stmt_assignment->bind_param("i", $assignment_id);
    $stmt_assignment->execute();
    $result_assignment = $stmt_assignment->get_result();

    //check if assignment exists
    if ($result_assignment->num_rows > 0) {
        $row_assignment = $result_assignment->fetch_assoc();
        $assignment_title = $row_assignment['title'];
    }
    $stmt_assignment->close();

        $stmt_step = $conn->prepare("CALL getAssignmentSteps(?)");
        $stmt_step->bind_param("i", $assignment_id);
        $stmt_step->execute();
        $result_step = $stmt_step->get_result();

        //output assignment title and steps
        echo "<h1>$assignment_title</h1>";
        echo "<div class='timeline'>";
        $leftPosition = 0;
        $counter = 1; // For step color
        while ($row_step = $result_step->fetch_assoc()) {
            $title = $row_step['title'];
            $step_num = $row_step['step_number'];
            $percentage = $row_step['percentage'];
            $step_class = "step step-" . $counter;
            ?>
            <div class="<?php echo $step_class; ?>" style="width:<?php echo $percentage; ?>%; left: <?php echo $leftPosition; ?>%;">
            <div class="step-percent"><?php echo $percentage; ?>%</div>
            <div class="step-name">
                <div class="vertical-line"></div><?php echo $step_num.'.' . ' ' . $title; ?>
        </div>
            </div>
        <?php
        $leftPosition += $percentage; // Increment left position for the next step
        $counter++; // Increment counter for the next step color
        } // Close while loop
        echo "</div>"; // Close timeline div
    } else {
        echo "Assignment not found.";
    }

    // Close the connection
    $stmt_step->close();
    $conn->close();
?>
</div>

</body>
</html>