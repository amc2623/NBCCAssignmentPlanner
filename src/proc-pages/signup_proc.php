<!-- signup_proc.php -->
<!-- Purpose: Proc to sign up a user. -->
<?php
session_start();
include("../connect.php");

// Retrieve the assignment ID from the form data
if (isset($_GET['assignment_id'])) {
    // Get the assignment ID from the URL parameter
    $assignmentId = $_GET['assignment_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!$conn) {
        die('<script type="text/javascript">alert("Database connection failed!"); window.location.href = "../../login.php";</script>');
    }

    // Prepare and execute the stored procedure
    $sql = "CALL signupProcedure(?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        $stmt->bind_param("ssss", $username, $name, $email, $hashed_password);

        // Execute the stored procedure
        if ($stmt->execute()) {
            if (!empty($assignmentId)) {
                header("Location: ../../assignment_plan.php?assignment_id=" . $assignmentId);
                exit();
            } else {
                // Redirect to login page
                header("location: ../../login.php");
                exit();
            }
        } else {
            $error = "Something went wrong. Please try again later.";
        }

        // Close statement
        $stmt->close();
    } else {
        // Redirect to signup page
        header("location: ../../signup.php");
        exit();
    }

    // Close connection
    $conn->close();
} else {
    // Redirect to signup page
    header("location: ../../signup.php");
    exit();
}
?>