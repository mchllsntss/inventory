-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2026 at 04:32 PM
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
-- Database: `lta_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_name` varchar(255) NOT NULL,
  `grade_level` enum('Kinder','Elementary','High School','Others') NOT NULL,
  `subject` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_limit` int(11) NOT NULL DEFAULT 10,
  `supplier_id` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `grade_level`, `subject`, `price`, `quantity`, `low_stock_limit`, `supplier_id`, `date_added`, `updated_at`) VALUES
(1, 'Nice', 'High School', 'Science', 500.00, 0, 10, 3, '2026-02-28 07:29:04', '2026-03-04 15:12:44'),
(2, 'Science Book', 'Others', 'Science', 100.00, 2330, 10, 3, '2026-03-02 22:56:12', '2026-03-04 15:13:12'),
(3, 'filipino', 'High School', 'filipino', 200.00, 279, 10, 3, '2026-03-03 14:33:04', '2026-03-04 15:21:00'),
(4, 'try', 'High School', 'try', 899.00, 982, 1, 4, '2026-03-04 15:17:24', '2026-03-04 15:20:43'),
(5, 'ingay', 'Elementary', 'ingay', 889.00, 9890, 8, 4, '2026-03-04 15:21:20', '2026-03-04 15:21:20'),
(6, '222', 'Elementary', '222222222', 1.00, 0, 2147483647, 3, '2026-03-04 15:30:17', '2026-03-04 23:03:29'),
(7, '2', 'Elementary', '2', 2.00, 0, 102, 4, '2026-03-04 21:48:35', '2026-03-04 21:52:43'),
(8, '2', 'Elementary', '2', 2.00, 2, 102, 4, '2026-03-04 21:52:03', '2026-03-04 21:52:03'),
(9, '3', 'High School', '3', 3.00, 2, 10, 4, '2026-03-04 21:52:59', '2026-03-04 21:53:04'),
(10, '11:28', 'Elementary', '1111111111111111111', 100.00, 0, 2147483647, 4, '2026-03-04 22:17:57', '2026-03-04 23:32:04'),
(12, 's', '', 's', 2.00, 2, 10, 4, '2026-03-04 22:23:13', '2026-03-04 22:23:13'),
(13, 's', '', 's', 2.00, 2, 10, 4, '2026-03-04 22:24:57', '2026-03-04 22:24:57'),
(14, 'high', 'High School', 's', 2.00, 32, 10, 4, '2026-03-04 22:25:35', '2026-03-04 22:25:35'),
(15, 'kinder', '', 'kinder', 98.00, 98, 10, 4, '2026-03-04 22:32:26', '2026-03-04 22:32:26'),
(16, 'mica', '', 'mica', 98.00, 8, 10, 4, '2026-03-04 22:35:09', '2026-03-04 22:35:09'),
(17, 'mica', 'Kinder', 'mica', 98.00, 8, 10, 4, '2026-03-04 22:38:46', '2026-03-04 22:40:14'),
(18, 't', '', 't', 1.00, 2, 10, 4, '2026-03-04 22:39:02', '2026-03-04 22:39:02'),
(19, 't', '', 't', 1.00, 2, 10, 4, '2026-03-04 22:39:15', '2026-03-04 22:39:15'),
(20, 't', 'Kinder', 't', 1.00, 2, 10, 4, '2026-03-04 22:40:18', '2026-03-04 22:40:18'),
(21, 't', 'Kinder', 't', 1.00, 2, 10, 4, '2026-03-04 22:40:20', '2026-03-04 22:40:20'),
(22, 'joshua', 'Kinder', 'joshua', 1.00, 1, 10, 4, '2026-03-04 22:40:39', '2026-03-04 22:40:39'),
(23, 'joshua', 'Kinder', 'joshua', 1.00, 1, 10, 4, '2026-03-04 22:44:02', '2026-03-04 22:44:02'),
(26, '111111111111111111111111111111', 'Kinder', '1111111111111111111111111111111111', 99999999.99, 2147483647, 10, 4, '2026-03-04 22:44:37', '2026-03-04 22:44:37'),
(27, '111111111111111111111111111111', 'Kinder', '1111111111111111111111111111111111', 99999999.99, 2147483647, 10, 4, '2026-03-04 22:44:49', '2026-03-04 22:44:49'),
(28, '111111111111111111111111111111', 'Kinder', '1111111111111111111111111111111111', 99999999.99, 2147483647, 10, 4, '2026-03-04 22:51:14', '2026-03-04 22:51:14'),
(29, 'jahdkhaksjkh', 'Kinder', 'kjasdhkajhd', 89790787.00, 8977, 10, 4, '2026-03-04 23:04:27', '2026-03-04 23:04:27'),
(30, 'pinalitan', 'Kinder', 'pinalitan', 1231.00, 735, 10, 4, '2026-03-04 23:04:41', '2026-03-04 23:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `book_sales_history`
--

CREATE TABLE `book_sales_history` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity_sold` int(11) NOT NULL DEFAULT 1,
  `price_at_sale` decimal(10,2) NOT NULL,
  `sold_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_sales_history`
