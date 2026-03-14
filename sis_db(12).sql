-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 08:19 PM
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
(3, 'Section B', 0, 2),
(4, 'Section C', 0, 2),
(5, 'Section D', 0, 2),
(6, 'Section A', 0, 3),
(7, 'Section A', 0, 4);

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
('dRpAFqeChx2P2Fe72IP5NHvEtvmdlUIJR3cmNySH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'ZXlKcGRpSTZJa3AzZHpkalNqVk1NM05tYW5GV2FIVkhkV2RhWlVFOVBTSXNJblpoYkhWbElqb2liSHBHTkROc2RrNXBOVTVIUzNNM1ptTmlhRXR6VjJjMk9FMTROa2hPV21GU1FtbEplRWg1V0hoTGNHMXhjM0ZMVEdWWk5sbE5kWEp2V1daS09WbHpla3hIUW5KWWN6QjBjMG8yYkhaM2VIRkZkbFJHWVVjeGJUUjFRa1YxY21Jd1pqUXlkV3RYVFdoSmIzbzFXa0pvTjBSRGVWRnFaMjVZVEM5Uk4ycDVWR3RrT1hoYVpVdFpkemhJV1hOa01VaFVaVmN3ZURWRE1qTjBkaXRsZG5GeWIyMVhXWFZpV0hGeVZrSmhjbkZrU0ZwRlNDdE5jbXhYVWtGT1QyUmhZek41SzNKWlkwa3dOU3RrYldaYWRXNVFXSEI0WTNneU5GRjNVVnBzYVZaTGFpczVia1JKTTI0clpVMWFZWEJoTVhnclUwaEJNRUkxU2psSmRYWnFNRTVSWjFGaVRXRmhRMk5LVXpKUFNXbEVjbEZZVTBkeWJtZHhXVkJ6V0hvdlJXMUhNekE1ZHk5R01ESjVUamxoV0dOdGFrVXdlSGwzWXpsUWVFeGFMMGN5V0ZoNVRqSTJSVVo1TkRkM1EwUXZRemhGYWpabVozaHdOWE41U1Roc1JHMVlabmQ1WlM5dFlYb3JhbE16VTI1c1VWZzFhM1p6UVZaU2JGVk1WRGhDZDIwd1FtSk1SV3hTVVVNelRHOTZOVUU0U0ZoNmIyaEhkMDlaUkdKemNrb3dMMUY1TWpScWJYTXZOM042TUhkdVUwOWhjVWRMZVdveE9EVmxSR3M0YzBnMWNGZGtPRW95V21wUlUwaE9XV3RJYW1KcWFrRkRTVU5SZEZndlYxZ3dLM1ppZUVSVWRVVmFXbFJYYlRWWWRrNTVMM1JWVDNKTmMzRjNRM1prZEhVNWRsQmpUalJyYzFGaWRrOWpha0Y1VG04MFVVUndaVVJhV1ZKSFZrRjJWbUk0ZFdaQ1JFNVBTREpOTVd4dE9TOVZUVTVSZFdaak9YQXpSM1ZHVGt4Q1NXOUdaREF5TXpSRFZGSkxlRVpHV0dsQ2FrWkdORFJOYVUxVU5tUmFSSFUzZDFadU9USktNakpJVW1GTlZFaHZRMms1WVdOVVZXcG5ibVE0Y1RoWGJ6ZG5kRUl6UzJSblQxWkZXa3RaVVdJdlVrdEVXV1VyWTNKM1dFYzJUVGRRUzNKSVVFMVpkVUprYkROcFltaG9URmxHU1VkelRVd3dORm92T0ZoSkt6TkhWM28wWlhCTmJ6WktlR2N2TkdSQ00weDVMM000VUdocFdraHdZemhJYlVKTmQwWklTVXB1Vmt4SFZXRkdjRkE1YkROTlJVVnNaalJyYUU5NmRUTk9UbEZRU0dodWFrUlJPRlpSVW5vNU5IVkVWR0k0ZGxGM1dVOUhkRGRPTUcxa1ZXUmFjbW95ZVcxRlRHcHdZVVpJYUVGcU5XOVdhbVpUVDNkaUwwOW9aRzFDVFZRd1EybHpSemR4UTFGV1pTdHpRbFZVYURsWmQxTXpVMmh2Y0VSQ1J6Vm9XbWNyZUcxVU1TOU1VWGR6Y2poR01XTk5RV280ZFZkRlpqZElXVkJGV0V4SWQxRklhM1J1ZUZSSVFqUnFSVTR4WVRKbFNIWnVXblI2WXl0R1JVaHZlSEppZVZoQ1NFeFRlWEJsVjBNcldIVkRWWFJ5YVRSUGJVdzBUVTV4T1V0bWFFMDJTSHBDYm5abU1ERjJUMDlTUm1Jd1p5OW9ZMGs1VEVoME0wWnpLemt6UjJvMVFYTm1SMVZyUlVWVFJWcHNiMFp4Ynpjd1VIcFNRbXcwYjJaRlFWSTVkR2xTU2xKME0xQlFUWFp4VGxobFVXZFJXV0ZGUVRsbU1FUTNUMFIxYTBOTlJXNDFlREJTTHl0aGVHdHBPV1ZzUzI5bFFYZFZhMEZGTlc5bUszQTNaREpHVWtGSVFVaFJNR1J6ZGpNNE56RlljM3B2VjJwUk5XNWhNM1ZMU2pOS2MySm1UWEV3YVhWM2NubzFkMnBKYVhwaGVra3dkM05XVkZoRU5qZGlUbTlJUXpSSGQxRlZWR1YzVTBwR1RtVkxjVVpXVjNOcUt6VkRURWgyZFRCRFJXWjVhMGhqUWs1TVEwNXBXQ3RQVWxkallWSXdjbU0wYTJOd1EwSnFiSFkwTUM4MlJHNUdRMXBWVEdkTU5WcERNV2syWlRCUVoyWm1RelJNWTBab1ZqbEdPVGRPSzJkS2RqTkZNazgwYWtWQ2VuWm1lVmxuVTBsVk1qVldibmhFWVdoWFpqbElaVmxCUmtvM2JYSkZla1JGVVZWWVRFZGpRbGg0Tm1zNWVWQkZZakZ3WlZWd1dqWnFUV0ppTTAxdmVtMUVjV1pUZG1saVFqUk1VVVoxYjBFMmMzaHJkVGRaY2xKMWRFTktTakJHWVRkb1ZETTBSRTlTUjFCNlMxaFBjR2hYVVhacVZXbE1jM2MwYUVwSE5WbFlkV2cwU3pGVlVsTXhiMVpoZEZONFlqaElWbWxMV1dSdVRHSTRTazVPY2pSQ09USmlkR1ZqWm5ad1JWSTRjbEJ1Vm10WVVESTVjbkZUVTB4aVJqRnFORVZFTm0xMGNuSkZSRVo1VFM5RE55dDRjSEZ1VmprMVUyczBNVUZSUXpaWWJqSm5XSHBaY1hOR1ZtWndSMVJzTTJnMGNIcE1hQ3NyY1hsWU5XNXBWVkJwY1ZSUGJGSXhla3gzY0hCbWVuVkpjMDVzVXpsdGRWWk1VamN6ZFZwMGNVNHlVMWd2Y25WNk0xTTFNRlZHWVVGTFpGb3JVME5yZWtVeFdYUnhSRlZTZFhKWGIxbEhNalJxVUU0emVGb3hhbEZWWmpBM1lreEdkMGs0TjJSSWRVTnRjVTB4YjJjdlJIUkdLMXBHYjFwMWVuUjRXVmRoZVdGRlQxaHVXVUpHZDFscWFVdDJVWEpNV1ZnMWEwOXBSRXBqU21GVlNrNHpXbTVQVm1wNGRsVlRhblJ6VTJ4c04wMUhTVkppVXk5WmRYcFBTa2xLV2l0dmFtUmxXREZEVlhWNmNuUnpkV1o2Y0dOWmFIZEdTUzgyVld4SlFtbDVka2w0V0V4dU9WQnBkMDl2VUhCNFFrMXNNSE0yY0VVd1FVMVdXVmhpTUU1U1NrSnhWVUpXTm5sVWJsRlllVzVoV0cxWVNYVTBLMEZ5YURsc1kyRlFUVloyVEVoalV6UlZOWFZvVTBsTVdGQm5ZMk5MWW1reVVEZzFiVFo0UWk5d1pscG9VVEZRVjBSTE9VNUpXVXRSTkVkNVRISTJZVVJGUlZkMlVFSm5VWFYwUkZkdVdEWlBkWGN3TVRKTlEwNUVRbWh1VFdKWk5sWnhRa0ZGU0dscU1ESXpia2N2ZG5kelFXUTJUMGc1VWpsS1IxcHNOVWcyWkhSSGNXOWFVRVYzT1RCMFZUUkhTSEpRUzJsM1YzUlZjemczV1N0d2QwOVJhWGRWYjJOT1JTOU1VVFp1UjNCMFExaFNUR0pzYkRCeE9VNWhOMmxLVjFCU1FtbHZRVEpIYUV4a00wcHlaVGdyWWs0NVJqVlZWMlkwTURoU1YwazBjRUYzY2xKRE1rVkNMMXA0ZEVZMVRYaEhXVVJ6VmxCRU0yWkxjRTFFTWxsUU5HRlNaRmM0WWxoTloxUkRjVzQxZDBWUUx6RkNielZvT1U1cE9TOUVSbFZ5ZEVwQlJ6STJkVGhJTjNGNlRqWjBVRkpQVGpGUWNqa3ZaVVU0ZEdaMmRtWTFVbEY1VG5Wa1NsZE1OMGRzYTJ0SWVEUk9SVE5XVjFJMVNHTnJhbWhZU0ZwaEt6bGFkVmQyUkdKVlZqTndiR3cyTTBSQk4wZFhTM2RaYjJvM1lrTkxSRkJLWTBkREx6UjNlVzVKYTBOaVZucHBha3cxU1dFeVdIWTVSRll4SzFGVWRtaDJjM3BuY2tOVlFreFpkelEyUWpGWE1IaHZVblJpVTNSc016TTFRamhTZEhwUVVrOXNlRlZRVG5GSE9YZGtkM05VUWtaak9HeE1ZbXRSTWxWYWVEVnNaakl2U1hobkswRnBhRUZSY0ZWSFUyNDVUVnBPTjFoNlQzcDVRMGhoZFRoSVpGUnVVM2haVjBzNFJ6QnpkM2xYUlRseWNFZFNiVkpYVm14a1IyeEJiSHBNY0ZKb1FsbFZjblV4TlVoQlpuTllhM042TlVwSk5UQkhaalEwYkUxaGN6aFVlRTF1U25oUlNtaHZVMFZWYm1GaWNHTTVhWE5qVldKaGRtUm1Va2RKTldKNlRsWmtaekptUlRKRlZTOHpOMXBJZFRSSFZHdFdOa3B5WVRsWlZqTlBPVVV3U1hoVlYyUkNSa3haTUc0cmRGQmlSbVp3WWpJNGQzTjNTMk4yZGtobk9FNUVRbkV5SzFWUFZYVm1TbkUyWkRCSFoybFhSVFpNYUhkdE9FUm5abWROUTFrMmRFWTRZM2M0VFZrMVVtMWxaR2RZTkZwaFZEWkJTWGR2ZEVWdGMwMVVlRlF5VUZwUFdsQnpRa0ZSWkU5clpIUnpVRTQwYnpCWlVYWlBWbEZaU1dadlRVUXJVVUZZYXpoc1lXOTJNV2REUkZBcmVtMW1WbU51Tm1OQlowdFhLMEUwSzBFeVZEbE5Uamh4Y1cxRmRGWkhOelpoS3l0Rk9WVkdiMFkyVGt4WmVqSnlaa1o2VTJnNU9ESnhjVzlCV2xkb09UWnJhV0p3VTFCa2VFRnpaRWw1YVVOV01XWktkbnBCV0VwMWFVOUxiVFpYWlZsUGFrUmpjRVJ6VDNwek1qQTVWVzQyVm1sSlNuRnFUa0pyUTNCaFVVOXNUME5EWVRBMWJtSkdNM1JWTDFWTE9HWnZXbWhtTlhGWU0xUjZla0YxY3pSM05HNU5UblJWWm1GRWJTOWhXVkYyWkdFeE5sYzVTME5PUkRoeFluVndMMEUzVkZkNmQzbFFZWG93YmpaRkwxUkRWazFSUjBaTE5WQmlOVkJFVXpGbVRVTnNRekJRU2xaaVVYUjBVM1JLWm1oeldGWm1WbFZVZURab2FITklWR1JyTW1Gb2JEUlZTRVF4ZW5ZM01YZDJNRzF6T1U1cWIxVkdWREJGVlV0Mk5WWmxRbXc0Y1dSSk1TdEZOM1Z0VVVKUFNESTFXV3RzTTJKSU4xVkZjbkJ0TW1OVVpYVnBRM2RHV2xNMU9FWnNhRWRXYlRKRmFETldiRmhsWVdkdGNDdEVTWFpvTlVGVU0xRnVOWFF2UVZnclRVbDNZM0pxVm5vMVVrTnRPR2xoTlVjclNXRm9kM0JIWlhOVWQwbzNTbXRXU1RsRWNuaG5kVVJpZUdwNlNFWjVaRXBuVkZscWJ6QlFkaXRNUms1Nk4weEdTMng1VlhOVVluWlBTbkJaZVZacU56SkhPVlpoUldkRVVEaE5PR1J0V2xWRVRHdFZNMlV2VUVadWJtZEZXamQ2V0hOT2VVb3dURmR6VlhOTmNrRnlRM1o2Wm10V2NrNUZWa2h4ZEcxWGFFNURLeXRqUjJkMVZGSk5hMUYxV1hndlRHeDNTSE55VXpCSmIxRktjRXRVYlU5TE9GVTRaekpaWm5kM1VEVlVZVVZRUVdaUk9FczVTVVZaUWxCRFNVMXBTMG8wWWtSMFExVkhiSFJ1Tnl0NFUzTjRkWE5EVGt4a1lrbFFOVGh6YmpnMVYwaHJlV3B6UkZCNmVGSkthRmxuTmpoWE9FTjVia2hxVTI5blMxcG5SMWRCYUd0WlNuWm1aVXhxY2tNdmFsVm5UMkp0Y0dKUlozZ3ZjbXRoZFV0cVNqRk5ZMFFyYzJJeFZrTnpMMmhuYWtwVWVsWnJlVTl1V2lzMmJrUk1ZMjlGYm1SM2QzTnhNMUYxWWxwS2NrZEpRbVV6ZEVoRFozSjRkSFp6YVVJeGVXTkhkaTk1UmtFeU9YZHlZVmhrVUM4ekt6WkVjWGd5U1ZZMFZqa3ZSazVKTURKYU9XVXpSbU5JZFVWNFFWZFZjbUpKWlUwelJGVm5iblZPWjNWaFF5OHZlbW8wY0RCQ09FZHNlWGhCYTNaTE1rTXpORVozWTJFMWVreDNWMngzWmtSekwxVk1jV001UW1wWmFrZDJOSHByTHpOS1ZVOVhjR3BYY0RkeGNqTjJNREptYTJVM1dIQkRjMGN6WkZoamJ6ZENZbkZzUldSNFZHZFhSRmQ0YjJZMU0weE9iM2RPVUZwd2NteE9Ua3R2Um1sWU1GWjJNbXBDZUV0U1pUWk9PVUk1Ums5eFpHaEdUSEpsV0VKR1pYcDNiVWcyU1VkWmRuRkJja041VjJrdlRFZDJTWEJxYUZwMWIwUjFaR3BGVURWSmQxbFNWQ3MyZDNsNGNubGlRa1EyTVVJeVpGSlpZVlkwYVdaaloySnBVSGhwUTBWakwxbFdaM1owYWswd0wzSnlUWEZKVm5Od1pqQjZaRk40YTBaclQwbDNPVkpQVDBaeVduVlRaRkJ3THpSTlMyRkxaVW8wTVhrd1lUSmxORXRLVTI1V2FFdFVZa0ZUVVd4bWJqWmpTWGhNS3pjMk1VSlhkMnBsVW14bFpISkVjaXRvYmtsMFFrMU1WRFVyZWtSMVZGZGpSR2MwU2pGTGJrRnNMM0pSYjBoa1QyWndSRlZTZW14R1luRTBNRmszUjNKUVEzUXlUMDVuY2tjeVJFMVRZM0EySzNsT1NHRmlMMGN4YVNzMWRqRTFUVk5XZUdKa0wycHplbEk1VFVORUx6ZFJUV3N4Y0doUU5VcDRjbVEwVVV3MVVHcDRMeXRPY0hSRVRDOHlaM0prTkhReGRUVmtVREJsVGpoUGRGQlRZbUZJV2xOcVFuWkNTMUpFTTBGWVNrNXZjRGwzYTFwUGJGazNPWGhtZEdGMkszcDBWVU5TZEZwNFNGQmlielIzYVU5ME1IZDZNemRFTlc4dlFrSkhZM0YwVjBrdll6QjVSamxRZUd0RWFtRlNiR3ByTmtWbmVuRkdTMHhGVUd0RmREQXljVzFoVkd4allqRTNlVXhyZFd0NVVYcFVhRzl0TUZCSmVHTjRiVTV2THpkTmIzbHVlREZDV25WaVRtNWFUbWhCZVdodFlpdHNaa1ZDTDFCMUx6QmhkakIxTXpKc1QxcHVURmhoWW1wNFFUQmhZV0l3TjJKS1VsaEdUV2hwUmtGd1IzSnJWQzk2WTJFeE4xWlJlVWRMTWxCWFZ6Vktia0pYZFVkcVRIWkxVMGhKTmxOSlVVSm9RV3RIVWpkQ1ZrTk5kMk5TYUdWT09YQTJhR2h2UjA1M2J6ZDRha1JsUWpaNmRtNUZSR2hMV0cxcFpVZFhkRms0YVhjd1dGcHBaVmRNV1c0d2IwZFZUek5SUlVkU1lqWjJXaTkxYmpoaFdGUkNVREZMWVZjMU9VWXdTVkZtTjJ3d1NHdHhlSGs0ZHpkdWNWRk5jVmN6WVdNM1ZGcFhRVXhGV2xaT2RVTXhZbVZZVmxCeE5VOXNSekpTSzJoV1lXdDZSMVpQTTFjNWFHZHRNV2t5YmpKQlNWUXJXWGcyUkhNelFWZ3JMeXRvWTNwWFpWSnBkamwzVkhKUk0zb3JhR2RaVmtseE5VbzVZVlpaZGpWUWQyUm9Sa3RTVkVsS2VXOUxhRTVGYjJodFEzUmFkRFJ6WTA5dldEUnlSbTE1TUhVMVVIVnJSMEp5Y21GcloyNWFkMlZuVUVwcFFsVTJjSFpJUm1SSlEwVkxVRmhJUTB0Wk9FSkdOWFpTY2l0S05sUndSRk00YkRaaldEbDZiVXAzVmxaMEwyMU5OVGRFVkdJd1VGUTFhRlp0VEVoNlpHOWFURVp1YVVOb1ExZDZORTB3VW5kYVNUZHFOMjlhYzA5blRHZEpTRXhzVjFOUmJYWmhOU3RxZEZWRlkxRnZaSFUwTUdOcU0wbDBOVUZIYUVsWVpERlJjMXBOVEUxSmQyWnJTaXROUlVGNmIyaDNkMUYzZGxCVFFuaEVObkJuTm5GdVFqa3JTRWhqYlM5dVNUY3dlREJRUkdvMVlrWnZNRFJ0Y1VGVlJHZHhVVXBMUVZNNE5EVXdSbmRHYlVsWFJUUnJZMHRpTlRCek1uZGpXVVJVSzJORk4zSnJUSFo0VUc1dE1Vd3JNVVpITDA1d09YWlhkVWxzYkdNM1kyNVdUalJSVkdsbk1GQkVZWEZTVW1OU09HZHhRbkZvWTNKWVJsbHNValZwYTJSNVMwVkxkbTVKTTJoUmNqUkRVaXQyZFV4ck0zSjNPVzFtVkVsNksySjFRMmRYVDAxdU9EaGpNVzF0UXpOUFZ5dG5XRUZ2ZEdOU01pOW1NMjVRTHpsclRESXliRTltTmprNFVIbGhla0pUTUhkU2NubE1VWE1yYW1ka04wRjJhV1J1ZG1JeFFWaHNURzVtYVU5UlFVY3pOV3g2VEVJMlJWRldhak5qY1V3MmRWWjJUVGhCUTBoT1pGa3habEpGZG5GWFRHb3dNV3BhZDNKcFZVaE5OR1JYYkZkdFpqUnZkMVJHSzFwRVFqRllPR2wxTlVGRlRub3ZLeXRTTUd0RVVWQlFaMm8wY25aR2VWTnBZVkY2VGpJdmRXc3lVMkpQVjI1cFIwaDFaVXBqUlZOVE0zWjZObWwyVVZobEt6UlpiWGcxUTBreFNEQkRXRFZWZEhKTk9TczNSWEJLUkhWaFEzSXpXVmt6VDBOeFRtRlFUREZOU21sMU1qVnZiMkoxUmtaalJFaGpibVpITm5RNU9ITk9NWGRCVEV4Q1owUmplRWRuZG5GSllrTm9jSEJsVmpnNFEyMVNSWEprYW5FMGNqVmFRV0kwV1dkdFpWQTFVM0psYW1FMFNEaDRhbWROYWxWb01tOUpUa1ZwU0M5U1FUaDFaemxQUmt3NVJUVTNlbVpSU21WNVZITnFTMFpxY1VkalQzcDFSV0pwTUdsSk5qVm9WMk5oYlZneE9HODViMFJNWlZGd1RXWjNkWFkwY3pKNkszbE1hVXM0VDJ4d2VrRjJZakpNYjNaUmFXTktNMDlzTWpkNlZXTlVaM2hzYXpKMEsydEhTVU5aYlVrMGMybFpRbmhETTJvMmVtTTVkbUkwWlROVGRIaHZOekkwYkcweGRVWkZXVFJqVTBWV1JVVlVPVms1VldkWlkyOTZWREpoZEdvNVZHczBkVEpXVFdKT2NYZFFRelZXZEV4alFWbzVabmQ1VVc1eEsxTk1hWEpoTTBvd2VXTlFWVzl3TkRjeGNrcHdOVmxSWmxwc09HTlhWbVZ4ZEVwdFRqWlZXbEI1THpsck5FSkxjMFJQU1VGMU16TjNWblpNZUZoM2IwNUVXR3Q1VkdkQkt6aE9MMmN6WXpGVVNrZE9NME42V25KUU1rVlphVFozY0hBclVrTTJibU5hTUVGdWVqaFJTRmxUVERFdk5GUlJUVmhEWTBObWNrbG5aM2xtWXpCd01IcGhjVVJ6ZDBJdmREWlRVbWxyVERkNlJIcFNLM0JGZUVsVE1sWklNamMyYm1kRkwwSTVaV2x1YUd4QlppdEVTRkJHWlZvdlVYZEJWek13UTBod2FIcHhWMWMzUVVOelpIUkZUVXRMWkVOT1EzSktjRGd4WnpCVU1qRnpNRkp4VmpRdlZtZHdVa1ZSUmtSaVFVWkVjVnBhUm5sRlYwOTFUakptU3pkaFdFdFNUamQyY3pRNGIzWkdWV0ZVUkVwelNEZGpaams1TDFWVFFXOW9VME0yYUhsa2NGVjBTV1psZEhjclZWRkZNbm9yTTJSMlRpdHZSR3hwUWxwR01UTm1OR3hPVVhneWJXeG1SREEwVGpSbFl6VXhiVFZ5TjNKVGJrbFFVazFMY0dsdFVYTTNWMDFYVlUxdmFFaFlibUZDYm1adGNtczRUWFo2U3pKNmJURnNTbFZuT0haTlMzVnRZbTlLT1ZOa0wyOXpXVXgzYzJ0QldHdFNVbUpQU2xvemRtTnViMmh2TWxkbGF6RklOVXBCUWpWc01qWjJhRlZUUTB4ak1tOVJlRXhZZFdsallWbE1RbEJuYkhKVmJ6bExTV2wwY1dkS2VsSXpTVlpwY1hscE16aHFObkF2YUdoT2FHdHJkM1ZNY25CbVlWSndNM1JpU2t4cVpuQkVNMG92Y1hZM1FtZGlTRUZ5TXpkVVZ6TnRkV1JDZGtaeVdXb3hiRzV3TW5CVVVYUXlka1pVVjBadWRFeEhVakJ4YzBkRkswVkZWVkZ5YURack0zbEZVRWtyVG5obmJUSm5ka3RqTkRsdGRVWnBUM1ZzV0hBMFFYTkhNbmRtVFc1SVNVaEpla1o0TlZkemNWcHZTV05QZW1aa2FrSlJZbUp5VTFkeFMyWkJTRVpRVTNod1EwMDVWV0ZQWlV4TE5GWjFUbU5YTkdNelpGQnFXVTUyUXpWbFZrNW9LelJwZUZKTWRGWlRTRXBqTkc5eFNGRXlSalE1VDBveEt6ZFJRa0ZtYjNGaldrYzRhMlU0VUd4U1RIazJUVkUzYTJodGJrNTFhakpzTkZwT2RVOTJiRTR3VVdOS1RXeFJhbnBvZVhnMGFqTndiMmhXTVhNM1RFaFBhbmxRVUZkNldYVnVPV1p1ZWpsM1lWZHJaRkkzUm5OSmFqWnlaRmRXUlhsek5VVlFURlV2ZDJGUVoyMUlhWGQ1YzBkNFZsVnRaVnBpYjJsdFpqVTBSR28zT1VWTmJHTjFaRTVvVUVZd1ZsRXdhRkZEZVRsWlFsb3pVazAwS3pVd1QwSTJhbTkwV2tOUVJsSjVRMWR0TTJkMU5WUTVXbTFuYm1SdWJUWlRVVUZXVldaUlR6RXdLM2x6WVhSSmJEaGlWVWQ2S3pCRGQyY3hWVmMxYmxsNVkyVkxLMWg2YVZWTGIxSlBkM1U0ZEZSQ1JtSlRlalphY21WMllVbEplVWcwU2pKbFpFeEhka0l2TVdKeVpGbGxTa0o1WW1oR1R6RXlSV3BLVERWeGQyOHJVamhCVFdwUU9GWndVRFZJV0RGTFlrZ3piM0pQYTJkaFQyOTROa05WU2pCa1UzRmlUWEZNTVUwd1QwVkxVbkpWY0RCaGNEaHdObWxRVFhvdlIyWTRkVll6YWtKcVJtWXlhSFpqVldNMFRUZHBhVWsyYzFSSU9GaDVVamRHUjBOblRuVlVZMjF3YkhKeVZqWlZiVkpyY20xcmNITjNRM1IxVG5OUU0zSmhhVTlJWmxJMmVFRnVjMjFwU1dOTlFsQjJSa3hwTDNjd1prcDJlWFpMYTFwb1RFODNkVWxVVDNSSFpsZzFSWFIwVW1zek0yaHpNamhVTUU1QlpWRkdObE4zTVhWTFpFNVFOM2RHVWtnclJVTkZWRUpxTkZGYVJHdHBVV2x3UzBSbWJsUlZRakpGVFVaS0wycEhWV0kzZUZKUFJtNVNOMVJNWjNGcWNIVlVkbWh0V1RsamF6VkVPSFoyVEZablREVnhNMmhWZVdwWGNEaHJWRGN6UjJsaFRVWkNRV1JsTkV4cVVGUTRObVpvT0VaU2NqaG1PV1pDT0VKMVNVZFJZalpZVUc4eGFVMWhiRWMzYUhWNFVuSkxaSG8zYUZsTGVDdGFURk5wWTIxVVVYRlVjSFY1YkUxdE5reE1UR3RwUTBGbGR6bFhlRWwzZEdveVJtUTFNelZDWkM5dVRISTRNMUZPUXpGTFkzY3lOalppT0hkUmRIVkxTMlpGTUdOSVlYcDBPRGhEV1VKelJHTlhNV3BMZEhKQ2VEZE1OMFJxYm5nMmRHMXRLM2c1WVVjdkwxUk9jREpYTWpkM1ZHTjVPWFpqVVRFdmFVVXZkWGs1ZDB3Mk1FTjNUbE5EYmpnMVVrbGhiR2hFVTBGRFJGUjRNM3BTYVVWT2NqTkhkeTlrY3psd1ptUjBORFF3VXpaM2JGY3JiQzkzVG1WQ1MxWnFLMEZ4YzBoeE1rUjJTRk5qWVdWVlNrMUZkVFJwWVRkS1QxWmtiRXhwYWpsMlowRkxXRzUxVlhSUWNYUjJNbFZUU0RkMFdraFJWWGRCTjJsUVlXa3JlR2hSU3pCWE5XRk9jMlp5YjJaaE5VTmtWVnB4WkhSM1ZsVlJPV3cxU1hZdmNraDBRV1JFYW1kRlNFUXlUVkZVVFc4MmMyRkJSa2w0VW5nMGFYQm1aMFU1UkdFNU1HWmliVGxETkRsMVdHbDJlRTVaYmtVMmVWSkZNa0ZGVURCTVpVSnJiQzlCY1c5eloyUldTR0YyZVVOS1JrTmhOWEV5Y25obWNrVTFTalpwYWtGWlZUTnpjbko1WkdOaWRGZDVVbXhrWlU1WFNUVmFTSEZJTWxORk9IcHFRa2RpWWt4aldsWXJXblJVZW5wYU9HNTFhVTQxUnpkWlprTjVORWdyTDBscldsaGFaRGREZFhaR1FYa3ZXa01yTUZwWFUyTnFRMmt2SzNkbGExSkRLMGhNVlhCU01IcDFTVGh0ZVZwak1tdHpPSFpFUWsxVWFuaDRaamxrZFdSdlZHaENOMGw2ZVhVcmRFbDFhRTQxTjJFeWQxZHNiMGhZVmtsWWFrRlJUV05EVDJGREwzTnRPVGxXTlhaMlVWTmljRmx4Ym5wVWNsVXJZeXR1WjBwalF6bHpRUzlyVTJaQmJYVmxhU3R0TkhkeVJXWjFiMjkzVDBKVU1XcHlWbmN4Y21KaFZqVkNaRmt3VDNwVGJHNDNObk5HUzJJcmQwbG1SRTVQV0dOb1NEUjZTR280UWxKblJXRTBMMDVuTUdZcmJGVm9XVmxSVEV4NlJGVkJRbVJ2ZVRabmFqQkZLMUpYYjFGek9UQkRNbU5aWjBKbVdGbEpWa3RTTVdJd1lWTjBkMWRSY2xWcmNYZ3ZVVTlJUm5FMVJtNDVSbXQzVms1aVYzTXdiMjlaTlN0UmJXRkVURzlLZEhsTVNXOWpVbk5DVUU5T2JtbzBTa2NyZEN0aU9XbGxTSEp3TkU5dU5YbDZSVFJPWjJwTmNXZFVha2d2T0RSVFJubFFUeTlLUWpoVlpXaGhTekF5VW1jM2VGa3lVa1Z0ZFdsaFRUWmtiV1EyV1ZCdGRYWTFTR3BwTkZCTWNXaGFRa2RKZDA1d1VtdE5hbFZ1ZGpkeGJDdHRVMFZ1YlRsTVlrUkVNWGRaYkVoNFMwVnFVVkV5WjB0eWRubElTakkwVm1Kb1RFOVpUakIwVTJOM1JVZzNXUzlXWVZod2RIcFhkbm92TXpFdlUzaFlVRmhQVlUwdmFVVkhjV0ZaVFV0UWVrZDVTamd2WTI1WU1IaFNkMWR6Ykc5TWJESnFiMnBCVDJReWJtNVlRVVpMV2taM1pqQjVURFJrVmpCYVIza3dTM3BwUVU1c01qTlFWemxUUmxkRWIwdHJRak41U1c1NFVrMUxRa1ZxWlVScU56Wk1ZMlYwVlZwQ2VtOXVjbFJTVjA5S1kwbGljekZyUkROTmFrazJhVWh4VFd0aVFWUm1PVVpQTkhKek15dDFVMU5wT1c0emNFaEplbHBSTTJnNWNqZGtWV3RMZVVrd2JHWlBVRGdyWlhKWldEUkhNV2w1TjJ0ck9FRjZiaXMzZVdSelUxRXhOV1FyWVRZMVpHdG5ka1J6YWxWa1lXbFZXVzkzVG1GWE5ua3hZemhDWkVNNFJVTjBPR3hoVUd4blVIaGtNWEpzU0ZSbUsxaG1ibTFyWVcxVk0xZzBiVWRoVURoRGRqaGFXV3NyYms5M1MzZGFObnBsT0dvNGFHTnljMFJYUlVKd0szZ3hkV1Y1UzJ3MFZuVjZMMXBoWjJOMVowTlBZVXBOWjA5M2NsQXhMM2hVTTNoVVdIbENWMGhoYzA1cVdYVlpia1p5TjBoQ2FWTkxNR1kwY21GNWQwZFJVMVZvUW0wMFEyRTBVM1ZNYkhSNGRHazBOUzk0VWtKRGNsTnRVR2xhU2twWllpdEpSRzluYnpGTlVqRmlOVlVyUWpGdk5razJZek1yVjA0eUwxcExNMVpXTDBoRldHWlZNbVpvY2xwRldGaElVR3NyV1M5UFYwWjFjaTh2UlZCRmFYVlBZMmxuYTNoUlN6QlliVWRPUzBsd1dsZERXbmxrVTI5bWQwbGhSMjVXYWxaSVJGUnhiMHRLVmxGaVkxbDBRV0kzTTNKQmFVSjBaM2xPTkhKRlJrWXdPVUprUVM5NGJVNWhTMHBDU1M5R1RtNHZTSHA1UkhWbE9IQkZZekZCVDJadVZVaEhRVUZyUjB0S09HNVdkVFJJU21OWk5pOTNjRkZxWm1aaVRYWTJlRWM0Ym5OQlQzQm5Za2gxUjB0bE5GZHlPV3c1U2pNNVF6UjNSME0zY1VkVVkwWmlaMUV2Wm1kdVZESjJXWHBqZDFKUGNWcDZXa2RNTjNjcmIxSjNTMGhpYzFaMWFDODVXSHBqVTJKc2RFZEhMMXBCZDI5cWFIbDJaM0pSWlhjeFJreEdhakkzV0Vwd2VuZzVSMGhKZW1kMVRWRnlUQzlMZUV4YU4wNTBLM2RuUVU4eGQwc3ZTSGh2U2xOS1pWWmtORGt5V0hFeFVqa3ZRM0owYmpBNVFXcEJkQzlvWVdvNWVraENNelpTVUdaSk5HaEhTSHBuVjFaM1ZEZHBRVTR3UTJ0T1dVTm9Va3czVlhjMFdFcDVWVGR4WldnMldHazJOSFZrUlZNclNIWlhMMjAzUjNCYVNTc3JXRlF3YmxaSFowOHZjMEZOVlZOS1lVUkliVFZwVXl0V0wxbEdWRlYzVlU5MlVIVjNXbXhETVdsMlFYZEVUSFZXWWlzNGNVMUpUVk5RYlhWS1oySmhZMHB0WkUxTmRIWjRaR3AyUVVwRWNXWlZNRmhZZG14eFIyWldPVlZ1YkN0VE1sTlpURGN3TnpCaGJHRTNVbmhPTDJKQ2NHeFpVbTl3YXpkTmIyRmFaWGxETVZGVVJtbDBZVlp6VTBOVWNqVmtVbHB0U0M5dWVUVmpjVWh0TmpkWmJtNXhVMUpoUjB0VUsweGtjVUZVZUZSVFJtdENiWEZGVVVWak4yWkNWMHhDZVRZMElpd2liV0ZqSWpvaU16azBOekJtWm1JNFpHUmxPVEpoTmpRMll6ZGlZbUl3TVdJd05tUmlaVFUzT0dGaU1UUmlNMkUwT1RBelltTTNOR1ZsWWpWa05tUmxabUUzWWpjMk9DSXNJblJoWnlJNklpSjk=', 1773519519);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
