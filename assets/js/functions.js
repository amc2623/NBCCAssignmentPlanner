// functions.js

document.addEventListener('DOMContentLoaded', function () {
    // Attach event listeners to existing percentage inputs
    var existingPercentageInputs = document.querySelectorAll('[name^="step_percentage"]');
    existingPercentageInputs.forEach(function (input) {
        input.addEventListener('input', updateRemainingPercentage);
    });
});

function addStep() {
    var stepsContainer = document.getElementById('steps-container');
    var currentCount = stepsContainer.childElementCount + 1;

    // Clone the first step without copying input values
    var clone = document.querySelector('.step').cloneNode(true);

    // Clear input values in the cloned step
    clone.querySelector('[name^="step_title"]').value = '';
    clone.querySelector('[name^="step_description"]').value = '';
    clone.querySelector('[name^="step_percentage"]').value = '';
    clone.querySelector('[name^="step_url"]').value = ''; // Clear step_url value

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

    // Append the cloned step to the steps-container
    stepsContainer.appendChild(clone);

    // Attach event listener to the new percentage input
    var newPercentageInput = clone.querySelector('[name^="step_percentage"]');
    newPercentageInput.addEventListener('input', updateRemainingPercentage);

    // Update the remaining percentage
    updateRemainingPercentage();
}

function removeStep(stepIndex) {
    var stepsContainer = document.getElementById('steps-container');
    var stepToRemove = document.querySelector('.step[data-step-index="' + stepIndex + '"]');

    if (stepsContainer.childElementCount > 1) {
        stepsContainer.removeChild(stepToRemove);

        // Update step indices after removal
        var steps = document.querySelectorAll('.step');
        steps.forEach(function (step, index) {
            step.dataset.stepIndex = index + 1;
        });

        // Update the remaining percentage
        updateRemainingPercentage();
    }
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

    return true;
}
