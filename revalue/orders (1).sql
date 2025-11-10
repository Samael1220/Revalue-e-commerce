-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 01:16 PM
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
  `product_prices` text NOT NULL,
  `shipping_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_date`, `status`, `payment_method`, `product_names`, `product_images`, `product_sizes`, `product_prices`, `shipping_address`) VALUES
(54, 1, 500.00, '2025-10-31 08:45:44', 'Delivered', 'Cash on Delivery', '[\"Modern Shirt\"]', '[\"uploads\\/1761888459_1760619591_1760618064_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]', '[\"XL\"]', '', 'Landayan 12d+6'),
(55, 1, 900.00, '2025-11-01 09:42:39', 'Delivered', 'Cash on Delivery', '[\"Xlarge\",\"Nautica\"]', '[\"uploads\\/1761925145_Xlarge.jpg\",\"uploads\\/1761925103_Nautica.jpg\"]', '[\"M\",\"M\"]', '', '+62+6'),
(56, 1, 650.00, '2025-11-01 10:47:15', 'Delivered', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Vintage Jacket\"]', '[\"uploads\\/1761925082_Kirsh x CareBears.jpg\",\"uploads\\/1761888219_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '[\"S\",\"S\"]', '', '+62+6'),
(57, 1, 600.00, '2025-11-01 10:48:49', 'Delivered', 'Cash on Delivery', '[\"Xlarge Camou\"]', '[\"uploads\\/1761925123_Xlarge Camou.jpg\"]', '[\"L\"]', '', '+62+6'),
(58, 1, 900.00, '2025-11-02 13:50:47', 'Delivered', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Modern Jacket\"]', '[\"uploads\\/1762015154_Kirsh x CareBears.jpg\",\"uploads\\/1761888343_1761272732_1759657956_2.jpg\"]', '[\"L\",\"M\"]', '', '#63 quezon st. Landayan San pedro Laguna'),
(59, 1, 450.00, '2025-11-02 13:55:30', 'Delivered', 'Cash on Delivery', '[\"Adidas Coat\"]', '[\"uploads\\/1761888400_1759999370_466827131_17991821561718176_4971187432800159467_n.jpg\"]', '[\"L\"]', '', '#63 quezon st. Landayan San pedro Laguna'),
(60, 1, 550.00, '2025-11-02 13:59:49', 'Delivered', 'Cash on Delivery', '[\"Cav Empt\"]', '[\"uploads\\/1761924989_Cav Empt.jpg\"]', '[\"XS\"]', '', '#63 quezon st. Landayan San pedro Laguna'),
(61, 1, 1150.00, '2025-11-04 06:44:58', 'Delivered', 'Cash on Delivery', '[\"T shirt\",\"Givenchy\"]', '[\"uploads\\/1762092092_466859525_17991823883718176_7967796178441113811_n.jpg\",\"uploads\\/1762090915_466458314_17991821522718176_3090680755391982002_n.jpg\"]', '[\"XL\",\"L\"]', '', '#63 quezon st. Landayan San pedro Laguna'),
(62, 1, 500.00, '2025-11-10 10:51:57', 'Delivered', 'Cash on Delivery', '[\"Baggy Denim pants\"]', '[\"uploads\\/1762089340_467952125_17993100146718176_6050900487735072253_n.jpg\"]', '[\"L\"]', '', '#63 quezon st. landayan san pedro laguna'),
(63, 1, 200.00, '2025-11-10 11:01:29', 'Delivered', 'Cash on Delivery', '[\"Stussy Croptop\"]', '[\"uploads\\/1762319944_567733833_18031944941718176_9021507179451333466_n.jpg\"]', '[\"S\"]', '[200]', '#63 quezon st. landayan san pedro laguna'),
(64, 1, 950.00, '2025-11-10 11:02:04', 'Delivered', 'Cash on Delivery', '[\"Hunter metal band tee\",\"Hunter metal band tee\"]', '[\"uploads\\/1762090738_466859525_17991823883718176_7967796178441113811_n.jpg\",\"uploads\\/1762090609_466964397_17991823886718176_1726109815671737410_n.jpg\"]', '[\"S\",\"M\"]', '[350,600]', '#63 quezon st. landayan san pedro laguna'),
(65, 1, 400.00, '2025-11-10 11:12:11', 'Delivered', 'Cash on Delivery', '[\"Vtg Empyre Black Corduroy Jorts\"]', '[\"uploads\\/1762089725_466576827_17991823970718176_27312847498163817_n.jpg\"]', '[\"L\"]', '[400]', '#63 quezon st. landayan san pedro laguna'),
(66, 1, 500.00, '2025-11-10 11:24:45', 'Delivered', 'Cash on Delivery', '[\"Raised\"]', '[\"uploads\\/1762090482_466756111_17991824099718176_8086617303277702432_n.jpg\"]', '[\"XL\"]', '[500]', '#63 quezon st. landayan san pedro laguna'),
(67, 1, 600.00, '2025-11-10 11:27:46', 'Delivered', 'Cash on Delivery', '[\"Cradle Of filth\"]', '[\"uploads\\/1762090571_466932590_17991823901718176_6580880785945200061_n.jpg\"]', '[\"L\"]', '[600]', '#63 quezon st. landayan san pedro laguna'),
(68, 1, 600.00, '2025-11-10 11:34:15', 'Delivered', 'Cash on Delivery', '[\"Cavempt Script\"]', '[\"uploads\\/1762090450_466784935_17991823898718176_7527145333782158448_n.jpg\"]', '[\"L\"]', '[600]', '#63 quezon st. landayan san pedro laguna'),
(69, 1, 800.00, '2025-11-10 11:45:14', 'Delivered', 'Cash on Delivery', '[\"Cavempt portrait print\"]', '[\"uploads\\/1762090413_466896888_17991823895718176_6324796329203580808_n.jpg\"]', '[\"XL\"]', '[800]', '#63 quezon st. landayan san pedro laguna'),
(70, 1, 700.00, '2025-11-10 11:47:08', 'Delivered', 'Cash on Delivery', '[\"The Haunted Mask Goosebumps\"]', '[\"uploads\\/1762090376_466942956_17991824048718176_2126332151584878452_n.jpg\"]', '[\"L\"]', '[700]', '#63 quezon st. landayan san pedro laguna'),
(71, 1, 700.00, '2025-11-10 11:52:10', 'Delivered', 'Cash on Delivery', '[\"Shadow \\u201cWut\\u201d tee\"]', '[\"uploads\\/1762089804_466663280_17991823955718176_149267548539336773_n.jpg\"]', '[\"XL\"]', '[700]', '#63 quezon st. landayan san pedro laguna'),
(72, 1, 500.00, '2025-11-10 11:55:28', 'Delivered', 'Cash on Delivery', '[\"Double layered Jorts\"]', '[\"uploads\\/1762089758_466769103_17991824168718176_252407813348499395_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna'),
(73, 1, 500.00, '2025-11-10 11:57:49', 'Delivered', 'Cash on Delivery', '[\"JNCO Alternative + Double Knee\"]', '[\"uploads\\/1762089388_468152421_17993099303718176_2295547329613415753_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna'),
(74, 1, 500.00, '2025-11-10 11:59:45', 'Delivered', 'Cash on Delivery', '[\"Xlarge Camou\"]', '[\"uploads\\/1761994164_Xlarge Camou.jpg\"]', '[\"XS\"]', '[500]', '#63 quezon st. landayan san pedro laguna'),
(75, 1, 500.00, '2025-11-10 12:06:28', 'Pending', 'Cash on Delivery', '[\"Repaint Pants White to Grey\"]', '[\"uploads\\/1762089224_468148318_17993100551718176_1500198088463667463_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
