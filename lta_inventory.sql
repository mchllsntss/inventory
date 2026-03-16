-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 06:27 PM
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
  `activity_log` text DEFAULT NULL,
  `low_stock_limit` int(11) NOT NULL DEFAULT 10,
  `supplier_id` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_name`, `grade_level`, `subject`, `price`, `quantity`, `activity_log`, `low_stock_limit`, `supplier_id`, `date_added`, `updated_at`) VALUES
(1, 'Nice', 'High School', 'Science', 500.00, 5, NULL, 10, 3, '2026-02-28 07:29:04', '2026-03-06 04:31:06'),
(2, 'Science Book', 'Others', 'Science', 100.00, 50, NULL, 10, 3, '2026-03-02 22:56:12', '2026-03-06 04:30:30'),
(3, 'filipino', 'High School', 'filipino', 200.00, 20, '2026-03-16 05:38 PM: Damaged 10 pcs\n2026-03-16 05:38 PM: Returned 10 pcs\n2026-03-16 05:47 PM: Damaged 10 pcs\n2026-03-16 05:47 PM: Damaged 10 pcs', 10, 3, '2026-03-03 14:33:04', '2026-03-17 00:59:02'),
(30, 'Wikang Pambata', 'Kinder', 'Filipino', 250.00, 60, NULL, 10, 4, '2026-03-04 23:04:41', '2026-03-06 04:30:57'),
(31, 'Kasaysayan ng Pilipinas', 'Elementary', 'Araling Panlipunan', 500.00, 9, NULL, 10, 4, '2026-03-06 04:33:17', '2026-03-09 00:09:50');

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
(38, 26, 1, 12.00, '2026-03-05 23:00:03'),
(39, 31, 1, 500.00, '2026-03-09 00:09:50'),
(40, 3, -10, 200.00, '2026-03-17 00:38:19'),
(41, 3, -10, 200.00, '2026-03-17 00:38:28'),
(42, 3, -10, 200.00, '2026-03-17 00:47:24'),
(43, 3, -10, 200.00, '2026-03-17 00:47:42');

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
(4, 'Colleen Hoover', 'Error ad possimus s', 'nyqucevev@mailinator.com', '+1 (499) 542-3534', 'Est animi in dolore', 'books', 'Eos placeat vel num', '2020-08-12', 'inactive', '2026-02-27 23:22:05', '2026-03-16 17:13:56'),
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
  `gender` varchar(20) DEFAULT NULL,
  `school_level` varchar(50) DEFAULT NULL,
  `item_type` varchar(100) DEFAULT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `activity_log` text DEFAULT NULL,
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

INSERT INTO `uniform` (`uniform_id`, `uniform_name`, `category`, `gender`, `school_level`, `item_type`, `size`, `color`, `price`, `quantity`, `activity_log`, `small_qty`, `medium_qty`, `large_qty`, `small_price`, `medium_price`, `large_price`, `low_stock_limit`, `supplier_id`, `date_added`) VALUES
(10, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 20 Length 18', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(11, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 18 Length 21', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(12, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 22 Length 18', 'Default', 310.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(13, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 24 Length 20', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(14, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 26 Length 22', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(15, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 28 Length 20', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(16, 'Skirt', 'female', 'female', 'Elementary', 'Skirt', 'Waist 30 Length 22', 'Default', 305.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(17, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 26 Length 22', 'Default', 315.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(18, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 28 Length 24', 'Default', 315.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(19, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 30 Length 26', 'Default', 335.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(20, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 32 Length 28', 'Default', 335.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(21, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 34 Length 30', 'Default', 335.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(22, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 36 Length 32', 'Default', 345.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(23, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 38 Length 34', 'Default', 345.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(24, 'Skirt', 'female', 'female', 'High School', 'Skirt', 'Waist 40 Length 36', 'Default', 345.00, 202, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(25, 'Blouse', 'female', 'female', 'Elementary', 'Blouse', 'XS', 'Default', 250.00, 20, '2026-03-16 15:55: Returned 1 pcs - maliit papalitan ng medium\n2026-03-16 15:55: Returned 4 pcs - pinalitan dahil hindi kasya\n2026-03-16 15:55: Damaged 5 pcs - may punit\n2026-03-16 15:56: Returned 5 pcs - refund\n2026-03-16 15:56: Returned 1 pcs - diko alam\n2026-03-16 16:03: Returned 1 pcs - diko alam\n2026-03-16 16:03: Damaged 7 pcs\n2026-03-16 16:06: Damaged 7 pcs\n2026-03-16 05:41 PM: Returned 7 pcs', NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, 6, '2026-03-06 01:02:43'),
(26, 'Blouse', 'female', 'female', 'Elementary', 'Blouse', 'Small', 'Default', 275.00, 12, '2026-03-16 17:57: Damaged 19 pcs', NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(27, 'Blouse', 'female', 'female', 'Elementary', 'Blouse', 'Medium', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(28, 'Blouse', 'female', 'female', 'Elementary', 'Blouse', 'Large', 'Default', 295.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(29, 'Blouse', 'female', 'female', 'High School', 'Blouse', 'XS', 'Default', 295.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(30, 'Blouse', 'female', 'female', 'High School', 'Blouse', 'Small', 'Default', 295.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(31, 'Blouse', 'female', 'female', 'High School', 'Blouse', 'Medium', 'Default', 305.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(32, 'Blouse', 'female', 'female', 'High School', 'Blouse', 'Large', 'Default', 315.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(33, 'Blouse', 'female', 'female', 'High School', 'Blouse', 'XL', 'Default', 325.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(34, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'XS-10', 'Default', 340.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(35, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'XS', 'Default', 340.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(36, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'Small', 'Default', 365.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(37, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'Medium', 'Default', 365.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(38, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'Large', 'Default', 380.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(39, 'Dress', 'female', 'female', 'Pre-school', 'Dress', 'XL', 'Default', 380.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(40, 'Polo', 'male', 'male', 'Pre-school', 'Polo', 'XS', 'Default', 280.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(41, 'Polo', 'male', 'male', 'Pre-school', 'Polo', 'Small', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(42, 'Polo', 'male', 'male', 'Pre-school', 'Polo', 'Medium', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(43, 'Polo', 'male', 'male', 'Pre-school', 'Polo', 'Large', 'Default', 285.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(44, 'Polo', 'male', 'male', 'Pre-school', 'Polo', 'XL', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(45, 'Shorts', 'male', 'male', 'Pre-school', 'Shorts', 'XS', 'Default', 255.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(46, 'Shorts', 'male', 'male', 'Pre-school', 'Shorts', 'Small', 'Default', 255.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(47, 'Shorts', 'male', 'male', 'Pre-school', 'Shorts', 'Medium', 'Default', 255.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(48, 'Shorts', 'male', 'male', 'Pre-school', 'Shorts', 'Large', 'Default', 280.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(49, 'Shorts', 'male', 'male', 'Pre-school', 'Shorts', 'XL', 'Default', 280.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(50, 'Polo without Lining', 'male', 'male', 'Grade I-III', 'Polo', 'XS', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(51, 'Polo without Lining', 'male', 'male', 'Grade I-III', 'Polo', 'Small', 'Default', 300.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(52, 'Polo without Lining', 'male', 'male', 'Grade I-III', 'Polo', 'Medium', 'Default', 305.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(53, 'Polo without Lining', 'male', 'male', 'Grade I-III', 'Polo', 'Large', 'Default', 310.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(54, 'Polo without Lining', 'male', 'male', 'Grade I-III', 'Polo', 'XL', 'Default', 340.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(55, 'Elementary Shorts', 'male', 'male', 'Grade I-III', 'Shorts', 'XS', 'Default', 270.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(56, 'Elementary Shorts', 'male', 'male', 'Grade I-III', 'Shorts', 'Small', 'Default', 275.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(57, 'Elementary Shorts', 'male', 'male', 'Grade I-III', 'Shorts', 'Medium', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(58, 'Elementary Shorts', 'male', 'male', 'Grade I-III', 'Shorts', 'Large', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(59, 'Elementary Shorts', 'male', 'male', 'Grade I-III', 'Shorts', 'XL', 'Default', 290.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(60, 'Polo with Lining', 'male', 'male', 'Elementary', 'Polo', 'XS', 'Default', 310.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(61, 'Polo with Lining', 'male', 'male', 'Elementary', 'Polo', 'Small', 'Default', 320.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(62, 'Polo with Lining', 'male', 'male', 'Elementary', 'Polo', 'Medium', 'Default', 320.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(63, 'Polo with Lining', 'male', 'male', 'Elementary', 'Polo', 'Large', 'Default', 325.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(64, 'Polo with Lining', 'male', 'male', 'High School', 'Polo', 'Small', 'Default', 320.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(65, 'Polo with Lining', 'male', 'male', 'High School', 'Polo', 'Medium', 'Default', 320.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(66, 'Polo with Lining', 'male', 'male', 'High School', 'Polo', 'Large', 'Default', 325.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(67, 'Polo with Lining', 'male', 'male', 'High School', 'Polo', 'XL', 'Default', 335.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(68, 'PE Shorts', 'female', 'female', 'All Levels', 'PE Uniform', 'One Size', 'Default', 80.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(69, 'Necktie', 'male', 'male', 'All Levels', 'Accessory', 'One Size', 'Default', 80.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(70, 'Necktie', 'female', 'female', 'All Levels', 'Accessory', 'One Size', 'Default', 80.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(71, 'Patches', 'male', 'male', 'All Levels', 'Accessory', 'One Size', 'Default', 80.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(72, 'Patches', 'female', 'female', 'All Levels', 'Accessory', 'One Size', 'Default', 80.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(73, 'Jogging Pants', 'male', 'male', 'Pre-school', 'Jogging Pants', 'Pre-school & S (L24)', 'Default', 355.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(74, 'Jogging Pants', 'female', 'female', 'Pre-school', 'Jogging Pants', 'Pre-school & S (L24)', 'Default', 355.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(75, 'Jogging Pants', 'male', 'male', 'Elementary', 'Jogging Pants', 'Elementary S (L26)', 'Default', 365.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(76, 'Jogging Pants', 'female', 'female', 'Elementary', 'Jogging Pants', 'Elementary S (L26)', 'Default', 365.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(77, 'Jogging Pants', 'male', 'male', 'Elementary', 'Jogging Pants', 'M (L28)', 'Default', 385.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(78, 'Jogging Pants', 'female', 'female', 'Elementary', 'Jogging Pants', 'M (L28)', 'Default', 385.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(79, 'Jogging Pants', 'male', 'male', 'Elementary', 'Jogging Pants', 'M (L30)', 'Default', 385.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(80, 'Jogging Pants', 'female', 'female', 'Elementary', 'Jogging Pants', 'M (L30)', 'Default', 385.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(81, 'Jogging Pants', 'male', 'male', 'High School', 'Jogging Pants', 'Large (L32)', 'Default', 405.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(82, 'Jogging Pants', 'female', 'female', 'High School', 'Jogging Pants', 'Large (L32)', 'Default', 405.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(83, 'Jogging Pants', 'male', 'male', 'High School', 'Jogging Pants', 'Large (L34)', 'Default', 405.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(84, 'Jogging Pants', 'female', 'female', 'High School', 'Jogging Pants', 'Large (L34)', 'Default', 405.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(85, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'XS', 'Default', 225.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(86, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'XS', 'Default', 225.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(87, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'Small', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(88, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'Small', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(89, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'Medium', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(90, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'Medium', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(91, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'Large', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(92, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'Large', 'Default', 245.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(93, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'XL', 'Default', 265.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(94, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'XL', 'Default', 265.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(95, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'XXL', 'Default', 275.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(96, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'XXL', 'Default', 275.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(97, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', 'XXXL', 'Default', 295.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(98, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', 'XXXL', 'Default', 295.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(99, 'Pre-shirt', 'male', 'male', 'Intermediate', 'Shirt', '4XL', 'Default', 325.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(100, 'Pre-shirt', 'female', 'female', 'Intermediate', 'Shirt', '4XL', 'Default', 325.00, 20, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 10, NULL, '2026-03-06 01:02:43'),
(101, 'Shirt', 'male', 'male', 'Pre-school', 'Shirt', 'Small', 'Default', 500.00, 50, NULL, 0, 0, 0, 0.00, 0.00, 0.00, 10, 5, '2026-03-06 04:34:17');

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
(7, 10, 2, 290.00, '2026-03-06 01:35:55'),
(8, 25, 2, 250.00, '2026-03-06 01:36:34'),
(9, 25, 5, 250.00, '2026-03-06 01:46:51'),
(10, 25, 10, 250.00, '2026-03-16 22:06:52'),
(11, 25, -1, 250.00, '2026-03-16 23:03:26'),
(12, 25, -7, 250.00, '2026-03-16 23:03:36'),
(13, 25, -7, 250.00, '2026-03-16 23:06:15'),
(14, 25, -7, 250.00, '2026-03-17 00:41:25'),
(15, 26, -19, 275.00, '2026-03-17 00:57:16');

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
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `book_sales_history`
--
ALTER TABLE `book_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
  MODIFY `uniform_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `uniform_sales_history`
--
ALTER TABLE `uniform_sales_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
