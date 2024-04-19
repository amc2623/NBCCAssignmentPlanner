// functions.js

document.addEventListener('DOMContentLoaded', function () {
    // Attach event listeners to existing percentage inputs
    if (document.querySelector('[name^="step_percentage"]')) {
    var existingPercentageInputs = document.querySelectorAll('[name^="step_percentage"]');
    existingPercentageInputs.forEach(function (input) {
        input.addEventListener('input', updateRemainingPercentage);
    });

    // Update the remaining percentage on page load
    updateRemainingPercentage();
}
});

function addStep() {
    console.log('Add button clicked'); // Check if the function is being called
    var stepsContainer = document.getElementById('steps-container');
    var currentCount = stepsContainer.childElementCount + 1;

    // Clone the first step without copying input values
    var clone = document.querySelector('.step').cloneNode(true);

    // Clear input values in the cloned step
    clone.querySelector('[name^="step_title"]').value = '';
    clone.querySelector('[name^="step_description"]').value = '';
    clone.querySelector('[name^="step_percentage"]').value = '';
    clone.querySelector('[name^="step_url"]').value = '';

    // Update the step number for the cloned step
    clone.querySelector('[name^="step_number"]').value = currentCount;

    // Update the IDs for the cloned step
    clone.dataset.stepIndex = currentCount;
    clone.querySelector('[for^="step_title"]').htmlFor = 'step_title_' + currentCount;
    clone.querySelector('[name^="step_title"]').name = 'step_title_' + currentCount;
    clone.querySelector('[for^="step_description"]').htmlFor = 'step_description_' + currentCount;
    clone.querySelector('[name^="step_description"]').name = 'step_description_' + currentCount;
    clone.querySelector('[for^="step_percentage"]').htmlFor = 'step_percentage_' + currentCount;
    clone.querySelector('[name^="step_percentage"]').name = 'step_percentage_' + currentCount;
    clone.querySelector('[for^="step_url"]').htmlFor = 'step_url_' + currentCount;
    clone.querySelector('[name^="step_url"]').name = 'step_url_' + currentCount;
    clone.querySelector('[for^="step_number"]').htmlFor = 'step_number_' + currentCount;
    clone.querySelector('[name^="step_number"]').name = 'step_number_' + currentCount;

    // Append the cloned step to the steps-container
    stepsContainer.appendChild(clone);

    // Attach event listener to the new percentage input
    var newPercentageInput = clone.querySelector('[name^="step_percentage"]');
    newPercentageInput.addEventListener('input', updateRemainingPercentage);

    // Update the remaining percentage
    updateRemainingPercentage();
}

// Remove Step function
function removeStep(button) {
    // Get the parent element of the button (which is the step)
    var step = button.parentNode;

    // Get the step index from the data-step-index attribute of the step
    var stepIndex = step.dataset.stepIndex;

    var stepsContainer = document.getElementById('steps-container');
    var stepToRemove = document.querySelector('.step[data-step-index="' + stepIndex + '"]');

    if (stepsContainer.childElementCount > 1) {
        stepToRemove.parentNode.removeChild(stepToRemove);

        // Update step indices after removal  
        updateStepIndices();

        // Update the remaining percentage
        updateRemainingPercentage();
    }
}


function updateStepIndices() {
    var steps = document.querySelectorAll('.step');
    steps.forEach(function(step, index) {
        var stepIndex = index + 1; // Step index starts from 1
        step.dataset.stepIndex = stepIndex; // Update the data-step-index attribute
        
        var inputs = step.querySelectorAll('input, textarea');
        inputs.forEach(function(input) {
            var name = input.getAttribute('name');
            // Update all occurrences of the step index in the name attribute
            input.setAttribute('name', name.replace(/\d+/g, stepIndex));
        });

        var labels = step.querySelectorAll('label');
        labels.forEach(function(label) {
            var htmlFor = label.getAttribute('for');
            // Update all occurrences of the step index in the for attribute
            label.setAttribute('for', htmlFor.replace(/\d+/g, stepIndex));
        });
    });
}



function updateRemainingPercentage() {
    var steps = document.querySelectorAll('[name^="step_percentage"]');
    var warningMessage = document.getElementById('percentage_warning');

    var totalPercentage = 0;

    steps.forEach(function (step) {
        totalPercentage += parseInt(step.value) || 0;
    });

    var remainingPercentage = Math.max(100 - totalPercentage, 0);

    // Update the remaining percentage display
    document.getElementById('remaining_percentage').innerText = remainingPercentage;

    var overPercentage = totalPercentage - 100;

    if (overPercentage > 0) {
        warningMessage.innerText = 'Warning: Exceeded by ' + overPercentage + '%.';
    } else {
        warningMessage.innerText = '';
    }

    updatePercentageColor();
}

