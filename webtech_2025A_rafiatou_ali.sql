-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2025 at 05:06 PM
-- Server version: 8.0.44-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webtech_2025A_rafiatou_ali`
--

-- --------------------------------------------------------






--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `parent_id`, `created_at`) VALUES
(1, 'Clothing', 'Fashion clothing', NULL, '2025-11-25 00:55:30'),
(2, 'Shoes', 'Footwear', NULL, '2025-11-25 00:55:30'),
(3, 'Kitchen', 'Kitchen utensils', NULL, '2025-11-25 00:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `hex_code` varchar(7) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `login_ajax`
--

CREATE TABLE `login_ajax` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `low_stock_alerts`
--

CREATE TABLE `low_stock_alerts` (
  `id` int NOT NULL,
  `variant_id` int NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `current_stock` int NOT NULL,
  `threshold` int DEFAULT '5',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `address_id` int NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payment_method` enum('stripe','cod') COLLATE utf8mb4_general_ci DEFAULT 'stripe',
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `status`, `notes`, `created_at`, `updated_at`, `payment_method`, `payment_status`) VALUES
(1, 1, 1, 'pending', NULL, '2025-11-25 14:06:55', '2025-11-25 14:06:55', 'stripe', 'pending'),
(2, 1, 2, 'pending', NULL, '2025-11-25 14:08:19', '2025-11-25 14:08:19', 'stripe', 'pending'),
(3, 1, 3, 'delivered', NULL, '2025-11-25 14:13:24', '2025-11-25 15:09:24', 'stripe', 'pending'),
(4, 1, 4, 'pending', NULL, '2025-11-25 14:17:32', '2025-11-25 14:17:32', 'stripe', 'pending'),
(5, 1, 5, 'confirmed', NULL, '2025-11-25 14:35:03', '2025-11-25 15:07:55', 'stripe', 'pending'),
(6, 4, 6, 'confirmed', NULL, '2025-11-25 22:56:27', '2025-11-25 22:57:08', 'stripe', 'pending'),
(7, 5, 7, 'delivered', NULL, '2025-11-26 23:53:23', '2025-11-26 23:54:40', 'stripe', 'pending'),
(8, 5, 8, 'pending', NULL, '2025-11-27 00:31:40', '2025-11-27 00:31:40', 'stripe', 'pending'),
(9, 6, 9, 'pending', NULL, '2025-11-27 01:05:40', '2025-11-27 01:05:40', 'stripe', 'pending'),
(10, 7, 10, 'pending', NULL, '2025-11-27 22:45:17', '2025-11-27 22:45:17', 'stripe', 'pending'),
(11, 7, 11, 'pending', NULL, '2025-11-27 22:48:19', '2025-11-27 22:48:19', 'stripe', 'pending'),
(12, 7, 12, 'pending', NULL, '2025-11-27 22:51:23', '2025-11-27 22:51:23', 'stripe', 'pending'),
(13, 7, 13, 'pending', NULL, '2025-11-27 22:51:28', '2025-11-27 22:51:28', 'stripe', 'pending'),
(14, 7, 14, 'pending', NULL, '2025-11-27 22:51:32', '2025-11-27 22:51:32', 'stripe', 'pending'),
(15, 7, 15, 'pending', NULL, '2025-11-27 22:53:43', '2025-11-27 22:53:43', 'stripe', 'pending'),
(16, 7, 16, 'pending', NULL, '2025-11-27 22:55:24', '2025-11-27 22:55:24', 'stripe', 'pending'),
(17, 7, 17, 'pending', NULL, '2025-11-27 22:55:31', '2025-11-27 22:55:31', 'stripe', 'pending'),
(18, 7, 18, 'pending', NULL, '2025-11-27 22:59:37', '2025-11-27 22:59:37', 'stripe', 'pending'),
(19, 7, 19, 'pending', NULL, '2025-11-27 23:08:28', '2025-11-27 23:08:28', 'stripe', 'pending'),
(20, 7, 20, 'pending', NULL, '2025-11-27 23:11:39', '2025-11-27 23:11:39', 'stripe', 'pending'),
(21, 7, 21, 'pending', NULL, '2025-11-27 23:13:37', '2025-11-27 23:13:37', 'stripe', 'pending'),
(22, 7, 22, 'pending', NULL, '2025-11-27 23:17:18', '2025-11-27 23:17:18', 'stripe', 'pending'),
(23, 7, 23, 'pending', NULL, '2025-11-27 23:38:26', '2025-11-27 23:38:26', 'stripe', 'pending'),
(24, 7, 24, 'confirmed', NULL, '2025-11-27 23:39:01', '2025-12-08 16:47:41', 'stripe', 'pending'),
(25, 7, 25, 'confirmed', NULL, '2025-11-27 23:48:38', '2025-11-27 23:54:34', 'stripe', 'pending'),
(26, 7, 26, 'confirmed', NULL, '2025-11-28 00:07:58', '2025-11-28 00:07:58', 'stripe', 'pending'),
(27, 1, 27, 'shipped', NULL, '2025-11-28 00:09:58', '2025-12-08 18:37:57', 'stripe', 'pending'),
(28, 1, 28, 'confirmed', NULL, '2025-11-29 10:12:48', '2025-11-29 10:12:48', 'stripe', 'pending'),
(29, 1, 29, 'confirmed', NULL, '2025-11-29 10:13:56', '2025-12-08 16:47:31', 'stripe', 'pending'),
(30, 1, 30, 'confirmed', NULL, '2025-11-29 10:20:10', '2025-12-08 16:46:43', 'stripe', 'pending'),
(31, 8, 31, 'confirmed', NULL, '2025-11-30 00:25:19', '2025-11-30 00:25:19', 'stripe', 'pending'),
(32, 1, 32, 'confirmed', NULL, '2025-11-30 21:30:52', '2025-11-30 21:30:52', 'stripe', 'pending'),
(33, 1, 33, 'delivered', NULL, '2025-11-30 21:31:20', '2025-12-08 18:37:41', 'stripe', 'pending'),
(34, 1, 34, 'shipped', NULL, '2025-12-06 12:32:44', '2025-12-08 18:38:14', 'stripe', 'pending'),
(35, 1, 35, 'confirmed', NULL, '2025-12-08 18:40:10', '2025-12-08 18:41:16', 'stripe', 'pending'),
(36, 1, 36, 'confirmed', NULL, '2025-12-08 20:21:26', '2025-12-08 20:21:26', 'stripe', 'pending'),
(37, 1, 37, 'confirmed', NULL, '2025-12-08 22:31:06', '2025-12-08 22:31:06', 'stripe', 'pending'),
(38, 1, 38, 'confirmed', NULL, '2025-12-09 00:39:11', '2025-12-09 00:39:11', 'stripe', 'pending'),
(39, 1, 39, 'delivered', NULL, '2025-12-09 00:45:13', '2025-12-09 12:30:49', 'stripe', 'pending'),
(40, 1, 40, 'shipped', NULL, '2025-12-09 12:17:08', '2025-12-09 12:30:36', 'stripe', 'pending'),
(41, 1, 41, 'shipped', NULL, '2025-12-09 12:32:38', '2025-12-10 09:11:31', 'stripe', 'pending'),
(42, 1, 42, 'pending', NULL, '2025-12-10 09:54:52', '2025-12-10 09:54:52', 'stripe', 'pending'),
(43, 1, 43, 'pending', NULL, '2025-12-10 10:03:35', '2025-12-10 10:03:35', '', 'pending'),
(44, 1, 44, 'pending', NULL, '2025-12-10 10:04:27', '2025-12-10 10:04:27', 'stripe', 'pending'),
(45, 1, 45, 'pending', NULL, '2025-12-11 03:15:33', '2025-12-11 03:15:33', '', 'pending'),
(46, 1, 46, 'pending', NULL, '2025-12-11 03:37:54', '2025-12-11 03:37:54', '', 'pending'),
(47, 1, 47, 'shipped', NULL, '2025-12-11 03:54:32', '2025-12-11 14:02:35', 'stripe', 'pending'),
(48, 1, 48, 'delivered', NULL, '2025-12-11 06:25:00', '2025-12-11 06:35:25', '', 'pending'),
(49, 1, 49, 'confirmed', NULL, '2025-12-11 14:09:52', '2025-12-11 14:10:15', '', 'pending'),
(50, 1, 50, 'confirmed', NULL, '2025-12-11 15:21:38', '2025-12-11 18:30:27', '', 'pending'),
(51, 1, 51, 'confirmed', NULL, '2025-12-11 18:17:22', '2025-12-11 18:30:17', '', 'pending'),
(52, 8, 52, 'confirmed', NULL, '2025-12-11 18:48:29', '2025-12-11 18:50:24', '', 'pending'),
(53, 1, 57, 'confirmed', NULL, '2025-12-12 14:01:52', '2025-12-12 22:36:01', 'stripe', 'pending'),
(54, 1, 62, 'confirmed', NULL, '2025-12-12 14:58:03', '2025-12-12 14:58:21', 'stripe', 'pending'),
(55, 8, 63, 'pending', NULL, '2025-12-12 15:00:35', '2025-12-12 15:00:36', 'stripe', 'pending'),
(56, 1, 64, 'cancelled', NULL, '2025-12-12 22:39:47', '2025-12-14 00:07:49', 'stripe', 'pending'),
(57, 9, 65, 'confirmed', NULL, '2025-12-13 00:20:11', '2025-12-13 23:29:44', 'stripe', 'pending'),
(58, 11, 66, 'confirmed', NULL, '2025-12-13 01:24:00', '2025-12-13 01:26:43', 'stripe', 'pending'),
(59, 1, 67, 'shipped', NULL, '2025-12-13 01:31:57', '2025-12-13 01:32:18', 'stripe', 'pending'),
(60, 8, 68, 'pending', NULL, '2025-12-13 22:31:59', '2025-12-13 22:31:59', 'stripe', 'pending'),
(61, 12, 69, 'confirmed', NULL, '2025-12-13 23:58:34', '2025-12-14 00:02:43', 'stripe', 'pending'),
(62, 1, 70, 'confirmed', NULL, '2025-12-14 19:57:40', '2025-12-17 20:31:42', 'stripe', 'pending'),
(63, 15, 71, 'confirmed', NULL, '2025-12-18 10:18:18', '2025-12-18 10:20:16', 'stripe', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `custom_notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `quantity`, `unit_price`, `custom_notes`, `created_at`) VALUES
(1, 1, 1, NULL, 1, 45.00, 'Size: Small, Color: Red. ', '2025-11-25 14:06:55'),
(2, 1, 2, NULL, 1, 35.00, 'Size: Medium, Color: Red. ', '2025-11-25 14:06:55'),
(3, 1, 2, NULL, 1, 35.00, 'Size: Small, Color: Red. ', '2025-11-25 14:06:55'),
(4, 1, 1, NULL, 1, 45.00, 'Size: Medium, Color: Blue. ', '2025-11-25 14:06:55'),
(5, 2, 2, NULL, 1, 35.00, 'Size: Small, Color: Red. ', '2025-11-25 14:08:19'),
(6, 3, 1, NULL, 1, 45.00, 'Size: Small, Color: Red. ', '2025-11-25 14:13:24'),
(7, 4, 1, NULL, 1, 45.00, 'Size: Small, Color: Red. ', '2025-11-25 14:17:32'),
(8, 5, 1, NULL, 1, 45.00, 'Size: Small, Color: Red. ', '2025-11-25 14:35:03'),
(9, 6, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-25 22:56:27'),
(10, 7, 5, NULL, 5, 50.00, 'Size: Small, Color: Red. ', '2025-11-26 23:53:23'),
(11, 8, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-27 00:31:40'),
(12, 8, 2, NULL, 1, 35.00, 'Size: Small, Color: Red. ', '2025-11-27 00:31:40'),
(13, 9, 7, NULL, 1, 30.00, 'Size: Medium, Color: Red. ', '2025-11-27 01:05:40'),
(14, 10, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:45:17'),
(15, 10, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:45:17'),
(16, 11, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:48:19'),
(17, 11, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:48:19'),
(18, 12, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:51:23'),
(19, 12, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:51:23'),
(20, 13, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:51:28'),
(21, 13, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:51:28'),
(22, 14, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:51:32'),
(23, 14, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:51:32'),
(24, 15, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:53:43'),
(25, 15, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:53:43'),
(26, 16, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:55:24'),
(27, 16, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:55:24'),
(28, 17, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:55:31'),
(29, 17, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:55:31'),
(30, 18, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 22:59:37'),
(31, 18, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 22:59:37'),
(32, 19, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 23:08:28'),
(33, 19, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:08:28'),
(34, 20, 7, NULL, 1, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 23:11:39'),
(35, 20, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:11:39'),
(36, 21, 7, NULL, 7, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 23:13:37'),
(37, 21, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:13:37'),
(38, 22, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:17:18'),
(39, 23, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:38:26'),
(40, 24, 7, NULL, 7, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 23:39:01'),
(41, 24, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:39:01'),
(42, 25, 7, NULL, 7, 30.00, 'Size: Medium, Color: Blue. ', '2025-11-27 23:48:38'),
(43, 25, 6, NULL, 1, 60.00, 'Size: Medium, Color: Red. ', '2025-11-27 23:48:38'),
(44, 26, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-28 00:07:58'),
(45, 27, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-28 00:09:58'),
(46, 28, 7, NULL, 1, 30.00, 'Size: Small, Color: Red. ', '2025-11-29 10:12:48'),
(47, 29, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-29 10:13:56'),
(48, 30, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-29 10:20:10'),
(49, 31, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-30 00:25:19'),
(50, 32, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-30 21:30:52'),
(51, 32, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-30 21:30:52'),
(52, 33, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-11-30 21:31:20'),
(53, 34, 7, NULL, 1, 30.00, 'Size: Small, Color: Red. ', '2025-12-06 12:32:44'),
(54, 35, 7, NULL, 2, 30.00, 'Size: Small, Color: Red. ', '2025-12-08 18:40:10'),
(55, 36, 7, NULL, 1, 30.00, 'Size: Small, Color: Red. ', '2025-12-08 20:21:26'),
(56, 37, 6, NULL, 1, 60.00, 'Size: Small, Color: Red. ', '2025-12-08 22:31:06'),
(57, 38, 6, NULL, 2, 60.00, 'Size: Small, Color: Red. ', '2025-12-09 00:39:11'),
(58, 38, 7, NULL, 5, 30.00, 'Size: Small, Color: Red. ', '2025-12-09 00:39:11'),
(59, 39, 7, NULL, 10, 30.00, 'Size: Small, Color: Red. ', '2025-12-09 00:45:13'),
(60, 40, 7, NULL, 1, 30.00, 'Size: Small, Color: Red. ', '2025-12-09 12:17:08'),
(61, 41, 1, NULL, 8, 45.00, 'Size: Small, Color: Red. ', '2025-12-09 12:32:38'),
(62, 42, 7, NULL, 1, 30.00, 'Size: Small, Color: Red', '2025-12-10 09:54:52'),
(63, 42, 6, NULL, 2, 60.00, 'Size: Small, Color: Red', '2025-12-10 09:54:52'),
(64, 43, 7, NULL, 1, 30.00, 'Size: Small, Color: Red', '2025-12-10 10:03:35'),
(65, 43, 6, NULL, 2, 60.00, 'Size: Small, Color: Red', '2025-12-10 10:03:35'),
(66, 44, 6, NULL, 1, 60.00, 'Size: Small, Color: Blue', '2025-12-10 10:04:27'),
(67, 45, 6, NULL, 3, 60.00, 'Size: Small, Color: Red', '2025-12-11 03:15:33'),
(68, 46, 5, NULL, 1, 50.00, '', '2025-12-11 03:37:54'),
(69, 46, 7, NULL, 3, 30.00, '', '2025-12-11 03:37:54'),
(70, 47, 6, NULL, 1, 60.00, '', '2025-12-11 03:54:32'),
(71, 48, 6, NULL, 5, 60.00, '', '2025-12-11 06:25:00'),
(72, 49, 2, NULL, 3, 35.00, '', '2025-12-11 14:09:52'),
(73, 50, 6, NULL, 4, 60.00, 'Size: Small, Color: Red', '2025-12-11 15:21:38'),
(74, 51, 7, NULL, 3, 30.00, 'Size: Medium, Color: Red', '2025-12-11 18:17:22'),
(75, 52, 1, NULL, 3, 45.00, 'Size: Small, Color: Red', '2025-12-11 18:48:29'),
(76, 53, 7, NULL, 1, 30.00, '', '2025-12-12 14:01:52'),
(77, 54, 7, NULL, 1, 30.00, '', '2025-12-12 14:58:03'),
(78, 55, 3, NULL, 7, 25.00, '', '2025-12-12 15:00:35'),
(79, 56, 13, NULL, 1, 8000.00, '', '2025-12-12 22:39:47'),
(80, 57, 10, NULL, 2, 8000.00, '', '2025-12-13 00:20:11'),
(81, 58, 12, NULL, 1, 15000.00, '', '2025-12-13 01:24:00'),
(82, 59, 11, NULL, 1, 30000.00, '', '2025-12-13 01:31:57'),
(83, 60, 15, NULL, 1, 23000.00, '', '2025-12-13 22:31:59'),
(84, 61, 15, NULL, 1, 23000.00, '', '2025-12-13 23:58:34'),
(85, 62, 13, NULL, 1, 8000.00, '', '2025-12-14 19:57:40'),
(86, 63, 15, NULL, 1, 23000.00, '', '2025-12-18 10:18:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `changed_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_logs`
--

INSERT INTO `order_status_logs` (`id`, `order_id`, `old_status`, `new_status`, `changed_by`, `created_at`) VALUES
(1, 43, 'pending', 'pending', NULL, '2025-12-10 10:03:35'),
(2, 45, 'pending', 'pending', NULL, '2025-12-11 03:15:33'),
(3, 46, 'pending', 'pending', NULL, '2025-12-11 03:37:54'),
(4, 48, 'pending', 'pending', NULL, '2025-12-11 06:25:00'),
(5, 49, 'pending', 'pending', NULL, '2025-12-11 14:09:52'),
(6, 50, 'pending', 'pending', NULL, '2025-12-11 15:21:38'),
(7, 51, 'pending', 'pending', NULL, '2025-12-11 18:17:22'),
(8, 52, 'pending', 'pending', NULL, '2025-12-11 18:48:29'),
(9, 54, 'pending', 'pending', NULL, '2025-12-12 14:58:03'),
(10, 55, 'pending', 'pending', NULL, '2025-12-12 15:00:36'),
(11, 56, 'pending', 'pending', NULL, '2025-12-12 22:39:47'),
(12, 57, 'pending', 'pending', NULL, '2025-12-13 00:20:11'),
(13, 58, 'pending', 'pending', NULL, '2025-12-13 01:24:00'),
(14, 59, 'pending', 'pending', NULL, '2025-12-13 01:31:57'),
(15, 60, 'pending', 'pending', NULL, '2025-12-13 22:31:59'),
(16, 61, 'pending', 'pending', NULL, '2025-12-13 23:58:34'),
(17, 62, 'pending', 'pending', NULL, '2025-12-14 19:57:40'),
(18, 63, 'pending', 'pending', NULL, '2025-12-18 10:18:18');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `category_id` int NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock_quantity` int DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category_id`, `base_price`, `featured`, `created_at`, `image_url`, `stock_quantity`) VALUES
(1, 'Traditional Boubou', 'Elegant traditional wear', 1, 8000.00, 0, '2025-11-25 00:55:30', 'uploads/products/product_1765460264_693ac928e424c.jpg', 10),
(2, 'Modern Kaftan', 'Contemporary style kaftan', 1, 20000.00, 0, '2025-11-25 00:55:30', 'uploads/products/product_1765460655_693acaaf683fa.jpg', 10),
(3, 'Kitchen Knife Set', 'High-quality kitchen knives', 3, 13000.00, 1, '2025-11-25 00:55:30', 'uploads/products/product_1765460567_693aca57865c8.webp', 3),
(5, 'dubai abaya', 'dubai abaya for girls aged from 18 to 40', 1, 25000.00, 0, '2025-11-25 22:06:12', 'uploads/products/product_1764108372_69262854a7223.jpg', 10),
(6, 'Sahari', 'Sahari pour les filles', 1, 15000.00, 0, '2025-11-25 22:14:08', 'uploads/products/product_1764108848_69262a30446b1.webp', 10),
(7, 'heel and bag', 'heel and hand bag for ladies', 2, 15000.00, 0, '2025-11-26 23:59:14', 'uploads/products/product_1764201554_69279452e57fc.jpeg', 6),
(9, 'simple abaya', 'women abaya', 1, 17000.00, 0, '2025-12-12 22:01:39', 'uploads/products/product_1765576899_693c90c3811ba.jpeg', 15),
(10, 'shoes', 'demi talon', 2, 8000.00, 0, '2025-12-12 22:33:52', 'uploads/products/product_1765578832_693c985074c66.jpeg', 12),
(11, 'Food warmer set', '3 pieces food warmer', 3, 30000.00, 0, '2025-12-12 22:35:36', 'uploads/products/product_1765578936_693c98b83ab67.jpeg', 4),
(12, 'Indonosian hijab', 'indonesian hijab high quality', 1, 15000.00, 1, '2025-12-12 22:38:17', 'uploads/products/product_1765579097_693c995931d97.jpeg', 18),
(13, 'shoes', 'shoes for women', 2, 8000.00, 1, '2025-12-12 22:39:11', 'uploads/products/product_1765579151_693c998fa8def.jpeg', -1),
(14, 'Tea pot', 'tea pot', 3, 22000.00, 0, '2025-12-12 22:41:42', 'uploads/products/product_1765579302_693c9a26598da.jpeg', 1),
(15, 'simple abaya', 'simple abaya', 1, 23000.00, 0, '2025-12-13 01:26:14', 'uploads/products/product_1765589174_693cc0b62b12a.jpeg', 5),
(16, 'shoes', 'women shoes', 2, 8000.00, 0, '2025-12-13 23:29:11', 'uploads/products/product_1765668551_693df6c7cfaa3.jpeg', 10);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `size_id` int DEFAULT NULL,
  `color_id` int DEFAULT NULL,
  `material_id` int DEFAULT NULL,
  `price_adjustment` decimal(10,2) DEFAULT '0.00',
  `stock_quantity` int DEFAULT '0',
  `reserved_quantity` int DEFAULT '0',
  `low_stock_threshold` int DEFAULT '5',
  `sku` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `movement_type` enum('in','out','adjustment','return') COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `previous_quantity` int NOT NULL,
  `new_quantity` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `variant_id`, `movement_type`, `quantity`, `previous_quantity`, `new_quantity`, `reason`, `reference_id`, `product_name`, `created_at`) VALUES
(1, 7, NULL, 'out', 1, 10, 9, 'Order reserved (COD)', 53, NULL, '2025-12-12 14:01:52'),
(2, 7, NULL, 'out', 1, 9, 8, 'Order reserved (COD)', 54, NULL, '2025-12-12 14:58:03'),
(3, 3, NULL, 'out', 7, 10, 3, 'Order reserved (COD)', 55, NULL, '2025-12-12 15:00:35'),
(4, 13, NULL, 'out', 1, 1, 0, 'Order reserved (COD)', 56, NULL, '2025-12-12 22:39:47'),
(5, 10, NULL, 'out', 2, 16, 14, 'Order reserved (COD)', 57, NULL, '2025-12-13 00:20:11'),
(6, 12, NULL, 'out', 1, 20, 19, 'Order reserved (COD)', 58, NULL, '2025-12-13 01:24:00'),
(7, 11, NULL, 'out', 1, 5, 4, 'Order reserved (COD)', 59, NULL, '2025-12-13 01:31:57'),
(8, 15, NULL, 'out', 1, 10, 9, 'Order reserved (COD)', 60, NULL, '2025-12-13 22:31:59'),
(9, 15, NULL, 'out', 1, 9, 8, 'Order reserved (COD)', 61, NULL, '2025-12-13 23:58:34'),
(10, 13, NULL, 'out', 1, 1, 0, 'Order reserved (COD)', 62, NULL, '2025-12-14 19:57:40'),
(11, 15, NULL, 'out', 1, 7, 6, 'Order reserved (COD)', 63, NULL, '2025-12-18 10:18:18');

-- --------------------------------------------------------

--
-- Table structure for table `stock_reservations`
--

CREATE TABLE `stock_reservations` (
  `id` int NOT NULL,
  `variant_id` int NOT NULL,
  `order_id` int NOT NULL,
  `quantity` int NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('customer','admin','user') COLLATE utf8mb4_general_ci DEFAULT 'customer',
  `security_question` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'What city were you born in?',
  `security_answer` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'default',
  `reset_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `created_at`, `role`, `security_question`, `security_answer`, `reset_token`, `token_expires`) VALUES
