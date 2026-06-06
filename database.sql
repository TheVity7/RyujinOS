-- =====================================================================
-- RyujinOS - Veritabani Semasi + Demo Veri
-- Minecraft Sunucu CMS (saf PHP 8.3)
--
-- Kurulum: phpMyAdmin/cPanel'de bos bir veritabani secip bu dosyayi
-- 'Import' edin. Ardindan .env icindeki DB bilgilerini guncelleyin.
-- Varsayilan yonetici: admin / admin123
-- =====================================================================

-- MariaDB dump 10.19-12.3.2-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: ryujinos
-- ------------------------------------------------------
-- Server version	12.3.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `authme`
--

DROP TABLE IF EXISTS `authme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `authme` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `realname` varchar(64) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `lastlogin` bigint(20) DEFAULT NULL,
  `regdate` bigint(20) DEFAULT NULL,
  `regip` varchar(64) DEFAULT NULL,
  `isLogged` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authme`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `authme` WRITE;
/*!40000 ALTER TABLE `authme` DISABLE KEYS */;
INSERT INTO `authme` VALUES
(1,'admin','admin','$SHA$a6a8053e1622b675$55fca06038e3a8bd3c9a73a72358e69c63379885d537b5c20e250232cdfe746f','admin@ryujinos.net','127.0.0.1',1780760724000,1780760724000,NULL,0),
(2,'loky1454','loky1454','$SHA$4004b839b62a2266$505b23e9ba606ad285b63335538eb90043ead6957ba59b4b04dcd04b621feb4a','nodernetinfos@gmail.com','127.0.0.1',1780760724000,1780760724000,NULL,0),
(3,'notch','Notch','$SHA$f1942c2c766da34b$11ff7431cad200bd82f7cc13a8bbd1e178dfb9ec9ae1e82918f0fc5d101587fc','notch@example.com','127.0.0.1',1780760724000,1780760724000,NULL,0),
(4,'steve','Steve','$SHA$e7e596f66a365a2c$476087fe06da192857cf51aa108f690c49323565b68fb1282e6f3a9289f00c96','steve@example.com','127.0.0.1',1780760724000,1780760724000,NULL,0);
/*!40000 ALTER TABLE `authme` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'VIP Paketleri','vip','Sunucumuzdaki tüm VIP rütbeleri','crown',1),
(2,'Anahtarlar','anahtarlar','Sandık anahtarları ve kasalar','key',2),
(3,'Spawnerlar','spawnerlar','Mob spawnerları','cube',3),
(4,'Krediler','krediler','Bakiye yükleme paketleri','coins',4);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `credit_transactions`
--

DROP TABLE IF EXISTS `credit_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `type` varchar(20) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_transactions`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `credit_transactions` WRITE;
/*!40000 ALTER TABLE `credit_transactions` DISABLE KEYS */;
INSERT INTO `credit_transactions` VALUES
(1,1,6999.00,'purchase','Kredi yükleme','2026-06-06 19:15:58'),
(2,1,450.00,'purchase','Kredi yükleme','2026-06-06 19:15:58'),
(3,1,1000.00,'purchase','Kredi yükleme','2026-06-06 19:15:58'),
(4,1,123.00,'purchase','Kredi yükleme','2026-06-06 19:15:58');
/*!40000 ALTER TABLE `credit_transactions` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `product_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `delivery` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,1,0,'MVIP',1,79.90,'delivered','delivered','2026-06-06 19:15:58'),
(2,1,0,'Nadir Anahtar',1,24.90,'delivered','delivered','2026-06-06 19:15:58');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_ref` varchar(64) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credits` decimal(12,2) NOT NULL DEFAULT 0.00,
  `provider` varchar(30) NOT NULL DEFAULT 'shopier',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_id` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `excerpt` varchar(300) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(64) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `comments` int(11) NOT NULL DEFAULT 0,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES
(1,'Yeni Sezon Başladı!','yeni-sezon','Sezon 3 tüm sürprizleriyle açıldı, hemen katıl!','Yeni sezonumuz tüm haritalar, yeni eşyalar ve etkinliklerle başladı. Sunucuya giriş yaparak ödüllerini topla!',NULL,'admin',0,0,1,'2026-06-06 19:15:58'),
(2,'Hafta Sonu Etkinliği','hafta-sonu-etkinligi','Bu hafta sonu çift XP ve özel ödüller seni bekliyor.','Cumartesi ve Pazar günleri boyunca tüm modlarda çift XP kazanacaksınız.',NULL,'admin',0,0,1,'2026-06-06 19:15:58'),
(3,'Mağaza İndirimleri','magaza-indirimleri','Seçili ürünlerde %50 indirim fırsatını kaçırma.','VIP paketleri ve anahtarlarda büyük indirimler mağazamızda.',NULL,'admin',0,0,1,'2026-06-06 19:15:58'),
(4,'Discord Topluluğumuza Katıl','discord-katil','Güncel duyurular ve etkinlikler için Discord sunucumuza bekleriz.','Discord sunucumuzda 900+ aktif üyemizle sohbet edebilir, etkinliklere katılabilirsin.',NULL,'admin',0,0,1,'2026-06-06 19:15:58');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(150) NOT NULL,
  `slug` varchar(170) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `commands` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT -1,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES
(1,1,'VIP','vip','7 günlük VIP rütbesi. Renkli sohbet, /fly ve özel kit.',NULL,49.90,0.00,'lp user {player} parent addtemp vip 7d\nbroadcast &a{player} &fVIP satın aldı!',-1,1,1,0,'2026-06-06 19:15:58'),
(2,1,'MVIP','mvip','30 günlük MVIP rütbesi. Tüm VIP avantajları + ekstra home.',NULL,99.90,79.90,'lp user {player} parent addtemp mvip 30d',-1,1,1,1,'2026-06-06 19:15:58'),
(3,1,'VIP+','vip-plus','Süresiz VIP+ rütbesi.',NULL,149.90,0.00,'lp user {player} parent add vipplus',-1,1,0,2,'2026-06-06 19:15:58'),
(4,2,'Nadir Anahtar','nadir-anahtar','5 adet nadir sandık anahtarı.',NULL,24.90,0.00,'crates give {player} rare 5',-1,1,1,3,'2026-06-06 19:15:58'),
(5,2,'Efsanevi Anahtar','efsanevi-anahtar','3 adet efsanevi sandık anahtarı.',NULL,59.90,49.90,'crates give {player} legendary 3',-1,1,1,4,'2026-06-06 19:15:58'),
(6,3,'Zombi Spawner','zombi-spawner','1 adet zombi spawnerı.',NULL,39.90,0.00,'give {player} spawner 1',-1,1,1,5,'2026-06-06 19:15:58'),
(7,3,'Iron Golem Spawner','iron-golem-spawner','1 adet demir golem spawnerı.',NULL,89.90,0.00,'give {player} spawner 1',-1,1,0,6,'2026-06-06 19:15:58');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
('announcement','RyujinOS\'da %50\'ye varan indirimler sizi bekliyor!'),
('discord_invite','https://discord.gg/ryujinos'),
('discord_members','906'),
('gaming_night','2026-06-10 20:00:00'),
('hero_image','https://images.unsplash.com/photo-1607513746994-51f730a44832?q=80&w=1200&auto=format&fit=crop'),
('hero_subtitle','Ekonomi, ticaret ve kasaba sistemiyle dolu hayatta kalma deneyimini, arkadaşlarınla birlikte kasabanı büyüt!'),
('hero_title','Towny\'de Kasabanı Kur ve Yönet!'),
('server_ip','play.ryujinos.net'),
('site_description','RyujinOS Minecraft Sunucusu, Towny, Skyblock, TrapPVP ve Arena modlarını bir araya getiren, mobil uyumlu ve 1.19.4 üzeri tüm sürümleri destekleyen aktif bir oyuncu topluluğudur.'),
('site_name','RyujinOS'),
('social_facebook','#'),
('social_instagram','#'),
('social_tiktok','#'),
('social_x','#'),
('social_youtube','#');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `support_replies`
--

DROP TABLE IF EXISTS `support_replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_replies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_replies`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `support_replies` WRITE;
/*!40000 ALTER TABLE `support_replies` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_replies` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `uuid` varchar(64) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'member',
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `avatar` varchar(255) DEFAULT NULL,
  `two_factor` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_ip` varchar(64) DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin',NULL,'admin@ryujinos.net','$2y$10$e26Lh3gk0t1XYSOPpa5HYO0wJksQIBGnanDpzIdCCsKep.dVyRUm2','admin',8572.00,NULL,0,NULL,NULL,'2026-06-06 19:15:57',NULL),
(2,'loky1454',NULL,'nodernetinfos@gmail.com','$2y$10$YJ6Bc/7Ei0bR2B/OFMrcweKEVkt/0Y8EaqO/hNEvVW1js.xSUamJS','member',0.00,NULL,0,NULL,NULL,'2026-06-06 19:15:57',NULL),
(3,'Notch',NULL,NULL,'$2y$10$t2YdNQa4DFBzB10z8d91/OWYjpUfJ9Z54ekRGmxnCEDqGOI8Bb4Bu','member',0.00,NULL,0,NULL,NULL,'2026-06-06 19:15:57',NULL),
(4,'Steve',NULL,NULL,'$2y$10$GWKcL29pdu8YnEfNppLtJOBYFEpY/eS895GLz.D7gPBSm3vEnE7RK','member',0.00,NULL,0,NULL,NULL,'2026-06-06 19:15:57',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-06-06 16:17:17
