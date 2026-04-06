-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 04, 2026 at 02:49 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `content`, `is_correct`) VALUES
(25, 7, '2 m', 0),
(26, 7, '25 m', 0),
(27, 7, '50 m', 1),
(28, 7, '100 m', 0),
(29, 8, 'Ek = mgh', 0),
(30, 8, 'Ek = 1/2 mvÂē', 1),
(31, 8, 'Ek = mvÂē', 0),
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
(49, 13, 'LiÃŠn kášŋt ion', 0),
(50, 13, 'LiÃŠn kášŋt cáŧng hÃģa tráŧ cÃģ cáŧąc', 1),
(51, 13, 'LiÃŠn kášŋt cáŧng hÃģa tráŧ khÃ´ng cáŧąc', 0),
(52, 13, 'LiÃŠn kášŋt kim loášĄi', 0),
(53, 14, 'pH = 1', 0),
(54, 14, 'pH = 2', 1),
(55, 14, 'pH = 7', 0),
(56, 14, 'pH = 12', 0),
(57, 15, 'CHâ + Oâ â COâ + HâO', 0),
(58, 15, 'CHâ + 2Oâ â COâ + 2HâO', 1),
(59, 15, '2CHâ + 3Oâ â 2COâ + 4HâO', 0),
(60, 15, 'CHâ + 2Oâ â 2COâ + HâO', 0),
(193, 29, 'ÄÃĄp ÃĄn A', 0),
(194, 29, 'ÄÃĄp ÃĄn B (in Äáš­m)', 1),
(195, 29, 'ÄÃĄp ÃĄn C', 0),
(196, 29, 'ÄÃĄp ÃĄn D', 0),
(197, 30, 'ÄÃĄp ÃĄn A', 0),
(198, 30, 'ÄÃĄp ÃĄn B', 1),
(199, 30, 'ÄÃĄp ÃĄn C', 0),
(200, 30, 'ÄÃĄp ÃĄn D', 0),
(201, 31, 'ÄÃĄp ÃĄn A', 0),
(202, 31, 'ÄÃĄp ÃĄn B (dášĨu sao)', 1),
(203, 31, 'ÄÃĄp ÃĄn C (dášĨu tick)', 1),
(204, 31, '(ÄÃšng) ÄÃĄp ÃĄn D', 0),
(205, 32, 'ÄÃĄp ÃĄn A', 0),
(206, 32, 'ÄÃĄp ÃĄn B ÄÃšng (váŧŦa Äáš­m váŧŦa mÃ u)', 1),
(207, 32, 'ÄÃĄp ÃĄn C', 0),
(208, 32, 'ÄÃĄp ÃĄn D', 0),
(261, 44, 'ÄÃĄp ÃĄn A', 0),
(262, 44, 'ÄÃĄp ÃĄn B ÄÃšng (váŧŦa Äáš­m váŧŦa mÃ u)', 1),
(263, 44, 'ÄÃĄp ÃĄn C', 0),
(264, 44, 'ÄÃĄp ÃĄn D', 0),
(265, 43, 'ÄÃĄp ÃĄn A', 0),
(266, 43, 'ÄÃĄp ÃĄn B (dášĨu sao)', 1),
(267, 43, 'ÄÃĄp ÃĄn C (dášĨu tick)', 0),
(268, 43, '(ÄÃšng) ÄÃĄp ÃĄn D', 0),
(273, 45, 'ÄÃĄp ÃĄn A', 0),
(274, 45, 'ÄÃĄp ÃĄn B (in Äáš­m)', 1),
(275, 45, 'ÄÃĄp ÃĄn C', 0),
(276, 45, 'ÄÃĄp ÃĄn D', 0),
(277, 46, 'ÄÃĄp ÃĄn A', 0),
(278, 46, 'ÄÃĄp ÃĄn B', 1),
(279, 46, 'ÄÃĄp ÃĄn C', 0),
(280, 46, 'ÄÃĄp ÃĄn D', 0),
(281, 47, 'ÄÃĄp ÃĄn A', 0),
(282, 47, 'ÄÃĄp ÃĄn B (dášĨu sao)', 1),
(283, 47, 'ÄÃĄp ÃĄn C (dášĨu tick)', 1),
(284, 47, '(ÄÃšng) ÄÃĄp ÃĄn D', 0),
(285, 48, 'ÄÃĄp ÃĄn A', 0),
(286, 48, 'ÄÃĄp ÃĄn B ÄÃšng (váŧŦa Äáš­m váŧŦa mÃ u)', 1),
(287, 48, 'ÄÃĄp ÃĄn C', 0),
(288, 48, 'ÄÃĄp ÃĄn D', 0),
(289, 49, 'ÄÃĄp ÃĄn A', 0),
(290, 49, 'ÄÃĄp ÃĄn B (in Äáš­m)', 1),
(291, 49, 'ÄÃĄp ÃĄn C', 0),
(292, 49, 'ÄÃĄp ÃĄn D', 0),
(293, 50, 'ÄÃĄp ÃĄn A', 0),
(294, 50, 'ÄÃĄp ÃĄn B', 1),
(295, 50, 'ÄÃĄp ÃĄn C', 0),
(296, 50, 'ÄÃĄp ÃĄn D', 0),
(297, 51, 'ÄÃĄp ÃĄn A', 0),
(298, 51, 'ÄÃĄp ÃĄn B (dášĨu sao)', 1),
(299, 51, 'ÄÃĄp ÃĄn C (dášĨu tick)', 1),
(300, 51, '(ÄÃšng) ÄÃĄp ÃĄn D', 0),
(301, 52, 'ÄÃĄp ÃĄn A', 0),
(302, 52, 'ÄÃĄp ÃĄn B ÄÃšng (váŧŦa Äáš­m váŧŦa mÃ u)', 1),
(303, 52, 'ÄÃĄp ÃĄn C', 0),
(304, 52, 'ÄÃĄp ÃĄn D', 0),
(309, 53, 'a', 1),
(310, 53, 'b', 0),
(311, 53, 'c', 0),
(312, 53, 'd', 0),
(313, 54, 'ÄÃĄp ÃĄn A', 0),
(314, 54, 'ÄÃĄp ÃĄn B (in Äáš­m)', 1),
(315, 54, 'ÄÃĄp ÃĄn C', 0),
(316, 54, 'ÄÃĄp ÃĄn D', 0),
(317, 55, 'ÄÃĄp ÃĄn A', 0),
(318, 55, 'ÄÃĄp ÃĄn B', 1),
(319, 55, 'ÄÃĄp ÃĄn C', 0),
(320, 55, 'ÄÃĄp ÃĄn D', 0),
(321, 56, 'ÄÃĄp ÃĄn A', 0),
(322, 56, 'ÄÃĄp ÃĄn B (dášĨu sao)', 1),
(323, 56, 'ÄÃĄp ÃĄn C (dášĨu tick)', 1),
(324, 56, '(ÄÃšng) ÄÃĄp ÃĄn D', 0),
(325, 57, 'ÄÃĄp ÃĄn A', 0),
(326, 57, 'ÄÃĄp ÃĄn B ÄÃšng (váŧŦa Äáš­m váŧŦa mÃ u)', 1),
(327, 57, 'ÄÃĄp ÃĄn C', 0),
(328, 57, 'ÄÃĄp ÃĄn D', 0),
(329, 58, 'ÄÃĄp ÃĄn A', 1),
(330, 58, '(ÄÃšng) ÄÃĄp ÃĄn B ÄÃšng', 0),
(331, 58, 'ÄÃĄp ÃĄn C', 0),
(332, 58, 'ÄÃĄp ÃĄn D', 0),
(333, 59, 'ÄÃĄp ÃĄn A', 0),
(334, 59, 'ÄÃĄp ÃĄn B ÄÃšng', 1),
(335, 59, 'ÄÃĄp ÃĄn C', 0),
(336, 59, 'ÄÃĄp ÃĄn D', 0),
(337, 60, 'ÄÃĄp ÃĄn A', 0),
(338, 60, 'ÄÃĄp ÃĄn B ÄÃšng', 1),
(339, 60, 'ÄÃĄp ÃĄn C', 0),
(340, 60, 'ÄÃĄp ÃĄn D', 0),
(341, 61, '(ÄÃšng) ÄÃĄp ÃĄn B ÄÃšng', 1),
(342, 61, 'ÄÃĄp ÃĄn C', 0),
(343, 61, 'ÄÃĄp ÃĄn D', 0);

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
  `correct_count` int DEFAULT '0' COMMENT 'Số câu trả lời đúng',
  `total_questions_actual` int DEFAULT '0' COMMENT 'Số câu thực tế khi làm bài',
  `started_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `status` enum('doing','submitted') COLLATE utf8mb4_unicode_ci DEFAULT 'doing',
  `time_spent` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attempts_user_id` (`user_id`),
  KEY `idx_attempts_exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`id`, `user_id`, `exam_id`, `score`, `correct_count`, `total_questions_actual`, `started_at`, `submitted_at`, `status`, `time_spent`, `created_at`, `updated_at`) VALUES
(55, 6, 2, 75, 4, 13, '2026-03-24 23:41:55', '2026-03-24 23:41:55', 'submitted', 1500, '2026-03-28 23:41:55', '2026-04-04 14:48:33'),
(56, 7, 2, 90, 4, 13, '2026-03-25 23:41:55', '2026-03-25 23:41:55', 'submitted', 1080, '2026-03-28 23:41:55', '2026-04-04 14:48:33'),
(57, 7, 3, 85, 5, 5, '2026-03-26 23:41:55', '2026-03-26 23:41:55', 'submitted', 1200, '2026-03-28 23:41:55', '2026-04-04 14:48:33'),
(58, 8, 3, 70, 5, 5, '2026-03-24 23:41:55', '2026-03-24 23:41:55', 'submitted', 1500, '2026-03-28 23:41:55', '2026-04-04 14:48:33'),
(60, 9, 20, 5, 3, 6, '2026-03-28 17:24:54', '2026-03-28 17:25:04', 'submitted', 9, '2026-03-28 17:24:54', '2026-04-04 14:48:33'),
(61, 7, 21, 7.5, 3, 4, '2026-03-28 17:29:51', '2026-03-28 17:29:57', 'submitted', 4, '2026-03-28 17:29:51', '2026-03-28 17:29:57'),
(62, 6, 20, 3.33333, 2, 6, '2026-03-29 00:39:17', '2026-03-29 00:39:23', 'submitted', 4, '2026-03-29 00:39:17', '2026-04-04 14:48:33');

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_attempt_question` (`attempt_id`,`question_id`),
  KEY `fk_aa_answer` (`answer_id`),
  KEY `idx_aa_attempt_id` (`attempt_id`),
  KEY `idx_aa_question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempt_answers`
