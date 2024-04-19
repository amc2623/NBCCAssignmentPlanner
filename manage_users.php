<!-- manage_users.php -->
<!-- Purpose: Admin dashboard to manage users. -->
<?php
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION["role"] !== "Admin")) {
    header('Location: ./login.php');
    exit();
}

// Retrieve all roles
$roles = [];
$stmtRoles = $conn->prepare("CALL getAllRoles()");
$stmtRoles->execute();
$resultRoles = $stmtRoles->get_result();

while ($rowRoles = $resultRoles->fetch_assoc()) {
    // Add each role to the $roles array
    $roles[$rowRoles['role']] = $rowRoles['role'];
}

// Close the statement for getting roles
$stmtRoles->close();

// Retrieve the selected role from the query parameter
$selectedRole = isset($_GET['role']) ? $_GET['role'] : '';

// Prepare and execute the user query
if ($_SESSION["role"] === "Admin") {
    if (!($stmt = $conn->prepare("CALL getAllUsers()"))) {
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

    // Filter users based on role
$filteredResult = [];

    // Fetch and store the filtered users
    while ($row = $result->fetch_assoc()) {
        $filteredResult[] = $row;
    }
// Sort the filtered result by role
usort($filteredResult, function($a, $b) {
    return strcmp($a['role'], $b['role']);
});
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="./assets/css/manageStyle.css">
    <style>
        /* Additional styles if needed */
    </style>
</head>
<body>
<div class="mainform">
    <!-- Display the top bar, filter Users -->
    <div class="header">
    <h1>Manage Users</h1>
        <label for="role">Select Role:</label>
        <select class="role" name="role" onchange="filterRoles(this.value)">
            <option value="All" <?php echo ($selectedRole  === 'All') ? 'selected' : ''; ?>>All</option>

                <?php 
                asort($roles);
                // Populate the dropdown with roles
            foreach ($roles as $roleName => $_) : ?> 
                <option value="<?php echo $roleName; ?>" <?php echo ($selectedRole === $roleName) ? 'selected' : ''; ?>><?php echo $roleName; ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>
    </div>

    <?php
    if (isset($filteredResult) && count($filteredResult) > 0)  {
        // Start table
        echo "<table>";

        // Table headers
        echo "<tr><th>User ID</th><th>Role</th><th>Name</th><th>Username</th><th>Email</th><th>Actions</th></tr>";

        // Output data of each row
        foreach ($filteredResult as $row) {
            echo "<tr><td>" . $row["user_id"] . "</td><td>" . $row["role"] . "</td><td>" . $row["name"] . "</td><td>" . $row["username"] . "</td><td>" . $row["email"] . "</td>";
            echo "<td>
                <a href='./edit_user.php?id=" . $row["user_id"] . "'>Edit</a> | 
                <a href='./src/proc-pages/delete_proc.php?id=" . $row["user_id"] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
              </td></tr>";
        }
        echo "</table>";
    } else {
        echo "No users available.";
    }
    ?>
    </div>
    <div class="btn-container">
<button onclick="window.location.href='./signup.php'">Create New User</button>
        <button onclick="window.location.href='./dashboard.php'">Back to Dashboard</button>
     <button onclick="window.location.href='./logout.php'">Logout</button>
</div>
<script src="./assets/js/functions.js"></script>
</body>

<footer>
    <small>© <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.</small>
</footer>

</html>
