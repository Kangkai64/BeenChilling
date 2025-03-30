-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 02:29 PM
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
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `review_id` varchar(10) NOT NULL,
  `member_id` int(10) NOT NULL,
  `ratings` int(1) NOT NULL DEFAULT 5,
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
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) NOT NULL,
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
(2, 'john.smith@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'John Smith', 'default_avatar.png', '011-1111111', '12, Yellow Street', 0, 'Admin'),
(3, 'emma.watson@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emma Watson', 'default_avatar.png', '', '', 0, 'Member'),
(4, 'michael.chen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Michael Chen', 'default_avatar.png', '', '', 0, 'Member'),
(5, 'sarah.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sarah Jones', 'default_avatar.png', '', '', 0, 'Member'),
(6, 'likeguy64@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'LikeGuy64👍', '67e9325bde272.png', '', '', 0, 'Member'),
(7, 'happy.man@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Happy😆Man', '67e9341853196.png', '', '', 0, 'Member'),
(8, 'sukuna@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Sukuna👑King Of Curse', '67e934c134d7e.png', '', '', 0, 'Member'),
(9, '2.5joSatoru@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', '2.5jo Satoru', '67e93531c71d1.png', '', '', 0, 'Member'),
(10, 'oppenheimer1904@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'OppenSmileLOL', '67e9359890e05.png', '', '', 0, 'Member'),
(11, 'psycho22@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'PsychoPhysicist', '67e935ce73e24.png', '', '', 0, 'Member'),
(12, 'jungun@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'JungUn Oppa', '67e90f3c6a068.png', '012-3456789', '222, Street 5, North Korea', 0, '1'),
(13, 'christopherColumbu11@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Christopher Columbus', '67e936df42922.png', '', '', 0, 'Member'),
(14, 'mr.philosopher@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mr.Philosopher', '67e93709b294a.png', '', '', 0, 'Member'),
(15, 'ava.nguyen@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Ava Nguyen', 'default_avatar.png', '', '', 0, 'Member'),
(16, 'james.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'James Liu', 'default_avatar.png', '', '', 0, 'Member'),
(17, 'mia.chen@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mia Chen', 'default_avatar.png', '', '', 0, 'Member'),
(18, 'william.park@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'William Park', 'default_avatar.png', '', '', 0, 'Member'),
(19, 'charlotte.kim@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Charlotte Kim', 'default_avatar.png', '', '', 0, 'Member'),
(20, 'benjamin.jones@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Benjamin Jones', 'default_avatar.png', '', '', 0, 'Member'),
(21, 'amelia.brown@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Amelia Brown', 'default_avatar.png', '', '', 0, 'Member'),
(22, 'lucas.martinez@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Lucas Martinez', 'default_avatar.png', '', '', 0, 'Member'),
(23, 'harper.lee@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Harper Lee', 'default_avatar.png', '', '', 0, 'Member'),
(24, 'henry.wong@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Henry Wong', 'default_avatar.png', '', '', 0, 'Member'),
(25, 'evelyn.garcia@example.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Evelyn Garcia', 'default_avatar.png', '', '', 0, 'Member'),
(26, 'alexander.rodriguez@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Alexander Rodriguez', 'default_avatar.png', '', '', 0, 'Member'),
(27, 'abigail.smith@yahoo.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Abigail Smith', 'default_avatar.png', '', '', 0, 'Member'),
(28, 'jacob.liu@outlook.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Jacob Liu', 'default_avatar.png', '', '', 0, 'Member'),
(29, 'emily.taylor@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Emily Taylor', 'default_avatar.png', '', '', 0, 'Member'),
(30, 'mason.chen@hotmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mason Chen', 'default_avatar.png', '', '', 0, 'Member');

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
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `member_id` (`member_id`);

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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
