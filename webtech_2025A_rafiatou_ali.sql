CREATE DATABASE IF NOT EXISTS webtech_2025A_rafiatou_ali;
USE webtech_2025A_rafiatou_ali;

-- =============================
-- 1. INDEPENDENT / LOOKUP TABLES
-- =============================

CREATE TABLE `categories` (
`id` int NOT NULL,
`name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
`description` text COLLATE utf8mb4_general_ci,
`parent_id` int DEFAULT NULL,
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `colors` (
`id` int NOT NULL,
`name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
`hex_code` varchar(7) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `materials` (
`id` int NOT NULL,
`name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
`description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sizes` (
`id` int NOT NULL,
`name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
`category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- =============================
-- 2. PRODUCT-RELATED TABLES
-- =============================

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

CREATE TABLE `product_images` (
`id` int NOT NULL,
`product_id` int NOT NULL,
`image_url` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
`is_primary` tinyint(1) DEFAULT '0',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- =============================
-- 3. ORDER-RELATED TABLES
-- =============================

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

CREATE TABLE `order_status_logs` (
`id` int NOT NULL,
`order_id` int NOT NULL,
`old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
`new_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
`changed_by` int DEFAULT NULL,
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================
-- 4. STOCK & MONITORING TABLES
-- =============================

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

CREATE TABLE `stock_reservations` (
`id` int NOT NULL,
`variant_id` int NOT NULL,
`order_id` int NOT NULL,
`quantity` int NOT NULL,
`expires_at` datetime NOT NULL,
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `low_stock_alerts` (
`id` int NOT NULL,
`variant_id` int NOT NULL,
`product_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
`current_stock` int NOT NULL,
`threshold` int DEFAULT '5',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
