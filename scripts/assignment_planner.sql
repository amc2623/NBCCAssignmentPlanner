DROP DATABASE IF EXISTS assignment_planner;
CREATE DATABASE assignment_planner;
USE assignment_planner;

CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(10) CHECK (role IN ('Student', 'Instructor', 'Admin')) /* Student, Instructor, Admin */
);

CREATE TABLE Course (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL
);

CREATE TABLE Assignment (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    thumbnail_path VARCHAR(100) NOT NULL, /* assignment thumbnail (First letter) */
    checklist_path VARCHAR(255), /* path to the checklist and other pdfs, placeholder for now */
    description VARCHAR(100) NOT NULL, /* 100 character description */
    deadline DATETIME NOT NULL,
    user_id INT, /* instructor id who created the assignment */
    course_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (course_id) REFERENCES Course(course_id)
);

CREATE TABLE AssignmentPlan (
    assignment_plan_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, /* student id who created the plan */
    assignment_id INT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    progress INT CHECK (progress >= 0 AND progress <= 100), /* progress of the assignment completed */
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (assignment_id) REFERENCES Assignment(assignment_id)
);

CREATE TABLE AssignmentSteps (
    steps_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    percentage INT CHECK (percentage >= 1 AND percentage <= 100), /* percentage of the individual step */
    url VARCHAR(255),
    verification_status VARCHAR(20) DEFAULT 'Pending',
    FOREIGN KEY (assignment_id) REFERENCES Assignment(assignment_id)
);



INSERT INTO User (username, password, email, name, role) VALUES ('student', 'password123', 'student@email.com', 'John Doe', 'Student');
INSERT INTO User (username, password, email, name, role) VALUES ('instructor', 'password123', 'instructor@email.com', 'John Smith', 'Instructor');
INSERT INTO User (username, password, email, name, role) VALUES ('admin', 'password123', 'admin@email.com', 'John Admin', 'Admin');

INSERT INTO Assignment (title, thumbnail_path, checklist_path, description, deadline, user_id) VALUES ('Assignment 1', 'path/to/thumbnail.png', 'path/to/checklist.pdf', 'Description for Assignment 1', '2024-10-10 12:00:00', 1);

INSERT INTO AssignmentPlan (user_id, assignment_id, start_date, end_date, progress) VALUES (1, 1, '2024-10-10 08:00:00', '2024-10-15 18:00:00', 1);

INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, verification_status) VALUES (1, 'Step 1', 'Description for Step 1', 20, 'www.google.com', 'Pending');
INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, verification_status) VALUES (1, 'Step 2', 'Description for Step 2', 20, null, 'Pending');
INSERT INTO Course (course_name) VALUES ('ITPA');


