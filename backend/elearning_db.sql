-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               11.5.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET NAMES utf8 */
;

/*!50503 SET NAMES utf8mb4 */
;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;

/*!40103 SET TIME_ZONE='+00:00' */
;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

-- Dumping database structure for elearning_db
CREATE DATABASE IF NOT EXISTS `elearning_db`
/*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */
;

USE `elearning_db`;

-- Dumping structure for table elearning_db.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('teacher', 'student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nis` varchar(50) DEFAULT NULL,
  `ttl` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Dumping data for table elearning_db.users: ~2 rows (approximately)
REPLACE INTO `users` (
  `id`,
  `username`,
  `password`,
  `full_name`,
  `email`,
  `role`,
  `created_at`,
  `nis`,
  `ttl`,
  `phone`,
  `gender`,
  `foto`
)
VALUES
  (
    1,
    'guru',
    '$2y$10$eOBRAls5fKzjgPWe7Q5ozuettcEF7MITSihN38ZntQhyCf9nTKS6K',
    'Ozan',
    'admin@school.com',
    'teacher',
    '2025-06-23 16:06:21',
    NULL,
    '16 September 2003',
    '081223104636',
    'L',
    'default.png'
  ),
  (
    2,
    'alif punkyyy',
    '$2y$10$Ygha8LJsdbcmmA36luRk0uNKdG6xbT1o.oBJov7RztC/pbxdqNIWO',
    'alif edit',
    'alif@mail.com',
    'student',
    '2025-06-23 16:10:28',
    '2203010387',
    'tasikmalaya',
    '08929192',
    'L',
    'default.png'
  );

-- Dumping structure for table elearning_db.materials
CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Dumping data for table elearning_db.materials: ~8 rows (approximately)
REPLACE INTO `materials` (
  `id`,
  `title`,
  `description`,
  `file_path`,
  `subject`,
  `grade_level`,
  `teacher_id`,
  `created_at`
)
VALUES
  (
    3,
    'materi baru',
    'baru',
    'uploads/materials/685f120b76b15_1751061003.pdf',
    'Bahasa Indonesia',
    'VII',
    1,
    '2025-06-27 21:50:03'
  ),
  (
    4,
    'materi 2',
    'materi 2',
    'uploads/materials/685f300eeeabe_1751068686.pdf',
    'IPA',
    'VIII',
    1,
    '2025-06-27 23:58:06'
  ),
  (
    5,
    'materi 3',
    'materi 3',
    'uploads/materials/685f303c6ac4a_1751068732.pdf',
    'Seni Budaya',
    'VIII',
    1,
    '2025-06-27 23:58:52'
  ),
  (
    6,
    'materi 4',
    'materi 4',
    'uploads/materials/685f305a34ee9_1751068762.pdf',
    'Bahasa Inggris',
    'IX',
    1,
    '2025-06-27 23:59:22'
  ),
  (
    7,
    'materi 5',
    'materi 5',
    'uploads/materials/685f306c7496c_1751068780.pdf',
    'PJOK',
    'VIII',
    1,
    '2025-06-27 23:59:40'
  ),
  (
    8,
    'materi 6',
    'materi 6',
    'uploads/materials/685f30812961f_1751068801.pdf',
    'PKn',
    'VIII',
    1,
    '2025-06-28 00:00:01'
  ),
  (
    9,
    'materi 7',
    'materi 7',
    'uploads/materials/685f30926b8e1_1751068818.pdf',
    'IPA',
    'IX',
    1,
    '2025-06-28 00:00:18'
  ),
  (
    10,
    'materi 8',
    'materi 8',
    'uploads/materials/6860db4e22375_1751178062.pdf',
    'Bahasa Indonesia',
    'VII',
    1,
    '2025-06-29 06:21:02'
  );

-- Dumping structure for table elearning_db.quizzes
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `grade_level` varchar(50) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `time_limit` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 8 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Dumping data for table elearning_db.quizzes: ~2 rows (approximately)
REPLACE INTO `quizzes` (
  `id`,
  `title`,
  `description`,
  `subject`,
  `grade_level`,
  `teacher_id`,
  `time_limit`,
  `created_at`
)
VALUES
  (
    6,
    'Latihan 3',
    'Dilarang Menyontek',
    'Matematika',
    'IX',
    1,
    30,
    '2025-06-26 11:34:00'
  ),
  (
    7,
    'coba lagi',
    'coba',
    'Matematika',
    'VII',
    1,
    30,
    '2025-06-27 21:45:20'
  );

-- Dumping structure for table elearning_db.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_answer` enum('a', 'b', 'c', 'd') NOT NULL,
  `points` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 6 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Dumping data for table elearning_db.questions: ~3 rows (approximately)
REPLACE INTO `questions` (
  `id`,
  `quiz_id`,
  `question_text`,
  `option_a`,
  `option_b`,
  `option_c`,
  `option_d`,
  `correct_answer`,
  `points`
)
VALUES
  (3, 6, '1 + 1 =', '2', '3', '4', '5', 'a', 1),
  (4, 6, '2 + 2 =', '3', '4', '5', '6', 'b', 1),
  (
    5,
    7,
    'test',
    'test 1',
    'test 2',
    'test 3',
    'test 4',
    'a',
    1
  );

-- Dumping structure for table elearning_db.quiz_attempts
CREATE TABLE IF NOT EXISTS `quiz_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 8 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- Dumping data for table elearning_db.quiz_attempts: ~3 rows (approximately)
REPLACE INTO `quiz_attempts` (
  `id`,
  `quiz_id`,
  `student_id`,
  `score`,
  `total_questions`,
  `completed_at`
)
VALUES
  (5, 6, 2, 2, 2, '2025-06-26 11:42:01'),
  (6, 6, 2, 2, 2, '2025-06-26 11:45:59'),
  (7, 7, 2, 1, 1, '2025-06-27 21:47:47');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */
;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */
;

/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */
;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */
;