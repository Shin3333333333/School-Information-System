-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 05:19 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sis_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_authentication` (IN `MODE` INT, IN `p_email` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
	IF MODE = 1
    THEN 
    	SELECT 		 u.email
        			,u.password
                    ,r.name
        FROM		 users u
        JOIN		 roles r
        ON			 u.role_id = r.id 
        WHERE		 u.email = p_email
       	AND			 u.password = SHA2(p_password, 256);
     END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_get_data` (IN `MODE` INT, IN `U_ID` INT)   BEGIN
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_sql_actions` (IN `MODE` INT, IN `p_json` JSON)   BEGIN
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
    	INSERT INTO	 user_details(
            		 lname
            		,fname
            		,mname
            		,birthdate
            		,sex
            		,Civil_status
            		,address
            		,Grade_Level
            		,Section
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
                    ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.grade_level'))
                    ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.section'))
                    ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.lrn'))
                    ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.contact'))
                     );
        SET v_details_id = LAST_INSERT_ID();
		INSERT INTO  users(
            		 name
            		,email
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
            		,NOW()
                    ,NOW()
                    ,NOW()
                    ,v_details_id
            		,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.student_type'))
                    ,'Active');
      END IF;	
      COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Teacher'),
(2, 'Student'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Ze6pXla2wBK5faYcJEiNsmKpGK0OB9NtghUrpPaT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJa1pwVmtoS2RrNUxjMGhNVFc5a1VuRmFkMEZMVTJjOVBTSXNJblpoYkhWbElqb2libTFuYmtOb1ZWYzFkR3N5TlhGbllWcElNR1poUmtZM2RXbEJkV0ZSUTNNNFIxQTVRVXRpUkV0cU9WSjFaMFJWWWxCeVN6azBWalJyTkhCbkx6UnJaRkZHVFVzMGNtSldSbmxJWVRoVU0wVnZjVkpZUnpGWFpGVXdNak5EYTJrM1pFSllSVVkxWW1sUE1tdFdObkV6U0ZZMksxSktXRXh0YVc4dmFrZE9NMGQ1WWxGV1MzRkJVbkZxU0U0MU9XUlJkM1p6WTA1a1EzWjBVbWhzVWxZNVpHSldUWFpDVWtwV2NsZDZSMlV2YW05SGNubGtVVGsxTlcxSldtTTNURGhQWVhBd1pFWlJkRkEwV1V4cU5rZHVhREp1U3pKR2VIZzFPSEJUZFZGTU5sbGlSbWczVEhOVVlraHZkVmt5YWxCaE1HNURWVTFYSzI1aE4yUXlUM1V5WWpjelRXUktMMGx3V1dsbFN6RkJVblJ2UldORVRYY3hUbmhJUm1KNE9EQTRjMFpoWXpnelpWVmpNMnM5SWl3aWJXRmpJam9pWWpWbE5UWmlNMk5qT1RFMk1EZGlOekExT1RSaU5UTXdabVZqWkRsa1kyWmpPV0ptTlRKak1tRmlPV00wTnpWaFlXWXhPR0ZoTVdObFpqZGxObUUzTlNJc0luUmhaeUk2SWlKOQ==', 1772816980);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `details_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `details_id`, `role_id`, `status`) VALUES
(1, 'Test User', 'test@example.com', '2026-03-03 09:38:11', '$2y$12$7RurItqie1M03.17Vlsvdupn0qIKtNj9/u4XbZHfF4OKFuy0xIPIa', 'bbVACapz7z', '2026-03-03 09:38:12', '2026-03-03 09:38:12', 0, 1, 'Active'),
(2, 'Student User', 'student@example.com', '2026-03-03 14:39:50', '$2y$12$LygRec/4Mpd02V46NdNUeOxYefQfcMiBFo7N6oNQrVX/WDpXHYi5i', 'XfMYRxHkkQ', '2026-03-03 14:39:50', '2026-03-03 14:39:50', 0, 2, 'Active'),
(3, 'Archelle Agdon', 'amapagdon@gmail.com', '2026-03-04 11:29:21', '', NULL, '2026-03-04 11:29:21', '2026-03-04 11:29:21', 1, 2, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` int(11) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `mname` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `Civil_status` enum('Single','Married') NOT NULL,
  `address` varchar(255) NOT NULL,
  `Grade_Level` varchar(50) NOT NULL,
  `Section` varchar(50) NOT NULL,
  `student_no` bigint(11) NOT NULL,
  `contact_no` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `lname`, `fname`, `mname`, `birthdate`, `sex`, `Civil_status`, `address`, `Grade_Level`, `Section`, `student_no`, `contact_no`) VALUES
(1, 'Agdon', 'Archelle', 'Marc', '2026-03-11', 'Male', 'Single', 'asdasdsadasd', 'Grade 7', 'Section A', 123456789111, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;