--

INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `answer_id`, `created_at`, `updated_at`) VALUES
(77, 55, 7, 27, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(78, 55, 8, 30, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(79, 55, 9, 35, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(80, 55, 10, 38, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(82, 56, 7, 27, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(83, 56, 8, 30, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(84, 56, 9, 35, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(85, 56, 10, 38, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(86, 57, 11, 42, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(87, 57, 12, 46, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(88, 57, 13, 50, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(89, 57, 14, 54, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(90, 57, 15, 58, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(91, 58, 11, 42, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(92, 58, 12, 46, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(93, 58, 13, 50, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(94, 58, 14, 54, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(95, 58, 15, 58, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(101, 60, 43, 265, '2026-03-29 00:24:58', '2026-03-29 00:24:58'),
(102, 60, 44, 262, '2026-03-29 00:24:58', '2026-03-29 00:24:58'),
(103, 60, 45, 274, '2026-03-29 00:25:01', '2026-03-29 00:25:01'),
(104, 60, 46, 277, '2026-03-29 00:25:01', '2026-03-29 00:25:01'),
(105, 60, 47, 282, '2026-03-29 00:25:02', '2026-03-29 00:25:02'),
(106, 60, 48, 287, '2026-03-29 00:25:03', '2026-03-29 00:25:04'),
(107, 61, 49, 290, '2026-03-29 00:29:52', '2026-03-29 00:29:52'),
(108, 61, 51, 298, '2026-03-29 00:29:55', '2026-03-29 00:29:55'),
(109, 61, 52, 302, '2026-03-29 00:29:55', '2026-03-29 00:29:55'),
(110, 62, 43, 266, '2026-03-29 07:39:18', '2026-03-29 07:39:18'),
(111, 62, 44, 262, '2026-03-29 07:39:18', '2026-03-29 07:39:18'),
(112, 62, 45, 276, '2026-03-29 07:39:19', '2026-03-29 07:39:19'),
(113, 62, 48, 285, '2026-03-29 07:39:20', '2026-03-29 07:39:20'),
(114, 62, 47, 284, '2026-03-29 07:39:21', '2026-03-29 07:39:21');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invite_code` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invite_expires_at` timestamp NULL DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `teacher_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invite_code` (`invite_code`),
  UNIQUE KEY `idx_invite_code` (`invite_code`),
  KEY `fk_classes_teacher` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `invite_code`, `invite_expires_at`, `description`, `teacher_id`, `created_at`, `updated_at`) VALUES
(1, 'Toán 10A1', 'UDHLBOCJ', '2026-04-29 16:14:06', 'Lớp Toán 10', 2, '2026-03-28 23:41:55', '2026-03-30 16:14:06'),
(15, 'lớp 1A', 'ZEBKGOOO', '2026-04-27 17:16:25', NULL, 5, '2026-03-28 17:16:25', '2026-03-28 17:16:25'),
(16, 'Toán Tin học k22', 'POWXBEUS', '2026-04-27 17:27:41', NULL, 3, '2026-03-28 17:27:41', '2026-03-28 17:27:41'),
(17, 'Công nghệ thông tin', 'N8NYELD0', '2026-04-28 00:16:53', NULL, 2, '2026-03-29 00:16:53', '2026-03-29 00:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

DROP TABLE IF EXISTS `class_students`;
CREATE TABLE IF NOT EXISTS `class_students` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_class_students_class` (`class_id`),
  KEY `fk_class_students_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_students`
--

INSERT INTO `class_students` (`id`, `class_id`, `user_id`) VALUES
(101, 1, 6),
(102, 1, 7),
(103, 1, 8),
(109, 15, 6),
(110, 15, 9),
(111, 16, 7),
(112, 1, 30);

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
  `max_attempts` int DEFAULT '1',
  `total_questions` int DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','published','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_shuffled` tinyint(1) DEFAULT '0' COMMENT 'Trộn câu hỏi',
  `shuffle_answers` tinyint(1) DEFAULT '0' COMMENT 'Trộn đáp án',
  `show_result_immediately` tinyint(1) DEFAULT '1' COMMENT 'Cho xem kết quả ngay sau khi nộp',
  `show_correct_answers` tinyint(1) DEFAULT '0' COMMENT 'Hiển thị đáp án đúng',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mật khẩu đề thi',
  PRIMARY KEY (`id`),
  KEY `fk_exam_subject` (`subject_id`),
  KEY `fk_exam_user` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `subject_id`, `title`, `description`, `duration`, `max_attempts`, `total_questions`, `created_by`, `created_at`, `status`, `start_time`, `end_time`, `updated_at`, `is_shuffled`, `shuffle_answers`, `show_result_immediately`, `show_correct_answers`, `password`) VALUES
(2, 2, 'Kiểm tra Vật lý - Cơ học', 'Chuyển động', 20, 2, 13, 2, '2026-03-28 23:41:55', 'published', NULL, NULL, '2026-03-29 07:14:26', 0, 0, 1, 0, NULL),
(3, 3, 'Kiểm tra Hóa học - Nguyên tử', 'Cấu tạo nguyên tử', 30, 2, 5, 3, '2026-03-28 23:41:55', 'closed', NULL, NULL, '2026-03-29 00:15:18', 0, 0, 1, 0, NULL),
(20, 11, 'kiểm tra 5\'', NULL, 30, 1, 6, 5, NULL, 'published', NULL, NULL, '2026-03-29 00:23:38', 0, 0, 1, 0, NULL),
(21, 12, 'thi trắc nghiệm', NULL, 45, 2, 4, 3, NULL, 'published', NULL, NULL, '2026-03-29 00:27:23', 0, 0, 1, 0, NULL),
(22, 1, 'Kiểm tra 1 tiết', NULL, 30, 1, 3, 2, NULL, 'draft', NULL, NULL, '2026-03-31 01:56:14', 0, 0, 1, 0, NULL),
(23, 1, 'test', NULL, 28, 8, 1, 2, NULL, 'draft', NULL, NULL, '2026-03-31 02:02:01', 0, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_class`
--

DROP TABLE IF EXISTS `exam_class`;
CREATE TABLE IF NOT EXISTS `exam_class` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` bigint UNSIGNED NOT NULL,
  `class_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_exam_class` (`exam_id`,`class_id`),
  KEY `fk_exam_class_class` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_class`
--

INSERT INTO `exam_class` (`id`, `exam_id`, `class_id`, `created_at`) VALUES
(38, 20, 15, '2026-03-29 00:24:28'),
(39, 21, 16, '2026-03-29 00:29:33');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

DROP TABLE IF EXISTS `exam_questions`;
CREATE TABLE IF NOT EXISTS `exam_questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `order` int DEFAULT '0' COMMENT 'Thứ tự câu hỏi trong đề',
  `score_weight` float DEFAULT '1' COMMENT 'Trọng số điểm',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_exam_question` (`exam_id`,`question_id`),
  KEY `idx_eq_question` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question_id`, `order`, `score_weight`) VALUES
(1, 2, 7, 1, 1),
(2, 2, 8, 2, 1),
(3, 2, 9, 3, 1),
(4, 2, 10, 4, 1),
(5, 2, 29, 5, 1),
(6, 2, 30, 6, 1),
(7, 2, 31, 7, 1),
(8, 2, 32, 8, 1),
(9, 2, 53, 9, 1),
(10, 2, 54, 10, 1),
(11, 2, 55, 11, 1),
(12, 2, 56, 12, 1),
(13, 2, 57, 13, 1),
(14, 3, 11, 1, 1),
(15, 3, 12, 2, 1),
(16, 3, 13, 3, 1),
(17, 3, 14, 4, 1),
(18, 3, 15, 5, 1),
(19, 20, 43, 1, 1),
(20, 20, 44, 2, 1),
(21, 20, 45, 3, 1),
(22, 20, 46, 4, 1),
(23, 20, 47, 5, 1),
(24, 20, 48, 6, 1),
(25, 21, 49, 1, 1),
(26, 21, 50, 2, 1),
(27, 21, 51, 3, 1),
(28, 21, 52, 4, 1),
(29, 22, 58, 1, 1),
(30, 22, 59, 2, 1),
(31, 22, 60, 3, 1),
(32, 23, 61, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

DROP TABLE IF EXISTS `question_bank`;
CREATE TABLE IF NOT EXISTS `question_bank` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint UNSIGNED NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('easy','medium','hard') COLLATE utf8mb4_unicode_ci DEFAULT 'easy',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_question_bank_subject` (`subject_id`),
  KEY `idx_question_bank_created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `question_bank`
--

INSERT INTO `question_bank` (`id`, `subject_id`, `content`, `image`, `level`, `created_by`, `created_at`, `updated_at`) VALUES
(7, 2, 'Một vật chuyển động thẳng đều với vận tốc 10 m/s. Trong 5 giây vật đi được bao nhiêu mét?', NULL, 'easy', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(8, 2, 'Công thức tính động năng của vật là?', NULL, 'medium', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(9, 2, 'Một lò xo có độ cứng k = 100 N/m bị nén 0,05 m. Thế năng đàn hồi của lò xo là?', NULL, 'medium', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(10, 2, 'Vật có khối lượng 2 kg rơi tự do từ độ cao 20 m (g = 10 m/s²). Vận tốc khi chạm đất là?', NULL, 'hard', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(11, 3, 'Nguyên tử Carbon có số proton là bao nhiêu?', NULL, 'easy', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(12, 3, 'Ký hiệu hóa học của sắt là gì?', NULL, 'easy', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(13, 3, 'Liên kết trong phân tử H₂O là loại liên kết gì?', NULL, 'medium', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(14, 3, 'pH của dung dịch acid mạnh (HCl 0,01M) là bao nhiêu?', NULL, 'medium', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(15, 3, 'Phương trình nào sau đây biểu diễn đúng phản ứng đốt cháy methane?', NULL, 'hard', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(29, 2, 'Câu hỏi có đáp án in đậm', NULL, 'easy', 2, '2026-03-28 23:45:39', '2026-03-28 23:45:39'),
(30, 2, 'Câu hỏi có đáp án màu xanh', NULL, 'easy', 2, '2026-03-28 23:45:39', '2026-03-28 23:45:39'),
(31, 2, 'Câu hỏi hỗn hợp', NULL, 'easy', 2, '2026-03-28 23:45:39', '2026-03-28 23:45:39'),
(32, 2, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', 2, '2026-03-28 23:45:39', '2026-03-28 23:45:39'),
(43, 11, 'Câu hỏi hỗn hợp', NULL, 'easy', 5, '2026-03-29 00:17:28', '2026-03-29 00:17:28'),
(44, 11, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', 5, '2026-03-29 00:17:28', '2026-03-29 00:17:28'),
(45, 11, 'Câu hỏi có đáp án in đậm', NULL, 'easy', 5, '2026-03-29 00:23:38', '2026-03-29 00:23:38'),
(46, 11, 'Câu hỏi có đáp án màu xanh', NULL, 'easy', 5, '2026-03-29 00:23:38', '2026-03-29 00:23:38'),
(47, 11, 'Câu hỏi hỗn hợp', NULL, 'easy', 5, '2026-03-29 00:23:38', '2026-03-29 00:23:38'),
(48, 11, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', 5, '2026-03-29 00:23:38', '2026-03-29 00:23:38'),
(49, 12, 'Câu hỏi có đáp án in đậm', NULL, 'easy', 3, '2026-03-29 00:27:23', '2026-03-29 00:27:23'),
(50, 12, 'Câu hỏi có đáp án màu xanh', NULL, 'easy', 3, '2026-03-29 00:27:23', '2026-03-29 00:27:23'),
(51, 12, 'Câu hỏi hỗn hợp', NULL, 'easy', 3, '2026-03-29 00:27:23', '2026-03-29 00:27:23'),
(52, 12, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', 3, '2026-03-29 00:27:23', '2026-03-29 00:27:23'),
(53, 2, 'a', NULL, 'easy', 2, '2026-03-29 07:14:03', '2026-03-29 07:14:03'),
(54, 2, 'Câu hỏi có đáp án in đậm', NULL, 'easy', 2, '2026-03-29 07:14:26', '2026-03-29 07:14:26'),
(55, 2, 'Câu hỏi có đáp án màu xanh', NULL, 'easy', 2, '2026-03-29 07:14:26', '2026-03-29 07:14:26'),
(56, 2, 'Câu hỏi hỗn hợp', NULL, 'easy', 2, '2026-03-29 07:14:26', '2026-03-29 07:14:26'),
(57, 2, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', 2, '2026-03-29 07:14:26', '2026-03-29 07:14:26'),
(58, 1, 'Câu hỏi số 3', NULL, 'easy', 2, '2026-03-31 01:56:14', '2026-03-31 01:56:14'),
(59, 1, 'Câu hỏi có đáp án in đậm', NULL, 'easy', 2, '2026-03-31 01:56:14', '2026-03-31 01:56:14'),
(60, 1, 'Câu hỏi có nhiều dòng Dòng thứ hai của câu hỏi Dòng thứ ba', NULL, 'easy', 2, '2026-03-31 01:56:14', '2026-03-31 01:56:14'),
(61, 1, 'CA. Đáp án A', NULL, 'easy', 2, '2026-03-31 02:02:01', '2026-03-31 02:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subject_name_teacher` (`name`,`created_by`),
  KEY `subjects_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Toán học', 'Toán đại số, giải tích, hình học', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(2, 'Vật lý', 'Cơ học, nhiệt học, điện từ', 2, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(3, 'Hóa học', 'Hóa vô cơ, hóa hữu cơ', 3, '2026-03-28 23:41:55', '2026-03-28 23:41:55'),
(11, 'toán a1', NULL, 5, '2026-03-28 17:16:53', '2026-03-28 17:16:53'),
(12, 'Toán tin học', 'dghjnmkl,', 3, '2026-03-28 17:26:49', '2026-03-28 17:26:49');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

DROP TABLE IF EXISTS `teacher_subject`;
CREATE TABLE IF NOT EXISTS `teacher_subject` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teacher_subject` (`user_id`,`subject_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `student_code`, `avatar`, `email_verified_at`) VALUES
(1, 'Nguyễn Quản Trị', 'admin@quizpro.edu.vn', '$2y$12$jkZh5W/2wxCaboVp9ut1M.HIcsI5Ce.viVKw.3Uybfm0eIlWKYq8q', 'admin', '2026-03-28 23:41:55', '2026-03-30 02:18:30', NULL, NULL, NULL),
(2, 'Trần Văn Thắng', 'thang.gv@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'teacher', '2026-03-28 23:41:55', '2026-03-28 23:41:55', NULL, NULL, NULL),
(3, 'Lê Thị Hoa', 'hoa.gv@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'teacher', '2026-03-28 23:41:55', '2026-03-28 23:41:55', NULL, NULL, NULL),
(5, 'Nguyễn Thị Lan', 'lan.gv@quizpro.edu.vn', '$2y$12$pJ2GAxY81ciGVvL6GMByFOTrXsmqvyODNZGeG3QeCsDbT93M6d/z2', 'teacher', '2026-03-28 23:41:55', '2026-03-28 16:49:42', NULL, NULL, NULL),
(6, 'Nguyễn Văn An', 'an.hs@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'student', '2026-03-28 23:41:55', '2026-03-28 23:41:55', '20230001', NULL, NULL),
(7, 'Trần Thị Bình', 'binh.hs@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'student', '2026-03-28 23:41:55', '2026-03-28 23:41:55', '20230002', NULL, NULL),
(8, 'Phạm Văn Cường', 'cuong.hs@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'student', '2026-03-28 23:41:55', '2026-03-28 23:41:55', '20230003', NULL, NULL),
(9, 'Lê Thị Dung', 'dung.hs@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'student', '2026-03-28 23:41:55', '2026-03-28 23:41:55', '20230004', NULL, NULL),
(10, 'Hoàng Văn Em', 'em.hs@quizpro.edu.vn', '$2y$12$2ekNkdjqxuhICQO8LIxKJuP.h9KlPLn7GtCT/Cp0H/a30EADLfC2K', 'student', '2026-03-28 23:41:55', '2026-03-28 23:41:55', '20230005', NULL, NULL),
(27, 'Nguyễn Huyền', 'huyen@gmail.com', '$2y$12$Ri.2AXY7Nuv0vCnrOpigiOqAaWa8S2JUcu3jWoMDNbWBoKA8i.5oO', 'student', '2026-03-29 01:27:04', '2026-03-29 01:31:52', 'DH52220122', NULL, NULL),
(29, 'Trần Hiếu', 'hieu@gmail.com', '$2y$12$gjcvzBwQzspBhiDbSa008OfuKwvTGHv6hN9ikzdNZcvXF33PdMErS', 'admin', '2026-03-29 01:47:54', '2026-03-29 01:48:14', NULL, NULL, NULL),
(30, 'Trần Nhật Quangtr', 'quang@quizpro.edu.vn', '$2y$12$IDZznzYv0d3xylWy/8sf1urWY0mF8PEWjYV8TsWsi9kMMe3qPX.Gi', 'student', '2026-03-30 16:11:34', '2026-03-30 16:11:34', '12345645', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answers_qbank` FOREIGN KEY (`question_id`) REFERENCES `question_bank` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `fk_attempts_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempt_answers`
--
ALTER TABLE `attempt_answers`
  ADD CONSTRAINT `fk_aa_answer` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aa_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aa_question` FOREIGN KEY (`question_id`) REFERENCES `question_bank` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_classes_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_students`
--
ALTER TABLE `class_students`
  ADD CONSTRAINT `fk_class_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `fk_exams_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exams_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_class`
--
ALTER TABLE `exam_class`
  ADD CONSTRAINT `fk_exam_class_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exam_class_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `fk_eq_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eq_question` FOREIGN KEY (`question_id`) REFERENCES `question_bank` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_bank`
--
ALTER TABLE `question_bank`
  ADD CONSTRAINT `fk_qbank_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qbank_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD CONSTRAINT `teacher_subject_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subject_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
