<!-- manage_assignments.php -->
<!-- Purpose: Admin and Instructor dashboard to manage assignments. -->
<?php
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION["role"] !== "Instructor")) {
    header('Location: ./login.php');
    exit();
}

// Retrieve all courses
$courses = [];
$stmtCourses = $conn->prepare("CALL getAllCourses()");
$stmtCourses->execute();
$resultCourses = $stmtCourses->get_result();

while ($rowCourse = $resultCourses->fetch_assoc()) {
    $courses[$rowCourse['course_id']] = $rowCourse['course_name'];
}

// Close the statement for getting courses
$stmtCourses->close();

$courseName = isset($_GET['course_name']) ? $_GET['course_name'] : '';

// Prepare and execute the assignment query based on user role
if ($_SESSION["role"] === "Instructor") {
    if ($stmt = $conn->prepare("CALL getAllAssignmentsForInstructor(?)")) {
        $stmt->bind_param("i", $_SESSION['user_id']);
    } else {
        // Handle error if the statement couldn't be prepared
        error_log("Failed to prepare statement: " . $conn->error);
    }
} elseif ($_SESSION["role"] === "Admin") {
    if (!($stmt = $conn->prepare("CALL getAllAssignments()"))) {
        // Handle error
        error_log("Failed to prepare statement: " . $conn->error);
    }
}

if ($stmt) {
    if (!$stmt->execute()) {
        // Handle execution error
        error_log("Failed to execute statement: " . $stmt->error);
    }
    $result = $stmt->get_result();
}

// Filter assignments based on course_name if provided in the URL
$filteredResult = [];

if (!empty($courseName)) {
    while ($row = $result->fetch_assoc()) {
        if ($row['course_name'] === $courseName) {
            $filteredResult[] = $row;
            
// Sort the filtered result by course name
usort($filteredResult, function($a, $b) {
    return strcmp($a['course_name'], $b['course_name']);
});
        }
    }
} else {
    // If no course_name is provided, assign the original result to $filteredResult
    while ($row = $result->fetch_assoc()) {
        $filteredResult[] = $row;
        
// Sort the filtered result by course name
usort($filteredResult, function($a, $b) {
    return strcmp($a['course_name'], $b['course_name']);
});
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Assignments</title>
    <link rel="stylesheet" href="./assets/css/manageStyle.css">
    <style>
        /* Additional styles if needed */
    </style>
</head>
<body>
<div class="mainform">
    <!-- Display the top bar, filter assignments -->
    <div class="header">
    <h1>Manage Assignments</h1>
        <label for="course_name">Select Course: </label>
        <select id="course_name" name="course_name" onchange="filterAssignments(this.value)">
    <option value="All" <?php echo ($courseName === 'All') ? 'selected' : ''; ?>>All</option>
    <?php 
        // Sort the courses alphabetically
        asort($courses);
        
        // Loop through the sorted courses
        foreach ($courses as $courseId => $course) : 
    ?>
    <option value="<?php echo $course; ?>" <?php echo ($course === $courseName) ? 'selected' : ''; ?>><?php echo $course; ?></option>
    <?php endforeach; ?>
</select>
        <br><br>
    </div>

    <?php
    if (isset($filteredResult) && count($filteredResult) > 0)  {
        // Start table
        echo "<table>";

        // Table headers
        echo "<tr><th>ID</th><th>Title</th><th>Course</th><th>Actions</th></tr>";

        // Output data of each row
        foreach ($filteredResult as $row) {
            echo "<tr><td>" . $row["assignment_id"] . "</td><td>" . $row["title"] . "</td><td>" . $row["course_name"] . "</td>";
            echo "<td>
                <a href='./edit_assignment.php?id=" . $row["assignment_id"] . "'>Edit</a> | 
                <a href='./src/proc-pages/delete_proc.php?id=" . $row["assignment_id"] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
              </td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No assignments available for this course.";
    }
    ?>
    </div>
    <div class="btn-container">
        <button onclick="window.location.href='./create_assignment.php'">Create New Assignment</button><br>
        <button onclick="window.location.href='./dashboard.php'">Back to Dashboard</button></BR>
     <button onclick="window.location.href='./logout.php'">Logout</button>
</div>
<script src="./assets/js/functions.js"></script>
</body>

<footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>

</html>
