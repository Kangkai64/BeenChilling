-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 05:22 AM
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
-- Database: `beenchilling`
--
CREATE DATABASE IF NOT EXISTS `beenchilling` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `beenchilling`;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `cart_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','checked_out','abandoned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `member_id`, `created_at`, `updated_at`, `status`) VALUES
('CA0001', 7, '2025-04-25 13:02:36', '2025-04-25 13:02:53', ''),
('CA0002', 7, '2025-04-26 22:46:35', '2025-04-26 23:23:15', ''),
('CA0003', 7, '2025-04-26 23:23:26', '2025-04-26 23:25:48', ''),
('CA0004', 7, '2025-04-26 23:26:01', '2025-04-26 23:31:44', ''),
('CA0005', 7, '2025-04-26 23:33:03', '2025-04-26 23:40:13', ''),
('CA0006', 7, '2025-04-26 23:42:31', '2025-04-27 10:29:14', 'abandoned');

--
-- Triggers `cart`
--
DROP TRIGGER IF EXISTS `before_insert_cart`;
DELIMITER $$
CREATE TRIGGER `before_insert_cart` BEFORE INSERT ON `cart` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(cart_id, 3)), 0) + 1 FROM cart);
    SET NEW.cart_id = CONCAT('CA', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

DROP TABLE IF EXISTS `cart_item`;
CREATE TABLE `cart_item` (
  `cart_item_id` varchar(10) NOT NULL,
  `cart_id` varchar(10) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
('CI0001', 'CA0001', 'SUN002', 1, 8.00, '2025-04-25 13:02:44', '2025-04-25 13:02:44'),
('CI0002', 'CA0001', 'DESS002', 1, 12.50, '2025-04-25 13:02:44', '2025-04-25 13:02:44'),
('CI0003', 'CA0001', 'ICE002', 1, 4.00, '2025-04-25 13:02:44', '2025-04-25 13:02:44'),
('CI0004', 'CA0002', 'DESS002', 9, 12.50, '2025-04-26 22:46:42', '2025-04-26 22:48:24'),
('CI0005', 'CA0002', 'ICE003', 12, 4.00, '2025-04-26 22:46:43', '2025-04-26 22:46:47'),
('CI0006', 'CA0002', 'ICE002', 1, 4.00, '2025-04-26 22:46:44', '2025-04-26 22:46:44'),
('CI0007', 'CA0003', 'SUN004', 1, 8.00, '2025-04-26 23:25:30', '2025-04-26 23:25:30'),
('CI0008', 'CA0003', 'ICE011', 11, 4.00, '2025-04-26 23:25:32', '2025-04-26 23:25:39'),
('CI0009', 'CA0003', 'ICE016', 1, 4.00, '2025-04-26 23:25:34', '2025-04-26 23:25:34'),
('CI0010', 'CA0004', 'SUN004', 1, 8.00, '2025-04-26 23:31:31', '2025-04-26 23:31:31'),
('CI0011', 'CA0004', 'DESS003', 1, 15.00, '2025-04-26 23:31:32', '2025-04-26 23:31:32'),
('CI0012', 'CA0004', 'DESS002', 1, 12.50, '2025-04-26 23:31:33', '2025-04-26 23:31:33'),
('CI0013', 'CA0004', 'ICE003', 1, 4.00, '2025-04-26 23:31:34', '2025-04-26 23:31:34'),
('CI0014', 'CA0005', 'SUN004', 1, 8.00, '2025-04-26 23:35:56', '2025-04-26 23:35:56'),
('CI0015', 'CA0005', 'DESS002', 1, 12.50, '2025-04-26 23:35:58', '2025-04-26 23:35:58'),
('CI0016', 'CA0005', 'DESS001', 1, 19.50, '2025-04-26 23:35:59', '2025-04-26 23:35:59'),
('CI0017', 'CA0006', 'DESS001', 1, 19.50, '2025-04-27 08:44:41', '2025-04-27 08:44:41'),
('CI0018', 'CA0006', 'ICE003', 2, 4.00, '2025-04-27 08:44:50', '2025-04-27 10:28:46'),
('CI0019', 'CA0006', 'DESS003', 1, 15.00, '2025-04-27 10:28:46', '2025-04-27 10:28:46');

--
-- Triggers `cart_item`
--
DROP TRIGGER IF EXISTS `before_insert_cart_item`;
DELIMITER $$
CREATE TRIGGER `before_insert_cart_item` BEFORE INSERT ON `cart_item` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(cart_item_id, 3)), 0) + 1 FROM cart_item);
    SET NEW.cart_item_id = CONCAT('CI', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_details`
--

