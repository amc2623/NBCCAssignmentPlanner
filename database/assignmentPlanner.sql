DROP DATABASE IF EXISTS assignmentPlanner;
CREATE SCHEMA assignmentPlanner;
USE assignmentPlanner;

CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(10) CHECK (role IN ('Student', 'Instructor', 'Admin'))
);

CREATE TABLE Course (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL
);

CREATE TABLE Assignment (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    thumbnail_path VARCHAR(100), 
    checklist_path VARCHAR(255), /* path to the checklist and other pdfs, placeholder for now */
    description VARCHAR(1200) NOT NULL, /* 1200 character description */
    deadline DATETIME NOT NULL,
    user_id INT, /* instructor id who created the assignment */
    course_id INT,
    verification_status VARCHAR(10) DEFAULT 'Pending', /* Pending/Approved */
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
    description VARCHAR(1200) NOT NULL,
    percentage INT CHECK (percentage >= 1 AND percentage <= 100), /* percentage of the individual step */
    url VARCHAR(255),
    step_number INT,
    FOREIGN KEY (assignment_id) REFERENCES Assignment(assignment_id)
);

/* Table to connect students with courses */
CREATE TABLE UserCourse (
    user_course_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    course_id INT,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (course_id) REFERENCES Course(course_id)
);

/* default values */
INSERT INTO User (username, password, email, name, role) VALUES ('admin', '$2y$10$Nj5psTSRRj7Fq6NE.dLqw.phbgPirq.kcq2wWRDg4zpEmQvcuw5Sy', 'admin@email.com', 'John Admin', 'Admin');
INSERT INTO User (username, password, email, name, role) VALUES ('instructor', '$2y$10$Nj5psTSRRj7Fq6NE.dLqw.phbgPirq.kcq2wWRDg4zpEmQvcuw5Sy', 'instructor@email.com', 'John Smith', 'Instructor');

INSERT INTO User (username, password, email, name, role) VALUES ('student', '$2y$10$Nj5psTSRRj7Fq6NE.dLqw.phbgPirq.kcq2wWRDg4zpEmQvcuw5Sy', 'student@email.com', 'John Doe', 'Student');
INSERT INTO User (username, password, email, name, role) VALUES ('student2', '$2y$10$Nj5psTSRRj7Fq6NE.dLqw.phbgPirq.kcq2wWRDg4zpEmQvcuw5Sy', 'student2@email.com', 'Jane Doe', 'Student');
INSERT INTO User (username, password, email, name, role) VALUES ('student3', '$2y$10$Nj5psTSRRj7Fq6NE.dLqw.phbgPirq.kcq2wWRDg4zpEmQvcuw5Sy', 'student3@email.com', 'Jane Smith', 'Student');

INSERT INTO Course (course_name) VALUES ('SYST 1018');
INSERT INTO Course (course_name) VALUES ('PROG 1197');
INSERT INTO Course (course_name) VALUES ('MNFT 1057');
INSERT INTO Course (course_name) VALUES ('OSSE 1027');
INSERT INTO Course (course_name) VALUES ('DATA 1034');

INSERT INTO Assignment (title, description, deadline, user_id, course_id) VALUES ('Assignment 1', 'Assignment 1 description', '2021-12-01 23:59:59', 1, 1);
INSERT INTO Assignment (title, description, deadline, user_id, course_id) VALUES ('Assignment 2', 'Assignment 2 description', '2021-12-01 23:59:59', 1, 2);
INSERT INTO Assignment (title, description, deadline, user_id, course_id) VALUES ('Assignment 3', 'Assignment 3 description', '2021-12-01 23:59:59', 2, 1);

INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (1, 'Step 1', 'Step 1 description', 25, 'https://www.google.com', 1);
INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (1, 'Step 2', 'Step 2 description', 25, '', 2);
INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (1, 'Step 3', 'Step 3 description', 50, '', 3);

INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (2, 'Step 1', 'Step 1 description', 75, '', 1);
INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (2, 'Step 2', 'Step 2 description', 25, '', 2);

INSERT INTO AssignmentSteps (assignment_id, title, description, percentage, url, step_number) VALUES (3, 'Step 1', 'Step 1 description', 100, '', 1);

INSERT INTO UserCourse (user_id, course_id) VALUES (3, 1);
INSERT INTO UserCourse (user_id, course_id) VALUES (3, 2);
INSERT INTO UserCourse (user_id, course_id) VALUES (4, 3);
INSERT INTO UserCourse (user_id, course_id) VALUES (4, 4);
INSERT INTO UserCourse (user_id, course_id) VALUES (5, 1);