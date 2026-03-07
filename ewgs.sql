-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 14, 2025 at 06:36 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ewgs`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity`
--

CREATE TABLE `tbl_activity` (
  `activity_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  `activity_name` varchar(100) NOT NULL COMMENT 'e.g., Quiz 1, Project 1, etc.',
  `perfect_score` decimal(10,2) NOT NULL COMMENT 'Maximum score for this activity',
  `activity_order` int(11) NOT NULL DEFAULT 1 COMMENT 'Display order',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class`
--

CREATE TABLE `tbl_class` (
  `id` int(11) NOT NULL,
  `class_name` text NOT NULL,
  `grade_level` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grades`
--

CREATE TABLE `tbl_grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `quarter` enum('1st','2nd','3rd','4th') NOT NULL,
  `written_task` decimal(5,2) DEFAULT 0.00,
  `performance_task` decimal(5,2) DEFAULT 0.00,
  `quarterly_exam` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grading_component`
--

CREATE TABLE `tbl_grading_component` (
  `component_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `component_name` varchar(50) NOT NULL COMMENT 'Written Work, Performance Task, Quarterly Exam',
  `percentage` decimal(5,2) NOT NULL COMMENT 'Percentage weight (0.00 to 1.00)',
  `activity_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of activities for this component',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student`
--

CREATE TABLE `tbl_student` (
  `id` int(100) NOT NULL,
  `fullname` text NOT NULL,
  `age` text NOT NULL,
  `lrn` text NOT NULL,
  `gender` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_class`
--

CREATE TABLE `tbl_student_class` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_score`
--

CREATE TABLE `tbl_student_score` (
  `score_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `score` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quarter` enum('1st','2nd','3rd','4th') NOT NULL,
  `school_year` varchar(20) NOT NULL COMMENT 'e.g., 2024-2025',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subject`
--

CREATE TABLE `tbl_subject` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `written_task` decimal(5,2) NOT NULL,
  `performance_task` decimal(5,2) NOT NULL,
  `quarterly_exam` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subject_class`
--

CREATE TABLE `tbl_subject_class` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_teacher`
--

CREATE TABLE `tbl_teacher` (
  `id` int(50) NOT NULL,
  `fullname` text NOT NULL,
  `email` text NOT NULL,
  `school_name` text DEFAULT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_activity`
--
ALTER TABLE `tbl_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_component` (`component_id`);

--
-- Indexes for table `tbl_class`
--
ALTER TABLE `tbl_class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_grades_student` (`student_id`),
  ADD KEY `fk_grades_subject` (`subject_id`),
  ADD KEY `fk_grades_class` (`class_id`);

--
-- Indexes for table `tbl_grading_component`
--
ALTER TABLE `tbl_grading_component`
  ADD PRIMARY KEY (`component_id`),
  ADD KEY `idx_subject` (`subject_id`);

--
-- Indexes for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_student_class`
--
ALTER TABLE `tbl_student_class`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student` (`student_id`),
  ADD KEY `fk_class` (`class_id`);

--
-- Indexes for table `tbl_student_score`
--
ALTER TABLE `tbl_student_score`
  ADD PRIMARY KEY (`score_id`),
  ADD UNIQUE KEY `unique_student_activity_quarter` (`student_id`,`activity_id`,`quarter`,`school_year`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_activity` (`activity_id`);

--
-- Indexes for table `tbl_subject`
--
ALTER TABLE `tbl_subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `tbl_subject_class`
--
ALTER TABLE `tbl_subject_class`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subject_class` (`subject_id`,`class_id`),
  ADD KEY `fk_subject_class_class` (`class_id`);

--
-- Indexes for table `tbl_teacher`
--
ALTER TABLE `tbl_teacher`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_activity`
--
ALTER TABLE `tbl_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `tbl_class`
--
ALTER TABLE `tbl_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tbl_grading_component`
--
ALTER TABLE `tbl_grading_component`
  MODIFY `component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tbl_student`
--
ALTER TABLE `tbl_student`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_student_class`
--
ALTER TABLE `tbl_student_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tbl_student_score`
--
ALTER TABLE `tbl_student_score`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `tbl_subject`
--
ALTER TABLE `tbl_subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_subject_class`
--
ALTER TABLE `tbl_subject_class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tbl_teacher`
--
ALTER TABLE `tbl_teacher`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_activity`
--
ALTER TABLE `tbl_activity`
  ADD CONSTRAINT `fk_activity_component` FOREIGN KEY (`component_id`) REFERENCES `tbl_grading_component` (`component_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_grades`
--
ALTER TABLE `tbl_grades`
  ADD CONSTRAINT `fk_grades_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_class` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_grades_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_grades_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subject` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_grading_component`
--
ALTER TABLE `tbl_grading_component`
  ADD CONSTRAINT `fk_component_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subject` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_student_class`
--
ALTER TABLE `tbl_student_class`
  ADD CONSTRAINT `fk_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_class` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `tbl_student` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_subject_class`
--
ALTER TABLE `tbl_subject_class`
  ADD CONSTRAINT `fk_subject_class_class` FOREIGN KEY (`class_id`) REFERENCES `tbl_class` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subject_class_subject` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subject` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
