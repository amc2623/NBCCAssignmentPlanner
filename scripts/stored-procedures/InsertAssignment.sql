DELIMITER //

CREATE PROCEDURE InsertAssignment(
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(100),
    IN p_thumbnail_path VARCHAR(100),
    IN p_deadline DATETIME,
    IN p_course_id INT
)
BEGIN
    INSERT INTO Assignment (title, description, thumbnail_path, deadline, course_id)
    VALUES (p_title, p_description, p_thumbnail_path, p_deadline, p_course_id);
END //

DELIMITER ;