function validateTotalPercentage() {
    var remainingPercentage = parseInt(document.getElementById('remaining_percentage').innerText);

    if (remainingPercentage !== 0) {
        alert('Total percentage must be 100%. Please adjust your step percentages.');
        return false;
    }
    
    var totalPercentage = 100;
    var steps = document.querySelectorAll('[name^="step_percentage"]');

    steps.forEach(function (step) {
        totalPercentage -= parseInt(step.value) || 0;
    });

    if (totalPercentage !== 0) {
        alert('Total percentage must be 100%. Please adjust your step percentages.');
        return false;
    }

    // Display the confirmation prompt
    var confirmation = confirm('Do you want to submit?');
    
    // Return true if the user confirms, false otherwise
    return confirmation;
}

function goBack() {
    // Check if any step inputs have been filled out
    var steps = document.querySelectorAll('.step');
    for (var i = 0; i < steps.length; i++) {
        var inputs = steps[i].querySelectorAll('input, textarea');
        for (var j = 0; j < inputs.length; j++) {
            if (inputs[j].value.trim() !== '') {
                // If any input has a value, confirm before going back
                if (confirm('There are unsaved changes. Are you sure you want to go back?')) {
                    window.location.href = './create_assignment.php';
                }
                return;
            }
        }
    }
    // If no inputs are filled, simply go back
    window.location.href = './create_assignment.php';
}

function updatePercentageColor() {
    var remainingPercentageText = document.getElementById('remaining_percentage').innerText;
    console.log("remainingPercentageText: ", remainingPercentageText);
    var remainingPercentage = parseInt(remainingPercentageText);
    console.log("remainingPercentage: ", remainingPercentage);
    var percentageElement = document.getElementById('remaining_percentage');
    
    if (remainingPercentage >= 51 && remainingPercentage <= 100) {
        percentageElement.classList.remove('yellow');
        percentageElement.classList.remove('red');
        percentageElement.classList.add('green');
    } else if (remainingPercentage >= 1 && remainingPercentage <= 50) {
        percentageElement.classList.remove('green');
        percentageElement.classList.remove('red');
        percentageElement.classList.add('yellow');
    } else if (remainingPercentage == 0) {
        percentageElement.classList.remove('yellow');
        percentageElement.classList.remove('green');
        percentageElement.classList.add('red');
    }
}

      //function to filter assignments by course
      // If "All" is selected, remove the course from URL, otherwise add it
function filterAssignments() {
    var selectedCourse = document.getElementById("course_name").value;
    if (selectedCourse === "All") {
        window.location.href = "manage_assignments.php";
    } else {
    window.location.href = "manage_assignments.php?course_name=" + encodeURIComponent(selectedCourse);
    }
}

// filter users by role
function filterRoles() {
    var selectedRole = document.getElementById("role").value;
    var roles = document.querySelectorAll(".role");
    if (selectedRole === "All") {
        window.location.href = "manage_users.php";
    } else {
    window.location.href = "manage_users.php?role=" + encodeURIComponent(selectedRole);
    }
}
    
    //feel free to add else if statements if you wanna use this for other pages
    function confirmPrompt() {
        var page = window.location.pathname;
        var message = "";
    
        if (page.includes("create_assignment.php")) {
            message = "Are you sure you want to create this assignment?";
        } else if (page.includes("edit_user.php")) {
            message = "Do you want to confirm these changes?";
            if (!confirm(message)) {
                return false;
            }
            window.location.href = "manage_users.php";
        } else if (page.includes("edit_assignment.php")) {
            message = "Do you want to confirm these changes?";
            if (!confirm(message)) {
                return false;
            }
            window.location.href = "manage_assignments.php";
        } else if (page.includes("checklist.php")) {
            message = "Do you want to create this checklist?";
            if (!confirm(message)) {
                return false;
            }
            // window.location.href = "manage_assignments.php";
            header('Location: ../../assignment_preview.php?assignmentId=' . $assignmentId);
        } else {
            message = "Please confirm this action.";
        }
    
        return true; // Returning true to allow form submission or link click if no confirmation message is displayed
    }

function validateForm() {
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    if (password != confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }
    return true;
}

function signupRedirect() {
    window.location.href = 'signup.php';
}