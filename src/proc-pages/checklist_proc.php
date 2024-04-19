<!-- Purpose: process the checklist form and generate a PDF -->
<?php
session_start();
require_once '../connect.php';
require_once '../../assets/libraries/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Check if the form was submitted and the necessary data is available
if (isset($_POST['submit']) && !empty($_POST['assignmentId'])) {
    $assignmentId = $_POST['assignmentId'];
    
    // Fetch steps data from the database
    $stepsDataFromDB = [];
    if ($stmt = $conn->prepare("CALL getAssignmentSteps(?)")) {
        $stmt->bind_param("i", $assignmentId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $stepsDataFromDB[] = $row;
            }
        } else {
            echo "Failed to execute the query: (" . $stmt->errno . ") " . $stmt->error;
            exit;
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the query: (" . $conn->errno . ") " . $conn->error;
        exit;
    }

    // Merge steps data from database with form data
    $stepsDataMerged = $stepsDataFromDB;
    foreach ($_POST['steps'] as $stepId => $step) {
        foreach ($stepsDataMerged as &$mergedStep) {
            if ($mergedStep['steps_id'] == $stepId) {
                $mergedStep['substeps'] = array_filter($step['substeps']); // Remove empty substeps
            }
        }
    }

    // Initialize DOM-PDF
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $dompdf->setOptions($options);

       //html content for the pdf
       $html = '<!DOCTYPE html>
       <html lang="en">
       <head>
           <meta charset="UTF-8">
           <title>Assignment Checklist</title>
           <link rel="stylesheet" href="../../assets/css/dompdfStyles.css">  
       </head>
       <body>';
   
        
       $html .= '<div class="container">
         <h1>Assignment Checklist</h1>';

    //loop through the steps and substeps to generate the checklist
    foreach ($stepsDataMerged as $step) {
        $html .= '<h4>' . htmlspecialchars($step['title']) . '</h4>';
        
        if (!empty($step['description'])) {
            $html .= '<p>' . htmlspecialchars($step['description']) . '</p>';
        }
        
        if (!empty($step['substeps'])) {
            foreach ($step['substeps'] as $substep) {
                if (!empty($substep)) {
                    $html .= '<input type="checkbox"> ' . htmlspecialchars($substep) . '<br>';
                }
            }
        } else {
            // If no substeps, display checkbox next to the step title
            $html .= '<input type="checkbox"> ' . htmlspecialchars($step['title']) . '<br>';
        }
    }

    // Load the HTML content
    $dompdf->loadHtml($html);

    // Set the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML to PDF
    $dompdf->render();

    // Define the relative file path
    $fileName = "checklist_$assignmentId.pdf";
    $filePath = "../../assets/img/checklist-pdfs/" . $fileName;

    // Use the absolute path for file_put_contents operation
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $absolutePath = $rootPath . '/AssignmentPlanner/assets/img/checklist-pdfs/' . $fileName;
    file_put_contents($absolutePath, $dompdf->output());

    // Now, $filePath has the relative path, which will be saved in the database
    if ($stmt = $conn->prepare("CALL updateChecklistPath(?, ?)")) {
        $stmt->bind_param("is", $assignmentId, $filePath); // Bind the relative path here
        if (!$stmt->execute()) {
            echo "Failed to execute the query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the query: " . $conn->error;
    }

    // Redirect or inform the user
    header('Location: ../../assignment_preview.php?assignmentId=' . $assignmentId);
    exit();
} else {
    // Handle invalid access or missing data
    echo "Invalid access or data missing.";
}
?>

