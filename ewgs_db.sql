-- ============================================================
-- EWGS Database Schema (aligned with document assumptions)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================
-- tbl_admin
-- PK renamed to admin_id per document assumptions
-- ============================================================
CREATE TABLE tbl_admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tbl_admin (username, password) VALUES
('admin', '1234');

-- ============================================================
-- tbl_teacher
-- admin_id FK references tbl_admin(admin_id)
-- status: Active/Inactive for account management
-- ============================================================
CREATE TABLE tbl_teacher (
    teacher_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_fname VARCHAR(50) NOT NULL,
    teacher_lname VARCHAR(50) NOT NULL,
    teacher_email VARCHAR(100) NOT NULL UNIQUE,
    teacher_password VARCHAR(255) NOT NULL,
    must_change_password TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = must change on next login',
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES tbl_admin(admin_id) ON DELETE SET NULL
);

-- ============================================================
-- tbl_class
-- Teacher assignment handled via tbl_teacher_class (many-to-many)
-- Supports subject teachers and class advisers in Philippine schools
-- ============================================================
CREATE TABLE tbl_class (
    class_id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(100) NOT NULL,
    grade_level VARCHAR(20) NOT NULL,
    school_year VARCHAR(20) NOT NULL,
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES tbl_admin(admin_id) ON DELETE SET NULL
);

-- ============================================================
-- tbl_teacher_class
-- Junction: one teacher can handle many classes (subject teacher),
-- one class can have many teachers (adviser + subject teachers)
-- ============================================================
CREATE TABLE tbl_teacher_class (
    teacher_class_id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    UNIQUE KEY unique_teacher_class (teacher_id, class_id),
    FOREIGN KEY (teacher_id) REFERENCES tbl_teacher(teacher_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES tbl_class(class_id) ON DELETE CASCADE
);

-- ============================================================
-- tbl_subject
-- ============================================================
CREATE TABLE tbl_subject (
    subject_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_name VARCHAR(100) NOT NULL,
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES tbl_admin(admin_id) ON DELETE SET NULL
);

-- ============================================================
-- tbl_subject_class
-- Links subjects to classes (many-to-many)
-- PK renamed to subject_class_id per document assumptions
-- ============================================================
CREATE TABLE tbl_subject_class (
    subject_class_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    class_id INT NOT NULL,
    UNIQUE KEY unique_subject_class (subject_id, class_id),
    FOREIGN KEY (subject_id) REFERENCES tbl_subject(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES tbl_class(class_id) ON DELETE CASCADE
);

-- ============================================================
-- tbl_student
-- student_lrn: replaces lrn (document assumption)
-- student_gender: replaces gender (document assumption)
-- birth_date: replaces age (document assumption)
-- Direct class_id FK (nullable) on tbl_student — students imported first,
-- assigned to a class later. 1 student = 1 class only.
-- ============================================================
CREATE TABLE tbl_student (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    student_fname VARCHAR(50) NOT NULL,
    student_lname VARCHAR(50) NOT NULL,
    student_lrn VARCHAR(20) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    student_gender ENUM('Male', 'Female') NOT NULL,
    class_id INT DEFAULT NULL COMMENT 'Nullable: assigned to a class later',
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES tbl_class(class_id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES tbl_admin(admin_id) ON DELETE SET NULL
);

-- ============================================================
-- tbl_grading_component
-- Defines WW / PT / QA weights per subject
-- Kept as separate table (better than embedding % on tbl_subject)
-- because it allows flexible activity counts per component
-- ============================================================
CREATE TABLE tbl_grading_component (
    component_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    component_name ENUM('Written Work', 'Performance Task', 'Quarterly Exam') NOT NULL,
    percentage DECIMAL(5,2) NOT NULL COMMENT 'Weight as decimal e.g. 0.30 = 30%',
    activity_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES tbl_subject(subject_id) ON DELETE CASCADE
);

-- ============================================================
-- tbl_activity
-- Individual activities per grading component (Quiz 1, etc.)
-- ============================================================
CREATE TABLE tbl_activity (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    component_id INT NOT NULL,
    activity_name VARCHAR(100) NOT NULL COMMENT 'e.g., Quiz 1, Project 1',
    perfect_score DECIMAL(10,2) NOT NULL COMMENT 'Maximum score for this activity',
    activity_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (component_id) REFERENCES tbl_grading_component(component_id) ON DELETE CASCADE
);

-- ============================================================
-- tbl_student_score
-- Raw per-activity scores per student
-- ============================================================
CREATE TABLE tbl_student_score (
    score_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    activity_id INT NOT NULL,
    score DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    quarter ENUM('1st', '2nd', '3rd', '4th') NOT NULL,
    school_year VARCHAR(20) NOT NULL COMMENT 'e.g., 2024-2025',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_activity_quarter (student_id, activity_id, quarter, school_year),
    FOREIGN KEY (student_id) REFERENCES tbl_student(student_id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES tbl_activity(activity_id) ON DELETE CASCADE
);

-- ============================================================
-- tbl_grade
-- Computed quarterly grades per student per subject-class per quarter
-- Uses subject_class_id FK (references tbl_subject_class) per document
-- UNIQUE on (student_id, subject_class_id, quarter)
-- ============================================================
CREATE TABLE tbl_grade (
    grade_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_class_id INT NOT NULL,
    quarter ENUM('1st', '2nd', '3rd', '4th') NOT NULL,
    written_work DECIMAL(5,2) DEFAULT 0.00,
    performance_task DECIMAL(5,2) DEFAULT 0.00,
    quarterly_exam DECIMAL(5,2) DEFAULT 0.00,
    final_grade DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Computed weighted grade',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_grade (student_id, subject_class_id, quarter),
    FOREIGN KEY (student_id) REFERENCES tbl_student(student_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_class_id) REFERENCES tbl_subject_class(subject_class_id) ON DELETE CASCADE
);
