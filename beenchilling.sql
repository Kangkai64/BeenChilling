-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 05:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

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
('CA0001', 22, '2025-04-23 13:49:23', '2025-04-23 13:57:28', ''),
('CA0002', 22, '2025-04-23 14:01:02', '2025-04-23 14:27:53', 'abandoned'),
('CA0003', 3, '2025-04-23 14:28:53', '2025-04-23 14:35:34', 'abandoned'),
('CA0004', 22, '2025-04-23 14:35:54', '2025-04-23 14:37:50', ''),
('CA0005', 22, '2025-04-23 14:53:48', '2025-04-23 14:53:59', 'abandoned'),
('CA0006', 3, '2025-04-23 14:54:36', '2025-04-23 14:54:42', ''),
('CA0007', 3, '2025-04-23 14:57:28', '2025-04-23 14:57:33', ''),
('CA0008', 3, '2025-04-23 15:11:40', '2025-04-23 15:11:49', ''),
('CA0009', 3, '2025-04-23 15:19:30', '2025-04-23 15:19:38', ''),
('CA0010', 3, '2025-04-23 15:40:04', '2025-04-23 15:40:11', ''),
('CA0011', 3, '2025-04-23 15:44:09', '2025-04-23 15:44:18', ''),
('CA0012', 3, '2025-04-23 15:49:37', '2025-04-23 15:49:45', ''),
('CA0013', 3, '2025-04-23 16:02:24', '2025-04-23 16:02:31', ''),
('CA0014', 3, '2025-04-23 16:02:41', '2025-04-23 16:03:09', ''),
('CA0015', 3, '2025-04-23 16:04:38', '2025-04-23 16:04:53', ''),
('CA0016', 3, '2025-04-23 16:05:07', '2025-04-23 22:27:06', ''),
('CA0017', 3, '2025-04-23 22:28:02', '2025-04-23 22:28:09', ''),
('CA0018', 3, '2025-04-23 22:28:17', '2025-04-23 22:34:33', ''),
('CA0019', 3, '2025-04-23 22:39:05', '2025-04-23 22:41:51', ''),
('CA0020', 3, '2025-04-23 22:49:24', '2025-04-23 22:49:39', ''),
('CA0021', 3, '2025-04-23 22:49:49', '2025-04-23 23:01:13', ''),
('CA0022', 3, '2025-04-23 22:50:10', '2025-04-23 23:09:39', ''),
('CA0023', 3, '2025-04-23 22:57:39', '2025-04-23 23:22:51', ''),
('CA0024', 3, '2025-04-23 22:58:11', '2025-04-23 22:58:11', 'active'),
('CA0025', 3, '2025-04-23 23:09:53', '2025-04-23 23:09:53', 'active'),
('CA0026', 3, '2025-04-23 23:22:59', '2025-04-23 23:22:59', 'active');

