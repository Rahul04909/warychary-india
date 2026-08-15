/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.16-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: jhdindus_warycharycare
-- ------------------------------------------------------
-- Server version	10.11.16-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `jhdindus_warycharycare`
--

-- CREATE DATABASE /*!32312 IF NOT EXISTS*/ `jhdindus_warycharycare` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;

-- USE `jhdindus_warycharycare`;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES
(1,'admin','$2y$12$z/QfcgDluS3Wnruxfea/d.RoI3Q4dx.U69P6kJRZnemZ3r3N9ms4W','admin@warychary.in','2026-02-06 13:29:47','2026-02-06 13:29:47');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_details`
--

DROP TABLE IF EXISTS `bank_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('partner','senior_partner') NOT NULL,
  `account_holder_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `branch_address` text DEFAULT NULL,
  `bank_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_bank` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_details`
--

LOCK TABLES `bank_details` WRITE;
/*!40000 ALTER TABLE `bank_details` DISABLE KEYS */;
INSERT INTO `bank_details` VALUES
(1,3,'senior_partner','Rahul Dhiman','1196000100351034','PUNB0119600','Punjab National Bank','VPOKANDELA','PUNB','2026-02-09 11:52:19','2026-02-09 11:52:19'),
(2,3,'partner','Rahul','644202010008357','UBIN0564427','Union Bank of India','JAGAT BLDG ADJOINING  OBC RANI TALAB JIND  JIND  PIN CODE-126102','UBIN','2026-02-09 11:53:07','2026-02-09 11:53:07');
/*!40000 ALTER TABLE `bank_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `free_gift_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(19,24,2,'Warychary Sanitary Pad ( Pack Of 30 )',1,1.00,1.00,'Panty ( Pack of 2 )','2026-02-10 09:34:00'),
(21,26,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-02-13 14:57:55'),
(22,27,2,'Warychary Pack Of 30 ',1,360.00,360.00,'Double Panty','2026-02-22 06:29:48'),
(23,28,2,'Warychary Pack Of 30 ',1,360.00,360.00,'Double Panty','2026-02-27 14:28:22'),
(24,29,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-03-02 09:50:07'),
(25,30,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-03-02 13:18:41'),
(26,31,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-03-03 09:49:53'),
(29,34,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-03-08 14:58:06'),
(30,35,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-03-25 08:46:24'),
(31,36,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-02 13:56:50'),
(32,37,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-03 11:47:59'),
(33,38,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-05 05:07:21'),
(34,39,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-05 07:12:28'),
(35,40,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-05 13:57:01'),
(36,41,3,'Warychary Pack Of 20 ',1,1.00,1.00,'panty','2026-04-07 07:17:56'),
(37,42,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-08 07:45:03'),
(38,43,3,'Warychary Pack Of 20 ',1,240.00,240.00,'panty','2026-04-09 17:49:35');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `courier_name` varchar(255) DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL,
  `dispatched_from` varchar(255) DEFAULT NULL,
  `dispatched_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `partner_id` (`partner_id`),
  KEY `senior_partner_id` (`senior_partner_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`senior_partner_id`) REFERENCES `senior_partners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(24,'order_SEOY6zsdAsj71A',5,'Rekha','sukhwantsingh16@gmail.com','8607642330','Hisar cantt','Hisar','Haryana','125006',1.00,'pending',NULL,6,3,'2026-02-10 09:34:00','2026-02-10 09:34:00',NULL,NULL,NULL,NULL,'pending'),
(26,'order_SFffclLeCnhNm1',8,'Sheetal kumari ','kumari.shetal36@gmail.com','8374715092','C615, Jalvayu Towers, Sec 56','Gurugram ','Haryana','122011',240.00,'paid','pay_SFffoFEolA91LA',NULL,NULL,'2026-02-13 14:57:54','2026-02-18 03:01:43','india post','CH039823688IN','HISAR','2026-02-18 08:31:43','completed'),
(27,'order_SJ5oyiAFV83Cy6',9,'Manoj Kuhar','kuharmanoj@gmail.com','9999828073','Saffron enclave pvt ltd \r\n415/2,MG ROAD SECTOR-14 GURGAON \r\n122001','Gurgaon ','Haryana ','122001',360.00,'pending',NULL,NULL,NULL,'2026-02-22 06:29:48','2026-02-22 06:29:48',NULL,NULL,NULL,NULL,'pending'),
(28,'order_SLCe5xnUJjkB6n',10,'Tanvi Rastogi ','rastogi.tanvi7@gmail.com','9871192228','48/49, Master Colony \r\nNear Hisar Hyundai \r\nDelhi By pass road','Hisar','Haryana ','125044',360.00,'pending',NULL,NULL,NULL,'2026-02-27 14:28:21','2026-02-27 14:28:22',NULL,NULL,NULL,NULL,'pending'),
(29,'order_SMJVY73KGxhIPH',11,'Varsha Gupta ','varshagupta437@gmail.com','9255646333','House no 750\r\nUrban Estate,Near Ram Dwar ','Hisar','Haryana ','125001',240.00,'pending',NULL,NULL,NULL,'2026-03-02 09:50:07','2026-03-02 09:50:07',NULL,NULL,NULL,NULL,'pending'),
(30,'order_SMN3rwdNfhsSk3',12,'sangita kumari','kumarisangita01205@gmail.com','7015926435','H no 30 chodhary complex TCP 2 hisar cant','Hisar ','Haryana ','125001',240.00,'paid','pay_SMN42VVUgejpv2',NULL,NULL,'2026-03-02 13:18:41','2026-03-09 10:31:30','S','S','Hisar cantt','2026-03-09 16:01:30','completed'),
(31,'order_SMi2QghHO5rGkE',11,'Varsha Gupta ','varshagupta437@gmail.com','9255646333','House no 750 \r\nUrban Estate Near Ram Dwar','Hisar','Haryana ','125001',240.00,'paid','pay_SMN42VVUgejpv3',NULL,NULL,'2026-03-03 09:49:53','2026-03-09 10:31:08','S','S','Hisar cantt ','2026-03-09 16:01:08','completed'),
(34,'order_SOlxaTs33vj8ne',13,'Jaiti Arora','jaiti.arora@gmail.com','9215022229','H no.378 ,old pla behind town park,hisar (haryana )','hisar','Haryana','125001',240.00,'paid','pay_SOly8SThkbrpRi',NULL,NULL,'2026-03-08 14:58:06','2026-03-09 10:30:45','S','S','Hisar cantt','2026-03-09 16:00:45','completed'),
(35,'order_SVOi0RTrdRnLkD',14,'Rajnesh Kumar','divineenergym@gmail.com','7015340472','C/O RUKMANI TOWER FIRST, FLOOR OLD NAJAFGARH ROAD, Bahadurgarh,','BAHADURGARH','INDIA','124507',240.00,'pending',NULL,NULL,NULL,'2026-03-25 08:46:24','2026-03-25 08:46:24',NULL,NULL,NULL,NULL,'pending'),
(36,'order_SYeGuBJGiHDh6y',4,'Rahul Dhiman','rd5212452@gmail.com','8059982049','KANDELA','Delhi ','Delhi','125102',240.00,'pending',NULL,3,3,'2026-04-02 13:56:50','2026-04-02 13:56:50',NULL,NULL,NULL,NULL,'pending'),
(37,'order_SZ0btsMvPOedK6',4,'Rahul Dhiman','rd5212452@gmail.com','9466939049','Kandela','Jind','Haryana','126125',240.00,'pending',NULL,3,3,'2026-04-03 11:47:59','2026-04-03 11:47:59',NULL,NULL,NULL,NULL,'pending'),
(38,'order_SZgqxG21Hh1C15',15,'A','rekha1606@gmail.com','1234567899','1','H','H','132345',240.00,'pending',NULL,NULL,NULL,'2026-04-05 05:07:21','2026-04-05 05:07:21',NULL,NULL,NULL,NULL,'pending'),
(39,'order_SZiz6qAuMgHwji',15,'A','rekha1606@gmail.com','1234567899','1','A','A','123456',240.00,'pending',NULL,NULL,NULL,'2026-04-05 07:12:27','2026-04-05 07:12:28',NULL,NULL,NULL,NULL,'pending'),
(40,'order_SZpsShG6ChUKEK',16,'Asha','asharoperia57@gmail.com','7404527003','H no 187 Suraj mal enclave sector 5 hisar haryana','Hisar','Haryana','125001',240.00,'pending',NULL,NULL,NULL,'2026-04-05 13:57:01','2026-04-05 13:57:01',NULL,NULL,NULL,NULL,'pending'),
(41,'order_SaW97Wf3kKT3Ki',1,'RAHUL DHIMAN','rahul.dhiman.mohanlal@gmail.com','8059982049','KANDELA06','Jind','Haryana','126125',1.00,'paid','pay_SaW9YUVmKX2IAW',NULL,NULL,'2026-04-07 07:17:56','2026-04-07 07:20:18','India Post','7282728','From Warehouse Dispatched ','2026-04-07 07:20:18','completed'),
(42,'order_Sav8tb2NHHc97c',15,'A','rekha1606@gmail.com','1234567899','1','H','A','123456',240.00,'pending',NULL,NULL,NULL,'2026-04-08 07:45:03','2026-04-08 07:45:03',NULL,NULL,NULL,NULL,'pending'),
(43,'order_SbTyaeCR1fWHfu',17,'Kamini Singh','kamini.kanishk@gmail.com','9416730707','#847,Sector 13','Hisar','HARYANA','125001',240.00,'paid','pay_SbTyipzHvM9ivo',NULL,NULL,'2026-04-09 17:49:34','2026-04-09 17:49:56',NULL,NULL,NULL,NULL,'pending'),
(44,'order_SgamX6zwJT5OAf',18,'Pinky Sheoran','kumaripinky59935@gmail.com','8295820658','271 deri Yojana ramliawala Bade Pipli ','Jaipur ','Rajasthan ','302013',120.00,'paid','pay_SgamxgCYpkfKut',NULL,NULL,'2026-04-22 15:44:02','2026-04-22 15:44:42',NULL,NULL,NULL,NULL,'pending');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_earnings`
--

DROP TABLE IF EXISTS `partner_earnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partner_id` int(11) NOT NULL,
  `partner_type` enum('marketing','senior') NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `partner_earnings_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_earnings`
--

LOCK TABLES `partner_earnings` WRITE;
/*!40000 ALTER TABLE `partner_earnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_earnings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `earning` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `referral_code` (`referral_code`),
  KEY `senior_partner_id` (`senior_partner_id`),
  CONSTRAINT `partners_ibfk_1` FOREIGN KEY (`senior_partner_id`) REFERENCES `senior_partners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partners`
--

LOCK TABLES `partners` WRITE;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
INSERT INTO `partners` VALUES
(3,3,'Rahul Dhiman','vyparsetu@gmail.com','8059982049','Male','uploads/partners/partner_698877ed3cafe.png','Haryana','JIND','126125','Near PNB Bank, Kandela, Jind, Haryana, 126125','$2y$12$MrnuGEDV0Nlb9DOW4HMLO.X2ruyapXyPVeqvVpwlhT0N6FlSkJ4PK','active','BEA452CF',15.00,'2026-02-08 11:47:57','2026-02-08 15:35:06',0.15,0.15),
(4,3,'Dipanshu Mehera','rd5212452@gmail.com','9996512049','Male','uploads/partners/partner_6989d3cb1621b.jpg','Haryana','Jind','126125','Near PNB Bank, Kandela, Jind, Haryana','$2y$12$ekPb5vDIZJW2gSNkd.DOmOSpYZJCfc3Dj3XC.nHuElCs4bREUCt9O','active','2F99EF34',15.00,'2026-02-09 12:32:11','2026-02-09 12:32:11',0.00,0.00),
(5,3,'Rahul','rd52124522@gmail.com','9839384292','Male','uploads/partners/partner_698a112eadb73.png','Haryana','Jind','126125','Near PNB bank, kandela','$2y$12$638uvC/hRXQ.DzXE8N66A.pl77r/yA0TUglVLvoEEQVdQdHX1Dk1a','active','11246D89',15.00,'2026-02-09 16:54:06','2026-02-09 16:54:06',0.00,0.00),
(6,3,'Rekha','sukhwantsingh16@gmail.com','8607642330','Female',NULL,'Haryana','Hisar','125006','#21, Choudhary Complex Tcp 2 Hisar cantt','$2y$12$IwOak61D7oN3.MkgJ1bKJuGSAHXH1xKPqFukpXJ0IyKjwqXtEpCQi','active','F5CC1A83',15.00,'2026-02-10 02:31:40','2026-02-10 09:47:26',0.15,0.15),
(7,3,'Soniya rani','vidhi862013@gmail.com','9991772054','Female',NULL,'Haryana','Hisar','125033','Vpo umra ,near Dharamshala ,Hansi','$2y$12$yYigammew3esy08xg2C4puiAgCVeLN6N2JRk1LyXUjPWuoD3gWp9m','active','8C7216CE',15.00,'2026-02-10 11:05:10','2026-02-10 11:05:10',0.00,0.00),
(8,3,'Ram','ram@warychary.com','9896123456','Male',NULL,'Haryana','Hisar','125006','2','$2y$12$tzlvP8Fq2tKqpr5usTqrL.ABDN0TA.DIMOqufb0k833tuEQDWJX0i','active','065DB92B',15.00,'2026-02-11 10:21:10','2026-02-11 10:21:10',0.00,0.00);
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payout_history`
--

DROP TABLE IF EXISTS `payout_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payout_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('partner','senior_partner') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT 'Bank Transfer',
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','processed','failed') DEFAULT 'processed',
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`,`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payout_history`
--

LOCK TABLES `payout_history` WRITE;
/*!40000 ALTER TABLE `payout_history` DISABLE KEYS */;
INSERT INTO `payout_history` VALUES
(1,3,'partner',0.60,'Bank Transfer','radst-asha-ja','processed','Payments Will send to the bank transfter ','2026-02-09 12:17:33'),
(2,3,'senior_partner',0.02,'Bank Transfer','','processed','Thankyou for choosing warychary','2026-02-09 12:25:13');
/*!40000 ALTER TABLE `payout_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(2,'Warychary Pack Of 30 ','warychary-sanitary-pad-pack-of-30-','uploads/products/1771219153_IMG_9239.JPG','[\"uploads\\/products\\/1771219946_gal_0_IMG_9238.JPG\",\"uploads\\/products\\/1771219946_gal_1_IMG_9237.JPG\",\"uploads\\/products\\/1771219946_gal_2_IMG_9236.JPG\"]','Warychary Sanitary Pad (Pack of 20) offers reliable protection, high absorbency, and all-day comfort. Designed with a soft, breathable top layer and secure wings to keep you dry, fresh, and confident during your period.','<p><strong data-start=\"137\" data-end=\"176\">Warychary Sanitary Pad (Pack of 20)</strong> offers reliable protection, high absorbency, and all-day comfort. Designed with a soft, breathable top layer and secure wings to keep you dry, fresh, and confident during your period.</p>',360.00,200.00,360.00,0.00,0.00,200.00,1,'Double Panty','uploads/products/1771219206_free_IMG_9256.JPG','active','2026-02-08 06:02:04','2026-02-16 05:32:26'),
(3,'Warychary Pack Of 20 ','warychary-sanitary-pad-pack-of-20-','uploads/products/1771219116_IMG_9238.JPG','[\"uploads\\/products\\/1771219909_gal_0_IMG_9239.JPG\",\"uploads\\/products\\/1771219909_gal_1_IMG_9237.JPG\",\"uploads\\/products\\/1771219909_gal_2_IMG_9236.JPG\"]','','<p><span style=\"font-weight: bolder; color: rgb(60, 67, 74); font-family: Inter, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif;\">Warychary Sanitary Pad ( Pack Of 30 )</span></p>',240.00,100.00,240.00,62.00,10.00,172.00,1,'panty',NULL,'active','2026-02-09 18:15:49','2026-04-07 07:19:38');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `razorpay_settings`
--

DROP TABLE IF EXISTS `razorpay_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `razorpay_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_id` varchar(255) NOT NULL,
  `key_secret` varchar(255) NOT NULL,
  `webhook_secret` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'INR',
  `mode` enum('test','live') DEFAULT 'test',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `razorpay_settings`
--

LOCK TABLES `razorpay_settings` WRITE;
/*!40000 ALTER TABLE `razorpay_settings` DISABLE KEYS */;
INSERT INTO `razorpay_settings` VALUES
(1,'rzp_live_SBaWkVJiaA14zd','IahTfrA8iQ5ioZektXj1pxG4','','INR','live','2026-02-08 08:22:31','2026-02-08 08:53:42');
/*!40000 ALTER TABLE `razorpay_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `razorpay_transactions`
--

DROP TABLE IF EXISTS `razorpay_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `razorpay_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(255) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `razorpay_transactions`
--

LOCK TABLES `razorpay_transactions` WRITE;
/*!40000 ALTER TABLE `razorpay_transactions` DISABLE KEYS */;
INSERT INTO `razorpay_transactions` VALUES
(1,'order_SDboWKZaJBmG1D','pay_SDbpAKxgiSkHcF',1.00,'captured',NULL,NULL,'2026-02-08 09:54:25'),
(2,'order_SDc51OKkeP8iiE','pay_SDc5HNwrB2MPVQ',1.00,'captured',NULL,NULL,'2026-02-08 10:09:40'),
(3,'order_SDcKdbH9MUJnJN','pay_SDcL0ZOsUAt9l0',1.00,'captured',NULL,NULL,'2026-02-08 10:24:33'),
(4,'order_SDcNT1gDErivhY','pay_SDcNncS0xxsDbw',1.00,'captured',NULL,NULL,'2026-02-08 10:27:11'),
(5,'order_SDcY3LEro3r8kk','pay_SDcYQ7PIY3YpNP',1.00,'captured',NULL,NULL,'2026-02-08 10:37:17'),
(6,'order_SDchRgMF5buWPp','pay_SDchl39DDMmd5I',1.00,'captured',NULL,NULL,'2026-02-08 10:46:05'),
(7,'order_SDcykI2645svhm','pay_SDcywL26ZgnqiR',1.00,'captured',NULL,NULL,'2026-02-08 11:02:22'),
(8,'order_SDdCIJYlDcuNHQ','pay_SDdChA606sruIO',1.00,'captured',NULL,NULL,'2026-02-08 11:15:23'),
(9,'order_SDhcNoiuqE120G','pay_SDhd0cfxj0IGeu',1.00,'captured',NULL,NULL,'2026-02-08 15:35:06'),
(10,'order_SEOG5IK1FjqKIc','pay_SEOGCt0Ex9RD70',1.00,'captured',NULL,NULL,'2026-02-10 09:17:32'),
(11,'order_SEOlpbICWInHPq','pay_SEOlwTfMTyQpwC',1.00,'captured',NULL,NULL,'2026-02-10 09:47:26'),
(12,'order_SFffclLeCnhNm1','pay_SFffoFEolA91LA',240.00,'captured',NULL,NULL,'2026-02-13 14:58:36'),
(13,'order_SMN3rwdNfhsSk3','pay_SMN42VVUgejpv2',240.00,'captured',NULL,NULL,'2026-03-02 13:19:22'),
(14,'order_SMlNSm3JcjGyFo','pay_SMlQdrGQP6WPJs',1.00,'captured',NULL,NULL,'2026-03-03 13:09:10'),
(15,'order_SOlxaTs33vj8ne','pay_SOly8SThkbrpRi',240.00,'captured',NULL,NULL,'2026-03-08 14:59:29'),
(16,'order_SaW97Wf3kKT3Ki','pay_SaW9YUVmKX2IAW',1.00,'captured',NULL,NULL,'2026-04-07 07:18:23'),
(17,'order_SbTyaeCR1fWHfu','pay_SbTyipzHvM9ivo',240.00,'captured',NULL,NULL,'2026-04-09 17:49:56'),
(18,'order_SgamX6zwJT5OAf','pay_SgamxgCYpkfKut',120.00,'captured',NULL,NULL,'2026-04-22 15:44:42');
/*!40000 ALTER TABLE `razorpay_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `review_images` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES
(1,2,'Rahul','rahul.dhiman.mohanlal@gmail.com',5,'The greatest product i have seen here','[\"uploads\\/reviews\\/69883ffcf249c.jpeg\"]','approved','2026-02-08 07:49:16'),
(3,3,'ex1zl0b5o','nil8asyoi@6f540.com',1,'dhgrxn','[]','approved','2026-04-26 20:20:16'),
(4,3,'jib3kvdh2','4d2bemjjt@sankh.com',1,'dhgrxn','[]','approved','2026-04-26 20:20:24'),
(5,3,'cx9th7q0n','46lhrngcl@71eqk.com',1,'dhgrxn','[]','approved','2026-04-26 20:20:32'),
(6,3,'tawllgedx','fqbl089fm@to4wp.com',1,'dhgrxn','[\"uploads\\/reviews\\/69ee7391ac4e9.jpg\"]','approved','2026-04-26 20:20:33'),
(7,3,'6g6gbj9qr','6c06ogre2@d7odb.com',1,'dhgrxn','[\"uploads\\/reviews\\/69ee739ab772e.png\"]','approved','2026-04-26 20:20:42'),
(8,3,'kxvgln2nw','25lkzvtmr@njm8j.com',1,'dhgrxn','[\"uploads\\/reviews\\/69ee73a2ea607.gif\"]','approved','2026-04-26 20:20:50'),
(9,3,'5tdz62813','wwnx1qxq1@mv7eb.com',1,'dhgrxn','[]','approved','2026-04-26 20:20:59'),
(10,3,'62qpw64du','i3xfzi8ae@cbdci.com',1,'dhgrxn','[\"uploads\\/reviews\\/69ee73b3c5ec5.jpg\"]','approved','2026-04-26 20:21:07'),
(11,2,'o7sqfnclm','o4mgdg153@715gq.com',1,'z2tcge','[]','approved','2026-04-26 20:21:18'),
(12,2,'63rhabgh3','byjmqavyc@1cul7.com',1,'z2tcge','[]','approved','2026-04-26 20:21:26'),
(13,2,'ipntv0q0u','7bet84g1d@1hzpq.com',1,'z2tcge','[]','approved','2026-04-26 20:21:34'),
(14,2,'dey5qe1ew','kni8r0w35@kdif6.com',1,'z2tcge','[\"uploads\\/reviews\\/69ee73cf84564.jpg\"]','approved','2026-04-26 20:21:35'),
(15,2,'r0qbksbiz','atlubpd2w@vda2n.com',1,'z2tcge','[\"uploads\\/reviews\\/69ee73d82c949.png\"]','approved','2026-04-26 20:21:44'),
(16,2,'2p2ew65dj','99dovdc10@mjupa.com',1,'z2tcge','[\"uploads\\/reviews\\/69ee73e113530.gif\"]','approved','2026-04-26 20:21:53'),
(17,2,'aoepku1of','2xqfyk63q@hs8y8.com',1,'z2tcge','[]','approved','2026-04-26 20:22:02'),
(18,2,'0p3ij2hj4','kr32ulnkf@sia30.com',1,'z2tcge','[\"uploads\\/reviews\\/69ee73f571a28.jpg\"]','approved','2026-04-26 20:22:13');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `senior_partner_earnings`
--

DROP TABLE IF EXISTS `senior_partner_earnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `senior_partner_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `senior_partner_id` int(11) NOT NULL,
  `source_partner_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 2.00,
  `status` enum('pending','paid') DEFAULT 'pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `senior_partner_earnings`
--

LOCK TABLES `senior_partner_earnings` WRITE;
/*!40000 ALTER TABLE `senior_partner_earnings` DISABLE KEYS */;
INSERT INTO `senior_partner_earnings` VALUES
(1,3,3,21,0.02,2.00,'pending','Override Commission for Order #21','2026-02-08 15:35:06'),
(2,3,6,25,0.02,2.00,'pending','Override Commission for Order #25','2026-02-10 09:47:26');
/*!40000 ALTER TABLE `senior_partner_earnings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `senior_partners`
--

DROP TABLE IF EXISTS `senior_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `senior_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `earning` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `referral_code` (`referral_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `senior_partners`
--

LOCK TABLES `senior_partners` WRITE;
/*!40000 ALTER TABLE `senior_partners` DISABLE KEYS */;
INSERT INTO `senior_partners` VALUES
(3,'Rahul','rahul.dhiman.mohanlal@gmail.com','7015587488','uploads/partners/partner_3_1770640133.png','Male','Haryana','jind','126125','Near PNB Bank, Kandela, Jind, Haryana','$2y$12$YTxOt1U5KqFivUiB6wqCd.3ZMOuKNHm1qx1/p1TYXMHPlWP3fMTSy','ICUWJZ',2.00,'active','2026-02-08 11:46:51','2026-02-10 09:47:26',0.04,0.04),
(4,'Sandeep ','sandeeppawar5286169@gmail.com','8295329457','','Male','Haryana ','Hisar ','125121','S/o Vijay Singh, Vill.- Rajli,te','$2y$12$lpPPgnJmUsblahI57OL.RuJMZH/RZ9/O1sAmsKtBb.D.dPDdmenEm','HDADCC',2.00,'active','2026-02-09 15:40:05','2026-02-09 15:40:05',0.00,0.00),
(5,'Rahul','rd5212452@gmail.com','8059982649','assets/uploads/partners/sp_1770653599_698a079f6beba.jpg','Male','Haryana','Jind','126125','KANDELA06','$2y$12$NbjXXscB44UFafK..DZsJetLrF7VCfJjjzd/9/S3myM21roCOpahS','NLWWIM',2.00,'active','2026-02-09 16:13:19','2026-02-09 16:13:19',0.00,0.00),
(6,'Sonu Singh','0007sonusingh@gmail.com','9555036972','','Male','Delhi','South West Delhi','110043','S/O Rattan Singh, Plot No. 103/A,Sangam Vihar, Najafgarh','$2y$12$hlVoHhXXVDLTWGi37eTF/ehTTHxQyKBtEmet9vfEMJWwdK/sxRD5O','EACVVO',2.00,'active','2026-02-11 15:14:35','2026-02-11 15:14:35',0.00,0.00),
(7,'Kiran','kiran25794@gmail.com','9149373174','','Female','Uttar Pradesh','Etawah','206001','W/O Sonu, Shiv Nagar, Adda Jalim, Etawah','$2y$12$AhajsXedopC9ac.jmsNkGOB/Ea2Qxw1jSGs0j3oj9y9WGtOnmKCUe','BXTMVR',2.00,'active','2026-02-11 15:38:17','2026-02-11 15:38:17',0.00,0.00),
(8,'RAHUL DHIMAN','ecemanager.online@gmail.com','7015570155','assets/uploads/partners/sp_1770827611_698caf5b630c8.jpg','Male','Haryana','Jind','126125','KANDELA06','$2y$12$6iKUybt.yT7P/wmAeZIyKO5y0GnpUhQ6oK5oF3fXaPTNVVSy3v4a.','XHJFMD',2.00,'active','2026-02-11 16:33:31','2026-02-11 16:33:31',0.00,0.00);
/*!40000 ALTER TABLE `senior_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smtp_settings`
--

DROP TABLE IF EXISTS `smtp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smtp_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `encryption` enum('tls','ssl','none') DEFAULT 'tls',
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smtp_settings`
--

LOCK TABLES `smtp_settings` WRITE;
/*!40000 ALTER TABLE `smtp_settings` DISABLE KEYS */;
INSERT INTO `smtp_settings` VALUES
(1,'mail.warychary.com','info@warychary.com','Rd14072003@./',465,'ssl','info@warychary.com','Warychary Care ','2026-02-07 09:22:40');
/*!40000 ALTER TABLE `smtp_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,NULL,'Rahul','rahul.dhiman.mohanlal@gmail.com','8059982049','Male','assets/uploads/users/698873ab0b962.png','Haryana','JIND','126102','VPO KANDELA\r\nVPO KANDELA NEAR PNB BANK','$2y$12$YNoCigeoJGw.e3WZWlfKSuTUI66xnTOmy9/lF9QitMnQGEZwKlnLa','active','2026-02-07 19:30:03','2026-02-08 11:29:47'),
(2,NULL,'Rahul Dhiman','vyparsetu@gmail.com','8059982049','Male',NULL,'Haryana','Jind','126125','Near PNB Bank, Kandela, Jind, Haryana, 126125','','active','2026-02-08 09:53:33','2026-02-08 09:53:33'),
(3,3,'Rahul Dhiman','jhdindustrialsolution@gmail.com','9466939049','Male','assets/uploads/users/6988787f8a9f2.png','Haryana','jind','126125','kandela','$2y$12$oxoFL.5EReerfkNwsLuSkeIcdxYnrdjcWXWwKVn83ZC4jakJbX6ju','active','2026-02-08 11:50:23','2026-02-08 11:50:23'),
(4,3,'Rahul Dhiman','rd5212452@gmail.com','9898989898','Male','assets/uploads/users/6989d568a4f5b.png','haryana','Jind','126125','Near Pnb Bank, kandela, Jind, Haryana','$2y$12$fl2wo9Syw7jX6P5PlSNze.YR1ksdO05k4GiEDpskI.tk1.nFXFtPK','active','2026-02-09 12:39:04','2026-02-09 12:39:04'),
(5,6,'Rekha','sukhwantsingh16@gmail.com','8607642330','Male',NULL,'Haryana','Hisar','125006','Hisar cantt','$2y$12$P5litqxmURdaY/n3UBBL.OYlQm8ZNXXXIRsgmP2xd8NtMhEYXKHFi','active','2026-02-10 09:16:56','2026-02-10 09:29:53'),
(6,4,'satbir singh','satbirsinghdighana@gmail.com','3164346431','Male','','Haryana','jind','126125','jind','$2y$12$C9y5rYRqlnWntpMcEDA59.5AEdJw5ttY7KkFPT2P.F1KtBrFWHVQG','active','2026-02-10 09:29:59','2026-02-10 09:29:59'),
(7,8,'Ram','ram@warychary.com','9896123456','Male','','Haryana','Hisar','125006','2','$2y$12$RPFqSqagfd6zQIksvQaHTuie2WdHed/RiYpBna8CdDsrqVNNcWWX.','active','2026-02-11 10:30:32','2026-02-11 10:30:32'),
(8,NULL,'Sheetal kumari ','kumari.shetal36@gmail.com','8374715092','Male',NULL,'Haryana','Gurugram ','122011','C615, Jalvayu Towers, Sec 56','','active','2026-02-13 14:57:54','2026-02-13 14:57:54'),
(9,NULL,'Manoj Kuhar','kuharmanoj@gmail.com','9999828073','Male',NULL,'Haryana ','Gurgaon ','122001','Saffron enclave pvt ltd \r\n415/2,MG ROAD SECTOR-14 GURGAON \r\n122001','','active','2026-02-22 06:29:48','2026-02-22 06:29:48'),
(10,NULL,'Tanvi Rastogi ','rastogi.tanvi7@gmail.com','9871192228','Male',NULL,'Haryana ','Hisar','125044','48/49, Master Colony \r\nNear Hisar Hyundai \r\nDelhi By pass road','','active','2026-02-27 14:28:21','2026-02-27 14:28:21'),
(11,NULL,'Varsha Gupta ','varshagupta437@gmail.com','9255646333','Male',NULL,'Haryana ','Hisar','125001','House no 750\r\nUrban Estate,Near Ram Dwar ','','active','2026-03-02 09:50:07','2026-03-02 09:50:07'),
(12,NULL,'sangita kumari','kumarisangita01205@gmail.com','7015926435','Male',NULL,'Haryana ','Hisar ','125001','H no 30 chodhary complex TCP 2 hisar cant','','active','2026-03-02 13:18:41','2026-03-02 13:18:41'),
(13,NULL,'Jaiti Arora','jaiti.arora@gmail.com','9215022229','Male',NULL,'Haryana','hisar','125001','H no.378 ,old pla behind town park,hisar (haryana )','','active','2026-03-08 14:58:06','2026-03-08 14:58:06'),
(14,NULL,'Rajnesh Kumar','divineenergym@gmail.com','7015340472','Male',NULL,'INDIA','BAHADURGARH','124507','C/O RUKMANI TOWER FIRST, FLOOR OLD NAJAFGARH ROAD, Bahadurgarh,','','active','2026-03-25 08:46:24','2026-03-25 08:46:24'),
(15,NULL,'A','rekha1606@gmail.com','1234567899','Other',NULL,'H','H','132345','1','$2y$10$pEnpc18RDmWMbDpMjidU8.8O4xmf0zxJ5HfJRFzqHpQKUEa9Th0Sm','active','2026-04-05 05:07:21','2026-04-05 05:07:21'),
(16,NULL,'Asha','asharoperia57@gmail.com','7404527003','Other',NULL,'Haryana','Hisar','125001','H no 187 Suraj mal enclave sector 5 hisar haryana','$2y$10$4q9KbYKj686NWLd.U./fdeO8fQ1pVRuTGVdRes9Pmv2xqvvNLPT8i','active','2026-04-05 13:57:01','2026-04-05 13:57:01'),
(17,NULL,'Kamini Singh','kamini.kanishk@gmail.com','9416730707','Other',NULL,'HARYANA','Hisar','125001','#847,Sector 13','$2y$10$PT3eMQb0/.iLAfUIkeUqN.gCtklq8IP86sBtErdI1/sMdXBdGu6CS','active','2026-04-09 17:49:34','2026-04-09 17:49:34'),
(18,NULL,'Pinky Sheoran','kumaripinky59935@gmail.com','8295820658','Other',NULL,'Rajasthan ','Jaipur ','302013','271 deri Yojana ramliawala Bade Pipli ','$2y$10$oS2UiUGmjGxyY/gQ86KUtuu0a40wp7gSf1owK6H2hRzJvudCyEal6','active','2026-04-22 15:44:02','2026-04-22 15:44:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'jhdindus_warycharycare'
--

--
-- Dumping routines for database 'jhdindus_warycharycare'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30  2:40:33
