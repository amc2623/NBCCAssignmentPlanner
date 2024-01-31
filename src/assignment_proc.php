<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_title = $_POST['assignment_title'];
    $assignment_description = $_POST['assignment_description'];
    $deadline = $_POST['deadline'];
    $course_id = $_POST['course_id'];

    // Check if the user uploaded a thumbnail
    if (!empty($_FILES['thumbnail']['name'])) {
        // Handle thumbnail upload
        $thumbnail_name = $_FILES['thumbnail']['name'];
        $thumbnail_temp = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_destination = "C:/xampp/htdocs/NBCC-Planner-Project/AssignmentPlanner/thumbnails/" . $thumbnail_name;
        move_uploaded_file($thumbnail_temp, $thumbnail_destination);
    } else {
        // If no thumbnail uploaded, set the thumbnail path to the first letter of the assignment title
        $firstLetter = strtolower(substr($assignment_title, 0, 1));
        $thumbnail_destination = "C:/xampp/htdocs/NBCC-Planner-Project/AssignmentPlanner/thumbnails/{$firstLetter}.png";
    }

    $mysql_deadline = date('Y-m-d H:i:s', strtotime($deadline));

    // Use a stored procedure to insert assignment into the database
    $stmt = $conn->prepare("CALL InsertAssignment(?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $assignment_title, $assignment_description, $thumbnail_destination, $mysql_deadline, $course_id);
    $stmt->execute();

    $stmt->close();

    $result = $conn->query("SELECT LAST_INSERT_ID() as assignment_id");
    if ($row = $result->fetch_assoc()) {
        $assignment_id = $row['assignment_id'];
    } 
    else {
        echo "Error getting assignment_id: " . mysqli_error($conn) . "<br>";
    exit();
}

$_SESSION['assignment_id'] = $assignment_id;

$conn->close();

header("Location: create_steps.php");
exit();

}
?>
