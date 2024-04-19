<!-- assignment_plan. -->
<!-- Purpose: to display the details of an assignment and allow the user to create a plan for the assignment. -->
<?php
include './assets/includes/topBar.php';
include './src/connect.php';
session_start();
// Check if assignment ID is present in the URL parameters
if (isset($_GET['assignment_id'])) {
    // Get the assignment ID from the URL parameter
    $assignmentId = $_GET['assignment_id'];
} else {
    // Display error message if assignment ID is not provided
    echo "<p>No assignment ID provided.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plan your NBCC assignments!" />
    <title>Assignment Preview</title>
    <link rel="stylesheet" href="./assets/css/studentAssignmentPreviewStyle.css">
    <link rel="icon" href="./assets/img/logos/favicon.png">
    <script src="./assets/js/functions.js"></script>
    <script>
        function printPage() {
            window.print(); // This triggers the print functionality of the browser
        }

        // toggle for steps
        document.addEventListener('DOMContentLoaded', function () {
            var stepHeaders = document.querySelectorAll('.step-header');
            var stepContents = document.querySelectorAll('.step-content');

            // Hide all step contents initially
            stepContents.forEach(function (content) {
                content.style.display = 'none';
            });

            stepHeaders.forEach(function (header) {
                header.addEventListener('click', function () {
                    var clickedStepContent = this.nextElementSibling;

                    // Toggle the 'open' class on the clicked step header
                    this.classList.toggle('open');

                    // Toggle the display of the clicked step content
                    if (clickedStepContent.style.display === 'block') {
                        clickedStepContent.style.display = 'none';
                    } else {
                        clickedStepContent.style.display = 'block';
                    }
                });
            });
        });


        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('btnCreate').addEventListener('click', function () {
                // Get the values of the input fields
                var startDate = document.getElementById('start_date').value;
                var endDate = document.getElementById('end_date').value;

                // Check if either start date or end date are empty
                if (startDate === '' || endDate === '') {
                    // Display a pop-up message asking the user to enter both dates
                    alert('Please enter both start date and end date.');
                    return;
                }

                // Check if start date is greater than end date
                if (startDate > endDate) {
                    // Display a pop-up message asking the user to enter an earlier start date
                    alert('Please enter a start date that begins before the due date.');
                    return;
                }

                // Parse the dates as Date objects
                startDate = new Date(startDate);
                endDate = new Date(endDate);

                // Calculate the difference in milliseconds between the two dates
                var timeDifference = endDate.getTime() - startDate.getTime();
                // Convert the difference to days
                var remainingDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

                var options = {
                    month: 'long',
                    day: 'numeric',
                    weekday: 'long',
                    hour: 'numeric',
                    minute: 'numeric',
                    hour12: true
                };

                // Format the dates with the customized options
                var formattedStartDate = startDate.toLocaleDateString('en-US', options);
                var formattedEndDate = endDate.toLocaleDateString('en-US', options);

                // Display the formatted dates on the page
                var resultsDiv = document.getElementById('totalDays');
                resultsDiv.innerHTML = 'Start Date: <span class="formatted-date">' + formattedStartDate + '</span><br>End Date: <span class="formatted-date">' + formattedEndDate + '</span><br><u>You have <span class="formatted-date">' + remainingDays + ' </span>days to complete assignment.';

                // Get all step elements
                var assignmentSteps = document.querySelectorAll('.steps');
                assignmentSteps.forEach(function (step, index) {
                    // Get the percentage value for this step
                    var percentage = 0.25; // Placeholder value, replace with actual percentage fetched from the database

                    // Calculate the completion date for this step
                    var stepDuration = remainingDays * percentage;
                    var stepStartDate = new Date(startDate.getTime() + (index * stepDuration * 24 * 60 * 60 * 1000));
                    var stepEndDate = new Date(stepStartDate.getTime() + (stepDuration * 24 * 60 * 60 * 1000));
                    // Format the dates
                    var formattedStartDate = stepStartDate.toLocaleDateString('en-US', options);
                    var formattedEndDate = stepEndDate.toLocaleDateString('en-US', options);
                    // Create a new element to display the completion date
                    var completionDateSpan = document.createElement('span');
                    completionDateSpan.innerHTML = "<span style='float: right;'>Complete by " + formattedStartDate + "</span>";
                    // Append the completion date to the step header
                    step.querySelector('.step-header').appendChild(completionDateSpan);
                });

                // Get step percent
                var assignmentStepsContainer = document.querySelectorAll('.assignmentStepsContainer');
                // Loop through each step element
                assignmentStepsContainer.forEach(function (container) {
                    // Toggle the visibility of the container
                    container.style.display = 'block';
                    var completeBy = container / remainingDays;
                });

                // Simulate click on step headers to open them
                var stepHeaders = document.querySelectorAll('.step-header');
                stepHeaders.forEach(function (header) {
                    var clickedStepContent = header.nextElementSibling;
                    // Open the step content
                    clickedStepContent.style.display = 'block';
                    // Add 'open' class to the header if needed (for styling purposes)
                    header.classList.add('open');
                });
            });
        });


        $(document).ready(function () {
            // Function to handle form submission
            $('#btnCreate').click(function (event) {
                event.preventDefault(); // Prevent default form submission
                <?php if (isset($_SESSION['user_id'])) ?> {
                    // User is logged in, proceed with form submission
                    // var formData = $(this).closest('form').serialize(); 
                    var formData = $(this).serialize();

                    // AJAX post request to submit form data
                    $.ajax({
                        type: 'POST',
                        url: 'assignment_plan_proc.php',
                        data: formData,
                        success: function (response) {
                            // Handle success response
                            console.log('Form submitted successfully');
                            // You can update the page content here if needed
                        },
                        error: function (xhr, status, error) {
                            // Handle error response
                            console.error('Error submitting form:', error);
                        }
                    });
                }
            });
        });


        // JavaScript function to redirect to login page with assignment ID
        function loginRedirect() {
            // Get the assignment_id from the URL and put it in the session
            var urlParams = new URLSearchParams(window.location.search);
            console.log("URL Parameters:", urlParams.toString()); // Log URL parameters

            var assignmentId = urlParams.get('assignment_id');
            sessionStorage.setItem("assignmentId", assignmentId);

            // If user chooses to login or sign up, once logged in it will redirect them back to the assignment they were on.
            if (assignmentId) {
                sessionStorage.setItem("assignmentId", assignmentId);
                sessionStorage.setItem("redirect", "login");
                window.location.href = "login.php?assignment_id=" + assignmentId;
            } else {
                alert("An error has occurred.");
            }
        }
    </script>
