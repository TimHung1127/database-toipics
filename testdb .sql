SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `credits` int(11) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `time_slot` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `grade` int(11) NOT NULL,
  `selected_count` int(11) NOT NULL DEFAULT 0,
  `capacity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `courses` (`course_id`, `course_name`, `credits`, `day_of_week`, `time_slot`, `required`, `grade`, `selected_count`, `capacity`) VALUES
(1, '计算机科学', 30, 3, 2, 1, 1, 0, 1),
(2, '数学', 4, 5, 4, 0, 2, 1, 1),
(3, '英语', 2, 3, 2, 0, 1, 0, 1),
(4, '物理学', 3, 4, 3, 0, 2, 0, 1),
(5, '化学', 3, 2, 6, 0, 1, 0, 1),
(6, '历史', 3, 5, 2, 1, 2, 0, 1),
(7, '地理', 3, 1, 3, 0, 3, 0, 1),
(8, '生物学', 4, 3, 5, 0, 3, 0, 1),
(9, '经济学', 3, 2, 1, 1, 2, 0, 1),
(10, '计量学', 3, 4, 4, 0, 1, 0, 1),
(11, '政治学', 3, 5, 3, 1, 2, 0, 1),
(12, '哲学', 2, 1, 2, 0, 1, 0, 1),
(13, '心理学', 3, 3, 3, 1, 2, 0, 1),
(14, '语言学', 3, 2, 2, 0, 1, 1, 1),
(15, '文学', 3, 4, 5, 1, 3, 0, 1),
(16, '艺术史', 3, 5, 4, 0, 2, 0, 1),
(17, '音乐', 2, 1, 1, 0, 4, 0, 1),
(18, '舞蹈', 2, 3, 6, 0, 1, 0, 1),
(19, '体育', 1, 2, 4, 0, 2, 0, 1),
(20, '美术', 2, 4, 2, 0, 1, 0, 1);

CREATE TABLE `course_department` (
  `course_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `course_department` (`course_id`, `department_id`) VALUES
(1, 1),
(2, 2),
(3, 1),
(4, 3),
(5, 1),
(6, 2),
(7, 3),
(8, 1),
(9, 2),
(10, 3),
(11, 1),
(12, 2),
(13, 3),
(14, 1),
(15, 2),
(16, 3),
(17, 1),
(18, 2),
(19, 3),
(20, 1);

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, '系所A'),
(2, '系所B'),
(3, '系所C');

CREATE TABLE `follow_list` (
  `student_id` varchar(50) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `follow_list` (`student_id`, `course_id`) VALUES
('123', 14),
('930718', 7),
('A1234567', 2),
('D1150416', 3),
('D1150416', 5);

CREATE TABLE `students` (
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `grade` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `students` (`student_id`, `student_name`, `department_name`, `grade`) VALUES
('123', '123', '系所A', 1),
('930718', '林嫌勳', '系所C', 3),
('A1234567', 'Tim', '系所B', 2),
('D1150416', '洪莛竣', '系所A', 1);

CREATE TABLE `student_courses` (
  `student_id` varchar(50) NOT NULL,
  `course_id` int(11) NOT NULL,
  `credits` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `student_courses` (`student_id`, `course_id`, `credits`) VALUES
('123', 1, 0),
('A1234567', 6, 0),
('A1234567', 9, 0),
('D1150416', 1, 0),
('D1150416', 14, 3);


ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

ALTER TABLE `course_department`
  ADD PRIMARY KEY (`course_id`,`department_id`),
  ADD KEY `department_id` (`department_id`);

ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

ALTER TABLE `follow_list`
  ADD PRIMARY KEY (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `department_name` (`department_name`);

ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);


ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `course_department`
  ADD CONSTRAINT `course_department_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_department_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE;

ALTER TABLE `follow_list`
  ADD CONSTRAINT `follow_list_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follow_list_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`department_name`) REFERENCES `departments` (`department_name`) ON DELETE CASCADE;

ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
