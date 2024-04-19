<!-- edit_user.php-->
<!-- Purpose: This page allows an admin to edit a user. -->
<?php
// Include necessary files and start session
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();

// Check if user ID is provided
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $_SESSION['user_id'] = $user_id;
} elseif (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Redirect to login if assignment ID is not available
    header('Location: ./login.php');
    exit();
}

// Retrieve current assignment details from the database
$stmt_user = $conn->prepare("CALL getUserDetails(?)");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

// Check if user exists
if ($result_user->num_rows == 0) {
    echo "<p>User not found.</p>";
    exit();
}

// Fetch user details
$row_user = $result_user->fetch_assoc();
$current_user_id = $row_user['user_id'];
$current_role = $row_user['role'];
$current_name = $row_user['name'];
$current_username = $row_user['username'];
$current_email = $row_user['email'];

// Close assignment statement
$stmt_user->close();


// view and edit student assignments
$stmt_assignments = $conn->prepare("CALL getAllAssignmentsForStudent(?)");
$stmt_assignments->bind_param("i", $user_id);
$stmt_assignments->execute();
$result_assignments = $stmt_assignments->get_result();

//Fetch user's assignments
$assignments = [];
while ($row_assignments = $result_assignments->fetch_assoc()) {
    $assignments[] = $row_assignments;
}

// Close assignments statement
$stmt_assignments->close();


// Retrieve courses for dropdown
// $courses = [];
// $stmt_courses = $conn->prepare("CALL getStudentCourses()");
// $stmt_courses->execute();
// $result_courses = $stmt_courses->get_result();

// while ($row_course = $result_courses->fetch_assoc()) {
//     $courses[$row_course['course_id']] = $row_course['course_name'];
// }

// // Close courses statement
// $stmt_courses->close();

// // Close database connection
// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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

<form id="steps-form" action="./src/proc-pages/edit_user_proc.php" method="post">
    <h2>Edit User Details</h2>

    <!-- User Details -->
    <label for="userId">User ID:</label>
    <input type="text" id="userId" name="userId" value="<?php echo $current_user_id; ?>">

    <label for="role">Role:</label>
    <input type="text" id="role" name="role" value="<?php echo $current_role; ?>">

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo $current_name; ?>">
    
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $current_username; ?>">

    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="<?php echo $current_email; ?>">

    <!-- Dropdown for user courses -->
    <label for="course_id">Courses:</label>
    <select id="course_id" name="course_id">
        <?php foreach ($courses as $courseId => $courseName): ?>
            <option value="<?php echo $courseId; ?>" <?php echo ($courseId == $row_assignment['course_id']) ? 'selected' : ''; ?>>
                <?php echo $courseName; ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <!-- End Dropdown for course selection -->

    <!-- Assignment Details -->
    <div>
        <label for="assignments">Assignments:</label>
        <select id="assignment_id" name="assignment_id">
        <?php foreach ($assignments as $assignment): ?>
            <div class="step-wrapper">
                <div class="assignment" data-step-index="<?php echo $assignment['assignment_id']; ?>">
                    <div class="step-info">
                        <label for="step_title_<?php echo $assignment['title']; ?>">Title:</label>
                    </div></BR>
            </div>
        <?php endforeach; ?>
        </select></div></BR>
    </div><hr></BR></BR>

    <button type="submit" onclick="return confirmPrompt()">Confirm</button></BR></BR>
    <button onclick="window.location.href='./manage_users.php'">Back to Users</button>
</form>

<footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>

</body>
</html>

