<!-- index.php -->
<!-- Purpose: The main page of the website. Displays all assignments and allows the user to login or logout. -->
<?php
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Plan your NBCC assignments!" />
    <title>NBCC Assignment Planner</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/studentDashboardStyle.css" />
    <link rel="icon" href="./assets/img/logos/favicon.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="dashboard-container">
        <?php
        // If the user is logged in
        if (isset($_SESSION['user_id'])) {
            // Get user ID from session
            $user_id = $_SESSION['user_id'];
            $actionButton = "Logout";
            $actionUrl = "logout.php";
        } else {
            $actionButton = "Login or Sign up";
            $actionUrl = "login.php";
        }

        echo "<h1>NBCC Assignment Planner</h1>";
        echo "<div><p> View assignments. Login to save a custom plan. </p>";
        echo "<hr class='hrclass'>";

        $stmt = $conn->prepare("CALL getAllAssignments()");
        // Execute the statement
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Start table
            echo "<table>";
            // Initialize column counter
            $columnCount = 0;

            // Output data of each row
            while ($row = $result->fetch_assoc()) {

                // Start a new row if column count is zero and less than 4
                if ($columnCount == 0) {
                    echo "<tr>";
                }

                // Output table data for each assignment
                echo "<td>";
                echo "<a href='./assignment_plan.php?assignment_id=" . $row["assignment_id"] . "&title=" . urlencode($row['title']) . "'><img src='" . $row["thumbnail_path"] . "' alt='" . $row["title"] . "'>" . $row["title"] . "</a></td>";

                // Increment column count
                $columnCount++;

                // If column count reaches 4, end the row and reset column count
                if ($columnCount == 4) {
                    echo "</tr>";
                    $columnCount = 0;
                }
            }

            // Close the table if column count is not zero
            if ($columnCount != 0) {
                // Add empty cells to fill the row if needed
                while ($columnCount < 4) {
                    echo "<td></td>";
                    $columnCount++;
                }
                echo "</tr>";
            }

            // End table
            echo "</table>";
        } else {
            echo "0 results";
        }

        // Close the statement and the connection
        $stmt->close();
        $conn->close();
        ?>
        </div>
        </div>

        <div id="btn-container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="window.location.href='my_assignments.php'" style="width: 400px; flex-direction: column;" class="logout-button">Show My Assignments</button>
        <?php endif; ?>

        <form action="<?php echo $actionUrl; ?>" method="post">
            <button type="submit" style="width: 400px; flex-direction: column;" class="logout-button">
                <?php echo $actionButton; ?>
            </button>
        </form>
        </div>
</body>

<footer style="text-align:left">
    <small>©
        <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.
    </small>
</footer>

</html>