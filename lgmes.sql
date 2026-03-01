-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2026 at 08:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lgmes`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `action_by` int(11) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `report_type_id` int(11) DEFAULT NULL,
  `period_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `office_id` int(11) NOT NULL,
  `office_name` varchar(255) NOT NULL,
  `office_type` enum('PROVINCE','CITY','MUNICIPALITY') NOT NULL,
  `cluster` enum('1','2','3') DEFAULT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reporting_period`
--

CREATE TABLE `reporting_period` (
  `period_id` int(11) NOT NULL,
  `period_month` int(11) NOT NULL,
  `period_year` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_requirements`
--

CREATE TABLE `report_requirements` (
  `requirement_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `report_type_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_types`
--

CREATE TABLE `report_types` (
  `report_type_id` int(11) NOT NULL,
  `report_code` varchar(20) NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `opr` varchar(50) DEFAULT NULL,
  `default_deadline_day` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `report_type_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `file_link` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `submission_status` enum('ON_TIME','LATE','ERROR','NO_SUBMISSION','NOT_REQUIRED') DEFAULT 'NO_SUBMISSION',
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `role` enum('SUPER_ADMIN','ADMIN','LGU_OFFICER') NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `action_by` (`action_by`),
  ADD KEY `office_id` (`office_id`),
  ADD KEY `report_type_id` (`report_type_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`office_id`);

--
-- Indexes for table `reporting_period`
--
ALTER TABLE `reporting_period`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `report_requirements`
--
ALTER TABLE `report_requirements`
  ADD PRIMARY KEY (`requirement_id`),
  ADD UNIQUE KEY `office_id` (`office_id`,`report_type_id`,`period_id`),
  ADD KEY `report_type_id` (`report_type_id`),
  ADD KEY `period_id` (`period_id`);

--
-- Indexes for table `report_types`
--
ALTER TABLE `report_types`
  ADD PRIMARY KEY (`report_type_id`),
  ADD UNIQUE KEY `report_code` (`report_code`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD UNIQUE KEY `office_id` (`office_id`,`report_type_id`,`period_id`),
  ADD KEY `report_type_id` (`report_type_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `submitted_by` (`submitted_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `office_id` (`office_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `office_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reporting_period`
--
ALTER TABLE `reporting_period`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_requirements`
--
ALTER TABLE `report_requirements`
  MODIFY `requirement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_types`
--
ALTER TABLE `report_types`
  MODIFY `report_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`action_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`),
  ADD CONSTRAINT `audit_logs_ibfk_3` FOREIGN KEY (`report_type_id`) REFERENCES `report_types` (`report_type_id`),
  ADD CONSTRAINT `audit_logs_ibfk_4` FOREIGN KEY (`period_id`) REFERENCES `reporting_period` (`period_id`);

--
-- Constraints for table `report_requirements`
--
ALTER TABLE `report_requirements`
  ADD CONSTRAINT `report_requirements_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`),
  ADD CONSTRAINT `report_requirements_ibfk_2` FOREIGN KEY (`report_type_id`) REFERENCES `report_types` (`report_type_id`),
  ADD CONSTRAINT `report_requirements_ibfk_3` FOREIGN KEY (`period_id`) REFERENCES `reporting_period` (`period_id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`report_type_id`) REFERENCES `report_types` (`report_type_id`),
  ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (`period_id`) REFERENCES `reporting_period` (`period_id`),
  ADD CONSTRAINT `submissions_ibfk_4` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `offices` (`office_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
