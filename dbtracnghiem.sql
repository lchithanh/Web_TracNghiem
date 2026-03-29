-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 24, 2026 at 05:00 PM
-- Server version: 8.4.7
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbtracnghiem`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_answers_question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `content`, `is_correct`) VALUES
(1, 1, '20', 0),
(2, 1, '25', 1),
(3, 1, '30', 0),
(4, 1, '15', 0),
(5, 2, 'x = 3', 0),
(6, 2, 'x = -3', 1),
(7, 2, 'x = 6', 0),
(8, 2, 'x = -6', 0),
(9, 3, '(-∞, 2)', 0),
(10, 3, '[2, +∞)', 1),
(11, 3, '(2, +∞)', 0),
(12, 3, 'ℝ', 0),
(13, 4, '-3', 1),
(14, 4, '3', 0),
(15, 4, '0', 0),
(16, 4, '-1', 0),
(17, 5, '4', 0),
(18, 5, '3', 1),
(19, 5, '2', 0),
(20, 5, '5', 0),
(21, 6, 'Joule (J)', 0),
(22, 6, 'Newton (N)', 1),
(23, 6, 'Watt (W)', 0),
(24, 6, 'Pascal (Pa)', 0),
(25, 7, '2 m', 0),
(26, 7, '25 m', 0),
(27, 7, '50 m', 1),
(28, 7, '100 m', 0),
(29, 8, 'Ek = mgh', 0),
(30, 8, 'Ek = 1/2 mv²', 1),
(31, 8, 'Ek = mv²', 0),
(32, 8, 'Ek = Fd', 0),
(33, 9, '0,25 J', 0),
(34, 9, '0,5 J', 0),
(35, 9, '0,125 J', 1),
(36, 9, '1 J', 0),
(37, 10, '10 m/s', 0),
(38, 10, '20 m/s', 1),
(39, 10, '30 m/s', 0),
(40, 10, '40 m/s', 0),
(41, 11, '4', 0),
(42, 11, '6', 1),
(43, 11, '8', 0),
(44, 11, '12', 0),
(45, 12, 'Ca', 0),
(46, 12, 'Fe', 1),
(47, 12, 'Cu', 0),
(48, 12, 'Zn', 0),
(49, 13, 'Liên kết ion', 0),
(50, 13, 'Liên kết cộng hóa trị có cực', 1),
(51, 13, 'Liên kết cộng hóa trị không cực', 0),
(52, 13, 'Liên kết kim loại', 0),
(53, 14, 'pH = 1', 0),
(54, 14, 'pH = 2', 1),
(55, 14, 'pH = 7', 0),
(56, 14, 'pH = 12', 0),
(57, 15, 'CH₄ + O₂ → CO₂ + H₂O', 0),
(58, 15, 'CH₄ + 2O₂ → CO₂ + 2H₂O', 1),
(59, 15, '2CH₄ + 3O₂ → 2CO₂ + 4H₂O', 0),
(60, 15, 'CH₄ + 2O₂ → 2CO₂ + H₂O', 0),
(61, 16, 'go', 0),
(62, 16, 'goes', 1),
(63, 16, 'going', 0),
(64, 16, 'gone', 0),
(65, 17, 'She go to the market yesterday.', 0),
(66, 17, 'She went to the market yesterday.', 1),
(67, 17, 'She goes to the market yesterday.', 0),
(68, 17, 'She is going to the market yesterday.', 0),
(69, 18, 'has already left', 0),
(70, 18, 'had already left', 1),
(71, 18, 'already left', 0),
(72, 18, 'was already left', 0),
(73, 19, 'The book writes by him.', 0),
(74, 19, 'The book was written by him.', 1),
(75, 19, 'The book is written by him.', 0),
(76, 19, 'The book written by him.', 0),
(77, 20, 'If it rains, I will stay home.', 0),
(78, 20, 'If I were rich, I would travel the world.', 1),
(79, 20, 'If I had studied, I would have passed.', 0),
(80, 20, 'If I am tired, I rest.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `exam_id` bigint UNSIGNED NOT NULL,
  `score` float DEFAULT '0',
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `status` enum('doing','submitted') COLLATE utf8mb4_unicode_ci DEFAULT 'doing',
  `time_spent` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_attempts_user_id` (`user_id`),
  KEY `idx_attempts_exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`id`, `user_id`, `exam_id`, `score`, `started_at`, `submitted_at`, `status`, `time_spent`) VALUES
(1, 5, 1, 80, '2025-03-10 00:30:00', '2025-03-10 00:44:00', 'submitted', NULL),
(2, 5, 2, 60, '2025-03-10 02:00:00', '2025-03-10 02:19:00', 'submitted', NULL),
(3, 6, 1, 100, '2025-03-10 00:30:00', '2025-03-10 00:43:00', 'submitted', NULL),
(4, 6, 3, 80, '2025-03-11 01:00:00', '2025-03-11 01:18:00', 'submitted', NULL),
(5, 7, 2, 40, '2025-03-10 02:00:00', '2025-03-10 02:20:00', 'submitted', NULL),
(6, 7, 4, 60, '2025-03-11 02:00:00', '2025-03-11 02:19:00', 'submitted', NULL),
(7, 8, 3, 60, '2025-03-11 01:00:00', '2025-03-11 01:17:00', 'submitted', NULL),
(8, 8, 4, 80, '2025-03-11 02:00:00', '2025-03-11 02:18:00', 'submitted', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attempt_answers`
--

