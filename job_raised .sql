-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 20, 2025 at 10:20 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `job_raised`
--

CREATE TABLE `job_raised` (
  `job_id` int NOT NULL,
  `jobnumber_created` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref` varchar(200) NOT NULL,
  `job_type_prefix` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL,
  `date_and_time_raised` datetime DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `job_raised`
--

INSERT INTO `job_raised` (`job_id`, `jobnumber_created`, `ref`, `job_type_prefix`, `description`, `date_and_time_raised`, `client_name`, `target_date`, `priority`) VALUES
(34, 'BTF-N-24-13.2-05', 'REF-BTF-N-24-13.2-05', 'BTF-N-C', 'Chassis Mounted Fuel Tank-New', '2024-03-04 00:00:00', 'Hansagiri', '2024-10-05', 'High'),
(35, 'BTF-N-24-13.2-06', 'REF-BTF-N-24-13.2-06', 'BTF-N-C', 'Chassis Mounted Fuel Tank-New', '2024-03-04 11:58:00', 'Not Known', '2024-07-20', 'High')
;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `job_raised`
--
ALTER TABLE `job_raised`
  ADD PRIMARY KEY (`job_id`),
  ADD UNIQUE KEY `jobnumber_created_2` (`jobnumber_created`),
  ADD UNIQUE KEY `jobnumber_created_3` (`jobnumber_created`),
  ADD KEY `jobnumber_created` (`jobnumber_created`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `job_raised`
--
ALTER TABLE `job_raised`
  MODIFY `job_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12449;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
