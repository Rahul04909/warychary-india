-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2026 at 04:00 PM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jhdindus_warychary`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `created_at`, `updated_at`, `last_login`, `login_attempts`, `locked_until`, `created_by`, `profile_image`, `phone`, `address`, `permissions`) VALUES
(1, 'admin', '$2y$10$pb2cjAy8SxN/4MqN3hI1nedMh8CnYqrxRTz1752WWBjhlEe1/R/L6', 'System Administrator', 'admin@warycharycare.com', 'super_admin', 'active', '2025-09-18 17:55:18', '2026-02-08 05:21:41', '2026-02-08 05:21:41', 0, NULL, NULL, NULL, NULL, NULL, '{\"users\": true, \"orders\": true, \"reports\": true, \"products\": true, \"settings\": true, \"dashboard\": true, \"admin_management\": true}'),
(2, 'moderator', '$2y$10$UqRZYvx/Zcns3vW6nJjLwe6svdQMXeVuSdkdDMYNZA60fm4IkkWda', 'Content Moderator', 'moderator@warycharycare.com', 'moderator', 'active', '2025-09-18 17:55:18', '2025-09-18 17:55:18', NULL, 0, NULL, NULL, NULL, NULL, NULL, '{\"users\": false, \"orders\": true, \"reports\": false, \"products\": true, \"settings\": false, \"dashboard\": true, \"admin_management\": false}');

-- --------------------------------------------------------

--
-- Table structure for table `bank_details`
--

CREATE TABLE `bank_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) NOT NULL,
  `guest_email` varchar(100) NOT NULL,
  `guest_phone` varchar(20) NOT NULL,
  `guest_state` varchar(50) NOT NULL,
  `guest_district` varchar(50) DEFAULT NULL,
  `guest_pincode` varchar(10) NOT NULL,
  `guest_full_address` text NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL COMMENT 'Tracking number provided by courier service',
  `courier_name` varchar(100) DEFAULT NULL COMMENT 'Name of the courier/shipping company',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `guest_name`, `guest_email`, `guest_phone`, `guest_state`, `guest_district`, `guest_pincode`, `guest_full_address`, `product_id`, `product_name`, `product_price`, `quantity`, `total_amount`, `payment_id`, `payment_status`, `order_status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `tracking_number`, `courier_name`, `created_at`, `updated_at`) VALUES
(42, NULL, 'RAHUL DHIMAN', 'rahul.dhiman.mohanlal@gmail.com', '08059982049', 'Haryana', 'Jind', '126125', 'KANDELA06', 2, 'WaryChary Sanitary Pad (Pack Of 20)', 240.00, 1, 240.00, NULL, 'pending', 'pending', 'order_SCl7NtlU1ZTmrb', NULL, NULL, NULL, NULL, '2026-02-06 06:20:38', '2026-02-06 06:20:38'),
(43, NULL, 'Heena Dua', 'heenadua0628@gmail.com', '7206793380', 'Haryana', 'Hisar', '125006', 'H.No.970, VPO Satrod Kalan , Hisar cantt , hisar', 1, 'WaryChary Sanitary Pad (Pack Of 30)', 360.00, 1, 360.00, NULL, 'completed', 'confirmed', 'order_SCuCyIyATUrgqC', 'pay_SCuDEXMnrWwBxl', '8ef9df4bf83112f32289c01e42b5a1bc910f15af423822c0d71fa4ef09946eb1', NULL, NULL, '2026-02-06 15:14:10', '2026-02-06 15:15:03'),
(41, NULL, 'Heena Dua', 'heenadua0628@gmail.com', '7206793380', 'Haryana', 'Hisar', '125006', 'H.No. 970, VPO Satrod Kalan,  Hisar Cantt, Hisar', 1, 'WaryChary Sanitary Pad (Pack Of 30)', 360.00, 1, 360.00, NULL, 'pending', 'pending', 'order_SChpllXuYxJGzu', NULL, NULL, NULL, NULL, '2026-02-06 03:07:53', '2026-02-06 03:07:53');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `partner_id` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `state` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `pincode` varchar(6) NOT NULL,
  `full_address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `referral_code` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `earning` decimal(10,2) DEFAULT 0.00,
  `total_earnings` decimal(10,2) DEFAULT 0.00,
  `referred_by_senior_partner` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_bank_details`
--

CREATE TABLE `partner_bank_details` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_earnings`
--

