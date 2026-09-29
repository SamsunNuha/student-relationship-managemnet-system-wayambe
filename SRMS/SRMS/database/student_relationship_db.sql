-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 11:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_relationship_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL DEFAULT 'System Administrator',
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$rwVDfheV9MOoinG8X2EEIO5riAzC0982f2nQ8oayGYKwcHVleanqC', 'System Administrator', 'admin@university.edu', '2026-09-29 07:40:50');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Welcome to Fall Semester 2026', 'We warmly welcome all new and returning students! Orientation events will take place this Thursday at the main auditorium at 10:00 AM.', '2026-09-29 07:40:51'),
(2, 'Midterm Examination Schedule Released', 'The preliminary schedule for midterm examinations has been posted on the notice boards. Please review your departmental boards for exact dates and times.', '2026-09-29 07:40:51'),
(3, 'Library Extended Hours for Finals', 'The main university library will be open 24/7 starting next Monday to assist students during the final examination preparation period.', '2026-09-29 07:40:51'),
(4, 'semstet exam', 'start on december', '2026-09-29 08:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(5, 'Biology'),
(4, 'Chemistry'),
(1, 'Computer Science'),
(2, 'Mathematics'),
(3, 'Physics'),
(6, 'Statistics');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reply` text DEFAULT NULL,
  `status` enum('Pending','Replied') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `student_id`, `subject`, `message`, `reply`, `status`, `created_at`) VALUES
(1, 1, 'Transcript Request', 'Hello Admin, I would like to request an official copy of my academic transcript for internship application purposes.', 'Dear Alice, your transcript request has been processed and is ready for pickup at the Registrar Office during business hours.', 'Replied', '2026-09-29 07:40:51'),
(2, 2, 'Course Change Request', 'Hi, I would like to know if I can switch my minor elective from Statistics 101 to Physics 201 this semester?', NULL, 'Pending', '2026-09-29 07:40:51'),
(3, 3, 'Address Update Confirmation', 'Good day! I have updated my residential address in the system. Kindly confirm it was received correctly.', 'Hello Carol, your updated address has been successfully recorded in our database. No further action needed.', 'Replied', '2026-09-29 07:40:51'),
(4, 1, 'elective subject change', 'Dear admin,\r\n\r\nI would like to request a change of my elective subject from English to Information Technology (IT). I kindly request you to consider my request and make the necessary changes.\r\n\r\nThank you for your consideration.', 'ok within two days i will change', 'Replied', '2026-09-29 08:09:17'),
(5, 7, 'result', 'when released exam result semeseter2', 'will soon', 'Replied', '2026-09-29 09:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(30) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `department` varchar(100) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `enrollment_date` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `full_name`, `dob`, `gender`, `email`, `phone`, `address`, `department`, `academic_year`, `enrollment_date`, `password`, `profile_image`, `created_at`) VALUES
(1, 'STU-2026-001', 'Alice Johnson', '2003-05-14', 'Female', 'alice@student.edu', '0754321234', '123 kolannawa main,colombo-10.', 'Computer Science', '3rd Year', '2024-09-01', '$2y$10$EgzMTwYVvnMDscXMZ7J5TukmXZhyK/wRuAl/UydVWsZ6LkHMD9op2', 'stu_1_1790669228.png', '2026-09-29 07:40:51'),
(2, 'STU-2026-002', 'Bob Smith', '2002-11-22', 'Male', 'bob@student.edu', '555-0102', '456 College St, Science Town', 'Mathematics', '4th Year', '2023-09-01', '$2y$10$5Oj/6iP2DkiIV6pQnmKDGu8Sttcc0YEf2Z2gUseRwUUwUfrKN483a', NULL, '2026-09-29 07:40:51'),
(3, 'STU-2026-003', 'Carol Davis', '2004-02-10', 'Female', 'carol@student.edu', '555-0103', '789 Oak Lane, Academic Village', 'Physics', '2nd Year', '2025-09-01', '$2y$10$5Oj/6iP2DkiIV6pQnmKDGu8Sttcc0YEf2Z2gUseRwUUwUfrKN483a', NULL, '2026-09-29 07:40:51'),
(4, 'STU-2026-004', 'David Wilson', '2003-08-30', 'Male', 'david@student.edu', '555-0104', '321 Pine Road, Innovation Campus', 'Chemistry', '3rd Year', '2024-09-01', '$2y$10$5Oj/6iP2DkiIV6pQnmKDGu8Sttcc0YEf2Z2gUseRwUUwUfrKN483a', NULL, '2026-09-29 07:40:51'),
(5, 'STU-2026-005', 'Emma Brown', '2005-01-18', 'Female', 'emma@student.edu', '555-0105', '654 Maple Street, Metro Campus', 'Biology', '1st Year', '2026-09-01', '$2y$10$5Oj/6iP2DkiIV6pQnmKDGu8Sttcc0YEf2Z2gUseRwUUwUfrKN483a', NULL, '2026-09-29 07:40:51'),
(6, 'STU-2026-006', 'Frank Miller', '2003-04-12', 'Male', 'frank@student.edu', '555-0106', '987 Cedar Way, Research Park', 'Statistics', '3rd Year', '2024-09-01', '$2y$10$5Oj/6iP2DkiIV6pQnmKDGu8Sttcc0YEf2Z2gUseRwUUwUfrKN483a', NULL, '2026-09-29 07:40:51'),
(7, 'STU-2026-007', 'nuha', '2002-05-20', 'Female', 'nuha@gmail.com', '0753360265', 'kalmunai-09,sri lanka', 'Computer Science', '4th Year', '2026-09-29', '$2y$10$o3ktFtFOjJdE2cgrnFPJhOY1EJkxydrxb14jKQQm.8m/ss45kqiA2', NULL, '2026-09-29 09:10:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
