-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 11:47 AM
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
  `grade_level` enum('Elementary','High School','Others') NOT NULL,
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
(1, 'Nice', 'High School', 'Science', 500.00, 4, 10, 3, '2026-02-28 07:29:04', '2026-02-28 07:29:51');

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
(1, 1, 1, 500.00, '2026-02-28 07:29:51');

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
(5, 'Connor Herring', 'Possimus neque saep', 'zegewivyg@mailinator.com', '+1 (439) 985-8459', 'Sapiente velit id r', 'uniforms', 'Exercitation est eni', '1975-09-11', 'active', '2026-02-27 23:29:19', '2026-02-27 23:29:30');

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
(2, 'Quinn Sandoval', 'Female', 'Corrupti aperiam es', 'Sed assumenda alias', 999.00, 270, 0, 0, 0, 0.00, 0.00, 0.00, 7, 3, '2026-02-28 07:23:59');

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
(2, 1, 1, 450.00, '2026-02-28 07:12:28');

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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `book_sales_history`
--
ALTER TABLE `book_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `uniform`
--
ALTER TABLE `uniform`
  MODIFY `uniform_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uniform_sales_history`
--
ALTER TABLE `uniform_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
