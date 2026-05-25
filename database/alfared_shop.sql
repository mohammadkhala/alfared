-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: alfared_shop
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT 'الرئيسي',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `delivery_zone_id` bigint(20) unsigned DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `area` varchar(255) DEFAULT NULL,
  `address_line` text NOT NULL,
  `building` varchar(255) DEFAULT NULL,
  `floor` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_foreign` (`user_id`),
  KEY `addresses_delivery_zone_id_foreign` (`delivery_zone_id`),
  CONSTRAINT `addresses_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title_ar` varchar(255) NOT NULL,
  `subtitle_ar` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `button_text_ar` varchar(255) DEFAULT NULL,
  `badge_text` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL DEFAULT 'hero',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'L\'Oréal','loreal',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(2,'Maybelline','maybelline',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(3,'NYX','nyx',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(4,'MAC','mac',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(5,'Nivea','nivea',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(6,'Garnier','garnier',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(7,'Pantene','pantene',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(8,'Head & Shoulders','head-shoulders',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(9,'Dove','dove',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(10,'Revlon','revlon',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(11,'OPI','opi',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(12,'Schwarzkopf','schwarzkopf',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(13,'Essence','essence',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(14,'Flormar','flormar',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(15,'Rimmel','rimmel',NULL,NULL,1,0,'2026-05-22 04:18:33','2026-05-22 04:18:33');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('abnaaa-alfryd-cache-4d7d1061b9b186bc723404b8ace3848e54ce60bd','i:4;',1779707658),('abnaaa-alfryd-cache-4d7d1061b9b186bc723404b8ace3848e54ce60bd:timer','i:1779707658;',1779707658),('abnaaa-alfryd-cache-5c785c036466adea360111aa28563bfd556b5fba','i:1;',1779707222),('abnaaa-alfryd-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1779707222;',1779707222),('abnaaa-alfryd-cache-api:home','a:6:{s:7:\"banners\";a:0:{}s:10:\"categories\";a:8:{i:0;a:7:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:7:\"name_en\";s:6:\"Makeup\";s:7:\"name_he\";s:10:\"איפור\";s:4:\"slug\";s:6:\"makeup\";s:4:\"icon\";s:4:\"💄\";s:5:\"image\";N;}i:1;a:7:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:7:\"name_en\";s:8:\"Skincare\";s:7:\"name_he\";s:17:\"טיפוח עור\";s:4:\"slug\";s:8:\"skincare\";s:4:\"icon\";s:4:\"🧴\";s:5:\"image\";N;}i:2;a:7:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"العناية بالشعر\";s:7:\"name_en\";s:9:\"Hair Care\";s:7:\"name_he\";s:19:\"טיפוח שיער\";s:4:\"slug\";s:4:\"hair\";s:4:\"icon\";s:4:\"💆\";s:5:\"image\";N;}i:3;a:7:{s:2:\"id\";i:4;s:4:\"name\";s:12:\"العطور\";s:7:\"name_en\";s:8:\"Perfumes\";s:7:\"name_he\";s:10:\"בשמים\";s:4:\"slug\";s:7:\"perfume\";s:4:\"icon\";s:4:\"🌸\";s:5:\"image\";N;}i:4;a:7:{s:2:\"id\";i:5;s:4:\"name\";s:31:\"العناية بالأظافر\";s:7:\"name_en\";s:9:\"Nail Care\";s:7:\"name_he\";s:29:\"טיפוח ציפורניים\";s:4:\"slug\";s:5:\"nails\";s:4:\"icon\";s:4:\"💅\";s:5:\"image\";N;}i:5;a:7:{s:2:\"id\";i:6;s:4:\"name\";s:31:\"الأجهزة والأدوات\";s:7:\"name_en\";s:7:\"Devices\";s:7:\"name_he\";s:25:\"מכשירים וכלים\";s:4:\"slug\";s:7:\"devices\";s:4:\"icon\";s:4:\"🪭\";s:5:\"image\";N;}i:6;a:7:{s:2:\"id\";i:7;s:4:\"name\";s:27:\"العناية بالجسم\";s:7:\"name_en\";s:9:\"Body Care\";s:7:\"name_he\";s:17:\"טיפוח גוף\";s:4:\"slug\";s:4:\"body\";s:4:\"icon\";s:4:\"🛁\";s:5:\"image\";N;}i:7;a:7:{s:2:\"id\";i:8;s:4:\"name\";s:27:\"منتجات الأطفال\";s:7:\"name_en\";s:4:\"Baby\";s:7:\"name_he\";s:25:\"מוצרי תינוקות\";s:4:\"slug\";s:4:\"baby\";s:4:\"icon\";s:4:\"👶\";s:5:\"image\";N;}}s:8:\"featured\";a:8:{i:0;a:18:{s:2:\"id\";i:7;s:4:\"name\";s:47:\"كريم نيفيا الترطيب اليومي\";s:7:\"name_en\";s:23:\"Nivea Daily Moisturizer\";s:7:\"name_he\";s:23:\"Nivea Daily Moisturizer\";s:4:\"slug\";s:27:\"nivea-daily-moisturizer-447\";s:5:\"image\";N;s:5:\"price\";d:29;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.8;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:5:\"Nivea\";s:4:\"slug\";s:5:\"nivea\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:78;s:9:\"low_stock\";b:0;}i:1;a:18:{s:2:\"id\";i:16;s:4:\"name\";s:34:\"طلاء أظافر فلورمار\";s:7:\"name_en\";s:19:\"Flormar Nail Polish\";s:7:\"name_he\";s:19:\"Flormar Nail Polish\";s:4:\"slug\";s:23:\"flormar-nail-polish-836\";s:5:\"image\";N;s:5:\"price\";d:18;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.2;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:31:\"العناية بالأظافر\";s:4:\"slug\";s:5:\"nails\";}s:5:\"brand\";a:3:{s:2:\"id\";i:14;s:4:\"name\";s:7:\"Flormar\";s:4:\"slug\";s:7:\"flormar\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:120;s:9:\"low_stock\";b:0;}i:2;a:18:{s:2:\"id\";i:11;s:4:\"name\";s:47:\"شامبو بانتين للشعر التالف\";s:7:\"name_en\";s:28:\"Pantene Damaged Hair Shampoo\";s:7:\"name_he\";s:28:\"Pantene Damaged Hair Shampoo\";s:4:\"slug\";s:32:\"pantene-damaged-hair-shampoo-997\";s:5:\"image\";N;s:5:\"price\";d:25;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"العناية بالشعر\";s:4:\"slug\";s:4:\"hair\";}s:5:\"brand\";a:3:{s:2:\"id\";i:7;s:4:\"name\";s:7:\"Pantene\";s:4:\"slug\";s:7:\"pantene\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:90;s:9:\"low_stock\";b:0;}i:3;a:18:{s:2:\"id\";i:9;s:4:\"name\";s:26:\"غسول وجه نيفيا\";s:7:\"name_en\";s:15:\"Nivea Face Wash\";s:7:\"name_he\";s:15:\"Nivea Face Wash\";s:4:\"slug\";s:19:\"nivea-face-wash-699\";s:5:\"image\";N;s:5:\"price\";d:22;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.4;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:5:\"Nivea\";s:4:\"slug\";s:5:\"nivea\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:100;s:9:\"low_stock\";b:0;}i:4;a:18:{s:2:\"id\";i:4;s:4:\"name\";s:34:\"كحل مايبلين العيون\";s:7:\"name_en\";s:20:\"Maybelline Eye Liner\";s:7:\"name_he\";s:20:\"Maybelline Eye Liner\";s:4:\"slug\";s:24:\"maybelline-eye-liner-255\";s:5:\"image\";N;s:5:\"price\";d:28;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.6;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:10:\"Maybelline\";s:4:\"slug\";s:10:\"maybelline\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:58;s:9:\"low_stock\";b:0;}i:5;a:18:{s:2:\"id\";i:17;s:4:\"name\";s:28:\"طلاء جل للأظافر\";s:7:\"name_en\";s:15:\"Gel Nail Polish\";s:7:\"name_he\";s:15:\"Gel Nail Polish\";s:4:\"slug\";s:19:\"gel-nail-polish-377\";s:5:\"image\";N;s:5:\"price\";d:25;s:13:\"compare_price\";d:32;s:16:\"discount_percent\";i:22;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:31:\"العناية بالأظافر\";s:4:\"slug\";s:5:\"nails\";}s:5:\"brand\";N;s:17:\"short_description\";N;s:14:\"stock_quantity\";i:75;s:9:\"low_stock\";b:0;}i:6;a:18:{s:2:\"id\";i:1;s:4:\"name\";s:39:\"أحمر شفاه ميبلين ماتي\";s:7:\"name_en\";s:25:\"Maybelline Matte Lipstick\";s:7:\"name_he\";s:25:\"Maybelline Matte Lipstick\";s:4:\"slug\";s:29:\"maybelline-matte-lipstick-622\";s:5:\"image\";N;s:5:\"price\";d:35;s:13:\"compare_price\";d:45;s:16:\"discount_percent\";i:22;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:10:\"Maybelline\";s:4:\"slug\";s:10:\"maybelline\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:40;s:9:\"low_stock\";b:0;}i:7;a:18:{s:2:\"id\";i:12;s:4:\"name\";s:45:\"بلسم غارنييه للشعر الجاف\";s:7:\"name_en\";s:28:\"Garnier Conditioner Dry Hair\";s:7:\"name_he\";s:28:\"Garnier Conditioner Dry Hair\";s:4:\"slug\";s:32:\"garnier-conditioner-dry-hair-563\";s:5:\"image\";N;s:5:\"price\";d:27;s:13:\"compare_price\";d:35;s:16:\"discount_percent\";i:23;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.3;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"العناية بالشعر\";s:4:\"slug\";s:4:\"hair\";}s:5:\"brand\";a:3:{s:2:\"id\";i:6;s:4:\"name\";s:7:\"Garnier\";s:4:\"slug\";s:7:\"garnier\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:55;s:9:\"low_stock\";b:0;}}s:11:\"newArrivals\";a:8:{i:0;a:18:{s:2:\"id\";i:1;s:4:\"name\";s:39:\"أحمر شفاه ميبلين ماتي\";s:7:\"name_en\";s:25:\"Maybelline Matte Lipstick\";s:7:\"name_he\";s:25:\"Maybelline Matte Lipstick\";s:4:\"slug\";s:29:\"maybelline-matte-lipstick-622\";s:5:\"image\";N;s:5:\"price\";d:35;s:13:\"compare_price\";d:45;s:16:\"discount_percent\";i:22;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:10:\"Maybelline\";s:4:\"slug\";s:10:\"maybelline\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:40;s:9:\"low_stock\";b:0;}i:1;a:18:{s:2:\"id\";i:2;s:4:\"name\";s:28:\"مسكارا L\'Oréal Volume\";s:7:\"name_en\";s:23:\"L\'Oréal Volume Mascara\";s:7:\"name_he\";s:23:\"L\'Oréal Volume Mascara\";s:4:\"slug\";s:25:\"loreal-volume-mascara-532\";s:5:\"image\";N;s:5:\"price\";d:42;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.7;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:8:\"L\'Oréal\";s:4:\"slug\";s:6:\"loreal\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:31;s:9:\"low_stock\";b:0;}i:2;a:18:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"بودرة NYX HD Finishing\";s:7:\"name_en\";s:23:\"NYX HD Finishing Powder\";s:7:\"name_he\";s:23:\"NYX HD Finishing Powder\";s:4:\"slug\";s:27:\"nyx-hd-finishing-powder-664\";s:5:\"image\";N;s:5:\"price\";d:55;s:13:\"compare_price\";d:70;s:16:\"discount_percent\";i:21;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.3;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:3:\"NYX\";s:4:\"slug\";s:3:\"nyx\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:28;s:9:\"low_stock\";b:0;}i:3;a:18:{s:2:\"id\";i:4;s:4:\"name\";s:34:\"كحل مايبلين العيون\";s:7:\"name_en\";s:20:\"Maybelline Eye Liner\";s:7:\"name_he\";s:20:\"Maybelline Eye Liner\";s:4:\"slug\";s:24:\"maybelline-eye-liner-255\";s:5:\"image\";N;s:5:\"price\";d:28;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.6;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:10:\"Maybelline\";s:4:\"slug\";s:10:\"maybelline\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:58;s:9:\"low_stock\";b:0;}i:4;a:18:{s:2:\"id\";i:5;s:4:\"name\";s:29:\"فاونديشن إيسينس\";s:7:\"name_en\";s:18:\"Essence Foundation\";s:7:\"name_he\";s:18:\"Essence Foundation\";s:4:\"slug\";s:22:\"essence-foundation-651\";s:5:\"image\";N;s:5:\"price\";d:38;s:13:\"compare_price\";d:48;s:16:\"discount_percent\";i:21;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.2;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:13;s:4:\"name\";s:7:\"Essence\";s:4:\"slug\";s:7:\"essence\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:45;s:9:\"low_stock\";b:0;}i:5;a:18:{s:2:\"id\";i:6;s:4:\"name\";s:25:\"بلاشر فلورمار\";s:7:\"name_en\";s:13:\"Flormar Blush\";s:7:\"name_he\";s:13:\"Flormar Blush\";s:4:\"slug\";s:17:\"flormar-blush-489\";s:5:\"image\";N;s:5:\"price\";d:32;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:14;s:4:\"name\";s:7:\"Flormar\";s:4:\"slug\";s:7:\"flormar\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:40;s:9:\"low_stock\";b:0;}i:6;a:18:{s:2:\"id\";i:7;s:4:\"name\";s:47:\"كريم نيفيا الترطيب اليومي\";s:7:\"name_en\";s:23:\"Nivea Daily Moisturizer\";s:7:\"name_he\";s:23:\"Nivea Daily Moisturizer\";s:4:\"slug\";s:27:\"nivea-daily-moisturizer-447\";s:5:\"image\";N;s:5:\"price\";d:29;s:13:\"compare_price\";N;s:16:\"discount_percent\";N;s:10:\"is_on_sale\";b:0;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.8;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:5:\"Nivea\";s:4:\"slug\";s:5:\"nivea\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:78;s:9:\"low_stock\";b:0;}i:7;a:18:{s:2:\"id\";i:8;s:4:\"name\";s:47:\"سيروم فيتامين C من غارنييه\";s:7:\"name_en\";s:23:\"Garnier Vitamin C Serum\";s:7:\"name_he\";s:23:\"Garnier Vitamin C Serum\";s:4:\"slug\";s:27:\"garnier-vitamin-c-serum-708\";s:5:\"image\";N;s:5:\"price\";d:65;s:13:\"compare_price\";d:80;s:16:\"discount_percent\";i:19;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.6;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:6;s:4:\"name\";s:7:\"Garnier\";s:4:\"slug\";s:7:\"garnier\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:25;s:9:\"low_stock\";b:0;}}s:6:\"offers\";a:9:{i:0;a:18:{s:2:\"id\";i:17;s:4:\"name\";s:28:\"طلاء جل للأظافر\";s:7:\"name_en\";s:15:\"Gel Nail Polish\";s:7:\"name_he\";s:15:\"Gel Nail Polish\";s:4:\"slug\";s:19:\"gel-nail-polish-377\";s:5:\"image\";N;s:5:\"price\";d:25;s:13:\"compare_price\";d:32;s:16:\"discount_percent\";i:22;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:5;s:4:\"name\";s:31:\"العناية بالأظافر\";s:4:\"slug\";s:5:\"nails\";}s:5:\"brand\";N;s:17:\"short_description\";N;s:14:\"stock_quantity\";i:75;s:9:\"low_stock\";b:0;}i:1;a:18:{s:2:\"id\";i:1;s:4:\"name\";s:39:\"أحمر شفاه ميبلين ماتي\";s:7:\"name_en\";s:25:\"Maybelline Matte Lipstick\";s:7:\"name_he\";s:25:\"Maybelline Matte Lipstick\";s:4:\"slug\";s:29:\"maybelline-matte-lipstick-622\";s:5:\"image\";N;s:5:\"price\";d:35;s:13:\"compare_price\";d:45;s:16:\"discount_percent\";i:22;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.5;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:10:\"Maybelline\";s:4:\"slug\";s:10:\"maybelline\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:40;s:9:\"low_stock\";b:0;}i:2;a:18:{s:2:\"id\";i:12;s:4:\"name\";s:45:\"بلسم غارنييه للشعر الجاف\";s:7:\"name_en\";s:28:\"Garnier Conditioner Dry Hair\";s:7:\"name_he\";s:28:\"Garnier Conditioner Dry Hair\";s:4:\"slug\";s:32:\"garnier-conditioner-dry-hair-563\";s:5:\"image\";N;s:5:\"price\";d:27;s:13:\"compare_price\";d:35;s:16:\"discount_percent\";i:23;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.3;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"العناية بالشعر\";s:4:\"slug\";s:4:\"hair\";}s:5:\"brand\";a:3:{s:2:\"id\";i:6;s:4:\"name\";s:7:\"Garnier\";s:4:\"slug\";s:7:\"garnier\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:55;s:9:\"low_stock\";b:0;}i:3;a:18:{s:2:\"id\";i:8;s:4:\"name\";s:47:\"سيروم فيتامين C من غارنييه\";s:7:\"name_en\";s:23:\"Garnier Vitamin C Serum\";s:7:\"name_he\";s:23:\"Garnier Vitamin C Serum\";s:4:\"slug\";s:27:\"garnier-vitamin-c-serum-708\";s:5:\"image\";N;s:5:\"price\";d:65;s:13:\"compare_price\";d:80;s:16:\"discount_percent\";i:19;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.6;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:6;s:4:\"name\";s:7:\"Garnier\";s:4:\"slug\";s:7:\"garnier\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:25;s:9:\"low_stock\";b:0;}i:4;a:18:{s:2:\"id\";i:13;s:4:\"name\";s:30:\"زيت الشعر لوريال\";s:7:\"name_en\";s:17:\"L\'Oréal Hair Oil\";s:7:\"name_he\";s:17:\"L\'Oréal Hair Oil\";s:4:\"slug\";s:19:\"loreal-hair-oil-408\";s:5:\"image\";N;s:5:\"price\";d:48;s:13:\"compare_price\";d:60;s:16:\"discount_percent\";i:20;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.6;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"العناية بالشعر\";s:4:\"slug\";s:4:\"hair\";}s:5:\"brand\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:8:\"L\'Oréal\";s:4:\"slug\";s:6:\"loreal\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:30;s:9:\"low_stock\";b:0;}i:5;a:18:{s:2:\"id\";i:5;s:4:\"name\";s:29:\"فاونديشن إيسينس\";s:7:\"name_en\";s:18:\"Essence Foundation\";s:7:\"name_he\";s:18:\"Essence Foundation\";s:4:\"slug\";s:22:\"essence-foundation-651\";s:5:\"image\";N;s:5:\"price\";d:38;s:13:\"compare_price\";d:48;s:16:\"discount_percent\";i:21;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.2;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:13;s:4:\"name\";s:7:\"Essence\";s:4:\"slug\";s:7:\"essence\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:45;s:9:\"low_stock\";b:0;}i:6;a:18:{s:2:\"id\";i:10;s:4:\"name\";s:42:\"مرطب لوريال هيالورونيك\";s:7:\"name_en\";s:31:\"L\'Oréal Hyaluronic Moisturizer\";s:7:\"name_he\";s:31:\"L\'Oréal Hyaluronic Moisturizer\";s:4:\"slug\";s:33:\"loreal-hyaluronic-moisturizer-955\";s:5:\"image\";N;s:5:\"price\";d:72;s:13:\"compare_price\";d:90;s:16:\"discount_percent\";i:20;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.7;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:29:\"العناية بالبشرة\";s:4:\"slug\";s:8:\"skincare\";}s:5:\"brand\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:8:\"L\'Oréal\";s:4:\"slug\";s:6:\"loreal\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:18;s:9:\"low_stock\";b:0;}i:7;a:18:{s:2:\"id\";i:3;s:4:\"name\";s:27:\"بودرة NYX HD Finishing\";s:7:\"name_en\";s:23:\"NYX HD Finishing Powder\";s:7:\"name_he\";s:23:\"NYX HD Finishing Powder\";s:4:\"slug\";s:27:\"nyx-hd-finishing-powder-664\";s:5:\"image\";N;s:5:\"price\";d:55;s:13:\"compare_price\";d:70;s:16:\"discount_percent\";i:21;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.3;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:10:\"مكياج\";s:4:\"slug\";s:6:\"makeup\";}s:5:\"brand\";a:3:{s:2:\"id\";i:3;s:4:\"name\";s:3:\"NYX\";s:4:\"slug\";s:3:\"nyx\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:28;s:9:\"low_stock\";b:0;}i:8;a:18:{s:2:\"id\";i:14;s:4:\"name\";s:34:\"عطر فلورمار للنساء\";s:7:\"name_en\";s:21:\"Flormar Women Perfume\";s:7:\"name_he\";s:21:\"Flormar Women Perfume\";s:4:\"slug\";s:25:\"flormar-women-perfume-843\";s:5:\"image\";N;s:5:\"price\";d:120;s:13:\"compare_price\";d:150;s:16:\"discount_percent\";i:20;s:10:\"is_on_sale\";b:1;s:8:\"in_stock\";b:1;s:10:\"rating_avg\";d:4.4;s:13:\"reviews_count\";i:0;s:8:\"category\";a:3:{s:2:\"id\";i:4;s:4:\"name\";s:12:\"العطور\";s:4:\"slug\";s:7:\"perfume\";}s:5:\"brand\";a:3:{s:2:\"id\";i:14;s:4:\"name\";s:7:\"Flormar\";s:4:\"slug\";s:7:\"flormar\";}s:17:\"short_description\";N;s:14:\"stock_quantity\";i:20;s:9:\"low_stock\";b:0;}}s:12:\"offerBanners\";a:0:{}}',1779707825);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `name_he` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description_ar` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'مكياج','איפור','Makeup','makeup',NULL,NULL,'💄','#FF6B9D',NULL,1,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(2,'العناية بالبشرة','טיפוח עור','Skincare','skincare',NULL,NULL,'🧴','#A8E6CF',NULL,2,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(3,'العناية بالشعر','טיפוח שיער','Hair Care','hair',NULL,NULL,'💆','#FFD93D',NULL,3,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(4,'العطور','בשמים','Perfumes','perfume',NULL,NULL,'🌸','#C9B8FF',NULL,4,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(5,'العناية بالأظافر','טיפוח ציפורניים','Nail Care','nails',NULL,NULL,'💅','#FFB347',NULL,5,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(6,'الأجهزة والأدوات','מכשירים וכלים','Devices','devices',NULL,NULL,'🪭','#87CEEB',NULL,6,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(7,'العناية بالجسم','טיפוח גוף','Body Care','body',NULL,NULL,'🛁','#98FB98',NULL,7,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16'),(8,'منتجات الأطفال','מוצרי תינוקות','Baby','baby',NULL,NULL,'👶','#FFE4E1',NULL,8,1,1,'2026-05-22 04:18:16','2026-05-22 04:18:16');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(8,2) NOT NULL,
  `min_order_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(8,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_per_user` int(11) NOT NULL DEFAULT 1,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `applicable_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_categories`)),
  `applicable_products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_products`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'ALFARED10','خصم 10% لأبناء الفريد','percentage',10.00,100.00,NULL,500,1,0,NULL,'2027-05-22 04:19:04',1,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04'),(2,'WELCOME20','خصم 20% للعملاء الجدد','percentage',20.00,200.00,NULL,200,1,0,NULL,'2026-11-22 05:19:04',1,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04'),(3,'SAVE15','خصم 15 شيكل','fixed',15.00,80.00,NULL,300,1,0,NULL,'2027-05-22 04:19:04',1,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_zones`
--

DROP TABLE IF EXISTS `delivery_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_zones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `name_he` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `base_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `free_above` decimal(8,2) DEFAULT NULL,
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `free_shipping_above` decimal(8,2) DEFAULT NULL,
  `estimated_days` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_zones`
--

LOCK TABLES `delivery_zones` WRITE;
/*!40000 ALTER TABLE `delivery_zones` DISABLE KEYS */;
INSERT INTO `delivery_zones` VALUES (1,'الخليل المدينة','חברון עיר','Hebron City',10.00,200.00,0.00,NULL,1,1,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(2,'محافظة الخليل','מחוז חברון','Hebron Governorate',15.00,300.00,0.00,NULL,1,1,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(3,'رام الله والبيرة','רמאללה','Ramallah',25.00,400.00,0.00,NULL,2,1,'2026-05-22 04:18:33','2026-05-22 04:18:33'),(4,'نابلس','שכם','Nablus',25.00,400.00,0.00,NULL,2,1,'2026-05-22 04:18:34','2026-05-22 04:18:34'),(5,'جنين','ג\'נין','Jenin',30.00,500.00,0.00,NULL,2,1,'2026-05-22 04:18:34','2026-05-22 04:18:34'),(6,'القدس','ירושלים','Jerusalem',20.00,350.00,0.00,NULL,1,1,'2026-05-22 04:18:34','2026-05-22 04:18:34'),(7,'بيت لحم','בית לחם','Bethlehem',15.00,300.00,0.00,NULL,1,1,'2026-05-22 04:18:34','2026-05-22 04:18:34'),(8,'غزة','עזה','Gaza',35.00,600.00,0.00,NULL,3,1,'2026-05-22 04:18:34','2026-05-22 04:18:34');
/*!40000 ALTER TABLE `delivery_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_logins`
--

DROP TABLE IF EXISTS `failed_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'storefront',
  `reason` varchar(50) NOT NULL DEFAULT 'invalid_credentials',
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_logins_ip_attempted_at_index` (`ip`,`attempted_at`),
  KEY `failed_logins_email_attempted_at_index` (`email`,`attempted_at`),
  KEY `failed_logins_email_index` (`email`),
  KEY `failed_logins_ip_index` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_logins`
--

LOCK TABLES `failed_logins` WRITE;
/*!40000 ALTER TABLE `failed_logins` DISABLE KEYS */;
INSERT INTO `failed_logins` VALUES (1,'admin@alfared.ps','192.168.1.7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','storefront','invalid_credentials','2026-05-25 04:48:28','2026-05-25 04:48:28','2026-05-25 04:48:28'),(2,'admin@alfared.ps','192.168.1.7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','storefront','invalid_credentials','2026-05-25 04:48:33','2026-05-25 04:48:33','2026-05-25 04:48:33'),(3,'admin@alfared.ps','192.168.1.6','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','storefront','invalid_credentials','2026-05-25 07:08:11','2026-05-25 07:08:11','2026-05-25 07:08:11'),(4,'+97059000000','127.0.0.1','curl/8.11.0','app','invalid_credentials','2026-05-25 08:06:02','2026-05-25 08:06:02','2026-05-25 08:06:02');
/*!40000 ALTER TABLE `failed_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_sale_products`
--

DROP TABLE IF EXISTS `flash_sale_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_sale_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flash_sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `quantity_limit` int(11) DEFAULT NULL,
  `sold_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sale_products_flash_sale_id_foreign` (`flash_sale_id`),
  KEY `flash_sale_products_product_id_foreign` (`product_id`),
  CONSTRAINT `flash_sale_products_flash_sale_id_foreign` FOREIGN KEY (`flash_sale_id`) REFERENCES `flash_sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `flash_sale_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sale_products`
--

LOCK TABLES `flash_sale_products` WRITE;
/*!40000 ALTER TABLE `flash_sale_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `flash_sale_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flash_sales`
--

DROP TABLE IF EXISTS `flash_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flash_sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flash_sales`
--

LOCK TABLES `flash_sales` WRITE;
/*!40000 ALTER TABLE `flash_sales` DISABLE KEYS */;
INSERT INTO `flash_sales` VALUES (1,'عرض خاص','2026-05-24 18:13:49','2026-05-25 18:13:53',1,'2026-05-24 15:13:56','2026-05-24 15:13:56');
/*!40000 ALTER TABLE `flash_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"9a6c46fa-bc02-4143-bbc0-b978b7645eb9\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:1:{i:0;a:21:{s:4:\\\"name\\\";s:4:\\\"view\\\";s:5:\\\"color\\\";N;s:5:\\\"event\\\";N;s:9:\\\"eventData\\\";a:0:{}s:17:\\\"dispatchDirection\\\";b:0;s:19:\\\"dispatchToComponent\\\";N;s:15:\\\"extraAttributes\\\";a:0:{}s:4:\\\"icon\\\";N;s:12:\\\"iconPosition\\\";E:42:\\\"Filament\\\\Support\\\\Enums\\\\IconPosition:Before\\\";s:8:\\\"iconSize\\\";N;s:10:\\\"isOutlined\\\";b:0;s:10:\\\"isDisabled\\\";b:0;s:5:\\\"label\\\";s:17:\\\"عرض الطلب\\\";s:11:\\\"shouldClose\\\";b:0;s:16:\\\"shouldMarkAsRead\\\";b:1;s:18:\\\"shouldMarkAsUnread\\\";b:0;s:21:\\\"shouldOpenUrlInNewTab\\\";b:0;s:4:\\\"size\\\";E:39:\\\"Filament\\\\Support\\\\Enums\\\\ActionSize:Small\\\";s:7:\\\"tooltip\\\";N;s:3:\\\"url\\\";s:36:\\\"http:\\/\\/localhost:8000\\/admin\\/orders\\/3\\\";s:4:\\\"view\\\";s:29:\\\"filament-actions::link-action\\\";}}s:4:\\\"body\\\";s:41:\\\"mohammad khallaf • 65.00 ₪ • Hebron\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:24:\\\"heroicon-o-shopping-cart\\\";s:9:\\\"iconColor\\\";s:7:\\\"warning\\\";s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:35:\\\"🛒 طلب جديد #ORD-2026-0003\\\";s:4:\\\"view\\\";s:36:\\\"filament-notifications::notification\\\";s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"f6195cc8-aaae-44a6-88e6-3f6454dc9816\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1779468100,\"delay\":null}',0,NULL,1779468100,1779468100),(2,'default','{\"uuid\":\"def3d6cf-c262-41ef-a47a-c14ca4778c55\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:11:{s:7:\\\"actions\\\";a:0:{}s:4:\\\"body\\\";s:19:\\\"هذا اختبار\\\";s:5:\\\"color\\\";N;s:8:\\\"duration\\\";s:10:\\\"persistent\\\";s:4:\\\"icon\\\";s:24:\\\"heroicon-o-shopping-cart\\\";s:9:\\\"iconColor\\\";s:7:\\\"warning\\\";s:6:\\\"status\\\";N;s:5:\\\"title\\\";s:28:\\\"🛒 اختبار إشعار\\\";s:4:\\\"view\\\";s:36:\\\"filament-notifications::notification\\\";s:8:\\\"viewData\\\";a:0:{}s:6:\\\"format\\\";s:8:\\\"filament\\\";}s:2:\\\"id\\\";s:36:\\\"d8e72555-edd8-4fee-88ac-4cdb0834d931\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1779468342,\"delay\":null}',0,NULL,1779468342,1779468342),(3,'default','{\"uuid\":\"b57aea94-970a-4a4f-9cc8-7aa523d05570\",\"displayName\":\"Filament\\\\Notifications\\\\DatabaseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:43:\\\"Filament\\\\Notifications\\\\DatabaseNotification\\\":2:{s:4:\\\"data\\\";a:2:{s:5:\\\"title\\\";s:4:\\\"Test\\\";s:4:\\\"body\\\";s:4:\\\"Body\\\";}s:2:\\\"id\\\";s:36:\\\"22a7a17b-f634-40f7-94f2-efc4af367697\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1779468362,\"delay\":null}',0,NULL,1779468362,1779468362);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_points`
--

DROP TABLE IF EXISTS `loyalty_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `points` int(11) NOT NULL,
  `balance` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `description_ar` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loyalty_points_user_id_foreign` (`user_id`),
  KEY `loyalty_points_order_id_foreign` (`order_id`),
  CONSTRAINT `loyalty_points_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loyalty_points_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_points`
--

LOCK TABLES `loyalty_points` WRITE;
/*!40000 ALTER TABLE `loyalty_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `loyalty_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loyalty_transactions`
--

DROP TABLE IF EXISTS `loyalty_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `points` int(11) NOT NULL,
  `action` varchar(40) NOT NULL,
  `value_ils` decimal(10,2) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loyalty_transactions_order_id_foreign` (`order_id`),
  KEY `loyalty_transactions_admin_id_foreign` (`admin_id`),
  KEY `loyalty_transactions_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `idx_loyalty_tx_user_id` (`user_id`),
  CONSTRAINT `loyalty_transactions_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loyalty_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loyalty_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loyalty_transactions`
--

LOCK TABLES `loyalty_transactions` WRITE;
/*!40000 ALTER TABLE `loyalty_transactions` DISABLE KEYS */;
INSERT INTO `loyalty_transactions` VALUES (4,9,NULL,NULL,5,'admin_add',NULL,'حضور يومي (يوم 1)','2026-05-25 08:14:18','2026-05-25 08:14:18');
/*!40000 ALTER TABLE `loyalty_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_01_000010_create_categories_table',1),(5,'2025_01_01_000011_create_brands_table',1),(6,'2025_01_01_000012_create_products_table',1),(7,'2025_01_01_000013_create_product_images_table',1),(8,'2025_01_01_000014_create_product_variants_table',1),(9,'2025_01_01_000015_create_delivery_zones_table',1),(10,'2025_01_01_000016_create_addresses_table',1),(11,'2025_01_01_000017_create_coupons_table',1),(12,'2025_01_01_000018_create_orders_table',1),(13,'2025_01_01_000019_create_order_items_table',1),(14,'2025_01_01_000020_create_reviews_table',1),(15,'2025_01_01_000021_create_wishlists_table',1),(16,'2025_01_01_000022_create_loyalty_points_table',1),(17,'2025_01_01_000023_create_banners_table',1),(18,'2025_01_01_000024_create_flash_sales_table',2),(19,'2025_01_01_000025_create_settings_table',2),(20,'2026_05_22_071720_add_extra_fields_to_users_table',3),(21,'2026_05_22_072000_add_missing_columns',3),(22,'2026_05_22_140000_add_permissions_to_users_table',4),(23,'2026_05_22_163831_create_notifications_table',5),(24,'2026_05_22_180000_create_order_audits_table',6),(25,'2026_05_22_200000_create_loyalty_transactions_table',7),(26,'2026_05_22_201000_add_loyalty_columns_to_orders',8),(27,'2026_05_22_220000_create_site_visits_table',9),(28,'2026_05_22_240000_create_failed_logins_table',10),(29,'2026_05_23_172609_create_personal_access_tokens_table',11),(30,'2026_05_23_180000_add_fcm_token_to_users',12),(31,'2026_05_24_000000_create_user_addresses_table',13),(32,'2026_05_24_001000_add_referral_and_checkin_to_users',13),(33,'2026_05_24_002000_backfill_referral_codes',14),(34,'2026_05_24_100000_add_performance_indexes',15),(35,'2026_05_24_110000_add_return_request_to_orders',16),(36,'2026_05_25_085156_create_otp_codes_table',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('469b3b72-c992-4982-8389-da2ce990a5e3','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8001\\/admin\\/orders\\/7\",\"view\":\"filament-actions::link-action\"}],\"body\":\"mohammad khallaf khallaf \\u2022 104.00 \\u20aa \\u2022 Hebron\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0007\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-24 14:40:04','2026-05-24 14:34:44','2026-05-24 14:40:04'),('6483ae44-0c06-495a-bc00-8a723e43041d','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/localhost:8000\\/admin\\/orders\",\"view\":\"filament-actions::link-action\"}],\"body\":\"\\u0639\\u0645\\u064a\\u0644 \\u0627\\u062e\\u062a\\u0628\\u0627\\u0631 \\u2022 250.00 \\u20aa \\u2022 \\u0627\\u0644\\u062e\\u0644\\u064a\\u0644\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #AF-TEST\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-22 13:51:24','2026-05-22 13:50:17','2026-05-22 13:51:24'),('6d6e7e16-77e3-4aad-9e08-39f8936ad3c2','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.0.2.2:8001\\/admin\\/orders\\/5\",\"view\":\"filament-actions::link-action\"}],\"body\":\"mohammad khallaf \\u2022 44.00 \\u20aa \\u2022 \\u0627\\u0644\\u062e\\u0644\\u064a (\\u062a\\u0637\\u0628\\u064a\\u0642)\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0005\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-24 14:26:55','2026-05-24 11:35:49','2026-05-24 14:26:55'),('7559b6c4-b4de-44bf-bf37-c5437194b6ab','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/192.168.1.7:8001\\/admin\\/orders\\/9\",\"view\":\"filament-actions::link-action\"}],\"body\":\"\\u0645\\u062d\\u0645\\u062f \\u062e\\u0644\\u0627\\u0641 \\u2022 185.00 \\u20aa \\u2022 \\u062f\\u0648\\u0631\\u0627 (\\u062a\\u0637\\u0628\\u064a\\u0642)\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0009\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-25 04:48:16','2026-05-25 04:46:19','2026-05-25 04:48:16'),('791828eb-b911-490c-8a27-4d2a2e1e3fd5','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[],\"body\":\"\\u0625\\u0630\\u0627 \\u0631\\u0623\\u064a\\u062a \\u0647\\u0630\\u0627 \\u0641\\u064a \\u0627\\u0644\\u062c\\u0631\\u0633 \\u0641\\u0627\\u0644\\u0646\\u0638\\u0627\\u0645 \\u064a\\u0639\\u0645\\u0644!\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u0627\\u062e\\u062a\\u0628\\u0627\\u0631\\u064a\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-22 13:49:30','2026-05-22 13:46:47','2026-05-22 13:49:30'),('dcd61540-8a4f-4228-a45d-7cbcbd1cd4ca','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/192.168.1.7:8001\\/admin\\/orders\\/8\",\"view\":\"filament-actions::link-action\"}],\"body\":\"\\u0645\\u062d\\u0645\\u062f \\u062e\\u0644\\u0627\\u0641 \\u2022 150.00 \\u20aa \\u2022 \\u062f\\u0648\\u0631\\u0627 (\\u062a\\u0637\\u0628\\u064a\\u0642)\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0008\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-25 04:49:24','2026-05-24 15:50:36','2026-05-25 04:49:24'),('dd431179-7e86-4a60-9c12-303e75727843','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/localhost:8000\\/admin\\/orders\\/4\",\"view\":\"filament-actions::link-action\"}],\"body\":\"mohammad khallaf \\u2022 71.00 \\u20aa \\u2022 Hebron\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0004\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-22 13:53:00','2026-05-22 13:52:29','2026-05-22 13:53:00'),('ffe2036c-3042-4531-96ac-df96e61f6834','Filament\\Notifications\\DatabaseNotification','App\\Models\\User',1,'{\"actions\":[{\"name\":\"view\",\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0637\\u0644\\u0628\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.0.2.2:8001\\/admin\\/orders\\/6\",\"view\":\"filament-actions::link-action\"}],\"body\":\"mohammad fff \\u2022 54.00 \\u20aa \\u2022 \\u0627\\u0644\\u062e\\u0644\\u064a\\u0644asdasd (\\u062a\\u0637\\u0628\\u064a\\u0642)\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-shopping-cart\",\"iconColor\":\"warning\",\"status\":null,\"title\":\"\\ud83d\\uded2 \\u0637\\u0644\\u0628 \\u062c\\u062f\\u064a\\u062f #ORD-2026-0006\",\"view\":\"filament-notifications::notification\",\"viewData\":[],\"format\":\"filament\"}','2026-05-24 14:26:56','2026-05-24 12:02:37','2026-05-24 14:26:56');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_audits`
--

DROP TABLE IF EXISTS `order_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_audits_user_id_foreign` (`user_id`),
  KEY `order_audits_order_id_created_at_index` (`order_id`,`created_at`),
  CONSTRAINT `order_audits_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_audits`
--

LOCK TABLES `order_audits` WRITE;
/*!40000 ALTER TABLE `order_audits` DISABLE KEYS */;
INSERT INTO `order_audits` VALUES (1,4,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"pending\",\"after\":\"shipped\"}}',NULL,'2026-05-22 14:14:51','2026-05-22 14:14:51'),(2,3,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"pending\",\"after\":\"processing\"}}',NULL,'2026-05-22 14:14:57','2026-05-22 14:14:57'),(3,2,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"pending\",\"after\":\"delivered\"}}',NULL,'2026-05-22 14:14:59','2026-05-22 14:14:59'),(4,5,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"pending\",\"after\":\"processing\"}}',NULL,'2026-05-24 11:36:41','2026-05-24 11:36:41'),(5,8,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"pending\",\"after\":\"processing\"}}',NULL,'2026-05-24 15:50:56','2026-05-24 15:50:56'),(6,8,1,'أبناء الفريد','status_change','{\"status\":{\"before\":\"processing\",\"after\":\"shipped\"}}',NULL,'2026-05-24 15:51:03','2026-05-24 15:51:03');
/*!40000 ALTER TABLE `order_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `variant_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `variant_info` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_variant_id_foreign` (`variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,2,NULL,'مسكارا L\'Oréal Volume',NULL,NULL,42.00,1,42.00,'2026-05-22 09:43:33','2026-05-22 09:43:33'),(2,2,2,NULL,'مسكارا L\'Oréal Volume',NULL,NULL,42.00,1,42.00,'2026-05-22 09:45:43','2026-05-22 09:45:43'),(3,3,1,NULL,'أحمر شفاه ميبلين ماتي',NULL,NULL,35.00,1,35.00,'2026-05-22 13:41:40','2026-05-22 13:41:40'),(4,4,4,NULL,'كحل مايبلين العيون',NULL,NULL,28.00,2,56.00,'2026-05-22 13:52:29','2026-05-22 13:52:29'),(5,5,7,NULL,'كريم نيفيا الترطيب اليومي',NULL,NULL,29.00,1,29.00,'2026-05-24 11:35:49','2026-05-24 11:35:49'),(6,6,7,NULL,'كريم نيفيا الترطيب اليومي',NULL,NULL,29.00,1,29.00,'2026-05-24 12:02:36','2026-05-24 12:02:36'),(7,7,2,NULL,'مسكارا L\'Oréal Volume',NULL,NULL,42.00,2,84.00,'2026-05-24 14:34:44','2026-05-24 14:34:44'),(8,8,1,NULL,'أحمر شفاه ميبلين ماتي',NULL,NULL,35.00,4,140.00,'2026-05-24 15:50:36','2026-05-24 15:50:36'),(9,9,1,NULL,'أحمر شفاه ميبلين ماتي',NULL,NULL,35.00,5,175.00,'2026-05-25 04:46:19','2026-05-25 04:46:19');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(255) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `area` varchar(255) DEFAULT NULL,
  `address_line` text NOT NULL,
  `building` varchar(255) DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL,
  `delivery_zone_id` bigint(20) unsigned DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `loyalty_points_redeemed` int(11) NOT NULL DEFAULT 0,
  `loyalty_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'pending',
  `return_requested_at` timestamp NULL DEFAULT NULL,
  `return_reason` varchar(500) DEFAULT NULL,
  `return_status` varchar(30) DEFAULT NULL,
  `payment_method` enum('cod') NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `last_edited_by` bigint(20) unsigned DEFAULT NULL,
  `last_edited_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_delivery_zone_id_foreign` (`delivery_zone_id`),
  KEY `orders_coupon_id_foreign` (`coupon_id`),
  KEY `orders_last_edited_by_foreign` (`last_edited_by`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_last_edited_by_foreign` FOREIGN KEY (`last_edited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'ORD-2026-0001',NULL,'mohammad khallaf khallaf','0594513978','mohammadkhallaf2002@gmail.com','Hebron','dd','PALESTINE _ hebron _ dura','dd',NULL,3,42.00,25.00,0.00,0,0.00,67.00,NULL,NULL,'processing',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-22 09:43:33','2026-05-22 09:46:13'),(2,'ORD-2026-0002',NULL,'mohammad khallaf khallaf','0594513978','mohammadkhallaf2002@gmail.com','الخليل',NULL,'رابود - دورا',NULL,NULL,6,42.00,20.00,0.00,0,0.00,62.00,NULL,NULL,'delivered',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-22 09:45:43','2026-05-22 14:14:59'),(3,'ORD-2026-0003',NULL,'mohammad khallaf','+972594513978','mohammadkhallaf2002@gmail.com','Hebron',NULL,'PALESTINE _ hebron _ dura',NULL,NULL,5,35.00,30.00,0.00,0,0.00,65.00,NULL,NULL,'processing',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-22 13:41:40','2026-05-22 14:14:57'),(4,'ORD-2026-0004',NULL,'mohammad khallaf','+972594513978','mohammadkhallaf2002@gmail.com','Hebron',NULL,'PALESTINE _ hebron _ dura',NULL,NULL,7,56.00,15.00,0.00,0,0.00,71.00,NULL,NULL,'shipped',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-22 13:52:29','2026-05-22 14:14:51'),(5,'ORD-2026-0005',NULL,'mohammad khallaf','+972598420090','mohammadkhallaf15@gmail.com','الخلي','dgdg','dgfgf','dg','g',2,29.00,15.00,0.00,0,0.00,44.00,NULL,NULL,'processing',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-24 11:35:49','2026-05-24 11:36:41'),(6,'ORD-2026-0006',NULL,'mohammad fff','+970594513978','mohammad@gmail.com','الخليلasdasd','asdasd','asdsa','asds','d',3,29.00,25.00,0.00,0,0.00,54.00,NULL,NULL,'pending',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-24 12:02:36','2026-05-24 12:02:36'),(7,'ORD-2026-0007',NULL,'mohammad khallaf khallaf','+970594513978','mohammadkhallaf2002@gmail.com','Hebron','1okk','PALESTINE _ hebron _ dura',NULL,NULL,6,0.00,20.00,0.00,0,0.00,20.00,NULL,NULL,'shipped',NULL,NULL,NULL,'cod','pending',NULL,1,'2026-05-24 14:40:20',NULL,NULL,NULL,'2026-05-24 14:34:44','2026-05-24 14:40:20'),(8,'ORD-2026-0008',NULL,'محمد خلاف','+970594513978','mohammadkhallaf2002@gmail.com','دورا','ابو العرقان','رابود',NULL,NULL,1,140.00,10.00,0.00,0,0.00,150.00,NULL,NULL,'shipped',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-24 15:50:36','2026-05-24 15:51:03'),(9,'ORD-2026-0009',NULL,'محمد خلاف','+972594513978','mohammadkhallaf2002@gmail.com','دورا','ابو العرقان','رابود',NULL,NULL,1,175.00,10.00,0.00,0,0.00,185.00,NULL,NULL,'pending',NULL,NULL,NULL,'cod','pending',NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-25 04:46:19','2026-05-25 04:46:19');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_phone_index` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
INSERT INTO `otp_codes` VALUES (1,'+972594513978','437824','2026-05-25 08:58:42',1,'2026-05-25 05:58:25','2026-05-25 05:58:42'),(2,'+972593435061','647836','2026-05-25 11:13:32',1,'2026-05-25 08:13:19','2026-05-25 08:13:32');
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',3,'mobile-app','9fc691d825d2a9421cca6eb96f62b99b38d438242f29fb5ef355a294837df55b','[\"*\"]','2026-05-24 11:24:26','2026-11-24 06:52:38','2026-05-24 05:52:38','2026-05-24 11:24:26'),(2,'App\\Models\\User',3,'mobile-app','8d0bd0f59dd0a0875b4eba7bba7291179b2cad29c889437b6fff4f7dd7a4c795','[\"*\"]','2026-05-24 11:56:02','2026-11-24 12:32:55','2026-05-24 11:32:55','2026-05-24 11:56:02'),(3,'App\\Models\\User',4,'mobile-app','52bdaaf76ece45a1e10fa98315d7d44050e308e7966b35b77a529a96cf8a925f','[\"*\"]','2026-05-24 12:02:42','2026-11-24 12:59:16','2026-05-24 11:59:16','2026-05-24 12:02:42'),(4,'App\\Models\\User',2,'mobile-app','946ed15786f35a9672f5c244fbefdaf2afe5620106d85032e5e0af641743ee4c','[\"*\"]','2026-05-24 12:33:03','2026-11-24 13:16:42','2026-05-24 12:16:42','2026-05-24 12:33:03'),(5,'App\\Models\\User',2,'mobile-app','949d929caffb827eb7113b76bb40d8fb5ed9e9d1ef72fff9031c7bb306993b5b','[\"*\"]','2026-05-24 15:10:26','2026-11-24 13:36:29','2026-05-24 12:36:29','2026-05-24 15:10:26'),(6,'App\\Models\\User',2,'mobile-app','6730f324ee538cbe249165ca7965cbe83d379df6d52d3e3055d37906ecbc00b5','[\"*\"]','2026-05-24 15:35:52','2026-11-24 16:35:13','2026-05-24 15:35:13','2026-05-24 15:35:52'),(7,'App\\Models\\User',5,'mobile-app','5c319482bc9b76a1089e4dcf7aa58a62b3af0be470e5b2c3eb7b9b0e7bd1bc54','[\"*\"]','2026-05-24 15:59:43','2026-11-24 16:49:19','2026-05-24 15:49:19','2026-05-24 15:59:43'),(8,'App\\Models\\User',5,'mobile-app','4edcaeb364901e0421d34c9aa703eb14f707ca12d75bbd655419ba7827d26431','[\"*\"]','2026-05-24 16:18:01','2026-11-24 17:16:07','2026-05-24 16:16:07','2026-05-24 16:18:01'),(9,'App\\Models\\User',5,'mobile-app','80833a685c32cd66bfe3a39aaed41158880e0d1e219b91d72fa469449c4e4e49','[\"*\"]','2026-05-25 03:49:02','2026-11-24 17:26:28','2026-05-24 16:26:28','2026-05-25 03:49:02'),(10,'App\\Models\\User',5,'mobile-app','83d303cd26402cdec86184c374742dfbb052fa5985fef31f06b0078ec5572998','[\"*\"]','2026-05-25 04:15:16','2026-11-25 05:14:20','2026-05-25 04:14:20','2026-05-25 04:15:16'),(11,'App\\Models\\User',5,'mobile-app','e8319b802926b4fd7450e3984e6cf683ad4c28985377311aa3aa78ac632d43c6','[\"*\"]','2026-05-25 04:27:04','2026-11-25 05:26:30','2026-05-25 04:26:30','2026-05-25 04:27:04'),(13,'App\\Models\\User',5,'mobile-app','4b1588885fbcae0e1d6bace6dcbf6a2632f7965eb93395c4074b1e754260e58d','[\"*\"]','2026-05-25 05:18:07','2026-11-25 05:44:15','2026-05-25 04:44:15','2026-05-25 05:18:07'),(14,'App\\Models\\User',6,'mobile-app','58c4917b822760b68c0cc2321a77420ee20af286a4ae7adc3e176d076c6a12ff','[\"*\"]','2026-05-25 05:37:51','2026-11-25 06:37:03','2026-05-25 05:37:03','2026-05-25 05:37:51'),(16,'App\\Models\\User',8,'mobile-app','40f360c61d94c5cc2e878616fb11670e1aef72c75f2fc8fba8e717e58169ae0e','[\"*\"]','2026-05-25 05:58:49','2026-11-25 06:58:43','2026-05-25 05:58:43','2026-05-25 05:58:49'),(18,'App\\Models\\User',9,'mobile-app','c02c1d1327f76c1e6d99f158b1ef16485ee70b3548701e6ed1d7b96a3dd7ec3b','[\"*\"]','2026-05-25 08:26:03','2026-11-25 09:13:33','2026-05-25 08:13:33','2026-05-25 08:26:03');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `value_en` varchar(255) DEFAULT NULL,
  `color_code` varchar(255) DEFAULT NULL,
  `price_modifier` decimal(8,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `sku` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name_ar` varchar(255) NOT NULL,
  `name_he` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description_ar` text DEFAULT NULL,
  `description_he` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `low_stock_alert` int(11) NOT NULL DEFAULT 5,
  `main_image` varchar(255) DEFAULT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_new` tinyint(1) NOT NULL DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `track_quantity` tinyint(1) NOT NULL DEFAULT 1,
  `allow_backorder` tinyint(1) NOT NULL DEFAULT 0,
  `views_count` int(10) unsigned NOT NULL DEFAULT 0,
  `sales_count` int(10) unsigned NOT NULL DEFAULT 0,
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) unsigned NOT NULL DEFAULT 0,
  `reviews_count` int(10) unsigned NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `idx_products_category_id` (`category_id`),
  KEY `idx_products_brand_id` (`brand_id`),
  KEY `idx_products_is_active` (`is_active`),
  KEY `idx_products_active_stock` (`is_active`,`stock_quantity`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'أحمر شفاه ميبلين ماتي','Maybelline Matte Lipstick','Maybelline Matte Lipstick','maybelline-matte-lipstick-622','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,2,35.00,45.00,NULL,'SKU-WX0XMU',40,5,NULL,NULL,1,1,0,1,1,0,5,120,4.50,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-25 04:46:19',NULL),(2,'مسكارا L\'Oréal Volume','L\'Oréal Volume Mascara','L\'Oréal Volume Mascara','loreal-volume-mascara-532','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,1,42.00,NULL,NULL,'SKU-9GDOSZ',31,5,NULL,NULL,1,1,0,1,1,0,1,89,4.70,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-24 14:34:44',NULL),(3,'بودرة NYX HD Finishing','NYX HD Finishing Powder','NYX HD Finishing Powder','nyx-hd-finishing-powder-664','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,3,55.00,70.00,NULL,'SKU-Z5EDHG',28,5,NULL,NULL,1,0,0,1,1,0,2,67,4.30,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-24 14:56:30',NULL),(4,'كحل مايبلين العيون','Maybelline Eye Liner','Maybelline Eye Liner','maybelline-eye-liner-255','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,2,28.00,NULL,NULL,'SKU-GGMPNV',58,5,NULL,NULL,1,1,0,1,1,0,0,145,4.60,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 13:52:29',NULL),(5,'فاونديشن إيسينس','Essence Foundation','Essence Foundation','essence-foundation-651','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,13,38.00,48.00,NULL,'SKU-PEPVIS',45,5,NULL,NULL,1,1,0,1,1,0,0,78,4.20,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(6,'بلاشر فلورمار','Flormar Blush','Flormar Blush','flormar-blush-489','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,1,14,32.00,NULL,NULL,'SKU-U89WHO',40,5,NULL,NULL,1,0,0,1,1,0,0,55,4.00,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(7,'كريم نيفيا الترطيب اليومي','Nivea Daily Moisturizer','Nivea Daily Moisturizer','nivea-daily-moisturizer-447','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,2,5,29.00,NULL,NULL,'SKU-EXZGAI',78,5,NULL,NULL,1,1,0,1,1,0,4,200,4.80,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-24 15:18:51',NULL),(8,'سيروم فيتامين C من غارنييه','Garnier Vitamin C Serum','Garnier Vitamin C Serum','garnier-vitamin-c-serum-708','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,2,6,65.00,80.00,NULL,'SKU-217CLE',25,5,NULL,NULL,1,1,0,1,1,0,0,93,4.60,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(9,'غسول وجه نيفيا','Nivea Face Wash','Nivea Face Wash','nivea-face-wash-699','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,2,5,22.00,NULL,NULL,'SKU-OHTSDR',100,5,NULL,NULL,1,0,0,1,1,0,0,155,4.40,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(10,'مرطب لوريال هيالورونيك','L\'Oréal Hyaluronic Moisturizer','L\'Oréal Hyaluronic Moisturizer','loreal-hyaluronic-moisturizer-955','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,2,1,72.00,90.00,NULL,'SKU-UEM6XA',18,5,NULL,NULL,1,1,0,1,1,0,0,77,4.70,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(11,'شامبو بانتين للشعر التالف','Pantene Damaged Hair Shampoo','Pantene Damaged Hair Shampoo','pantene-damaged-hair-shampoo-997','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,3,7,25.00,NULL,NULL,'SKU-HWVTSK',90,5,NULL,NULL,1,1,0,1,1,0,0,180,4.50,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(12,'بلسم غارنييه للشعر الجاف','Garnier Conditioner Dry Hair','Garnier Conditioner Dry Hair','garnier-conditioner-dry-hair-563','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,3,6,27.00,35.00,NULL,'SKU-JZOXPJ',55,5,NULL,NULL,1,0,0,1,1,0,0,102,4.30,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(13,'زيت الشعر لوريال','L\'Oréal Hair Oil','L\'Oréal Hair Oil','loreal-hair-oil-408','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,3,1,48.00,60.00,NULL,'SKU-KY5FDO',30,5,NULL,NULL,1,1,0,1,1,0,0,85,4.60,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(14,'عطر فلورمار للنساء','Flormar Women Perfume','Flormar Women Perfume','flormar-women-perfume-843','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,4,14,120.00,150.00,NULL,'SKU-NIQ0BH',20,5,NULL,NULL,1,1,0,1,1,0,0,45,4.40,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(15,'بخاخ جسم فاخر','Luxury Body Spray','Luxury Body Spray','luxury-body-spray-738','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,4,NULL,35.00,NULL,NULL,'SKU-GBXUNL',65,5,NULL,NULL,1,0,0,1,1,0,0,70,4.10,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(16,'طلاء أظافر فلورمار','Flormar Nail Polish','Flormar Nail Polish','flormar-nail-polish-836','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,5,14,18.00,NULL,NULL,'SKU-WFOU0D',120,5,NULL,NULL,1,0,0,1,1,0,0,190,4.20,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-22 04:19:04',NULL),(17,'طلاء جل للأظافر','Gel Nail Polish','Gel Nail Polish','gel-nail-polish-377','منتج عالي الجودة من أفضل الماركات العالمية. متوفر لدى شركة أبناء الفريد التجارية بالخليل.',NULL,'High quality product from the best international brands. Available at Alfared Sons Company, Hebron.',NULL,5,NULL,25.00,32.00,NULL,'SKU-95B4RN',75,5,NULL,NULL,1,1,0,1,1,0,1,130,4.50,0,0,NULL,NULL,'2026-05-22 04:19:04','2026-05-24 15:59:35',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_order_id_foreign` (`order_id`),
  KEY `idx_reviews_product_id` (`product_id`),
  KEY `idx_reviews_is_approved` (`is_approved`),
  CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,17,NULL,NULL,'محمد خلاف',5,NULL,0,0,'2026-05-24 15:59:43','2026-05-24 15:59:43');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('DzPPyoh1snIUwCwIM7Y8o0G73hBpJpvzKNcZJHh7',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiOFN2VzFmY3pJd0dITmZaUVRNeU1VazNLNVBhd0hWYWM5d2RRUzNEbyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDEvYWRtaW4tYXBpL2xhdGVzdC1ub3RpZmljYXRpb24iO3M6NToicm91dGUiO3M6MjU6ImFkbWluLmxhdGVzdC1ub3RpZmljYXRpb24iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI3NzFiYjE2N2Q5MmJjOWJjYjliYjU0MGFkMzFlNGM1OTgwMmFlOWQ3MzRkNTM3ZDhhNWY1MjUxYjI3YmE2NGI3IjtzOjY6ImxvY2FsZSI7czoyOiJhciI7fQ==',1779703638),('eHAnEM4LTHz4FN6sSdAwH80TDtjeX2I2bg7Otcc2',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiVE05c2dUdEdaSlNJd1NtYjFzSG1aNFU4RUtoOHhrclJDNHcwOW5VUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi1hcGkvbGF0ZXN0LW5vdGlmaWNhdGlvbiI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4ubGF0ZXN0LW5vdGlmaWNhdGlvbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI3NzFiYjE2N2Q5MmJjOWJjYjliYjU0MGFkMzFlNGM1OTgwMmFlOWQ3MzRkNTM3ZDhhNWY1MjUxYjI3YmE2NGI3Ijt9',1779703631),('EnpO4vMq0avVGwrm7BXQQ56GpNYtw6qZBo8SpgmQ',1,'192.168.1.7','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiY0V5S25EWjJONzJIa1IzN05md0o3MGtuUXJsenk1TzMwdlFpcjNZeSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUzOiJodHRwOi8vMTkyLjE2OC4xLjc6ODAwMS9hZG1pbi1hcGkvbGF0ZXN0LW5vdGlmaWNhdGlvbiI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4ubGF0ZXN0LW5vdGlmaWNhdGlvbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6Ijc3MWJiMTY3ZDkyYmM5YmNiOWJiNTQwYWQzMWU0YzU5ODAyYWU5ZDczNGQ1MzdkOGE1ZjUyNTFiMjdiYTY0YjciO3M6ODoiZmlsYW1lbnQiO2E6MDp7fX0=',1779700634),('hG4fL6mJLXWH4q9loT0L0x1KIGJEab5qEqM11Pal',NULL,'10.121.69.249','','YTozOntzOjY6Il90b2tlbiI7czo0MDoidHN5ZUVGR2xjbjRiNVNpVklFelJpaTF0NWR2WU80aHNVRzduYWRQQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xMC4xMjEuNjkuMjQ5OjgwMDEiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779700664),('iWCBRVFMapiQxEbiRgciz4x1K9BWAeDnXeXzw3fI',1,'192.168.1.6','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo2OntzOjY6Il90b2tlbiI7czo0MDoiNFdkNkxaeFA2eE9zM0ZXTTh2Q29CdWh2eU9QaEVqcGpudnlXMlJVbiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjUzOiJodHRwOi8vMTkyLjE2OC4xLjY6ODAwMS9hZG1pbi1hcGkvbGF0ZXN0LW5vdGlmaWNhdGlvbiI7czo1OiJyb3V0ZSI7czoyNToiYWRtaW4ubGF0ZXN0LW5vdGlmaWNhdGlvbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6Ijc3MWJiMTY3ZDkyYmM5YmNiOWJiNTQwYWQzMWU0YzU5ODAyYWU5ZDczNGQ1MzdkOGE1ZjUyNTFiMjdiYTY0YjciO30=',1779709700),('NSY9aEvq9BQ5vw61dVit0HrPUN3OLFlQANqAMALd',NULL,'10.121.69.249','Avast Antivirus','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNkhXSnN2Tk1jMUdsSExsaU96S0o1bHNHTmcxbVdZTnIwbEhMc3ExTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly8xMC4xMjEuNjkuMjQ5OjgwMDEiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779700653);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_visits`
--

DROP TABLE IF EXISTS `site_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_visits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `visitor_id` varchar(64) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `device_type` varchar(20) NOT NULL,
  `browser` varchar(60) DEFAULT NULL,
  `os` varchar(60) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `referer` varchar(500) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_visits_user_id_foreign` (`user_id`),
  KEY `site_visits_visited_at_device_type_index` (`visited_at`,`device_type`),
  KEY `site_visits_visitor_id_index` (`visitor_id`),
  KEY `site_visits_device_type_index` (`device_type`),
  KEY `site_visits_visited_at_index` (`visited_at`),
  CONSTRAINT `site_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_visits`
--

LOCK TABLES `site_visits` WRITE;
/*!40000 ALTER TABLE `site_visits` DISABLE KEYS */;
INSERT INTO `site_visits` VALUES (1,'b45d740db8739fc6472b2c890c31ec3a',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8000','/','http://localhost:8000/admin/site-settings',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 14:42:25','2026-05-22 14:42:25','2026-05-22 14:42:25'),(2,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:27:24','2026-05-23 03:27:24','2026-05-23 03:27:24'),(3,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/','http://localhost:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:28:39','2026-05-23 03:28:39','2026-05-23 03:28:39'),(4,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/','http://localhost:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:29:41','2026-05-23 03:29:41','2026-05-23 03:29:41'),(5,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001/account/wishlist','/account/wishlist','http://localhost:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:29:51','2026-05-23 03:29:51','2026-05-23 03:29:51'),(6,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001/products','/products','http://localhost:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:30:25','2026-05-23 03:30:25','2026-05-23 03:30:25'),(7,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001/cart','/cart','http://localhost:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:30:43','2026-05-23 03:30:43','2026-05-23 03:30:43'),(8,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/','http://localhost:8001/cart',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:38:09','2026-05-23 03:38:09','2026-05-23 03:38:09'),(9,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001/account/wishlist','/account/wishlist','http://localhost:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:52:19','2026-05-23 03:52:19','2026-05-23 03:52:19'),(10,'bfdbf45ae09eefdcd0dbcfac7ea00880',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/','http://localhost:8001/account/wishlist',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 03:52:22','2026-05-23 03:52:22','2026-05-23 03:52:22'),(11,'a59e4083b2c1549889dd6d9c6c26adcd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8001','/','http://localhost:8001/account/wishlist',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 13:47:55','2026-05-23 13:47:55','2026-05-23 13:47:55'),(12,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/admin',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 13:59:43','2026-05-23 13:59:43','2026-05-23 13:59:43'),(13,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','mobile','Chrome','Android','http://127.0.0.1:8001','/','http://127.0.0.1:8001/admin',NULL,'ar','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36','2026-05-23 14:02:21','2026-05-23 14:02:21','2026-05-23 14:02:21'),(14,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/admin',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:20:33','2026-05-23 14:20:33','2026-05-23 14:20:33'),(15,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/admin',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:22:47','2026-05-23 14:22:47','2026-05-23 14:22:47'),(16,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:33:14','2026-05-23 14:33:14','2026-05-23 14:33:14'),(17,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:44:36','2026-05-23 14:44:36','2026-05-23 14:44:36'),(18,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/category/makeup','/category/makeup','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:44:38','2026-05-23 14:44:38','2026-05-23 14:44:38'),(19,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/category/skincare','/category/skincare','http://127.0.0.1:8001/category/makeup',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:44:40','2026-05-23 14:44:40','2026-05-23 14:44:40'),(20,'932beed8d421bfe6b0cb5212a165a0b7',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/category/hair','/category/hair','http://127.0.0.1:8001/category/skincare',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-23 14:44:42','2026-05-23 14:44:42','2026-05-23 14:44:42'),(21,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/category/hair',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 05:16:16','2026-05-24 05:16:16','2026-05-24 05:16:16'),(22,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/category/hair',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 05:56:51','2026-05-24 05:56:51','2026-05-24 05:56:51'),(23,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 07:14:01','2026-05-24 07:14:01','2026-05-24 07:14:01'),(24,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:12:39','2026-05-24 08:12:39','2026-05-24 08:12:39'),(25,'f0b65792652432293f7ab1a89a38a7cb',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/login','/login',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:12:55','2026-05-24 08:12:55','2026-05-24 08:12:55'),(26,'d4401cdab0ce5ca7350a362dd6ee9494',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/login',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:13:08','2026-05-24 08:13:08','2026-05-24 08:13:08'),(27,'d4401cdab0ce5ca7350a362dd6ee9494',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/orders','/account/orders','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:13:16','2026-05-24 08:13:16','2026-05-24 08:13:16'),(28,'d4401cdab0ce5ca7350a362dd6ee9494',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/points','/account/points','http://127.0.0.1:8001/account/orders',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:13:19','2026-05-24 08:13:19','2026-05-24 08:13:19'),(29,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 08:13:52','2026-05-24 08:13:52','2026-05-24 08:13:52'),(30,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account/points',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 11:25:33','2026-05-24 11:25:33','2026-05-24 11:25:33'),(31,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account/points',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 12:27:10','2026-05-24 12:27:10','2026-05-24 12:27:10'),(32,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 12:30:13','2026-05-24 12:30:13','2026-05-24 12:30:13'),(33,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 12:37:38','2026-05-24 12:37:38','2026-05-24 12:37:38'),(34,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:16:29','2026-05-24 14:16:29','2026-05-24 14:16:29'),(35,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:27:57','2026-05-24 14:27:57','2026-05-24 14:27:57'),(36,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:29:35','2026-05-24 14:29:35','2026-05-24 14:29:35'),(37,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/orders','/account/orders','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:29:40','2026-05-24 14:29:40','2026-05-24 14:29:40'),(38,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/wishlist','/account/wishlist','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:29:50','2026-05-24 14:29:50','2026-05-24 14:29:50'),(39,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/cart','/cart','http://127.0.0.1:8001/account/wishlist',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:29:57','2026-05-24 14:29:57','2026-05-24 14:29:57'),(40,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout','/checkout','http://127.0.0.1:8001/cart',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:30:01','2026-05-24 14:30:01','2026-05-24 14:30:01'),(41,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout','/checkout','http://127.0.0.1:8001/cart',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:31:23','2026-05-24 14:31:23','2026-05-24 14:31:23'),(42,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout','/checkout','http://127.0.0.1:8001/cart',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:33:01','2026-05-24 14:33:01','2026-05-24 14:33:01'),(43,'3f47c3fdeaaccb112070fa73ede4a340',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/checkout',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:33:27','2026-05-24 14:33:27','2026-05-24 14:33:27'),(44,'4c53b0f58caffe4c69b57473e183252a',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:33:30','2026-05-24 14:33:31','2026-05-24 14:33:31'),(45,'4c53b0f58caffe4c69b57473e183252a',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/login','/login','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:33:35','2026-05-24 14:33:35','2026-05-24 14:33:35'),(46,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/login',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:33:52','2026-05-24 14:33:52','2026-05-24 14:33:52'),(47,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/orders','/account/orders','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:01','2026-05-24 14:34:01','2026-05-24 14:34:01'),(48,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/wishlist','/account/wishlist','http://127.0.0.1:8001/account/orders',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:11','2026-05-24 14:34:11','2026-05-24 14:34:11'),(49,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:22','2026-05-24 14:34:22','2026-05-24 14:34:22'),(50,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/cart','/cart','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:28','2026-05-24 14:34:28','2026-05-24 14:34:28'),(51,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout','/checkout','http://127.0.0.1:8001/cart',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:31','2026-05-24 14:34:31','2026-05-24 14:34:31'),(52,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout/delivery-fee?subtotal=84&zone_id=6','/checkout/delivery-fee','http://127.0.0.1:8001/checkout',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:37','2026-05-24 14:34:37','2026-05-24 14:34:37'),(53,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout/success/ORD-2026-0007','/checkout/success/ORD-2026-0007','http://127.0.0.1:8001/checkout',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:45','2026-05-24 14:34:45','2026-05-24 14:34:45'),(54,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/checkout/tracking/ORD-2026-0007','/checkout/tracking/ORD-2026-0007','http://127.0.0.1:8001/checkout/success/ORD-2026-0007',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:34:50','2026-05-24 14:34:50','2026-05-24 14:34:50'),(55,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/checkout/tracking/ORD-2026-0007',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:37:32','2026-05-24 14:37:32','2026-05-24 14:37:32'),(56,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products?sort=price_asc','/products','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:38:44','2026-05-24 14:38:44','2026-05-24 14:38:44'),(57,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:39:11','2026-05-24 14:39:11','2026-05-24 14:39:11'),(58,'2f5c24f70da4b605d86b6e63539e4cdd',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:39:28','2026-05-24 14:39:28','2026-05-24 14:39:28'),(59,'35993bb4b3b31794c29d37796b782d08',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:39:31','2026-05-24 14:39:31','2026-05-24 14:39:31'),(60,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products?sort=newest',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:40:55','2026-05-24 14:40:55','2026-05-24 14:40:55'),(61,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:54:17','2026-05-24 14:54:17','2026-05-24 14:54:17'),(62,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products/maybelline-matte-lipstick-622','/products/maybelline-matte-lipstick-622','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:54:20','2026-05-24 14:54:20','2026-05-24 14:54:20'),(63,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/products/maybelline-matte-lipstick-622',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:54:28','2026-05-24 14:54:28','2026-05-24 14:54:28'),(64,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products/nyx-hd-finishing-powder-664','/products/nyx-hd-finishing-powder-664','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:54:30','2026-05-24 14:54:30','2026-05-24 14:54:30'),(65,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products/nyx-hd-finishing-powder-664','/products/nyx-hd-finishing-powder-664','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:56:30','2026-05-24 14:56:30','2026-05-24 14:56:30'),(66,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products/nyx-hd-finishing-powder-664',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 14:56:39','2026-05-24 14:56:39','2026-05-24 14:56:39'),(67,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products?sort=newest',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:03:01','2026-05-24 15:03:01','2026-05-24 15:03:01'),(68,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products?sort=newest',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:14:38','2026-05-24 15:14:38','2026-05-24 15:14:38'),(69,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products?on_sale=1','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:14:46','2026-05-24 15:14:46','2026-05-24 15:14:46'),(70,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:15:53','2026-05-24 15:15:53','2026-05-24 15:15:53'),(71,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:15:59','2026-05-24 15:15:59','2026-05-24 15:15:59'),(72,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products/nyx-hd-finishing-powder-664',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:20:02','2026-05-24 15:20:02','2026-05-24 15:20:02'),(73,'0312cf930388b5c852d95f9d74be7694',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products?brand=8','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 15:20:13','2026-05-24 15:20:13','2026-05-24 15:20:13'),(74,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account/points','/account/points','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:10:22','2026-05-24 16:10:22','2026-05-24 16:10:22'),(75,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:12:30','2026-05-24 16:12:30','2026-05-24 16:12:30'),(76,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products?on_sale=1','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:13:10','2026-05-24 16:13:10','2026-05-24 16:13:10'),(77,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products?on_sale=1',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:14:44','2026-05-24 16:14:44','2026-05-24 16:14:44'),(78,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:14:52','2026-05-24 16:14:52','2026-05-24 16:14:52'),(79,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:19:04','2026-05-24 16:19:04','2026-05-24 16:19:04'),(80,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/about','/about','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:19:14','2026-05-24 16:19:14','2026-05-24 16:19:14'),(81,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/contact','/contact','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:19:31','2026-05-24 16:19:31','2026-05-24 16:19:31'),(82,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:20:28','2026-05-24 16:20:28','2026-05-24 16:20:28'),(83,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:28:22','2026-05-24 16:28:22','2026-05-24 16:28:22'),(84,'2e134658275a3ba4e847af9cf6580b6c',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-24 16:29:31','2026-05-24 16:29:31','2026-05-24 16:29:31'),(85,'e497bb20c0b7e926a8688d15b66658f4',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products??brand=8',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:45:12','2026-05-25 03:45:12','2026-05-25 03:45:12'),(86,'e497bb20c0b7e926a8688d15b66658f4',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:45:17','2026-05-25 03:45:17','2026-05-25 03:45:17'),(87,'e497bb20c0b7e926a8688d15b66658f4',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/about','/about','http://127.0.0.1:8001/products?on_sale=1',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:45:33','2026-05-25 03:45:33','2026-05-25 03:45:33'),(88,'e497bb20c0b7e926a8688d15b66658f4',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/contact','/contact','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:45:40','2026-05-25 03:45:40','2026-05-25 03:45:40'),(89,'e497bb20c0b7e926a8688d15b66658f4',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/login','/login','http://127.0.0.1:8001/contact',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:55:35','2026-05-25 03:55:35','2026-05-25 03:55:35'),(90,'2d3b5b68804a5c68ddb653937b35c2e7',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/account','/account','http://127.0.0.1:8001/login',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 03:55:40','2026-05-25 03:55:40','2026-05-25 03:55:40'),(91,'2d3b5b68804a5c68ddb653937b35c2e7',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/account',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 04:18:32','2026-05-25 04:18:32','2026-05-25 04:18:32'),(92,'86e5e4ef1a32838ce7ec193eca1a4b77',NULL,'10.121.69.249','desktop','Other','Other','http://10.121.69.249:8001','/',NULL,NULL,'ar','Avast Antivirus','2026-05-25 06:17:33','2026-05-25 06:17:33','2026-05-25 06:17:33'),(93,'5558ec80925004ceef6e5466d231e922',NULL,'10.121.69.249','desktop','Other','Other','http://10.121.69.249:8001','/',NULL,NULL,'ar','','2026-05-25 06:17:44','2026-05-25 06:17:44','2026-05-25 06:17:44'),(94,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:23:58','2026-05-25 06:23:58','2026-05-25 06:23:58'),(95,'19d1da5de504c99bd53e9925e33faed0',NULL,'127.0.0.1','desktop','Chrome','Windows 10/11','http://localhost:8000','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:25:02','2026-05-25 06:25:02','2026-05-25 06:25:02'),(96,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/',NULL,NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:31:18','2026-05-25 06:31:18','2026-05-25 06:31:18'),(97,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/about','/about','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:31:35','2026-05-25 06:31:35','2026-05-25 06:31:35'),(98,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/contact','/contact','http://127.0.0.1:8001/about',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:31:50','2026-05-25 06:31:50','2026-05-25 06:31:50'),(99,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001/products','/products','http://127.0.0.1:8001/',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 06:59:46','2026-05-25 06:59:46','2026-05-25 06:59:46'),(100,'346e2f8071a58c261e0c1f5083686601',1,'127.0.0.1','desktop','Chrome','Windows 10/11','http://127.0.0.1:8001','/','http://127.0.0.1:8001/products',NULL,'ar','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-25 07:00:30','2026-05-25 07:00:30','2026-05-25 07:00:30');
/*!40000 ALTER TABLE `site_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(60) DEFAULT NULL,
  `delivery_zone_id` bigint(20) unsigned DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `address_line` varchar(500) NOT NULL,
  `building` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_addresses_delivery_zone_id_foreign` (`delivery_zone_id`),
  KEY `user_addresses_user_id_is_default_index` (`user_id`,`is_default`),
  CONSTRAINT `user_addresses_delivery_zone_id_foreign` FOREIGN KEY (`delivery_zone_id`) REFERENCES `delivery_zones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_addresses`
--

LOCK TABLES `user_addresses` WRITE;
/*!40000 ALTER TABLE `user_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `loyalty_points_balance` int(11) NOT NULL DEFAULT 0,
  `phone` varchar(255) DEFAULT NULL,
  `referral_code` varchar(16) DEFAULT NULL,
  `referred_by` bigint(20) unsigned DEFAULT NULL,
  `last_checkin_at` date DEFAULT NULL,
  `checkin_streak` int(11) NOT NULL DEFAULT 0,
  `fcm_token` varchar(255) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `loyalty_points` int(10) unsigned NOT NULL DEFAULT 0,
  `gender` enum('female','male','other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_referral_code_unique` (`referral_code`),
  KEY `users_referred_by_foreign` (`referred_by`),
  CONSTRAINT `users_referred_by_foreign` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'أبناء الفريد','admin@alfared.ps',0,NULL,'FD6F4289',NULL,NULL,0,NULL,NULL,'admin',NULL,0,NULL,NULL,NULL,1,NULL,NULL,'$2y$12$G9G4T.fDyqX6oUc3N4OroudiyEVREP5KarGOFjg.PwY42byY6k8Ve',NULL,'2026-05-22 03:45:19','2026-05-24 12:19:29'),(8,'محمد خلاف','mohammadkhallaf2002@gmail.com',0,'+972594513978','AA609D19',NULL,NULL,0,NULL,NULL,'customer',NULL,0,NULL,NULL,NULL,1,NULL,NULL,'$2y$12$UlRA0vzSFWHl1YLtzBiASOEcXAkZpbKTcyQPLEcLefE4Wfp6P.Kei',NULL,'2026-05-25 05:58:43','2026-05-25 05:58:43'),(9,'Mohammad Khallaf','mohammadkhallaf15@gmail.com',0,'+972593435061','1E9D9E7F',NULL,'2026-05-25',1,NULL,NULL,'customer',NULL,5,NULL,NULL,NULL,1,NULL,NULL,'$2y$12$gcZNsE8vIDMIReD0hT6zCOz8CGO0YUYm8p7x8l6HA9Jhj8dgHT272',NULL,'2026-05-25 08:13:33','2026-05-25 08:14:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  KEY `idx_wishlists_user_id` (`user_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
INSERT INTO `wishlists` VALUES (1,1,1,'2026-05-23 03:29:32','2026-05-23 03:29:32'),(2,1,4,'2026-05-23 03:29:43','2026-05-23 03:29:43');
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'alfared_shop'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-25 14:54:55
