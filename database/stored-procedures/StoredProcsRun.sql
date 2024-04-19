
USE assignmentplanner;

DELIMITER //

DROP PROCEDURE IF EXISTS editUser
//
DROP PROCEDURE IF EXISTS editAssignment
//
DROP PROCEDURE IF EXISTS editStep
//
DROP PROCEDURE IF EXISTS getAllCourses
//
DROP PROCEDURE IF EXISTS getAllUsers
//
DROP PROCEDURE IF EXISTS getAllRoles
//
DROP PROCEDURE IF EXISTS getAllAssignments
//
DROP PROCEDURE IF EXISTS getAllAssignmentsForInstructor
//
DROP PROCEDURE IF EXISTS getAllAssignmentsForStudent
//
DROP PROCEDURE IF EXISTS getAssignmentDetails
//
DROP PROCEDURE IF EXISTS getUserDetails
//
DROP PROCEDURE IF EXISTS getAssignmentSteps
//
DROP PROCEDURE IF EXISTS insertAssignment
//
DROP PROCEDURE IF EXISTS insertStep
//
DROP PROCEDURE IF EXISTS loginProcedure
//
DROP PROCEDURE IF EXISTS removeAssignment
//
DROP PROCEDURE IF EXISTS verifyAssignment
//
DROP PROCEDURE IF EXISTS getLastAdded
//
DROP PROCEDURE IF EXISTS getStudentCourses
//
DROP PROCEDURE IF EXISTS insertAssignmentPlan
//
DROP PROCEDURE IF EXISTS getAssignmentPlan
//
DROP PROCEDURE IF EXISTS signupProcedure
//
DROP PROCEDURE IF EXISTS updateChecklistPath
//



CREATE PROCEDURE editUser(
    IN p_user_id INT,
    IN p_role VARCHAR(10),
    IN p_name VARCHAR(100),
    IN p_username VARCHAR(100),
    IN p_email VARCHAR(100)
)
BEGIN
    UPDATE User 
    SET user_id = p_user_id, 
        role = p_role, 
        name = p_name, 
        username = p_username,
        email = p_email
    WHERE user_id = p_user_id;
END //



CREATE PROCEDURE editAssignment(
    IN p_assignment_id INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(1200),
    IN p_thumbnail_path VARCHAR(100),
    IN p_deadline DATETIME,
    IN p_course_id INT
)
BEGIN
    UPDATE Assignment 
    SET title = p_title, 
        description = p_description, 
        thumbnail_path = p_thumbnail_path, 
        deadline = p_deadline,
        course_id = p_course_id
    WHERE assignment_id = p_assignment_id;
END //



CREATE PROCEDURE editStep(
    IN p_steps_id INT,
    IN p_step_number INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(1200),
    IN p_url VARCHAR(255),
    IN p_percentage INT
)
BEGIN
    UPDATE AssignmentSteps 
    SET step_number = p_step_number,
        title = p_title, 
        description = p_description,
        url = p_url,
        percentage = p_percentage
    WHERE steps_id = p_steps_id;
END //



CREATE PROCEDURE getAllCourses()
BEGIN
    SELECT course_name, course_id
    FROM Course;
    ORDER BY course_name ASC;
END //



CREATE PROCEDURE getAllUsers()
BEGIN
    SELECT user_id, name, username, role, email
    FROM User;
END //



CREATE PROCEDURE getAllRoles()
BEGIN
    SELECT DISTINCT role
    FROM User;
END //




CREATE PROCEDURE getAllAssignments()
BEGIN
    SELECT a.assignment_id, a.title, a.thumbnail_path, a.checklist_path, a.description, a.deadline, a.verification_status, c.course_name
    FROM Assignment a
    INNER JOIN Course c ON a.course_id = c.course_id;
END //



CREATE PROCEDURE getAllAssignmentsForInstructor(IN instructorID INT)
BEGIN
    SELECT a.assignment_id, a.title, a.thumbnail_path, a.checklist_path, a.description, a.deadline, a.verification_status, c.course_name
    FROM Assignment a
    INNER JOIN Course c ON a.course_id = c.course_id
    WHERE a.user_id = instructorID; 
END //



