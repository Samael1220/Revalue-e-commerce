-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 05:37 AM
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
-- Database: `revalue`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(12) NOT NULL,
  `Full_name` varchar(25) NOT NULL,
  `E_mail` varchar(25) NOT NULL,
  `Pass` varchar(255) NOT NULL,
  `F_name` varchar(255) NOT NULL,
  `L_name` varchar(255) NOT NULL,
  `number` int(12) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Full_name`, `E_mail`, `Pass`, `F_name`, `L_name`, `number`, `birth_date`, `address`, `country`) VALUES
(1, 'david heard', 'heard_David@rocketmail.co', '$2y$10$1Vbuz3X5tatkxEMcCrjpdu/dDEXEAz5C/EbSqP6qbsHz1y8bhQnKy', '', '', 0, NULL, '', ''),
(2, 'marvin pogi', 'angpogiko@gmail.com', '$2y$10$DcVC3Qsi2rtunraENTlMFOV9FhWdk7xQeoTzCX55.ja3BuFkKfDTG', '', '', 0, NULL, '', ''),
(7, 'eitan maceda', 'tantan@gmail.com', '$2y$10$7fv1gi8rgT5XFbNwNe.HBu24MYVZXfixlNQYso/LRdPX0G9VMOOk.', '', '', 0, NULL, '', ''),
(17, 'david heard', 'heard@gmail.com', '$2y$10$uDjBHR66LHYVTlY2BGbrnegko8LWrBv9ufdrH7zOtIBtGgdIzE3cy', '', '', 0, NULL, '', ''),
(18, 'admin', 'admin@revalue.com', '$2y$10$CBUYIhNFR/qWt6gWa5wYQ.eRjLITzyQ3rqclKv/ZoZ79GnvltcgZ6', '', '', 0, NULL, '', ''),
(19, 'david pogi', 'david@gmail.com', '$2y$10$gS0mK9ArmiL3uZ3Cag6MTep0OJhlwSCrN6QRvD.ohhEybDfj7TG5m', '', '', 0, NULL, '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `E_mail` (`E_mail`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
