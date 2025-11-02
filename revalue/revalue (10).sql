-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 03:59 PM
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
  `size` varchar(10) DEFAULT NULL,
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
  `product_images` text NOT NULL,
  `product_sizes` text NOT NULL,
  `shipping_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_date`, `status`, `payment_method`, `product_names`, `product_images`, `product_sizes`, `shipping_address`) VALUES
(1, 1, 248123.00, '2025-10-02 07:12:06', 'Completed', 'Cash on Delivery', '', '', '', ''),
(2, 1, 248123.00, '2025-10-02 07:13:13', 'Completed', 'Cash on Delivery', '', '', '', ''),
(3, 1, 120000.00, '2025-10-02 07:19:54', 'Completed', 'Cash on Delivery', '', '', '', ''),
(4, 1, 123123.00, '2025-10-02 07:22:23', 'Completed', 'Cash on Delivery', '', '', '', ''),
(5, 1, 120000.00, '2025-10-02 07:41:04', 'Completed', 'Cash on Delivery', '', '', '', ''),
(6, 1, 270.00, '2025-10-02 12:23:30', 'Completed', 'Cash on Delivery', '', '', '', ''),
(7, 1, 120000.00, '2025-10-03 04:46:53', 'Completed', 'Cash on Delivery', '', '', '', ''),
(8, 1, 270.00, '2025-10-05 09:41:52', 'Completed', 'Cash on Delivery', '', '', '', ''),
(9, 1, 500.00, '2025-10-07 10:38:09', 'Completed', 'Cash on Delivery', '', '', '', ''),
(10, 1, 5831.00, '2025-10-09 11:42:50', 'Completed', 'Cash on Delivery', '', '', '', ''),
(11, 1, 5206.00, '2025-10-16 12:07:06', 'Completed', 'Cash on Delivery', '', '', '', ''),
(12, 1, 200.00, '2025-10-16 12:16:01', 'Completed', 'Cash on Delivery', '', '', '', ''),
(13, 1, 270.00, '2025-10-16 12:27:21', 'Completed', 'Cash on Delivery', '', '', '', ''),
(14, 1, 125.00, '2025-10-16 12:29:17', 'Completed', 'Cash on Delivery', '', '', '', ''),
(15, 1, 200.00, '2025-10-16 12:31:28', 'Completed', 'Cash on Delivery', '', '', '', ''),
(16, 1, 200.00, '2025-10-16 12:32:31', 'Completed', 'Cash on Delivery', '', '', '', ''),
(17, 1, 123.00, '2025-10-16 12:40:41', 'Completed', 'Cash on Delivery', '', '', '', ''),
(18, 1, 200.00, '2025-10-16 12:47:51', 'Completed', 'Cash on Delivery', '', '', '', ''),
(19, 1, 500.00, '2025-10-16 12:50:50', 'Completed', 'Cash on Delivery', '[\"sando ni osit\"]', '[\"uploads\\/1759999370_466827131_17991821561718176_4971187432800159467_n.jpg\"]', '', ''),
(20, 1, 200.00, '2025-10-16 13:00:10', 'Completed', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1760619591_1760618064_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]', '', ''),
(21, 1, 120.00, '2025-10-16 14:28:31', 'Completed', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1760624889_1760010399_1.jpg\"]', '', ''),
(22, 1, 450.00, '2025-10-28 13:43:09', 'Pending', 'Cash on Delivery', '[\"jacket\"]', '[\"uploads\\/1761658819_1760010430_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '', ''),
(23, 1, 300.00, '2025-10-29 08:16:29', 'Pending', 'Cash on Delivery', '[\"tshirt\"]', '[\"uploads\\/1761658849_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]', '', 'Landayan 12d+6'),
(24, 1, 150.00, '2025-10-29 08:24:31', 'Pending', 'Cash on Delivery', '[\"jacket\"]', '[\"uploads\\/1761658795_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '', '456762165'),
(25, 1, 246.00, '2025-10-29 08:30:18', 'Completed', 'Cash on Delivery', '[\"sumbrelo ni marvin\",\"adadad\"]', '[\"uploads\\/1761726575_aifaceswap-5cd0768e900695770ab31457edb2b870.jpg\",\"uploads\\/1761726589_logo.png\"]', '', 'Landayan 12d+6'),
(26, 1, 221.00, '2025-10-29 09:09:59', 'Pending', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1761728396_1759406058_466859525_17991823883718176_7967796178441113811_n.jpg\"]', '', '456762165'),
(27, 1, 321.00, '2025-10-29 09:11:38', 'Pending', 'Cash on Delivery', '[\"sumbrelo ni marvin\"]', '[\"uploads\\/1761728409_1759381740_Screenshot 2025-08-27 221704.png\"]', '', '+62+6'),
(28, 1, 243.00, '2025-10-29 09:18:31', 'Pending', 'Cash on Delivery', '', '', '', 'Landayan 12d+6'),
(29, 1, 123.00, '2025-10-29 09:20:33', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(30, 1, 123.00, '2025-10-29 09:24:35', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(31, 1, 123.00, '2025-10-29 09:27:45', 'Pending', 'Cash on Delivery', '', '', '', 'Landayan 12d+6'),
(32, 1, 123.00, '2025-10-29 09:37:38', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(33, 1, 123.00, '2025-10-29 09:40:37', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(34, 1, 123.00, '2025-10-29 09:42:22', 'Pending', 'Cash on Delivery', '', '', '', 'Landayan 12d+6'),
(39, 1, 123.00, '2025-10-29 09:58:55', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(40, 1, 123.00, '2025-10-29 10:04:14', 'Pending', 'Cash on Delivery', '[\"adadad\"]', '[\"uploads\\/1761730690_213123123.jpg\"]', '[\"M\"]', '456762165'),
(41, 1, 123.00, '2025-10-29 10:04:56', 'Pending', 'Cash on Delivery', '[\"adadad\"]', '[\"uploads\\/1761730690_213123123.jpg\"]', '[\"M\"]', '456762165'),
(42, 1, 123.00, '2025-10-29 10:09:24', 'Pending', 'Cash on Delivery', '[\"adadad\"]', '[\"uploads\\/1761730690_213123123.jpg\"]', '[\"M\"]', 'Landayan 12d+6'),
(43, 1, 123.00, '2025-10-29 10:11:31', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(44, 1, 123.00, '2025-10-29 10:12:45', 'Pending', 'Cash on Delivery', '', '', '', '+62+6'),
(45, 1, 123.00, '2025-10-29 10:16:59', 'Pending', 'Cash on Delivery', '[\"sando ni osit\"]', '[\"uploads\\/1761732987_aifaceswap-5cd0768e900695770ab31457edb2b870.jpg\"]', '[\"S\"]', '+62+6'),
(46, 1, 246.00, '2025-10-29 10:18:09', 'Pending', 'Cash on Delivery', '[\"plain\",\"sando ni osit\"]', '[\"uploads\\/1761732998_553616276_1994556097752178_7525165633244839506_n.jpg\",\"uploads\\/1761732987_aifaceswap-5cd0768e900695770ab31457edb2b870.jpg\"]', '[\"L\",\"S\"]', 'Landayan 12d+6'),
(47, 1, 246.00, '2025-10-29 10:20:31', 'Pending', 'Cash on Delivery', '[\"plain\",\"sando ni osit\"]', '[\"uploads\\/1761732998_553616276_1994556097752178_7525165633244839506_n.jpg\",\"uploads\\/1761732987_aifaceswap-5cd0768e900695770ab31457edb2b870.jpg\"]', '[\"L\",\"S\"]', '+62+6'),
(48, 1, 123.00, '2025-10-29 10:21:13', 'Pending', 'Cash on Delivery', '[\"adadad\"]', '[\"uploads\\/1761732979_bb02f964-d9de-4dc9-802a-9da6651a4c39.jpg\"]', '[\"XL\"]', 'Landayan 12d+6'),
(49, 1, 123.00, '2025-10-29 10:26:14', 'Pending', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1761732969_68a1338e-751d-423c-9398-c0e84a5a7c5b.jpg\"]', '[\"M\"]', '+62+6');

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
  `price` decimal(10,2) NOT NULL,
  `size` varchar(10) DEFAULT NULL
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
  `address` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Full_name`, `E_mail`, `Pass`, `F_name`, `L_name`, `number`, `birth_date`, `address`, `address2`, `address3`, `country`) VALUES
(1, 'David Heard', 'heard_David@rocketmail.co', '$2y$10$BxiEp.OwzXQcENOvn6GEXuRoMWE5JeLSBwpnVO/lq7hUmDblh0ke6', 'David', 'Heard', '09760492077', '1999-08-16', '+62+6', 'Landayan 12d+6', '456762165', 'PH'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
