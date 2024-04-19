<?php
session_start();
require_once '../connect.php';
require_once '../../assets/libraries/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Check if assignment ID is provided in the URL
if (isset($_GET['assignment_id']) && !empty($_GET['assignment_id'])) {
    $assignmentId = $_GET['assignment_id'];

    // Initialize DOM-PDF
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $dompdf = new Dompdf($options);

    // Start the HTML content for the PDF
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Assignment Information</title>
    </head>
    <body>';

    // Fetch assignment details using a stored procedure
    if ($stmt = $conn->prepare("CALL getAssignmentDetails(?)")) {
        $stmt->bind_param("i", $assignmentId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $html .= '<h1>Title: ' . htmlspecialchars($row['title']) . '</h1>';
                $html .= '<p>Description: ' . htmlspecialchars($row['description']) . '</p>';
            } else {
                $html .= '<p>Assignment not found.</p>';
            }
        } else {
            echo 'Failed to execute the query: ' . $stmt->error;
            $stmt->close();
            exit;
        }
        $stmt->close();
    } else {
        echo 'Failed to prepare the query: ' . $conn->error;
        exit;
    }

    // Fetch assignment steps using another stored procedure
    if ($stmtSteps = $conn->prepare("CALL getAssignmentSteps(?)")) {
        $stmtSteps->bind_param("i", $assignmentId);
        if ($stmtSteps->execute()) {
            $resultSteps = $stmtSteps->get_result();
            if ($resultSteps->num_rows > 0) {
                $html .= '<h2>Steps:</h2>';
                while ($rowStep = $resultSteps->fetch_assoc()) {
                    $html .= '<h3>' . htmlspecialchars($rowStep['title']) . '</h3>';
                    $html .= '<p>' . htmlspecialchars($rowStep['description']) . '</p>';
                }
            } else {
                $html .= '<p>No steps found for this assignment.</p>';
            }
        } else {
            echo 'Failed to execute the query: ' . $stmtSteps->error;
            $stmtSteps->close();
            exit;
        }
        $stmtSteps->close();
    } else {
        echo 'Failed to prepare the query: ' . $conn->error;
        exit;
    }

    // Close HTML tags
    $html .= '</body></html>';

    // Load the HTML content into DOMPDF
    $dompdf->loadHtml($html);

    // Set the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML to PDF
    $dompdf->render();

    // Stream the generated PDF to the browser
    $dompdf->stream('assignment_information.pdf', ['Attachment' => 0]);
} else {
    echo "Assignment ID is missing.";
}
?>
