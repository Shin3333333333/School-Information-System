-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sis_db
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `academic_years`
--

DROP TABLE IF EXISTS `academic_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year_label` varchar(20) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcement_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_level_id` (`id`),
  KEY `announcement_id` (`announcement_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `announcement_sections_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcement_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcement_sections`
--

LOCK TABLES `announcement_sections` WRITE;
/*!40000 ALTER TABLE `announcement_sections` DISABLE KEYS */;
INSERT INTO `announcement_sections` VALUES (63,22,3,1),(82,14,3,2),(83,14,4,2),(84,14,5,2);
/*!40000 ALTER TABLE `announcement_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date_posted` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `add_to_calendar` tinyint(1) DEFAULT 0,
  `calendar_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES (13,'Preliminary Exams','2026-03-16','Upcoming exam on March 14-15',0,2,14,1,'2026-03-15'),(14,'Math Long Quiz','2026-03-16','Nyark',0,1,14,1,'2026-03-16'),(21,'asdsad','2026-03-13','asdasd',0,1,14,0,NULL),(22,'sdadasd','2026-03-14','asdasd',0,1,14,0,NULL);
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_type` enum('academic','admin','holiday','activity') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
-- Table structure for table `grade_level`
--

DROP TABLE IF EXISTS `grade_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_level_name` varchar(50) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `grade_level_id` int(10) unsigned NOT NULL,
  `quarter` tinyint(3) unsigned NOT NULL COMMENT '1 = Q1 … 4 = Q4',
  `grade` decimal(5,2) NOT NULL COMMENT '60.00 – 100.00',
  `remarks` varchar(255) DEFAULT NULL,
  `encoded_by` int(10) unsigned DEFAULT NULL COMMENT 'user_id of admin/teacher who entered the grade',
  `updated_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `effective_date` date NOT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `room` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_schedule_user` (`user_id`),
  KEY `fk_schedule_grade` (`grade_level_id`),
  CONSTRAINT `fk_schedule_grade` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`id`),
  CONSTRAINT `fk_schedule_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule`
--

LOCK TABLES `schedule` WRITE;
/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
INSERT INTO `schedule` VALUES (1,1,3,'Monday','08:00:00','12:00:00','101',14,2,'2026-03-14 20:12:11'),(3,3,9,'Monday','17:00:00','20:00:00','103',14,1,'2026-03-15 17:41:56');
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_info`
--

DROP TABLE IF EXISTS `school_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `school_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `core_values` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_info`
--

LOCK TABLES `school_info` WRITE;
/*!40000 ALTER TABLE `school_info` DISABLE KEYS */;
INSERT INTO `school_info` VALUES (1,'To provide quality education that nurtures the holistic development of every learner.','A center of excellence producing God-fearing, globally competitive, and socially responsible citizens.','[\"Integrity\",\"Excellence\",\"Service\",\"Compassion\",\"Respect\"]','2026-03-14 16:02:30');
/*!40000 ALTER TABLE `school_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) NOT NULL,
  `student_enrolled` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section`
--

LOCK TABLES `section` WRITE;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` VALUES (3,'Section B',0,2),(4,'Section C',0,2),(5,'Section D',0,2),(6,'Section A',0,3),(7,'Section A',0,4),(8,'Section C',0,1),(9,'Section A',0,1),(10,'Section B',0,1);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('0a7Z1zFsJdYzcmIji4ZXVzNVqKR2HoP423GtdMjL',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36','ZXlKcGRpSTZJazlFS3pWSlJqUlZOSE4xYTNCaGFIa3lOVVpIY2tFOVBTSXNJblpoYkhWbElqb2ljMlp0UzNwaE1UWkRVbE5HZDJWbWJGUlpaV3RWUWpWbVJGQmljREJ0UXpCNVpteElNMEk0UXpKRlppczRSbXNyTDAxNVdUbFhUV1pVU1RCNWQxVjRZMkUwV2xsM2QwUlVhMDR4TTFOSVRYTTRSa1VyUkVWaVozUm5iRzhyVTFGdlFURTJhRFJPWm5STlptaHVOWGRDUm01VGNDdGhVMmMwUWxWc1RrbDBkMHhJT1ZWMmVqSlphM0l4UWxGelQzTjRLMWh4YjI1SFEyWkhRbTlwWVZrMlNrdHRXak5VSzNSSVJuUjVMMDlPTVRBMlJESlNiMnBaVTFaQmIzSnhSVE5ET1dWc1JFMHJkMGs0V2xKR1ZVcENOVU54ZFM5NVZYUnJiRWhLY0V3ek4wWlJTWEF5VjJKaVRGb3dlVTFYWWpKTVJFZHpOSHBtUVdFeWFpOUNNRTF5YTNsRU1EZG9kM2RvVlVZMk1XVkNiSFZyYW0xVVprYzBaMjkzUm1WamFVSTNWbUV6WXpadVJVSkhOMHhHTlZVNE0zaGhVMEpaTlRjMFpucEhVR0p0ZDJoaE5TdDNkblpXZVZRMGRpOUVZVlZqTWpscU9VWkdURGxuY1hveFFVeDRTRzl6YlhkMmNGcE5heTh4VlcxUGJtRXhjQ3RaV1dSQ1UyWnZVV0ZaWmtseFVVMWpRbWxzV2taQ1lqUk5iVmRFY2xaWVZVMXlRVDA5SWl3aWJXRmpJam9pWkRjM1pqUTNNRFl4WkRCaE4yRXlabUUwTW1Jd1pUQTBabUkzWVRVeE1qaG1NalZrTlRnek5HRm1NMkV5Wmpjek9EQTRNalV6WWpObFpqUXhZVGhsT1NJc0luUmhaeUk2SWlKOQ==',1773636579);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(255) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married') NOT NULL,
  `address` varchar(255) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `employment_status` enum('Permanent','Temporary','Contractual','Part-time') DEFAULT NULL,
  `contact_no` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_details`
--

LOCK TABLES `teacher_details` WRITE;
/*!40000 ALTER TABLE `teacher_details` DISABLE KEYS */;
INSERT INTO `teacher_details` VALUES (1,'Hermoso','Sean Carlo','Nieto','2003-09-10','Male','Single','B6 L23, Camachile st., South Plains 1, Sto.Tomas, Biñan, Laguna','22-3850-51','Senior High School','Subject Teacher','Information Technology',NULL,NULL,'09063128626'),(2,'Balibay','Jhon Edduard','Kabak','2004-06-08','Male','Single','adasdasdsad','22-3850-52','Senior High School','Subject Teacher','Information Technology',NULL,NULL,'091212121212'),(3,'Abarrientos','Melvin','N/A','2026-03-08','Male','Single','adasdasd','22-3850-53','Junior High School','Subject Teacher','Information Technology','0000-00-00','','09100010001'),(8,'uhihhkhjk','ssssdds','sdsss','2003-01-08','Male','Single','ghdhgdhdhfhg','22-3850-54','Junior High School','Subject Teacher','Information Technology','0000-00-00','','091212121212'),(9,'Alenzuela','Karl Randel','asdsa','2003-04-22','Male','Single','asdasdasdasd','22-3850-55','Senior High School','Subject Teacher','Mathematics','2026-03-06','Temporary','09100010001'),(10,'Tagalog','Robin Christian','wala','2001-06-25','Male','Single','sadasdsdasd','22-3850-55','Senior High School','Subject Teacher','Information Technology','2026-03-10','Permanent','09063128626'),(29,'Baliong','John Conrad','Iliw-Iliw','2026-03-10','Male','Single','asdasdasdasd','22-3850-57','Junior High School','Subject Teacher','null','2026-03-10','Temporary','09063128626');
/*!40000 ALTER TABLE `teacher_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `Civil_status` enum('Single','Married') NOT NULL,
  `grade_level_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `student_no` bigint(11) NOT NULL,
  `contact_no` int(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_details`
--

LOCK TABLES `user_details` WRITE;
/*!40000 ALTER TABLE `user_details` DISABLE KEYS */;
INSERT INTO `user_details` VALUES (3,'Calip','Kristin Chine','adsad','2026-03-13','Female','Single',1,1,'adsadsad',222222222222,2147483647),(4,'Publico','Bienvinido James','Mangao','2004-03-31','Male','Single',2,5,'Maligaya 1, Pacita Complex 1. San Pedro City, Laguna',123456789012,2147483647);
/*!40000 ALTER TABLE `user_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `details_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com','2026-03-03 09:38:11','$2y$12$7RurItqie1M03.17Vlsvdupn0qIKtNj9/u4XbZHfF4OKFuy0xIPIa','f4tD1BKmTeh765pE11rpvFNfup6HSAo5lPONc9pMksxYOUnXbcoHdUKYwwCG','2026-03-03 09:38:12','2026-03-03 09:38:12',0,3,'Active'),(2,'Student User','student@example.com','2026-03-03 14:39:50','$2y$12$LygRec/4Mpd02V46NdNUeOxYefQfcMiBFo7N6oNQrVX/WDpXHYi5i','upABd3lKb0c4k7stxNr9jwLvovvWz2BbktwMzYJnee0LzXvUzNJPyD00ugL7','2026-03-03 14:39:50','2026-03-03 14:39:50',0,1,'Active'),(14,'John Conrad Baliong','JCB@gmail.com','2026-03-10 14:23:07','$2y$12$s7ctx83Bd9FzZkTJMajriuOeCi0oBciN4qiE4g909nGcAhUW.hn1.',NULL,'2026-03-10 14:23:07','2026-03-10 14:23:07',29,1,'Active'),(16,'Kristin Chine Calip','kcc@gmail.com','2026-03-13 14:58:27','$2y$12$2RxiGO86V6wozvkfFdzxOupnas4I2RjQcdTBWMckBMPDixo724rHa',NULL,'2026-03-13 14:58:27','2026-03-13 14:58:27',3,2,'Active'),(17,'Bienvinido James Publico','bienvenido.publico@cdsp.edu.ph','2026-03-14 21:00:29','$2y$12$PfEEj78cz6dmsYK.3LNIiOJGl7BjKtQBQExQUurt3McmfYGXrEbgi',NULL,'2026-03-14 21:00:29','2026-03-14 21:00:29',4,2,'Active');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'sis_db'
--

--
-- Dumping routines for database 'sis_db'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_authentication` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
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
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_calendar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_calendar`(IN `U_ID` INT)
BEGIN

    -- ── Error handler ───────────────────────────────────────────────
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Database error in usp_get_calendar.';

    -- ── Get requesting user's role ──────────────────────────────────
    SET @u_role = (SELECT role_id FROM users WHERE id = U_ID LIMIT 1);

    -- ── For students, grab their section_id upfront ─────────────────
    SET @u_section = NULL;
    IF @u_role = 2 THEN -- 2 = Student
        SET @u_section = (
            SELECT ud.section_id
            FROM users u
            JOIN user_details ud ON ud.id = u.details_id
            WHERE u.id = U_ID
            LIMIT 1
        );
    END IF;

    -- ── Result set 1: Standalone events ─────────────────────────────
    -- All roles see school-wide events
    SELECT
         e.id
        ,'event'       AS source
        ,e.title
        ,e.description
        ,e.event_date
        ,e.event_type
        ,NULL          AS posted_by_name
    FROM events e
    ORDER BY e.event_date ASC;

    -- ── Result set 2: Announcement-linked calendar entries ─────────
    -- For announcements linked to calendar
SELECT
     a.id
    ,'announcement' AS source
    ,a.title
    ,a.description
    ,a.calendar_date AS event_date
    ,'academic' AS event_type
    ,CASE
        WHEN (SELECT role_id FROM users WHERE id = a.user_id LIMIT 1) = 1
            THEN CONCAT(td.fname, ' ', td.lname) -- teacher
        ELSE CONCAT(ud.fname, ' ', ud.lname)   -- student/admin
     END AS posted_by_name
FROM announcements a
JOIN users u ON u.id = a.user_id
LEFT JOIN user_details ud ON ud.id = u.details_id
LEFT JOIN teacher_details td ON td.user_id = u.id
WHERE a.add_to_calendar = 1
  AND a.calendar_date IS NOT NULL
  AND (
        -- Admin sees everything
        @u_role = 3

        -- Teacher sees their own announcements
        OR (@u_role = 1 AND a.user_id = U_ID)

        -- Teacher sees admin-posted announcements
        OR (@u_role = 1 AND (
            SELECT role_id FROM users WHERE id = a.user_id LIMIT 1
        ) = 3)

        -- Student sees announcements targeting their section
        OR (@u_role = 2 AND EXISTS (
            SELECT 1
            FROM announcement_sections ans
            JOIN user_details ud2 ON ud2.id = (SELECT details_id FROM users WHERE id = U_ID)
            WHERE ans.announcement_id = a.id
              AND ans.section_id = ud2.section_id
        ))
  )
ORDER BY a.calendar_date ASC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_get_data` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
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
    	SELECT 	 u.name
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
        SELECT   s.id
                ,s.section_name
                ,s.student_enrolled
                ,s.grade_level_id
                ,gl.grade_level_name
        FROM     section s
        JOIN     grade_level gl ON s.grade_level_id = gl.id
        ORDER BY gl.id ASC, s.section_name ASC;
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
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_populate_fields` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
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
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `usp_sql_actions` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_sql_actions`(IN `MODE` INT, IN `p_json` JSON)
BEGIN
	DECLARE v_details_id INT;

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
            INSERT INTO	 user_details(
                         lname
                        ,fname
                        ,mname
                        ,birthdate
                        ,sex
                        ,Civil_status
                        ,address
                        ,grade_level_id
                        ,section_id
                        ,student_no
                        ,contact_no
                        )
            VALUES		(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.middle_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.dob'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.sex'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.civil_status'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.address'))
                        ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level')) AS UNSIGNED)  -- ← ID now
                		,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section')) AS UNSIGNED)      -- ← ID now
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.lrn'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'))
                         );
            SET v_details_id = LAST_INSERT_ID();
            INSERT INTO  users(
                         name
                        ,email
                		,password
                        ,email_verified_at
                        ,created_at
                        ,updated_at
                        ,details_id
                        ,role_id
                        ,status)
             VALUES		(CONCAT(
                        JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                        ' ',
                        JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                        )
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.email'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.password'))
                        ,NOW()
                        ,NOW()
                        ,NOW()
                        ,v_details_id
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type'))
                        ,'Active');
          	END IF;
            IF JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type')) = 1
        	THEN
            INSERT INTO	 teacher_details(
                         lname
                        ,fname
                        ,mname
                        ,birthdate
                        ,sex
                        ,civil_status
                        ,address
                        ,employee_id
                        ,department
                        ,position
                        ,specialization
                		,employment_status
                		,date_hired
               			,contact_no
                        )
            VALUES		(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.middle_name'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.dob'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.sex'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.civil_status'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.address'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employee_id'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.department'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.position'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.specialization'))
                        ,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.employment_status')), '')  -- ← add
                        ,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_hired')), '')  -- ← add
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'))
                         );
            SET v_details_id = LAST_INSERT_ID();
            INSERT INTO  users(
                         name
                        ,email
                		,password
                        ,email_verified_at
                        ,created_at
                        ,updated_at
                        ,details_id
                        ,role_id
                        ,status)
             VALUES		(CONCAT(
                        JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.first_name')),
                        ' ',
                        JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.last_name'))
                        )
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.email'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.password'))
                        ,NOW()
                        ,NOW()
                        ,NOW()
                        ,v_details_id
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type'))
                        ,'Active');
                        
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
                CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))    AS UNSIGNED),
                CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.add_to_calendar')), 0) AS UNSIGNED),
                NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.calendar_date')), 'null')
            );
 
            -- Return the new announcement id (keep your existing SELECT)
            SELECT LAST_INSERT_ID() AS announcement_id;
        END IF;
        -- MODE 3: Insert section link with grade_level derived from section table
        IF MODE = 3 
        THEN
            INSERT INTO announcement_sections(announcement_id, section_id, grade_level_id)
            VALUES (
                 JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.announcement_id'))
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))
                ,(SELECT grade_level_id FROM section WHERE id = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id')))
            );
        END IF;
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
                         title
                        ,description
                        ,event_date
                        ,event_type
                        ,created_by
                        ,created_at
                    )
            VALUES (
                         JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                        ,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), '')
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_date'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_type'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.created_by'))
                        ,NOW()
                    );
        END IF;

        -- MODE 6: Update Event
        IF MODE = 6 
        THEN
            UPDATE events
            SET    title       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                  ,description = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), '')
                  ,event_date  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_date'))
                  ,event_type  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.event_type'))
            WHERE  id          = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
        END IF;

        -- MODE 7: Insert Policy
        IF MODE = 7 
        THEN
            INSERT INTO policies (
                         title
                        ,description
                        ,category
                        ,effective_date
                        ,status
                        ,created_by
                        ,created_at
                    )
            VALUES (
                         JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                        ,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), '')
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.category'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.effective_date'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.status'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.created_by'))
                        ,NOW()
                    );
        END IF;

        -- MODE 8: Update Policy
        IF MODE = 8 
        THEN
            UPDATE policies
            SET    title          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                  ,description    = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description')), '')
                  ,category       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.category'))
                  ,effective_date = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.effective_date'))
                  ,status         = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.status'))
            WHERE  id             = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id')) AS UNSIGNED);
        END IF;
        -- Add these three blocks inside usp_sql_actions, before the final COMMIT;

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
                 section_name
                ,grade_level_id
                ,student_enrolled
            )
            VALUES (
                 JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name'))
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
                ,0
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
            SET    section_name   = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_name'))
                  ,grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
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
                 CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))    AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))       AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))    AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id'))AS UNSIGNED)
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.room'))
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
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
            SET    subject_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))    AS UNSIGNED)
                  ,user_id       = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))       AS UNSIGNED)
                  ,section_id    = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))    AS UNSIGNED)
                  ,grade_level_id= CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id'))AS UNSIGNED)
                  ,room          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.room'))
                  ,day           = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.day'))
                  ,time_start    = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_start'))
                  ,time_end      = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.time_end'))
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
                 student_id
                ,subject_id
                ,section_id
                ,grade_level_id
                ,quarter
                ,grade
                ,remarks
                ,encoded_by
                ,created_at
                ,updated_at
            )
            VALUES (
                 CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id'))     AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))     AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))     AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))        AS UNSIGNED)
                ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade'))
                ,NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.remarks')), '')
                ,CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.encoded_by'))     AS UNSIGNED)
                ,NOW()
                ,NOW()
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
            SET     student_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_id'))     AS UNSIGNED)
                   ,subject_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))     AS UNSIGNED)
                   ,section_id     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section_id'))     AS UNSIGNED)
                   ,grade_level_id = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level_id')) AS UNSIGNED)
                   ,quarter        = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.quarter'))        AS UNSIGNED)
                   ,grade          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade'))
                   ,remarks        = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.remarks')), '')
                   ,updated_by     = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.updated_by'))     AS UNSIGNED)
                   ,updated_at     = NOW()
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

-- Dump completed on 2026-03-16 13:05:10
