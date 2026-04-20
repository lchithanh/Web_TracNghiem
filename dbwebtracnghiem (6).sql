-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 07, 2026 at 02:33 AM
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
-- Database: `dbwebtracnghiem`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` bigint UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_answers_question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `content`, `is_correct`) VALUES
(1, 1, 'Đáp án A', 0),
(2, 1, 'Đáp án B', 1),
(3, 1, 'Đáp án C', 0),
(4, 1, 'Đáp án D', 0),
(5, 2, 'Đáp án A', 0),
(6, 2, 'Đáp án B', 0),
(7, 2, 'Đáp án C', 1),
(8, 2, 'Đáp án D', 0),
(9, 3, 'Đáp án A', 1),
(10, 3, 'Đáp án B', 0),
(11, 3, 'Đáp án C', 0),
(12, 3, 'Đáp án D', 0),
(13, 4, 'Đáp án A', 0),
(14, 4, 'Đáp án B', 1),
(15, 4, 'Đáp án C', 0),
(16, 4, 'Đáp án D', 0),
(17, 5, 'Đáp án A', 0),
(18, 5, 'Đáp án B', 0),
(19, 5, 'Đáp án C', 1),
(20, 5, 'Đáp án D', 0),
(21, 6, 'Đáp án A', 1),
(22, 6, 'Đáp án B', 0),
(23, 6, 'Đáp án C', 0),
(24, 6, 'Đáp án D', 0),
(25, 7, 'Đáp án A', 0),
(26, 7, 'Đáp án B (in đậm)', 1),
(27, 7, 'Đáp án C', 0),
(28, 7, 'Đáp án D', 0),
(29, 8, 'Đáp án A', 0),
(30, 8, 'Đáp án B', 1),
(31, 8, 'Đáp án C', 0),
(32, 8, 'Đáp án D', 0),
(33, 9, 'Đáp án A', 0),
(34, 9, 'Đáp án B (dấu sao)', 1),
(35, 9, 'Đáp án C (dấu tick)', 1),
(36, 9, '(Đúng) Đáp án D', 0),
(37, 10, 'Đáp án A', 0),
(38, 10, 'Đáp án B đúng (vừa đậm vừa màu)', 1),
(39, 10, 'Đáp án C', 0),
(40, 10, 'Đáp án D', 0);

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
  `status` enum('doing','submitted') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'doing',
  `time_spent` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_attempts_user_id` (`user_id`),
  KEY `idx_attempts_exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`id`, `user_id`, `exam_id`, `score`, `started_at`, `submitted_at`, `status`, `time_spent`, `created_at`, `updated_at`) VALUES
(65, 7, 1, 3.33333, '2026-04-06 11:09:07', '2026-04-06 11:09:13', 'submitted', 10, '2026-04-06 11:09:07', '2026-04-06 11:09:13'),
(66, 6, 3, 10, '2026-04-06 11:42:33', '2026-04-06 11:42:40', 'submitted', 14, '2026-04-06 11:42:33', '2026-04-06 11:42:40'),
(67, 9, 1, 0, '2026-04-06 11:44:51', '2026-04-06 11:44:56', 'submitted', 10, '2026-04-06 11:44:51', '2026-04-06 11:44:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attempt_answers`
--

INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `answer_id`, `created_at`, `updated_at`) VALUES
(1, 65, 1, 2, '2026-04-06 18:09:09', '2026-04-06 18:09:09'),
(2, 65, 2, 6, '2026-04-06 18:09:09', '2026-04-06 18:09:09'),
(3, 65, 3, 10, '2026-04-06 18:09:11', '2026-04-06 18:09:11'),
(4, 66, 7, 26, '2026-04-06 18:42:34', '2026-04-06 18:42:34'),
(5, 66, 8, 30, '2026-04-06 18:42:36', '2026-04-06 18:42:36'),
(6, 66, 9, 35, '2026-04-06 18:42:37', '2026-04-06 18:42:37'),
(7, 66, 10, 38, '2026-04-06 18:42:39', '2026-04-06 18:42:39'),
(8, 67, 1, 1, '2026-04-06 18:44:52', '2026-04-06 18:44:52'),
(9, 67, 2, 6, '2026-04-06 18:44:52', '2026-04-06 18:44:52'),
(10, 67, 3, 12, '2026-04-06 18:44:55', '2026-04-06 18:44:55');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invite_code` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invite_expires_at` timestamp NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `teacher_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invite_code` (`invite_code`),
  UNIQUE KEY `idx_invite_code` (`invite_code`),
  KEY `fk_classes_teacher` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `invite_code`, `invite_expires_at`, `description`, `teacher_id`, `created_at`, `updated_at`) VALUES