(1, 'Rafiatoumalamali@gmail.com', '$2y$10$Rc25Cuud3fgLPSle1FeObemLezw3qzevbmuDVbQvwHhRxwc6J6meK', 'Rafiatou', 'Malam Aliyu', '+233 5003836061', '2025-11-25 12:33:46', 'admin', 'What city were you born in?', 'default', NULL, NULL),
(2, 'mafi@gmail.com', '$2y$10$p3dp5yqPqMKzoJcW3vFRVu3hQstYmBphqJh17eL/ERubwiow6LiDS', 'Rafiatou', 'Malam Ali', '+233 5003836061', '2025-11-25 19:54:04', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(3, 'sani@gmail.con', '$2y$10$U3lJuNx9Ku1YaeF4Kpr1pO4DYfzHLpLeY5PLEC9ENXipBTAmqRQIC', 'Sani', 'Malam Ali', '+227 72333445', '2025-11-25 20:13:42', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(4, 'mafida@gmail.com', '$2y$10$HH8Cz1QjQyciAZjm0Pgeyujfzp/WeZXWX1ylFzaVuZWtqtnPqbaTS', 'Mafida', 'Elhadji Zoubeirou', '+227 72333466', '2025-11-25 20:22:00', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(5, 'mama@gmail.com', '$2y$10$5GPWLi/CA90nkXVrD5/RbeH4/Yciy7QAb5yVTMlkfXCCIHKFzKBjm', 'mama', 'baba', '+227 72333445', '2025-11-26 23:51:14', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(6, 'rabia@gmail.com', '$2y$10$0FdNnykdsCFoxUPjnbmx/u3N4JXfmJTyXqjKoTBAq20XiCG5/g6w2', 'Rabi', 'Malam Ali', '+233 5003836060', '2025-11-27 00:49:03', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(7, 'aliyu@gmail.com', '$2y$10$4GYEQdW9jVPw5t4Dk0.1yOWJh2ByhwbXNtRkdKrpMzYqykCFRYU5O', 'Ali', 'baba', '+233 5003836061', '2025-11-27 22:43:08', 'customer', 'What city were you born in?', 'default', NULL, NULL),
(8, 'zina@gmail.com', '$2y$10$v9ptvQxtvhksjsB3uk3EcO7ztD./JShYoRimb0Hhg6/WXIMIij9SS', 'Zina', 'malam Sani', '+233 5003836061', '2025-11-29 15:45:32', 'customer', 'What city were you born in?', '$2y$10$4pPrv.uQyem/iI9flJqhJeYORL3BGjBYzVtTSdzCT9iGPqo0n3GYi', NULL, NULL),
(9, 'aicha@gmail.com', '$2y$10$x9ssG0zP0Dh1Jteo4lYxFu2RoS8IeJTAcnKTz1VWrYMnHTwtJk.M6', 'Aicha', 'Abdou', NULL, '2025-12-13 00:17:08', 'user', 'What city were you born in?', '$2y$10$jS3vMRKyrY2CCoxWxAlqVeZS9hgDL94JlZumL8MCc9NS/7aHSYAOi', NULL, NULL),
(10, 'hadjara.ouedraogo@ashesi.edu.gh', '$2y$10$ekvm1xMw8Df2bA5NcLq7DOjm8DRkqXZrxORherVVUnmgZ/zrhI74.', 'Hadji', 'ouedraogo', NULL, '2025-12-13 00:23:50', 'user', 'What city were you born in?', '$2y$10$ZVSMt32FrnocVeNmRhqn/u5gRsAyrhRTuBav4YCxBlWHuj5fflrl.', NULL, NULL),
(11, 'attou@gmail.com', '$2y$10$tGoERnT6lOGOmZL/RWeCV.k7RDZUSaCVYkmtW8oDRWcuve.Oebh3i', 'attou', 'atou', NULL, '2025-12-13 01:17:19', 'user', 'What city were you born in?', '$2y$10$Cra.i2KEk05JKzmRtZYgnusbvYxmcmMJuA66NRkYhkY/HVqJ7/GDq', NULL, NULL),
(12, 'Laila@gmail.com', '$2y$10$7Qu82uiJKfVhRQ32vhcVNuWofEfMwpzwCkyGMcxe/.vN9Ljml64Fi', 'leila', 'malam Sani', NULL, '2025-12-13 23:50:53', 'user', 'What city were you born in?', '$2y$10$NiCfQaCiSdr4Nu3Vu8paVuYDgR1n1HhvmaKxjNn2xOHYpdtsAxcKK', NULL, NULL),
(13, 'fatmaaliabdourahmane@gmail.com', '$2y$10$N1or92Iv/BXkdyYE2yZ4FeszsfSofU1m5irUMwlf/5yazVM3dyEIi', 'Fatma', 'Ali', NULL, '2025-12-17 20:23:03', 'user', 'What city were you born in?', '$2y$10$7DSU3O3FmyV7IEgPHa0nNeXS.j2NdlNiVHxZZJQ9QUf4kivuQDqJ.', NULL, NULL),
(14, 'fatima@gmail', '$2y$10$GoextLbalUogRvg.jVIayuqLpa9ZZJtoOdelwTPugN3dQIWriq1YC', 'fati', 'Sani', NULL, '2025-12-18 10:04:54', 'user', 'What city were you born in?', '$2y$10$4cVwazU0qBy1NEiE.atkCeEcBAemfXT6r4U.g1h/RE9pFFIod/zQ6', NULL, NULL),
(15, 'nana@gmail.com', '$2y$10$y5/pGBu2l9bzoEqE2zr07O0Oms/l5.v16IKiw7LDwpEjcC8mKu996', 'nana1', 'Sani', '', '2025-12-18 10:13:27', 'user', 'What city were you born in?', '$2y$10$dqQ7N/.14LTATWsf.dQ3ueyXmBQt1x0ewtZHHyvqj.r1U/3NP5Nxi', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `address_line1`, `address_line2`, `city`, `postal_code`, `is_default`, `created_at`) VALUES
(1, 1, 'Ashesi', NULL, 'Accra', 'Avenue_1', 0, '2025-11-25 14:06:55'),
(2, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-25 14:08:19'),
(3, 1, 'Ashesi', NULL, 'Accra', 'Avenue_1', 0, '2025-11-25 14:13:24'),
(4, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-25 14:17:32'),
(5, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-25 14:35:03'),
(6, 4, 'Zinder sabon gari', NULL, 'Zinder', 'sabonGari123', 0, '2025-11-25 22:56:27'),
(7, 5, 'niger', NULL, 'Niamey', 'sabonGari123', 0, '2025-11-26 23:53:23'),
(8, 5, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 00:31:40'),
(9, 6, 'niger', NULL, 'Niamey', 'Avenue_1', 0, '2025-11-27 01:05:40'),
(10, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:45:17'),
(11, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:48:19'),
(12, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:51:23'),
(13, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:51:28'),
(14, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:51:32'),
(15, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:53:43'),
(16, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:55:24'),
(17, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:55:31'),
(18, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 22:59:37'),
(19, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 23:08:28'),
(20, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 23:11:39'),
(21, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 23:13:37'),
(22, 7, 'Niger_zinder', NULL, 'zinder', 'Zinder_cartier', 0, '2025-11-27 23:17:18'),
(23, 7, 'Niger_zinder', NULL, 'zinder', 'Zinder_cartier', 0, '2025-11-27 23:38:26'),
(24, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 23:39:01'),
(25, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-27 23:48:38'),
(26, 7, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-28 00:07:58'),
(27, 1, 'niger', NULL, 'Niamey', 'sabonGari123', 0, '2025-11-28 00:09:58'),
(28, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-29 10:12:48'),
(29, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-29 10:13:56'),
(30, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-29 10:20:10'),
(31, 8, 'niger', NULL, 'Niamey', 'Avenue_1', 0, '2025-11-30 00:25:19'),
(32, 1, 'niger', NULL, 'Niamey', 'Avenue_1', 0, '2025-11-30 21:30:52'),
(33, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-11-30 21:31:20'),
(34, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-12-06 12:32:44'),
(35, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-12-08 18:40:10'),
(36, 1, 'niger', NULL, 'Niamey', 'sabonGari123', 0, '2025-12-08 20:21:26'),
(37, 1, 'niger', NULL, 'Niamey', 'Avenue_1', 0, '2025-12-08 22:31:06'),
(38, 1, 'Nigeria', NULL, 'Kano', 'KANO_STATE1', 0, '2025-12-09 00:39:11'),
(39, 1, 'niger', NULL, 'Niamey', 'KANO_STATE1', 0, '2025-12-09 00:45:13'),
(40, 1, 'niger', NULL, 'Niamey', 'Niamey_commun_I', 0, '2025-12-09 12:17:08'),
(41, 1, 'niger', NULL, 'Niamey', 'KANO_STATE1', 0, '2025-12-09 12:32:38'),
(42, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-10 09:54:52'),
(43, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-10 10:03:35'),
(44, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-10 10:04:27'),
(45, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 03:15:33'),
(46, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 03:37:54'),
(47, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 03:54:32'),
(48, 1, 'niger', '', 'Niamey', 'KANO_STATE1', 1, '2025-12-11 06:25:00'),
(49, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 14:09:52'),
(50, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 15:21:38'),
(51, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-11 18:17:22'),
(52, 8, 'niger', '', 'Niamey', 'Avenue_1', 1, '2025-12-11 18:48:29'),
(57, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-12 14:01:52'),
(62, 1, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-12 14:58:03'),
(63, 8, 'niger', 'quartier 1', 'Niamey', 'Avenue_1', 1, '2025-12-12 15:00:35'),
(64, 1, 'niger', '', 'Niamey', 'KANO_STATE1', 1, '2025-12-12 22:39:47'),
(65, 9, 'niger', '', 'Niamey', 'Avenue_1', 1, '2025-12-13 00:20:11'),
(66, 11, 'niger', '', 'Niamey', 'KANO_STATE1', 1, '2025-12-13 01:24:00'),
(67, 1, 'niger', '', 'Niamey', 'sabonGari123', 1, '2025-12-13 01:31:57'),
(68, 8, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-13 22:31:59'),
(69, 12, 'niger', '', 'Niamey', 'Avenue_1', 1, '2025-12-13 23:58:34'),
(70, 1, 'niger', '', 'Niamey', 'KANO_STATE1', 1, '2025-12-14 19:57:40'),
(71, 15, 'niger', '', 'Niamey', 'Niamey_commun_I', 1, '2025-12-18 10:18:18');



--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);


--
-- Indexes for table `login_ajax`
--
ALTER TABLE `login_ajax`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_variant_alert` (`variant_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `unique_variant` (`product_id`,`size_id`,`color_id`,`material_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_expires` (`expires_at`);


--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--


--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--

--
-- AUTO_INCREMENT for table `login_ajax`
--
ALTER TABLE `login_ajax`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);




--
-- Constraints for table `low_stock_alerts`
--
ALTER TABLE `low_stock_alerts`
  ADD CONSTRAINT `low_stock_alerts_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`);

--
-- Constraints for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`),
  ADD CONSTRAINT `product_variants_ibfk_3` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `product_variants_ibfk_4` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`);


-- Constraints for table `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_reservations`
--
ALTER TABLE `stock_reservations`
  ADD CONSTRAINT `stock_reservations_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_reservations_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;



-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