DROP TABLE IF EXISTS `ip_details`;
CREATE TABLE `ip_details` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `login_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `order_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `cart_id` varchar(10) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','awaiting_payment','paid','failed') DEFAULT 'pending',
  `order_status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `billplz_bill_id` varchar(100) DEFAULT NULL,
  `billplz_collection_id` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `member_id`, `cart_id`, `order_date`, `total_amount`, `shipping_address`, `billing_address`, `payment_method`, `payment_status`, `order_status`, `billplz_bill_id`, `billplz_collection_id`, `transaction_id`, `payment_date`) VALUES
('OR0001', 7, 'CA0001', '2025-04-25 13:02:53', 24.50, 'Happy😆Man, 012-2334037, 19, Happy Street, Segamat, Johor, 85000, Malaysia', 'Happy😆Man, 012-2334037, 19, Happy Street, Segamat, Johor, 85000, Malaysia', 'Billplz', 'paid', 'refunded', 'ae6113c652d95b41', 'racg2vr3', 'F541E67E94695BEB9E9C', '2025-04-26 22:00:34'),
('OR0002', 7, 'CA0002', '2025-04-26 23:23:15', 164.50, 'Happy😆Man, 012-2334037, 19, Happy Street, Segamat, Johor, 85000, Malaysia', 'Malaysia', 'Billplz', 'paid', 'processing', '3a67866199997203', 'racg2vr3', 'FC9456EF130861B22202', '2025-04-26 23:23:26'),
('OR0003', 7, 'CA0003', '2025-04-26 23:25:48', 56.00, 'Happy😆Man, 012-2334037, 19, Happy Street, Segamat, Johor, 85000, Malaysia', 'Malaysia', 'Billplz', 'paid', 'processing', 'dc89ebded62942bc', 'racg2vr3', 'CFBEED4F59D434AEBD0E', '2025-04-26 23:26:01');

--
-- Triggers `order`
--
DROP TRIGGER IF EXISTS `before_insert_order`;
DELIMITER $$
CREATE TRIGGER `before_insert_order` BEFORE INSERT ON `order` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(order_id, 3)), 0) + 1 FROM `order`);
    SET NEW.order_id = CONCAT('OR', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
CREATE TABLE `order_item` (
  `order_item_id` varchar(10) NOT NULL,
  `order_id` varchar(10) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
('OI0001', 'OR0001', 'SUN002', 1, 8.00),
('OI0002', 'OR0001', 'DESS002', 1, 12.50),
('OI0003', 'OR0001', 'ICE002', 1, 4.00),
('OI0004', 'OR0002', 'DESS002', 9, 12.50),
('OI0005', 'OR0002', 'ICE003', 12, 4.00),
('OI0006', 'OR0002', 'ICE002', 1, 4.00),
('OI0007', 'OR0003', 'SUN004', 1, 8.00),
('OI0008', 'OR0003', 'ICE011', 11, 4.00),
('OI0009', 'OR0003', 'ICE016', 1, 4.00);

--
-- Triggers `order_item`
--
DROP TRIGGER IF EXISTS `before_insert_order_item`;
DELIMITER $$
CREATE TRIGGER `before_insert_order_item` BEFORE INSERT ON `order_item` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(order_item_id, 3)), 0) + 1 FROM order_item);
    SET NEW.order_item_id = CONCAT('OI', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

DROP TABLE IF EXISTS `payment_logs`;
CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `raw_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_logs`
--

INSERT INTO `payment_logs` (`id`, `order_id`, `status`, `raw_data`, `created_at`) VALUES
(14, 'OR0001', 'paid', '{\"id\":\"8052dab5e3f1c64b\",\"collection_id\":\"racg2vr3\",\"paid\":\"true\",\"state\":\"paid\",\"amount\":\"1600\",\"paid_amount\":\"1600\",\"due_at\":\"2025-4-23\",\"email\":\"emma.watson@outlook.com\",\"mobile\":\"\",\"name\":\"EMMA WATSON\",\"url\":\"https:\\/\\/www.billplz-sandbox.com\\/bills\\/8052dab5e3f1c64b\",\"paid_at\":\"2025-04-23 23:58:08 +0800\",\"transaction_id\":\"F2CE28BC041F6F6B6C40\",\"transaction_status\":\"completed\",\"x_signature\":\"3d44bfeca65e918d8cca818ca973e510237af43fe47994bdfc5f23249858742d\"}', '2025-04-23 23:58:09'),
(15, 'OR0002', 'paid', '{\"id\":\"18db41369cfe6837\",\"collection_id\":\"racg2vr3\",\"paid\":\"true\",\"state\":\"paid\",\"amount\":\"800\",\"paid_amount\":\"800\",\"due_at\":\"2025-4-24\",\"email\":\"emma.watson@outlook.com\",\"mobile\":\"\",\"name\":\"EMMA WATSON\",\"url\":\"https:\\/\\/www.billplz-sandbox.com\\/bills\\/18db41369cfe6837\",\"paid_at\":\"2025-04-24 00:00:13 +0800\",\"transaction_id\":\"41362301393FF886C389\",\"transaction_status\":\"completed\",\"x_signature\":\"6f13d937e3bbddf938b4c7cd30a12cb4678509d6a6188c5243ad0e785a6a2220\"}', '2025-04-24 00:00:14'),
(16, 'OR0001', 'paid', '{\"id\":\"ae6113c652d95b41\",\"collection_id\":\"racg2vr3\",\"paid\":\"true\",\"state\":\"paid\",\"amount\":\"2450\",\"paid_amount\":\"2450\",\"due_at\":\"2025-4-26\",\"email\":\"happy.man@gmail.com\",\"mobile\":\"\",\"name\":\"HAPPY\\ud83d\\ude06MAN\",\"url\":\"https:\\/\\/www.billplz-sandbox.com\\/bills\\/ae6113c652d95b41\",\"paid_at\":\"2025-04-26 22:01:16 +0800\",\"transaction_id\":\"F541E67E94695BEB9E9C\",\"transaction_status\":\"completed\",\"x_signature\":\"7e425c498cd2395463d14a68ecdb975383d3334ea88a1bad3a55d08c6fec6c7d\"}', '2025-04-26 22:00:34'),
(17, 'OR0002', 'paid', '{\"id\":\"3a67866199997203\",\"collection_id\":\"racg2vr3\",\"paid\":\"true\",\"state\":\"paid\",\"amount\":\"16450\",\"paid_amount\":\"16450\",\"due_at\":\"2025-4-26\",\"email\":\"happy.man@gmail.com\",\"mobile\":\"\",\"name\":\"HAPPY\\ud83d\\ude06MAN\",\"url\":\"https:\\/\\/www.billplz-sandbox.com\\/bills\\/3a67866199997203\",\"paid_at\":\"2025-04-26 23:24:10 +0800\",\"transaction_id\":\"FC9456EF130861B22202\",\"transaction_status\":\"completed\",\"x_signature\":\"081dd0dbfdb05db5e025f3eff57b3fdc797cdf43048c35610f745413933c3794\"}', '2025-04-26 23:23:26'),
(18, 'OR0003', 'paid', '{\"id\":\"dc89ebded62942bc\",\"collection_id\":\"racg2vr3\",\"paid\":\"true\",\"state\":\"paid\",\"amount\":\"5600\",\"paid_amount\":\"5600\",\"due_at\":\"2025-4-26\",\"email\":\"happy.man@gmail.com\",\"mobile\":\"\",\"name\":\"HAPPY\\ud83d\\ude06MAN\",\"url\":\"https:\\/\\/www.billplz-sandbox.com\\/bills\\/dc89ebded62942bc\",\"paid_at\":\"2025-04-26 23:26:44 +0800\",\"transaction_id\":\"CFBEED4F59D434AEBD0E\",\"transaction_status\":\"completed\",\"x_signature\":\"1200fdadd07cea83a33772bb6c5a02bd8183a2b94fa2eb00266bd213ba1f623e\"}', '2025-04-26 23:26:01'),
(19, 'OR0004', 'paid', '{\"direct_update\":true}', '2025-04-26 23:33:03'),
(20, 'OR0005', 'failed', '[]', '2025-04-26 23:40:37'),
(21, 'OR0005', 'failed', '[]', '2025-04-26 23:42:03'),
(22, 'OR0005', 'paid', '{\"direct_update\":true}', '2025-04-26 23:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `product_id` varchar(10) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `description` text NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `type_id` int(10) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `product_status` enum('Active','Inactive','Out of Stock') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `price`, `description`, `product_image`, `type_id`, `stock`, `product_status`) VALUES
('DESS001', 'Banana Split', 19.50, 'A classic dessert featuring a ripe banana sliced in half, topped with scoops of vanilla, chocolate, and strawberry ice cream. Drizzled with chocolate syrup, whipped cream, nuts, and a cherry on top for the perfect indulgence.', 'BananaSplit.png', 2, 42, 'Active'),
('DESS002', 'Bubble Waffle', 12.50, 'A crispy-on-the-outside, soft-on-the-inside Hong Kong-style waffle filled with your choice of ice cream, fresh fruits, and drizzled with syrup or chocolate. A fun and delicious treat with a unique texture.', 'BubbleWaffle.png', 2, 42, 'Active'),
('DESS003', 'Brownie à la Mode', 15.00, 'A warm, fudgy brownie served with a scoop of vanilla ice cream, creating the perfect balance between rich chocolate and creamy sweetness. Topped with chocolate sauce and whipped cream for extra indulgence.', 'brownie-ala-mode.png', 2, 57, 'Active'),
('DESS004', 'Ice Cream Sandwiches', 6.50, 'Two soft cookies or crispy wafers hugging a generous scoop of creamy ice cream. A delightful handheld dessert that comes in a variety of flavors, from classic vanilla to rich chocolate chip.', 'Ice-creamSanwiches.png', 2, 64, 'Active'),
('ICE001', 'Banana Ice-cream', 4.00, 'A creamy, fruity banana-flavored ice cream.', 'Banana.png', 3, 53, 'Active'),
('ICE002', 'Butter Pecan', 4.00, 'A rich, buttery ice cream with pecan nuts.', 'ButterPecan.png', 3, 24, 'Active'),
('ICE003', 'Cherry', 4.00, 'A bright pink ice cream with a cherry flavor.', 'Cherry.png', 3, 55, 'Active'),
('ICE004', 'Chocolate Ice-cream', 4.00, 'A classic, deep chocolate-flavored ice cream.', 'Chocolate.png', 3, 24, 'Active'),
('ICE005', 'Chocolate Almond Ice-cream', 4.00, 'Chocolate ice cream with almond pieces.', 'ChocolateAlmond.png', 3, 51, 'Active'),
('ICE006', 'Chocolate Chip Ice-cream', 4.00, 'Vanilla ice cream with chocolate chips.', 'ChocolateChip.png', 3, 25, 'Active'),
('ICE007', 'Coconut Ice-cream', 4.00, 'A tropical, coconut-flavored ice cream.', 'Coconut.png', 3, 52, 'Active'),
('ICE008', 'Coffee Ice-cream', 4.00, 'A smooth, coffee-infused ice cream.', 'Coffee.png', 3, 46, 'Active'),
('ICE009', 'Cookies \'N\' Cream Ice-cream', 4.00, 'Vanilla ice cream with crushed chocolate cookies.', 'Cookies-n-Cream.png', 3, 62, 'Active'),
('ICE010', 'Cotton Candy Ice-cream', 4.00, 'A sweet, pastel-colored cotton candy-flavored ice cream.', 'Cotton-Candy.png', 3, 77, 'Active'),
('ICE011', 'Durian Ice-cream', 4.00, 'A bold, creamy durian-flavored ice cream.', 'Durian.png', 3, 125, 'Active'),
('ICE012', 'Green Tea Ice-cream', 4.00, 'A matcha-flavored ice cream with a slightly bitter taste.', 'GreenTea.png', 3, 12, 'Active'),
('ICE013', 'Mango Ice-cream', 4.00, 'A tropical, juicy mango-flavored ice cream.', 'Mango.png', 3, 9, 'Active'),
('ICE014', 'Matcha Ice-cream', 4.00, 'A deep green, Japanese matcha-flavored ice cream.', 'Matcha.png', 3, 24, 'Active'),
('ICE015', 'Mint Chocolate Chip Ice-cream', 4.00, 'A cool mint ice cream with chocolate chips.', 'MintChocolateChip.png', 3, 25, 'Active'),
('ICE016', 'Peach Ice-cream', 4.00, 'A soft, peach-flavored ice cream.', 'Peach.png', 3, 24, 'Active'),
('ICE017', 'Raspberry Ripple Ice-cream', 4.00, 'Vanilla ice cream with swirls of raspberry sauce.', 'RaspberryRipple.png', 3, 24, 'Active'),
('ICE018', 'Strawberry', 4.00, 'A sweet, pink strawberry-flavored ice cream.', 'Strawberry.png', 3, 2, 'Active'),
('ICE019', 'Vanilla', 4.00, 'A classic, creamy vanilla-flavored ice cream.', 'Vanilla.png', 3, 24, 'Active'),
('ICE020', 'Watermelon', 4.00, 'A refreshing, watermelon-flavored ice cream.', 'Watermelon.png', 3, 12, 'Active'),
('SUN001', 'Strawberry Sundae', 8.00, 'A delightful treat featuring creamy vanilla ice cream topped with luscious strawberry sauce, fresh strawberry slices, and a dollop of whipped cream. Finished with a cherry on top for a refreshing burst of sweetness.', 'StrawberrySundae.png', 1, 42, 'Active'),
('SUN002', 'Chocolate Sundae', 8.00, 'A rich and indulgent dessert made with smooth vanilla ice cream, drizzled generously with velvety chocolate sauce, and garnished with chocolate shavings or chunks. Topped with whipped cream and a cherry for the perfect chocolatey experience.', 'ChocolateSundae.png', 1, 1, 'Active'),
('SUN003', 'Mixed Sundae', 8.00, 'A heavenly combination of classic flavors! This sundae blends vanilla and chocolate ice cream, layered with both chocolate and strawberry sauces. Topped with whipped cream, chocolate chips, and fresh fruit for a delightful balance of taste and texture.', 'MixedSundae.png', 1, 23, 'Active'),
('SUN004', 'Fruit Sundae', 8.00, 'A refreshing twist on a classic! This sundae features creamy vanilla ice cream loaded with an assortment of fresh fruits like strawberries, bananas, kiwis, and pineapples. Drizzled with fruity syrup and finished with a light whipped topping for a naturally sweet delight.', 'FruitSundae.png', 1, 24, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `image_id` varchar(10) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `product_images`
--
DROP TRIGGER IF EXISTS `before_insert_product_images`;
DELIMITER $$
CREATE TRIGGER `before_insert_product_images` BEFORE INSERT ON `product_images` FOR EACH ROW BEGIN
     DECLARE next_id INT;
     SET next_id = (SELECT IFNULL(MAX(SUBSTRING(image_id, 3)), 0) + 1 FROM product_images);
     SET NEW.image_id = CONCAT('PI', LPAD(next_id, 4, '0'));
 END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

DROP TABLE IF EXISTS `product_type`;
CREATE TABLE `product_type` (
  `type_id` int(10) NOT NULL,
  `type_name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`type_id`, `type_name`) VALUES
