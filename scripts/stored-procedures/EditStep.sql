DELIMITER //

CREATE PROCEDURE EditStep(
    -- Parameters
    IN p_step_id INT,
    IN p_step_number INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
)
BEGIN
    UPDATE Step 
    -- Change the step number, title, and description of the step
    -- based on the input parameters
    SET step_number = p_step_number,
        title = p_title, 
        description = p_description
    -- Only update the step with the given step ID
    WHERE step_id = p_step_id;
END //

DELIMITER ;