(1, '10A1', 'V6PEPCY0', '2026-05-06 11:31:12', NULL, 2, '2026-04-06 17:50:37', '2026-04-06 11:31:12'),
(2, '10A2', 'H2PY7N19', '2026-05-06 11:31:07', 'abc', 3, '2026-04-06 17:50:37', '2026-04-06 11:31:07'),
(3, '10A3', '3KYSC383', '2026-05-06 11:31:15', NULL, 4, '2026-04-06 17:50:37', '2026-04-06 11:31:15'),
(4, '10A4', '4XNUI3UV', '2026-05-06 11:34:13', NULL, 5, '2026-04-06 17:50:37', '2026-04-06 11:34:13');

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_students`
--

INSERT INTO `class_students` (`id`, `class_id`, `user_id`) VALUES
(1, 1, 6),
(2, 1, 7),
(3, 1, 8),
(4, 4, 6),
(5, 1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
CREATE TABLE IF NOT EXISTS `exams` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration` int NOT NULL,
  `max_attempts` int DEFAULT '1',
  `total_questions` int DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','published','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `shuffle_questions` tinyint(1) DEFAULT '0',
  `show_result` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_exam_subject` (`subject_id`),
  KEY `fk_exam_user` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `subject_id`, `title`, `description`, `duration`, `max_attempts`, `total_questions`, `created_by`, `created_at`, `status`, `start_time`, `end_time`, `updated_at`, `shuffle_questions`, `show_result`) VALUES
(1, 1, 'Toán 10A1 - Kì 1', NULL, 30, 1, 3, 2, NULL, 'published', NULL, NULL, '2026-04-06 18:09:46', 0, 1),
(2, 2, 'Vật Lý 10A2 - Kì 1', NULL, 30, 1, 3, 3, NULL, 'published', NULL, NULL, '2026-04-06 17:50:37', 0, 1),
(3, 4, 'bài thi công nghệ thông tin', NULL, 30, 1, 4, 5, NULL, 'published', NULL, NULL, '2026-04-06 18:42:22', 0, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_class`
--

INSERT INTO `exam_class` (`id`, `exam_id`, `class_id`, `created_at`) VALUES
(1, 1, 1, '2026-04-06 17:50:37'),
(2, 2, 2, '2026-04-06 17:50:37'),
(3, 3, 4, '2026-04-06 18:12:49');

-- --------------------------------------------------------

--
-- Table structure for table `exam_teacher`
--

