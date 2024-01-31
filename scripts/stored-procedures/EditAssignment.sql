DELIMITER //

CREATE PROCEDURE EditAssignment(
    -- Parameters
    IN p_assignment_id INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_thumbnail_path VARCHAR(100),
    IN p_deadline DATETIME
)
BEGIN
    UPDATE Assignment 
    -- Change the title, description, thumbnail path, and deadline of the assignment
    -- based on the input parameters
    SET title = p_title, 
        description = p_description, 
        thumbnail_path = p_thumbnail_path, 
        deadline = p_deadline
    -- Only update the assignment with the given assignment ID
    WHERE assignment_id = p_assignment_id;
END //

DELIMITER ;