DROP TABLE IF EXISTS `attempt_answers`;
CREATE TABLE IF NOT EXISTS `attempt_answers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attempt_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `answer_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_aa_answer` (`answer_id`),
  KEY `idx_aa_attempt_id` (`attempt_id`),
  KEY `idx_aa_question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempt_answers`
--

INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `answer_id`) VALUES
(1, 1, 1, 2),
(2, 1, 2, 6),
(3, 1, 3, 10),
(4, 1, 4, 13),
(5, 1, 5, 17),
(6, 2, 6, 22),
(7, 2, 7, 27),
(8, 2, 8, 30),
(9, 2, 9, 33),
(10, 2, 10, 37),
(11, 3, 1, 2),
(12, 3, 2, 6),
(13, 3, 3, 10),
(14, 3, 4, 13),
(15, 3, 5, 18),
(16, 4, 11, 42),
(17, 4, 12, 46),
(18, 4, 13, 50),
(19, 4, 14, 53),
(20, 4, 15, 58),
(21, 5, 6, 22),
(22, 5, 7, 25),
(23, 5, 8, 30),
(24, 5, 9, 36),
(25, 5, 10, 37),
(26, 6, 16, 62),
(27, 6, 17, 66),
(28, 6, 18, 69),
(29, 6, 19, 74),
(30, 6, 20, 77),
(31, 7, 11, 42),
(32, 7, 12, 45),
(33, 7, 13, 50),
(34, 7, 14, 54),
(35, 7, 15, 57),
(36, 8, 16, 62),
(37, 8, 17, 66),
(38, 8, 18, 70),
(39, 8, 19, 74),
(40, 8, 20, 77);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

DROP TABLE IF EXISTS `class_students`;
CREATE TABLE IF NOT EXISTS `class_students` (
  `class_id` bigint DEFAULT NULL,
  `user_id` bigint DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
CREATE TABLE IF NOT EXISTS `exams` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration` int NOT NULL,
  `total_questions` int DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','published','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_exam_subject` (`subject_id`),
  KEY `fk_exam_user` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `subject_id`, `title`, `description`, `duration`, `total_questions`, `created_by`, `created_at`, `status`, `start_time`, `end_time`) VALUES
(1, 1, 'Kiểm tra Toán học - Chương 1', 'Đề kiểm tra 15 phút chương hàm số', 15, 5, 3, '2025-03-01 02:00:00', 'draft', NULL, NULL),
(2, 2, 'Kiểm tra Vật lý - Cơ học', 'Đề kiểm tra về chuyển động và lực', 20, 5, 3, '2025-03-01 02:30:00', 'draft', NULL, NULL),
(3, 3, 'Kiểm tra Hóa học - Nguyên tử', 'Bài kiểm tra cấu tạo nguyên tử và bảng HTTH', 20, 5, 4, '2025-03-02 02:00:00', 'draft', NULL, NULL),
(4, 4, 'Kiểm tra Tiếng Anh - Grammar', 'Kiểm tra thì hiện tại và quá khứ', 20, 5, 4, '2025-03-02 02:30:00', 'draft', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('easy','medium','hard') COLLATE utf8mb4_unicode_ci DEFAULT 'easy',
  PRIMARY KEY (`id`),
  KEY `idx_questions_exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `content`, `image`, `level`) VALUES