--
-- Triggers `cart`
--
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
('CI0001', 'CA0001', 'SUN002', 1, 8.00, '2025-04-23 13:49:23', '2025-04-23 13:49:23'),
('CI0002', 'CA0002', 'SUN003', 1, 8.00, '2025-04-23 14:01:02', '2025-04-23 14:26:50'),
('CI0003', 'CA0002', 'SUN004', 1, 8.00, '2025-04-23 14:01:05', '2025-04-23 14:01:05'),
('CI0004', 'CA0003', 'SUN003', 1, 8.00, '2025-04-23 14:28:53', '2025-04-23 14:28:53'),
('CI0005', 'CA0004', 'SUN003', 1, 8.00, '2025-04-23 14:35:54', '2025-04-23 14:35:54'),
('CI0006', 'CA0005', 'SUN003', 1, 8.00, '2025-04-23 14:53:48', '2025-04-23 14:53:48'),
('CI0007', 'CA0006', 'SUN003', 1, 8.00, '2025-04-23 14:54:36', '2025-04-23 14:54:36'),
('CI0008', 'CA0007', 'SUN002', 1, 8.00, '2025-04-23 14:57:28', '2025-04-23 14:57:28'),
('CI0009', 'CA0008', 'DESS002', 1, 12.50, '2025-04-23 15:11:40', '2025-04-23 15:11:40'),
('CI0010', 'CA0009', 'SUN002', 1, 8.00, '2025-04-23 15:19:30', '2025-04-23 15:19:30'),
('CI0011', 'CA0010', 'SUN003', 1, 8.00, '2025-04-23 15:40:04', '2025-04-23 15:40:04'),
('CI0012', 'CA0011', 'DESS001', 1, 19.50, '2025-04-23 15:44:09', '2025-04-23 15:44:09'),
('CI0013', 'CA0012', 'SUN002', 1, 8.00, '2025-04-23 15:49:37', '2025-04-23 15:49:37'),
('CI0014', 'CA0013', 'SUN003', 1, 8.00, '2025-04-23 16:02:24', '2025-04-23 16:02:24'),
('CI0015', 'CA0014', 'SUN003', 1, 8.00, '2025-04-23 16:03:01', '2025-04-23 16:03:01'),
('CI0016', 'CA0015', 'SUN003', 12, 8.00, '2025-04-23 16:04:41', '2025-04-23 16:04:49'),
('CI0017', 'CA0016', 'SUN003', 10, 8.00, '2025-04-23 18:43:01', '2025-04-23 22:26:49'),
('CI0018', 'CA0016', 'SUN002', 12, 8.00, '2025-04-23 22:26:44', '2025-04-23 22:26:51'),
('CI0019', 'CA0016', 'DESS003', 5, 15.00, '2025-04-23 22:26:45', '2025-04-23 22:26:52'),
('CI0020', 'CA0017', 'SUN002', 1, 8.00, '2025-04-23 22:28:02', '2025-04-23 22:28:02'),
('CI0021', 'CA0018', 'SUN003', 1, 8.00, '2025-04-23 22:34:27', '2025-04-23 22:34:27'),
('CI0022', 'CA0018', 'SUN002', 1, 8.00, '2025-04-23 22:34:27', '2025-04-23 22:34:27'),
('CI0023', 'CA0019', 'DESS002', 1, 12.50, '2025-04-23 22:41:43', '2025-04-23 22:41:43'),
('CI0024', 'CA0019', 'DESS003', 1, 15.00, '2025-04-23 22:41:44', '2025-04-23 22:41:44'),
('CI0025', 'CA0020', 'DESS001', 1, 19.50, '2025-04-23 22:49:24', '2025-04-23 22:49:24'),
('CI0026', 'CA0020', 'DESS002', 1, 12.50, '2025-04-23 22:49:25', '2025-04-23 22:49:25'),
('CI0027', 'CA0020', 'DESS003', 1, 15.00, '2025-04-23 22:49:26', '2025-04-23 22:49:26'),
('CI0028', 'CA0020', 'DESS004', 1, 6.50, '2025-04-23 22:49:27', '2025-04-23 22:49:27'),
('CI0029', 'CA0021', 'SUN002', 1, 8.00, '2025-04-23 23:01:07', '2025-04-23 23:01:07'),
('CI0030', 'CA0022', 'DESS003', 1, 15.00, '2025-04-23 23:01:22', '2025-04-23 23:01:22'),
('CI0031', 'CA0023', 'DESS003', 1, 15.00, '2025-04-23 23:21:24', '2025-04-23 23:21:24'),
('CI0032', 'CA0023', 'DESS002', 1, 12.50, '2025-04-23 23:21:24', '2025-04-23 23:21:24');

--
-- Triggers `cart_item`
--
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
-- Table structure for table `order`
--

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
  `order_status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `billplz_bill_id` varchar(100) DEFAULT NULL,
  `billplz_collection_id` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `order`
--
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

CREATE TABLE `order_item` (
  `order_item_id` varchar(10) NOT NULL,
  `order_id` varchar(10) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `order_item`
--
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

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `raw_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` varchar(10) NOT NULL,
  `ProductName` varchar(30) NOT NULL,
  `Price` decimal(4,2) NOT NULL,
  `Description` text NOT NULL,
  `ProductImage` varchar(255) DEFAULT NULL,
  `TypeID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `ProductName`, `Price`, `Description`, `ProductImage`, `TypeID`) VALUES
