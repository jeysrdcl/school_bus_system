-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2025 at 10:43 PM
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
-- Database: `school_bus_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus_records`
--

CREATE TABLE `bus_records` (
  `id` bigint(20) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `conductor_id` int(11) NOT NULL,
  `direction` varchar(255) NOT NULL,
  `status` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `student` int(11) DEFAULT NULL,
  `actvity_type` enum('PICK-UP','DROP-OFF') DEFAULT NULL,
  `current_load` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bus_table`
--

CREATE TABLE `bus_table` (
  `id` int(11) NOT NULL,
  `bus_name` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `bus_type` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_table`
--

INSERT INTO `bus_table` (`id`, `bus_name`, `plate_number`, `bus_type`, `capacity`, `max_capacity`, `status`) VALUES
(1, 'BUS A', 'ABC-123', 'Mini Bus', 20, 30, 'ACTIVE'),
(2, 'BUS B', 'XYZ-456', 'Service', 10, 15, 'ACTIVE'),
(3, 'BUS C', 'XTY-456', 'Mini Bus', 20, 30, 'ACTIVE'),
(4, 'BUS D', 'XTVY-4567', 'Mini Bus', 20, 30, 'ACTIVE'),
(6, 'BUS C', 'XTY-4567', 'Mini Bus', 20, 30, 'ACTIVE'),
(8, 'BUS E', 'QWE-852', 'Service', 12, 20, 'ACTIVE'),
(9, 'BUS E', 'QWE-851', 'Service', 12, 20, 'ACTIVE'),
(10, 'BUS E', 'QWE-853', 'Service', 12, 20, 'ACTIVE'),
(11, 'BUS E', 'QWE-854', 'Service', 12, 20, 'ACTIVE'),
(12, 'BUS E', 'QWE-855', 'Service', 12, 20, 'ACTIVE'),
(13, 'BUS E', 'QWE-856', 'Service', 12, 20, 'ACTIVE'),
(14, 'BUS E', 'QWE-857', 'Service', 12, 20, 'ACTIVE'),
(15, 'BUS E', 'QWE-858', 'Service', 12, 20, 'ACTIVE'),
(16, 'BUS G', 'QWE-859', 'Service', 12, 20, 'ACTIVE'),
(17, 'BUS E', 'QWE-850', 'Service', 12, 20, 'ACTIVE'),
(18, 'BUS E', 'QWE-861', 'Service', 12, 20, 'ACTIVE'),
(19, 'BUS E', 'QWE-862', 'Service', 12, 20, 'ACTIVE'),
(20, 'BUS E', 'QWE-863', 'Service', 12, 20, 'ACTIVE'),
(21, 'BIG BUS 1', 'QWE-867', 'Mini Bus', 25, 35, 'INACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`) VALUES
(1, 'Administrator', 'admin@example.com', '0000 000 0000', '$2y$10$0..7abUDgR74PIe.gq5VpecsM3l7NFDAEsAr79qW.Bea/n3bFRunO', 'admin'),
(2, 'John Carlo R. Dacillo', 'johncarlodacillo1@gmail.com', '0906 656 4528', '$2y$10$KTatMiKrmfOyytuKDeV.u./xOw17/oPwGXxfTQSv/32JizhRsv08u', 'student'),
(3, 'Andrei Teacher', 'andrei@sample.com', '0000 000 0001', '$2y$10$h7k/HXTi8shc0tU5jw2CW.i2KlzM67oc6que9xMx4fFuTvgx9MCUO', 'teacher'),
(4, 'Andrei Admin', 'andrei+2@sample.com', '0000 000 0002', '$2y$10$d1e4t9KRCF73hQ3brw6kAuf6MiXSM5SWLjnCRRff7R03bl22Z7K/6', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus_records`
--
ALTER TABLE `bus_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_table`
--
ALTER TABLE `bus_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bus_records`
--
ALTER TABLE `bus_records`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bus_table`
--
ALTER TABLE `bus_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
