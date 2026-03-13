-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 06:50 PM
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
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_populate_fields` (IN `MODE` INT, IN `p_grade_level` INT)   BEGIN
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
           	INSERT INTO 	 announcements (
                             title
                            ,date_posted
                            ,description
                            ,subject_id
                            ,user_id
                        )
            VALUES (
                             JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                            ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_posted'))
                            ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description'))
                            ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))
                            ,JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'))
                        );
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
        IF MODE = 4 THEN
            UPDATE  	announcements
            SET     	 title       = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.title'))
                    	,description = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.description'))
                    	,subject_id  = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.subject_id'))
                    	,date_posted = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.date_posted'))
            WHERE   	 id          = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.id'))
            AND     	 user_id     = JSON_UNQUOTE(JSON_EXTRACT(p_json, '$.user_id'));
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
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `date_posted`, `description`, `section_id`, `subject_id`, `user_id`) VALUES
(1, 'Hey', '2026-03-08', 'asdasdas', 0, 1, 0),
(2, 'sdfsdf', '2026-03-08', 'sdfsdfsd', 0, 2, 0),
(13, 'Preliminary Exams', '2026-03-12', 'Upcoming exam on March 14-15', 0, 2, 14),
(14, 'Math Long Quiz', '2026-03-12', 'Nyark', 0, 1, 14),
(20, 'asdsa', '2026-03-13', 'asdda', 0, 1, 14),
(21, 'asdsad', '2026-03-13', 'asdasd', 0, 1, 14);

-- --------------------------------------------------------

--
-- Table structure for table `announcement_sections`
--

CREATE TABLE `announcement_sections` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement_sections`
--

INSERT INTO `announcement_sections` (`id`, `announcement_id`, `section_id`, `grade_level_id`) VALUES
(6, 13, 1, 0),
(7, 13, 3, 0),
(8, 13, 4, 0),
(59, 14, 1, 0),
(60, 14, 4, 0),
(61, 20, 3, 0),
(62, 21, 1, 1);

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
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_type` enum('academic','admin','holiday','activity') NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_type`, `created_by`, `created_at`) VALUES
(1, 'adasd', 'asdasd', '2026-03-14', 'academic', 1, '2026-03-13 17:48:14');

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
-- Table structure for table `grade_level`
--

