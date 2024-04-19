<!-- edit_user_proc.php -->
<!-- Purpose: Proc to edit a user. -->
<?php
// Include database connection code
include("../connect.php");

// Start the session
session_start();

// Check if user ID is set in session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_id'])) {
    echo "<p>User ID not set.</p>";
    exit();
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle user data
    if (isset($_POST['user_id']) && isset($_POST['role']) && isset($_POST['name']) && isset($_POST['username']) && isset($_POST['email'])) {
        $user_id = $_POST["user_id"];
        $role = $_POST["role"];
        $name = $_POST["name"];
        $username = $_POST["username"];
        $email = $_POST["email"];

        // Prepare and execute the editAssignment stored procedure
        $stmt_edit_user = $conn->prepare("CALL editUser(?, ?, ?, ?, ?, ?)");
        $stmt_edit_user->bind_param("issss", $user_id, $role, $name, $username, $email);
        $stmt_edit_user->execute();
        $stmt_edit_user->close();
    }

    $conn->close();

    // Redirect to assignment preview page or another appropriate page
    header("Location: ../../manage_users.php");
    exit();
}
?>
