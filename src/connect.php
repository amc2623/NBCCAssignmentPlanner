<!-- connect.php -->
<!-- Purpose: Establish a connection to the database on the remote server. -->
<?php
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'assignmentplanner');

define('DB_HOST', '10.155.200.103');
define('DB_USER', 'remoteUser');
define('DB_PASS', 'project_dev1');
define('DB_NAME', 'assignmentplanner');

global $conn;
try{
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    exit();
}

if (!$conn) {
    die('Could not connect: ' . mysqli_error($conn));
}
?>