CREATE TABLE `partner_earnings` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `earning_amount` decimal(10,2) NOT NULL,
  `earning_percentage` decimal(5,2) NOT NULL,
  `order_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_payout_history`
--

CREATE TABLE `partner_payout_history` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `payout_amount` decimal(10,2) NOT NULL,
  `payout_date` date NOT NULL,
  `payout_month` varchar(7) NOT NULL,
  `earnings_before_payout` decimal(10,2) NOT NULL,
  `earnings_after_payout` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('bank_transfer','upi','cash','cheque') DEFAULT 'bank_transfer',
  `transaction_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT 'Admin',
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `sales_price` decimal(10,2) NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `offer_product_name` varchar(255) DEFAULT NULL,
  `offer_product_image` varchar(255) DEFAULT NULL,
  `offer_product_purchase_price` decimal(10,2) DEFAULT NULL,
  `offer_product_sales_price` decimal(10,2) DEFAULT NULL,
  `offer_product_mrp` decimal(10,2) DEFAULT NULL,
  `delivery_cost` decimal(10,2) NOT NULL,
  `packing_cost` decimal(10,2) NOT NULL,
  `total_expense` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `product_image`, `product_description`, `purchase_price`, `sales_price`, `mrp`, `offer_product_name`, `offer_product_image`, `offer_product_purchase_price`, `offer_product_sales_price`, `offer_product_mrp`, `delivery_cost`, `packing_cost`, `total_expense`, `created_at`, `updated_at`) VALUES
(1, 'WaryChary Sanitary Pad (Pack Of 30)', 'product_68caabec3fea6.png', '<p data-start=\"371\" data-end=\"607\">Stay confident and worry-free during your periods with <strong data-start=\"426\" data-end=\"453\">WaryChary Sanitary Pads</strong>. Designed for maximum comfort and long-lasting protection, this <strong data-start=\"518\" data-end=\"553\">Pack of 20 ultra-absorbent pads</strong> is your perfect companion for those challenging days.</p><p>\r\n</p><p data-start=\"609\" data-end=\"888\">Crafted with a soft cottony top layer and advanced gel-lock technology, these pads quickly absorb heavy flow and lock it in, keeping you dry and fresh for hours. The extra-wide wings ensure a secure fit with no shifting or leakage, whether you\'re at work, traveling, or sleeping.</p>', 99.00, 360.00, 120.00, 'Double panty Free', 'offer_68ca97fe35ed2.webp', 70.00, 0.00, 0.00, 62.00, 12.00, 243.00, '2025-09-17 11:14:06', '2026-01-28 10:23:51'),
(2, 'WaryChary Sanitary Pad (Pack Of 20)', 'product_68ca9b928caaf.png', '<h4 data-start=\"1102\" data-end=\"1134\"><strong data-start=\"1107\" data-end=\"1132\">Why Choose WaryChary?</strong></h4><ul data-start=\"1135\" data-end=\"1332\">\r\n<li data-start=\"1135\" data-end=\"1225\">\r\n<p data-start=\"1137\" data-end=\"1225\">Thoughtfully designed for everyday use ï¿½ whether at home, at work, or while traveling.</p>\r\n</li>\r\n<li data-start=\"1226\" data-end=\"1271\">\r\n<p data-start=\"1228\" data-end=\"1271\">Reliable during light to heavy flow days.</p>\r\n</li>\r\n<li data-start=\"1272\" data-end=\"1332\">\r\n<p data-start=\"1274\" data-end=\"1332\">Made with skin-safe materials and eco-conscious processes.</p>\r\n</li>\r\n</ul><h4 data-start=\"1334\" data-end=\"1359\"><strong data-start=\"1339\" data-end=\"1357\">Pack Contains:</strong></h4><p>\r\n\r\n\r\n</p><ul data-start=\"1360\" data-end=\"1455\">\r\n<li data-start=\"1360\" data-end=\"1401\">\r\n<p data-start=\"1362\" data-end=\"1401\">30 individually wrapped sanitary pads</p>\r\n</li>\r\n<li data-start=\"1402\" data-end=\"1430\">\r\n<p data-start=\"1404\" data-end=\"1430\">Size: Regular with wings</p>\r\n</li>\r\n<li data-start=\"1431\" data-end=\"1455\">\r\n<p data-start=\"1433\" data-end=\"1455\">Eco-friendly packaging</p></li></ul>', 65.00, 240.00, 120.00, 'Panty Free', 'offer_68ca9b70a5227.png', 35.00, 0.00, 0.00, 62.00, 9.00, 171.00, '2025-09-17 11:28:48', '2026-02-03 13:06:52');

-- --------------------------------------------------------

--
-- Table structure for table `razorpay_settings`
--

CREATE TABLE `razorpay_settings` (
  `id` int(11) NOT NULL,
  `razorpay_key_id` varchar(255) NOT NULL,
  `razorpay_key_secret` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `razorpay_settings`
--

INSERT INTO `razorpay_settings` (`id`, `razorpay_key_id`, `razorpay_key_secret`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'rzp_live_SBaWkVJiaA14zd', 'IahTfrA8iQ5ioZektXj1pxG4', 1, '2026-02-03 07:20:30', '2026-02-03 07:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `senior_partners`
--

CREATE TABLE `senior_partners` (
  `id` int(11) NOT NULL,
  `partner_id` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `earning` decimal(10,2) DEFAULT 0.00,
  `total_earnings` decimal(10,2) DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_partner_earnings`
