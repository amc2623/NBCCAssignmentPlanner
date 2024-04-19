<!-- login_proc.php -->
<!-- Purpose: Proc to handle user login. -->
<?php
session_start();
include ("../connect.php");

// Retrieve the assignment ID from the form data
$assignmentId = $_POST['assignment_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (!$conn) {
        die('<script type="text/javascript">alert("Database connection failed!"); window.location.href = "../../login.php";</script>');
    }

    // Call the login stored procedure
    $stmt = $conn->prepare("CALL loginProcedure(?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo '<script type="text/javascript">
                alert("Invalid username or password!");
                window.location.href = "../../login.php";
              </script>';
        exit();
    }

    // Get the user ID, role, and password hash from the result set
    $row = $result->fetch_assoc();
    $userID = $row["user_id"];
    $userRole = $row["role"];
    $passwordHash = $row["password"];

    $stmt->close();
    $conn->close();

    // Verify the password
    if (!password_verify($password, $passwordHash)) {
        echo '<script type="text/javascript">
                alert("Invalid username or password!");
                window.location.href = "../../login.php";
              </script>';
        exit();
    }

    // Password is correct, set the session variables and redirect

    $_SESSION["user_id"] = $userID;
    $_SESSION["role"] = $userRole;

    // Redirect based on user role
    switch ($userRole) {
        case 'Admin':
            // Redirect to Admin Dashboard
            header("Location: ../../dashboard_admin.php");
            exit();
        case 'Instructor':
            // Redirect to Instructor Dashboard
            header("Location: ../../dashboard_instructor.php");
            exit();
        case 'Student':
            // Redirect to Student Dashboard or Assignment Preview Page with assignment ID
            if (!empty($assignmentId)) {
                header("Location: ../../assignment_plan.php?assignment_id=" . $assignmentId);
                exit();
            } else {
                header("Location: ../../my_assignments.php");
                exit();
            }

        default:
            // Unknown Role - Redirect to Main with Popup
            echo '<script type="text/javascript">
                alert("Unknown Role!");
                window.location.href = "./login.php";
              </script>';
            exit();
    }

} else {
    header("Location: ../../login.php");
    exit();
}
?>