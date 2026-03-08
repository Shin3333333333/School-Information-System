-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 11:46 PM
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
        WHERE		 u.email = p_email COLLATE utf8mb4_unicode_ci
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
    IF MODE = 3
    THEN
    	SELECT 	 a.date_posted
        		,a.title
                ,s.subject_name
                -- ,sc.section_name
        FROM	announcements a
        -- JOIN	section sc ON a.section_id = sc.id
        JOIN	subject s ON a.subject_id = s.id;
        IF (SELECT COUNT(*) FROM announcements) = 0 THEN
           SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No announcement records found.';
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_populate_fields` (IN `MODE` INT)   BEGIN
	IF MODE = 1
    THEN
    	SELECT		 id
        			,subject_name
        FROM		 subject
        ORDER BY 	 subject_name;
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
          END IF;
      IF MODE = 2
      THEN
      	INSERT INTO 	 announcements(
            			 title
            			,date_posted
            			,description
            			,subject_id)
		VALUES			(JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_posted'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description'))
                        ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))
                        );                        
  		END IF;
      COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date_posted` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `date_posted`, `description`, `section_id`, `subject_id`) VALUES
(1, 'Hey', '2026-03-08', 'asdasdas', 0, 1),
(2, 'sdfsdf', '2026-03-08', 'sdfsdfsd', 0, 2);

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
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `student_enrolled` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `section_name`, `student_enrolled`) VALUES
(1, 'Section A', 0);

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
('kLB5b7CgZTU2c1i3u3JuXc5ywy4He0hYhdewx80U', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbmRsYzFGT01UUk5VRU5zUlVWUFdrVlBTMDFyTTFFOVBTSXNJblpoYkhWbElqb2liMWxHWjBGNGIwTTFkRXBQT1RkcFZHZFJiVlZyTWt4V1V6VkRZbkZFTjBoQ01VNUJPUzlzVWlzd2VVaEhURk5RTDFNeGVrSlBWRTlHZVUwM2FscDZWRmROWTI5SVYwSnhXVE5RVHpSYU0xVjJabUkwY1dNclNpOUtTbGhCYURWVFIzbENkV3BGUzBWRldIRnNPWEJ6UzNNclNrcDNVVFJhYzJkeFluSjJWMEZrWkZkUVJWbFpTMmRMYTBSUE5FeFVMMk13V0VKMlVqbFRUbk00ZFhGMFptNXdRbGRIZGxSMFZXWlFkbUZDYW5SMk5Ga3ZTbXBpYVM4eWNERmpSRWRRWnpGNE4ycG1VMnd2ZGpnMFNWUlhhek5wVXpCNGJWbFRTSE0wTHpSTVRVdFFPWEZWVTNCUU9ITm5OSEJOWjBWblZsSkxWRlowWWxGalpGQXdRWGQ2TlhCTFZHa3lObFJZUm13M1pTdFdRVXRJUmpCVmVqUXhObEpLYVdsSlZHUndaMjFWY0RBNUx6Um9PRmxaY2tkck0xcGpNbWs1VDJZeVpIWTRNV0pwYjNBdk1GbFRXVnB6VldWemVIbEJTbGN2ZG5ScWJUQnpkWGRMZDBWQ1owSlpUakJZT0UxT1ZTOVdWMXBWUmxGSFMyaFlhMFEzU21waGJuVTNhRTV1TTA5elJYZG1RM1ZLZG1aWk1VeG1hbFZrTjJZNFQxWjRhRFpITDFScWF6VnJjMkU1UTBkemRHWktZbkF5VVM5VVVGbG5aMEppVFRaVFlYTnJSa2N5VFhZNWFXMDVSVlpaTDFkRE1qTldjWG94VmpRd1NreHlTR2M5UFNJc0ltMWhZeUk2SWpsaU1EYzFOV0k0TW1Jd1pqaGhaak00T1RGak9HUTFZak0xTXpsbU9URmlaV05pT0dReVpUWXpPVGd4TkRZNVpXUXhPV1ZsT1dFM04ySXhOVE5pWXpFaUxDSjBZV2NpT2lJaWZRPT0=', 1773013560),
('lLrtXhW9tPLSGCxYbPULhOzsV6ckAzmusbAB2YPP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJblpLTDNBMU1HNXJLelZWUWtSdlUwOTJRMjlzZWxFOVBTSXNJblpoYkhWbElqb2lXVnByV1VJck16bEhia3N2YlRad1N6UkxiVEZQWkZZMWRYbHBjbUpNTWt3d2FuWlVOM1JPUkZSRE5FWXdNamxMT0dKNVZXWlBaMDR5YjNCM1RGSk1hR1k1WkhsSU4wUklNbTB5WW1WVWRFeEZhVWR1VlRVMmRWQlZhVVJCYVRaa2FHWjZaRnBqV0hwVU1GZzNWVVUxUVhOVVoxQlZOamhYVXpkWGFWcHdNMVJDTmtwUFJWUlhWbWhRVERSeFNFVlBRM0JyWXpoSVFqRllXSEZqVDIweFFVSmlPRXBvTVhwVU5VaFJQU0lzSW0xaFl5STZJamd5Tm1Zd1ptWTRNamMyTURjd09UZzFOVEZsWVRjMk1qUmpaVFZrWWpjME5qTXpNRE0xWkdOa1lqYzFaalUwT0RjelpUWmhZams0TlRFNU5UZGlaRGtpTENKMFlXY2lPaUlpZlE9PQ==', 1773009250),
('ONnL6CsnUmExxX7YCsUE3G6aqejWxhKznmLAfw5d', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbGRaTUc5MmIxRklOVFZFT0V4VWRteEpWR3h2TmtFOVBTSXNJblpoYkhWbElqb2liRTR5TkdacFRXZHVWMlo2VWtoSWEzQlRUSEUxZDNoNlpYRmxWM3BYYTBRNFVIUlVPV0pDTlVWVFFXRjFWV1F4UWxoeVFrUklXbkI2TVZWSlVtMTJLM1prVkVGdWJVdEVibFp3U2xaMFJuZHpkMFpxZWk5MmNEQjZhak0zTjNoR01uZElhRlo0WkhBNVoxazBSVlYxYkVneU9TOUpOVTlRVWpkWGVteHVNVFJKZUhSSGVGVnJWbUp6Y0dsRVRWa3daSE16YUhkVVFsY3ZRM1pVUWpOaVNGVkRZWFUzVDI0MFVHNUpQU0lzSW0xaFl5STZJalF4WlRabU5UY3pNR0U0TldVek5tWmxNemszWlRjeVptSmtaVGt5T0dKak1tUm1ZVGMwTWpJMU5ETTJaR0l4TjJSaU5qUTJaak14T0RsbU1UYzFabVFpTENKMFlXY2lPaUlpZlE9PQ==', 1773009428),
('stNZ52mfoEA2bSxoVmPHqFUrCH0YWRWPo0k2Yhnh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbHBtUzJNMWMxbFpiemxGYm1vNVYyUkxTMDFzZDJjOVBTSXNJblpoYkhWbElqb2lZVFZLUVZWd1RURnFPVXR0YUdacFIzVnRObTVzZVZseVEwRkRlbUZpWTBsM1lUaHJiaXRMU1cwMGVUaExXRnAyTjFwUmVrNHlkemcyWWtGdFZsQlpSRE5hTTFSVmMyTkZlVzlHWTFselpuTXdWMkpKTmtaRGNIaHRRV1YxZDI1d1lpdHNVbXRXWld3cmRUWmlabTkyUjBST2VHSTVUMGxRU2pRM1EyaFRUVFozTDBSdU9VVkVRV3BuVjBaNVdVRjVNSEp6ZEVadk9IcG5RbGROZGxWeEt6VTFTazA0U1VOMmVqZHpQU0lzSW0xaFl5STZJbU5tWTJKaVpUaGhPV0psTVRreU4yWTVaVEEzWTJReE56QXpOV0UyTTJOa01XRm1NV1ZrTldNeU1tSXpZMlJoTURjMk9UbGxPREl3TURWaE5UTmtaRFFpTENKMFlXY2lPaUlpZlE9PQ==', 1773009251),
('xEv5rk9YgtOzaCTZSXiJXUnshL6UDRM29NTZnwqw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJakZhYmxCRllWcG9NQ3RSZEZWMFJ6SlJSV1JJVFhjOVBTSXNJblpoYkhWbElqb2laRTE1Ums1YVpUbHhWbXR6ZG1KVUsyOUtkVzg0VXpReWVsbFdXR1ZVYzA5U2VIcEVjVWszT0hobmRUZFhhVGN2V0hOdVNHMWpNVkp5T1dOQ1ZIbDFORmg0VmxwNFpFSjFhbkpDZDNwTVNYbDFkMjFHY2preFZtbHVLMlFyYkVoTlRFZFpUbU0yTWtGbFMzaHdaWEUyWm1kb05IWTFTWGxuT0ZWa1dXTlVUemhDU1dwd1lqUnBibEJTT1VKWloyYzBiR1JYUTIxeWJVTjZhSGQ1V0ZscVRVSlhOazg1WlZGcldHWXdQU0lzSW0xaFl5STZJbVE0TVRrek9ERTVOelE0WXpVMFlXUXhaRFF4T1RFMU1UYzROMkprT0dKbU9EWmtZelF3WldJMllqUTNNMkl6TjJVNVlqUmxOamhtWmpVNU16QTRPVGNpTENKMFlXY2lPaUlpZlE9PQ==', 1773009252);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `subject_name`) VALUES
(1, 'Math'),
(2, 'Science');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_details`
--

CREATE TABLE `teacher_details` (
  `id` int(11) NOT NULL,
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
  `contact_no` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_details`
--

INSERT INTO `teacher_details` (`id`, `lname`, `fname`, `mname`, `birthdate`, `sex`, `civil_status`, `address`, `employee_id`, `department`, `position`, `specialization`, `date_hired`, `employment_status`, `contact_no`) VALUES
(1, 'Hermoso', 'Sean Carlo', 'Nieto', '2003-09-10', 'Male', 'Single', 'B6 L23, Camachile st., South Plains 1, Sto.Tomas, Biñan, Laguna', '22-3850-51', 'Senior High School', 'Subject Teacher', 'Information Technology', NULL, NULL, '09063128626'),
(2, 'Balibay', 'Jhon Edduard', 'Kabak', '2004-06-08', 'Male', 'Single', 'adasdasdsad', '22-3850-52', 'Senior High School', 'Subject Teacher', 'Information Technology', NULL, NULL, '091212121212'),
(3, 'Abarrientos', 'Melvin', 'N/A', '2026-03-08', 'Male', 'Single', 'adasdasd', '22-3850-53', 'Junior High School', 'Subject Teacher', 'Information Technology', '0000-00-00', '', '09100010001'),
(8, 'uhihhkhjk', 'ssssdds', 'sdsss', '2003-01-08', 'Male', 'Single', 'ghdhgdhdhfhg', '22-3850-54', 'Junior High School', 'Subject Teacher', 'Information Technology', '0000-00-00', '', '091212121212'),
(9, 'Alenzuela', 'Karl Randel', 'asdsa', '2003-04-22', 'Male', 'Single', 'asdasdasdasd', '22-3850-55', 'Senior High School', 'Subject Teacher', 'Mathematics', '2026-03-06', 'Temporary', '09100010001');

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
(1, 'Test User', 'test@example.com', '2026-03-03 09:38:11', '$2y$12$7RurItqie1M03.17Vlsvdupn0qIKtNj9/u4XbZHfF4OKFuy0xIPIa', 'VS2gR6q8FqE1y8pqoOUtbxj7K6Nk2AF8S8Q3szkpNTHyJRoBBdqNkG5saHNN', '2026-03-03 09:38:12', '2026-03-03 09:38:12', 0, 3, 'Active'),
(2, 'Student User', 'student@example.com', '2026-03-03 14:39:50', '$2y$12$LygRec/4Mpd02V46NdNUeOxYefQfcMiBFo7N6oNQrVX/WDpXHYi5i', 'DVzYScnChTWAzKHA4asjOeXkI0VpZSX4nxzzfIq5Jidh5QD1D9Z1aRyv9d8K', '2026-03-03 14:39:50', '2026-03-03 14:39:50', 0, 1, 'Active'),
(3, 'Archelle Agdon', 'amapagdon@gmail.com', '2026-03-04 11:29:21', '', NULL, '2026-03-04 11:29:21', '2026-03-04 11:29:21', 1, 2, 'Active'),
(4, 'Sean Carlo Hermoso', 'hermososeancarlo@gmail.com', '2026-03-08 05:22:00', '', NULL, '2026-03-08 05:22:00', '2026-03-08 05:22:00', 1, 1, 'Active'),
(5, 'Jhon Edduard Balibay', 'jhonbalibs@gmail.com', '2026-03-08 05:26:22', '', NULL, '2026-03-08 05:26:22', '2026-03-08 05:26:22', 2, 1, 'Active'),
(6, 'Melvin Abarrientos', 'melvin@gmail.com', '2026-03-08 05:47:36', '', NULL, '2026-03-08 05:47:36', '2026-03-08 05:47:36', 3, 1, 'Active'),
(11, 'ssssdds uhihhkhjk', 'user@gmail.com', '2026-03-08 12:00:29', '', NULL, '2026-03-08 12:00:29', '2026-03-08 12:00:29', 8, 1, 'Active'),
(12, 'Karl Randel Alenzuela', 'karl@gmail.com', '2026-03-08 12:11:38', '', NULL, '2026-03-08 12:11:38', '2026-03-08 12:11:38', 9, 1, 'Active');

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
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_details`
--
ALTER TABLE `teacher_details`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_details`
--
ALTER TABLE `teacher_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