</head>


<body>
    <!-- container for print and download-->
    <div class="container">
        <div class="print-download-icons">
            <!-- Download -->
            <a href="src/proc-pages/assignmentpdf_proc.php?assignment_id=<?php echo $assignmentId; ?>" target='_blank'>
                <img src="./assets/img/download_icon.png" title="Download page" style="width:50px; float:right" alt="Download">
            </a>

            <!-- Print -->
            <a href="#" onclick="printPage()">
                <img src="./assets/img/print_icon.png" title="Print page" style="width:40px; float:right" alt="Print">
            </a>
        </div>
        <form
            action="./src/proc-pages/assignment_plan_proc.php<?php echo isset($_GET['assignment_id']) ? '?assignment_id=' . urlencode($_GET['assignment_id']) : ''; ?>"
            method="post" enctype="multipart/form-data">

            <?php
            //change the login/logout button depending on if the user is logged in or not
            $actionUrl = isset($_SESSION['user_id']) ? "logout.php" : "javascript:void(0)"; // Set URL or javascript:void(0) based on login status
            $actionButton = isset($_SESSION['user_id']) ? "Logout" : "Login or Sign up";

            // Check if assignment_id is set and not empty
            if (isset($_GET['assignment_id']) && !empty($_GET['assignment_id'])) {
                $assignment_id = $_GET['assignment_id'];

                // Fetch assignment details and steps from the database using multi-query
                $stmt_assignment = $conn->prepare("CALL getAssignmentDetails(?)");
                $stmt_assignment->bind_param("i", $assignment_id);
                $stmt_assignment->execute();
                $result_assignment = $stmt_assignment->get_result();

                if ($result_assignment->num_rows > 0) {
                    $row_assignment = $result_assignment->fetch_assoc();
                    // Output assignment details
                    echo "<h1 id='assignment_title' name='assignment_title'>" . $row_assignment['title'] . "</h1>";
                    echo "<span style='font-size: 100%;'>" . $row_assignment['course_name'] . "</span><br>";
                    echo "<hr class='hrclass'>";   // display divider
                    echo "<img src='" . $row_assignment['thumbnail_path'] . "' id='thumbnail' alt='Thumbnail'></BR>";
                    echo "<div class='image-container'>";
                   
                    // Checklist
                    echo "<div class='image-wrapper'>";
                    // echo "<a><img alt='Checklist' title='Download Checklist' style='width:80px' class='image-text' src='./forms/icon-checklist.png'></BR>View Checklist</a>"; //THIS IS JUST HERE FOR TESTING
                    $checklist_path = "./assets/img/checklist-pdfs/checklist_$assignment_id.pdf";
                    // if (file_exists($checklist_path)) {
                        echo "<a href='$checklist_path' target='_blank' class='image-text'><img alt='Checklist' title='Download Checklist' src='./forms/icon-checklist.png'></BR>View Checklist</a>";
                    // } else {
                    //     echo ""; //<p>No checklist found for this assignment.</p>
                    // }
                    echo "</div>";
                    $stmt_assignment->close();

                    // Timeline
                    echo "<div class='image-wrapper'>";
                    $stmt_assignment = $conn->prepare("CALL getAssignmentSteps(?)");
                    $stmt_assignment->bind_param("i", $assignment_id);
                    $stmt_assignment->execute();
                    $result_assignment = $stmt_assignment->get_result();

                    if ($result_assignment->num_rows > 0) {
                        $row_assignment = $result_assignment->fetch_assoc(); {
                            echo "<a href='forms/timeline.php?assignment_id=" . $row_assignment["assignment_id"] . "&title=" . urlencode($row_assignment['title']) . "' title='View Timeline' class='image-text' target='_blank'><img alt='Timeline' style='margin-top: 12px;' src='forms/timeline-vector.png'></BR>View Timeline</a>";
                        }
                    } else {
                        echo ""; //<p>No timeline found for this assignment.</p>
                    }
                    echo "</div>"; // Close image-wrapper           
                    echo "</div></BR>";
                    echo "</BR></BR><span id='assignment_description' name='assignment_description'><p>" . $row_assignment['description'] . "</p></span>";

                    // Date inputs
                    echo "<hr class='hrclass'>";
                    echo "<div class='dateContainer'>";
                    echo "<div>"; // Start a div for the first input and span
                    echo "<input type='datetime-local' id='start_date' name='start_date' required><br>";
                    echo "<span>Date to start assignment</span>";
                    echo "</div>"; // End of the first div
                    echo "<div>"; // Start a div for the second input and span
                    echo "<input type='datetime-local' id='end_date' name='end_date' required><br>";
                    echo "<span>Date to end assignment</span>";
                    echo "</div>"; // End of the second div
            
                    // Create Plan button
                    echo "<button type='button' onclick='btnCreate()' id='btnCreate'>Create Plan</button>"; // This allow all users to create a plan but not input in DB
                    //echo "<button type='submit' id='btnCreate'>Create Plan</button>";    // This allows only loggedin users to save progress but is not redirecting right
            
                    echo "</div></br>";
                    echo "<div id='totalDays'></div>";
                } else {
                    echo "<p>Assignment not found.</p>";
                }
                echo "<hr class='hrclass'>";
                $stmt_assignment->close();

                // Call getAssignmentSteps stored procedure
                $sql_steps = "CALL getAssignmentSteps(?)";
                $stmt_steps = $conn->prepare($sql_steps);
                // Bind parameter and execute the stored procedure
                $stmt_steps->bind_param("i", $assignment_id);
                $stmt_steps->execute();
                // Get the result set from the stored procedure execution
                $result_steps = $stmt_steps->get_result();

                // Check if there are any rows returned
                if ($result_steps->num_rows > 0) {
                    echo "<h2>Assignment Steps</h2>";
                    while ($row_step = $result_steps->fetch_assoc()) {
                        echo "<div class='steps'>";
                        echo "<h3 class='step-header'>Step " . $row_step['step_number'] . ":  " . $row_step['title'] . "<span class='carrot'>&#9660;</span></h3>";
                        echo "<div id='step-content'class='step-content'>";
                        echo "<div class='step-content" . $row_step['step_number'] . "'>"; // Add a class for each step content based on step number                       
                        echo "</div>";
                        echo "<div class='assignmentStepsContainer' style='float:right; display:none;'><b> Worth: " . $row_step['percentage'] . "%</b></div>";
                        echo "<div></BR> " . $row_step['description'] . "</div>";
                        if (!empty($row_step['url'])) {
                            echo "<p><a href='" . $row_step['url'] . "'>" . $row_step['url'] . "</a></p>";
                        }
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No steps found for this assignment.</p>";
                }
            } else {
                echo "<p>No assignment ID provided.</p>";
            }
            // Close the statement
            $stmt_steps->close();
            $conn->close();
            ?>
        </form>
        </BR>
        <div class="logout-button">
            <button onclick="window.location.href='./index.php'">Back to Assignments</button>
            <form button type="submit" action="<?php echo $actionUrl; ?>" method="post">
                <button type="submit" onclick="loginRedirect();"><?php echo $actionButton; ?></button>
                </button>
            </form>
        </div>
    </div>
</body>
<footer>
    <small>©
        <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.
    </small>
</footer>

</html>