-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 03:03 PM
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
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `size` varchar(3) NOT NULL,
  `price` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `image`, `category`, `size`, `price`) VALUES
(17, 'jacket', 'uploads/1760619572_466736746_17991824162718176_7790907259396982803_n.jpg', 'jackets', 'M', 150);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender` varchar(100) DEFAULT NULL,
  `receiver` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender`, `receiver`, `message`, `created_at`) VALUES
(1, 'heard_David@rocketmail.co', 'admin@revalue.com', 'dasd', '2025-10-07 12:28:42'),
(2, 'heard_David@rocketmail.co', 'admin@revalue.com', 'asdasd', '2025-10-07 12:28:49');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash on Delivery',
  `product_names` text NOT NULL,
  `product_images` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_date`, `status`, `payment_method`, `product_names`, `product_images`) VALUES
(1, 1, 248123.00, '2025-10-02 07:12:06', 'Completed', 'Cash on Delivery', '', ''),
(2, 1, 248123.00, '2025-10-02 07:13:13', 'Completed', 'Cash on Delivery', '', ''),
(3, 1, 120000.00, '2025-10-02 07:19:54', 'Completed', 'Cash on Delivery', '', ''),
(4, 1, 123123.00, '2025-10-02 07:22:23', 'Completed', 'Cash on Delivery', '', ''),
(5, 1, 120000.00, '2025-10-02 07:41:04', 'Completed', 'Cash on Delivery', '', ''),
(6, 1, 270.00, '2025-10-02 12:23:30', 'Completed', 'Cash on Delivery', '', ''),
(7, 1, 120000.00, '2025-10-03 04:46:53', 'Completed', 'Cash on Delivery', '', ''),
(8, 1, 270.00, '2025-10-05 09:41:52', 'Completed', 'Cash on Delivery', '', ''),
(9, 1, 500.00, '2025-10-07 10:38:09', 'Completed', 'Cash on Delivery', '', ''),
(10, 1, 5831.00, '2025-10-09 11:42:50', 'Completed', 'Cash on Delivery', '', ''),
(11, 1, 5206.00, '2025-10-16 12:07:06', 'Completed', 'Cash on Delivery', '', ''),
(12, 1, 200.00, '2025-10-16 12:16:01', 'Completed', 'Cash on Delivery', '', ''),
(13, 1, 270.00, '2025-10-16 12:27:21', 'Completed', 'Cash on Delivery', '', ''),
(14, 1, 125.00, '2025-10-16 12:29:17', 'Completed', 'Cash on Delivery', '', ''),
(15, 1, 200.00, '2025-10-16 12:31:28', 'Completed', 'Cash on Delivery', '', ''),
(16, 1, 200.00, '2025-10-16 12:32:31', 'Completed', 'Cash on Delivery', '', ''),
(17, 1, 123.00, '2025-10-16 12:40:41', 'Completed', 'Cash on Delivery', '', ''),
(18, 1, 200.00, '2025-10-16 12:47:51', 'Completed', 'Cash on Delivery', '', ''),
(19, 1, 500.00, '2025-10-16 12:50:50', 'Completed', 'Cash on Delivery', '[\"sando ni osit\"]', '[\"uploads\\/1759999370_466827131_17991821561718176_4971187432800159467_n.jpg\"]'),
(20, 1, 200.00, '2025-10-16 13:00:10', 'Completed', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1760619591_1760618064_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `number` varchar(16) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Full_name`, `E_mail`, `Pass`, `F_name`, `L_name`, `number`, `birth_date`, `address`, `address2`, `address3`, `country`) VALUES
(1, 'David Heard', 'heard_David@rocketmail.co', '$2y$10$1Vbuz3X5tatkxEMcCrjpdu/dDEXEAz5C/EbSqP6qbsHz1y8bhQnKy', 'David', 'Heard', '09760492077', '1999-08-16', 'centro sur', '', '', 'PH'),
(18, 'admin', 'admin@revalue.com', '$2y$10$CBUYIhNFR/qWt6gWa5wYQ.eRjLITzyQ3rqclKv/ZoZ79GnvltcgZ6', '', '', '0', NULL, '', NULL, NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
