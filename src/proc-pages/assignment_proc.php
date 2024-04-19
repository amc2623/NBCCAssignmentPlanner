<!-- assignment_proc.php -->
<!-- Purpose: Proc to create an assignment for a user. -->
<?php
session_start();
include '../connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_title = $_POST['assignment_title'];
    $assignment_description = $_POST['assignment_description'];
    $deadline = $_POST['deadline'];
    $course_id = $_POST['course_id'];

    //get the user_id from the session
    $user_id = $_SESSION['user_id'];

    //check for thumbnail upload
    if (!empty($_FILES['thumbnail']['name'])) {
        //handle it
        $thumbnail_name = $_FILES['thumbnail']['name'];
        $thumbnail_temp = $_FILES['thumbnail']['tmp_name'];

        //check to see if the file is a png
        $thumbnail_extension = pathinfo($thumbnail_name, PATHINFO_EXTENSION);
        if ($thumbnail_extension != "png") {
            //alerts
            echo "<script>alert('File must be a PNG.')</script>";
            echo "<script>window.history.back()</script>";
            exit();
        }
        //check if the image is 100x100 pixels
        list($width, $height) = getimagesize($thumbnail_temp);
        if ($width != 100 || $height != 100) {
            // Send out an alert if the file is not 100x100 pixels and go back
            echo "<script>alert('Image must be 100x100 pixels.')</script>";
            echo "<script>window.history.back()</script>";
            exit();
        }
        // Check if the file is less than 1MB
        if ($_FILES['thumbnail']['size'] > 1559000) {
            // Send out an alert if the file is too large and go back
            echo "<script>alert('File must be less than 1MB.')</script>";
            echo "<script>window.history.back()</script>";
            exit();
        }

        // All good, move the thumbnail to the correct folder
        $thumbnail_destination = "../../assets/img/assignment-thumbnails/" . $thumbnail_name;
        move_uploaded_file($thumbnail_temp, $thumbnail_destination);

    } else {
        // If no thumbnail uploaded, set the thumbnail path to the first letter of the assignment title
        $firstLetter = strtolower(substr($assignment_title, 0, 1));
        $thumbnail_destination = "../../assets/img/default-thumbnails/{$firstLetter}.png";
    }

    // Remove the ../../ from the thumbnail path but keep the ./ to make it relative
    $thumbnail_destination = str_replace("../.", "", $thumbnail_destination);

    $mysql_deadline = date('Y-m-d H:i:s', strtotime($deadline));

    // Use a stored procedure to insert assignment into the database
    $stmt = $conn->prepare("CALL insertAssignment(?, ?, ?, ?, ?, ?)"); 
    $stmt->bind_param("ssssii", $assignment_title, $assignment_description, $thumbnail_destination, $mysql_deadline, $course_id, $user_id); // Bind the user_id variable
    if (!$stmt->execute()) {
        // Handle error
        echo "Error inserting assignment: " . $stmt->error;
        exit();
    }

    $stmt = $conn->prepare("CALL getLastAdded()");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $assignment_id = $row['assignment_id'];
    } 
    else {
        echo "Error getting assignment_id: " . mysqli_error($conn) . "<br>";
    exit();
}

$_SESSION['assignment_id'] = $assignment_id;

//close connection
$stmt->close();
$conn->close();

header("Location: ../../create_steps.php");
exit();

}
?>
