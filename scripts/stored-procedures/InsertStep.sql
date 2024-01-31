DELIMITER //

CREATE PROCEDURE InsertStep(
    IN p_assignment_id INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_percentage INT,
    IN p_url VARCHAR(255)
)
BEGIN
    INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url)
    VALUES (p_assignment_id, p_title, p_description, p_percentage, p_url);
    
END //

DELIMITER ;