(1, 1, 'Giá trị của biểu thức 3² + 4² bằng bao nhiêu?', NULL, 'easy'),
(2, 1, 'Phương trình 2x + 6 = 0 có nghiệm là?', NULL, 'easy'),
(3, 1, 'Tập xác định của hàm số y = √(x - 2) là?', NULL, 'medium'),
(4, 1, 'Đạo hàm của hàm số f(x) = x³ - 3x² + 2 tại x = 1 là?', NULL, 'hard'),
(5, 1, 'Giá trị lớn nhất của hàm số y = -x² + 4x - 1 trên đoạn [0, 3] là?', NULL, 'hard'),
(6, 2, 'Đơn vị của lực trong hệ SI là gì?', NULL, 'easy'),
(7, 2, 'Một vật chuyển động thẳng đều với vận tốc 10 m/s. Trong 5 giây vật đi được bao nhiêu mét?', NULL, 'easy'),
(8, 2, 'Công thức tính động năng của vật là?', NULL, 'medium'),
(9, 2, 'Một lò xo có độ cứng k = 100 N/m bị nén 0,05 m. Thế năng đàn hồi của lò xo là?', NULL, 'medium'),
(10, 2, 'Vật có khối lượng 2 kg rơi tự do từ độ cao 20 m (g = 10 m/s²). Vận tốc khi chạm đất là?', NULL, 'hard'),
(11, 3, 'Nguyên tử Carbon có số proton là bao nhiêu?', NULL, 'easy'),
(12, 3, 'Ký hiệu hóa học của sắt là gì?', NULL, 'easy'),
(13, 3, 'Liên kết trong phân tử H₂O là loại liên kết gì?', NULL, 'medium'),
(14, 3, 'pH của dung dịch acid mạnh (HCl 0,01M) là bao nhiêu?', NULL, 'medium'),
(15, 3, 'Phương trình nào sau đây biểu diễn đúng phản ứng đốt cháy methane?', NULL, 'hard'),
(16, 4, 'Choose the correct form: She _____ to school every day.', NULL, 'easy'),
(17, 4, 'Which sentence uses the Past Simple correctly?', NULL, 'easy'),
(18, 4, 'Choose the correct form: By the time he arrived, she _____ already left.', NULL, 'medium'),
(19, 4, 'Identify the correct sentence with passive voice in Past Simple:', NULL, 'medium'),
(20, 4, 'Choose the correct conditional sentence (Type 2):', NULL, 'hard');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`) VALUES
(1, 'Toán học', 'Các bài tập toán cơ bản và nâng cao'),
(2, 'Vật lý', 'Bài thi vật lý cơ bản và nâng cao'),
(3, 'Hóa học', 'Bài thi hóa học hữu cơ và vô cơ'),
(4, 'Tiếng Anh', 'Bài thi ngữ pháp và từ vựng tiếng Anh');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_unicode_ci DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `student_code`, `avatar`, `email_verified_at`) VALUES
(1, 'Nguyễn Admin', 'admin@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'admin', '2025-01-01 00:00:00', '2026-03-24 02:01:21', NULL, NULL, NULL),
(2, 'Trần Admin', 'admin2@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'admin', '2025-01-01 00:00:00', '2026-03-24 02:01:21', NULL, NULL, NULL),
(3, 'Lê Văn Hùng', 'hung.gv@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'teacher', '2025-01-05 01:00:00', '2026-03-24 02:01:22', NULL, NULL, NULL),
(4, 'Phạm Thị Mai', 'mai.gv@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'teacher', '2025-01-05 01:00:00', '2026-03-24 02:01:22', NULL, NULL, NULL),
(5, 'Nguyễn Văn An', 'an.hs@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'student', '2025-02-01 00:30:00', '2026-03-24 02:01:22', NULL, NULL, NULL),
(6, 'Trần Thị Bình', 'binh.hs@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'student', '2025-02-01 00:30:00', '2026-03-24 02:01:22', NULL, NULL, NULL),
(7, 'Lê Hoàng Cường', 'cuong.hs@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'student', '2025-02-01 00:30:00', '2026-03-24 02:01:23', NULL, NULL, NULL),
(8, 'Võ Minh Dũng', 'dung.hs@school.edu.vn', '$2y$12$5v1/L1eSdHpWAQt5WUsufe203SNYfIO5RE/TwCV9YpT9J5Yb4z9di', 'student', '2025-02-01 00:30:00', '2026-03-24 02:01:23', NULL, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answer_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempt_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempt_answers`
--
ALTER TABLE `attempt_answers`
  ADD CONSTRAINT `fk_aa_answer` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aa_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aa_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `fk_exam_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exam_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_question_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
