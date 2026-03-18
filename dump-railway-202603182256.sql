-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: switchyard.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_years` (
  `id` int NOT NULL AUTO_INCREMENT,
  `year_label` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_years`
--

LOCK TABLES `academic_years` WRITE;
/*!40000 ALTER TABLE `academic_years` DISABLE KEYS */;
INSERT INTO `academic_years` VALUES (1,'2023–2024',0,'2026-03-14 20:54:15'),(2,'2024–2025',1,'2026-03-14 20:54:15'),(3,'2025–2026',0,'2026-03-14 20:54:15');
/*!40000 ALTER TABLE `academic_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcement_sections`
--

DROP TABLE IF EXISTS `announcement_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcement_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `announcement_id` int NOT NULL,
  `section_id` int NOT NULL,
  `grade_level_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_level_id` (`id`),
  KEY `announcement_id` (`announcement_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `announcement_sections_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcement_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcement_sections`
--

LOCK TABLES `announcement_sections` WRITE;
/*!40000 ALTER TABLE `announcement_sections` DISABLE KEYS */;
INSERT INTO `announcement_sections` VALUES (63,22,3,1),(88,23,3,2),(89,23,4,2),(90,23,5,2),(95,14,11,2),(96,14,3,2),(97,14,4,2);
/*!40000 ALTER TABLE `announcement_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_posted` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `section_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `user_id` int NOT NULL,
  `add_to_calendar` tinyint(1) DEFAULT '0',
  `calendar_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (14,'Math Long Quiz','2026-03-18','Nyark',0,1,14,1,'2026-03-21'),(22,'sdadasd','2026-03-14','asdasd',0,1,14,0,NULL),(23,'asdasd','2026-03-16','asasdasd',0,1,18,1,'2026-03-19');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('schp-cache-chat_history_1','a:4:{i:0;a:2:{s:4:\"role\";s:4:\"user\";s:7:\"content\";s:3:\"Sul\";}i:1;a:2:{s:4:\"role\";s:9:\"assistant\";s:7:\"content\";s:22:\"Hi! How are you today?\";}i:2;a:2:{s:4:\"role\";s:4:\"user\";s:7:\"content\";s:33:\"Give me current students enrolled\";}i:3;a:2:{s:4:\"role\";s:9:\"assistant\";s:7:\"content\";s:372:\"Here are the current students enrolled:\n\n• <strong>Kristin Chine Calip</strong> (LRN: 222222222222)\n  - Grade 7 - Section A\n\n• <strong>Bienvinido James Publico</strong> (LRN: 123456789012)\n  - Grade 8 - Section D\n\nYou can view the complete list at <a href=\"/students\" class=\"chat-link\" onclick=\"window.location.href=\'/students\'; return false;\">User Management →</a>.\";}}',1773835048);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `event_date` date NOT NULL,
  `event_type` enum('academic','admin','holiday','activity') COLLATE utf8mb4_general_ci NOT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'adasd','asdasd','2026-03-14','academic',1,'2026-03-13 17:48:14'),(2,'asdasd','asdasd','2026-03-21','holiday',1,'2026-03-14 13:32:31');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `grade_level`
--

DROP TABLE IF EXISTS `grade_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_level` (
  `id` int NOT NULL AUTO_INCREMENT,
  `grade_level_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_level`
--

LOCK TABLES `grade_level` WRITE;
/*!40000 ALTER TABLE `grade_level` DISABLE KEYS */;
INSERT INTO `grade_level` VALUES (1,'Grade 7'),(2,'Grade 8'),(3,'Grade 9'),(4,'Grade 10'),(5,'Grade 11'),(6,'Grade 12');
/*!40000 ALTER TABLE `grade_level` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int unsigned NOT NULL,
  `subject_id` int unsigned NOT NULL,
  `section_id` int unsigned NOT NULL,
  `grade_level_id` int unsigned NOT NULL,
  `quarter` tinyint unsigned NOT NULL COMMENT '1 = Q1 … 4 = Q4',
  `grade` decimal(5,2) NOT NULL COMMENT '60.00 – 100.00',
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `encoded_by` int unsigned DEFAULT NULL COMMENT 'user_id of admin/teacher who entered the grade',
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_grade_entry` (`student_id`,`subject_id`,`quarter`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
INSERT INTO `grades` VALUES (1,17,1,5,2,1,100.00,'Galing tangina?',1,1,'2026-03-15 17:13:46','2026-03-15 17:20:52'),(2,17,2,5,2,1,100.00,'Galing tangina?',1,NULL,'2026-03-15 17:42:27','2026-03-15 17:42:27');
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_18_083545_create_notifications_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
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
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `policies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `effective_date` date NOT NULL,
  `status` enum('Active','Archived') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Teacher'),(2,'Student'),(3,'Admin');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_id` int NOT NULL,
  `section_id` int NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') COLLATE utf8mb4_general_ci NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `room` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `grade_level_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_schedule_user` (`user_id`),
  KEY `fk_schedule_grade` (`grade_level_id`),
  CONSTRAINT `fk_schedule_grade` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`id`),
  CONSTRAINT `fk_schedule_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule`
--

LOCK TABLES `schedule` WRITE;
/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
INSERT INTO `schedule` VALUES (1,1,3,'Monday','08:00:00','12:00:00','102',14,2,'2026-03-14 20:12:11'),(3,3,9,'Monday','17:00:00','20:00:00','103',14,1,'2026-03-15 17:41:56'),(4,3,5,'Tuesday','12:00:00','15:00:00','103',14,2,'2026-03-16 16:16:30'),(5,3,5,'Monday','08:00:00','11:00:00','104',18,2,'2026-03-16 16:36:48');
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_info`
--

DROP TABLE IF EXISTS `school_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mission` text COLLATE utf8mb4_general_ci,
  `vision` text COLLATE utf8mb4_general_ci,
  `core_values` text COLLATE utf8mb4_general_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_info`
--

LOCK TABLES `school_info` WRITE;
/*!40000 ALTER TABLE `school_info` DISABLE KEYS */;
INSERT INTO `school_info` VALUES (1,'To provide quality education that nurtures the holistic development of every learner.','A center of excellence producing God-fearing, globally competitive, and socially responsible citizens.','[\"Integrity\",\"Excellence\",\"Service\",\"Compassion\",\"Respect\"]','2026-03-18 04:21:35');
/*!40000 ALTER TABLE `school_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `section` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `student_enrolled` int NOT NULL,
  `grade_level_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (3,'Section B',0,2),(4,'Section C',0,2),(5,'Section D',0,2),(6,'Section A',0,3),(7,'Section A',0,4),(8,'Section C',0,1),(9,'Section A',0,1),(10,'Section B',0,1),(11,'Section A',0,2);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('1bTWVGBooKXUwHgGpik1aT8T3kCt3BbyUYCLEmRB',NULL,'100.64.0.19','Mozilla/5.0 (Linux; Android 11; CPH1907 Build/RKQ1.200903.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.159 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/548.0.0.37.65;]','ZXlKcGRpSTZJamxQT0Rad1ZFUjJUVkZLYldSSlRFWTFkMGRFVmtFOVBTSXNJblpoYkhWbElqb2lMelJPVDBOcWR6bDRLMk4yWW5GbVkxaEtTR3QzY0cxeFJpOTNWVW95VWtwWVVUVjZNVzB5TlVSTWJpOU1UR28zWmpSd1NFSnhNeTl2TlhkaFIxUTVTVmN2TVZCRFZHbFhiRUpOWTNKeE9IUkdTMFpuVjBrdlduUlJhbmhaWWpKck5YTXZkMnh2T1hoYVZqbEhRbGRIWlM5UFN6TlBRek01V1ZGMFpXZ3JibUpIYzNKSlkxaE1WMlZyTUZST2JTOTVZbFkzV0RCVkwzTkhiakZLYUUxWmFVMUVSbFY2YWxNMVRVVlNPVzFoY1RKVWFHUnVhVEZCWlVVMGIxbFVaVGcxU2pOWVIwY3lSQ3Q0T0hkVlNFcEhWSEJHWlRaSGNIQm5hM0ZNVlRGUFJrWnZUbFJSWm5ObU0yeHlTeTlhWVhoVFpGcHJTV2QwT0VSMmFsbEdkMXBGYkhBM1NreFlSeTgyVjBsWGEzWktXR3A2U0dzM1drMDBka2hLY205S1VIVnZaVlpNTVVkdVJXZExUVFZpWW1jcmFTdEdUMlZWZGtKT1ZVMVBhU3R6VEZWNE5tTmlRalp3TVdWMU5XRTFOSEZXU0RSSFYwSlJQVDBpTENKdFlXTWlPaUpqWmpsaU5qRTJPRGRoT1Raa09XUTNZemRoT0Rrd05URXdaamhpWkRRek56QmtNV0V6TkRReFpEZGpaakUxTWpZMVpUVXhOMlJtTXpoalltVm1ZV1V3SWl3aWRHRm5Jam9pSW4wPQ==',1773805441),('280s0y92DFVgWrVXLRllrHzXFfkSqS7Efzbzwcib',NULL,'100.64.0.2','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','ZXlKcGRpSTZJa0pvTVdoek5tdHBRa2xsZW1GR1IzY3JiV1ZEY2xFOVBTSXNJblpoYkhWbElqb2laV3hoY2s1eVdYUkRiMjEwU21aMFkxWmpPRVZxZG5WeWNFTklTV3BTZVhkS01XSllRVVpRVVUxMlIxTm9kbUZMUjI5YVkxaFFZa2t4WkdKeVVXTjFaQzlYY0dGWlVXSk9aMWw1UjAxNmVtaHVTVGxrU1VVNGRYRkVOblYwTmtwT1EzSkZUWEJYY2xWdlZrUTJOV04wZDFGbVl6RkhNeTk1SzJ0YU5TODRaVU12VTJ0T1ZqZEpVa052ZWlzd1drNXZRaTlMU21kVU1GcG5ZblJRZGtWRmRtZ3JOa2hxZFdaU1MxTklRV3h5U0hVeFNsZHhOVTVrVm1wUFZGSk9jMEYwYkZFcldscElRVzhyZEVwUFIxbFNjbmswVnpSM00wTjBPRGxrWVdOVlpHRlZkemszVUhZNVFrVXdNalJvVkRJNGJHaDNjQzl4T0dsU1lWWmtNMHhJY1VKT2FEWmtja2xKYUZWWFNGaERNR2s1TTJKMmNWVkxlR2RHWjFKbVlXOVdVemhyUlRkMVRUQklVSEJYT0hBd1ZHZE5RMUY0TWxSV05uQXhlRWxMVUcxd1RXcDBTREpLYzBnMFFubG9OV3MyYTFOWFdqUkJQVDBpTENKdFlXTWlPaUk1WlRSaFltRXpOVE16WWpGallUZGpNVEUzWWpKa01qSTVaamsxTm1FeE5qRTRPVEl6TjJGaVpqWTBPVEF4T1Rka1kyTTVNek15TldVME16RmlaRFUySWl3aWRHRm5Jam9pSW4wPQ==',1773810993),('2XGXssVQE8BJLg8rOv6lmbTL5wtDTQkJy6jY2DrX',1,'100.64.0.24','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','ZXlKcGRpSTZJbU56TTA1M05YTnlUU3R2VkRkVmIxYzRiVko1VWtFOVBTSXNJblpoYkhWbElqb2lSVzVrWTFsS2JHWTNURVY1ZVZsRmQxaHZRVWhpVW10Tk5VWm1SVzR2VmpGalZqaFBRVEZCVlZRNEsyc3hUSFUxTjBVdlZGUnFTMFE0YlVwQmRtWmtXbGxzZVhWU2VFOWxlVGx6U3pkR1pGRjVaVkZtYVZweFZWZE5UbVJTVG05cFVXNVNhVmRzZEVOcVFVeHFZM2xGWjFOaVpVOW5lRTVwUjI5bFdXcHdkV2xPYmpkRWQyRnBjMU53TVRFclZXVkdabXhqYVZrNU5GZFpjREV2T0VwTVJraE9ZVzlsUmt4MmVuRm5VRlJsYjIwNGNFdFljRlU1T1daTVlsVmFPWEYxYlhoSldVeGpWR2R0VmlzdlpUaHJkRFkxUkROaGFEaHNZMUpITHpab1lqRndjRGRaWVZaaU1tMWFaVFJIUVVSdmJISnJjM0V3TUdZd1NGVjRjbHB3ZG05WlpFVjFRVEp5TkdkRmIwZHpTR1JQUlV4Q1ZqbEhRM1l3YW1sRmVXbzFlbnBCUlU0eFVYY3JaVVZvUldSTlJVNWhlRGN6VTNoTGRHcHZhVkZsU1RaR01reHpPRFI1WjFkQlQxcG1kREJOWTJWS1IwVXlVbEJ1YkdVMGJHNVlURzVhU1dad05sVlhPVzgyVlhWTFVIRTVNSGhSVm5OeE5qVjNWUzk2TVhsSU9GRnViRlJyY2tGc1VXMXNiR3BHTWpGSVFubE5XVkpNY1hCaVRXODJOV2xOTldVeGRreE1ZV3R3T0ZocE1XeERhMlkwU1dOTVJFSmxZbVp1SzI0elNVcE1iVXRNYkhSdmFqRjNRbEZ1THpBMmFtbHNXWGM5UFNJc0ltMWhZeUk2SWpWaU56WXpaVE13TkRWa05ERXlOV1V5WkRoak1tSXlOV1UwWXpKaVpXUmxORGN4TjJJMk1ETXhOV1pqWVdWbFkyWmtPV0V3WkRFNU1HWmpOV0psTnpZaUxDSjBZV2NpT2lJaWZRPT0=',1773808317),('7bYHWKZKgfKafLrVT2FvcnoEFSe4TN5uUOi8uV3G',14,'100.64.0.2','Mozilla/5.0 (Linux; Android 13; 2201117TG Build/TKQ1.221114.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.159 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/552.0.0.44.65;]','ZXlKcGRpSTZJazlpUWtnd2FDOHpWRzlDTlVoSGVYcE9hMGhOTjFFOVBTSXNJblpoYkhWbElqb2lLekJvVFdJdk9XUkJNaXQwYlVSMlZXdDBNRGN6VjFaUU1EVmpXRlpQWVRFMGJWaFRaMlUwVERGQ1kwVkZNVzlSWmxWVFkyVmtabkY0UjNKb1VFVlZiVU5vVlhZeVkzVnRPR2d2WkZSaU0wcGpNemRoYW1GSmFVZDJaVWN4UWpNNU5sVlZNR3h3WkM4MmFtTTBhRFkyY0M5WGEwVkhXV3hGUkZGUldtOVRVRGhZTjJWR2JXa3habTFtTldGNlJuWkpXRE5WUzFaSVNHZENkRGsyUVV4c1NUVTFkU3RoT0RGa1RrWXhiMVZFYlRaUVpHMXJSMlJHVXpCUFlXOW1ibk5xUWtoSFNrMVJUMjlCUlhSS1JGUnVlVU5GWldsNk5tRTRZWE53VEdGeVVVeEhjMGhZTlRJMU5FSnFja2MxU1d4U00zZFBUaTkyUjJ4UFRHTTNWMUJUU3pSSGJYTlJXRGxhT0VsSFJHdG9Va2xQWVdKNWR6TXZNbk5uTVVWRlRIUllTVFZDT1dOcE9FRnhZMVp3WmxoR1praE9aVVZNYkdORFYybzBiM1owUjBVclREQlFTVXcwT0haTlF6Z3hUbGhqVEhaa1pYQmFOVU5sTTNoRE1UZ3dTekpKY1cwNEx6UlhlVWgxZWxsWWRsaHVObWxIVURWbU5VbHphbWRLYkdwSFUwOUlibFkyYXpkck1YVmtOM05xU20xSFVVZFdkUzl2VFRodWVDOHhaVVp0TDFNNWRVdzVkV2R2WXpkcFNWSnBTVmxQVVRWRFYyMU1SMlp2Y210RFV5SXNJbTFoWXlJNklqWmxOakV6TnpCak9HSmlZV00xTVdZeVlXRTNabVJtTldZNVpqQXdNV1l4T1dSaFpETmpabUprTUdZek5qTmlOVGRoTmpBM01UZzVZak15TkdSbFpXUWlMQ0owWVdjaU9pSWlmUT09',1773807291),('9lNAlEGuQjlSVFpjqVDjGcarTkcuF0zNuv4K6cCP',NULL,'100.64.0.15','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJbFpQVFVwM00xWjNRMlYzZDFWb1N6YzFLM0FyUVVFOVBTSXNJblpoYkhWbElqb2lSVlpsWW1SbVZVeEpRa3g2V1dobVRHeEdaMWhRYWxCeFIzSTNaWEZDU2xKMlJYUlhZa0YyTjNwQk1rRXJOR0prYUdNNFlsTmpVVm92ZVRKSVdYRk5LMW9yT0dnNVZXNVFZVnBoZFZCSUsxZDFWbkJNYkZkaWVHdFhlbU41T1VoVFpXdE9XbkJUUkhkRFNsTnlUalIzTjA5eFdGSlplRFpHYmk5d1RrVndVamhpTUVwaUsyeHVUMncwZUhOSlVGZEpha3RzZURaUFNrVXdZa2NyWm1KUFpERnpUM0pQZVUxMmVVMHJlVkJFUzBWSmVraG9jbE53TlVVck9UVnlRVUZNT1ZKdWFscGFZazFYTUZscmFuQmFUVVZNUXpKbVRrOTRVVlIyY0dJNU5tUlFWV2h0WjFwcVZXUklkVU0xT0M5RWVYUXhiME5SVjNFNE1Xa3ZNV3N2WlVGd01FUmtVbmw1VWs5UVEyZzJZMUpzUjI5NlkxbGhhWFp2ZGxNeFJ6WlVkMk0wTm5oQmNIazVjMm92T1VaNEwwNHhiSGROTkVFd1YzcDBXVWsxY2xWQ1VXMVBLelZzYVNzdll6RTJhbXMxWjBOQlRuTm5QVDBpTENKdFlXTWlPaUpsWmpaaE1HTXdNakl6WkdKa1pEVTJZVGcwTkRneU9EWmpOemhrTkRJd056UXhaVEZqT1dNM1pUZG1aV1pqTURSa09UQXhPREprWWpFNFpXWTRNbU5qSWl3aWRHRm5Jam9pSW4wPQ==',1773805283),('Bcnz2m88FX9TCqi8uUMrsT5xtu2gL9hAw8TKfCfW',NULL,'100.64.0.7','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJblJTZDFSdGVXZFVZVWhsV0N0a01VbEViMEZoY1VFOVBTSXNJblpoYkhWbElqb2lOM0YzVXpOTk0yNWlOMXAyTVVJelMzbHZURXBsU2twQ2JrNVdjakEwUmpKU05FNTVSbUV5WTFrMFpXbDZhR2RuVERaSWQySnFSaXRhTkVkTE1Xc3piMlExVnpCUWVVaFJUemc0YlVoMlNWZFZORmh5V1hsSE4yOXpUQ3RpUVhoMFVrTjNjM05rVEZoUFNERlpZMFJCVkdSQlVFTmtXRWR3TkU1WlNHdENlSFJ6VGk5Q0szQmhUMjlqWTBSNlQyODVXbGcyUVdOQlpsaDZVVFprVkRCcWNqa3ZSV3BEYzFsT2NVcDZSbEV6VGsxSk4wUnBXVFYxUVVGd1UxTTVVR1pKY0dwME5EUllaRXR6UzFKdGQwbDRUbmRaWlVzM1RIbDNZVTV0VEZCellYTnJaSHB6V0V0S01qZEhjMHROY2xkd1YwdzVWVVY2YVZKdFlraFRUV3RJYjNjMVNXaElNa1pFVUROcmFUZHRieTkyVGs1TmN6RnZkWFZoT1VWWWVsTmFTVTVzTVZrM1ZqVjVLMEpDZWtGNFNERjBVVXgxWlRoT1JETlJUbXRQU0hGNGVrNDNjVXhSV2xaYWVuQkNhSFYyUzNoek1YSjNQVDBpTENKdFlXTWlPaUpsT0dabE1URmxPVFkwTVdOaU1UbG1Nak01TWpabVpURXlOVE0wTldNMk1UVXhOVFprTldReE5EYzNaV05oTW1GbFpUY3pOVEJsTldFMlpqY3hNRGRsSWl3aWRHRm5Jam9pSW4wPQ==',1773805241),('bZFIbCntvdBORVRVKpHNKjZtB1BSiZ2Br4hNWzhz',NULL,'100.64.0.4','Mozilla/5.0 (Linux; Android 13; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36','ZXlKcGRpSTZJbVJDYkdSUk9IQlJWelZVYjNSWU0yRkdSRlpaV2tFOVBTSXNJblpoYkhWbElqb2libFZ0TlROcFRtcHpVR2RMTkdoQ2ExbzNXSGN2VW1ZMUwwbGlVRXBVYUUwdlYwOTJkSE5EYkdkVlMwbHdTVUZFV0VJMllXNVNUMW80VVZkRFVrNDBLMFJCYVdnMmMxQmlXbWx6WjNJNVkzZFFhMlpoYkZjNVpUWmFValZ0VkRaeWVEVlNlSFJuYmtRMWJsTnNlRkZoZDI1T1lXTktaRkl6UjNCSFZEZDVRWE5oU2pneFduZGhlVlJuV1ZScGRVd3ZTazlWTlVGTVlVRk1PRFpQUTBGMk5VWlFTa0Z1VDIweWJYbzFVeTlaVGtvdmMzZE9RVEJXVDNWUU16UndZbE14T1VKd1VtUnRiazlIV21SWFEybE9XbUkzUzFwU1JsQnJibXBoU2poVmJVdHZZU3QyU0ZvdmJXZHdXRk0wUnpkR0syTnFTbEJTWkU4M1NHUTFha2hhSzJ4bWQwcG9lbk5hTUZSMWRXVm1TWEF5WmtOalMycFVSVlEyUkc1NlNYQkdRWEJzTDBFd1lqTkxWV0VyTDJ3NE9IbEVjVWgyUmxCaFFUWnhLMDE0YVVjcldWaHNOMVp3VGpkSkwyRjJhbWxTVWs1NE1VVkJQVDBpTENKdFlXTWlPaUk0TmpnMU0yUXdNamRtT1dKak9XUTJZbVl4WlRReU5tSTVOVEE1WWpNM01URXlPRFkxWlRJNFl6TTRNakZoWXpoaU16YzRPVFE1TW1SbU5tWTFNbVkySWl3aWRHRm5Jam9pSW4wPQ==',1773812267),('dBxclVLZy8taebXAcFmCd9yxZkFzwssGbEDLAwm0',NULL,'100.64.0.5','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 OPR/128.0.0.0','ZXlKcGRpSTZJblZEYlhSQmNtbHljMkpHUjBOT1FTdGpTVVJhWjFFOVBTSXNJblpoYkhWbElqb2lVRGhzY0dsSFpWQlVSbTU2VkdoTVpFcENVVWQyWjJJMlVHbFNhbTk2UjBkd1l6UldRVmRTTVN0VVRteFdUekZ0U3pkbFRUaHdOMDFRUVVJMGMxSnVjMkV6T0VRMVNGWm5UbkJqY25OYVVVcGxibVpITDFwcWNEQnJWMnBHTldaaFVHNXNNV2t5UlhwcFpGSjFVazg0VFhwaGNtaDJhM0kyWTB0MVoxZHJURTFaY0VjeFowaDVXSEV5ZDJKVWMyMW1Tak5UT1cxQk1HTnllWFJrUWtkb2RVSTFOamRYTm5OWlJXRnFTVTFHTW01NmFscExjakU1TVU1MWFEaFpMMHhNWjB0U1kwRXljRTFDTUZCMlVHTjBOM1JrWkVkWWNqZ3pRMFZ3Tm1kYVdHeHdORFI1SzJGRWRFZ3dVREpDVkRsMlNreFFjVGxETVVzd2QybE9iRW95V1RaQ2FFTTFNWEZITlVkb1NscHVOMEl5TDNKck15OXVMM0IxUVROSFoxSXhVR2x6TDNOR1Rub3hla3h5ZHpsaGVGRnZkMGhtVUUxNWJFNDVTV2RRVkVabFQwTTJReXQ1VjNwNVJtTnhUV2xaU2tnMFdGQm9RV2QyUW1SQ2JHNTFVM2h0TlhGSWJqUnFXbEpQUjBkNU5IZEVPVUV4VnpZMlQwdEpkVWRKSzJKemRtZFNibGtyYlZwb2MxSmljREprZDBSS1dYSXJabTR6VVd0UWEwZGtSVU0xTUU0MVJUTXJSbkV4ZWl0aVNEZFpja29yYW1WWVQyZGFlWEZOTHpSNWQyczVlaXRUUTNST0sxVkpRbXROTlhOeVJFZHNUV2xoYzBkeVlXbEllVEp6Y1VsSFoyWlpMM1p4ZFd3NFNtVXlNMVJpVUdaMFlWY3dVWGh4Vkd0VFkzbG9aRTVEZGtZd2Vub3lTalJSVlVnM1JGRkdhM0ZZZW1kYVNFOWtaMnhUT1ROcWQyOTJSVWRHWjBadk1tdGFSVVZXTUhOWVYyWnZiRXBIWlhGTmRXRndTR280VEhVeFdXZ3ZUR1ZDY1M5bk4wbzNSemRKWnowOUlpd2liV0ZqSWpvaVpUbGpObUZtWkRJeU56WTROREkyWm1GaFptVTVNRGRtWlRFeVpXVmhaV1V4TXpFd05tUTFZakJoT1RRMk1UWTBOR1psTW1ZeU9UTTJZelV5TkRWaE9DSXNJblJoWnlJNklpSjk=',1773843851),('EbVyCBKjkoaZM8HvgWnSglSJ9xPZxeL95NOQ6m0A',NULL,'100.64.0.8','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJblp3U3poa01rOTJNMnBUTDJrMlZIQkRXbllyVUdjOVBTSXNJblpoYkhWbElqb2ljRlUyVjJNeGRUUnpaRE5pZDNKbldGVlpPVk5OV0UxWFIxTXdlalpQV0VOUmIwUnpiRlpSYlRSWkx6WlVPVk13YkVoUWRXNWxUVzlRYUdkdlV6bHRVR1I2UWpOeFZHOVNUMGRhWkRVdldWaE1WMGxQUTB4QlFVTnNWRmhXU1RCamMzWlZjVTl1U2pWTVUxWlhhWEJZYkZSSGVFOHJNV3A0UjNwRlJHcExkV0ZVS3pWQ2FsUkZUR0lyWTB4a1IzWnZjM2xFV1hNMFRsb3ZSbVpyUVVwUFJITk5LMmxUYldoRFVqVlhNbUUyVTFadlRtRTBhVFJoVFRoRVZXTmpLM0J0UldNME1HVXdSRVZ4VkVaU2IxbFdNVzVaVEdGQmFuWnJjMVpqWkM5UE1VWkhkelZuV25kYWFuaHRVMGhxWlZoVmMzZEJNR0Z5T0V4QmEyczBSa1JZV2tsbVNHRXljazl1Wm14bGJYSkdNRlpETkM5UFNFdHlLMUpYWjBaSU9EVjRaVlZLTlV0U1dYZGhTazVtVUU1eU5WRkdkbEJaSzJ4VmJIRnpTRGhGTDNWSUswdHJlVkUzVEhaek56Rm9PV3BxUVVRNWRrRkJQVDBpTENKdFlXTWlPaUl5TkdNMk16SmlObVptWmpnMk1USmtNRFl5T1dJMk16VmtabUptTVdNek5UTmxNakF6TUdJeU56Y3pPR0l4WVRsbU5XWTFZMlppTW1FM01XTTVabVptSWl3aWRHRm5Jam9pSW4wPQ==',1773805242),('Fy82GxunaWNUwAEAgZ8iet1o46BJVNvT4IitgbRf',NULL,'100.64.0.5','Mozilla/5.0 (Linux; Android 13; 2201117TG Build/TKQ1.221114.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.159 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/552.0.0.44.65;]','ZXlKcGRpSTZJa1JGUlc5MVEySmFTWGRoUTNjNFYyRTRNRXhFYm5jOVBTSXNJblpoYkhWbElqb2lPVEZuVjBsWmMwNUZWRTlwWkZrNFJreHViVVpCTkhSRWFuWjVXVzlYWTFKMVIyaEVSVTA1TmpoNWJUQmpURVZVZGxOQ09HdHFSVzA0TURGM2JWSXhPUzlJZFRRME5uQktlbmhaTUhBMVVVbGlVekZhZUc1TlpWWTVNbmwzVDNWSVozUkpURFoxZFVGbmVIaElNRzVGVlhWdFdqUnBaVEZTU21nNWFXZFJkRFZyVEhabGJUZzFhRk5FUkdwb1ZTdFJSMEZCU0RWRVNFeE5RV2wzY0dwM0wxbFNSWE0wVkVVNGNWWkpiRmd4UldkNE5YbDVUSFF2VEZsamJYQXZWVk13TTFOTk9FNW5NR2gyVTBjNVUwOTNNVTV1U1hWUlR6VnBiVTVaT0VWRFFWaExjQ3N3VWtwemJsWnJRbFU1Wld0TVZreFZjM1paVWxsbE1VVnBhRmhKU1VwNWJFeHhNR2RtVDNvNFdURkZkVkZMT1c0NFJuQnlaV1JaWm1FM1UzZHNaa05tT0hZeUwzVTVVRU14TTJNeFVrWjBTR2xJTkhZMlMxcFJjMlJZWlRWeE5EbG1VekJpT0ZSMk5sRkpaMVl4YzB4eVJXMU5UMjF5TmxOcmMxcDNNakV5WkdkWE4xZEpRMXB2VEcwdllUQmpSVE5GVlhWREwyUlBTMVJJY1hkcE0wSmxaV2N5ZGxKeFlXMDNXVVZSYkVnMlNtdFdhVEpJUkU5emFrcElWV2N3Y0hoaWFIQkRla2g0UzJSWVRXOXBSblZCYW1GWWRFTmFMMlpuU0c0MmExaHVhMnQwZGs1TVEzWjJla2wxYzFseU5EbDZWbFpDZUZFMWEwUkVPUzlpVm5aMlJIZ3dTVkpNV1doeU5TOXZSRTgzTDJnMmEwdG5UR3RzTnpGRlJVRlVWRlowVkVsUU1uZFNkR2N5U2k5NmRHczRSMkpuZVZvM2FWbzFjSEI0VkhKNFRUQnNXaTl2WjJZNFBTSXNJbTFoWXlJNklqazBZalEzTWpFMU5qQXlaakV5TnpWaFl6VmxaVEk0WlRjM09HTTBZbUUwWmpReE1XVTBOamcxT0RKa01qSTBPV0UxTUdJNU56WTBaREptTW1Jek5UQWlMQ0owWVdjaU9pSWlmUT09',1773823391),('fzKfvx8on4pg7fpFahmXuagRAzDUBhj2BUizXzhn',NULL,'100.64.0.6','Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1','ZXlKcGRpSTZJakJDUldjMVRURlBhM1phZFdORlVWWXpUMDF0WVVFOVBTSXNJblpoYkhWbElqb2lOWFozVld4Qk5tRTNiVzluVVhacFFYQjVlRkpxT1dwbGFWZE9NbU5OWm1wdmMyRjZiak53TWs5WFRqSXpOVzA0ZFRocFp6bEVWMXBPVmt3eVdVNXJTVEJoTW5sTmRYWmFRVEZ1YkRRek5YUklWVmxtUWtONk15OHpaQzltT1dkc1YxbE5aMHRqWW5VdlZHaFpiaTlHVGpselNFNUpTbVZuWm1sbFNFUXlXVEJFZFhNMGRpdElXVTUyVFU5VE0yTndabmhuS3l0UlYybHJjREJvYUdvMWJtdHhURGRDZUhkMlNtdFdjRnBKSzBSVE5IQXhRMWRpUkZGdE5XeFFVbHBPYWxVeVpqVldOSFZFVG5KS2FubEdjR2RZT1dsVEswOUpkVTB3TTJ3eVJYQTNjelpFVlZsU2NIVk5WM2RZZVVOdVQxaHhTMWhzZVV4VFVGbE9iRU5zZVVSMU1XZHVWRFF4UWxneFUzcG1ZMlJsYkZBd2FuVkpiMHBFYWpCellXdGtOa3RTU1hoTlJUaG1WRmx5VFdSWFNqWXlSRlJvVFZWV2QyZHNUM05WYjJ0ellqRjJVbTQ0TkdSQ2NsSTBOVTByTVRGVFZrSkRXVXB6Y1VselNFWjZPR0ZuYlRkblprbHNRM2s1VVZGclQwVkNSblZTVDFGQmMxRkJOMGc1U3pGNFUyMXhVVFphZG5oek1XSnpjQzl1SzFWV05HMXdaVEZKZGxSQlEzUXdPSHBzTjJWcWJuSnlXVmszUm5oU2FrRXhlazVDTTI5cVZsaEhjVzE0WTBSbU0wOVpaVVpXVjBWUk0wTlhZMkZDVEN0UkwyOVlNSGh4U0dOdGFqVjRkbFV2V1RZeFZtbDNXbTgyTUVGamQwdDJWbHBYVG1WMVJIbEllamx3TDFjNFUza3phMUY1VmtzcmRqWXZjSE12VlZoVVJFMVFZMk5SUFQwaUxDSnRZV01pT2lKaU1XSmtZak5tTjJZMFkyTTJaVEprTkRGa05UVmhaRGc1TjJNNE1ETTFOVE00T0RBNU5qQmxNRFJpWVdFNE5EUmhaVGN4TXprek9ETTJNVFV4WldKaklpd2lkR0ZuSWpvaUluMD0=',1773805239),('GBxWV7VNPaQFpq15nhHw2vQela6UW5EuOXxZerZK',NULL,'100.64.0.4','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJalJOVTFaYVFXVTVOVkJGYmxSbkwydElWbXh0WjFFOVBTSXNJblpoYkhWbElqb2lSa0kwYlVKTVdXNHpkRkZYU1d4eFVuTnhWbWxFWTNwTmQxQmhhV3RPU0VGcFltZ3ZNbmhpWXl0WFRYVlVUa2MzYlN0TVdrNWlWemwxVFRWTE1YZFJXVWc1Y1V4T1ltWldUazVEVEc5dVRFczROek14VkhveFlreFFUMEUyZURNeVZWcG1LM1F4VG1KRVYwMVJiMmc1YzBaRVJFUkdXR3B3UzFkSU4yNW5VVVpKTDBkSUwwRjJja2x6ZEM5NU9WZFBhSGhJT1RCUlRYVTJhemhxYzBOcFQxQnFSbkpzYTFKT1FqQmhTR2cyU1hWbk0wZ3JWU3M1Ym5wSmJ6UTBkM2N5U2toSlNUYzRTV3BrTURSM1JFdzFkbTlpYjBsQlNUbElkSEpHWm0xeWJWRnFSV2N4VURWNFFWUnNhMk41UTJnd2N6aDJiMWQyVDNoM1J5dGtVMlZOWkhReFZGcE9XVkV4Y0dGbFFrWkxXa2xETVdwcVpqRmxLMDgwTTNkM1VtSlFiblpHZGxrM1lrOTBWMUJ4UmpkQk1UTlFMM05QVlhSU2QwTlZUVnBVZUZSdmVqUnlNMlpDVEhKT0swZzVWblJsVW0wMVpIRlJQVDBpTENKdFlXTWlPaUpqWXpReE5EVmlaR1poTWpkaFlqRmhObVJsTTJGa05qYzVPVFl5TlRGbU1qQmpaVGs1TW1ZeE16UTFabVkwTm1FM01tSmhaREV3T0dGaE1UVXpZVFZsSWl3aWRHRm5Jam9pSW4wPQ==',1773805241),('ICeDA9TCLJPp1uCUgvEKS7ubhu5f0ejwWK4tkJbQ',NULL,'100.64.0.5','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJbVZzZHk5WVlVcFlOMnQyUjJoSlQzaFdibHBuTlhjOVBTSXNJblpoYkhWbElqb2lhV0pHZVRNeU9VRndlVzQyU0ZkMFFURXdlWHBtYURscllrNXRXbWhZVFhCNWFEbDNSMUE0YUhONk4yMXZVR1V6TjJ0TFpuUXhhVFJrVWxGVFIyaDZUbEpHZVU1RVZXbFpjVmxYVkRFd1IzZHdXSEkyT0ZsSGVIbHBTVXd3UVhwbWVuWmlTRFp6VFRGVWEyMXZhMHBNUTA1MFRFRldWRzlaSzFsNlVtUklhbWRvTDFkTlJUQlNXbFkzT0ZkTmVUaGtibVE0TlVscVFqUk1PRVZJZEdGNk9HTXJkVlZ1V1RoSWJuZFpaakUzT1hCTGJYTm5hVnBzV1ZGSmFXcHhOalpsU1RsV1RqVlJibmxRZW5GMVJraHhXRkJ5ZGxGQmNHSnFTMk52VDFwMWJVcFhiMWMzV0N0NmJFWXdaalpFWmt4elVWTkhVbUo2SzNkSmVYZExkbGxSSzFSaVRHSk5SMlZUUWpSYVlqVjRhVFJQTTNWTVEySk5VVGRPV1VvemFsRnFXVmRvZEdGbmRGVXdVRVkxT0VwdGQzbzBSRFJSZVhJMU5rdGpXRVpJYjNkeU5sbDJRVGhCYTBkbVNFeDZXVVYyU0Rka04wVm5QVDBpTENKdFlXTWlPaUl6TkdObE9EUTFZV0l4TlRVM1kyTTROemd6WXpkaFpUTTFNRFF5TkRVeFpUWTJObU5tWWprNVpqbGxNR0l4WlRjME9ETm1ZMlV6T0RRMk1ETTNZall3SWl3aWRHRm5Jam9pSW4wPQ==',1773805233),('JbVCZDPixedhgbmPIiDQ60YLipmIl6akUfQ2JCui',NULL,'100.64.0.12','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJa2N4V21weFQyUmhjemRYVjNsdU5qYzJkVUUyVEVFOVBTSXNJblpoYkhWbElqb2lUVVIyZVdFeVFWbFZRalpOT1ZoVGJGb3ZNelpTV20xT1JqQjRlRXRPWkU1WlYzbFZRMDF0U3l0T09YRjZjRFpKWXpoc0wwRnVZbEF2WjIwNU5VOVhXbmh1Y3pVcmJVUXJSRVphZFdsNk1VcHZVRzV2ZUVST1oxRm5kM1ZpTlZKUlFUUnZablYyV0ZsRWRXTjRkakZHYVRKeVZVMVBkemx3Tm5OUU1VNXRUVk53VkV4Nk9WQlZPRGNyWTA5VFluZFdhMXBpYUZVeVZqbEpSRkZ2WkVOVlJUbEliVWhDYTFocVptWTRVVGMxYVUxTlpVTTVlRnAzUlRBM1VtcEdObkZuUjI5SWFUbHRabU5OV1doNmEyVjFlVWRVWVVWM1R6Y3JSMlJrYkVkdE1HVTNibkpIVjB4R2VWVnVWV1ZOTkZacU1DOWxlbXM0ZEVwbVJHVnBZMlphTkRWM2FFSXdTSEJ5UTNCUVVVdE5aRGRJV1ZCSmEzQm1OSEZZTDBOMWMwdHpNMHBHWjFGMmFVeFhVVlpVVWtaQk9GcFdTMmhGUTBaNFEyOVBhbkkyYkUxNFR6aFFLMjl5TURaNWFIVnFOVlpLTkU5SVdGcG5QVDBpTENKdFlXTWlPaUptTVdKak5UVXdZVEEyTnpjNE1tSm1NRFZqWlRJNVkyVTBNRGRsTURsaU9XUmpNR0ptWWpRd01HTTNOamRqWTJOak9ETTJaREkxTm1ZMVpHUXdPRFJrSWl3aWRHRm5Jam9pSW4wPQ==',1773805355),('ljWrpldIAG8UxubymbJsQJxTc6KRPhYAS7Ez3Rvz',NULL,'100.64.0.3','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','ZXlKcGRpSTZJa1pOVmpNelprUTRSa3M1ZWxGQlptODBlRUZpWW5jOVBTSXNJblpoYkhWbElqb2ljVVZIZVU0MVoxVTFSVzlLWkU1UmVqWlNjM1Y0TkRFeFFWSTFaVXR6VlRnclRGRXZhM2hhVGxScFR6Qk5aMDE1YjFKWk5VVjNiekZhVVd3MFJHSjNLeXRTVXpFMVFubFZRVlJwUWtwaFNrMHZRbEJZYjA5SU1IaFViMDFhVlRsV00yZExhRVl6ZFM5cFlYcHVla0ZHUW1GVFoycDVTVUZ1T0hkNFdqZEdXakZYV2s5U04yUlNTV051WmxORVIydFdlR2x0TmsxUUswcE5aV2t6T1RSUlpFdHRNRk4yUmpGM09EZDVPRkZhYlRsck1HaG9VMEpWUjNKU09FMXpka1JaYUZVMWFHRlpkWFpDVkdvNEt6bFVlbFkzWjJsWlZYaG5jbWh1V1dKd1VXdExUMUpTVEVaNk1qVXpiRUk1VkU1b1RtSkVZMU56YmtJclFXMHlVRU42Unl0SWQxZ3ZZbEpzZFZSa0szZFJWbGtyZERkTlZXRjZaR2g0VEdwRE9HRkdSVVpuVlcxS2VFMWFiVmRHWmxsTU1FZFRWakZTVUROWVlVRnFaMU5WWkdJeFZVOTBaMGhWYms5dGMyaHpaamwwTUROaGNuRjNQVDBpTENKdFlXTWlPaUl6WTJaaU9UVTVPRE5pWW1ReU5EWTNOVFEyWXpsbU1USmhOamRqTjJGallUYzROVE0zWTJWak9ESmpaVEJsT0Rnek1qRmpNbVUwWWpJeE1tSmlOemRpSWl3aWRHRm5Jam9pSW4wPQ==',1773829013),('RpCn43ral7Gaq09igSBHoPjKfufCHxLkoyaApDUM',NULL,'100.64.0.21','Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36','ZXlKcGRpSTZJbXRRYURsMlpEQlhkbEp5YjA5MVZrWjBiVTVNYTJjOVBTSXNJblpoYkhWbElqb2lVakYwVUUweWVHaENaa3hCYTJzclIxVnNhWGRYYzFsVlNrWldOWEl2TW5CcmFWbE9ia1ZWUmtwc1RubEtTbE42U3pBd1pVTTJWa2xsTTJoWWJUTkZXV3hVVGpOUWFsTnNRWEJ3WlN0aWEzcFVSVGhGU1haMVpqbGhLM0IyT0c1RVRFc3hjMjhyVFdKUGNqZzJRV2xpUW0xTE1pdGpXSEpJVkVrdmVYZHJjalEyZDFKSlFXSTRiblJQYzNCV2MwVnlTMDE0Y1Znd2IzTmhRMjl1WVhsMGEzWmtOMUZCVVZsSlNFczVWbmRJUkc5R2QwUkpPV3N2Y21KQlRsZE1jSEF5VmxCb1JYSlBPR2hLYTFkYVFXRkhkRlYxYW14cWJWSnFUVWhDYUhST1l6QndPVXhqS3pkRmVFVm5ZMnN4VjBJM1ZqVlhiWE5pWWxKRk56SmFTa1JxTTJkeVRHSlpaRWhEYXpSbmExWTRhM056V0hwUE9YWXZUREo1ZFRNMVRIVm1XbUZrTTFkVGJWTXJiVUUzU1cxRmFuUnpkSFJVU0hSSWNUWXhaMWRWYVVOWlkxaDFjblZGWm1rMGVqZHVaak50ZDFGWE5IQTBZVEJxVlN0UFFVVnZNbE5PWmpsak9ISnVOR3B5YlhSNE1XWlZNMDV2TmtGa2VrNU1TRFJ5YTNZM09YUTNWREZwUWt0VVJuZEVXSGt6VmtWbVRITlRaaTlTVEV0cFRrVjJRMUI0TWxCaVQxaEVaM1puWTBzM2RtTnRWVmh6UlhBelpIaEJRU3RVUXk5bFdUSmFhVzB4VDNwR2QydGxOR0pVZVRKNGJtUlNjRTlwU2twcVFtVnNkR1JXVEUxRmNYRjVZMnRDVmtVOUlpd2liV0ZqSWpvaU16Y3pZMlZrWlRVM1pqVXdOamxoTURnelltTmhOVFEwT1dJek16bGtPRGczT1dNeU5UUTFNR00xTnpZNFlqUmhZemszWmpjNFl6QXdOV0UzWVRVMVlpSXNJblJoWnlJNklpSjk=',1773806059),('rUkSOlwDaJ4GMCXRfQlZu1QwQjoWozPEefYpeer0',NULL,'100.64.0.2','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36','ZXlKcGRpSTZJazVDZDB0Vk4xWXdTRTlrY0dSdGRIbzJhblJIT1VFOVBTSXNJblpoYkhWbElqb2lURWd6T0NzeWIwOVJaWFpWVEV4SlIxUkJiVk15WjNOblZFMDVXRXMwVURKT1Z6RnNTa1pqVGxwbGFEVnVkRGxhUkhaUWFGVlJWVUZFUkZWMWMzY3hiMFU0VmsxSk5VdEZaaTl5YVZSMlFsSmhTMWxSYkdSVlQwaEJkV294UmtSVE1VOHhNR3RsYjBsTmVFYzBiMHdyWm1Vdk4yeEdLM0EzYlM5c1Nrb3ZWblZrY214Vk1YZERXRTVvTDJGTFRubDBOWGxoWXpkMlRsSTVkazVQZVhwcU5FNUVOMWxDU2tkeFNITjFZMUJSTWlzcmNGUnRTRmhhTlV0c2VsVkZSRlV2TUhCaU1FTnZTMHRKYmtWTGNYVkhUbXBUYVROdVRGWlRPQzgxYlVKM2FrbFJlVFJNUlhJeU5YRXJlV3BTU0dWbk5sZEVUMmd2YTBrNE5HWlZiak5DZDIxQ1puVllRek5yU1RRMVdrVjJRVUpZYmk5Q0wyWk9keklyZGtWcVZHODBORFZaZFVOc2RXbFBOak15Ym1oVUsxVnVjM0pzYkRCTFEySXJUa0V4Y2l0amJUaGxlSEpzYWxkMVZrSnVUMGxYTDNweVRXdDNLMEZSV20xUmEyVjRiakYxZDB4TlJ6QXdTV2xJYTFkaU1scFhMMjUxYkhwME16VlFSV0ZuZFdkNlNESmhNSFJ3Tm10VVIwbEJRVXBMT1RVcmEycHJRWHBYTWpGaGFGUjJUR3h3ZVdONVQwMHdiVU54VUZodldHNUxja3MwUlUwM2VtVkpOR1ZaTUN0YVYyMXdlSE5HWWpaV05qUjJjWEl6V1hoeFZrWjZXbVU1ZDAxM09VRXpOMjVDWmxCeFZ6VkxVakpKYUZabE5HVXpaelpYUldkcVpWZ3pORE5zUjA1YVNua3haVGRhU0cwdlZsTTRObVJvTkVkdGNrRlRXbEpuUFQwaUxDSnRZV01pT2lKbFpETm1PVGxqT0dWa1pETTBPRGs1TXpJMk1EWTFNMlptTWprNU9URmhaamxrWXpZMk1tVmhaalEwWmpOaE9UaG1NalJsWW1KbFl6WTJPRFJoWXpJNElpd2lkR0ZuSWpvaUluMD0=',1773805238),('TqFo4nzxMIB56gBNvDPeMxiLxzvHZ4chwH3m0nhz',NULL,'100.64.0.16','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJbWR2YTA5clExWnNVVEZzY1RGVVVGVlBVSEJYYldjOVBTSXNJblpoYkhWbElqb2lkbTE2VkVneVJYWnVUV2haZDA5MmVFSkRZa1Z5U3psRmVUTkNhMmRqVUZrNVJEQlZTVEowY1hVNFp6azJVVFpYYVVSWWFHaEJZM1kwWkU1bWQyVmpOSEZUSzFSV1JrdDNOeTgyV0d0MmNITnplR1UxZEdZMlRUaG9NMUJJYjI1VlFuWm5NWFFyTWpWSVlsRnVTRFExYVVNd1lUVkphWEpDY0dOMVYwaGFhRUpZU21oUVkyY3dhVVZxVWtaeFpqWkpNbGdyVTJ0TVkyVmplVWxIV0VsSlEwOVpRMHBDUlVodFVqUktZVXRWYTJsdk9EVkphemxJU0hGNGJsRmtiekpRZEU1aVptOW9jWFoyYUhVeUt6ZzFjSGRDYlhRd1kzcHpaeTlCY1RkUk5rbzBWRkpGUldsMWVVRkZZV2c0U21SQmJrOXdjRGQwYnpCb1NITXdkMmM1VVdOQlZGQkdaRFoxUTBad1QyaHlRbmxvVXpBM2NIazJXRFZhYkZSSVprTkRWbnBPWVhSWVZrcFBlRVZqVFZkT1ZWZzJWV2xITVZrclNIaGlaRnBJVG5jcmJVNUNORFZSUTNaQ05rUmxTRVIyUVhnd2NuUkVkMnROVjBaaFpFaDBjMFZLV2pONU5tbGpjemMyUVdoWGRqVXdMMFIyTlcxRFUzTTJTbXhhYXpocFVHRmtiazV0U0c5cmJUSnZSMHhuWlhoeVppOTRhVEJ0ZEcxSFpEa3diRzlGVWk5YVFUZHBkVGN2YmxGSlJ5c3hNRWhaT0RWQlFXVnRaREJIVFdoamNWVkhUMnA1ZUV0T1RWZGxiMVZCU0VOVE1YZzFVbFpZZEVsTldHNHZSbmhWZEZKclNVOWxPRmR5Y1hkWlVXNXNTVzQwWjJGdmNXeHFTazR3WjNoemJWWjFTMlEzV0RneFZWSllXa2xoVFZGdE9IbEtUbFJSUFQwaUxDSnRZV01pT2lKaE4yWmhZalJoTWpNMU16WTRZVEkyTTJVME5EVTBNRGhrT0RBMVlqUTVOemcyWmpSaU5qTTNNbUV4WkdWbFpHUmxPRFUwTVRCaU9EUXpPR1ZpTXpneUlpd2lkR0ZuSWpvaUluMD0=',1773805326),('xC2kUQgNmfpC1d5zVzVMVc9QgnpOUcMn7BbiTluZ',NULL,'100.64.0.4','facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)','ZXlKcGRpSTZJa1pPVTFWVlJGUmhMMHhNVVhGRmFrdENiVXhGVlVFOVBTSXNJblpoYkhWbElqb2llSGhIVldGa1FuUXJVamhPUWpobFIwa3hkMXBsYUV4b1VuZDNSR0ZSTjJseVZGWnFjRzVpV0VKRFJIUXdjREpHZWk5d1RqazNUMk5GVERkRVpXUjBNRkJtU0N0Q2RsQkRZMjFzWmtweGFHTkpiMlJXVEdGNFlXWkJNRnBZWm05NGFrbFhaV0pzWVhOS1NtZGtSR0l2WWxCaE1WSmlaMnhOVkhSdlduazNZMHg2Ulc5NGEyTmtRVGd3VkU4MGJ6VkxUMWx5VTB4RlZqSm9PRzlxYlhFMVRXOUNWMmxIVjNKVldrSnRXbTVET0ZveVNtbGlWbGxoTDA1aVpHUnJhSGRJUVdGcU5HdGFSMll3TkV3NE15dFFlU3RoVFZKbWJFbHhPVTlaVGpkbmVXMTJTRXQ1VWpsVEwwbzVhVWh2WVZrM1ZUa3plVVpuVEVJemRuVjRiVEJaV1dSM2VuRlhkaXN4U1VKU1dXZFlUbFphVFVGRlMwZEpWa1U1WW5GQ05IWlVTalY0VEc4NFNsSXhVRzFXYzNGRU5VRlFXbWh6Y0hWU2RHRkxUMmhrYmxkaGFucFBORFpqYm01bVptdHVPRUZ0YUhKb2RVZEJQVDBpTENKdFlXTWlPaUl3TURKa1ptUTNNMkpoT0dVeVl6WTFZakZoWTJRNFpETTFPREk1WkRabVltRXpNek0xWVRjMVlqQTJaRGN6WWpobU9EZzRZV0U1TXpRNE5Ua3dZakU0SWl3aWRHRm5Jam9pSW4wPQ==',1773805241),('zwcByc0ab0yafaysaqWRxBjEh2kpOlLrBco32wGe',NULL,'100.64.0.4','Mozilla/5.0 (Linux; Android 13; 2201117TG Build/TKQ1.221114.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/145.0.7632.159 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/552.0.0.44.65;]','ZXlKcGRpSTZJa3cwYzNwRVdrbzFNbU5UY0U5emFVNVlhVWxWUTJjOVBTSXNJblpoYkhWbElqb2lRV3BMT1VwMlpVMDJaSFZxT1ZwU1VtOVNaV040YlZWSFpscHllbWgwVUc5RmJXaHNiRzQxWWtwVFMxcDRkbkJLWW1GTmFHZE9jSEp4VVVwWVowUkJSMW92VUZkVE1sUktNemN6VEZOb2NVOVRNRkU1T0dkS1VFZHViV3RSYUZNdlRUWnlMekpOYW14RlZuQlBSa2REY0hJNWFtNWxNVk5ST1ZoaGQyaDVkbXh2YlRCT2FVZEtlRlpuTjJKU2JrcENiazkxVHpjNGFFMTZibFZzZFhCYVZVbFNSVkp6VkhSTmFISnhTSFJNYkVSSFMxTTVRWGhXWWxFeVRsY3ZSSFZ2ZVVwMlRIazNSbXRaTDJKS0swSmlObXBLVTIxU1pHWk5UbU5rWWtsME5IcG5iMWt3TXpKRFJIWkhNWEJIUml0NGNXY3lkemh4VDI5T2EzRTFVSFpNZVZSa1kyd3lPRTVYTTJGNWJEUjFTVTFMVjNWM2VEQlBOa2gxTTFOMmFWZFZTMEZNZVdkUVltSjFabmxVY2tabWNrTXZhR1I1VGxrNFFtSkpXREZWUVRWcU4yTkdXVzVUUTNBclJtWk5hQzh3WjFoRFprRmhaMVI2Y0ZOSWVtOVZkVWd6UkU5elQwNVBNbkIwUjB3dlJXSnJZMnQ2V0VveldpOVpLemgxV0ROUk5YTTVUbXg1VTNwNmNHWklNV1ZyUW1GRGJXeHRNRTlXWkRReWRURk9Sek53Y2xGc1EzcDRWRlpFZGk5emNWUnllbk5YYmt4U1NIbFNaMkl4UW1JeFlsY3pPVlJEYURKcGIxRXdOVGxQYUZveFlqQlBXSEo1TTI5YWFtdEVjRVZwY0VST1MzbFZWMUJIVFdOSVdYRlJNV0ZCWjNkcU1FTkZjelZQYXpsUE1rdzRURVFyZFVzMGJqbHVUMHBRU1V0cVl5c3lkazFDTm5KTFMwbzNZVVV6VjBKT2NGQk5aRVEwVHk5M1BTSXNJbTFoWXlJNklqZGlOamsxTnpRME5UUmlaRGs0TkROaFptTmtOamRtTjJRek5tTTVNMk13TUdaaFpUbGxZV1JrTlRZeFlUbGlNMlkyTURVM1lqVmlOMkkxWkRNelpqWWlMQ0owWVdjaU9pSWlmUT09',1773836498);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES (1,'Math'),(2,'Science'),(3,'English');
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_details`
--

DROP TABLE IF EXISTS `teacher_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mname` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female') COLLATE utf8mb4_general_ci NOT NULL,
  `civil_status` enum('Single','Married') COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `employee_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` enum('Permanent','Temporary','Contractual','Part-time') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_details`
--

LOCK TABLES `teacher_details` WRITE;
/*!40000 ALTER TABLE `teacher_details` DISABLE KEYS */;
INSERT INTO `teacher_details` VALUES (1,'Hermoso','Sean Carlo','Nieto','2003-09-10','Male','Single','B6 L23, Camachile st., South Plains 1, Sto.Tomas, Biñan, Laguna','22-3850-51','Senior High School','Subject Teacher','Information Technology',NULL,NULL,'09063128626'),(2,'Balibay','Jhon Edduard','Kabak','2004-06-08','Male','Single','adasdasdsad','22-3850-52','Senior High School','Subject Teacher','Information Technology',NULL,NULL,'091212121212'),(3,'Abarrientos','Melvin','N/A','2026-03-08','Male','Single','adasdasd','22-3850-53','Junior High School','Subject Teacher','Information Technology','0000-00-00','','09100010001'),(8,'uhihhkhjk','ssssdds','sdsss','2003-01-08','Male','Single','ghdhgdhdhfhg','22-3850-54','Junior High School','Subject Teacher','Information Technology','0000-00-00','','091212121212'),(9,'Alenzuela','Karl Randel','asdsa','2003-04-22','Male','Single','asdasdasdasd','22-3850-55','Senior High School','Subject Teacher','Mathematics','2026-03-06','Temporary','09100010001'),(10,'Tagalog','Robin Christian','wala','2001-06-25','Male','Single','sadasdsdasd','22-3850-55','Senior High School','Subject Teacher','Information Technology','2026-03-10','Permanent','09063128626'),(29,'Baliong','John Conrad','Iliw-Iliw','2026-03-10','Male','Single','asdasdasdasd','22-3850-57','Junior High School','Subject Teacher','Information Technology','2026-03-10','Temporary','09063128626'),(30,'Osabel','Anthony','asda','2026-03-17','Male','Single','dadasdasdas','22-3850-58','Junior High School','Subject Teacher','Information Technology','2026-03-17','Permanent','09063128626');
/*!40000 ALTER TABLE `teacher_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mname` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female') COLLATE utf8mb4_general_ci NOT NULL,
  `Civil_status` enum('Single','Married') COLLATE utf8mb4_general_ci NOT NULL,
  `grade_level_id` int DEFAULT NULL,
  `section_id` int DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `student_no` bigint NOT NULL,
  `contact_no` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_details`
--

LOCK TABLES `user_details` WRITE;
/*!40000 ALTER TABLE `user_details` DISABLE KEYS */;
INSERT INTO `user_details` VALUES (3,'Calip','Kristin Chine','adsad','2026-03-13','Female','Single',1,9,'adsadsad',222222222222,2147483647),(4,'Publico','Bienvinido James','Mangao','2004-03-31','Male','Single',2,5,'Maligaya 1, Pacita Complex 1. San Pedro City, Laguna',123456789012,2147483647);
/*!40000 ALTER TABLE `user_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `details_id` int NOT NULL,
  `role_id` int NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','administrator@example.com','2026-03-03 09:38:11','$2y$12$ELmBz8jzzAT86DtBccEUXuCO/FtevfUktk3u9KRmx0OeKr/YAYPOq','NdwdwbYJs0vEajfGpZAsZWpWgErOfzIVdCkhHjQ80uEN7bK76kji3TH8d2BQ','2026-03-03 09:38:12','2026-03-17 16:31:48',0,3,'Active'),(14,'John Conrad Baliong','JCB@gmail.com','2026-03-10 14:23:07','$2y$12$dqZuwDA3ht/DmSM6BEN3ze3v.sDZM9tZA.lspTaXWQie3jaSussGm',NULL,'2026-03-10 14:23:07','2026-03-18 03:55:35',29,1,'Active'),(16,'Kristin Chine Calip','kcc@gmail.com','2026-03-13 14:58:27','$2y$12$2RxiGO86V6wozvkfFdzxOupnas4I2RjQcdTBWMckBMPDixo724rHa',NULL,'2026-03-13 14:58:27','2026-03-13 14:58:27',3,2,'Active'),(17,'Bienvinido James Publico','bienvenido.publico@cdsp.edu.ph','2026-03-14 21:00:29','$2y$12$PfEEj78cz6dmsYK.3LNIiOJGl7BjKtQBQExQUurt3McmfYGXrEbgi',NULL,'2026-03-14 21:00:29','2026-03-14 21:00:29',4,2,'Active'),(18,'Anthony Osabel','anthony@gmail.com','2026-03-16 16:35:22','$2y$12$KHa43ybnSuvJ4FrH5do.COKvP/mx2VoR6Y53FG8U7bBZRFhQT973O',NULL,'2026-03-16 16:35:22','2026-03-16 16:35:22',30,1,'Active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'railway'
--
/*!50003 DROP PROCEDURE IF EXISTS `usp_authentication` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_authentication`(IN `MODE` INT, IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))
BEGIN

	IF MODE = 1

    THEN 

    	SELECT 		 u.email

        			,u.password

                    ,r.name

        FROM		 users u

        JOIN		 roles r

        ON			 u.role_id = r.id 

        WHERE		 u.email = p_email COLLATE utf8mb4_unicode_ci

       	AND			 u.password = SHA2(p_password, 256);

     END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_announcements_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_announcements_data`(IN `p_mode` INT, IN `p_user_id` INT)
BEGIN

    DECLARE v_section_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION

    BEGIN

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error fetching announcements data';

    END;



    -- MODE 1: Get teacher's own announcements

    IF p_mode = 1 THEN

        SELECT 

             a.id

            ,DATE_FORMAT(a.date_posted, '%Y-%m-%d') AS date_posted

            ,a.title

            ,a.description

            ,s.subject_name

            ,GROUP_CONCAT(DISTINCT CONCAT(gl.grade_level_name, ' - ', sec.section_name) ORDER BY gl.id, sec.section_name SEPARATOR ', ') AS section_names

            ,GROUP_CONCAT(DISTINCT gl.grade_level_name ORDER BY gl.id SEPARATOR ', ') AS grade_level_names

            ,CONCAT(td.fname, ' ', td.lname) AS posted_by

            ,a.add_to_calendar

            ,a.calendar_date

            ,a.subject_id

        FROM announcements a

        JOIN subject s ON a.subject_id = s.id

        JOIN announcement_sections ans ON a.id = ans.announcement_id

        JOIN section sec ON ans.section_id = sec.id

        JOIN grade_level gl ON sec.grade_level_id = gl.id

        JOIN users u ON a.user_id = u.id

        JOIN teacher_details td ON u.details_id = td.id

        WHERE a.user_id = p_user_id

        GROUP BY 

            a.id, a.date_posted, a.title, a.description, s.subject_name, 

            td.fname, td.lname, a.add_to_calendar, a.calendar_date, a.subject_id

        ORDER BY a.date_posted DESC;

    END IF;



    -- MODE 2: Get student's announcements (their section + admin announcements)

    IF p_mode = 2 THEN

        -- Get student's section

        SELECT ud.section_id INTO v_section_id

        FROM users u

        JOIN user_details ud ON u.details_id = ud.id

        WHERE u.id = p_user_id AND u.role_id = 2;



        -- If student has no section, return empty result

        IF v_section_id IS NULL THEN

            SELECT 1 WHERE 1=0;

        ELSE

            SELECT 

                 a.id

                ,DATE_FORMAT(a.date_posted, '%Y-%m-%d') AS date_posted

                ,a.title

                ,a.description

                ,s.subject_name

                ,CASE 

                    WHEN u_role.role_id = 0 THEN 'All Sections'

                    ELSE GROUP_CONCAT(DISTINCT CONCAT(gl.grade_level_name, ' - ', sec.section_name) ORDER BY gl.id, sec.section_name SEPARATOR ', ')

                 END AS section_names

                ,CASE 

                    WHEN u_role.role_id = 0 THEN 'Admin'

                    ELSE CONCAT(td.fname, ' ', td.lname)

                 END AS posted_by

                ,a.add_to_calendar

                ,a.calendar_date

            FROM announcements a

            JOIN subject s ON a.subject_id = s.id

            LEFT JOIN announcement_sections ans ON a.id = ans.announcement_id

            LEFT JOIN section sec ON ans.section_id = sec.id

            LEFT JOIN grade_level gl ON sec.grade_level_id = gl.id

            JOIN users u_role ON a.user_id = u_role.id

            LEFT JOIN teacher_details td ON u_role.details_id = td.id

            WHERE 

                (ans.section_id = v_section_id OR u_role.role_id = 0)

            GROUP BY 

                a.id, a.date_posted, a.title, a.description, s.subject_name, 

                a.user_id, u_role.role_id, td.fname, td.lname, a.add_to_calendar, a.calendar_date

            ORDER BY a.date_posted DESC;

        END IF;

    END IF;



    -- MODE 3: Get all announcements (for admin)

    IF p_mode = 3 THEN

        SELECT 

             a.id

            ,DATE_FORMAT(a.date_posted, '%Y-%m-%d') AS date_posted

            ,a.title

            ,a.description

            ,s.subject_name

            ,GROUP_CONCAT(DISTINCT CONCAT(gl.grade_level_name, ' - ', sec.section_name) ORDER BY gl.id, sec.section_name SEPARATOR ', ') AS section_names

            ,GROUP_CONCAT(DISTINCT gl.grade_level_name ORDER BY gl.id SEPARATOR ', ') AS grade_level_names

            ,CASE 

                WHEN u.role_id = 0 THEN 'Admin'

                ELSE CONCAT(td.fname, ' ', td.lname)

             END AS posted_by

            ,a.add_to_calendar

            ,a.calendar_date

        FROM announcements a

        JOIN subject s ON a.subject_id = s.id

        LEFT JOIN announcement_sections ans ON a.id = ans.announcement_id

        LEFT JOIN section sec ON ans.section_id = sec.id

        LEFT JOIN grade_level gl ON sec.grade_level_id = gl.id

        JOIN users u ON a.user_id = u.id

        LEFT JOIN teacher_details td ON u.details_id = td.id

        GROUP BY 

            a.id, a.date_posted, a.title, a.description, s.subject_name, 

            u.role_id, td.fname, td.lname, a.add_to_calendar, a.calendar_date

        ORDER BY a.date_posted DESC;

    END IF;
    IF p_mode = 4 
    THEN
	    SELECT
			         a.id
			        ,a.title
			        ,a.description
			        ,a.date_posted
			        ,a.user_id
			        ,a.add_to_calendar
			        ,a.calendar_date
			        ,a.subject_id
			        ,s.subject_name
			        ,u.name AS posted_by
			        ,(SELECT grade_level_id FROM announcement_sections WHERE announcement_id = a.id LIMIT 1) AS grade_level_id
	    FROM         announcements a
	    JOIN         subject s ON a.subject_id = s.id
	    JOIN 		 users u ON a.user_id = u.id
	    WHERE 		 a.id = p_user_id;
	END IF;
    IF p_mode = 5 
    THEN
	    SELECT
				         ans.section_id
				        ,s.section_name
				        ,s.grade_level_id
				        ,gl.grade_level_name
	    FROM 			 announcement_sections ans
	    JOIN 			 section s 
	    ON 				 ans.section_id = s.id
	    JOIN 			 grade_level gl 
	    ON 				 s.grade_level_id = gl.id
	    WHERE 			 ans.announcement_id = p_userId
	    ORDER BY 		 gl.id, s.section_name;
	END IF;



END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_calendar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_calendar`(IN `U_ID` INT)
BEGIN

    DECLARE EXIT HANDLER FOR SQLEXCEPTION

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Database error in usp_get_calendar.';



    -- Requesting user's role (3=admin, 1=teacher, 2=student)

    SET @u_role = (SELECT role_id FROM users WHERE id = U_ID LIMIT 1);



    -- For students: grab their section_id once upfront

    SET @u_section = NULL;

    IF @u_role = 2 THEN

        SET @u_section = (

            SELECT ud.section_id

            FROM   users        u

            JOIN   user_details ud ON ud.id = u.details_id

            WHERE  u.id = U_ID

            LIMIT  1

        );

    END IF;



    -- ── Result set 1: Standalone events (all roles) ──────────────────────────

    SELECT

         e.id

        ,'event'   AS source

        ,e.title

        ,e.description

        ,e.event_date

        ,e.event_type

        ,NULL      AS posted_by_name

    FROM events e

    ORDER BY e.event_date ASC;



    -- ── Result set 2: Announcement-linked calendar entries ───────────────────

    -- posted_by_name resolved via CASE:

    --   teachers → teacher_details (joined via td.id = u.details_id)

    --   admins   → user_details    (joined via ud.id = u.details_id)

    SELECT

         a.id

        ,'announcement'             AS source

        ,a.title

        ,a.description

        ,a.calendar_date            AS event_date

        ,'academic'                 AS event_type

        ,CASE

            WHEN (SELECT role_id FROM users WHERE id = a.user_id LIMIT 1) = 1

                THEN CONCAT(td.fname, ' ', td.lname)

            ELSE CONCAT(ud.fname, ' ', ud.lname)

         END                        AS posted_by_name

    FROM announcements a

    JOIN  users           u   ON u.id  = a.user_id

    LEFT JOIN user_details    ud  ON ud.id = u.details_id

    LEFT JOIN teacher_details td  ON td.id = u.details_id

    WHERE a.add_to_calendar = 1

      AND a.calendar_date   IS NOT NULL

      AND (

            @u_role = 3



            OR (@u_role = 1 AND a.user_id = U_ID)



            OR (@u_role = 1 AND (

                SELECT role_id FROM users WHERE id = a.user_id LIMIT 1

            ) = 3)



            OR (@u_role = 2 AND EXISTS (

                SELECT 1

                FROM   announcement_sections ans

                WHERE  ans.announcement_id = a.id

                  AND  ans.section_id      = @u_section

            ))

      )

    ORDER BY a.calendar_date ASC;



END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_data`(IN `MODE` INT, IN `U_ID` INT)
BEGIN


	DECLARE EXIT HANDLER FOR SQLEXCEPTION


    BEGIN


        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Database system error: Could not retrieve data.';


    END;





    -- Get user data 


	IF MODE = 1


    THEN 


    	IF NOT EXISTS(SELECT 1 FROM users WHERE ID = U_ID)


        THEN


        	SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'User not Found';


    	ELSE


    		SELECT 1 FROM users WHERE ID = U_ID;


        END IF;


    END IF;


    -- Get students data


    IF MODE = 2


    THEN


    	SELECT 	 u.id
        		
                ,u.name


        		,u.email


                ,r.name AS role_name


                ,u.status


        FROM	users u


        JOIN	roles r ON r.id = u.role_id;


        IF (SELECT COUNT(*) FROM students) = 0 THEN


           SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No student records found.';


        END IF;


    END IF;


    IF MODE = 3 


    THEN


        IF (SELECT COUNT(*) FROM announcements WHERE user_id = U_ID) = 0 THEN


            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No announcement records found.';


        END IF;





        SELECT      a.id


                   ,a.date_posted


                   ,a.title


                   ,a.description


                   ,a.subject_id


                   ,s.subject_name


                   ,GROUP_CONCAT(sc.section_name ORDER BY sc.section_name SEPARATOR ', ') AS section_names


				   ,GROUP_CONCAT(DISTINCT gl.grade_level_name ORDER BY gl.grade_level_name SEPARATOR ', ') AS grade_level_names        


        FROM        announcements a


        JOIN        subject s              


        ON 			a.subject_id       = s.id


        JOIN        announcement_sections ans 


        ON 			a.id = ans.announcement_id


        JOIN        section sc             


        ON 			ans.section_id = sc.id


        JOIN		grade_level gl 


        ON			sc.grade_level_id = gl.id


        WHERE       a.user_id = U_ID


        GROUP BY    a.id, a.date_posted, a.title, a.description, a.subject_id, s.subject_name;


    END IF;


    IF MODE = 4 


    THEN


        SELECT      a.id


                   ,a.date_posted


                   ,a.title


                   ,a.description


                   ,a.subject_id


                   ,s.subject_name


                   ,GROUP_CONCAT(sc.section_name ORDER BY sc.section_name SEPARATOR ', ') AS section_names


        		   ,GROUP_CONCAT(DISTINCT gl.grade_level_name ORDER BY gl.grade_level_name SEPARATOR ', ') AS grade_level_names    


        FROM        announcements a


        JOIN        subject s


        ON          a.subject_id = s.id


        JOIN        announcement_sections ans


        ON          a.id = ans.announcement_id


        JOIN        section sc


        ON          ans.section_id = sc.id


        JOIN        grade_level gl


        ON          sc.grade_level_id = gl.id


        GROUP BY    a.id, a.date_posted, a.title, a.description, a.subject_id, s.subject_name


        ORDER BY    a.date_posted DESC;


    END IF;


    IF MODE = 5 


    THEN


        SELECT      a.id


                   ,a.date_posted


                   ,a.title


                   ,a.description


                   ,a.subject_id


                   ,s.subject_name


                   ,u.name AS posted_by


                   ,GROUP_CONCAT(sc.section_name ORDER BY sc.section_name SEPARATOR ', ') AS section_names


        		   ,GROUP_CONCAT(DISTINCT gl.grade_level_name ORDER BY gl.grade_level_name SEPARATOR ', ') AS grade_level_names    


        FROM        announcements a


        JOIN        subject s                 


        ON 			a.subject_id = s.id


        JOIN        users u                   


        ON 			a.user_id = u.id


        JOIN        announcement_sections ans 


        ON 			a.id = ans.announcement_id


        JOIN        section sc               


        ON 			ans.section_id = sc.id


        JOIN        grade_level gl                


        ON 			sc.grade_level_id = gl.id


        GROUP BY    a.id, a.date_posted, a.title, a.description, a.subject_id, s.subject_name, u.name


        ORDER BY    a.date_posted DESC;


    END IF;


    IF MODE = 6


    THEN


        SELECT   id


                ,title


                ,description


                ,DATE_FORMAT(event_date, '%Y-%m-%d') AS event_date


                ,event_type


                ,created_by


                ,created_at


        FROM     events


        ORDER BY event_date ASC;


    END IF;





    IF MODE = 7


    THEN


        SELECT   id


                ,title


                ,description


                ,category


                ,DATE_FORMAT(effective_date, '%Y-%m-%d') AS effective_date


                ,status


                ,created_by


                ,created_at


        FROM     policies


        ORDER BY effective_date DESC;


    END IF;


    IF MODE = 8 
    THEN
	    SELECT
	        	 	s.id
	            	,s.section_name
	        		,COUNT(u.id) AS student_enrolled
	        		,s.grade_level_id
	        		,gl.grade_level_name
	    FROM 		section s
	    JOIN 		grade_level gl 
	    ON 			s.grade_level_id = gl.id
	    LEFT JOIN 	user_details ud 
	    ON 			s.id = ud.section_id
	    LEFT JOIN 	users u 
	    ON 			u.details_id = ud.id 
	    AND 		u.role_id = 2
	    GROUP BY 	s.id, s.section_name, s.grade_level_id, gl.grade_level_name
	    ORDER BY 	gl.id ASC, s.section_name ASC;
	END IF;


    IF MODE = 9


    THEN


        SELECT


             sch.id


            ,sch.day


            ,TIME_FORMAT(sch.time_start, '%H:%i') AS time_start


            ,TIME_FORMAT(sch.time_end,   '%H:%i') AS time_end


            ,sch.room


            ,sch.subject_id


            ,s.subject_name


            ,sch.user_id


            ,CONCAT(td.fname, ' ', td.lname) AS teacher_name


            ,sch.section_id


            ,sec.section_name


            ,sch.grade_level_id


            ,gl.grade_level_name


        FROM   schedule sch


        JOIN   subject        s   ON s.id   = sch.subject_id


        JOIN   users          u   ON u.id   = sch.user_id


        JOIN   teacher_details td ON td.id  = u.details_id


        JOIN   section        sec ON sec.id = sch.section_id


        JOIN   grade_level    gl  ON gl.id  = sch.grade_level_id


        ORDER BY FIELD(sch.day,'Monday','Tuesday','Wednesday','Thursday','Friday'), sch.time_start;


    END IF;


     -- MODE 10: All grades — Admin


    IF MODE = 10


    THEN


        SELECT      g.id


                   ,g.student_id


                   ,CONCAT(ud.fname, ' ', ud.lname)  AS student_name


                   ,g.subject_id


                   ,sub.subject_name


                   ,g.section_id


                   ,sec.section_name


                   ,g.grade_level_id


                   ,gl.grade_level_name


                   ,g.quarter


                   ,g.grade


                   ,g.remarks


                   ,g.created_at


                   ,g.updated_at


        FROM        grades g


        JOIN        users          u   ON  u.id    = g.student_id


        JOIN        user_details   ud  ON  ud.id   = u.details_id


        JOIN        subject        sub ON  sub.id  = g.subject_id


        JOIN        section        sec ON  sec.id  = g.section_id


        JOIN        grade_level    gl  ON  gl.id   = g.grade_level_id


        ORDER BY    gl.id ASC, sec.section_name ASC, ud.lname ASC, ud.fname ASC, g.quarter ASC;


    END IF;


 


    -- MODE 11: Teacher's grades — scoped to sections they handle via schedule


    -- U_ID = teacher's user id (passed as the second SP parameter)


    IF MODE = 11


    THEN


        SELECT      g.id


                   ,g.student_id


                   ,CONCAT(ud.fname, ' ', ud.lname)  AS student_name


                   ,g.subject_id


                   ,sub.subject_name


                   ,g.section_id


                   ,sec.section_name


                   ,g.grade_level_id


                   ,gl.grade_level_name


                   ,g.quarter


                   ,g.grade


                   ,g.remarks


                   ,g.created_at


                   ,g.updated_at


        FROM        grades g


        JOIN        users          u   ON  u.id    = g.student_id


        JOIN        user_details   ud  ON  ud.id   = u.details_id


        JOIN        subject        sub ON  sub.id  = g.subject_id


        JOIN        section        sec ON  sec.id  = g.section_id


        JOIN        grade_level    gl  ON  gl.id   = g.grade_level_id


        WHERE       g.section_id IN (


                        SELECT DISTINCT section_id


                        FROM   schedule


                        WHERE  user_id = U_ID


                    )


        ORDER BY    gl.id ASC, sec.section_name ASC, ud.lname ASC, ud.fname ASC, g.quarter ASC;


    END IF;


 


    -- MODE 12: Student's own grades — scoped to their user id


    -- U_ID = student's user id (passed as the second SP parameter)


    IF MODE = 12


    THEN


        SELECT      g.id


                   ,g.student_id


                   ,g.subject_id


                   ,sub.subject_name


                   ,g.section_id


                   ,sec.section_name


                   ,g.grade_level_id


                   ,gl.grade_level_name


                   ,g.quarter


                   ,g.grade


                   ,g.remarks


                   ,g.created_at


        FROM        grades g


        JOIN        subject     sub ON  sub.id  = g.subject_id


        JOIN        section     sec ON  sec.id  = g.section_id


        JOIN        grade_level gl  ON  gl.id   = g.grade_level_id


        WHERE       g.student_id = U_ID


        ORDER BY    g.quarter ASC, sub.subject_name ASC;


    END IF;


      IF MODE = 13


    THEN


        SELECT


             sch.subject_id


            ,sub.subject_name


            ,sch.section_id


            ,sec.section_name


            ,gl.grade_level_name


            ,sch.day


            ,TIME_FORMAT(sch.time_start, '%h:%i %p') AS time_start


            ,TIME_FORMAT(sch.time_end,   '%h:%i %p') AS time_end


            ,sch.room


            -- Count active students enrolled in this section


            ,(


                SELECT COUNT(*)


                FROM   users u2


                JOIN   user_details ud2 ON ud2.id = u2.details_id


                WHERE  u2.role_id     = 2


                  AND  u2.status      = 'Active'


                  AND  ud2.section_id = sch.section_id


            ) AS student_count


        FROM   schedule sch


        JOIN   subject      sub ON sub.id  = sch.subject_id


        JOIN   section      sec ON sec.id  = sch.section_id


        JOIN   grade_level  gl  ON gl.id   = sch.grade_level_id


        WHERE  sch.user_id = U_ID


        ORDER BY


            sub.subject_name ASC,


            FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday'),


            sch.time_start ASC;


    END IF;


 


-- =============================================================================


-- MODE 14: Students enrolled in a specific section


--          U_ID = section_id (second SP parameter)


-- =============================================================================


 


    IF MODE = 14


    THEN


        SELECT


             u.id


            ,CONCAT(ud.lname, ', ', ud.fname,


                    IF(ud.mname IS NOT NULL AND ud.mname != '',


                       CONCAT(' ', LEFT(ud.mname, 1), '.'), '')


             ) AS student_name


            ,ud.fname


            ,ud.lname


            ,ud.mname


            ,ud.student_no   AS lrn


            ,ud.sex


            ,u.status


            ,ud.section_id


            ,sec.section_name


            ,gl.grade_level_name


        FROM   users        u


        JOIN   user_details ud  ON ud.id             = u.details_id


        JOIN   section      sec ON sec.id            = ud.section_id


        JOIN   grade_level  gl  ON gl.id             = sec.grade_level_id


        WHERE  u.role_id    = 2


          AND  u.status     = 'Active'


          AND  ud.section_id = U_ID


        ORDER BY ud.lname ASC, ud.fname ASC;


    END IF;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_student_schedule` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_student_schedule`(IN `p_user_id` INT)
BEGIN

    DECLARE v_section_id INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION

    BEGIN

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error fetching student schedule';

    END;



    -- Get student's section

    SELECT ud.section_id INTO v_section_id

    FROM users u

    JOIN user_details ud ON u.details_id = ud.id

    WHERE u.id = p_user_id AND u.role_id = 2;



    -- If student has no section, return empty result

    IF v_section_id IS NULL THEN

        SELECT 1 WHERE 1=0; -- Return empty result set

    ELSE

        -- Get schedule for that section

        SELECT

             sch.id

            ,sch.day

            ,TIME_FORMAT(sch.time_start, '%h:%i %p') AS time_start

            ,TIME_FORMAT(sch.time_end, '%h:%i %p') AS time_end

            ,sch.room

            ,sch.subject_id

            ,s.subject_name

            ,sch.user_id

            ,CONCAT(td.fname, ' ', td.lname) AS teacher_name

            ,sch.section_id

            ,sec.section_name

            ,sch.grade_level_id

            ,gl.grade_level_name

        FROM   schedule sch

        JOIN   subject s ON s.id = sch.subject_id

        JOIN   users u ON u.id = sch.user_id

        JOIN   teacher_details td ON td.id = u.details_id

        JOIN   section sec ON sec.id = sch.section_id

        JOIN   grade_level gl ON gl.id = sch.grade_level_id

        WHERE  sch.section_id = v_section_id

        ORDER BY 

            FIELD(sch.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),

            sch.time_start;

    END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_teacher_schedule` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_teacher_schedule`(IN `p_user_id` INT)
BEGIN

    DECLARE EXIT HANDLER FOR SQLEXCEPTION

    BEGIN

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error fetching teacher schedule';

    END;



    -- Get schedule for teacher

    SELECT

         sch.id

        ,sch.day

        ,TIME_FORMAT(sch.time_start, '%h:%i %p') AS time_start

        ,TIME_FORMAT(sch.time_end, '%h:%i %p') AS time_end

        ,sch.room

        ,sch.subject_id

        ,s.subject_name

        ,sch.user_id

        ,CONCAT(td.fname, ' ', td.lname) AS teacher_name

        ,sch.section_id

        ,sec.section_name

        ,sch.grade_level_id

        ,gl.grade_level_name

        ,(

            SELECT COUNT(*)

            FROM users u2

            JOIN user_details ud2 ON ud2.id = u2.details_id

            WHERE u2.role_id = 2

              AND u2.status = 'Active'

              AND ud2.section_id = sch.section_id

        ) AS student_count

    FROM   schedule sch

    JOIN   subject s ON s.id = sch.subject_id

    JOIN   users u ON u.id = sch.user_id

    JOIN   teacher_details td ON td.id = u.details_id

    JOIN   section sec ON sec.id = sch.section_id

    JOIN   grade_level gl ON gl.id = sch.grade_level_id

    WHERE  sch.user_id = p_user_id

    ORDER BY 

        FIELD(sch.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),

        sch.time_start;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_populate_fields` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_populate_fields`(IN `MODE` INT, IN `p_grade_level` INT)
BEGIN


	IF MODE = 1


    THEN


    	SELECT		 id


        			,subject_name


        FROM		 subject


        ORDER BY 	 subject_name;


    END IF;


    IF MODE = 2


    THEN


    	SELECT		 id


        			,section_name


                    ,grade_level_id


        FROM		 section


        ORDER BY 	 section_name;


    END IF;


      -- MODE 3: sections filtered by grade_level id


    IF MODE = 3 


    THEN


        SELECT      s.id


                   ,s.section_name


        FROM        section s


        WHERE       s.grade_level_id = p_grade_level


        ORDER BY    s.section_name;


    END IF;





    -- MODE 4: grade levels


    IF MODE = 4 


    THEN


        SELECT      id


                   ,grade_level_name


        FROM        grade_level


        ORDER BY    id;


    END IF;


    -- MODE 5: students per grade level


    IF MODE = 5


    THEN


        SELECT      gl.id


                   ,gl.grade_level_name


                   ,COUNT(u.id) as total


        FROM        grade_level gl


        LEFT JOIN   section s   


        ON 			s.grade_level_id = gl.id


        LEFT JOIN   user_details ud     


        ON 			ud.section_id = s.id


        LEFT JOIN 	users u


        ON			u.details_id = ud.id


                                AND u.role_id = 2


                                AND u.status = 'Active'


        GROUP BY    gl.id, gl.grade_level_name


        ORDER BY    gl.id;


END IF;


-- MODE 6: Active students enrolled in a specific section


    -- p_grade_level is reused here as the section_id context value


    IF MODE = 6


    THEN


        SELECT      u.id


                   ,CONCAT(ud.fname, ' ', ud.lname) AS student_name


                   ,ud.fname


                   ,ud.lname


        FROM        users        u


        JOIN        user_details ud  ON  ud.id           = u.details_id


        WHERE       u.role_id   = 2              -- student role


          AND       u.status    = 'Active'


          AND       ud.section_id = p_grade_level -- p_grade_level carries section_id


        ORDER BY    ud.lname ASC, ud.fname ASC;


    END IF;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_sql_actions` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_sql_actions`(IN `MODE` INT, IN `p_json` JSON)
BEGIN
    DECLARE v_details_id INT;
    DECLARE v_announce_id INT;  -- for mode 21

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Database error occurred.';
    END;

    START TRANSACTION;

    IF MODE = 1
    THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type')) = 2
        THEN
            INSERT INTO user_details(
                lname,
                fname,
                mname,
                birthdate,
                sex,
                Civil_status,
                address,
                grade_level_id,
                section_id,
                student_no,
                contact_no
            )
            VALUES(
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.middle_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.dob')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.sex')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.civil_status')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.address')),
                CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level')) AS UNSIGNED),
                CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section')) AS UNSIGNED),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.lrn')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'))
            );
            SET v_details_id = LAST_INSERT_ID();
            INSERT INTO users(
                name,
                email,
                password,
                email_verified_at,
                created_at,
                updated_at,
                details_id,
                role_id,
                status
            )
            VALUES(
                CONCAT(
                    JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                    ' ',
                    JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                ),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.email')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.password')),
                NOW(),
                NOW(),
                NOW(),
                v_details_id,
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type')),
                'Active'
            );
        END IF;

        IF JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type')) = 1
        THEN
            INSERT INTO teacher_details(
                lname,
                fname,
                mname,
                birthdate,
                sex,
                civil_status,
                address,
                employee_id,
                department,
                position,
                specialization,
                employment_status,
                date_hired,
                contact_no
            )
            VALUES(
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.middle_name')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.dob')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.sex')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.civil_status')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.address')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employee_id')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.department')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.position')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.specialization')),
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employment_status')), ''),
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_hired')), ''),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'))
            );
            SET v_details_id = LAST_INSERT_ID();
            INSERT INTO users(
                name,
                email,
                password,
                email_verified_at,
                created_at,
                updated_at,
                details_id,
                role_id,
                status
            )
            VALUES(
                CONCAT(
                    JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                    ' ',
                    JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                ),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.email')),
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.password')),
                NOW(),
                NOW(),
                NOW(),
                v_details_id,
                JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type')),
                'Active'
            );
        END IF;
    END IF;

    -- MODE 2: Insert the announcement (once)
    IF MODE = 2
    THEN
        INSERT INTO announcements (
            title,
            description,
            subject_id,
            date_posted,
            user_id,
            add_to_calendar,
            calendar_date
        ) VALUES (
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id')) AS UNSIGNED),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_posted')),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id')) AS UNSIGNED),
            CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.add_to_calendar')), 0) AS UNSIGNED),
            NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.calendar_date')), 'null')
        );

        -- Return the new announcement id
        SELECT LAST_INSERT_ID() AS announcement_id;
    END IF;

    -- MODE 3: Insert section link with grade_level derived from section table
    IF MODE = 3
    THEN
        INSERT INTO announcement_sections(announcement_id, section_id, grade_level_id)
        VALUES (
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.announcement_id')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id')),
            (SELECT grade_level_id FROM section WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id')))
        );
    END IF;

    -- MODE 4: Update announcement
    IF MODE = 4
    THEN
        UPDATE announcements
        SET
            title           = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
            description     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')),
            subject_id      = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id')) AS UNSIGNED),
            date_posted     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_posted')),
            add_to_calendar = CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.add_to_calendar')), 0) AS UNSIGNED),
            calendar_date   = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.calendar_date')), 'null')
        WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 5: Insert Event
    IF MODE = 5
    THEN
        INSERT INTO events (
            title,
            description,
            event_date,
            event_type,
            created_by,
            created_at
        )
        VALUES (
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
            NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), ''),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_date')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_type')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.created_by')),
            NOW()
        );
    END IF;

    -- MODE 6: Update Event
    IF MODE = 6
    THEN
        UPDATE events
        SET    title       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
               description = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), ''),
               event_date  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_date')),
               event_type  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_type'))
        WHERE  id          = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 7: Insert Policy
    IF MODE = 7
    THEN
        INSERT INTO policies (
            title,
            description,
            category,
            effective_date,
            status,
            created_by,
            created_at
        )
        VALUES (
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
            NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), ''),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.category')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.effective_date')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.status')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.created_by')),
            NOW()
        );
    END IF;

    -- MODE 8: Update Policy
    IF MODE = 8
    THEN
        UPDATE policies
        SET    title          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title')),
               description    = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), ''),
               category       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.category')),
               effective_date = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.effective_date')),
               status         = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.status'))
        WHERE  id             = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 9: Insert Section
    IF MODE = 9
    THEN
        IF EXISTS (
            SELECT 1 FROM section
            WHERE section_name  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name'))
            AND   grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A section with this name already exists in the selected grade level.';
        END IF;

        INSERT INTO section (
            section_name,
            grade_level_id,
            student_enrolled
        )
        VALUES (
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name')),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED),
            0
        );
    END IF;

    -- MODE 10: Update Section
    IF MODE = 10
    THEN
        IF EXISTS (
            SELECT 1 FROM section
            WHERE section_name   = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name'))
            AND   grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
            AND   id            != CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A section with this name already exists in the selected grade level.';
        END IF;

        UPDATE section
        SET    section_name   = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name')),
               grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
        WHERE  id             = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 11: Delete Section
    IF MODE = 11
    THEN
        IF (SELECT student_enrolled FROM section WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED)) > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot delete — students are still enrolled in this section.';
        END IF;

        DELETE FROM section
        WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 12: Insert Schedule
    IF MODE = 12
    THEN
        -- Check for time conflict: same section, same day, overlapping time
        IF EXISTS (
            SELECT 1 FROM schedule
            WHERE  section_id  = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))  AS UNSIGNED)
            AND    day         = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
            AND    time_start  < JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
            AND    time_end    > JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Schedule conflict: this section already has a class during that time slot.';
        END IF;

        -- Check teacher conflict: same teacher, same day, overlapping time
        IF EXISTS (
            SELECT 1 FROM schedule
            WHERE  user_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))    AS UNSIGNED)
            AND    day        = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
            AND    time_start < JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
            AND    time_end   > JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Schedule conflict: this teacher already has a class during that time slot.';
        END IF;

        INSERT INTO schedule (subject_id, user_id, section_id, grade_level_id, room, day, time_start, time_end)
        VALUES (
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))    AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))       AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))    AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id'))AS UNSIGNED),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.room')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start')),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
        );
    END IF;

    -- MODE 13: Update Schedule
    IF MODE = 13
    THEN
        IF EXISTS (
            SELECT 1 FROM schedule
            WHERE  section_id  = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))  AS UNSIGNED)
            AND    day         = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
            AND    time_start  < JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
            AND    time_end    > JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
            AND    id         != CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))           AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Schedule conflict: this section already has a class during that time slot.';
        END IF;

        IF EXISTS (
            SELECT 1 FROM schedule
            WHERE  user_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))    AS UNSIGNED)
            AND    day        = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
            AND    time_start < JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
            AND    time_end   > JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
            AND    id        != CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))          AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Schedule conflict: this teacher already has a class during that time slot.';
        END IF;

        UPDATE schedule
        SET    subject_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))    AS UNSIGNED),
               user_id       = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))       AS UNSIGNED),
               section_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))    AS UNSIGNED),
               grade_level_id= CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id'))AS UNSIGNED),
               room          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.room')),
               day           = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day')),
               time_start    = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start')),
               time_end      = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
        WHERE  id            = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))            AS UNSIGNED);
    END IF;

    -- MODE 14: Delete Schedule
    IF MODE = 14
    THEN
        DELETE FROM schedule
        WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 15: Insert Grade
    IF MODE = 15
    THEN
        -- Prevent duplicate entry for same student + subject + quarter
        IF EXISTS (
            SELECT 1 FROM grades
            WHERE  student_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id')) AS UNSIGNED)
            AND    subject_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id')) AS UNSIGNED)
            AND    quarter    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))    AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A grade record for this student, subject and quarter already exists.';
        END IF;

        INSERT INTO grades (
            student_id,
            subject_id,
            section_id,
            grade_level_id,
            quarter,
            grade,
            remarks,
            encoded_by,
            created_at,
            updated_at
        )
        VALUES (
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id'))     AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))     AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))     AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))        AS UNSIGNED),
            JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade')),
            NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.remarks')), ''),
            CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.encoded_by'))     AS UNSIGNED),
            NOW(),
            NOW()
        );
    END IF;

    -- MODE 16: Update Grade
    IF MODE = 16
    THEN
        -- Prevent duplicate when changing student/subject/quarter combination
        IF EXISTS (
            SELECT 1 FROM grades
            WHERE  student_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id')) AS UNSIGNED)
            AND    subject_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id')) AS UNSIGNED)
            AND    quarter    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))    AS UNSIGNED)
            AND    id        != CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))         AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A grade record for this student, subject and quarter already exists.';
        END IF;

        UPDATE  grades
        SET     student_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id'))     AS UNSIGNED),
                subject_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))     AS UNSIGNED),
                section_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))     AS UNSIGNED),
                grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED),
                quarter        = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))        AS UNSIGNED),
                grade          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade')),
                remarks        = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.remarks')), ''),
                updated_by     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.updated_by'))     AS UNSIGNED),
                updated_at     = NOW()
        WHERE   id             = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))             AS UNSIGNED);
    END IF;

    -- MODE 17: Delete Grade
    IF MODE = 17
    THEN
        DELETE FROM grades
        WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 18: Insert Subject
    IF MODE = 18
    THEN
        IF EXISTS (
            SELECT 1 FROM subject
            WHERE subject_name = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_name'))
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A subject with this name already exists.';
        END IF;

        INSERT INTO subject (subject_name)
        VALUES (JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_name')));
    END IF;

    -- MODE 19: Update Subject
    IF MODE = 19
    THEN
        IF EXISTS (
            SELECT 1 FROM subject
            WHERE subject_name = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_name'))
            AND   id          != CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED)
        ) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A subject with this name already exists.';
        END IF;

        UPDATE subject
        SET    subject_name = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_name'))
        WHERE  id           = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 20: Delete Subject
    IF MODE = 20
    THEN
        -- Prevent deletion if the subject is still linked to grades or schedule
        IF EXISTS (SELECT 1 FROM grades   WHERE subject_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED)) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot delete — this subject has existing grade records.';
        END IF;

        IF EXISTS (SELECT 1 FROM schedule WHERE subject_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED)) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot delete — this subject is assigned to an active schedule.';
        END IF;

        DELETE FROM subject
        WHERE id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
    END IF;

    -- MODE 21: Delete announcement
    IF MODE = 21
    THEN
        SET v_announce_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);

        -- Delete sections first (foreign key)
        DELETE FROM announcement_sections WHERE announcement_id = v_announce_id;

        -- Delete announcement
        DELETE FROM announcements WHERE id = v_announce_id;
    END IF;

    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_user_management` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_user_management`(IN `p_mode` INT, IN `p_json` JSON)
BEGIN
    DECLARE v_role_id INT;
    DECLARE v_details_id INT;
    DECLARE v_user_id INT;

    -- Common fields
    DECLARE v_last_name VARCHAR(80);
    DECLARE v_first_name VARCHAR(80);
    DECLARE v_middle_name VARCHAR(80);
    DECLARE v_dob DATE;
    DECLARE v_sex VARCHAR(10);
    DECLARE v_civil_status VARCHAR(20);
    DECLARE v_address VARCHAR(255);
    DECLARE v_contact VARCHAR(20);
    DECLARE v_email VARCHAR(120);
    DECLARE v_password VARCHAR(255);
    DECLARE v_status VARCHAR(20);
    DECLARE v_student_type INT;

    -- Student specific
    DECLARE v_lrn VARCHAR(12);
    DECLARE v_section INT;

    -- Teacher specific
    DECLARE v_employee_id VARCHAR(50);
    DECLARE v_department VARCHAR(100);
    DECLARE v_position VARCHAR(100);
    DECLARE v_specialization VARCHAR(100);
    DECLARE v_date_hired DATE;
    DECLARE v_employment_status VARCHAR(50);

    -- Error handler
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    -- Extract common fields
    SET v_last_name      = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'));
    SET v_first_name     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name'));
    SET v_middle_name    = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.middle_name'));
    SET v_dob            = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.dob'));
    SET v_sex            = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.sex'));
    SET v_civil_status   = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.civil_status'));
    SET v_address        = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.address'));
    SET v_contact        = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'));
    SET v_email          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.email'));
    SET v_password       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.password'));
    SET v_status         = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.status'));
    SET v_student_type   = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type'));

    -- Extract type‑specific fields
    SET v_lrn            = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.lrn'));
    SET v_section        = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section'));
    SET v_employee_id    = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employee_id'));
    SET v_department     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.department'));
    SET v_position       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.position'));
    SET v_specialization = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.specialization'));
    SET v_date_hired     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_hired'));
    SET v_employment_status = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employment_status'));

    START TRANSACTION;

    CASE p_mode
        WHEN 1 THEN -- Insert new user
            IF v_student_type = 2 THEN -- Student
                INSERT INTO user_details (
                    lname, fname, mname, birthdate, sex, Civil_status,
                    address, contact_no, student_no, section_id
                ) VALUES (
                    v_last_name, v_first_name, v_middle_name, v_dob, v_sex, v_civil_status,
                    v_address, v_contact, v_lrn, v_section
                );
                SET v_details_id = LAST_INSERT_ID();
                SET v_role_id = 2;

                INSERT INTO users (role_id, details_id, email, password, status)
                VALUES (v_role_id, v_details_id, v_email, v_password, 'Active');
            ELSE -- Teacher
                INSERT INTO teacher_details (
                    employee_id, lname, fname, mname, birthdate, sex, civil_status,
                    address, contact_no, department, position, specialization, date_hired, employment_status
                ) VALUES (
                    v_employee_id, v_last_name, v_first_name, v_middle_name, v_dob, v_sex, v_civil_status,
                    v_address, v_contact, v_department, v_position, v_specialization, v_date_hired, v_employment_status
                );
                SET v_details_id = LAST_INSERT_ID();
                SET v_role_id = 1;

                INSERT INTO users (role_id, details_id, email, password, status)
                VALUES (v_role_id, v_details_id, v_email, v_password, 'Active');
            END IF;

        WHEN 24 THEN -- Update student
            SET v_user_id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'));
            SELECT details_id INTO v_details_id FROM users WHERE id = v_user_id;

            UPDATE user_details
            SET
                lname       = COALESCE(v_last_name, lname),
                fname       = COALESCE(v_first_name, fname),
                mname       = COALESCE(v_middle_name, mname),
                birthdate   = COALESCE(v_dob, birthdate),
                sex         = COALESCE(v_sex, sex),
                Civil_status = COALESCE(v_civil_status, Civil_status),
                address     = COALESCE(v_address, address),
                contact_no  = COALESCE(v_contact, contact_no),
                student_no  = COALESCE(v_lrn, student_no),
                section_id  = COALESCE(v_section, section_id)
            WHERE id = v_details_id;

            UPDATE users
            SET
                email  = COALESCE(v_email, email),
                status = COALESCE(v_status, status)
            WHERE id = v_user_id;

        WHEN 25 THEN -- Update teacher
            SET v_user_id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'));
            SELECT details_id INTO v_details_id FROM users WHERE id = v_user_id;

            UPDATE teacher_details
            SET
                employee_id      = COALESCE(v_employee_id, employee_id),
                lname            = COALESCE(v_last_name, lname),
                fname            = COALESCE(v_first_name, fname),
                mname            = COALESCE(v_middle_name, mname),
                birthdate        = COALESCE(v_dob, birthdate),
                sex              = COALESCE(v_sex, sex),
                civil_status     = COALESCE(v_civil_status, civil_status),
                address          = COALESCE(v_address, address),
                contact_no       = COALESCE(v_contact, contact_no),
                department       = COALESCE(v_department, department),
                position         = COALESCE(v_position, position),
                specialization   = COALESCE(v_specialization, specialization),
                date_hired       = COALESCE(v_date_hired, date_hired),
                employment_status = COALESCE(v_employment_status, employment_status)
            WHERE id = v_details_id;

            UPDATE users
            SET
                email  = COALESCE(v_email, email),
                status = COALESCE(v_status, status)
            WHERE id = v_user_id;

        WHEN 26 THEN -- Soft delete (set inactive)
            SET v_user_id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'));
            UPDATE users SET status = 'Inactive' WHERE id = v_user_id;

        WHEN 27 THEN -- Hard delete
            SET v_user_id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'));

            -- Get the user's role and details_id
            SELECT role_id, details_id INTO v_role_id, v_details_id
            FROM users WHERE id = v_user_id;

            -- Delete from the appropriate details table first
            IF v_role_id = 1 THEN -- Teacher
                DELETE FROM teacher_details WHERE id = v_details_id;
            ELSEIF v_role_id = 2 THEN -- Student
                DELETE FROM user_details WHERE id = v_details_id;
            END IF;

            -- Finally delete the user
            DELETE FROM users WHERE id = v_user_id;

        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid mode supplied to usp_user_management';
    END CASE;

    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-18 22:57:52
