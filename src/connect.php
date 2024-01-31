<!--connect.php -->
<?php
//these are defined as constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'assignment_planner');
	
global $conn;
	  $conn = mysqli_connect(DB_HOST,DB_USER,DB_PASS, DB_NAME);
if (!$conn)
  {
  die('Could not connect: ' . mysql_error());
  }
?>
