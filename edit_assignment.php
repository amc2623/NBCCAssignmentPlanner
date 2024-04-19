<!-- edit_assignment.php-->
<!-- Purpose: This page allows an admin to edit an assignment. -->
<?php
// Include necessary files and start session
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();

// Check if assignment ID is provided
if (isset($_GET['id'])) {
    $assignment_id = $_GET['id'];
    $_SESSION['assignment_id'] = $assignment_id;
} elseif (isset($_SESSION['assignment_id'])) {
    $assignment_id = $_SESSION['assignment_id'];
} else {
    // Redirect to login if assignment ID is not available
    header('Location: ./login.php');
    exit();
}

$assignment_id = isset($_SESSION['assignment_id']) ? $_SESSION['assignment_id'] : null;

// Retrieve current assignment details from the database
$stmt_assignment = $conn->prepare("CALL getAssignmentDetails(?)");
$stmt_assignment->bind_param("i", $assignment_id);
$stmt_assignment->execute();
$result_assignment = $stmt_assignment->get_result();

// Check if assignment exists
if ($result_assignment->num_rows == 0) {
    echo "<p>Assignment not found.</p>";
    exit();
}

// Fetch assignment details
$row_assignment = $result_assignment->fetch_assoc();
$current_title = $row_assignment['title'];
$current_description = $row_assignment['description'];
$current_thumbnail_path = $row_assignment['thumbnail_path'];

// Close assignment statement
$stmt_assignment->close();

// Retrieve assignment steps from the database
$stmt_steps = $conn->prepare("CALL getAssignmentSteps(?)");
$stmt_steps->bind_param("i", $assignment_id);
$stmt_steps->execute();
$result_steps = $stmt_steps->get_result();

// Fetch assignment steps
$steps = [];
while ($row_step = $result_steps->fetch_assoc()) {
    $steps[] = $row_step;
}

// Close steps statement
$stmt_steps->close();

// Retrieve courses for dropdown
$courses = [];
$stmt_courses = $conn->prepare("CALL GetAllCourses()");
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

while ($row_course = $result_courses->fetch_assoc()) {
    $courses[$row_course['course_id']] = $row_course['course_name'];
}

// Close courses statement
$stmt_courses->close();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="./assets/css/editStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
    <script src="./assets/js/functions.js"></script>
    <script>
        $(document).ready(function() {
            // Add click event listener to step-info div
            $(".step-info").click(function(event) {
                if (!$(event.target).is('input, textarea')) {
                    $(this).siblings(".step-description").slideToggle(750);
                }
                event.stopPropagation();
            });
        });
    </script>
</head>
<body>

<form id="steps-form" action="./src/proc-pages/edit_assignment_proc.php" method="post">
    <h2>Edit Assignment Details</h2>

    <!-- Assignment Details -->
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?php echo $current_title; ?>">

    <label for="description">Description:</label>
    <textarea id="description" name="description"><?php echo $current_description; ?></textarea>

    <label for="thumbnail_path">Thumbnail:</label>
<img src="<?php echo $row_assignment['thumbnail_path']; ?>" id="thumbnail_path" alt="Thumbnail"><?php echo $current_thumbnail_path; ?>
<input type="file" id="new_thumbnail" name="new_thumbnail" accept="image/*">
    

    <!-- Dropdown for course selection -->
    <label for="course_id">Select Course:</label>
    <select id="course_id" name="course_id">
        <?php foreach ($courses as $courseId => $courseName): ?>
            <option value="<?php echo $courseId; ?>" <?php echo ($courseId == $row_assignment['course_id']) ? 'selected' : ''; ?>>
                <?php echo $courseName; ?>
            </option>
        <?php endforeach; ?>
    </select><br><br><hr>
    <!-- End Dropdown for course selection -->
    <div>
        <label for="remaining_percentage">Remaining Percentage:</label>
        <span id="remaining_percentage" >100 </span>% 
        <div id="percentage_warning" style="color: red;"></div><br>
    </div>
    <!-- Steps Details -->
    <h2>Edit Assignment Steps</h2>
    <div id="collapse">
    <div id="steps-container">
        <?php foreach ($steps as $step): ?>
            <div class="step-wrapper">
                <div class="step" data-step-index="<?php echo $step['steps_id']; ?>">
                    <div class="step-info">
                        <label for="step_title_<?php echo $step['steps_id']; ?>">Step Title:</label>
                        <input type="text" id="step_title_<?php echo $step['steps_id']; ?>" name="step_title_<?php echo $step['steps_id']; ?>" value="<?php echo $step['title']; ?>">&nbsp;
                        
                        <label for="step_number_<?php echo $step['steps_id']; ?>">Step Number:</label>
                        <input type="text" id="step_number_<?php echo $step['steps_id']; ?>" name="step_number_<?php echo $step['steps_id']; ?>" value="<?php echo $step['step_number']; ?>">&nbsp;

                        <label for="step_percentage_<?php echo $step['steps_id']; ?>">Step Percentage:</label>
                        <input type="text" id="step_percentage_<?php echo $step['steps_id']; ?>" name="step_percentage_<?php echo $step['steps_id']; ?>" value="<?php echo $step['percentage']; ?>">&nbsp;
                    </div>

                    <div class="step-description">
                        <label for="step_url_<?php echo $step['steps_id']; ?>">Step URL:</label>
                        <input type="text" id="step_url_<?php echo $step['steps_id']; ?>" name="step_url_<?php echo $step['steps_id']; ?>" value="<?php echo $step['url']; ?>">
                        <label for="step_description_<?php echo $step['steps_id']; ?>">Step Description:</label>
                        <textarea id="step_description_<?php echo $step['steps_id']; ?>" name="step_description_<?php echo $step['steps_id']; ?>"><?php echo $step['description']; ?></textarea>
                        <!-- Remove Step Button -->
                        <button type="button" onclick="removeStep(this)">Remove Step</button><br><br>
                    </div>
                    <button type="button" onclick="addStep()">Add Step</button>
                </div><hr><br>
            </div>
        <?php endforeach; ?>
    </div>
    </div></BR></BR>

    <button type="submit" onclick="return validateTotalPercentage()">Confirm Steps</button></BR></BR>
    <button onclick="window.location.href='./checklist.php?assignmentId=<?php echo $assignment_id; ?>'">Create Checklist</button></BR></BR>
    <button onclick="window.location.href='./manage_assignments.php'">Back</button>
</form>

<footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>

</body>
</html>