(1, 'Sundae'),
(2, 'Dessert'),
(3, 'Icecream');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `review_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `ratings` int(1) DEFAULT 5,
  `review_text` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `member_id`, `ratings`, `review_text`) VALUES
('R0001', 6, 5, 'I\'m a cheeky guy who likes to try out new things. BeenChilling happens to be nearby and here I come. I ordered their best seller, and it actually tasted good yet affordable. It is a place worth staying in this hot summer and I would like to visit here again.'),
('R0002', 7, 5, 'Hello, readers. I found BeenChilling on the Internet. I saw their promotion so I paid them a visit. It\'s real. I have never chilled like this before. My profile picture was literally my reaction when I took my first bite of my banana split. I\'m surprised, and I will definitely recommend it to all my friends. <br><br> P.S.: The security cat is cute though.'),
('R0003', 8, 5, 'Stand Proud. You have BeenChilling.'),
('R0004', 9, 5, 'BeenChilling is insanely foreign delicious, and they haven\'t given it all they had. Honestly, I don\'t think I wouldn\'t come even if they didn\'t have John Cena. Still, I kinda feel sorry for them. I didn\'t make it for their opening ceremony. I had fun. I am glad I got diabetes because of having BeenChilling. It\'d have been embarrassing if I let some strong opponent or old age get the best of me.'),
('R0005', 10, 5, 'Now I am become Death. The Destroyer of World. The Turkish Ice Cream Man give me a hard time. I just want an ice-cream, but since I can\'t outmaneuver him, I ended up having a banana split. It made me feels exhausted and happy at the same time. What a day!'),
('R0006', 11, 5, 'BeenChilling from yesterday,<br> BeenChilling for today,<br> BeenChilling for tomorrow.<br> The important thing is not to stop BeenChilling.<br><br> - Not by Albert Einstein'),
('R0007', 12, 5, 'Anyeonghasaeyo! I\'m your JungUn Oppa from North Korea. I will recommend BeenChilling to you guys, and you must come here in a month or I\'ll give you a free \"nuke\" and a \"vaccine\".'),
('R0008', 13, 5, 'I came looking for copper and I found BeenChilling.'),
('R0009', 14, 5, 'I BeenChilling, therefore, I am.');

--
-- Triggers `review`
--
DROP TRIGGER IF EXISTS `before_insert_review`;
DELIMITER $$
CREATE TRIGGER `before_insert_review` BEFORE INSERT ON `review` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(review_id, 2)), 0) + 1 FROM review);
    SET NEW.review_id = CONCAT('R', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

DROP TABLE IF EXISTS `shipping_address`;
CREATE TABLE `shipping_address` (
  `shipping_address_id` varchar(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `address_name` varchar(255) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(30) NOT NULL,
  `postal_code` int(5) NOT NULL,
  `country` varchar(100) NOT NULL,
  `address_phone_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_address`
--

INSERT INTO `shipping_address` (`shipping_address_id`, `user_id`, `address_name`, `recipient_name`, `street_address`, `city`, `state`, `postal_code`, `country`, `address_phone_number`, `created_at`, `updated_at`) VALUES
('SA0003', 2, 'Home', 'John Smith', '56, Blue Lane', 'Ipoh', 'Perak', 30000, 'Malaysia', '011-1111111', '2025-03-31 00:52:29', '2025-04-03 01:28:50'),
('SA0004', 3, 'Home', 'Emma Watson', '78, Red Road', 'Johor Bahru', 'Johor', 80000, 'Malaysia', '013-5792468', '2025-03-31 00:52:29', '2025-04-03 01:28:50'),
('SA0005', 3, 'Sister', 'Jane Doe', '90, Purple Path', 'Malacca', 'Malacca', 75000, 'Malaysia', '013-5792468', '2025-03-31 00:52:29', '2025-04-03 01:29:36'),
('SA0007', 5, 'Home', 'Sarah Jones', '124, White Avenue', 'Kota Kinabalu', 'Sabah', 88000, 'Malaysia', '018-4052038', '2025-03-31 00:52:29', '2025-04-03 01:28:50'),
('SA0010', 32, 'Home', 'Lana', '18, Brown Street', 'Puchong', 'Selangor', 41700, 'Malaysia', '016-7889900', '2025-04-06 02:35:56', '2025-04-06 02:35:56'),
('SA0011', 1, 'Sister', 'Maria Doe', '12, Yellow Street 1', 'Georgetown', 'Penang', 10000, 'Malaysia', '012-3456789', '2025-04-06 03:02:49', '2025-04-06 03:02:49'),
('SA0012', 1, 'Home', 'Lisa Manobal', '34, Blue Avenue', 'Ampang', 'Kuala Lumpur', 50000, 'Malaysia', '012-3456789', '2025-04-06 03:02:49', '2025-04-06 03:02:49'),
('SA0013', 1, 'Address 3', 'Lisa Manobal', '45, Green Avenue', 'Sungai Buloh', 'Kuala Lumpur', 50000, 'Malaysia', '012-2456789', '2025-04-06 03:02:49', '2025-04-06 03:02:49'),
('SA0014', 4, 'Home', 'Alice Brown', '102, Orange Street', 'Kuching', 'Sarawak', 93000, 'Malaysia', '014-7894561', '2025-04-06 03:03:08', '2025-04-06 03:03:08'),
('SA0016', 6, 'Home', 'LikeMom👍1989', '300, Happy Street', 'Segamat', 'Johor', 85000, 'Malaysia', '018-1012458', '2025-04-06 03:13:45', '2025-04-06 03:13:45'),
('SA0018', 35, 'School', 'Muthu a/l Gopalsami', 'PV 9 Residence, A - 33A - 12', 'Setapak', 'Kuala Lumpur', 50000, 'Malaysia', '016-4437889', '2025-04-06 04:11:41', '2025-04-06 04:11:41'),
('SA0019', 34, 'Home', 'Ali bin Abu Bakar', '250, Jalan Bunga Raya 3', 'Bachok', 'Kelantan', 16300, 'Malaysia', '018-6649238', '2025-04-06 06:41:29', '2025-04-06 06:41:29'),
('SA0020', 7, 'Happy Home', 'Happy😆Man', '19, Happy Street', 'Segamat', 'Johor', 85000, 'Malaysia', '012-2334037', '2025-04-26 11:51:58', '2025-04-26 11:51:58');

--
-- Triggers `shipping_address`
--
DROP TRIGGER IF EXISTS `before_insert_shipping_address`;
DELIMITER $$
CREATE TRIGGER `before_insert_shipping_address` BEFORE INSERT ON `shipping_address` FOR EACH ROW BEGIN
	DECLARE next_id INT;
    
    -- Set default recipient_name if NULL
    IF NEW.recipient_name IS NULL THEN
        SET NEW.recipient_name = (SELECT `name` FROM `user` WHERE `id` = NEW.user_id);
    END IF;
    
    -- Set default address_phone_number if NULL
    IF NEW.address_phone_number IS NULL THEN
        SET NEW.address_phone_number = (SELECT `phone_number` FROM `user` WHERE `id` = NEW.user_id);
    END IF;
    
    -- Generate shipping_id with format 'SAXXXX'
    SELECT IFNULL(MAX(CAST(SUBSTRING(shipping_address_id, 3) AS UNSIGNED)), 0) + 1 INTO next_id 
    FROM shipping_address;
    SET NEW.shipping_address_id = CONCAT('SA', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` varchar(100) NOT NULL,
  `expire` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('verify','reset') DEFAULT 'reset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL DEFAULT 'default_avatar.png',
  `phone_number` varchar(15) NOT NULL,
  `reward_point` int(6) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `role` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `photo`, `phone_number`, `reward_point`, `status`, `role`, `created_at`, `updated_at`) VALUES