('DESS001', 'Banana Split', 19.50, ' A classic dessert featuring a ripe banana sliced in half, topped with scoops of vanilla, chocolate, and strawberry ice cream. Drizzled with chocolate syrup, whipped cream, nuts, and a cherry on top for the perfect indulgence.', 'BananaSplit.png', 2),
('DESS002', 'Bubble Waffle', 12.50, 'A crispy-on-the-outside, soft-on-the-inside Hong Kong-style waffle filled with your choice of ice cream, fresh fruits, and drizzled with syrup or chocolate. A fun and delicious treat with a unique texture.', 'BubbleWaffle.png', 2),
('DESS003', 'Brownie à la Mode', 15.00, 'A warm, fudgy brownie served with a scoop of vanilla ice cream, creating the perfect balance between rich chocolate and creamy sweetness. Topped with chocolate sauce and whipped cream for extra indulgence.', 'brownie-ala-mode.png', 2),
('DESS004', 'Ice Cream Sandwiches', 6.50, 'Two soft cookies or crispy wafers hugging a generous scoop of creamy ice cream. A delightful handheld dessert that comes in a variety of flavors, from classic vanilla to rich chocolate chip.', 'Ice-creamSanwiches.png', 2),
('ICE001', 'Banana Ice-cream', 4.00, 'A creamy, fruity banana-flavored ice cream.', 'Banana.png', 3),
('ICE002', 'Butter Pecan', 4.00, 'A rich, buttery ice cream with pecan nuts.', 'ButterPecan.png', 3),
('ICE003', 'Cherry', 4.00, 'A bright pink ice cream with a cherry flavor.', 'Cherry.png', 3),
('ICE004', 'Chocolate Ice-cream', 4.00, 'A classic, deep chocolate-flavored ice cream.', 'Chocolate.png', 3),
('ICE005', 'Chocolate Almond Ice-cream', 4.00, 'Chocolate ice cream with almond pieces.', 'ChocolateAlmond.png', 3),
('ICE006', 'Chocolate Chip Ice-cream', 4.00, 'Vanilla ice cream with chocolate chips.', 'ChocolateChip.png', 3),
('ICE007', 'Coconut Ice-cream', 4.00, 'A tropical, coconut-flavored ice cream.', 'Coconut.png', 3),
('ICE008', 'Coffee Ice-cream', 4.00, 'A smooth, coffee-infused ice cream.', 'Coffee.png', 3),
('ICE009', 'Cookies \'N\' Cream Ice-cream', 4.00, 'Vanilla ice cream with crushed chocolate cookies.', 'Cookies-n-Cream.png', 3),
('ICE010', 'Cotton Candy Ice-cream', 4.00, 'A sweet, pastel-colored cotton candy-flavored ice cream.', 'Cotton-Candy.png', 3),
('ICE011', 'Durian Ice-cream', 0.00, 'A bold, creamy durian-flavored ice cream.', 'Durian.png', 3),
('ICE012', 'Green Tea Ice-cream', 4.00, 'A matcha-flavored ice cream with a slightly bitter taste.', 'GreenTea.png', 3),
('ICE013', 'Mango Ice-cream', 4.00, 'A tropical, juicy mango-flavored ice cream.', 'Mango.png', 3),
('ICE014', 'Matcha Ice-cream', 4.00, 'A deep green, Japanese matcha-flavored ice cream.', 'Matcha.png', 3),
('ICE015', 'Mint Chocolate Chip Ice-cream', 4.00, 'A cool mint ice cream with chocolate chips.', 'MintChocolateChip.png', 3),
('ICE016', 'Peach Ice-cream', 4.00, 'A soft, peach-flavored ice cream.', 'Peach.png', 3),
('ICE017', 'Raspberry Ripple Ice-cream', 4.00, 'Vanilla ice cream with swirls of raspberry sauce.', 'RaspberryRipple.png', 3),
('ICE018', 'Strawberry ', 4.00, 'A sweet, pink strawberry-flavored ice cream.', 'Strawberry.png', 3),
('ICE019', 'Vanilla', 4.00, 'A classic, creamy vanilla-flavored ice cream.', 'Vanilla.png', 3),
('ICE020', 'Watermelon', 4.00, 'A refreshing, watermelon-flavored ice cream.', 'Watermelon.png', 3),
('SUN001', 'Strawberry Sundae', 8.00, 'A delightful treat featuring creamy vanilla ice cream topped with luscious strawberry sauce, fresh strawberry slices, and a dollop of whipped cream. Finished with a cherry on top for a refreshing burst of sweetness.', 'StrawberrySundae.png', 1),
('SUN002', 'Chocolate Sundae', 8.00, 'A rich and indulgent dessert made with smooth vanilla ice cream, drizzled generously with velvety chocolate sauce, and garnished with chocolate shavings or chunks. Topped with whipped cream and a cherry for the perfect chocolatey experience.', 'ChocolateSundae.png', 1),
('SUN003', 'Mixed Sundae', 8.00, 'A heavenly combination of classic flavors! This sundae blends vanilla and chocolate ice cream, layered with both chocolate and strawberry sauces. Topped with whipped cream, chocolate chips, and fresh fruit for a delightful balance of taste and texture.', 'MixedSundae.png', 1),
('SUN004', 'Fruit Sundae', 8.00, 'A refreshing twist on a classic! This sundae features creamy vanilla ice cream loaded with an assortment of fresh fruits like strawberries, bananas, kiwis, and pineapples. Drizzled with fruity syrup and finished with a light whipped topping for a naturally sweet delight.', 'FruitSundae.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `producttype`
--

CREATE TABLE `producttype` (
  `TypeID` int(10) NOT NULL,
  `TypeName` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `producttype`
--

INSERT INTO `producttype` (`TypeID`, `TypeName`) VALUES
(1, 'Sundae'),
(2, 'Dessert'),
(3, 'Icecream');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `shipping_address`
--

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
('SA0015', 7, 'Happy Home', 'Happy😆Man', '19, Happy Street', 'Segamat', 'Johor', 85000, 'Malaysia', '012-2334037', '2025-04-06 03:09:29', '2025-04-06 03:09:29'),
('SA0016', 6, 'Home', 'LikeMom👍1989', '300, Happy Street', 'Segamat', 'Johor', 85000, 'Malaysia', '018-1012458', '2025-04-06 03:13:45', '2025-04-06 03:13:45'),
('SA0018', 35, 'School', 'Muthu a/l Gopalsami', 'PV 9 Residence, A - 33A - 12', 'Setapak', 'Kuala Lumpur', 50000, 'Malaysia', '016-4437889', '2025-04-06 04:11:41', '2025-04-06 04:11:41'),
('SA0019', 34, 'Home', 'Ali bin Abu Bakar', '250, Jalan Bunga Raya 3', 'Bachok', 'Kelantan', 16300, 'Malaysia', '018-6649238', '2025-04-06 06:41:29', '2025-04-06 06:41:29');

--
-- Triggers `shipping_address`
--
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

CREATE TABLE `token` (
  `id` varchar(100) NOT NULL,
  `expire` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL DEFAULT 'default_avatar.png',
  `phone_number` varchar(15) NOT NULL,
  `reward_point` int(6) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `photo`, `phone_number`, `reward_point`, `status`, `role`) VALUES
(1, '1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lisa Manobal', '67e93f7b9b07b.png', '012-3456789', 0, 1, 'Admin'),
(2, 'john.smith@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'John Smith', 'default_avatar.png', '011-1111111', 0, 1, 'Admin'),
(3, 'emma.watson@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emma Watson', 'default_avatar.png', '013-5792468', 389, 1, 'Member'),
(4, 'michael.chen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Michael Chen', 'default_avatar.png', '014-7894561', 0, 1, 'Admin'),
(5, 'sarah.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sarah Jones', 'default_avatar.png', '018-4052038', 0, 1, 'Member'),
(6, 'likeguy64@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'LikeGuy64👍', '67e9325bde272.png', '011-2987632', 0, 1, 'Member'),
(7, 'happy.man@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Happy😆Man', '67e9341853196.png', '012-2334037', 0, 1, 'Member'),
(8, 'sukuna@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sukuna👑King Of Curse', '67e934c134d7e.png', '', 0, 1, 'Member'),
(9, '2.5joSatoru@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '2.5jo Satoru', '67e93531c71d1.png', '', 0, 1, 'Member'),
(10, 'oppenheimer1904@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'OppenSmileLOL', '67e9359890e05.png', '', 0, 1, 'Member'),
(11, 'psycho22@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'PsychoPhysicist', '67e935ce73e24.png', '', 0, 1, 'Member'),
(12, 'jungun@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'JungUn Oppa', '67e90f3c6a068.png', '018-3456789', 0, 1, 'Member'),
(13, 'christopherColumbu11@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Christopher Columbus', '67e936df42922.png', '', 0, 1, 'Member'),
(14, 'mr.philosopher@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mr.Philosopher', '67e93709b294a.png', '', 0, 1, 'Member'),
(15, 'ava.nguyen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Ava Nguyen', 'default_avatar.png', '', 0, 1, 'Member'),
(16, 'james.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'James Liu', 'default_avatar.png', '', 0, 1, 'Member'),
(17, 'mia.chen@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mia Chen', 'default_avatar.png', '', 0, 1, 'Member'),
(18, 'william.park@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'William Park', 'default_avatar.png', '', 0, 1, 'Member'),
(19, 'charlotte.kim@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Charlotte Kim', 'default_avatar.png', '', 0, 1, 'Member'),
(20, 'benjamin.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Benjamin Jones', 'default_avatar.png', '', 0, 1, 'Member'),
(21, 'amelia.brown@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Amelia Brown', 'default_avatar.png', '', 0, 1, 'Member'),
(22, 'lucas.martinez@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lucas Martinez', 'default_avatar.png', '', 0, 1, 'Member'),
(23, 'harper.lee@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Harper Lee', 'default_avatar.png', '', 0, 1, 'Member'),
(24, 'henry.wong@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Henry Wong', 'default_avatar.png', '', 0, 1, 'Member'),
(25, 'evelyn.garcia@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Evelyn Garcia', 'default_avatar.png', '', 0, 1, 'Member'),
(26, 'alexander.rodriguez@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Alexander Rodriguez', 'default_avatar.png', '', 0, 1, 'Member'),
(27, 'abigail.smith@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Abigail Smith', 'default_avatar.png', '', 0, 1, 'Member'),
(28, 'jacob.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Jacob Liu', 'default_avatar.png', '', 0, 1, 'Member'),
(29, 'emily.taylor@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emily Taylor', 'default_avatar.png', '', 0, 1, 'Member'),
(30, 'mason.chen@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mason Chen', 'default_avatar.png', '', 0, 1, 'Member'),
(32, 'lana@gmail.com', '$2y$10$yvnzZ9UQm/7uouaeZkpCXe2VdqxRS.QatStY2k9.H4y.NYdLOeGh6', 'Lana', 'default_avatar.png', '016-7889900', 0, 1, 'Admin'),
(34, 'ali@hotmail.com', '$2y$10$4ykwAXoiczi3Ytmxvy9cOOEGFuFslXzN9IBFQiQVE73h9LtS.I91m', 'Ali bin Abu Bakar', '67f2221919368.png', '018-6649238', 0, 1, 'Member'),
(35, 'muthu@yahoo.com', '$2y$10$eBMQqmABfkzdVIhKje9y8.2I6gUYaRISdaPIebDZ3RWl3osic7svC', 'Muthu a/l Gopalsami', 'default_avatar.png', '016-4437889', 0, 1, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','added_to_cart','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_item`
--

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
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `cart_id` (`cart_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

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
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `ProductType` (`TypeID`);

--
-- Indexes for table `producttype`
--
ALTER TABLE `producttype`
  ADD PRIMARY KEY (`TypeID`);

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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `ProductType` FOREIGN KEY (`TypeID`) REFERENCES `producttype` (`TypeID`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `member_id` FOREIGN KEY (`member_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `shipping_address`
--
ALTER TABLE `shipping_address`
  ADD CONSTRAINT `shipping_address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
