<!-- login.php -->
<!-- Purpose: Provide a login form for users to enter their credentials. -->
<?php include './assets/includes/topBar.php';
session_start();
if (isset($_GET['assignment_id'])) {
    // Get the assignment ID from the URL parameter
    $assignmentId = $_GET['assignment_id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plan your NBCC assignments!" />
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/css/loginStyle.css">
    <link rel="icon" href="./assets/img/logos/favicon.png">
    <script src="./assets/js/functions.js"></script>
    <script>
        // JavaScript function to redirect to login page with assignment ID
        function signupRedirect() {
            // Get the assignment_id from the URL and put it in the session
            var urlParams = new URLSearchParams(window.location.search);
            console.log("URL Parameters:", urlParams.toString()); // Log URL parameters

            var assignmentId = urlParams.get('assignment_id');
            sessionStorage.setItem("assignmentId", assignmentId);

            // If user chooses to login or sign up, once logged in it will redirect them back to the assignment they were on.
            if (assignmentId) {
                sessionStorage.setItem("assignmentId", assignmentId);
                sessionStorage.setItem("redirect", "signup");
                window.location.href = "signup.php?assignment_id=" + assignmentId;
            } else {
                window.location.href = "signup.php";
            }
        }
    </script>
</head>

<body>
    <div>
        <!-- Simple form with dropdown for selecting roles -->
        <form action="./src/proc-pages/login_proc.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <!-- Get assignment_id to redirect back if originally from an assignment -->
            <input type="hidden" name="assignment_id"
                value="<?php echo isset($_GET['assignment_id']) ? $_GET['assignment_id'] : ''; ?>">
            <button type="submit">Login</button>
        </form></BR></BR>

        <button onclick="signupRedirect(this)">Create an account</button></BR></BR>
        <button onclick="window.location.href='./index.php'">Back to Home</button>
    </div>

</body>
<footer>
    <small>©
        <script>document.write(new Date().getFullYear())</script> NBCC. All Rights Reserved.
    </small>
</footer>

</html>