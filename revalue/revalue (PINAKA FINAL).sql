-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 03:10 PM
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

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `name`, `image`, `category`, `size`, `price`) VALUES
(51, 'Chocolate x Baby Milo', 'uploads/1761925010_Chocolate x Baby Milo.jpg', 'modern', 'XL', 600),
(52, 'DeathNote Anime Tee', 'uploads/1761925035_DeathNote Anime tee.jpg', 'modern', 'L', 300),
(53, 'H&M Rap Tee ', 'uploads/1761925060_H&M Rap tee design.jpg', 'modern', 'L', 400),
(58, 'Xlarge Camou', 'uploads/1761994164_Xlarge Camou.jpg', 'modern', 'XS', 500),
(61, 'Creamy Denim Shorts', 'uploads/1762088814_468323912_17993102120718176_133498590810238816_n.jpg', 'pants', 'M', 350),
(62, 'Y2k Akademiks Long Jorts', 'uploads/1762088912_468148318_17993101886718176_639268249709211523_n.jpg', 'pants', 'L', 550),
(63, 'Ed Hardy Vintage Washed', 'uploads/1762088973_468226359_17993101595718176_2246143113314232243_n.jpg', 'pants', 'S', 340),
(64, 'Textured Checkered Pants', 'uploads/1762089127_468264107_17993101148718176_3244216587281009698_n.jpg', 'pants', 'M', 400),
(65, 'Repaint Pants White to Grey', 'uploads/1762089224_468148318_17993100551718176_1500198088463667463_n.jpg', 'pants', 'L', 500),
(66, 'Baggy Denim pants', 'uploads/1762089340_467952125_17993100146718176_6050900487735072253_n.jpg', 'pants', 'L', 500),
(67, 'JNCO Alternative + Double Knee', 'uploads/1762089388_468152421_17993099303718176_2295547329613415753_n.jpg', 'pants', 'L', 500),
(68, 'Chino Corduroy Pants', 'uploads/1762089600_468141030_17993098610718176_6039245351425379885_n.jpg', 'pants', 'L', 400),
(69, 'Walking Dead c2012 zip up hoodie', 'uploads/1762089644_466736746_17991824162718176_7790907259396982803_n.jpg', 'jackets', 'XL', 900),
(70, 'Walking Dead warmer longsleeves', 'uploads/1762089687_466964395_17991823964718176_5199477492212639792_n.jpg', 'modern', 'XL', 600),
(71, 'Vtg Empyre Black Corduroy Jorts', 'uploads/1762089725_466576827_17991823970718176_27312847498163817_n.jpg', 'pants', 'L', 400),
(72, 'Double layered Jorts', 'uploads/1762089758_466769103_17991824168718176_252407813348499395_n.jpg', 'pants', 'L', 500),
(73, 'Shadow “Wut” tee', 'uploads/1762089804_466663280_17991823955718176_149267548539336773_n.jpg', 'modern', 'XL', 700),
(75, 'Osit Obama', 'uploads/1762090113_aifaceswap-27cc681a669c10471816057f6d08f8c7.jpg', 'vintage', 'XL', 1499),
(76, 'Deathrow 4Great Rapper', 'uploads/1762090313_aifaceswap-0f55fa218c64f6c7546406e281d082ac.jpg', 'vintage', 'XL', 900),
(77, 'The Haunted Mask Goosebumps', 'uploads/1762090376_466942956_17991824048718176_2126332151584878452_n.jpg', 'modern', 'L', 700),
(78, 'Cavempt portrait print', 'uploads/1762090413_466896888_17991823895718176_6324796329203580808_n.jpg', 'modern', 'XL', 800),
(79, 'Cavempt Script', 'uploads/1762090450_466784935_17991823898718176_7527145333782158448_n.jpg', 'modern', 'L', 600),
(80, 'Raised', 'uploads/1762090482_466756111_17991824099718176_8086617303277702432_n.jpg', 'modern', 'XL', 500),
(81, 'Cradle Of filth', 'uploads/1762090571_466932590_17991823901718176_6580880785945200061_n.jpg', 'jackets', 'L', 600),
(82, 'Hunter metal band tee', 'uploads/1762090609_466964397_17991823886718176_1726109815671737410_n.jpg', 'modern', 'M', 600),
(83, 'Hunter metal band tee', 'uploads/1762090738_466859525_17991823883718176_7967796178441113811_n.jpg', 'modern', 'S', 350),
(84, 'Stussy Croptop', 'uploads/1762090811_567733833_18031944941718176_9021507179451333466_n.jpg', 'modern', 'S', 200),
(85, 'Givenchy', 'uploads/1762090915_466458314_17991821522718176_3090680755391982002_n.jpg', 'jackets', 'L', 650),
(86, 'T shirt', 'uploads/1762092092_466859525_17991823883718176_7967796178441113811_n.jpg', 'modern', 'XL', 500);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `sender_name` varchar(255) DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `body`, `created_at`, `sender_name`, `receiver_name`) VALUES
(22, 20, 18, 'may sardinas?', '2025-10-30 10:38:26', 'eitan', 'admin'),
(23, 18, 20, 'corned beef po meron', '2025-10-30 10:38:43', 'admin', 'eitan'),
(24, 1, 18, 'may nilagang manok?', '2025-10-30 10:39:06', 'David Heard', 'admin'),
(25, 18, 1, 'meron po', '2025-10-30 10:39:18', 'admin', 'David Heard'),
(26, 1, 18, 'okie', '2025-10-30 11:12:30', 'David Heard', 'admin'),
(27, 1, 18, 'may sardinas?', '2025-10-30 11:29:38', 'David Heard', 'admin'),
(28, 1, 18, 'askgdasbdas', '2025-10-30 11:31:43', 'David Heard', 'admin'),
(29, 18, 1, 'asdasdas', '2025-10-30 11:31:52', 'admin', 'David Heard'),
(30, 1, 18, 'asdasd', '2025-10-31 13:22:17', 'David Heard', 'admin'),
(31, 18, 1, 'ok po', '2025-10-31 16:44:11', 'admin', 'David Heard'),
(32, 1, 18, 'asdasd', '2025-11-01 18:46:40', 'David Heard', 'admin'),
(33, 1, 18, 'kung may new stock incoming', '2025-11-02 21:49:34', 'David Heard', 'admin'),
(34, 18, 1, 'yes po meron', '2025-11-02 22:01:48', 'admin', 'David Heard'),
(35, 1, 18, 'thank you po', '2025-11-02 22:02:04', 'David Heard', 'admin');

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
(54, 1, 500.00, '2025-10-31 08:45:44', 'Completed', 'Cash on Delivery', '[\"Modern Shirt\"]', '[\"uploads\\/1761888459_1760619591_1760618064_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]', '[\"XL\"]', 'Landayan 12d+6'),
(55, 1, 900.00, '2025-11-01 09:42:39', 'Completed', 'Cash on Delivery', '[\"Xlarge\",\"Nautica\"]', '[\"uploads\\/1761925145_Xlarge.jpg\",\"uploads\\/1761925103_Nautica.jpg\"]', '[\"M\",\"M\"]', '+62+6'),
(56, 1, 650.00, '2025-11-01 10:47:15', 'Completed', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Vintage Jacket\"]', '[\"uploads\\/1761925082_Kirsh x CareBears.jpg\",\"uploads\\/1761888219_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '[\"S\",\"S\"]', '+62+6'),
(57, 1, 600.00, '2025-11-01 10:48:49', 'Completed', 'Cash on Delivery', '[\"Xlarge Camou\"]', '[\"uploads\\/1761925123_Xlarge Camou.jpg\"]', '[\"L\"]', '+62+6'),
(58, 1, 900.00, '2025-11-02 13:50:47', 'Completed', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Modern Jacket\"]', '[\"uploads\\/1762015154_Kirsh x CareBears.jpg\",\"uploads\\/1761888343_1761272732_1759657956_2.jpg\"]', '[\"L\",\"M\"]', '#63 quezon st. Landayan San pedro Laguna'),
(59, 1, 450.00, '2025-11-02 13:55:30', 'Completed', 'Cash on Delivery', '[\"Adidas Coat\"]', '[\"uploads\\/1761888400_1759999370_466827131_17991821561718176_4971187432800159467_n.jpg\"]', '[\"L\"]', '#63 quezon st. Landayan San pedro Laguna'),
(60, 1, 550.00, '2025-11-02 13:59:49', 'Completed', 'Cash on Delivery', '[\"Cav Empt\"]', '[\"uploads\\/1761924989_Cav Empt.jpg\"]', '[\"XS\"]', '#63 quezon st. Landayan San pedro Laguna');

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
(1, 'David Heard', 'heard_David@rocketmail.co', '$2y$10$BxiEp.OwzXQcENOvn6GEXuRoMWE5JeLSBwpnVO/lq7hUmDblh0ke6', 'David', 'Heard', '09760492077', '1999-08-16', '#63 quezon st. Landayan San pedro Laguna', '#73 roxas st. Landayan san pedro laguna', 'Jupiter', 'PH'),
(18, 'admin', 'admin@revalue.com', '$2y$10$CBUYIhNFR/qWt6gWa5wYQ.eRjLITzyQ3rqclKv/ZoZ79GnvltcgZ6', '', '', '0', NULL, 'asdasd', 'asdasdzxczx', 'asd12q1q3e', ''),
(20, 'eitan', 'eitandwane@gmail.com', '$2y$10$dHGKr5q9uGJi0fU9044NguPpa6tx9ThGvz1U0RJ95anbBQEXYt6jy', 'eitan', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `created_at` (`created_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
