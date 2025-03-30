-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 08:51 AM
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
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
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
('SUN004', 'ruit Sundae', 8.00, 'A refreshing twist on a classic! This sundae features creamy vanilla ice cream loaded with an assortment of fresh fruits like strawberries, bananas, kiwis, and pineapples. Drizzled with fruity syrup and finished with a light whipped topping for a naturally sweet delight.', 'FruitSundae.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `producttype`
--

DROP TABLE IF EXISTS `producttype`;
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
  `shipping_address` varchar(255) NOT NULL,
  `reward_point` int(6) NOT NULL DEFAULT 0,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `photo`, `phone_number`, `shipping_address`, `reward_point`, `role`) VALUES
(1, '1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lisa Manobal', 'default_avatar.png', '', '', 0, 'Admin'),
(2, 'john.smith@example.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'John Smith', 'default_avatar.png', '011-1111111', '12, Yellow Street', 0, 'Admin'),
(3, 'emma.watson@outlook.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Emma Watson', 'default_avatar.png', '', '', 0, 'Member'),
(4, 'michael.chen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Michael Chen', 'default_avatar.png', '', '', 0, 'Member'),
(5, 'sarah.jones@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Sarah Jones', 'default_avatar.png', '', '', 0, 'Member'),
(6, 'david.kim@hotmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'David Kim', 'default_avatar.png', '', '', 0, 'Member'),
(7, 'olivia.brown@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Olivia Brown', 'default_avatar.png', '', '', 0, 'Member'),
(8, 'ryan.lee@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Ryan Lee', 'default_avatar.png', '', '', 0, 'Member'),
(9, 'emily.garcia@yahoo.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Emily Garcia', 'default_avatar.png', '', '', 0, 'Member'),
(10, 'alex.martinez@outlook.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Alex Martinez', 'default_avatar.png', '', '', 0, 'Member'),
(11, 'sophia.park@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Sophia Park', 'default_avatar.png', '', '', 0, 'Member'),
(12, 'daniel.wong@hotmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Daniel Wong', 'default_avatar.png', '', '', 0, 'Member'),
(13, 'isabella.taylor@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Isabella Taylor', 'default_avatar.png', '', '', 0, 'Member'),
(14, 'ethan.rodriguez@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Ethan Rodriguez', 'default_avatar.png', '', '', 0, 'Member'),
(15, 'ava.nguyen@yahoo.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Ava Nguyen', 'default_avatar.png', '', '', 0, 'Member'),
(16, 'james.liu@outlook.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'James Liu', 'default_avatar.png', '', '', 0, 'Member'),
(17, 'mia.chen@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Mia Chen', 'default_avatar.png', '', '', 0, 'Member'),
(18, 'william.park@hotmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'William Park', 'default_avatar.png', '', '', 0, 'Member'),
(19, 'charlotte.kim@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Charlotte Kim', 'default_avatar.png', '', '', 0, 'Member'),
(20, 'benjamin.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Benjamin Jones', 'default_avatar.png', '', '', 0, 'Member'),
(21, 'amelia.brown@yahoo.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Amelia Brown', 'default_avatar.png', '', '', 0, 'Member'),
(22, 'lucas.martinez@outlook.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Lucas Martinez', 'default_avatar.png', '', '', 0, 'Member'),
(23, 'harper.lee@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Harper Lee', 'default_avatar.png', '', '', 0, 'Member'),
(24, 'henry.wong@hotmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Henry Wong', 'default_avatar.png', '', '', 0, 'Member'),
(25, 'evelyn.garcia@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Evelyn Garcia', 'default_avatar.png', '', '', 0, 'Member'),
(26, 'alexander.rodriguez@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Alexander Rodriguez', 'default_avatar.png', '', '', 0, 'Member'),
(27, 'abigail.smith@yahoo.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Abigail Smith', 'default_avatar.png', '', '', 0, 'Member'),
(28, 'jacob.liu@outlook.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Jacob Liu', 'default_avatar.png', '', '', 0, 'Member'),
(29, 'emily.taylor@gmail.com', '8cb2237d0679ca88db6464eac60da96345513964', 'Emily Taylor', 'default_avatar.png', '', '', 0, 'Member'),
(30, 'mason.chen@hotmail.com', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'Mason Chen', 'default_avatar.png', '', '', 0, 'Member');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `ProductType` FOREIGN KEY (`TypeID`) REFERENCES `producttype` (`TypeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
