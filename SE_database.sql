create database SE_database;
use SE_database;

-- testing purpose
SET SQL_SAFE_UPDATES = 0;
-- select * from task;
-- select * from user;
-- truncate table task;

CREATE TABLE user (
	user_id int AUTO_INCREMENT PRIMARY KEY,
	user_name varchar(50) DEFAULT NULL,
	user_password varchar(100) DEFAULT NULL,
	user_email varchar(50) DEFAULT NULL,
	user_status enum ('basic', 'premium') DEFAULT 'basic',
    user_block_status enum ('block','unblock') DEFAULT 'unblock',
	user_image blob DEFAULT NULL,
	user_verify_token varchar(255) DEFAULT NULL,
	user_reg_date datetime DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE admin (
	admin_id int AUTO_INCREMENT PRIMARY KEY,
	admin_name varchar(50) DEFAULT NULL,
	admin_password varchar(255) DEFAULT NULL,
	admin_email varchar(100) DEFAULT NULL,
	avatar_data LONGBLOB DEFAULT NULL,
	admin_verify_token varchar(255) DEFAULT NULL,
	admin_reg_date datetime DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE helpdesk(
	helpdesk_id int AUTO_INCREMENT PRIMARY KEY,
	helpdesk_name varchar(50) DEFAULT NULL,
	helpdesk_password varchar(100) DEFAULT NULL,
	helpdesk_email varchar(50) DEFAULT NULL,
	helpdesk_image LONGBLOB DEFAULT NULL,
	helpdesk_verify_token varchar(255) DEFAULT NULL,
	helpdesk_reg_date datetime DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE task (
    task_id int AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    task_type enum ('work', 'personal', 'household', 'fitness'),     
    task_title VARCHAR(100) DEFAULT NULL,  
    task_description TEXT DEFAULT NULL,                              
    task_start_date datetime DEFAULT CURRENT_TIMESTAMP,  
    task_end_date datetime DEFAULT CURRENT_TIMESTAMP, 
    task_status enum ('in progress', 'done', 'over') DEFAULT 'in progress',        
    task_recurring enum ('0', '1', '7', '30') DEFAULT '0',
    task_reminder_time enum ('0', '15', '30', '60') DEFAULT '0',
    task_reminder_status enum ('none', 'pending', 'sent') DEFAULT 'none',
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
);


SET GLOBAL event_scheduler = ON;

CREATE EVENT update_task_status
ON SCHEDULE EVERY 1 MINUTE
DO
UPDATE task
SET task_status = 'over'
WHERE CURRENT_TIMESTAMP() >= task_end_date
AND task_status = 'in progress';

-- DROP EVENT IF EXISTS update_task_status;
-- SHOW EVENTS;


CREATE TABLE special_day (
	special_day_id int AUTO_INCREMENT PRIMARY KEY,      
    special_day_content text DEFAULT NULL,
    special_day_date datetime DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO special_day (special_day_content, special_day_date) VALUES
('New Year Celebration', '2025-02-02 00:00:00'),
('Valentine''s Day', '2025-02-03 00:00:00'),
('Company Anniversary', '2025-03-10 00:00:00'),
('Project Deadline', '2025-04-25 00:00:00'),
('Independence Day', '2025-07-04 00:00:00'),
('Family Gathering', '2025-08-15 00:00:00'),
('Halloween Party', '2025-10-31 00:00:00'),
('Christmas Eve', '2025-12-24 00:00:00'),
('Christmas Day', '2025-12-25 00:00:00'),
('New Year''s Eve', '2025-12-31 00:00:00');

CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    feedback_content TEXT NOT NULL,
    feedback_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id) 
);

-- Announcement 
CREATE TABLE announcement (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_tittle TEXT NOT NULL,
    announcement_content TEXT NOT NULL,
    announcement_date DATE NOT NULL
);

CREATE TABLE question (
    question_id INT AUTO_INCREMENT PRIMARY KEY,      
    question_content TEXT DEFAULT NULL,
    question_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('done', 'pending') DEFAULT 'pending',
    user_id INT,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES user(user_id)
);


CREATE TABLE FAQ (
    faq_id INT AUTO_INCREMENT PRIMARY KEY,
    faq_question TEXT NOT NULL,
    faq_content TEXT NOT NULL
);