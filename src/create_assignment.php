<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

if ($_SESSION["role"] !== "Instructor") {
    header("Location: login.php");
    exit();
}

include 'connect.php';

$courses = [];

$result = $conn->query("SELECT course_id, course_name FROM Course");

while ($row = $result->fetch_assoc()) {
    $courses[$row['course_id']] = $row['course_name'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="createassignmentstyle.css">
    <title>Assignment Creation</title>
</head>
<body>
    <h2>Create New Assignment</h2>

    <form action="assignment_proc.php" method="post" enctype="multipart/form-data"> <!-- using a proc page to handle the assignment creation -->
        <!-- Title field -->
        <label for="assignment_title">Assignment Title:</label>
        <input type="text" id="assignment_title" name="assignment_title" required>
        <br>

        <!-- Thumbnail upload -->
        <label for="thumbnail">Thumbnail:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
        <br>

        <!-- Description field -->
        <label for="assignment_description">Assignment Description:</label>
        <textarea id="assignment_description" name="assignment_description" rows="4" required></textarea>
        <br>

        <label for="course_id">Select Course:</label>
        <select id="course_id" name="course_id">
            <?php foreach ($courses as $courseId => $courseName) : ?>
                <option value="<?php echo $courseId; ?>"><?php echo $courseName; ?></option>
            <?php endforeach; ?>
        </select>
        <br>

        <!-- Deadline field -->
        <label for="deadline">Deadline:</label>
        <input type="datetime-local" id="deadline" name="deadline" required>
        <br>

        <!-- Next button -- leads to create steps so instructor can add those -->
        <input type="submit" value="Next">
    </form>
</body>
</html>