--

CREATE TABLE `senior_partner_earnings` (
  `id` int(11) NOT NULL,
  `senior_partner_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `earning_amount` decimal(10,2) NOT NULL,
  `earning_percentage` decimal(5,2) NOT NULL,
  `order_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_partner_payout_history`
--

CREATE TABLE `senior_partner_payout_history` (
  `id` int(11) NOT NULL,
  `senior_partner_id` int(11) NOT NULL,
  `payout_amount` decimal(10,2) NOT NULL,
  `previous_earnings` decimal(10,2) NOT NULL,
  `payout_date` timestamp NULL DEFAULT current_timestamp(),
  `payout_method` enum('bank_transfer','upi','cash','cheque') DEFAULT 'bank_transfer',
  `transaction_id` varchar(100) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `account_holder_name` varchar(255) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `notes` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL,
  `smtp_username` varchar(255) NOT NULL,
  `smtp_password` varchar(255) NOT NULL,
  `smtp_from_email` varchar(255) NOT NULL,
  `smtp_from_name` varchar(255) NOT NULL,
  `smtp_encryption` enum('tls','ssl') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_from_email`, `smtp_from_name`, `smtp_encryption`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'mail.warychary.com', 465, 'info@warychary.com', 'Sukh@2025', 'info@warychary.com', 'WaryChary Care', 'ssl', 1, '2025-09-16 14:33:19', '2025-09-16 14:33:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `state` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `full_address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `referral_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `referred_by_partner` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bank_details`
--
ALTER TABLE `bank_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_tracking_number` (`tracking_number`(250)),
  ADD KEY `idx_courier_name` (`courier_name`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partner_id` (`partner_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `referred_by_senior_partner` (`referred_by_senior_partner`);

--
-- Indexes for table `partner_bank_details`
--
ALTER TABLE `partner_bank_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_partner_bank` (`partner_id`);

--
-- Indexes for table `partner_earnings`
--
ALTER TABLE `partner_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `partner_payout_history`
--
ALTER TABLE `partner_payout_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partner_payout` (`partner_id`,`payout_month`),
  ADD KEY `idx_payout_date` (`payout_date`),
  ADD KEY `idx_payout_month` (`payout_month`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `razorpay_settings`
--
ALTER TABLE `razorpay_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `senior_partners`
--
ALTER TABLE `senior_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partner_id` (`partner_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `senior_partner_earnings`
--
ALTER TABLE `senior_partner_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `senior_partner_id` (`senior_partner_id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `senior_partner_payout_history`
--
ALTER TABLE `senior_partner_payout_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_senior_partner_payout` (`senior_partner_id`),
  ADD KEY `idx_payout_date` (`payout_date`),
  ADD KEY `idx_payout_status` (`status`),
  ADD KEY `idx_payout_method` (`payout_method`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `referred_by_partner` (`referred_by_partner`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bank_details`
--
ALTER TABLE `bank_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `partner_bank_details`
--
ALTER TABLE `partner_bank_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `partner_earnings`
--
ALTER TABLE `partner_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `partner_payout_history`
--
ALTER TABLE `partner_payout_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `razorpay_settings`
--
ALTER TABLE `razorpay_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `senior_partners`
--
ALTER TABLE `senior_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `senior_partner_earnings`
--
ALTER TABLE `senior_partner_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `senior_partner_payout_history`
--
ALTER TABLE `senior_partner_payout_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
