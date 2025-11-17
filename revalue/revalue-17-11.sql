-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 11:02 AM
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
(106, 'H&M Rap tee design', 'uploads/1763367613_H&M Rap tee design.jpg', 'modern', 'M', 350),
(107, 'Xlarge', 'uploads/1763367642_Xlarge.jpg', 'modern', 'L', 300),
(108, 'Xlarge Camou', 'uploads/1763368334_Xlarge Camou.jpg', 'vintage', 'XS', 400),
(109, 'Nautica', 'uploads/1763368360_Nautica.jpg', 'modern', 'XXL', 500),
(110, 'Kirsh x CareBears', 'uploads/1763368376_Kirsh x CareBears.jpg', 'modern', 'L', 499),
(111, 'Chocolate x Baby Milo', 'uploads/1763368395_Chocolate x Baby Milo.jpg', 'vintage', 'S', 499),
(112, 'Cav Empt', 'uploads/1763368509_Cav Empt.jpg', 'modern', 'M', 399),
(113, 'DeathNote Anime Tee', 'uploads/1763368534_DeathNote Anime tee.jpg', 'modern', 'XS', 799),
(114, 'Osit x 2012 tee', 'uploads/1763368611_1762090113_aifaceswap-27cc681a669c10471816057f6d08f8c7.jpg', 'vintage', 'M', 899);

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
(35, 1, 18, 'thank you po', '2025-11-02 22:02:04', 'David Heard', 'admin'),
(36, 18, 1, 'hello', '2025-11-11 14:59:04', 'admin', 'David Heard'),
(37, 28, 18, 'hello po', '2025-11-11 15:43:20', 'brianna', 'admin'),
(38, 18, 28, 'hi', '2025-11-11 15:43:31', 'admin', 'brianna'),
(39, 33, 18, 'helloo', '2025-11-11 16:48:30', 'brianna nikola', 'admin'),
(40, 18, 1, 'testing', '2025-11-13 15:00:24', 'admin', 'David Heard'),
(41, 1, 18, 'hi', '2025-11-13 15:00:37', 'David Heard', 'admin'),
(42, 18, 28, 'mahal na kita', '2025-11-14 19:29:47', 'admin', 'brianna');

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
  `shipping_address` varchar(255) NOT NULL,
  `proof_of_delivery` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `order_date`, `status`, `payment_method`, `product_names`, `product_images`, `product_sizes`, `product_prices`, `shipping_address`, `proof_of_delivery`) VALUES
