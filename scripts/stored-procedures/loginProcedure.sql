DELIMITER //

CREATE DEFINER=root@localhost PROCEDURE loginProcedure(
    IN loginType VARCHAR(20),
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    OUT userID INT,
    OUT userRole VARCHAR(20)
)
BEGIN
    DECLARE userCount INT;

    -- Choose the appropriate role based on the login type
    SELECT COUNT(*) INTO userCount FROM user WHERE username = p_username AND password = p_password AND role = loginType;

    IF userCount = 1 THEN
        SELECT user_id, role INTO userID, userRole FROM user WHERE username = p_username;
    ELSE
        SET userID = 0;
        SET userRole = '';
    END IF;
END //

DELIMITER ;

