-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 08:48 AM
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
-- Database: `auto_parts`
--

-- --------------------------------------------------------

--
-- Table structure for table `auto_parts`
--

CREATE TABLE `auto_parts` (
  `part_id` int(11) NOT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `part_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auto_parts`
--

INSERT INTO `auto_parts` (`part_id`, `subcategory_id`, `part_name`, `description`, `price`, `stock`, `image_url`, `created_at`) VALUES
(1, 1, 'Honda 70cc Piston Kit', 'Piston kit compatible with Honda 70cc', 1200.00, 30, 'uploads/pic1.jpg', '2025-11-15 08:08:16'),
(2, 1, 'CD70 Engine Valve Set', 'OEM engine valve set for CD70', 850.00, 50, 'uploads/pic2.jpg', '2025-11-15 08:08:16'),
(3, 1, '70cc Clutch Plate Set', 'Heat resistant clutch plates', 900.00, 40, 'uploads/pic3.jpg', '2025-11-15 08:08:16'),
(4, 1, 'Atom Carburetor 70cc', 'Carburetor for Honda CD70', 2600.00, 20, 'uploads/pic4.jpg', '2025-11-15 08:08:16'),
(5, 1, 'Engine Oil Seal Kit 70cc', 'Complete oil seal kit', 350.00, 80, 'uploads/pic5.jpg', '2025-11-15 08:08:16'),
(6, 2, 'Headlight Assembly 70cc', 'Complete headlight assembly', 350.00, 60, 'uploads/pic6.jpg', '2025-11-15 08:08:16'),
(7, 2, 'Battery 12V 5Ah', 'Motorbike battery 12V', 2500.00, 25, 'uploads/pic7.jpg', '2025-11-15 08:08:16'),
(8, 2, 'Ignition Coil', 'High performance ignition coil', 2500.00, 35, 'uploads/pic8.jpg', '2025-11-15 08:08:16'),
(9, 2, 'Switch Gear Set', 'Handlebar switch gear', 450.00, 45, 'uploads/pic9.jpg', '2025-11-15 08:08:16'),
(10, 2, 'Spark Plug', 'Standard spark plug', 80.00, 200, 'uploads/pic10.jpg', '2025-11-15 08:08:16'),
(11, 3, 'Seat Cover 70cc', 'Comfort seat cover', 500.00, 70, 'uploads/pic11.jpg', '2025-11-15 08:08:16'),
(12, 3, 'Handle Grip Set', 'Rubber handle grips', 250.00, 90, 'uploads/pic12.jpg', '2025-11-15 08:08:16'),
(13, 3, 'Mirror Pair', 'Left & right mirrors', 300.00, 100, 'uploads/pic13.jpg', '2025-11-15 08:08:16'),
(14, 3, 'Tool Kit', 'Basic bike tool kit', 800.00, 40, 'uploads/pic14.jpg', '2025-11-15 08:08:16'),
(15, 3, 'Luggage Rack', 'Rear luggage rack', 1200.00, 30, 'uploads/pic15.jpg', '2025-11-15 08:08:16'),
(16, 4, 'Front Fender', 'Front fender for 70cc', 350.00, 50, 'uploads/pic16.jpg', '2025-11-15 08:08:16'),
(17, 4, 'Side Panel Set', 'Plastic side panels', 600.00, 60, 'uploads/pic17.jpg', '2025-11-15 08:08:16'),
(18, 4, 'Fuel Tank Cover', 'Tank cover', 900.00, 20, 'uploads/pic18.jpg', '2025-11-15 08:08:16'),
(19, 4, 'Rear Mudguard', 'Rear mudguard', 250.00, 80, 'uploads/pic19.jpg', '2025-11-15 08:08:16'),
(20, 4, 'Fairing Clip Set', 'Clip repair set', 120.00, 150, 'uploads/pic20.jpg', '2025-11-15 08:08:16'),
(21, 5, 'Car Timing Belt', 'Timing belt for common cars', 4500.00, 20, 'uploads/pic21.jpg', '2025-11-15 08:08:16'),
(22, 5, 'Fuel Pump', 'Electric fuel pump', 5200.00, 15, 'uploads/pic22.jpg', '2025-11-15 08:08:16'),
(23, 5, 'Alternator', 'Car alternator 12V', 9500.00, 10, 'uploads/pic23.jpg', '2025-11-15 08:08:16'),
(24, 5, 'Water Pump', 'Engine water pump', 3200.00, 30, 'uploads/pic24.jpg', '2025-11-15 08:08:16'),
(25, 5, 'Air Filter', 'Car air filter', 800.00, 100, 'uploads/pic25.jpg', '2025-11-15 08:08:16'),
(26, 6, 'Car Battery 12V', 'Lead-acid car battery', 15000.00, 12, 'uploads/pic26.jpg', '2025-11-15 08:08:16'),
(27, 6, 'Headlight Bulb H4', 'H4 halogen bulb', 1200.00, 100, 'uploads/pic27.jpg', '2025-11-15 08:08:16'),
(28, 6, 'ABS Sensor', 'Wheel speed sensor', 2200.00, 40, 'uploads/pic28.jpg', '2025-11-15 08:08:16'),
(29, 6, 'Starter Motor', 'Starter motor assembly', 8500.00, 8, 'uploads/pic29.jpg', '2025-11-15 08:08:16'),
(30, 6, 'Wiring Harness', 'Partial wiring harness', 4500.00, 10, 'uploads/pic30.jpg', '2025-11-15 08:08:16'),
(31, 12, 'Rear Spoiler', 'Roof rear spoiler', 4200.00, 9, 'uploads/pic31.jpg', '2025-11-15 08:08:16'),
(32, 5, 'Rear Spoiler', 'nice', 2000.00, 4, 'uploads/part_1763195411_f4c37e9d.jpg', '2025-11-15 08:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(2, 'Car'),
(1, 'Motorbike'),
(3, 'SUV');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `order_id`, `comment`, `rating`, `created_at`, `is_read`) VALUES
(1, 2, 1, 'nice', 5, '2025-11-15 11:30:46', 1),
(2, 2, 3, 'kjhgf', 2, '2025-11-15 11:43:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Accepted','Rejected','Packed','Shipped','Out for delivery','Delivered') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `full_name`, `phone`, `address`, `total_amount`, `status`, `created_at`) VALUES
(1, 2, 'ali', '12345678909', 'lahore', 11600.00, 'Delivered', '2025-11-15 10:59:25'),
(3, 2, 'ahmad ali', '12345678909', ';lkjhg', 9000.00, 'Delivered', '2025-11-15 11:41:44'),
(4, 2, 'ahmad ali', '12345678909', 'gtfde', 6200.00, 'Delivered', '2025-11-15 11:44:15'),
(5, 2, 'ahmad ali', '12345678909', 'ertgh', 4200.00, 'Delivered', '2025-11-15 11:44:38');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `part_id`, `quantity`, `price`) VALUES
(1, 1, 24, 1, 3200.00),
(2, 1, 31, 2, 4200.00),
(4, 3, 30, 2, 4500.00),
(5, 4, 31, 1, 4200.00),
(6, 4, 32, 1, 2000.00),
(7, 5, 31, 1, 4200.00);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`subcategory_id`, `category_id`, `subcategory_name`) VALUES
(1, 1, 'Engine Parts'),
(2, 1, 'Electric Parts'),
(3, 1, 'Accessories'),
(4, 1, 'Body Parts'),
(5, 2, 'Engine Parts'),
(6, 2, 'Electric Parts'),
(7, 2, 'Accessories'),
(8, 2, 'Body Parts'),
(9, 3, 'Engine Parts'),
(10, 3, 'Electric Parts'),
(11, 3, 'Accessories'),
(12, 3, 'Body Parts');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_type` enum('buyer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `user_type`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$fZYkVIp.asTiFk7geyzXcOOjHwbMR6zaRTtyqvpnHr97UXUK.5PDy', 'admin', NULL, NULL, 'admin', '2025-11-14 00:20:54'),
(2, 'ali123', 'ali@gmail.com', '$2y$10$L7jtGOBF.Oxh5iQCg4wIKuUqsiku4ugCKhruVDYY9ewdaoEfgg6KS', 'ali ahmad', '12345678909', 'karachi', 'buyer', '2025-11-14 00:37:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auto_parts`
--
ALTER TABLE `auto_parts`
  ADD PRIMARY KEY (`part_id`),
  ADD KEY `fk_auto_parts_subcategory` (`subcategory_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auto_parts`
--
ALTER TABLE `auto_parts`
  MODIFY `part_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auto_parts`
--
ALTER TABLE `auto_parts`
  ADD CONSTRAINT `fk_auto_parts_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`subcategory_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `auto_parts` (`part_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `auto_parts` (`part_id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
