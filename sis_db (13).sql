-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 12:29 AM
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
      COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year_label` varchar(20) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year_label`, `is_active`, `created_at`) VALUES
(1, '2023–2024', 0, '2026-03-14 20:54:15'),
(2, '2024–2025', 1, '2026-03-14 20:54:15'),
(3, '2025–2026', 0, '2026-03-14 20:54:15');

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
(21, 'asdsad', '2026-03-13', 'asdasd', 0, 1, 14),
(22, 'sdadasd', '2026-03-14', 'asdasd', 0, 1, 14);

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
(7, 13, 3, 0),
(8, 13, 4, 0),
(60, 14, 4, 0),
(61, 20, 3, 0),
(63, 22, 3, 1);

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
(1, 'adasd', 'asdasd', '2026-03-14', 'academic', 1, '2026-03-13 17:48:14'),
(2, 'asdasd', 'asdasd', '2026-03-21', 'holiday', 1, '2026-03-14 13:32:31');

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
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `effective_date` date NOT NULL,
  `status` enum('Active','Archived') DEFAULT 'Active',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `room` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `subject_id`, `section_id`, `day`, `time_start`, `time_end`, `room`, `user_id`, `grade_level_id`, `created_at`) VALUES
(1, 1, 3, 'Monday', '08:00:00', '12:00:00', '101', 14, 2, '2026-03-14 20:12:11');

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
(1, 'To provide quality education that nurtures the holistic development of every learner.', 'A center of excellence producing God-fearing, globally competitive, and socially responsible citizens.', '[\"Integrity\",\"Excellence\",\"Service\",\"Compassion\",\"Respect\"]', '2026-03-14 16:02:30');

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
(3, 'Section B', 0, 2),
(4, 'Section C', 0, 2),
(5, 'Section D', 0, 2),
(6, 'Section A', 0, 3),
(7, 'Section A', 0, 4),
(8, 'Section C', 0, 1),
(9, 'Section A', 0, 1),
(10, 'Section B', 0, 1);

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
('QaYI3Ejugg3JOtmtMXwuIvhPmF2fbEVOQTgHX4Ua', 16, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbXRhWVRGWllXRlJMMGR4T0hNeWMyUnZTbEkzUjBFOVBTSXNJblpoYkhWbElqb2lkeTh3TDBwMGVrSnJPVFZPWWl0aFprNU9NSGxNUlc5TWNqSllWMkoxWnpaQmJtNW1NelZTY1U1RlQwZzVhR2Q2UjJKQ05XZHRNMjkxVG5ocmNUbGpSRGt5Y2tGQ1IzZExRME12TmxGQk0ycHVPRkkyWjIwd1lUSXpUbEJRVEcxcEszTXhLM0pwUkhaSlowdGlNV2R1U1VZeFpGcE9jaXNyVkV0RmJXOXFXWGQ1ZVhoQldFZGtSVWt2WjBoaFUyNUhlVVZPY25wWUwwWjVUVlIxZFdFd1VtRnFPR05IV0hWd1JGcDBkM2t6YkVSSVIyUXZWMHA2T1VkYVJYVnFiV2hOUldaQ1IwMXJRakZNWjJGVE1FVkpVbXRhWmtZcmRXNDFTWFZETlRkTWVrOVFlWEpFVjFwNmVqZzBkM0ZOVXpJMVVtTnZja2cwZFRWeFFqTlJieXRUUjNkck9IQjFOMWRxZEZBdmMwMVBUbEkzVVU0clVURnpjMGhLZVVsdk5ISTJOVVJzU1V4V2JFUnBUVEl3UlZsb1lsVTNhSEJMVVhGTlQyeE9WRkV4V25kMU1WSnpkM2h6VFZSYVlWaHFNRXg1ZG5KV2JreDRTV1JwWkRGTE9Dc3pVR2Q0UVVGRk5WVmhVMnBrUkd4UGEycEJaazV1TkhobFlsVk1PV1ZPVm10SlQxRnViR05DVWtOelNWbEViSEZwYnpWS1R6aEtSRVoyYVVWVGVtbDFRalY1YVdrd1dWQTRiMnhzT0QwaUxDSnRZV01pT2lJMFpqZ3paamRrTURRMk5EQmlNVFEwWmpJeVlUbGhPVFkyTWpJeU1ETmpNbVZpWWpVeE1EbGlZVEUyWkRKbE9HRTBPVEZtTkRrek5UZGpORGxoTW1NNUlpd2lkR0ZuSWpvaUluMD0=', 1773533819),
('RpD3oBawmOG6lkN2ZIFiro0BiV5KYP2MERQy7ndR', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 OPR/127.0.0.0', 'ZXlKcGRpSTZJbkpGVkdRemNWVkRiU3RKTlV4SmVrbG9ZM2hYY0djOVBTSXNJblpoYkhWbElqb2lPRFJaZGxjeFJERXJSMVVyYWtOWE5FUlFTbmxHYWpKRWVVRlVUVWRVYTJsYVRXeDBiVGN3ZFRSRU1HbHFVMmhwV2s0NFJIZE1lVUl6V1UxSFVuWkNNalIzVFhocFRteE9ZbWhLWmtSQk9URjROVUZsUkRRMldqVkxOa2hhYkdOb1oyazNjR1ZpT0N0WlZtcDFWVzVDTTBoUlFsUkRRVEJJTUZKeGJrMXFSWEJ0Tkd4eldTdFBWVTVtZVhSUFRVeEZWR0pFTkhnd2J5czRRa2Q1V2pCdWFYSktkbVJhVUN0SU4yNU9WalF5TkV4SFpGcElOR3c0TUVwRWJIVmhSbWxNVnpSaGVXSmhVbW96TW10b2JtWlVOV2gyYkhCbmFUSm9hamhJYURkRGQybFNkalEwZUVWak5IZE1OVlZZV1hKRWEycE5NRGxJTlRseEwwSTJUMjFtZEVOaVlrdHdaVEkyYWtGSmRrOUVhRXh2TDNsaloyeDNaMU5KT1U1MEx6TllVRmhVZWtGdVpUbEpNbE1yUW14T1NqSlpaV0k1ZVRrd2NHMTNibWt2UW5WdWVsa3ZLemhzU2xkQ2JXMHdhSGRhUWtkbFVHOVhRMUEwZUdWdk5VOHpRbmdyY0RWSU5IUjNaVXBWZUhZd1J6WlhRWGg0Y2tVNFJrVTBUVzFSTDJSd2JEUnpSa2M1UWsxRFQxWkVOakIwYkhwTlJuRlhTMVV3TnpjeVJrdzVWakZJUjFreFF6QkNkMHN4WnowaUxDSnRZV01pT2lJNU9XTmtZVEpsT0RWaE5XSmhaR00yWTJSaU9XUXlOamt5T0RnNFlURTBabVZpWVdSbU1EYzVNekE1TTJNME0yTXdaalZtT1RNeE1qTm1aRFEzTTJJeklpd2lkR0ZuSWpvaUluMD0=', 1773529276),
('VjAr00UNvQ955PiaewUBOn2qDnQ6btcXUPKWl3bg', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbEI1Y0daNWRIWTRkWFZKWnpGa05HTnFiRE5ZYm1jOVBTSXNJblpoYkhWbElqb2lRV1pOU1dNelJHRmxWbk5ZZWpsMVoyVm5ZbWhXVTFSSFJ6VTVjVmRhVUU0M2MyNXJkeXRDVkcxRFNrTjRZa2hYZWxwQ1ZFRTFZbXd2YVU0clQzTXZjRkpuWTJKTVdFcDZia0pLYmpSbVpGazFiRE5yVHpoVGIwZDZabU01ZEhSdWNWb3ZOWGhZZGpoc1FtdzViV1JJUWk5SVVYWlBZMUI1TnpOSE0xUldaRlpzT0dwdU1UQm9VME41YmpabVduVnNWQ3RLTWxsR09YYzBkVmhzY201dlFXd3daaXQ1Tm0xR2FUVjRXakZuYTNwVmMyTldjM05uVWtrNVQxcG1aMDV6YlZFM2NHRnFiMnAyZVU1eFNrSXhLMVZ2WW10Sk4wSXZZa2N3Tm1oUGJYcDNLekpCVW5GVVducG9VSHA2VlhKd2NHeDZRV3g2VDFSallWWm5UMVk1ZGtGeGNWZ3lZWHBRZVU1b1FUaFFkM000YzJ0cVJuQkpORXgyZDNSbGVtWlRZM1ppY2xKUVlVaElkSFJDWTBsWlFYVjVabHBOY25oT1VHZFhTME5VUlRCM1ZIcDVVbGRxY0ZRM2NHNVlla1V3VkhSTWEySTFhWGt5ZWxkS2NIUTFSR3hDTW14bGJFTnZWMmRWVmpSalEwSlVhSFZEU2tnM1NGQkhhV0Y0ZVZCNVpGTmFOWEJHZFVSVWJrNXdNM0JtVUVGRk4zWjFlR1p1YVhjNFdXcGxTRE42YUZOV2VsYzJWVWhsZEM5WGMyRm5Ta056WkU1MmJVcDFZMmxwZEVGQmRGQjBVRU5TYkdaVWRIVnVaSFoyY0dVdlVWQnpURTAzVnpaVU1HUTJNMGd6Wkd4UlQxcG1Ua3RXWkUwOUlpd2liV0ZqSWpvaU5qUXlOVGxpTXpBeE5qRTJNalJqTnpSbU56Z3lOREUwTjJVMFlXTTNOamt3WWpnd1l6TXpZalV6WlRJek9XTmlNVEl5TWpoa04yUmtOelF6WkRReE5pSXNJblJoWnlJNklpSjk=', 1773531122),
('ySpW5HfPjy31FgbyZNdTVXpVzGspmEbOaWxYMinV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbVV4Y25aYU5IZHpOM2hTY1hwSFptbFhha1JyZFZFOVBTSXNJblpoYkhWbElqb2lSUzl4Tm1wYVdVMVNXVWRTYlc1bVJuWkpVWE41VkVJMFowbHdXazV6VFVJMVRXb3ZkWG93TmxaRU1qVmxVbGxvUW5wMlVHNVdWV0UzWTBOWVRYZENkWGs0SzFKaVpVcHJjRWR6Vm5sa09YSmFRMlJKTUVjeFFteENNRmhpYkUxQmExaFJkMVEzWWpjM2RGZDZLMHRKTDFKRFVtdFljREJMT0haUllXRTVURU5CZUZKQlVFdG1iVTVHZUVRMWVIRm1kREpJYm1NMU4xa3hPVFpYY0ZaTFNWVTVVelF3Y0hsRk5sTXJWMnhhSzFoTlkwMW9hSHBYZUhadE1tTllaRXhaVDIxaVNYbE5OVEJ5V1hWaWRGZzNRVlJEWkdka2FXeDNkR1k0ZW1kWllYVlRPRkZvWW1SNk5rcElOREphTkdSc2VFSm9TWEZwUW5SU2FFNHhWVzk0WW5kVmVtOXFaMGR6T1dkRk5XVldZazlMWmtWSGFFbEhWbEJHUkV0d2NVSnBhelFyVG5GalFrVXlhREpFTVVGM1lpczBRMFpCY1ZnMU5tSnpOamxXVmxkdU5WUTNSV2RSUkU5TmNGbEVRVU5UUVZoWk9XRkNVRFZ0V2xSM1FVaFZXVlUzZWxONmF6SkhjVmhvVlVGUmRGb3piazR4ZGtveGNVVmtiV1UwWjFGMFMwMTNZV3haV2poTVQxcFNjMEZHT0cxTFZUZHFRVDA5SWl3aWJXRmpJam9pTVRVMk1UVm1ZV0kyTnpGbU1UWTBNRGt5WW1Oak9UYzFOR014TldObVlXWmpabVJqTnpabU1EUXdPVEl5T1dJd016bGhNVE5tWWpKbU1HSmlZMk5sT1NJc0luUmhaeUk2SWlKOQ==', 1773531209),
('ZeaXO2wKotalZSizzOIVVpiq0BZI8EBmRosg6SkJ', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbFJ1U0ZjMVpGaE5UMHBMU3paVWJXSkJhMkpMVm1jOVBTSXNJblpoYkhWbElqb2lZM3BHVUU4eGFTOXFLekJxVm1sTGVITnhVWFZHT1U5U1NrbzRNRzQ0T1VOUGVscE5OVlZxZG1Sa1NVUlFUbmxwVFN0cmIzUXZXUzl3ZUdWNFRXWTRlRVpOWmtwd05IVTVUR0ZCWkVkaFdtbEplbXhpY1RaS1pGQnRiRlpsTmtWR0wwUTNkRTVJSzNOYVFUZDNiMFpFVDFsUU9FOWxhMkZLVlZWaFMzcDVSblF2WjFodU9IUm1jbGh4VDIxbVNXSktjSFZXS3pVMGJYcENiRFZpZDNOWksyTnhkU3RJYm1VelVUVmFZbWxFUkdONVRteGhkMlk1VGxWeU1WaFBPVGQyZFZSQk5Fa3lTemx4UWpaeU5VTnRTVkpsTldrMmIyZ3pRVXhHZERNMWEwZHJVMGMwVXpSdVQzVnZWamt4VWpoVGFsaENkRmxyYlZsU1pUVlhNa2M0Ym1JNVNITjVUWGRsYTFJd01qbExSR0kzWW5STmFGYzBlR1JOWjNoMVExWkNXalpSY3pWbE5uSk5NVEI1T0RWeFMxcFRLMDFCTDNORlYzVldla05TYjA4ek9FaEViM2hzYzFSeGRuaENibEo1TVVSaE9XNTVibFp5VERkcFNFWkRiRTEzYlRodVJGVTViMk0wU0Znd1Fua3JlV05XY0U1UEwwNUxPSFV6WVZRM2VtVkplSFZpVml0WVdXNU5XVGxLVTJwUVdpdDFVVDA5SWl3aWJXRmpJam9pWWpGaU4ySTJNamM1TURKaVkyWm1ZamxqTmprM1pHRmpZVEZtT0RRM016a3lPVFl4WlRjM1ptUTNObUkzWlRBMFpqQmlabUZrTkRJMllUVmpPRFl4TXlJc0luUmhaeUk2SWlKOQ==', 1773530921),
('zfMjw1SlAg8kjeTxk7vLfDWhv54BGGZqFm4fODRw', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbTlFZGtKUVkzUndhRGswT1cxUlFsWjNabWx0TTNjOVBTSXNJblpoYkhWbElqb2lWVEEyUlhWVlZVMXZUalJtVm1rcmFUZElSelpxYW1JMk5FOUZNV1ZHVlcxSmJrMVVNVWwyUkZaQlVqWkZObnBsT1VGRWRVNUZRVWhoUVhSeE5FOWpNRUowWTFwTWVVUmxRbWxHWWxkM1RYa3hjakl3Y0V4WVpqZGpZVk5ETldwRFlqbDVTMGxIT1ZwS05FOVRTa1J6ZGs1VE1scE1MM2hpTm1GTlltODVNemhKWmtOUlFUaHFaSGt6UTFaTVpYcHZTalowWWtoRFVHNTNNR0k0Vm5OeGJtTk9VV2RDYXpsa1FUaEZXRGczVUhkRlIweDBUbmhUU0RKSmRETmhSQ3Q2UVVsUFl6ZHJNVzR3YmtOc05qUTBOR1J2VDJsWE1pOTJhRmxPYjIxUWFtMWtSSHAyYjFOcVZVSlBieTlTVVN0NVlrNHlPRVpwWWpCd2JYTTJVR1JKYkd0WUsxcE5aelJWZERSQ2FIWmhieTloTVZWalN5OUdNbnBUWjJKM1dUWjFPU3R3UjBWdVlrUldiVXR1UjJOVFRGRlllRVZxWTBWMVZHMXBTMFozUWxsaWVUZHBWVkp3V0U4MlJUbGthVmhDVEhZMmVHeFRNa1p4WjBoMk1taG5TMlZNV1ZwdFFXNVliMVpUVm5KbWREVXlaVmxQU3pOUFUwdFhaRWhWWnpsd01HTTRRMmMyTkhjNEswSXhXazFoWmxablVVbDBlVFpuWTBoRE0wSjRaMUY2WnpCcmEwcEhSa2w2ZUU5b1ZuWXZkbEJGTVdoRFpscFlUVEYzUlc5b05taE1aRFJLTUhwU01YSnRhbVZTY25vd1kwODFORGhxVVU5bVFYZG5NR0pCWjNwUVdHNHhlRWhyUWtFOUlpd2liV0ZqSWpvaU16STNNbVUwTUdFd1l6WTFZV1poTVdNNE1HWTFNV1l4Tm1ZMFpEWmtaREkyTmpFM1lqaGpOemhoTURabVlXWXhPVFkxWVdZek1UTTJOekJoTVdKbU55SXNJblJoWnlJNklpSjk=', 1773531207),
('ZRoa21k8TY5hcZzS6YqxFHeNjvTG7w8ztjGrLPCC', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbmgyYVM5VVVHODBOV3cwVmxCRk4wTnZVWEUzVEZFOVBTSXNJblpoYkhWbElqb2liRGxETlUxTk1taG9VMVJxZG14dVZsZHdXRXMxTjJwVlFWTnpOMjAxTkM5QlZHVTBOSFJuVXk5S01uQjJiQ3RFYmxjMmR6bHVZa3hTUmxCUGVrbGFUMHBDUVRONmEycE1lbmc1SzNCRFJHVnNkMHdyV1RCS1QzcGxjWHBaWjBSNFJreEJORWRCVm5KWlZtTkNZa0ZJVkVZMkwxQm5hRGdyUVhKVEszQkhhM051TmpGTFFtb3dZMkkyVVdKV1YwOTBjRmxUVWpWVlZVVkJUMmRHU3pCMWJ6TkpVbVJFTURCT2VqbHFaRVpVT1dkQmJtZDJaVFJUWjBkVmFGUkxjbGRSZUhaRmVHOVlVbVo1T1hwc2N5dFZaMlJ3YlZKNVdUSkZOMWRqTW5SV01ISnNWWGhWWTAxeGNsQjZjRFZvUW5sS1JIZFROWGxXTlhKUVdXUnRiWEJzVUVOR2RuVnBZelpFV0VKSWIyZENXbkpXYjFSWFNXRTJPWFZuUzJwcU1YUXZkVUU1Ympaa1prTmhiWE01YjFkMVFWTTBWakV4TkZOTmFFNDJXREppTWtKUVl6VXhLM0ZETkZSbVUzazJabTFsYzJGeGN6ZHllakYzVURKaWFWQlZSbnBYWTJkWlYzVlRXSFJWVmtsdWNrSkVkQzlPZWpRMlQwVk5hemxRVm05eVdtVXhSR0o1TVdWbU56TTJVVUV6YVVaVVZHeHNWbGhzU0NzdlQzQjVjR1YzU2xZd2NFTk5URVV2YldaRWRVdE1SMnQ2V20xSVdscGlOVFZpUjJnM2RYbFZRbWRKZFM5bE0zcFFaRk5YYkVOdGIySldObk5xYmt4NVNqSjRlSFE1WmpSWVNtVXpNRWREZEVFOUlpd2liV0ZqSWpvaVpUYzVZbUptWlRoaU9UY3pNbUZtWW1NNVpUZGpOREEyT1RNMU56Z3lOVE5rWVRJMFlUQTJZVE0yTkRFNVptSmxNV1JpTmpSa01EUmlZekU1WldJd1pDSXNJblJoWnlJNklpSjk=', 1773530967),
('zzeeHtebbiHPpKOR9aRplStuFpcPPHdtaHTiB0cN', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJbE16Ukhsa1FuQjRMMjVKVEUwcmNrVnZTVEp6T0djOVBTSXNJblpoYkhWbElqb2lWbXN5Y25GQlVYUk5jRmwzU0dkMlQwWlRUMVYzTWk5RGJVdzRZblYwUm0xMFdVcEtURTByUW1oSFlqTk9kWHBCVTNvck5XOTBWRVV3TkVRNE9ESldTRFU0ZWxGdFV6WlVjREZrWXpWUmVHNHJjR0pZWWsxUGFXVlhiRGRrWm5Oa2JsTXZRM28xYWxveVp5OUllazl2WWtsS04wSnFhR05WUlZSVFpWRjJaRTg1YUdsUlVtb3dRbTVzSzJSUE16VlhUME00YlVsRWIyUmFUR0ZzY0VncmIyMWtiamhZUkhsM2IyVnVUMGQ0YTBOblIweGpTWEF3Y1VGdWNsVjZkSFY1VDFkMlltOHlia0ZUYkVka2QzWnNNRXRTVlRka2EzVnBVRGhHTTJnNVMzVjRRaTl3WVU5U2RWWlhabFJEWTA1a1RtaEJPRXBxU0RFMU5FWTJOV2RyYXpoR2RFNDBVMFZVU1VadVVXd3djVVpPTUd4TU9XMXdXbFUxSzI1SU5GTlVabWw2VVRkeVJuTkZjamhMUkRZeU5uYzJaV2RoTHpSVloySkRTVmxxV214YVZTdFlTbEZvVVVKSFUyZFlVV2N4YUM5VE5FUjZSVUZOTUZsNmNsQmxXbTlPTlc0MVJpdG1TMVZRYzBZeU5tdzFjMjh5VHpjeVZFVmFNazVYVFhOa1pISm9TRE5STkRFeGFHSndRMHBOVTFoM1N6TkJlSEYwYm5kTFoySllSV0ZKYWxsNWEydFFabkpaTUQwaUxDSnRZV01pT2lKaU5qaG1NakEwT0dRM056SmhPV1JtTVRVM016UXhOekkxTURrME4yWXpNRGcwTnpZd01XSmlNakJrTldJM05XVmtaRGhqTUdGbE1qa3haVGMyWVRjMElpd2lkR0ZuSWpvaUluMD0=', 1773531066);

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
(1, 'Test User', 'test@example.com', '2026-03-03 09:38:11', '$2y$12$7RurItqie1M03.17Vlsvdupn0qIKtNj9/u4XbZHfF4OKFuy0xIPIa', 'gdjuPFVrmuacQer9zc0Cb2ZjkOZItuLz6YsmrkOAbQ1Bl9N1hErMPmIwWzgp', '2026-03-03 09:38:12', '2026-03-03 09:38:12', 0, 3, 'Active'),
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
(16, 'Kristin Chine Calip', 'kcc@gmail.com', '2026-03-13 14:58:27', '$2y$12$2RxiGO86V6wozvkfFdzxOupnas4I2RjQcdTBWMckBMPDixo724rHa', NULL, '2026-03-13 14:58:27', '2026-03-13 14:58:27', 3, 2, 'Active'),
(17, 'Bienvinido James Publico', 'bienvenido.publico@cdsp.edu.ph', '2026-03-14 21:00:29', '$2y$12$PfEEj78cz6dmsYK.3LNIiOJGl7BjKtQBQExQUurt3McmfYGXrEbgi', NULL, '2026-03-14 21:00:29', '2026-03-14 21:00:29', 4, 2, 'Active');

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
(3, 'Calip', 'Kristin Chine', 'adsad', '2026-03-13', 'Female', 'Single', 1, 1, 'adsadsad', 222222222222, 2147483647),
(4, 'Publico', 'Bienvinido James', 'Mangao', '2004-03-31', 'Male', 'Single', 2, 5, 'Maligaya 1, Pacita Complex 1. San Pedro City, Laguna', 123456789012, 2147483647);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_schedule_user` (`user_id`),
  ADD KEY `fk_schedule_grade` (`grade_level_id`);

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
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `announcement_sections`
--
ALTER TABLE `announcement_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `school_info`
--
ALTER TABLE `school_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement_sections`
--
ALTER TABLE `announcement_sections`
  ADD CONSTRAINT `announcement_sections_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcement_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `fk_schedule_grade` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_level` (`id`),
  ADD CONSTRAINT `fk_schedule_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
