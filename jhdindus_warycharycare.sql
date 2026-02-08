-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 08, 2026 at 03:58 PM
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
-- Database: `jhdindus_warycharycare`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$z/QfcgDluS3Wnruxfea/d.RoI3Q4dx.U69P6kJRZnemZ3r3N9ms4W', 'admin@warychary.in', '2026-02-06 13:29:47', '2026-02-06 13:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_mobile` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(100) NOT NULL,
  `shipping_pincode` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `payment_id` varchar(100) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `senior_partner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `customer_name`, `customer_email`, `customer_mobile`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_pincode`, `total_amount`, `payment_status`, `payment_id`, `partner_id`, `senior_partner_id`, `created_at`, `updated_at`) VALUES
(1, '', 1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, kandela, Jind, Haryana', 'Jind', 'Haryana', '126125', 232.00, 'pending', NULL, NULL, NULL, '2026-02-08 08:48:36', '2026-02-08 08:48:36'),
(4, 'ORD_1770540802_2234', 1, 'RAHUL DHIMAN', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'KANDELA06', 'Jind', 'Haryana', '126125', 232.00, 'pending', NULL, NULL, NULL, '2026-02-08 08:53:22', '2026-02-08 08:53:22'),
(5, 'ORD_1770540838_5721', 1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 232.00, 'pending', NULL, NULL, NULL, '2026-02-08 08:53:58', '2026-02-08 08:53:58'),
(6, 'order_SDavUsdsl8seP4', 1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 232.00, 'pending', NULL, NULL, NULL, '2026-02-08 09:01:28', '2026-02-08 09:01:28'),
(7, 'order_SDawy4huEfK5zI', 1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'pending', NULL, NULL, NULL, '2026-02-08 09:02:51', '2026-02-08 09:02:51'),
(8, 'order_SDboWKZaJBmG1D', 2, 'Rahul Dhiman', 'vyparsetu@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'paid', 'pay_SDbpAKxgiSkHcF', NULL, NULL, '2026-02-08 09:53:33', '2026-02-08 09:54:25'),
(9, 'order_SDc3UidQeOlhqi', 1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'pending', NULL, NULL, NULL, '2026-02-08 10:07:43', '2026-02-08 10:07:44'),
(10, 'order_SDc51OKkeP8iiE', 2, 'Rahul Dhiman', 'vyparsetu@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'paid', 'pay_SDc5HNwrB2MPVQ', NULL, NULL, '2026-02-08 10:09:10', '2026-02-08 10:09:40'),
(11, 'order_SDcKdbH9MUJnJN', 2, 'Rahul Dhiman', 'vyparsetu@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'paid', 'pay_SDcL0ZOsUAt9l0', NULL, NULL, '2026-02-08 10:23:57', '2026-02-08 10:24:33'),
(12, 'order_SDcNT1gDErivhY', 1, 'Rahul', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', 'Jind', 'Haryana', '126125', 1.00, 'paid', 'pay_SDcNncS0xxsDbw', NULL, NULL, '2026-02-08 10:26:38', '2026-02-08 10:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `free_gift_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `total_price`, `free_gift_name`, `created_at`) VALUES
(1, 6, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 232.00, 232.00, 'Panty ( Pack of 2 )', '2026-02-08 09:01:28'),
(2, 7, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 09:02:51'),
(3, 8, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 09:53:33'),
(4, 9, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 10:07:44'),
(5, 10, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 10:09:10'),
(6, 11, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 10:23:57'),
(7, 12, 2, 'Warychary Sanitary Pad ( Pack Of 30 )', 1, 1.00, 1.00, 'Panty ( Pack of 2 )', '2026-02-08 10:26:38');

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `senior_partner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `referral_code` varchar(50) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT 15.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `senior_partner_id`, `name`, `email`, `mobile`, `gender`, `image`, `state`, `city`, `pincode`, `address`, `password`, `status`, `referral_code`, `commission`, `created_at`, `updated_at`) VALUES
(1, 1, 'Rahul', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Male', 'uploads/partners/partner_698775f6b5f33.png', 'Haryana', 'Jind', '126125', 'Near PNB Bank, Kandela, Jind, Haryana, India', '$2y$12$XRFwxMqtmRVgXXl16AN9auMeN9fDV4g7teQ9akE.DaR7N6htZhZsS', 'active', NULL, 15.00, '2026-02-07 17:27:18', '2026-02-07 17:27:18'),
(2, 2, 'Beast Dipanshu', 'jhdindustrialsolution@gmail.com', '8295106402', 'Male', 'uploads/partners/partner_6987810ec23ce.png', 'Haryana', 'Gurgaon', '122050', 'Near Old Bus Stand, Manesar, Haryana, 122050', '$2y$12$YwuGYnpC5OtgnEmeSfGTbet.fQvScOXiq9G9MDy1KIi8Yu0enSGHS', 'active', '1CBB59BE', 15.00, '2026-02-07 18:14:38', '2026-02-07 18:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `partner_earnings`
--

CREATE TABLE `partner_earnings` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `partner_type` enum('marketing','senior') NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `mrp` decimal(10,2) DEFAULT 0.00,
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `sales_price` decimal(10,2) DEFAULT 0.00,
  `delivery_cost` decimal(10,2) DEFAULT 0.00,
  `packing_cost` decimal(10,2) DEFAULT 0.00,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `is_free_product_active` tinyint(1) DEFAULT 0,
  `free_product_name` varchar(255) DEFAULT NULL,
  `free_product_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `featured_image`, `gallery_images`, `short_description`, `description`, `mrp`, `purchase_price`, `sales_price`, `delivery_cost`, `packing_cost`, `total_cost`, `is_free_product_active`, `free_product_name`, `free_product_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Warychary Sanitary Pad ( Pack Of 20 )', 'warychary-sanitary-pad-pack-of-20-', 'uploads/products/1770530484_warychary.jpeg', '[\"uploads\\/products\\/1770530484_0_OIP.webp\"]', 'Warychary Sanitary Pad (Pack of 20) offers reliable protection, high absorbency, and all-day comfort. Designed with a soft, breathable top layer and secure wings to keep you dry, fresh, and confident during your period.', '<p data-start=\"391\" data-end=\"677\"><strong data-start=\"391\" data-end=\"430\">Warychary Sanitary Pad (Pack of 20)</strong> is thoughtfully designed to provide superior comfort and dependable protection throughout your menstrual cycle. Made with a soft cottony top layer, these pads are gentle on the skin and help prevent irritation, making them suitable for daily use.</p><p data-start=\"679\" data-end=\"925\">The pads feature <strong data-start=\"696\" data-end=\"726\">high absorbency technology</strong> that quickly locks in moisture and prevents leakage, even during heavy flow days. Its <strong data-start=\"813\" data-end=\"834\">breathable design</strong> allows proper airflow, reducing discomfort and keeping you feeling fresh for longer hours.</p><p data-start=\"927\" data-end=\"1168\">With <strong data-start=\"932\" data-end=\"957\">strong adhesive wings</strong>, Warychary Sanitary Pads stay firmly in place, allowing you to move freely without worry. Whether you’re at work, school, traveling, or resting at home, these pads ensure long-lasting protection and confidence.</p><p data-start=\"1170\" data-end=\"1187\"><strong data-start=\"1170\" data-end=\"1187\">Key Features:</strong></p><ul data-start=\"1188\" data-end=\"1407\">\r\n<li data-start=\"1188\" data-end=\"1216\">\r\n<p data-start=\"1190\" data-end=\"1216\">Pack of 20 sanitary pads</p>\r\n</li>\r\n<li data-start=\"1217\" data-end=\"1251\">\r\n<p data-start=\"1219\" data-end=\"1251\">Soft &amp; skin-friendly top layer</p>\r\n</li>\r\n<li data-start=\"1252\" data-end=\"1291\">\r\n<p data-start=\"1254\" data-end=\"1291\">High absorbency for leak protection</p>\r\n</li>\r\n<li data-start=\"1292\" data-end=\"1337\">\r\n<p data-start=\"1294\" data-end=\"1337\">Breathable material for all-day freshness</p>\r\n</li>\r\n<li data-start=\"1338\" data-end=\"1372\">\r\n<p data-start=\"1340\" data-end=\"1372\">Secure wings for a perfect fit</p>\r\n</li>\r\n<li data-start=\"1373\" data-end=\"1407\">\r\n<p data-start=\"1375\" data-end=\"1407\">Suitable for day and night use</p>\r\n</li>\r\n</ul><p>\r\n\r\n\r\n\r\n\r\n</p><p data-start=\"1409\" data-end=\"1527\">Choose <strong data-start=\"1416\" data-end=\"1442\">Warychary Sanitary Pad</strong> for comfort you can trust and protection you can rely on—every day of your cycle.&nbsp;</p>', 260.00, 200.00, 232.00, 0.00, 0.00, 200.00, 1, 'Panty ( Pack of 2 )', 'uploads/products/1770530484_free_OIP.webp', 'active', '2026-02-08 06:01:24', '2026-02-08 06:01:24'),
(2, 'Warychary Sanitary Pad ( Pack Of 30 )', 'warychary-sanitary-pad-pack-of-30-', 'uploads/products/1770530524_warychary.jpeg', '[\"uploads\\/products\\/1770530524_0_warychary.jpeg\"]', 'Warychary Sanitary Pad (Pack of 20) offers reliable protection, high absorbency, and all-day comfort. Designed with a soft, breathable top layer and secure wings to keep you dry, fresh, and confident during your period.', '<p><strong data-start=\"137\" data-end=\"176\">Warychary Sanitary Pad (Pack of 20)</strong> offers reliable protection, high absorbency, and all-day comfort. Designed with a soft, breathable top layer and secure wings to keep you dry, fresh, and confident during your period.</p>', 260.00, 200.00, 1.00, 0.00, 0.00, 200.00, 1, 'Panty ( Pack of 2 )', 'uploads/products/1770530524_free_warychary.jpeg', 'active', '2026-02-08 06:02:04', '2026-02-08 09:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `razorpay_settings`
--

CREATE TABLE `razorpay_settings` (
  `id` int(11) NOT NULL,
  `key_id` varchar(255) NOT NULL,
  `key_secret` varchar(255) NOT NULL,
  `webhook_secret` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'INR',
  `mode` enum('test','live') DEFAULT 'test',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `razorpay_settings`
--

INSERT INTO `razorpay_settings` (`id`, `key_id`, `key_secret`, `webhook_secret`, `currency`, `mode`, `created_at`, `updated_at`) VALUES
(1, 'rzp_live_SBaWkVJiaA14zd', 'IahTfrA8iQ5ioZektXj1pxG4', '', 'INR', 'live', '2026-02-08 08:22:31', '2026-02-08 08:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `razorpay_transactions`
--

CREATE TABLE `razorpay_transactions` (
  `id` int(11) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `razorpay_transactions`
--

INSERT INTO `razorpay_transactions` (`id`, `order_id`, `payment_id`, `amount`, `status`, `email`, `contact`, `created_at`) VALUES
(1, 'order_SDboWKZaJBmG1D', 'pay_SDbpAKxgiSkHcF', 1.00, 'captured', NULL, NULL, '2026-02-08 09:54:25'),
(2, 'order_SDc51OKkeP8iiE', 'pay_SDc5HNwrB2MPVQ', 1.00, 'captured', NULL, NULL, '2026-02-08 10:09:40'),
(3, 'order_SDcKdbH9MUJnJN', 'pay_SDcL0ZOsUAt9l0', 1.00, 'captured', NULL, NULL, '2026-02-08 10:24:33'),
(4, 'order_SDcNT1gDErivhY', 'pay_SDcNncS0xxsDbw', 1.00, 'captured', NULL, NULL, '2026-02-08 10:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `review_images` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_name`, `user_email`, `rating`, `review_text`, `review_images`, `status`, `created_at`) VALUES
(1, 2, 'Rahul', 'rahul.dhiman.mohanlal@gmail.com', 5, 'The greatest product i have seen here', '[\"uploads\\/reviews\\/69883ffcf249c.jpeg\"]', 'approved', '2026-02-08 07:49:16'),
(2, 1, 'Rahul', 'rahul.dhiman.mohanlal@gmail.com', 5, 'great product i have got from warychary care', '[\"uploads\\/reviews\\/6988445b6c4cf.png\"]', 'approved', '2026-02-08 08:07:55');

-- --------------------------------------------------------

--
-- Table structure for table `senior_partners`
--

CREATE TABLE `senior_partners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `referral_code` varchar(6) NOT NULL,
  `commission` decimal(5,2) DEFAULT 2.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `senior_partners`
--

INSERT INTO `senior_partners` (`id`, `name`, `email`, `mobile`, `image`, `gender`, `state`, `city`, `pincode`, `address`, `password`, `referral_code`, `commission`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rahul Dhiman', 'rahul.dhiman.mohanlal@gmail.com', '', 'assets/uploads/partners/sp_1770455905_69870361cfab9.png', 'Male', 'Haryana', 'Jind', '126125', 'Near PNB Bank, kandela, Jind, Haryana', '$2y$12$A7Y5zn9czsyrn7fnXUvwseKDgDGOaUmZAvZJNBSOGuFKYQclvXW8y', 'LFKNLK', 2.00, 'active', '2026-02-07 09:18:25', '2026-02-07 09:18:25'),
(2, 'Dipanshu', 'jhdindustrialsolution@gmail.com', '8295106402', 'assets/uploads/partners/sp_1770487952_698780901cfe3.png', 'Male', 'Haryana', 'Gurgaon', '122050', 'Near Old Bus Stand, Manesar, Haryana, 122050', '$2y$12$ZGP2yhz8G5oYGQyZKxDHRuaoXVxMznzZMlnxu5SZkSF0oxfBioWmi', 'POOSTC', 2.00, 'active', '2026-02-07 18:12:32', '2026-02-07 18:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `senior_partner_earnings`
--

CREATE TABLE `senior_partner_earnings` (
  `id` int(11) NOT NULL,
  `senior_partner_id` int(11) NOT NULL,
  `source_partner_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 2.00,
  `status` enum('pending','paid') DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `encryption` enum('tls','ssl','none') DEFAULT 'tls',
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `host`, `username`, `password`, `port`, `encryption`, `from_email`, `from_name`, `updated_at`) VALUES
(1, 'mail.warychary.com', 'info@warychary.com', 'Rd14072003@./', 465, 'ssl', 'info@warychary.com', 'Warychary Care ', '2026-02-07 09:22:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `partner_id`, `name`, `email`, `mobile`, `gender`, `image`, `state`, `city`, `pincode`, `address`, `password`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'Rahul', 'rahul.dhiman.mohanlal@gmail.com', '8059982049', 'Male', 'assets/uploads/users/698792bbcaeb8.png', 'Haryana', 'JIND', '126102', 'VPO KANDELA\r\nVPO KANDELA NEAR PNB BANK', '$2y$12$YNoCigeoJGw.e3WZWlfKSuTUI66xnTOmy9/lF9QitMnQGEZwKlnLa', 'active', '2026-02-07 19:30:03', '2026-02-08 10:02:55'),
(2, NULL, 'Rahul Dhiman', 'vyparsetu@gmail.com', '8059982049', 'Male', NULL, 'Haryana', 'Jind', '126125', 'Near PNB Bank, Kandela, Jind, Haryana, 126125', '', 'active', '2026-02-08 09:53:33', '2026-02-08 09:53:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `senior_partner_id` (`senior_partner_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD KEY `senior_partner_id` (`senior_partner_id`);

--
-- Indexes for table `partner_earnings`
--
ALTER TABLE `partner_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `razorpay_settings`
--
ALTER TABLE `razorpay_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `razorpay_transactions`
--
ALTER TABLE `razorpay_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `senior_partners`
--
ALTER TABLE `senior_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `referral_code` (`referral_code`);

--
-- Indexes for table `senior_partner_earnings`
--
ALTER TABLE `senior_partner_earnings`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `partner_id` (`partner_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `partner_earnings`
--
ALTER TABLE `partner_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `razorpay_settings`
--
ALTER TABLE `razorpay_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `razorpay_transactions`
--
ALTER TABLE `razorpay_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `senior_partners`
--
ALTER TABLE `senior_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `senior_partner_earnings`
--
ALTER TABLE `senior_partner_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`senior_partner_id`) REFERENCES `senior_partners` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partners`
--
ALTER TABLE `partners`
  ADD CONSTRAINT `partners_ibfk_1` FOREIGN KEY (`senior_partner_id`) REFERENCES `senior_partners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partner_earnings`
--
ALTER TABLE `partner_earnings`
  ADD CONSTRAINT `partner_earnings_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