--

INSERT INTO `book_sales_history` (`id`, `book_id`, `quantity_sold`, `price_at_sale`, `sold_at`) VALUES
(1, 1, 1, 500.00, '2026-02-28 07:29:51'),
(2, 1, 1, 500.00, '2026-03-02 22:56:17'),
(3, 3, 1, 200.00, '2026-03-03 14:33:13'),
(4, 3, 12, 200.00, '2026-03-04 15:12:38'),
(5, 1, 3, 500.00, '2026-03-04 15:12:44'),
(6, 2, 11, 100.00, '2026-03-04 15:13:12'),
(7, 4, 8, 899.00, '2026-03-04 15:17:33'),
(8, 4, 8, 899.00, '2026-03-04 15:20:43'),
(9, 3, 8, 200.00, '2026-03-04 15:21:00'),
(10, 6, 1, 1.00, '2026-03-04 15:30:22'),
(11, 6, 12, 1.00, '2026-03-04 15:41:06'),
(12, 6, 1, 1.00, '2026-03-04 15:46:15'),
(13, 6, 1, 1.00, '2026-03-04 15:48:33'),
(14, 6, 1, 1.00, '2026-03-04 15:52:32'),
(15, 6, 1, 1.00, '2026-03-04 21:48:20'),
(16, 6, 3, 1.00, '2026-03-04 21:52:32'),
(17, 7, 2, 2.00, '2026-03-04 21:52:43'),
(18, 9, 1, 3.00, '2026-03-04 21:53:04'),
(19, 6, 2147483647, 1.00, '2026-03-04 22:17:27'),
(20, 10, 2147483647, 100.00, '2026-03-04 22:20:33'),
(21, 30, 31, 1231.00, '2026-03-04 23:05:13'),
(22, 30, 31, 1231.00, '2026-03-04 23:08:58'),
(23, 30, 31, 1231.00, '2026-03-04 23:09:12'),
(24, 30, 31, 1231.00, '2026-03-04 23:09:22'),
(25, 30, 31, 1231.00, '2026-03-04 23:11:50'),
(26, 30, 31, 1231.00, '2026-03-04 23:14:57'),
(27, 30, 31, 1231.00, '2026-03-04 23:17:36'),
(28, 30, 31, 1231.00, '2026-03-04 23:20:03'),
(29, 30, 31, 1231.00, '2026-03-04 23:25:12'),
(30, 30, 31, 1231.00, '2026-03-04 23:26:52'),
(31, 30, 31, 1231.00, '2026-03-04 23:27:15'),
(32, 30, 31, 1231.00, '2026-03-04 23:27:32'),
(33, 30, 31, 1231.00, '2026-03-04 23:27:39'),
(34, 30, 31, 1231.00, '2026-03-04 23:27:51'),
(35, 30, 31, 1231.00, '2026-03-04 23:28:06'),
(36, 30, 31, 1231.00, '2026-03-04 23:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `name`) VALUES
(1, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `supplier_type` enum('books','uniforms','both') DEFAULT 'both',
  `payment_terms` varchar(100) DEFAULT NULL,
  `last_order` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `contact_person`, `email`, `phone`, `address`, `supplier_type`, `payment_terms`, `last_order`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Wanda Pate', 'Earum duis doloribus', 'vigezace@mailinator.com', '+1 (529) 954-5255', 'A reprehenderit aliq', 'both', 'Molestiae qui id qui', '1987-07-16', 'active', '2026-02-27 23:05:12', '2026-02-27 23:05:12'),
(4, 'Colleen Hoover', 'Error ad possimus s', 'nyqucevev@mailinator.com', '+1 (499) 542-3534', 'Est animi in dolore', 'books', 'Eos placeat vel num', '2020-08-12', 'active', '2026-02-27 23:22:05', '2026-02-27 23:22:05'),
(5, 'Connor Herring', 'Possimus neque saep', 'zegewivyg@mailinator.com', '+1 (439) 985-8459', 'Sapiente velit id r', 'uniforms', 'Exercitation est eni', '1975-09-11', 'active', '2026-02-27 23:29:19', '2026-02-27 23:29:30'),
(6, 'sda', 'adsda', 'sda@gmail.com', 'dsad', 'dsada', 'uniforms', 'Net 30', '2026-02-09', 'active', '2026-03-02 14:57:52', '2026-03-02 14:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `uniform`
--

CREATE TABLE `uniform` (
  `uniform_id` int(11) NOT NULL,
  `uniform_name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL CHECK (`category` in ('Male','Female')),
  `size` varchar(50) NOT NULL,
  `color` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `small_qty` int(11) DEFAULT 0,
  `medium_qty` int(11) DEFAULT 0,
  `large_qty` int(11) DEFAULT 0,
  `small_price` decimal(10,2) DEFAULT 0.00,
  `medium_price` decimal(10,2) DEFAULT 0.00,
  `large_price` decimal(10,2) DEFAULT 0.00,
  `low_stock_limit` int(11) NOT NULL DEFAULT 10,
  `supplier_id` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uniform`
--

INSERT INTO `uniform` (`uniform_id`, `uniform_name`, `category`, `size`, `color`, `price`, `quantity`, `small_qty`, `medium_qty`, `large_qty`, `small_price`, `medium_price`, `large_price`, `low_stock_limit`, `supplier_id`, `date_added`) VALUES
(1, 'Boys Polo and Pants', 'Male', 'Small', 'White', 450.00, 46, 0, 0, 0, 0.00, 0.00, 0.00, 10, 3, '2026-02-28 07:05:43'),
(2, 'Quinn Sandoval', 'Female', 'Corrupti aperiam es', 'Sed assumenda alias', 999.00, 270, 0, 0, 0, 0.00, 0.00, 0.00, 7, 3, '2026-02-28 07:23:59'),
(3, 'PE UNIFORM', 'Female', 'small', 'white', 3323.00, 76, 0, 0, 0, 0.00, 0.00, 0.00, 1, 5, '2026-03-02 22:57:00'),
(4, 'PE UNIFORM', 'Female', 'small', 'white', 100.00, 899, 0, 0, 0, 0.00, 0.00, 0.00, 10, 6, '2026-03-03 14:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `uniform_sales_history`
--

CREATE TABLE `uniform_sales_history` (
  `id` int(11) NOT NULL,
  `uniform_id` int(11) NOT NULL,
  `quantity_sold` int(11) NOT NULL DEFAULT 1,
  `price_at_sale` decimal(10,2) NOT NULL,
  `sold_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uniform_sales_history`
--

INSERT INTO `uniform_sales_history` (`id`, `uniform_id`, `quantity_sold`, `price_at_sale`, `sold_at`) VALUES
(1, 1, 1, 450.00, '2026-02-28 07:12:13'),
(2, 1, 1, 450.00, '2026-02-28 07:12:28'),
(3, 3, 1, 3323.00, '2026-03-02 22:57:06'),
(4, 4, 1, 100.00, '2026-03-03 14:34:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `profile_id`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2a$12$HYr7.QYQo2NW9OxMHol6au6Sk6erJIJtajVUT7LDq9rMO3pH16vgm', 1, NULL, NULL, '2026-02-13 15:50:42', '2026-02-13 15:50:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `book_sales_history`
--
ALTER TABLE `book_sales_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_supplier_name` (`supplier_name`),
  ADD KEY `idx_supplier_type` (`supplier_type`);

--
-- Indexes for table `uniform`
--
ALTER TABLE `uniform`
  ADD PRIMARY KEY (`uniform_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `uniform_sales_history`
--
ALTER TABLE `uniform_sales_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_profile_id` (`profile_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `book_sales_history`
--
ALTER TABLE `book_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `uniform`
--
ALTER TABLE `uniform`
  MODIFY `uniform_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `uniform_sales_history`
--
ALTER TABLE `uniform_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `book_sales_history`
--
ALTER TABLE `book_sales_history`
  ADD CONSTRAINT `book_sales_history_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
