-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 19, 2026 at 10:15 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `therapist`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `therapist_id` int DEFAULT NULL,
  `availability_id` int DEFAULT NULL,
  `status` enum('confirmed','cancelled','completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'confirmed',
  `rating` int DEFAULT NULL,
  `report_file` varchar(255) DEFAULT NULL,
  `feedback` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `student_id`, `student_name`, `email`, `therapist_id`, `availability_id`, `status`, `rating`, `report_file`, `feedback`, `created_at`) VALUES
(1, 'IS01082117', 'Muhammad Nabil Bin Muhamad', 'muhammadnabil@gmail.com', 1, 4, 'cancelled', NULL, NULL, NULL, '2025-06-12 18:01:08'),
(2, 'IS01082117', 'Muhammad Nabil Bin Muhamad', 'muhammadnabil@gmail.com', 2, 11, 'cancelled', NULL, NULL, NULL, '2025-06-12 18:04:40'),
(3, 'CS01928382', 'Rizwan Kamaruddin', 'CS01928382@student.uniten.edu.my', 1, 3, 'completed', 3, NULL, '', '2025-06-12 20:44:48'),
(4, 'CS01928382', 'Rizwan Kamaruddin', 'CS01928382@student.uniten.edu.my', 1, 23, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:44:59'),
(5, 'IS01082534', 'Singh Vijay', 'IS01082534@student.uniten.edu.my', 2, 27, 'completed', NULL, NULL, NULL, '2025-06-12 20:45:59'),
(6, 'VM01029392', 'Nadia Zainuddin', 'VM01029392@student.uniten.edu.my', 1, 22, 'completed', NULL, NULL, NULL, '2025-06-12 20:46:39'),
(7, 'VM01029393', 'Ramesh Suresh', 'VM01029393@student.uniten.edu.my', 1, 18, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:47:34'),
(8, 'IS01082537', 'Shabir Yasir', 'IS01082537@student.uniten.edu.my', 1, 23, 'completed', NULL, NULL, NULL, '2025-06-12 20:48:29'),
(9, 'IS01082537', 'Shabir Yasir', 'IS01082537@student.uniten.edu.my', 2, 26, 'completed', NULL, NULL, NULL, '2025-06-12 20:48:37'),
(10, 'IS01082537', 'Shabir Yasir', 'IS01082537@student.uniten.edu.my', 2, 32, 'completed', NULL, NULL, NULL, '2025-06-12 20:48:45'),
(11, 'VM01029399', 'Sundaram Arun', 'VM01029399@student.uniten.edu.my', 1, 7, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:49:44'),
(12, 'VM01029399', 'Sundaram Arun', 'VM01029399@student.uniten.edu.my', 1, 7, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:50:07'),
(13, 'IS01082540', 'Ravi Shankar', 'IS01082540@student.uniten.edu.my', 1, 33, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:50:58'),
(14, 'IS01082540', 'Ravi Shankar', 'IS01082540@student.uniten.edu.my', 1, 33, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:51:14'),
(15, 'IS01082540', 'Ravi Shankar', 'IS01082540@student.uniten.edu.my', 2, 25, 'completed', NULL, NULL, NULL, '2025-06-12 20:51:49'),
(16, 'IS01082540', 'Ravi Shankar', 'IS01082540@student.uniten.edu.my', 1, 20, 'cancelled', NULL, NULL, NULL, '2025-06-12 20:56:49'),
(17, 'IS01082531', 'Nashit Ariff', 'IS01082531@student.uniten.edu.my', 1, 8, 'completed', NULL, NULL, NULL, '2025-06-12 20:58:53'),
(18, 'IS01082531', 'Nashit Ariff', 'IS01082531@student.uniten.edu.my', 1, 6, 'confirmed', NULL, NULL, NULL, '2025-06-12 20:59:14'),
(19, 'IS01082531', 'Nashit Ariff', 'IS01082531@student.uniten.edu.my', 1, 7, 'completed', NULL, NULL, NULL, '2025-06-12 20:59:21'),
(20, 'IS01082531', 'Nashit Ariff', 'IS01082531@student.uniten.edu.my', 1, 22, 'completed', NULL, NULL, NULL, '2025-06-12 21:00:20'),
(21, 'IS01082531', 'Nashit Ariff', 'IS01082531@student.uniten.edu.my', 1, 21, 'completed', NULL, NULL, NULL, '2025-06-12 21:00:49'),
(22, 'CS01928377', 'Arvind Kumar', 'CS01928377@student.uniten.edu.my', 1, 18, 'completed', NULL, NULL, NULL, '2025-06-13 13:39:04'),
(23, 'CS01928377', 'Arvind Kumar', 'CS01928377@student.uniten.edu.my', 1, 19, 'completed', NULL, NULL, NULL, '2025-06-13 13:39:14'),
(24, 'CS01928377', 'Arvind Kumar', 'CS01928377@student.uniten.edu.my', 1, 17, 'completed', NULL, NULL, NULL, '2025-06-13 13:39:43'),
(25, 'CS01928377', 'Arvind Kumar', 'CS01928377@student.uniten.edu.my', 1, 24, 'cancelled', NULL, NULL, NULL, '2025-06-13 13:39:51'),
(26, 'CS01928377', 'Arvind Kumar', 'CS01928377@student.uniten.edu.my', 1, 24, 'completed', NULL, NULL, NULL, '2025-06-13 13:41:46'),
(27, 'IS01082538', 'Ishwar Raja', 'IS01082538@student.uniten.edu.my', 1, 34, 'completed', NULL, NULL, NULL, '2025-06-13 14:08:19'),
(28, 'IS01082525', 'Ahmad Tan', 'IS01082525@student.uniten.edu.my', 2, 15, 'completed', NULL, NULL, NULL, '2025-06-13 19:48:51'),
(29, 'IS01082117', 'Muhammad Nabil Bin Muhamad', 'muhammadnabil@gmail.com', 1, 20, 'cancelled', NULL, NULL, NULL, '2025-06-13 19:59:44'),
(30, 'IS01082117', 'Muhammad Nabil bin Muhammad', 'muhammadnabil@gmail.com', 1, 36, 'completed', 2, 'is01082117_120625_session_report.pdf', '', '2025-06-13 20:34:47'),
(31, 'CS01928382', 'Rizwan Kamaruddin', 'CS01928382@student.uniten.edu.my', 1, 33, 'completed', NULL, NULL, NULL, '2025-06-14 23:05:23'),
(32, 'CS01928382', 'Rizwan Kamaruddin', 'CS01928382@student.uniten.edu.my', 1, 37, 'completed', NULL, NULL, NULL, '2025-06-14 23:08:56'),
(33, 'CS01928382', 'Rizwan Kamaruddin', 'CS01928382@student.uniten.edu.my', 1, 20, 'completed', NULL, NULL, NULL, '2025-06-14 23:29:57'),
(34, 'IS01082525', 'Ahmad Tan', 'IS01082525@student.uniten.edu.my', 1, 38, 'cancelled', NULL, NULL, NULL, '2025-06-15 05:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `id` int NOT NULL,
  `therapist_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('available','passed','booked','disabled') DEFAULT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`id`, `therapist_id`, `date`, `start_time`, `end_time`, `status`, `is_booked`) VALUES
(1, 1, '2025-06-14', '08:00:00', '09:00:00', 'disabled', 0),
(2, 1, '2025-06-14', '10:00:00', '11:00:00', 'disabled', 0),
(3, 1, '2025-06-14', '14:00:00', '15:00:00', 'passed', 0),
(4, 1, '2025-06-14', '16:00:00', '17:00:00', 'passed', 0),
(39, 1, '2025-07-04', '08:00:00', '09:00:00', 'disabled', 0),
(40, 1, '2025-07-10', '16:00:00', '17:00:00', 'disabled', 0),
(7, 1, '2025-06-15', '14:00:00', '15:00:00', 'booked', 0),
(8, 1, '2025-06-15', '16:00:00', '17:00:00', 'booked', 0),
(9, 2, '2025-06-14', '08:00:00', '09:00:00', 'passed', 0),
(10, 2, '2025-06-14', '10:00:00', '11:00:00', 'passed', 0),
(11, 2, '2025-06-14', '14:00:00', '15:00:00', 'available', 0),
(12, 2, '2025-06-14', '16:00:00', '17:00:00', 'available', 0),
(13, 2, '2025-06-15', '08:00:00', '09:00:00', 'available', 0),
(14, 2, '2025-06-15', '10:00:00', '11:00:00', 'passed', 0),
(15, 2, '2025-06-15', '14:00:00', '15:00:00', 'booked', 0),
(16, 2, '2025-06-15', '16:00:00', '17:00:00', 'available', 0),
(17, 1, '2025-06-16', '08:00:00', '09:00:00', 'booked', 0),
(18, 1, '2025-06-16', '10:00:00', '11:00:00', 'booked', 0),
(19, 1, '2025-06-16', '14:00:00', '15:00:00', 'booked', 0),
(20, 1, '2025-06-16', '16:00:00', '17:00:00', 'booked', 0),
(21, 1, '2025-06-17', '08:00:00', '09:00:00', 'booked', 0),
(22, 1, '2025-06-17', '10:00:00', '11:00:00', 'booked', 0),
(23, 1, '2025-06-17', '14:00:00', '15:00:00', 'booked', 0),
(24, 1, '2025-06-17', '16:00:00', '17:00:00', 'booked', 0),
(25, 2, '2025-06-16', '08:00:00', '09:00:00', 'booked', 0),
(26, 2, '2025-06-16', '10:00:00', '11:00:00', 'booked', 0),
(27, 2, '2025-06-16', '14:00:00', '15:00:00', 'booked', 0),
(28, 2, '2025-06-16', '16:00:00', '17:00:00', 'passed', 0),
(29, 2, '2025-06-17', '08:00:00', '09:00:00', 'passed', 0),
(30, 2, '2025-06-17', '10:00:00', '11:00:00', 'available', 0),
(31, 2, '2025-06-17', '14:00:00', '15:00:00', 'available', 0),
(32, 2, '2025-06-17', '16:00:00', '17:00:00', 'booked', 0),
(33, 1, '2025-07-01', '08:00:00', '09:00:00', 'booked', 0),
(34, 1, '2025-07-02', '08:00:00', '09:00:00', 'booked', 0),
(35, 2, '2025-07-02', '08:00:00', '09:00:00', 'available', 0),
(36, 1, '2025-06-12', '08:00:00', '09:00:00', 'passed', 0),
(37, 1, '2025-06-28', '10:00:00', '11:00:00', 'booked', 0),
(38, 1, '2025-07-12', '08:00:00', '09:00:00', 'available', 0),
(41, 1, '2025-06-15', '08:00:00', '09:00:00', 'passed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cancellation_requests`
--

CREATE TABLE `cancellation_requests` (
  `id` int NOT NULL,
  `appointment_id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cancellation_requests`
--

INSERT INTO `cancellation_requests` (`id`, `appointment_id`, `email`, `reason`, `status`, `request_date`) VALUES
(1, 2, 'muhammadnabil@gmail.com', 'Schedule conflict', 'approved', '2025-06-11 18:05:17'),
(2, 4, 'CS01928382@student.uniten.edu.my', 'No longer needed', 'approved', '2025-06-11 20:45:19'),
(3, 7, 'VM01029393@student.uniten.edu.my', 'Personal emergency', 'approved', '2025-06-11 20:47:47'),
(4, 11, 'VM01029399@student.uniten.edu.my', 'Personal emergency', 'approved', '2025-06-11 20:50:00'),
(5, 12, 'VM01029399@student.uniten.edu.my', 'No longer needed', 'approved', '2025-06-11 20:50:19'),
(6, 13, 'IS01082540@student.uniten.edu.my', 'Personal emergency', 'approved', '2025-06-11 20:51:06'),
(7, 25, 'CS01928377@student.uniten.edu.my', 'Schedule conflict', 'approved', '2025-06-12 13:40:25'),
(8, 14, 'IS01082540@student.uniten.edu.my', 'Schedule conflict', 'approved', '2025-06-12 14:09:13'),
(9, 16, 'IS01082540@student.uniten.edu.my', 'Personal emergency', 'approved', '2025-06-12 14:09:18'),
(10, 1, 'muhammadnaabil@gmail.com', 'Not feeling well', 'approved', '2025-06-12 19:56:03'),
(11, 29, 'muhammadnabil@gmail.com', 'No longer needed', 'approved', '2025-06-12 20:22:49'),
(12, 34, 'IS01082525@student.uniten.edu.my', 'No longer needed', 'approved', '2025-06-14 05:30:57');

-- --------------------------------------------------------

--
-- Table structure for table `sample_files`
--

CREATE TABLE `sample_files` (
  `id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sample_files`
--

INSERT INTO `sample_files` (`id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 'report_sample.docx', '/uploads/report_sample.docx', 'pdf', '2025-06-13 04:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `therapists`
--

CREATE TABLE `therapists` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) DEFAULT NULL,
  `bio` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `therapists`
--

INSERT INTO `therapists` (`id`, `name`, `email`, `password`, `photo`, `specialty`, `bio`) VALUES
(1, 'Miss Fatonah binti Mohd Zaidi', 'fatonah@uniten.edu.my', 'Uni10pass!', '../uploads/therapist_684ac732ac9a77.83272627.jpeg', 'Mental health counseling, personality assessment, academic career counseling, family counseling and psychometric testing.', 'Miss Fatonah is a licensed university counselor deeply committed to the emotional and academic well-being of students. With over 3 years of dedicated experience, she specializes in helping students navigate mental health challenges, understand their personality strengths, and make informed academic and career decisions. Her warm and approachable demeanor makes her especially effective in supporting students through family-related stress, academic pressure, and personal development journeys. She also conducts professional psychometric assessments to guide students in discovering their full potential and building confidence in their future path.'),
(2, 'Mr. Muhamad Asmar Bin Omar', 'asmar.omar@uniten.edu.my', 'Uni10pass!', '1747631564_asmar.jpeg', 'Personality assessment, career counseling, addiction issues, peer pressure in university and psychometric testing.', 'Mr. Asmar brings over 12 years of professional experience in student mental health and wellness. A licensed counselor known for his empathetic yet practical approach, he provides in-depth personality profiling and personalized career guidance to help students thrive both academically and personally. His expertise includes addressing sensitive issues such as addiction, low motivation, and peer pressure, creating a safe space where students feel heard and empowered. Mr. Asmar’s strong background in psychometric testing ensures that students receive comprehensive insights to support their growth, self-awareness, and long-term resilience.\r\n\r\n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `therapist_id` (`therapist_id`),
  ADD KEY `availability_id` (`availability_id`);

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `therapist_id` (`therapist_id`);

--
-- Indexes for table `cancellation_requests`
--
ALTER TABLE `cancellation_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sample_files`
--
ALTER TABLE `sample_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapists`
--
ALTER TABLE `therapists`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `cancellation_requests`
--
ALTER TABLE `cancellation_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sample_files`
--
ALTER TABLE `sample_files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `therapists`
--
ALTER TABLE `therapists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
