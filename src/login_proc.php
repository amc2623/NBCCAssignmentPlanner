<?php
session_start();

// Include database connection code if not already included
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the values from the form
    $loginType = $_POST["loginType"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the stored procedure
    $stmt = $conn->prepare("CALL loginProcedure(?, ?, ?, @userID, @userRole)");
    $stmt->bind_param("sss", $loginType, $username, $password);
    $stmt->execute();
    $stmt->close();

    // Retrieve the output parameters from the stored procedure
    $result = $conn->query("SELECT @userID AS userID, @userRole AS userRole");
    $row = $result->fetch_assoc();
    $userID = $row["userID"];
    $userRole = $row["userRole"];

    // Close the database connection
    $conn->close();

    // Check if the login was successful
    if ($userID > 0 && !empty($userRole)) {
        // Start the session
        session_start();

        // Set session variables
        $_SESSION["user_id"] = $userID;
        $_SESSION["role"] = $userRole;

        // Redirect to the appropriate dashboard based on the role
        if ($userRole === 'Admin') {
            header("Location: dashboard.php");
            exit();
        } elseif ($userRole === 'Instructor') {
            header("Location: dashboard.php");
            exit();
        } elseif ($userRole === 'Student') {
            header("Location: dashboard.php");
            exit();
        }
    } else {
        // Redirect back to the login page if login fails
        header("Location: login.php");
        exit();
    }
} else {
    // Redirect back to the login page if accessed directly without submitting the form
    header("Location: login.php");
    exit();
}
?>