DROP TABLE IF EXISTS `exam_teacher`;
CREATE TABLE IF NOT EXISTS `exam_teacher` (
  `exam_id` bigint UNSIGNED NOT NULL,
  `teacher_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`exam_id`,`teacher_id`),
  KEY `fk_exam_teacher_teacher` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exam_teacher`
--

INSERT INTO `exam_teacher` (`exam_id`, `teacher_id`) VALUES
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(136, 'App\\Models\\User', 2, 'auth_token', '1e26400fbc92913aef51fe5b2f5b0779f0e625a40c6148af12f62620587b9469', '[\"*\"]', '2026-03-29 05:25:41', NULL, '2026-03-29 05:05:40', '2026-03-29 05:25:41'),
(145, 'App\\Models\\User', 29, 'auth_token', '0906b477a0d03ab2ae3ff029b6953c8fff6a59235687329ad4ad668495731ddf', '[\"*\"]', '2026-03-29 08:54:09', NULL, '2026-03-29 08:48:32', '2026-03-29 08:54:09'),
(146, 'App\\Models\\User', 1, 'auth_token', '42f0d7925084a994dc5ed72b962f9d98743e83e3f09486ee68a685b96644b95f', '[\"*\"]', '2026-03-30 09:31:36', NULL, '2026-03-30 09:23:13', '2026-03-30 09:31:36'),
(147, 'App\\Models\\User', 1, 'auth_token', '3b35508a41869a0513cf00515d35ff1231bf87fe3134eab4274bb02dd72fd939', '[\"*\"]', '2026-03-30 09:51:21', NULL, '2026-03-30 09:48:55', '2026-03-30 09:51:21'),
(148, 'App\\Models\\User', 6, 'auth_token', '6a0c4202ad24a777c82a84600a203d701bf061ed422f14f9a7f8a99c9af95316', '[\"*\"]', '2026-03-30 10:11:42', NULL, '2026-03-30 10:09:55', '2026-03-30 10:11:42'),
(149, 'App\\Models\\User', 2, 'auth_token', '8096649859b373298bcb111c40e5b6100df633e5dfe0c01f96b777b707d2530b', '[\"*\"]', '2026-03-30 10:14:25', NULL, '2026-03-30 10:14:22', '2026-03-30 10:14:25'),
(150, 'App\\Models\\User', 2, 'auth_token', '4b4cd32090689f34620102fff1b09a7f292c77f6ca77c32f9475665a0fddff37', '[\"*\"]', '2026-03-30 11:04:34', NULL, '2026-03-30 11:00:31', '2026-03-30 11:04:34'),
(151, 'App\\Models\\User', 1, 'auth_token', '1206b6d181a9314057002bf460c3720bc9a208c7fd8095011f5f1dbb4934fe11', '[\"*\"]', '2026-03-30 11:06:00', NULL, '2026-03-30 11:05:48', '2026-03-30 11:06:00'),
(152, 'App\\Models\\User', 1, 'auth_token', '7f21722dea803071005eb1d36c262b650774edd7fe81f5fb99ad4433e38695a1', '[\"*\"]', '2026-03-30 11:07:03', NULL, '2026-03-30 11:06:50', '2026-03-30 11:07:03'),
(153, 'App\\Models\\User', 1, 'auth_token', '6af6eff0ef78ec7f27a42d95b1d030774df0d54b34a6c129f42d94732b70af2e', '[\"*\"]', '2026-03-30 11:07:46', NULL, '2026-03-30 11:07:34', '2026-03-30 11:07:46'),
(154, 'App\\Models\\User', 1, 'auth_token', '21d0d55cd5da7961f2d4a24b6890c4d83e127ac5901c59eaac1ee49e60e18de6', '[\"*\"]', '2026-03-30 11:10:08', NULL, '2026-03-30 11:09:57', '2026-03-30 11:10:08'),
(155, 'App\\Models\\User', 1, 'auth_token', '08b5004e26eaf9c70fb7434cf84c9ca95c188ac635b970a3fdfc8e422918bd4a', '[\"*\"]', '2026-03-30 11:49:20', NULL, '2026-03-30 11:49:19', '2026-03-30 11:49:20'),
(156, 'App\\Models\\User', 1, 'auth_token', 'fb00a866cc36f465b1a70c365714836235eed57c6ece3fbd4da6f13ae99dc75b', '[\"*\"]', '2026-03-30 11:56:17', NULL, '2026-03-30 11:54:31', '2026-03-30 11:56:17'),
(157, 'App\\Models\\User', 1, 'auth_token', 'ad3968b32ab1e3c0e1ea9b61380da1249303664dac9f08f61030533371434036', '[\"*\"]', '2026-03-30 12:00:55', NULL, '2026-03-30 12:00:54', '2026-03-30 12:00:55'),
(158, 'App\\Models\\User', 1, 'auth_token', '9f2fb2f3def478fb7e5991fcf96a5e5cc95211db1b2c6fc3aff088cf08068651', '[\"*\"]', '2026-03-30 12:04:54', NULL, '2026-03-30 12:04:54', '2026-03-30 12:04:54'),
(159, 'App\\Models\\User', 1, 'auth_token', '22b8ec19e6441487ad1242af34ec7250a47e461e6f1ca18e4e4f9629c248e84f', '[\"*\"]', '2026-03-30 12:09:02', NULL, '2026-03-30 12:09:02', '2026-03-30 12:09:02'),
(160, 'App\\Models\\User', 1, 'auth_token', '17e6282571165d2c271eab233e1069ca8ffcb1eebfa1f0af35a266df7b493df7', '[\"*\"]', '2026-03-30 12:11:48', NULL, '2026-03-30 12:10:32', '2026-03-30 12:11:48'),
(161, 'App\\Models\\User', 1, 'auth_token', '0fc22208d912472c6b31fc92cdc471efea537e2b2bf165d2e3f9efe6e9bb60d0', '[\"*\"]', '2026-03-30 12:14:50', NULL, '2026-03-30 12:13:22', '2026-03-30 12:14:50'),
(162, 'App\\Models\\User', 1, 'auth_token', '86f219338d8a4988dee43bf8e4ff40c1aa25e6a2c216c5977cded3fc2859fcc8', '[\"*\"]', '2026-03-30 12:18:12', NULL, '2026-03-30 12:16:21', '2026-03-30 12:18:12'),
(165, 'App\\Models\\User', 30, 'auth_token', '4df37d75593bb61a50109a4395b91f8059a59ffe730df31662e840205d5e38dd', '[\"*\"]', NULL, NULL, '2026-03-30 23:11:34', '2026-03-30 23:11:34'),
(174, 'App\\Models\\User', 2, 'auth_token', 'aa4e0279397cb6f0285db9196a064b18ccc5c2558f1313d86f925ae542ff1c28', '[\"*\"]', '2026-04-04 06:35:24', NULL, '2026-03-31 01:52:23', '2026-04-04 06:35:24'),
(178, 'App\\Models\\User', 1, 'auth_token', 'e74abb2fc340f5d07e1e6253061d426028bb33ea0bebaeffe3530727b07ef7cb', '[\"*\"]', '2026-04-04 07:18:36', NULL, '2026-04-04 07:14:41', '2026-04-04 07:18:36'),
(211, 'App\\Models\\User', 9, 'auth_token', '1b1635c9caf32583767a5667c4adf545475f094a3385196a2464ce59a2512f4d', '[\"*\"]', NULL, NULL, '2026-04-06 11:43:49', '2026-04-06 11:43:49'),
(213, 'App\\Models\\User', 2, 'auth_token', 'e8559f5c66c57662539e5b4f5dea4912096aba4d5b4c25b58d114d1cdd61dfe4', '[\"*\"]', '2026-04-06 11:53:09', NULL, '2026-04-06 11:45:24', '2026-04-06 11:53:09'),
(214, 'App\\Models\\User', 10, 'auth_token', 'c9457cfbd780cdde0d48edf9fb556b230c287099e33ab5972722f95c5e2585fe', '[\"*\"]', NULL, NULL, '2026-04-06 12:02:27', '2026-04-06 12:02:27'),
(219, 'App\\Models\\User', 9, 'auth_token', '3669d8467af83d066de82dc6521bc3ad0330c9a64285f3f21a1cd3b514cf38ef', '[\"*\"]', '2026-04-06 12:09:53', NULL, '2026-04-06 12:09:52', '2026-04-06 12:09:53');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` bigint UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('easy','medium','hard') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'easy',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_questions_exam_id` (`exam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `content`, `image`, `level`, `created_at`, `updated_at`) VALUES
(1, 1, 'Câu hỏi Toán 1', NULL, 'easy', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(2, 1, 'Câu hỏi Toán 2', NULL, 'medium', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(3, 1, 'Câu hỏi Toán 3', NULL, 'hard', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(4, 2, 'Câu hỏi Lý 1', NULL, 'easy', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(5, 2, 'Câu hỏi Lý 2', NULL, 'medium', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(6, 2, 'Câu hỏi Lý 3', NULL, 'hard', '2026-04-06 17:50:37', '2026-04-06 17:50:37'),
(7, 3, 'Câu hỏi có đáp án in đậm', NULL, 'easy', '2026-04-06 18:12:31', '2026-04-06 18:12:31'),
(8, 3, 'Câu hỏi có đáp án màu xanh', NULL, 'easy', '2026-04-06 18:12:31', '2026-04-06 18:12:31'),
(9, 3, 'Câu hỏi hỗn hợp', NULL, 'easy', '2026-04-06 18:12:31', '2026-04-06 18:12:31'),
(10, 3, 'Câu hỏi có đáp án đúng là in đậm và màu', NULL, 'easy', '2026-04-06 18:12:31', '2026-04-06 18:12:31');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_created` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subject_name_teacher` (`name`,`created_by`),
  KEY `subjects_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `created_by`, `created_at`, `updated_at`, `admin_created`) VALUES
(1, 'Toán', 'Môn Toán 10', 1, NULL, NULL, 0),
(2, 'Vật Lý', 'Môn Vật Lý 10', 1, NULL, NULL, 0),
(3, 'Hóa Học', 'Môn Hóa 10', 1, NULL, NULL, 0),
(4, 'CNTT', 'Môn CNTT 10', 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

DROP TABLE IF EXISTS `teacher_subject`;
CREATE TABLE IF NOT EXISTS `teacher_subject` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint UNSIGNED NOT NULL,
  `subject_id` bigint UNSIGNED NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_teacher_subject` (`teacher_id`,`subject_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_subject`
--

INSERT INTO `teacher_subject` (`id`, `teacher_id`, `subject_id`, `assigned_at`) VALUES
(1, 2, 1, '2026-04-06 17:50:37'),
(2, 3, 2, '2026-04-06 17:50:37'),
(3, 4, 3, '2026-04-06 17:50:37'),
(4, 5, 4, '2026-04-06 17:50:37'),
(5, 5, 1, '2026-04-06 18:11:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','teacher','student') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `student_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `student_code`, `avatar`, `email_verified_at`) VALUES
(1, 'Admin A', 'admin@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'admin', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(2, 'Teacher Toán', 'teachertoan@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'teacher', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(3, 'Teacher Lý', 'teacherly@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'teacher', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(4, 'Teacher Hóa', 'teacherhoa@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'teacher', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(5, 'Teacher CNTT', 'teachercntt@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'teacher', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(6, 'Student 1', 'student1@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'student', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(7, 'Student 2', 'student2@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'student', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(8, 'Student 3', 'student3@quizpro.edu.vn', '$2y$12$SOzaDGUISJYbILqJ2QXfv.ke9POSy4F1DT6o8WatdemxAEczW6dPW', 'student', NULL, '2026-04-06 11:02:52', NULL, NULL, NULL),
(9, 'Lư Chí Thanh', 'DH52201447@student.stu.edu.vn', '$2y$12$/STRTWidb1LXahj0/JxYU.N8.FYznROcg8gsw/IRTDBOEbSQ0t.Me', 'student', '2026-04-06 11:43:49', '2026-04-06 11:43:49', '12345678', NULL, NULL),
(10, 'DUY', 'DH52200554@student.stu.edu.vn', '$2y$12$o1Y2ciZufdVX96IHCbus6uZg0V2p.A/k4./8/qZ47MILUW8ap/ZM6', 'student', '2026-04-06 12:02:27', '2026-04-06 12:02:27', 'DH52200554', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answers_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_aa_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `exam_teacher`
--
ALTER TABLE `exam_teacher`
  ADD CONSTRAINT `fk_exam_teacher_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exam_teacher_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD CONSTRAINT `fk_teacher_subject_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teacher_subject_user` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