(54, 1, 500.00, '2025-10-31 08:45:44', 'Delivered', 'Cash on Delivery', '[\"Modern Shirt\"]', '[\"uploads\\/1761888459_1760619591_1760618064_1760010246_466730130_17991824051718176_724116604996009720_n.jpg\"]', '[\"XL\"]', '', 'Landayan 12d+6', NULL),
(55, 1, 900.00, '2025-11-01 09:42:39', 'Delivered', 'Cash on Delivery', '[\"Xlarge\",\"Nautica\"]', '[\"uploads\\/1761925145_Xlarge.jpg\",\"uploads\\/1761925103_Nautica.jpg\"]', '[\"M\",\"M\"]', '', '+62+6', NULL),
(56, 1, 650.00, '2025-11-01 10:47:15', 'Delivered', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Vintage Jacket\"]', '[\"uploads\\/1761925082_Kirsh x CareBears.jpg\",\"uploads\\/1761888219_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '[\"S\",\"S\"]', '', '+62+6', NULL),
(57, 1, 600.00, '2025-11-01 10:48:49', 'Delivered', 'Cash on Delivery', '[\"Xlarge Camou\"]', '[\"uploads\\/1761925123_Xlarge Camou.jpg\"]', '[\"L\"]', '', '+62+6', NULL),
(58, 1, 900.00, '2025-11-02 13:50:47', 'Completed', 'Cash on Delivery', '[\"Kirsh x CareBears\",\"Modern Jacket\"]', '[\"uploads\\/1762015154_Kirsh x CareBears.jpg\",\"uploads\\/1761888343_1761272732_1759657956_2.jpg\"]', '[\"L\",\"M\"]', '', '#63 quezon st. Landayan San pedro Laguna', NULL),
(59, 1, 450.00, '2025-11-02 13:55:30', 'Completed', 'Cash on Delivery', '[\"Adidas Coat\"]', '[\"uploads\\/1761888400_1759999370_466827131_17991821561718176_4971187432800159467_n.jpg\"]', '[\"L\"]', '', '#63 quezon st. Landayan San pedro Laguna', NULL),
(60, 1, 550.00, '2025-11-02 13:59:49', 'Completed', 'Cash on Delivery', '[\"Cav Empt\"]', '[\"uploads\\/1761924989_Cav Empt.jpg\"]', '[\"XS\"]', '', '#63 quezon st. Landayan San pedro Laguna', NULL),
(61, 1, 1150.00, '2025-11-04 06:44:58', 'Completed', 'Cash on Delivery', '[\"T shirt\",\"Givenchy\"]', '[\"uploads\\/1762092092_466859525_17991823883718176_7967796178441113811_n.jpg\",\"uploads\\/1762090915_466458314_17991821522718176_3090680755391982002_n.jpg\"]', '[\"XL\",\"L\"]', '', '#63 quezon st. Landayan San pedro Laguna', NULL),
(62, 1, 500.00, '2025-11-10 10:51:57', 'Completed', 'Cash on Delivery', '[\"Baggy Denim pants\"]', '[\"uploads\\/1762089340_467952125_17993100146718176_6050900487735072253_n.jpg\"]', '[\"L\"]', '', '#63 quezon st. landayan san pedro laguna', NULL),
(63, 1, 200.00, '2025-11-10 11:01:29', 'Completed', 'Cash on Delivery', '[\"Stussy Croptop\"]', '[\"uploads\\/1762319944_567733833_18031944941718176_9021507179451333466_n.jpg\"]', '[\"S\"]', '[200]', '#63 quezon st. landayan san pedro laguna', NULL),
(64, 1, 950.00, '2025-11-10 11:02:04', 'Completed', 'Cash on Delivery', '[\"Hunter metal band tee\",\"Hunter metal band tee\"]', '[\"uploads\\/1762090738_466859525_17991823883718176_7967796178441113811_n.jpg\",\"uploads\\/1762090609_466964397_17991823886718176_1726109815671737410_n.jpg\"]', '[\"S\",\"M\"]', '[350,600]', '#63 quezon st. landayan san pedro laguna', NULL),
(65, 1, 400.00, '2025-11-10 11:12:11', 'Completed', 'Cash on Delivery', '[\"Vtg Empyre Black Corduroy Jorts\"]', '[\"uploads\\/1762089725_466576827_17991823970718176_27312847498163817_n.jpg\"]', '[\"L\"]', '[400]', '#63 quezon st. landayan san pedro laguna', NULL),
(66, 1, 500.00, '2025-11-10 11:24:45', 'Completed', 'Cash on Delivery', '[\"Raised\"]', '[\"uploads\\/1762090482_466756111_17991824099718176_8086617303277702432_n.jpg\"]', '[\"XL\"]', '[500]', '#63 quezon st. landayan san pedro laguna', NULL),
(67, 1, 600.00, '2025-11-10 11:27:46', 'Completed', 'Cash on Delivery', '[\"Cradle Of filth\"]', '[\"uploads\\/1762090571_466932590_17991823901718176_6580880785945200061_n.jpg\"]', '[\"L\"]', '[600]', '#63 quezon st. landayan san pedro laguna', NULL),
(68, 1, 600.00, '2025-11-10 11:34:15', 'Completed', 'Cash on Delivery', '[\"Cavempt Script\"]', '[\"uploads\\/1762090450_466784935_17991823898718176_7527145333782158448_n.jpg\"]', '[\"L\"]', '[600]', '#63 quezon st. landayan san pedro laguna', NULL),
(69, 1, 800.00, '2025-11-10 11:45:14', 'Completed', 'Cash on Delivery', '[\"Cavempt portrait print\"]', '[\"uploads\\/1762090413_466896888_17991823895718176_6324796329203580808_n.jpg\"]', '[\"XL\"]', '[800]', '#63 quezon st. landayan san pedro laguna', NULL),
(70, 1, 700.00, '2025-11-10 11:47:08', 'Completed', 'Cash on Delivery', '[\"The Haunted Mask Goosebumps\"]', '[\"uploads\\/1762090376_466942956_17991824048718176_2126332151584878452_n.jpg\"]', '[\"L\"]', '[700]', '#63 quezon st. landayan san pedro laguna', NULL),
(71, 1, 700.00, '2025-11-10 11:52:10', 'Completed', 'Cash on Delivery', '[\"Shadow \\u201cWut\\u201d tee\"]', '[\"uploads\\/1762089804_466663280_17991823955718176_149267548539336773_n.jpg\"]', '[\"XL\"]', '[700]', '#63 quezon st. landayan san pedro laguna', NULL),
(72, 1, 500.00, '2025-11-10 11:55:28', 'Completed', 'Cash on Delivery', '[\"Double layered Jorts\"]', '[\"uploads\\/1762089758_466769103_17991824168718176_252407813348499395_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna', NULL),
(73, 1, 500.00, '2025-11-10 11:57:49', 'Completed', 'Cash on Delivery', '[\"JNCO Alternative + Double Knee\"]', '[\"uploads\\/1762089388_468152421_17993099303718176_2295547329613415753_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna', NULL),
(74, 1, 500.00, '2025-11-10 11:59:45', 'Completed', 'Cash on Delivery', '[\"Xlarge Camou\"]', '[\"uploads\\/1761994164_Xlarge Camou.jpg\"]', '[\"XS\"]', '[500]', '#63 quezon st. landayan san pedro laguna', NULL),
(75, 1, 500.00, '2025-11-10 12:06:28', 'Completed', 'Cash on Delivery', '[\"Repaint Pants White to Grey\"]', '[\"uploads\\/1762089224_468148318_17993100551718176_1500198088463667463_n.jpg\"]', '[\"L\"]', '[500]', '#63 quezon st. landayan san pedro laguna', NULL),
(76, 1, 600.00, '2025-11-10 12:57:08', 'Completed', 'Cash on Delivery', '[\"Walking Dead warmer longsleeves\"]', '[\"uploads\\/1762089687_466964395_17991823964718176_5199477492212639792_n.jpg\"]', '[\"XL\"]', '[600]', '#63 quezon st. landayan san pedro laguna', NULL),
(77, 1, 600.00, '2025-11-10 13:00:08', 'Completed', 'Cash on Delivery', '[\"Chocolate x Baby Milo\"]', '[\"uploads\\/1761925010_Chocolate x Baby Milo.jpg\"]', '[\"XL\"]', '[600]', '#63 quezon st. landayan san pedro laguna', NULL),
(78, 1, 400.00, '2025-11-10 13:04:07', 'Completed', 'Cash on Delivery', '[\"Textured Checkered Pants\"]', '[\"uploads\\/1762089127_468264107_17993101148718176_3244216587281009698_n.jpg\"]', '[\"M\"]', '[400]', '#63 quezon st. landayan san pedro laguna', NULL),
(79, 1, 300.00, '2025-11-10 13:41:09', 'Completed', 'Cash on Delivery', '[\"DeathNote Anime Tee\"]', '[\"uploads\\/1761925035_DeathNote Anime tee.jpg\"]', '[\"L\"]', '[300]', '#63 quezon st. landayan san pedro laguna', NULL),
(80, 1, 900.00, '2025-11-11 06:58:23', 'Delivered', 'Cash on Delivery', '[\"Walking Dead c2012 zip up hoodie\"]', '[\"uploads\\/1762089644_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '[\"XL\"]', '[900]', '#63 quezon st. landayan san pedro laguna', NULL),
(81, 1, 400.00, '2025-11-11 07:00:19', 'Delivered', 'Cash on Delivery', '[\"H&M Rap Tee \"]', '[\"uploads\\/1761925060_H&M Rap tee design.jpg\"]', '[\"L\"]', '[400]', '#63 quezon st. landayan san pedro laguna', NULL),
(82, 33, 400.00, '2025-11-11 08:51:32', 'Delivered', 'Cash on Delivery', '[\"Chino Corduroy Pants\"]', '[\"uploads\\/1762089600_468141030_17993098610718176_6039245351425379885_n.jpg\"]', '[\"L\"]', '[400]', 'Not provided', NULL),
(83, 28, 66.00, '2025-11-12 14:35:18', 'Completed', 'Cash on Delivery', '[\"sdasd\"]', '[\"uploads\\/1762939364_1762319732_567733833_18031944941718176_9021507179451333466_n.jpg\"]', '[\"S\"]', '[66]', 'Not provided', NULL),
(84, 1, 10.00, '2025-11-12 14:57:44', 'Completed', 'Cash on Delivery', '[\"a\"]', '[\"uploads\\/1762939111_Xlarge.jpg\"]', '[\"S\"]', '[10]', 'aa', NULL),
(85, 1, 65.00, '2025-11-12 15:02:01', 'Completed', 'Cash on Delivery', '[\"sdasd\",\"aa\"]', '[\"uploads\\/1762939340_571019242_18031943834718176_864980706128177505_n.jpg\",\"uploads\\/1762939287_569023167_18031943348718176_7206675219372402339_n.jpg\"]', '[\"S\",\"S\"]', '[55,10]', '#63 quezon st. landayan san pedro laguna', NULL),
(86, 1, 10.00, '2025-11-13 06:54:49', 'Completed', 'Cash on Delivery', '[\"a\"]', '[\"uploads\\/1762939215_568341854_18031944470718176_5382764532382433647_n.jpg\"]', '[\"S\"]', '[10]', '#63 quezon st. landayan san pedro laguna', NULL),
(87, 1, 3639.00, '2025-11-13 10:29:48', 'Completed', 'Cash on Delivery', '[\"Creamy Denim Shorts\",\"Y2k Akademiks Long Jorts\",\"Ed Hardy Vintage Washed\",\"Osit Obama\",\"Deathrow 4Great Rapper\"]', '[\"uploads\\/1762088814_468323912_17993102120718176_133498590810238816_n.jpg\",\"uploads\\/1762088912_468148318_17993101886718176_639268249709211523_n.jpg\",\"uploads\\/1762088973_468226359_17993101595718176_2246143113314232243_n.jpg\",\"uploads\\/1762090113_aifaceswap-27cc681a669c10471816057f6d08f8c7.jpg\",\"uploads\\/1762090313_aifaceswap-0f55fa218c64f6c7546406e281d082ac.jpg\"]', '[\"M\",\"L\",\"S\",\"XL\",\"XL\"]', '[350,550,340,1499,900]', 'hhh', NULL),
(88, 36, 880.00, '2025-11-14 10:53:08', 'Delivered', 'Cash on Delivery', '[\"e\"]', '[\"uploads\\/1763040999_1759999348_466923890_17991822080718176_299731762076899406_n.jpg\"]', '[\"M\"]', '[880]', 'Not provided', NULL),
(89, 36, 400.00, '2025-11-14 10:54:37', 'Delivered', 'Cash on Delivery', '[\"c\"]', '[\"uploads\\/1763040932_1759999313_466730130_17991824051718176_724116604996009720_n.jpg\"]', '[\"XL\"]', '[400]', 'Not provided', NULL),
(90, 36, 999.00, '2025-11-14 10:58:34', 'Completed', 'Cash on Delivery', '[\"d\"]', '[\"uploads\\/1763040978_1760010399_1.jpg\"]', '[\"XL\"]', '[999]', 'sadjka', 'uploads/proofs/order_90_1763119642_aifaceswap-4d44518f050834d1bc563eb5375aa506.jpg'),
(91, 37, 400.00, '2025-11-14 11:07:54', 'Completed', 'Cash on Delivery', '[\"c\"]', '[\"uploads\\/1763040932_1759999313_466730130_17991824051718176_724116604996009720_n.jpg\"]', '[\"XL\"]', '[400]', '123 sa puso ni brianna street', NULL),
(92, 37, 500.00, '2025-11-14 11:20:37', 'Completed', 'Cash on Delivery', '[\"a\"]', '[\"uploads\\/1763040882_jcket.jpg\"]', '[\"M\"]', '[500]', '123 sa puso ni brianna street', 'uploads/proofs/order_92_1763119324_aifaceswap-26006b8aa0f38a6145ff0d8acd279925.jpg'),
(93, 1, 2131.00, '2025-11-15 11:58:21', 'Completed', 'Cash on Delivery', '[\"adadad\"]', '[\"uploads\\/1763207810_1761630838_1760010296_2.jpg\"]', '[\"M\"]', '[2131]', '#63 quezon st. landayan san pedro laguna', NULL),
(94, 1, 300.00, '2025-11-15 12:31:25', 'Completed', 'Cash on Delivery', '[\"b\"]', '[\"uploads\\/1763040898_1759999323_466859525_17991823883718176_7967796178441113811_n.jpg\"]', '[\"S\"]', '[300]', 'hhh', 'uploads/proofs/order_94_1763209980_7.jpg'),
(95, 1, 123.00, '2025-11-15 12:37:09', 'Completed', 'Cash on Delivery', '[\"t shirt\"]', '[\"uploads\\/1763207827_1759406066_466736746_17991824162718176_7790907259396982803_n.jpg\"]', '[\"XS\"]', '[123]', '#63 quezon st. landayan san pedro laguna', 'uploads/proofs/order_95_1763210734_12.jpg'),
(96, 1, 123.00, '2025-11-15 12:37:41', 'Completed', 'Cash on Delivery', '[\"sumbrelo ni marvin\"]', '[\"uploads\\/1763207818_1759657946_466923890_17991822080718176_299731762076899406_n.jpg\"]', '[\"XL\"]', '[123]', '#63 quezon st. landayan san pedro laguna', NULL),
(97, 1, 150.00, '2025-11-15 22:04:59', 'Completed', 'Cash on Delivery', '[\"c\"]', '[\"uploads\\/1763211774_1759657956_2.jpg\"]', '[\"L\"]', '[150]', '#63 quezon st. landayan san pedro laguna', 'uploads/proofs/order_97_1763244583_9.jpg'),
(98, 1, 150.00, '2025-11-16 10:00:07', 'Delivered', 'Cash on Delivery', '[\"a\"]', '[\"uploads\\/1763211746_jcket.jpg\"]', '[\"S\"]', '[150]', 'hhh', NULL),
(99, 1, 300.00, '2025-11-16 10:02:30', 'Cancelled', 'Cash on Delivery', '[\"b\"]', '[\"uploads\\/1763211759_1759406058_466859525_17991823883718176_7967796178441113811_n.jpg\"]', '[\"L\"]', '[300]', 'aa', NULL),
(100, 1, 25.00, '2025-11-16 10:15:23', 'Cancelled', 'Cash on Delivery', '[\"aa\"]', '[\"uploads\\/1763288032_1759657946_466923890_17991822080718176_299731762076899406_n.jpg\"]', '[\"M\"]', '[25]', 'aa', NULL),
(101, 1, 55.00, '2025-11-16 10:15:34', 'Cancelled', 'Cash on Delivery', '[\" b\"]', '[\"uploads\\/1763288020_1759406058_466859525_17991823883718176_7967796178441113811_n.jpg\"]', '[\"S\"]', '[55]', '#63 quezon st. landayan san pedro laguna', NULL),
(102, 1, 45.00, '2025-11-16 10:15:44', 'Pending', 'Cash on Delivery', '[\"a\"]', '[\"uploads\\/1763288008_1761492049_jacket.jpg\"]', '[\"M\"]', '[45]', '#63 quezon st. landayan san pedro laguna', NULL);

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
(1, 'David Heard', 'heard_David@rocketmail.co', '$2y$10$BxiEp.OwzXQcENOvn6GEXuRoMWE5JeLSBwpnVO/lq7hUmDblh0ke6', 'David', 'Heard', '09760492077', '1999-08-16', '#63 quezon st. landayan san pedro laguna', 'aa', 'hhh', 'JP'),
(18, 'admin', 'admin@revalue.com', '$2y$10$CBUYIhNFR/qWt6gWa5wYQ.eRjLITzyQ3rqclKv/ZoZ79GnvltcgZ6', '', '', '0', NULL, 'asdasd', 'asdasdzxczx', 'asd12q1q3e', ''),
(20, 'eitan', 'eitandwane@gmail.com', '$2y$10$dHGKr5q9uGJi0fU9044NguPpa6tx9ThGvz1U0RJ95anbBQEXYt6jy', 'eitan', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(21, 'totoy brown', 'user1@gmail.com', '$2y$10$KqRMED6fV3UhfFS4Lit.UeHM0rkF9wMUr/EZlwozP672umYHgEz4m', 'totoy', 'brown', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(22, 'brianna', 'user2@gmail.com', '$2y$10$HZW6HrO9/PLdAsWiwurVeegEX9ap2k479PocD9P/Sh5F.dTyLTBuq', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(23, 'brianna', 'user3@gmail.com', '$2y$10$WmCNSCJNQ4YG3blPR4SnR.gcydbA2rU6koQLcOgdEAFVgEOFn047q', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(24, 'brianna', 'user4@gmail.com', '$2y$10$r4o3aN7Fjz14npckCeHd/eZFN0WxCdeDmwt1xzKhZtIP6KgTB.4Yy', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(25, 'brianna', 'user5@gmail.com', '$2y$10$aHVZvpPkbvaQp8l9b6uy.OyLhzUdFkA2fzJuw2UzBnSj2ytPXzrD6', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(26, 'brianna', 'user6@gmail.com', '$2y$10$YdExliNnrc7PGm09BG/xoOzCvDGe7Y3qP4qpaUAlFa4a8e/y.i3pC', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(27, 'brianna', 'user7@gmail.com', '$2y$10$HkSxngd6uprPbwVO3iwZueVx5PY1KbzQPpVWcM7PXOXLTRbI7Z/jW', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(28, 'brianna', 'user8@gmail.com', '$2y$10$nRuEhcmIdW6eP1grbvEZOebhLP2.gdTnTC97LWCuG6p8ZM6KYO0.e', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(29, 'brianna', 'user9@gmail.com', '$2y$10$Jmo/bMziO3Bd4V92ViUdXOFizNLKYxt0gydqlrV4PYxnNuSFGH0hi', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(30, 'brianna', 'user10@gmail.com', '$2y$10$znPTwzU6FR6/cM.UiFmSSOjwgcYPiERIf7hMyGUNUkeP0LFNKsZfm', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(31, 'brianna', 'user11@gmail.com', '$2y$10$FGwGc0sznb0RIo2E7qQitOD3ioZskAcZI7.gcx15ZiifB1shkikoa', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(32, 'brianna', 'user12@gmail.com', '$2y$10$iVOUUgycWlbsMV6QgYVkeupfRDQ6/Kj9UVA7mU6fuYXRnQJi8jDk.', 'brianna', '', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(33, 'brianna nikola', 'user012@gmail.com', '$2y$10$T1dB3z6NEUzTg5r7WRosDOANWs3d1FmRkTOvfHutYhtPFjcLcW61q', 'brianna', 'nikola', '0', NULL, 'Not provided', NULL, NULL, ''),
(34, 'brianna nikola', 'user0@gmail.com', '$2y$10$NuXLu.LI7HulAAyMjjT7ce6mOhsM3lDK8jSoZ.9iUFSo8taYjejIO', 'brianna', 'nikola', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(35, 'brianna nikola', 'user01@gmail.com', '$2y$10$u48TYl0bj0jsbH8EIu3/Depm2e.RyK3VVe86hsLw1xOkzZKLfwv7C', 'brianna', 'nikola', '0', NULL, 'Not provided', NULL, NULL, 'Philippines'),
(36, 'marvin acuin', 'marvs@gmail.com', '$2y$10$XV7GSVKSGtU76fe9aqVKSuX7Q3GWqMP.e268cDsuLud/Rmn8VDlgG', 'marvin', 'acuin', '0', NULL, 'sadjka', NULL, NULL, 'Philippines'),
(37, 'eitan heart brianna', 'tantan@gmail.com', '$2y$10$7SY90Gyz2cR0DGJYj0ROmuXVmFALkpPJ8qzELgbf.3ywzhjndsaiy', 'eitan', 'heart brianna', '0', NULL, '123 sa puso ni brianna street', NULL, NULL, 'Philippines');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