(1, '1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lisa Manobal', '67e93f7b9b07b.png', '012-3456789', 0, 2, 'Admin', '2025-04-27 00:14:55', '2025-04-27 11:05:26'),
(2, 'john.smith@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'John Smith', 'default_avatar.png', '011-1111111', 0, 2, 'Admin', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(3, 'emma.watson@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emma Watson', 'default_avatar.png', '013-5792468', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(4, 'michael.chen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Michael Chen', 'default_avatar.png', '014-7894561', 0, 2, 'Admin', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(5, 'sarah.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sarah Jones', 'default_avatar.png', '018-4052038', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(6, 'likeguy64@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'LikeGuy64👍', '67e9325bde272.png', '011-2987632', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(7, 'happy.man@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Happy😆Man', '67e9341853196.png', '012-2334037', 323, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(8, 'sukuna@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sukuna👑King Of Curse', '67e934c134d7e.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(9, '2.5joSatoru@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '2.5jo Satoru', '67e93531c71d1.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(10, 'oppenheimer1904@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'OppenSmileLOL', '67e9359890e05.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(11, 'psycho22@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'PsychoPhysicist', '67e935ce73e24.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(12, 'jungun@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'JungUn Oppa', '67e90f3c6a068.png', '018-3456789', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(13, 'christopherColumbu11@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Christopher Columbus', '67e936df42922.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(14, 'mr.philosopher@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mr.Philosopher', '67e93709b294a.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(15, 'ava.nguyen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Ava Nguyen', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(16, 'james.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'James Liu', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(17, 'mia.chen@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mia Chen', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(18, 'william.park@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'William Park', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(19, 'charlotte.kim@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Charlotte Kim', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(20, 'benjamin.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Benjamin Jones', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(21, 'amelia.brown@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Amelia Brown', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(22, 'lucas.martinez@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lucas Martinez', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(23, 'harper.lee@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Harper Lee', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(24, 'henry.wong@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Henry Wong', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(25, 'evelyn.garcia@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Evelyn Garcia', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(26, 'alexander.rodriguez@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Alexander Rodriguez', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(27, 'abigail.smith@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Abigail Smith', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(28, 'jacob.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Jacob Liu', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(29, 'emily.taylor@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emily Taylor', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(30, 'mason.chen@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mason Chen', 'default_avatar.png', '', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(32, 'lana@gmail.com', '$2y$10$yvnzZ9UQm/7uouaeZkpCXe2VdqxRS.QatStY2k9.H4y.NYdLOeGh6', 'Lana', 'default_avatar.png', '016-7889900', 0, 2, 'Admin', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(34, 'ali@hotmail.com', '$2y$10$4ykwAXoiczi3Ytmxvy9cOOEGFuFslXzN9IBFQiQVE73h9LtS.I91m', 'Ali bin Abu Bakar', '67f2221919368.png', '018-6649238', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55'),
(35, 'muthu@yahoo.com', '$2y$10$eBMQqmABfkzdVIhKje9y8.2I6gUYaRISdaPIebDZ3RWl3osic7svC', 'Muthu a/l Gopalsami', 'default_avatar.png', '016-4437889', 0, 2, 'Member', '2025-04-27 00:14:55', '2025-04-27 00:14:55');

--
-- Triggers `user`
--
DROP TRIGGER IF EXISTS `before_user_insert`;
DELIMITER $$
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `user` FOR EACH ROW BEGIN
  SET NEW.created_at = NOW();
  SET NEW.updated_at = NOW();
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_user_update`;
DELIMITER $$
CREATE TRIGGER `before_user_update` BEFORE UPDATE ON `user` FOR EACH ROW BEGIN
  SET NEW.updated_at = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `wishlist_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','added_to_cart','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `member_id`, `created_at`, `updated_at`, `status`) VALUES
('WL0001', 7, '2025-04-25 13:02:40', '2025-04-25 13:02:44', 'added_to_cart'),
('WL0002', 7, '2025-04-26 19:52:32', '2025-04-26 22:07:25', 'added_to_cart'),
('WL0003', 7, '2025-04-26 22:07:43', '2025-04-26 22:11:49', 'added_to_cart'),
('WL0004', 7, '2025-04-26 22:15:11', '2025-04-26 22:15:20', 'added_to_cart'),
('WL0005', 7, '2025-04-27 08:44:43', '2025-04-27 10:28:46', 'added_to_cart');

--
-- Triggers `wishlist`
--
DROP TRIGGER IF EXISTS `before_insert_wishlist`;
DELIMITER $$
CREATE TRIGGER `before_insert_wishlist` BEFORE INSERT ON `wishlist` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(wishlist_id, 3)), 0) + 1 FROM wishlist);
    SET NEW.wishlist_id = CONCAT('WL', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_item`
--

DROP TABLE IF EXISTS `wishlist_item`;
CREATE TABLE `wishlist_item` (
  `wishlist_item_id` varchar(10) NOT NULL,
  `wishlist_id` varchar(10) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','checked_out','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist_item`
--

INSERT INTO `wishlist_item` (`wishlist_item_id`, `wishlist_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`, `status`) VALUES
('WI0001', 'WL0001', 'SUN002', 1, 8.00, '2025-04-25 13:02:40', '2025-04-25 13:02:40', 'active'),
('WI0002', 'WL0001', 'DESS002', 1, 12.50, '2025-04-25 13:02:41', '2025-04-25 13:02:41', 'active'),
('WI0003', 'WL0001', 'ICE002', 1, 4.00, '2025-04-25 13:02:41', '2025-04-25 13:02:41', 'active'),
('WI0004', 'WL0002', 'SUN004', 1, 8.00, '2025-04-26 22:07:16', '2025-04-26 22:07:16', 'active'),
('WI0005', 'WL0002', 'DESS003', 6, 15.00, '2025-04-26 22:07:19', '2025-04-26 22:07:24', 'active'),
('WI0006', 'WL0002', 'DESS001', 1, 19.50, '2025-04-26 22:07:19', '2025-04-26 22:07:19', 'active'),
('WI0007', 'WL0003', 'SUN004', 7, 8.00, '2025-04-26 22:09:42', '2025-04-26 22:11:32', 'active'),
('WI0008', 'WL0003', 'SUN002', 1, 8.00, '2025-04-26 22:09:43', '2025-04-26 22:09:43', 'active'),
('WI0009', 'WL0004', 'SUN004', 1, 8.00, '2025-04-26 22:15:11', '2025-04-26 22:15:11', 'active'),
('WI0010', 'WL0004', 'DESS002', 1, 12.50, '2025-04-26 22:15:13', '2025-04-26 22:15:13', 'active'),
('WI0011', 'WL0004', 'DESS004', 1, 6.50, '2025-04-26 22:15:14', '2025-04-26 22:15:14', 'active'),
('WI0012', 'WL0005', 'DESS003', 1, 15.00, '2025-04-27 08:44:43', '2025-04-27 08:44:43', 'active'),
('WI0013', 'WL0005', 'ICE003', 1, 4.00, '2025-04-27 08:44:44', '2025-04-27 08:44:44', 'active');

--
-- Triggers `wishlist_item`
--
DROP TRIGGER IF EXISTS `before_insert_wishlist_item`;
DELIMITER $$
CREATE TRIGGER `before_insert_wishlist_item` BEFORE INSERT ON `wishlist_item` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT IFNULL(MAX(SUBSTRING(wishlist_item_id, 3)), 0) + 1 FROM wishlist_item);
    SET NEW.wishlist_item_id = CONCAT('WI', LPAD(next_id, 4, '0'));
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_member_id` (`member_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ip_details`
--
ALTER TABLE `ip_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `fk_order_member_id` (`member_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `fk_order_item_product_id` (`product_id`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `Product_Type` (`type_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_product_images_product_id` (`product_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD PRIMARY KEY (`shipping_address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token_ibfk_1` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `wishlist_ibfk_1` (`member_id`);

--
-- Indexes for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD PRIMARY KEY (`wishlist_item_id`),
  ADD KEY `wishlist_item_ibfk_1` (`product_id`),
  ADD KEY `wishlist_item_ibfk_2` (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ip_details`
--
ALTER TABLE `ip_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_member_id` FOREIGN KEY (`member_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `fk_cart_item_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_item_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_cart_id` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `fk_order_member_id` FOREIGN KEY (`member_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_order_item_order_id` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_item_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_type_id` FOREIGN KEY (`type_id`) REFERENCES `product_type` (`type_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_member_id` FOREIGN KEY (`member_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD CONSTRAINT `fk_shipping_address_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_token_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_member_id` FOREIGN KEY (`member_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `wishlist_item`
--
ALTER TABLE `wishlist_item`
  ADD CONSTRAINT `fk_wishlist_item_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `fk_wishlist_item_wishlist_id` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`wishlist_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