CREATE PROCEDURE getAllAssignmentsForStudent(IN user_id INT)
BEGIN
    SELECT a.assignment_id, a.title, a.thumbnail_path, a.checklist_path, a.description, c.course_name
    FROM Assignment a
    INNER JOIN Course c ON a.course_id = c.course_id
    WHERE a.user_id = user_id; -- Adjust this condition based on your schema and logic
END //



CREATE PROCEDURE getAssignmentDetails(IN assignmentID INT)
BEGIN
    SELECT a.assignment_id, a.title, a.thumbnail_path, a.checklist_path, a.description, a.deadline, a.user_id, a.course_id, a.verification_status, c.course_name
        FROM Assignment a
        INNER JOIN Course c ON a.course_id = c.course_id 
        WHERE assignment_id = assignmentID;
END //



CREATE PROCEDURE getUserDetails(IN userID INT)
BEGIN
    SELECT user_id, role, name, username, email
        FROM User;
END //



CREATE PROCEDURE getAssignmentSteps(IN assignmentID INT)
BEGIN
    SELECT * FROM AssignmentSteps WHERE assignment_id = assignmentID ORDER BY step_number;
END //



CREATE PROCEDURE insertAssignment(
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(1200),
    IN p_thumbnail_path VARCHAR(100),
    IN p_deadline DATETIME,
    IN p_course_id INT,
    IN p_user_id INT 
)
BEGIN
    INSERT INTO Assignment (title, description, thumbnail_path, deadline, course_id, user_id)
    VALUES (p_title, p_description, p_thumbnail_path, p_deadline, p_course_id, p_user_id); 
END //



CREATE PROCEDURE insertStep(
    IN p_assignment_id INT,
    IN p_title VARCHAR(100),
    IN p_description VARCHAR(1200),
    IN p_percentage INT,
    IN p_url VARCHAR(255),
    IN p_step_number INT
)
BEGIN
    INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number)
    VALUES (p_assignment_id, p_title, p_description, p_percentage, p_url, p_step_number);
    
END //



CREATE PROCEDURE loginProcedure(IN p_username VARCHAR(50))
BEGIN
    SELECT password, user_id, role FROM user WHERE username = p_username;
END //



CREATE PROCEDURE removeAssignment(IN p_assignment_id INT)
BEGIN
    DELETE FROM AssignmentSteps WHERE assignment_id = p_assignment_id;
    
    DELETE FROM Assignment WHERE assignment_id = p_assignment_id;
END //



CREATE PROCEDURE verifyAssignment(IN a_id INT)
BEGIN
    UPDATE assignment
    SET verification_status = 'Approved'
    WHERE assignment_id = a_id;
END //



CREATE PROCEDURE getLastAdded()
BEGIN
    SELECT LAST_INSERT_ID() AS assignment_id;
END //



CREATE PROCEDURE getStudentCourses(IN studentID INT)
BEGIN
    SELECT c.course_name, c.course_id
    FROM Course c
    INNER JOIN UserCourse uc ON c.course_id = uc.course_id
    WHERE uc.user_id = studentID;
END //



CREATE PROCEDURE insertAssignmentPlan(
    IN p_user_id INT,
    IN p_assignment_id INT,
    IN p_start_date DATETIME,
    IN p_end_date DATETIME

)
BEGIN
INSERT INTO assignmentplan (user_id, assignment_id, start_date, end_date)
VALUES (p_user_id, p_assignment_id, p_start_date, p_end_date);
END //



CREATE PROCEDURE getAssignmentPlan(IN student_id INT, assignment_id INT)
BEGIN
    SELECT assignment_plan_id, user_id, a.assignment_id, start_date, end_date, progress
    FROM assignment a
    WHERE user_id = student_id AND a.assignment_id = assignment_id;
END//



CREATE PROCEDURE signupProcedure(
    IN p_username VARCHAR(50),
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(255),
    IN p_role VARCHAR(10)
)
BEGIN
    DECLARE userExists INT;
    SELECT COUNT(*) INTO userExists FROM User WHERE username = p_username;

    IF userExists = 0 THEN
        INSERT INTO User (username, name, email,  password, role) VALUES (p_username, p_name, p_email, p_password, p_role);
    END IF;
END //



CREATE PROCEDURE updateChecklistPath(IN p_assignment_id INT, IN p_checklist_path VARCHAR(255))
BEGIN
    UPDATE Assignment
    SET checklist_path = p_checklist_path
    WHERE assignment_id = p_assignment_id;
END //

