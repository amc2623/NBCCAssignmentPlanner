<!-- manage_courses.php -->
<!-- Purpose: Admin dashboard to manage courses. -->
<?php
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION["role"] !== "Admin")) {
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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Courses</title>
    <link rel="stylesheet" href="./assets/css/manageStyle.css">
    <style>
        /* Additional styles if needed */
    </style>
</head>

<body>
    <div class="mainform">
        <!-- Display the top bar -->
        <?php include './assets/includes/topBar.php'; ?>

        <!-- Display the courses -->
        <div class="header">
            <h1>Manage Courses</h1>
            <hr>
            <h2>Courses: </h2>

            <?php
            // Sort the courses alphabetically
            asort($courses);

            // Loop through the sorted courses
            foreach ($courses as $courseId => $course):
                ?>
                <ul><input type="text" id="course" name="course" value="<?php echo $course; ?>"></ul>
            <?php endforeach; ?>

        </div></BR>

        <form action="addCourse.php" class="addCourse" method="post" style="display: flex;">
            <label for="add_course"><h3>Add A Course: &nbsp</h3></label>
            <input type="text" id="add_course" name="course_name" value="Course Code" style="width: 200px; height: 40px;">
            <button type="submit" class="addCourseBtn">Add Course</button>
        </form>


    </div>
    <!-- Buttons -->
    <div class="btn-container">
        <button type="submit">Confirm Changes</button></BR>
        <button onclick="window.location.href='./dashboard.php'">Back to Dashboard</button>
        <button onclick="window.location.href='./logout.php'">Logout</button>
    </div>

    <script src="./assets/js/functions.js"></script>
</body>

<footer>
    <small>©
        <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.
    </small>
</footer>

</html>