CREATE TABLE `grade_level` (
  `id` int(11) NOT NULL,
  `grade_level_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_level`
--

INSERT INTO `grade_level` (`id`, `grade_level_name`) VALUES
(1, 'Grade 7'),
(2, 'Grade 8'),
(3, 'Grade 9'),
(4, 'Grade 10'),
(5, 'Grade 11'),
(6, 'Grade 12');

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
-- Table structure for table `school_info`
--

CREATE TABLE `school_info` (
  `id` int(11) NOT NULL,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `core_values` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_info`
--

INSERT INTO `school_info` (`id`, `mission`, `vision`, `core_values`, `updated_at`) VALUES
(1, 'To provide quality education that nurtures the holistic development of every learner.', 'A center of excellence producing God-fearing, globally competitive, and socially responsible citizens.', '[\"Integrity\",\"Excellence\",\"Service\",\"Compassion\",\"Respect\"]', '2026-03-13 09:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `student_enrolled` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `section_name`, `student_enrolled`, `grade_level_id`) VALUES
(1, 'Section A', 0, 1),
(3, 'Section B', 0, 1),
(4, 'Section C', 0, 2),
(5, 'Section D', 0, 2);

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
('V8kgvFGB5g3bzJPifLXBzLcyNypI5jvVpwm27wh0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'ZXlKcGRpSTZJbkZrZDA5dGNFOHlhelZvTjJFNE9XTmthR2xVZWxFOVBTSXNJblpoYkhWbElqb2lUMUJqZEVnemJrVklaVGhzVFhCRlExWnFXalEwUXpKTmVrODBOalZIYUc1MGJrUkNjREEyVW1ock1WVm9Rbkp1TmpWeGRVczVkWFo0WTB0bmNIcGxXRzU0VldFMldpdDZMMmxLVldacllWUm9WRzFGZUVoT2FFRlhaVU55WkdWSFIyNUZjazgwT1VOMVowbHpUWGQ0YzBSbE9IVktZbTlwUW1wSU9EaDJiVWRSVmxSVFJXRTBZMDFyZUZCeU9XNHJTRFIwWjBRck5rSjZUSFozTVdZcll6RktRMUIzVFhkelkycHNiMDVXZVM4d2JFZHhXVE41U1dOb1drSmhjWGhLUjFoek1HaDBRazFsVG5kTFZWWkxPRGhSUVVSdGJVcEJWVVZ2WlVOSWMxSXlZVEJtYjFKNk4zZDNOVk13VFV0WE9UZHhNRGw1ZG1kcFZYTkNiRWhISzBoa1YySlhhbGhpTWxReU1EaG9MM1kzY2pOemVISndWRVYzVDJacWVUUXJNREZxUkZkTGQwa3dObU05SWl3aWJXRmpJam9pTW1Nd05HUXlaRGhsTnpaa00yTTVNRGd4TkRnMk0yWXpaalJsWkRBM1pUbGtOV1JpWTJNeU5HWmpZelpqTm1Jd05UTXpOekJpTm1SbVlURXlNbU5sWlNJc0luUmhaeUk2SWlKOQ==', 1773427800);

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
(9, 'Alenzuela', 'Karl Randel', 'asdsa', '2003-04-22', 'Male', 'Single', 'asdasdasdasd', '22-3850-55', 'Senior High School', 'Subject Teacher', 'Mathematics', '2026-03-06', 'Temporary', '09100010001'),
(10, 'Tagalog', 'Robin Christian', 'wala', '2001-06-25', 'Male', 'Single', 'sadasdsdasd', '22-3850-55', 'Senior High School', 'Subject Teacher', 'Information Technology', '2026-03-10', 'Permanent', '09063128626'),
(29, 'Baliong', 'John Conrad', 'Iliw-Iliw', '2026-03-10', 'Male', 'Single', 'asdasdasdasd', '22-3850-57', 'Junior High School', 'Subject Teacher', 'null', '2026-03-10', 'Temporary', '09063128626');

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
(1, 'Test User', 'test@example.com', '2026-03-03 09:38:11', '$2y$12$7RurItqie1M03.17Vlsvdupn0qIKtNj9/u4XbZHfF4OKFuy0xIPIa', 'yRmX1i5A8j6W2tylYyJV2HiyDj1Tz7AdKSvFG6xMdPtjz6hYih9iM5NAHTt7', '2026-03-03 09:38:12', '2026-03-03 09:38:12', 0, 3, 'Active'),
(2, 'Student User', 'student@example.com', '2026-03-03 14:39:50', '$2y$12$LygRec/4Mpd02V46NdNUeOxYefQfcMiBFo7N6oNQrVX/WDpXHYi5i', 'upABd3lKb0c4k7stxNr9jwLvovvWz2BbktwMzYJnee0LzXvUzNJPyD00ugL7', '2026-03-03 14:39:50', '2026-03-03 14:39:50', 0, 1, 'Active'),
(3, 'Archelle Agdon', 'amapagdon@gmail.com', '2026-03-04 11:29:21', '', NULL, '2026-03-04 11:29:21', '2026-03-04 11:29:21', 1, 2, 'Active'),
(4, 'Sean Carlo Hermoso', 'hermososeancarlo@gmail.com', '2026-03-08 05:22:00', '', NULL, '2026-03-08 05:22:00', '2026-03-08 05:22:00', 1, 1, 'Active'),
(5, 'Jhon Edduard Balibay', 'jhonbalibs@gmail.com', '2026-03-08 05:26:22', '', NULL, '2026-03-08 05:26:22', '2026-03-08 05:26:22', 2, 1, 'Active'),
(6, 'Melvin Abarrientos', 'melvin@gmail.com', '2026-03-08 05:47:36', '', NULL, '2026-03-08 05:47:36', '2026-03-08 05:47:36', 3, 1, 'Active'),
(11, 'ssssdds uhihhkhjk', 'user@gmail.com', '2026-03-08 12:00:29', '', NULL, '2026-03-08 12:00:29', '2026-03-08 12:00:29', 8, 1, 'Active'),
(12, 'Karl Randel Alenzuela', 'karl@gmail.com', '2026-03-08 12:11:38', '', NULL, '2026-03-08 12:11:38', '2026-03-08 12:11:38', 9, 1, 'Active'),
(13, 'Robin Christian Tagalog', 'robin@gmail.com', '2026-03-10 14:00:03', '', NULL, '2026-03-10 14:00:03', '2026-03-10 14:00:03', 10, 1, 'Active'),
(14, 'John Conrad Baliong', 'JCB@gmail.com', '2026-03-10 14:23:07', '$2y$12$s7ctx83Bd9FzZkTJMajriuOeCi0oBciN4qiE4g909nGcAhUW.hn1.', NULL, '2026-03-10 14:23:07', '2026-03-10 14:23:07', 29, 1, 'Active'),
(15, 'user student', 'student1@example.com', '2026-03-11 17:41:50', '$2y$12$dqdMYeKFtJejXPKCNPpCVu6.6vt2aKcOCyUlW11CvG9DkgwC2MCES', NULL, '2026-03-11 17:41:50', '2026-03-11 17:41:50', 2, 2, 'Active'),
(16, 'Kristin Chine Calip', 'kcc@gmail.com', '2026-03-13 14:58:27', '$2y$12$2RxiGO86V6wozvkfFdzxOupnas4I2RjQcdTBWMckBMPDixo724rHa', NULL, '2026-03-13 14:58:27', '2026-03-13 14:58:27', 3, 2, 'Active');

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
  `grade_level_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `student_no` bigint(11) NOT NULL,
  `contact_no` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `lname`, `fname`, `mname`, `birthdate`, `sex`, `Civil_status`, `grade_level_id`, `section_id`, `address`, `student_no`, `contact_no`) VALUES
(3, 'Calip', 'Kristin Chine', 'adsad', '2026-03-13', 'Female', 'Single', 1, 1, 'adsadsad', 222222222222, 2147483647);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement_sections`
--
ALTER TABLE `announcement_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_level_id` (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `section_id` (`section_id`);

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grade_level`
--
ALTER TABLE `grade_level`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `school_info`
--
ALTER TABLE `school_info`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `announcement_sections`
--
ALTER TABLE `announcement_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_level`
--
ALTER TABLE `grade_level`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `school_info`
--
ALTER TABLE `school_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_details`
--
ALTER TABLE `teacher_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_sections`
--
ALTER TABLE `announcement_sections`
  ADD CONSTRAINT `announcement_sections_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
