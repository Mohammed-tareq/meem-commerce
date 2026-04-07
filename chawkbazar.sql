-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 17, 2026 at 01:34 PM
-- Server version: 8.4.7
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 1;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chawkbazar`
--

-- --------------------------------------------------------

--
-- Table structure for table `abusive_reports`
--

DROP TABLE IF EXISTS `abusive_reports`;
CREATE TABLE IF NOT EXISTS `abusive_reports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `abusive_reports_user_id_foreign` (`user_id`),
  KEY `abusive_reports_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `address` json NOT NULL,
  `location` json DEFAULT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `address_customer_id_foreign` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `title`, `type`, `default`, `address`, `location`, `customer_id`, `created_at`, `updated_at`) VALUES
(1, 'Billing', 'billing', 0, '{\"zip\": \"99614\", \"city\": \"Kipnuk\", \"state\": \"AK\", \"country\": \"United States\", \"street_address\": \"2231 Kidd Avenue\"}', NULL, 1, '2021-08-18 10:18:03', '2021-08-18 10:18:03'),
(2, 'Shipping', 'shipping', 0, '{\"zip\": \"40391\", \"city\": \"Winchester\", \"state\": \"KY\", \"country\": \"United States\", \"street_address\": \"2148  Straford Park\"}', NULL, 1, '2021-08-18 10:18:12', '2021-08-18 10:18:12'),
(3, 'Billing', 'billing', 0, '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 3, '2021-11-25 04:23:04', '2021-11-25 04:23:04'),
(4, 'Shipping', 'shipping', 0, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', NULL, 3, '2021-11-25 04:23:28', '2021-11-25 04:23:28');

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `url`, `created_at`, `updated_at`) VALUES
(1, '', '2021-10-09 10:30:23', '2021-10-09 10:30:23'),
(2, '', '2021-10-09 10:30:34', '2021-10-09 10:30:34'),
(3, '', '2021-10-09 10:39:00', '2021-10-09 10:39:00'),
(4, '', '2021-10-09 10:42:22', '2021-10-09 10:42:22'),
(5, '', '2021-10-09 10:47:15', '2021-10-09 10:47:15'),
(6, '', '2021-10-09 10:47:43', '2021-10-09 10:47:43'),
(7, '', '2021-10-09 10:53:45', '2021-10-09 10:53:45'),
(8, '', '2021-10-09 13:20:22', '2021-10-09 13:20:22'),
(9, '', '2021-10-09 13:20:25', '2021-10-09 13:20:25'),
(10, '', '2021-10-09 13:21:30', '2021-10-09 13:21:30'),
(11, '', '2021-10-09 13:21:40', '2021-10-09 13:21:40'),
(12, '', '2021-10-09 13:22:16', '2021-10-09 13:22:16'),
(13, '', '2021-10-09 14:01:35', '2021-10-09 14:01:35'),
(14, '', '2021-10-09 14:02:11', '2021-10-09 14:02:11'),
(15, '', '2021-10-09 14:05:23', '2021-10-09 14:05:23'),
(16, '', '2021-10-09 14:06:57', '2021-10-09 14:06:57'),
(17, '', '2021-10-09 14:17:42', '2021-10-09 14:17:42'),
(18, '', '2021-10-09 14:18:48', '2021-10-09 14:18:48'),
(19, '', '2021-10-09 14:20:47', '2021-10-09 14:20:47'),
(20, '', '2021-10-09 14:21:10', '2021-10-09 14:21:10'),
(21, '', '2021-10-09 14:21:30', '2021-10-09 14:21:30'),
(22, '', '2021-10-09 14:21:55', '2021-10-09 14:21:55'),
(23, '', '2021-10-09 14:22:15', '2021-10-09 14:22:15'),
(24, '', '2021-10-09 14:23:13', '2021-10-09 14:23:13'),
(25, '', '2021-10-09 14:23:58', '2021-10-09 14:23:58'),
(26, '', '2021-10-09 14:37:38', '2021-10-09 14:37:38'),
(27, '', '2021-10-09 14:38:22', '2021-10-09 14:38:22'),
(28, '', '2021-10-09 14:38:34', '2021-10-09 14:38:34'),
(29, '', '2021-10-09 14:38:47', '2021-10-09 14:38:47'),
(30, '', '2021-10-09 14:40:03', '2021-10-09 14:40:03'),
(31, '', '2021-10-09 14:40:14', '2021-10-09 14:40:14'),
(32, '', '2021-10-09 14:40:27', '2021-10-09 14:40:27'),
(33, '', '2021-10-09 14:40:40', '2021-10-09 14:40:40'),
(34, '', '2021-10-09 14:52:47', '2021-10-09 14:52:47'),
(35, '', '2021-10-09 14:53:33', '2021-10-09 14:53:33'),
(36, '', '2021-10-09 14:53:48', '2021-10-09 14:53:48'),
(37, '', '2021-10-09 14:54:17', '2021-10-09 14:54:17'),
(38, '', '2021-10-09 14:55:41', '2021-10-09 14:55:41'),
(39, '', '2021-10-10 10:21:22', '2021-10-10 10:21:22'),
(40, '', '2021-10-10 10:21:22', '2021-10-10 10:21:22'),
(41, '', '2021-10-10 10:30:48', '2021-10-10 10:30:48'),
(42, '', '2021-10-10 10:31:51', '2021-10-10 10:31:51'),
(43, '', '2021-10-10 10:50:07', '2021-10-10 10:50:07'),
(44, '', '2021-10-10 10:50:11', '2021-10-10 10:50:11'),
(45, '', '2021-10-10 10:50:49', '2021-10-10 10:50:49'),
(46, '', '2021-10-10 10:50:55', '2021-10-10 10:50:55'),
(47, '', '2021-10-10 10:51:22', '2021-10-10 10:51:22'),
(48, '', '2021-10-10 10:51:29', '2021-10-10 10:51:29'),
(49, '', '2021-10-10 10:55:12', '2021-10-10 10:55:12'),
(50, '', '2021-10-10 10:55:43', '2021-10-10 10:55:43'),
(51, '', '2021-10-10 10:55:56', '2021-10-10 10:55:56'),
(52, '', '2021-10-10 10:56:02', '2021-10-10 10:56:02'),
(53, '', '2021-10-10 10:56:19', '2021-10-10 10:56:19'),
(54, '', '2021-10-10 10:56:51', '2021-10-10 10:56:51'),
(55, '', '2021-10-10 10:57:50', '2021-10-10 10:57:50'),
(56, '', '2021-10-10 10:57:55', '2021-10-10 10:57:55'),
(57, '', '2021-10-10 10:58:24', '2021-10-10 10:58:24'),
(58, '', '2021-10-10 10:58:29', '2021-10-10 10:58:29'),
(59, '', '2021-10-10 10:58:54', '2021-10-10 10:58:54'),
(60, '', '2021-10-10 10:59:02', '2021-10-10 10:59:02'),
(61, '', '2021-10-10 10:59:24', '2021-10-10 10:59:24'),
(62, '', '2021-10-10 10:59:29', '2021-10-10 10:59:29'),
(63, '', '2021-10-10 11:00:05', '2021-10-10 11:00:05'),
(64, '', '2021-10-10 11:00:17', '2021-10-10 11:00:17'),
(65, '', '2021-10-10 11:01:05', '2021-10-10 11:01:05'),
(66, '', '2021-10-10 11:01:07', '2021-10-10 11:01:07'),
(67, '', '2021-10-10 11:01:32', '2021-10-10 11:01:32'),
(68, '', '2021-10-10 11:01:50', '2021-10-10 11:01:50'),
(69, '', '2021-10-10 11:01:55', '2021-10-10 11:01:55'),
(70, '', '2021-10-10 11:02:19', '2021-10-10 11:02:19'),
(71, '', '2021-10-10 11:02:52', '2021-10-10 11:02:52'),
(72, '', '2021-10-10 11:03:29', '2021-10-10 11:03:29'),
(73, '', '2021-10-10 11:03:36', '2021-10-10 11:03:36'),
(74, '', '2021-10-10 11:03:56', '2021-10-10 11:03:56'),
(75, '', '2021-10-10 11:04:00', '2021-10-10 11:04:00'),
(76, '', '2021-10-10 11:04:23', '2021-10-10 11:04:23'),
(77, '', '2021-10-10 11:04:27', '2021-10-10 11:04:27'),
(78, '', '2021-10-10 12:19:50', '2021-10-10 12:19:50'),
(79, '', '2021-10-10 15:43:39', '2021-10-10 15:43:39'),
(80, '', '2021-10-10 15:44:46', '2021-10-10 15:44:46'),
(81, '', '2021-10-10 15:44:54', '2021-10-10 15:44:54'),
(82, '', '2021-10-10 15:44:54', '2021-10-10 15:44:54'),
(83, '', '2021-10-10 15:45:30', '2021-10-10 15:45:30'),
(84, '', '2021-10-10 15:45:31', '2021-10-10 15:45:31'),
(85, '', '2021-10-10 15:49:03', '2021-10-10 15:49:03'),
(86, '', '2021-10-10 15:49:03', '2021-10-10 15:49:03'),
(87, '', '2021-10-10 15:49:24', '2021-10-10 15:49:24'),
(88, '', '2021-10-10 15:49:24', '2021-10-10 15:49:24'),
(89, '', '2021-10-10 15:50:35', '2021-10-10 15:50:35'),
(90, '', '2021-10-10 15:50:35', '2021-10-10 15:50:35'),
(91, '', '2021-10-10 15:50:35', '2021-10-10 15:50:35'),
(92, '', '2021-10-10 15:50:36', '2021-10-10 15:50:36'),
(93, '', '2021-10-10 15:50:40', '2021-10-10 15:50:40'),
(94, '', '2021-10-10 15:50:40', '2021-10-10 15:50:40'),
(95, '', '2021-10-10 15:50:55', '2021-10-10 15:50:55'),
(96, '', '2021-10-10 15:50:55', '2021-10-10 15:50:55'),
(97, '', '2021-10-10 15:52:01', '2021-10-10 15:52:01'),
(98, '', '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(99, '', '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(100, '', '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(101, '', '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(102, '', '2021-10-10 15:52:23', '2021-10-10 15:52:23'),
(103, '', '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(104, '', '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(105, '', '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(106, '', '2021-10-10 15:53:31', '2021-10-10 15:53:31'),
(107, '', '2021-10-10 15:58:44', '2021-10-10 15:58:44'),
(108, '', '2021-10-10 15:59:50', '2021-10-10 15:59:50'),
(109, '', '2021-10-10 15:59:54', '2021-10-10 15:59:54'),
(110, '', '2021-10-10 16:29:51', '2021-10-10 16:29:51'),
(111, '', '2021-10-10 16:30:04', '2021-10-10 16:30:04'),
(112, '', '2021-10-10 16:30:04', '2021-10-10 16:30:04'),
(113, '', '2021-10-10 16:30:05', '2021-10-10 16:30:05'),
(114, '', '2021-10-10 16:30:05', '2021-10-10 16:30:05'),
(115, '', '2021-10-10 16:32:20', '2021-10-10 16:32:20'),
(116, '', '2021-10-10 16:32:27', '2021-10-10 16:32:27'),
(117, '', '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(118, '', '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(119, '', '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(120, '', '2021-10-10 16:36:45', '2021-10-10 16:36:45'),
(121, '', '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(122, '', '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(123, '', '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(124, '', '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(125, '', '2021-10-10 16:42:43', '2021-10-10 16:42:43'),
(126, '', '2021-10-10 16:43:08', '2021-10-10 16:43:08'),
(127, '', '2021-10-10 16:43:08', '2021-10-10 16:43:08'),
(128, '', '2021-10-10 16:43:09', '2021-10-10 16:43:09'),
(129, '', '2021-10-10 16:43:09', '2021-10-10 16:43:09'),
(130, '', '2021-10-10 16:46:05', '2021-10-10 16:46:05'),
(131, '', '2021-10-10 16:46:15', '2021-10-10 16:46:15'),
(132, '', '2021-10-10 16:46:15', '2021-10-10 16:46:15'),
(133, '', '2021-10-10 16:48:37', '2021-10-10 16:48:37'),
(134, '', '2021-10-10 16:48:42', '2021-10-10 16:48:42'),
(135, '', '2021-10-10 16:50:52', '2021-10-10 16:50:52'),
(136, '', '2021-10-10 16:50:57', '2021-10-10 16:50:57'),
(137, '', '2021-10-10 18:48:59', '2021-10-10 18:48:59'),
(138, '', '2021-10-10 18:49:10', '2021-10-10 18:49:10'),
(139, '', '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(140, '', '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(141, '', '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(142, '', '2021-10-11 10:13:35', '2021-10-11 10:13:35'),
(143, '', '2021-10-11 10:13:41', '2021-10-11 10:13:41'),
(144, '', '2021-10-11 10:13:42', '2021-10-11 10:13:42'),
(145, '', '2021-10-11 10:14:38', '2021-10-11 10:14:38'),
(146, '', '2021-10-11 10:14:44', '2021-10-11 10:14:44'),
(147, '', '2021-10-11 10:14:45', '2021-10-11 10:14:45'),
(148, '', '2021-10-11 10:19:40', '2021-10-11 10:19:40'),
(149, '', '2021-10-11 10:19:55', '2021-10-11 10:19:55'),
(150, '', '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(151, '', '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(152, '', '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(153, '', '2021-10-11 10:54:44', '2021-10-11 10:54:44'),
(154, '', '2021-10-11 10:55:02', '2021-10-11 10:55:02'),
(155, '', '2021-10-11 10:55:03', '2021-10-11 10:55:03'),
(156, '', '2021-10-11 10:55:03', '2021-10-11 10:55:03'),
(157, '', '2021-10-11 10:55:04', '2021-10-11 10:55:04'),
(158, '', '2021-10-11 11:29:34', '2021-10-11 11:29:34'),
(159, '', '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(160, '', '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(161, '', '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(162, '', '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(163, '', '2021-10-11 11:32:08', '2021-10-11 11:32:08'),
(164, '', '2021-10-11 11:33:03', '2021-10-11 11:33:03'),
(165, '', '2021-10-11 11:36:34', '2021-10-11 11:36:34'),
(166, '', '2021-10-11 11:36:39', '2021-10-11 11:36:39'),
(167, '', '2021-10-11 11:38:13', '2021-10-11 11:38:13'),
(168, '', '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(169, '', '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(170, '', '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(171, '', '2021-10-11 11:40:03', '2021-10-11 11:40:03'),
(172, '', '2021-10-11 11:40:14', '2021-10-11 11:40:14'),
(173, '', '2021-10-11 11:41:33', '2021-10-11 11:41:33'),
(174, '', '2021-10-11 11:42:02', '2021-10-11 11:42:02'),
(175, '', '2021-10-11 11:42:03', '2021-10-11 11:42:03'),
(176, '', '2021-10-11 11:42:04', '2021-10-11 11:42:04'),
(177, '', '2021-10-11 11:42:51', '2021-10-11 11:42:51'),
(178, '', '2021-10-11 11:42:54', '2021-10-11 11:42:54'),
(179, '', '2021-10-23 10:01:43', '2021-10-23 10:01:43'),
(180, '', '2021-10-23 10:09:07', '2021-10-23 10:09:07'),
(181, '', '2021-10-23 10:45:54', '2021-10-23 10:45:54'),
(182, '', '2021-10-23 10:48:31', '2021-10-23 10:48:31'),
(183, '', '2021-10-23 10:48:49', '2021-10-23 10:48:49'),
(184, '', '2021-10-23 13:17:44', '2021-10-23 13:17:44'),
(185, '', '2021-10-23 13:17:53', '2021-10-23 13:17:53'),
(186, '', '2021-10-23 16:54:10', '2021-10-23 16:54:10'),
(187, '', '2021-10-23 16:54:14', '2021-10-23 16:54:14'),
(188, '', '2021-10-23 17:12:44', '2021-10-23 17:12:44'),
(189, '', '2021-10-23 17:16:20', '2021-10-23 17:16:20'),
(190, '', '2021-10-23 17:16:24', '2021-10-23 17:16:24'),
(191, '', '2021-10-23 17:20:26', '2021-10-23 17:20:26'),
(192, '', '2021-10-23 17:30:08', '2021-10-23 17:30:08'),
(193, '', '2021-10-23 17:30:11', '2021-10-23 17:30:11'),
(194, '', '2021-10-23 18:09:12', '2021-10-23 18:09:12'),
(195, '', '2021-10-23 18:09:16', '2021-10-23 18:09:16'),
(196, '', '2021-10-23 18:11:48', '2021-10-23 18:11:48'),
(197, '', '2021-10-23 18:11:52', '2021-10-23 18:11:52'),
(198, '', '2021-10-23 18:16:05', '2021-10-23 18:16:05'),
(199, '', '2021-10-23 18:16:12', '2021-10-23 18:16:12'),
(200, '', '2021-10-23 18:18:21', '2021-10-23 18:18:21'),
(201, '', '2021-10-23 18:18:25', '2021-10-23 18:18:25'),
(202, '', '2021-10-23 18:20:07', '2021-10-23 18:20:07'),
(203, '', '2021-10-23 18:20:11', '2021-10-23 18:20:11'),
(204, '', '2021-10-23 18:22:08', '2021-10-23 18:22:08'),
(205, '', '2021-10-23 18:23:58', '2021-10-23 18:23:58'),
(206, '', '2021-10-23 18:24:02', '2021-10-23 18:24:02'),
(207, '', '2021-10-23 18:27:10', '2021-10-23 18:27:10'),
(208, '', '2021-10-23 18:27:14', '2021-10-23 18:27:14'),
(209, '', '2021-10-23 18:32:22', '2021-10-23 18:32:22'),
(210, '', '2021-10-23 18:35:05', '2021-10-23 18:35:05'),
(211, '', '2021-10-23 18:35:11', '2021-10-23 18:35:11'),
(212, '', '2021-10-23 18:38:52', '2021-10-23 18:38:52'),
(213, '', '2021-10-23 18:41:38', '2021-10-23 18:41:38'),
(214, '', '2021-10-23 18:41:42', '2021-10-23 18:41:42'),
(215, '', '2021-10-23 18:45:53', '2021-10-23 18:45:53'),
(216, '', '2021-10-23 18:45:58', '2021-10-23 18:45:58'),
(217, '', '2021-10-23 18:50:34', '2021-10-23 18:50:34'),
(218, '', '2021-10-23 18:50:38', '2021-10-23 18:50:38'),
(219, '', '2021-10-23 18:54:47', '2021-10-23 18:54:47'),
(220, '', '2021-10-23 18:54:51', '2021-10-23 18:54:51'),
(221, '', '2021-10-23 18:57:11', '2021-10-23 18:57:11'),
(222, '', '2021-10-23 18:57:14', '2021-10-23 18:57:14'),
(223, '', '2021-10-23 19:00:46', '2021-10-23 19:00:46'),
(224, '', '2021-10-23 19:01:09', '2021-10-23 19:01:09'),
(225, '', '2021-10-23 19:04:37', '2021-10-23 19:04:37'),
(226, '', '2021-10-23 19:08:09', '2021-10-23 19:08:09'),
(227, '', '2021-10-23 19:08:14', '2021-10-23 19:08:14'),
(228, '', '2021-10-23 19:10:37', '2021-10-23 19:10:37'),
(229, '', '2021-10-23 19:10:41', '2021-10-23 19:10:41'),
(230, '', '2021-10-23 19:13:34', '2021-10-23 19:13:34'),
(231, '', '2021-10-23 19:13:38', '2021-10-23 19:13:38'),
(232, '', '2021-10-23 19:16:37', '2021-10-23 19:16:37'),
(233, '', '2021-10-23 19:16:41', '2021-10-23 19:16:41'),
(234, '', '2021-10-23 19:19:04', '2021-10-23 19:19:04'),
(235, '', '2021-10-23 19:19:08', '2021-10-23 19:19:08'),
(236, '', '2021-10-23 19:21:07', '2021-10-23 19:21:07'),
(237, '', '2021-10-23 19:21:10', '2021-10-23 19:21:10'),
(238, '', '2021-10-25 04:17:45', '2021-10-25 04:17:45'),
(239, '', '2021-10-25 04:19:30', '2021-10-25 04:19:30'),
(240, '', '2021-10-25 04:19:44', '2021-10-25 04:19:44'),
(241, '', '2021-10-25 04:20:12', '2021-10-25 04:20:12'),
(242, '', '2021-10-25 04:20:21', '2021-10-25 04:20:21'),
(243, '', '2021-10-25 04:20:46', '2021-10-25 04:20:46'),
(244, '', '2021-10-25 04:20:54', '2021-10-25 04:20:54'),
(245, '', '2021-10-25 04:21:03', '2021-10-25 04:21:03'),
(246, '', '2021-10-25 04:35:16', '2021-10-25 04:35:16'),
(247, '', '2021-10-25 04:37:12', '2021-10-25 04:37:12'),
(248, '', '2021-10-25 04:39:11', '2021-10-25 04:39:11'),
(249, '', '2021-10-25 04:39:48', '2021-10-25 04:39:48'),
(250, '', '2021-10-25 09:35:01', '2021-10-25 09:35:01'),
(251, '', '2021-10-25 09:36:20', '2021-10-25 09:36:20'),
(252, '', '2021-10-25 09:37:22', '2021-10-25 09:37:22'),
(253, '', '2021-10-25 09:37:43', '2021-10-25 09:37:43'),
(254, '', '2021-10-25 09:39:01', '2021-10-25 09:39:01'),
(255, '', '2021-10-25 09:39:38', '2021-10-25 09:39:38'),
(256, '', '2021-10-27 06:13:12', '2021-10-27 06:13:12'),
(257, '', '2021-10-27 06:13:26', '2021-10-27 06:13:26'),
(258, '', '2021-10-27 06:13:39', '2021-10-27 06:13:39'),
(259, '', '2021-10-27 06:50:39', '2021-10-27 06:50:39'),
(260, '', '2021-10-27 06:51:22', '2021-10-27 06:51:22'),
(261, '', '2021-10-27 06:51:59', '2021-10-27 06:51:59'),
(262, '', '2021-10-27 06:52:25', '2021-10-27 06:52:25'),
(263, '', '2021-10-27 06:52:45', '2021-10-27 06:52:45'),
(264, '', '2021-10-27 06:53:00', '2021-10-27 06:53:00'),
(265, '', '2021-10-27 06:53:21', '2021-10-27 06:53:21'),
(266, '', '2021-10-27 06:53:39', '2021-10-27 06:53:39'),
(267, '', '2021-10-27 06:53:52', '2021-10-27 06:53:52'),
(268, '', '2021-11-08 04:57:52', '2021-11-08 04:57:52'),
(269, '', '2021-11-08 05:06:04', '2021-11-08 05:06:04'),
(270, '', '2021-11-08 05:06:17', '2021-11-08 05:06:17'),
(271, '', '2021-11-08 05:08:47', '2021-11-08 05:08:47'),
(272, '', '2021-11-08 05:09:47', '2021-11-08 05:09:47'),
(273, '', '2021-11-08 05:10:19', '2021-11-08 05:10:19'),
(274, '', '2021-11-08 05:13:54', '2021-11-08 05:13:54'),
(275, '', '2021-11-08 05:22:39', '2021-11-08 05:22:39'),
(276, '', '2021-11-08 05:23:07', '2021-11-08 05:23:07'),
(277, '', '2021-11-08 05:31:07', '2021-11-08 05:31:07'),
(278, '', '2021-11-08 05:31:22', '2021-11-08 05:31:22'),
(279, '', '2021-11-08 05:32:20', '2021-11-08 05:32:20'),
(280, '', '2021-11-08 05:33:04', '2021-11-08 05:33:04'),
(281, '', '2021-11-08 05:33:10', '2021-11-08 05:33:10'),
(282, '', '2021-11-08 05:33:38', '2021-11-08 05:33:38'),
(283, '', '2021-11-08 05:34:26', '2021-11-08 05:34:26'),
(284, '', '2021-11-08 05:35:08', '2021-11-08 05:35:08'),
(285, '', '2021-11-08 05:35:40', '2021-11-08 05:35:40'),
(286, '', '2021-11-08 05:36:06', '2021-11-08 05:36:06'),
(287, '', '2021-11-08 05:36:39', '2021-11-08 05:36:39'),
(288, '', '2021-11-08 05:37:03', '2021-11-08 05:37:03'),
(289, '', '2021-11-08 05:37:20', '2021-11-08 05:37:20'),
(290, '', '2021-11-08 05:38:18', '2021-11-08 05:38:18'),
(291, '', '2021-11-08 05:38:41', '2021-11-08 05:38:41'),
(292, '', '2021-11-08 08:01:31', '2021-11-08 08:01:31'),
(293, '', '2021-11-08 08:02:00', '2021-11-08 08:02:00'),
(294, '', '2021-11-08 08:03:23', '2021-11-08 08:03:23'),
(295, '', '2021-11-08 08:03:53', '2021-11-08 08:03:53'),
(296, '', '2021-11-08 08:04:44', '2021-11-08 08:04:44'),
(297, '', '2021-11-25 04:21:27', '2021-11-25 04:21:27'),
(298, '', '2021-11-27 15:16:54', '2021-11-27 15:16:54'),
(299, '', '2021-11-27 15:53:40', '2021-11-27 15:53:40'),
(300, '', '2021-11-27 15:53:48', '2021-11-27 15:53:48'),
(301, '', '2021-11-28 10:58:08', '2021-11-28 10:58:08'),
(302, '', '2021-11-28 10:58:21', '2021-11-28 10:58:21'),
(303, '', '2021-11-28 10:58:21', '2021-11-28 10:58:21'),
(304, '', '2022-01-10 15:47:49', '2022-01-10 15:47:49'),
(305, '', '2022-01-10 15:48:06', '2022-01-10 15:48:06'),
(306, '', '2022-01-10 15:48:25', '2022-01-10 15:48:25'),
(307, '', '2022-01-10 15:48:39', '2022-01-10 15:48:39'),
(308, '', '2022-01-10 15:48:49', '2022-01-10 15:48:49'),
(309, '', '2022-01-10 15:49:00', '2022-01-10 15:49:00'),
(310, '', '2022-01-10 15:49:12', '2022-01-10 15:49:12'),
(311, '', '2022-01-10 15:49:22', '2022-01-10 15:49:22'),
(312, '', '2022-03-01 03:02:27', '2022-03-01 03:02:27'),
(313, '', '2022-03-01 03:03:07', '2022-03-01 03:03:07'),
(314, '', '2022-03-01 03:03:17', '2022-03-01 03:03:17'),
(315, '', '2022-03-01 03:03:25', '2022-03-01 03:03:25'),
(316, '', '2022-03-01 03:03:33', '2022-03-01 03:03:33'),
(317, '', '2022-03-01 03:03:45', '2022-03-01 03:03:45'),
(318, '', '2022-03-01 03:03:52', '2022-03-01 03:03:52'),
(319, '', '2022-03-01 03:04:01', '2022-03-01 03:04:01'),
(320, '', '2022-03-02 02:47:15', '2022-03-02 02:47:15'),
(321, '', '2022-03-02 02:47:25', '2022-03-02 02:47:25'),
(322, '', '2022-03-02 02:47:48', '2022-03-02 02:47:48'),
(323, '', '2022-03-02 02:49:48', '2022-03-02 02:49:48'),
(324, '', '2025-12-02 10:10:19', '2025-12-02 10:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE IF NOT EXISTS `attributes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attributes_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `slug`, `language`, `name`, `shop_id`, `created_at`, `updated_at`) VALUES
(2, 'size', 'en', 'Size', 2, '2021-10-10 11:47:38', '2021-10-10 11:47:38'),
(3, 'color', 'en', 'Color', 2, '2021-10-10 11:48:43', '2021-10-10 11:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_product`
--

DROP TABLE IF EXISTS `attribute_product`;
CREATE TABLE IF NOT EXISTS `attribute_product` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attribute_value_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_product_attribute_value_id_foreign` (`attribute_value_id`),
  KEY `attribute_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=498 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_product`
--

INSERT INTO `attribute_product` (`id`, `attribute_value_id`, `product_id`, `created_at`, `updated_at`) VALUES
(384, 8, 1, NULL, NULL),
(385, 9, 1, NULL, NULL),
(386, 14, 2, NULL, NULL),
(387, 15, 2, NULL, NULL),
(388, 8, 3, NULL, NULL),
(389, 9, 3, NULL, NULL),
(390, 11, 3, NULL, NULL),
(391, 12, 3, NULL, NULL),
(392, 9, 5, NULL, NULL),
(393, 10, 5, NULL, NULL),
(394, 8, 7, NULL, NULL),
(395, 10, 7, NULL, NULL),
(396, 11, 7, NULL, NULL),
(397, 12, 7, NULL, NULL),
(398, 13, 7, NULL, NULL),
(399, 8, 8, NULL, NULL),
(400, 9, 8, NULL, NULL),
(401, 11, 8, NULL, NULL),
(402, 13, 8, NULL, NULL),
(403, 14, 14, NULL, NULL),
(404, 15, 14, NULL, NULL),
(405, 16, 14, NULL, NULL),
(406, 8, 15, NULL, NULL),
(407, 9, 15, NULL, NULL),
(408, 11, 15, NULL, NULL),
(409, 12, 15, NULL, NULL),
(410, 8, 16, NULL, NULL),
(411, 10, 16, NULL, NULL),
(412, 11, 16, NULL, NULL),
(413, 12, 16, NULL, NULL),
(414, 8, 17, NULL, NULL),
(415, 9, 17, NULL, NULL),
(430, 8, 20, NULL, NULL),
(431, 9, 20, NULL, NULL),
(432, 10, 20, NULL, NULL),
(435, 8, 21, NULL, NULL),
(436, 9, 21, NULL, NULL),
(437, 8, 22, NULL, NULL),
(438, 9, 22, NULL, NULL),
(439, 10, 22, NULL, NULL),
(440, 8, 23, NULL, NULL),
(441, 9, 23, NULL, NULL),
(442, 10, 23, NULL, NULL),
(443, 8, 24, NULL, NULL),
(444, 9, 24, NULL, NULL),
(445, 10, 24, NULL, NULL),
(446, 14, 25, NULL, NULL),
(447, 15, 25, NULL, NULL),
(448, 16, 25, NULL, NULL),
(449, 17, 25, NULL, NULL),
(450, 14, 27, NULL, NULL),
(451, 15, 27, NULL, NULL),
(452, 16, 27, NULL, NULL),
(453, 17, 27, NULL, NULL),
(454, 8, 29, NULL, NULL),
(455, 10, 29, NULL, NULL),
(456, 9, 29, NULL, NULL),
(457, 14, 31, NULL, NULL),
(458, 15, 31, NULL, NULL),
(459, 16, 31, NULL, NULL),
(460, 17, 31, NULL, NULL),
(461, 8, 33, NULL, NULL),
(462, 9, 33, NULL, NULL),
(463, 11, 35, NULL, NULL),
(464, 12, 35, NULL, NULL),
(465, 8, 36, NULL, NULL),
(466, 9, 36, NULL, NULL),
(467, 10, 36, NULL, NULL),
(468, 11, 57, NULL, NULL),
(469, 12, 57, NULL, NULL),
(470, 13, 57, NULL, NULL),
(471, 11, 55, NULL, NULL),
(472, 12, 55, NULL, NULL),
(473, 13, 55, NULL, NULL),
(474, 9, 53, NULL, NULL),
(475, 10, 53, NULL, NULL),
(476, 11, 50, NULL, NULL),
(477, 12, 50, NULL, NULL),
(478, 14, 49, NULL, NULL),
(479, 15, 49, NULL, NULL),
(480, 16, 49, NULL, NULL),
(481, 17, 49, NULL, NULL),
(482, 12, 47, NULL, NULL),
(483, 13, 47, NULL, NULL),
(484, 14, 46, NULL, NULL),
(485, 15, 46, NULL, NULL),
(486, 16, 46, NULL, NULL),
(487, 17, 46, NULL, NULL),
(488, 11, 43, NULL, NULL),
(489, 12, 43, NULL, NULL),
(490, 13, 43, NULL, NULL),
(491, 14, 42, NULL, NULL),
(492, 15, 42, NULL, NULL),
(493, 16, 42, NULL, NULL),
(494, 17, 42, NULL, NULL),
(495, 8, 41, NULL, NULL),
(496, 9, 41, NULL, NULL),
(497, 10, 41, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

DROP TABLE IF EXISTS `attribute_values`;
CREATE TABLE IF NOT EXISTS `attribute_values` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `value` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `meta` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_values_attribute_id_foreign` (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `slug`, `attribute_id`, `value`, `language`, `meta`, `created_at`, `updated_at`) VALUES
(8, '', 3, 'Red', 'en', '#FF0000', '2021-10-10 11:48:43', '2021-10-10 11:48:43'),
(9, '', 3, 'Blue', 'en', '#0000FF', '2021-10-10 11:48:43', '2021-10-10 11:48:43'),
(10, '', 3, 'Yellow', 'en', '#FFFF00', '2021-10-10 11:48:43', '2021-10-10 11:48:43'),
(11, '', 2, 'Small', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(12, '', 2, 'Medium', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(13, '', 2, 'Large', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(14, '', 2, '7', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(15, '', 2, '8', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(16, '', 2, '9', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01'),
(17, '', 2, '10', 'en', NULL, '2021-11-28 10:11:01', '2021-11-28 10:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `image` json DEFAULT NULL,
  `cover_image` json DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `quote` text COLLATE utf8mb4_unicode_ci,
  `born` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `death` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `languages` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `socials` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `availabilities`
--

DROP TABLE IF EXISTS `availabilities`;
CREATE TABLE IF NOT EXISTS `availabilities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `from` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `booking_duration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_quantity` int NOT NULL,
  `bookable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookable_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `availabilities_order_id_foreign` (`order_id`),
  KEY `availabilities_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `balances`
--

DROP TABLE IF EXISTS `balances`;
CREATE TABLE IF NOT EXISTS `balances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint UNSIGNED NOT NULL,
  `admin_commission_rate` double DEFAULT NULL,
  `total_earnings` double NOT NULL DEFAULT '0',
  `withdrawn_amount` double NOT NULL DEFAULT '0',
  `current_balance` double NOT NULL DEFAULT '0',
  `is_custom_commission` tinyint(1) NOT NULL DEFAULT '0',
  `payment_info` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `balances_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `balances`
--

INSERT INTO `balances` (`id`, `shop_id`, `admin_commission_rate`, `total_earnings`, `withdrawn_amount`, `current_balance`, `is_custom_commission`, `payment_info`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 0, 0, 0, 0, '{\"bank\": \"test bank\", \"name\": \"chawkbazar\", \"email\": \"shop_owner@demo.com\", \"account\": 53415435}', '2021-10-09 13:24:30', '2021-10-09 13:39:44'),
(2, 2, 10, 14562, 7850, 6712, 0, '{\"bank\": \"vendor bank\", \"name\": \"vendor\", \"email\": \"vendor@demo.com\", \"account\": 6523498651}', '2021-10-09 13:57:34', '2021-11-28 06:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id` bigint UNSIGNED NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_type_id_foreign` (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `became_sellers`
--

DROP TABLE IF EXISTS `became_sellers`;
CREATE TABLE IF NOT EXISTS `became_sellers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_options` json NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `became_sellers_language_unique` (`language`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `became_sellers`
--

INSERT INTO `became_sellers` (`id`, `page_options`, `language`, `created_at`, `updated_at`) VALUES
(1, '{\"banner\": {\"image\": {\"id\": 2468, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2465/hero.png\", \"file_name\": \"hero.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2465/conversions/hero-thumbnail.jpg\"}, \"title\": \"Launch your online store effortlessly with Chawkbazar and start selling today.\", \"button1Link\": \"https://chawkbazar-admin.redq.io/register\", \"button1Name\": \"SignUp Now\", \"button2Link\": \"https://chawkbazar.redq.io/faq\", \"button2Name\": \"Visit Help Center\", \"description\": \"Transform your business with Chawkbazar\'s seamless platform and kickstart your online sales instantly. Enjoy a hassle-free setup and reach your customers quickly and efficiently.\", \"newsTickerURL\": \"https://chawkbazar-admin.redq.io/terms\", \"newsTickerTitle\": \"Have a look at our updated terms and conditions policy.\"}, \"contact\": {\"title\": \"Do you have question? Our experts are here to answer your questions\", \"description\": \"Our team of experts is readily available to address any inquiries you may have. Whether it\'s about our products, services, or anything else, feel free to ask—we\'re here to help!\"}, \"faqItems\": [{\"title\": \"What is the process of project final delivery system?\", \"description\": \"<p>The project final delivery system typically involves several key steps. Firstly, the team conducts a thorough review of all project components to ensure completeness and quality. Secondly, any necessary documentation and deliverables are prepared and organized for client handover. Finally, the project is formally presented to the client, followed by a comprehensive review and sign-off process to ensure satisfaction and successful completion.</p>\"}, {\"title\": \"What is payment process, believe in upfront?\", \"description\": \"<p>The payment process, believing in upfront, involves transactions where payment is made in full at the beginning of a service or purchase, ensuring immediate financial commitment and trust between parties. This approach fosters transparency and reliability, minimizing risks and uncertainties associated with delayed or partial payments. Overall, upfront payment simplifies financial agreements, enhancing accountability and smooth transactional experiences.</p>\"}, {\"title\": \"What is the process of project final delivery system?\", \"description\": \"<p>The project final delivery system typically involves several key steps. Firstly, the project team conducts a thorough review to ensure all requirements are met. Then, they package and prepare the deliverables for client acceptance, often including documentation and training materials. Finally, the deliverables are formally handed over to the client, marking the completion of the project.</p>\"}, {\"title\": \"Estimate project budget for categories?\", \"description\": \"<p>To estimate project budgets for different categories, conduct thorough research on required resources and their costs. Utilize historical data, industry benchmarks, and expert consultations to create accurate projections. Regularly review and adjust budgets as project requirements evolve to ensure financial success.</p>\"}, {\"title\": \"All about project customization & monitaization\", \"description\": \"<p>Project customization and monetization involve tailoring your project to specific needs while generating revenue. It encompasses adapting features, design, and functionality to meet user preferences or business requirements. Effective customization and monetization strategies can enhance user experience and drive financial success for your project.</p>\"}], \"faqTitle\": \"Frequently Ask Question\", \"dashboard\": {\"image\": {\"id\": 2469, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2466/mockup.png\", \"file_name\": \"mockup.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2466/conversions/mockup-thumbnail.jpg\"}, \"title\": \"Monetize user with Chawkbazar Dashboard\", \"buttonLink\": \"https://chawkbazar-admin.redq.io/\", \"buttonName\": \"Chawkbazar Admin\", \"button2Link\": \"https://chawkbazar-laravel-doc.vercel.app/\", \"button2Name\": \"Documentation\", \"description\": \"We offers high-quality films and the best documentary selection, \\nand the ability to browse alphabetically and by genre\"}, \"userStories\": [{\"link\": \"https://vimeo.com/356872441\", \"title\": \"World-class customer stories\", \"thumbnail\": {\"id\": 2472, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2469/image-312-%281%29.png\", \"file_name\": \"image-312-%281%29.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2469/conversions/image-312-%281%29-thumbnail.jpg\"}, \"description\": \"I\'ve never seen a platform as easy to use, as easy to onboard new users, as easy to scale, and as easy to customize to your own workflow, process, team, clientele, and changing environment.\"}], \"purposeItems\": [{\"icon\": {\"value\": \"BullsEyeIcon\"}, \"title\": \"Reach Customer\", \"description\": \"Strategically engage and connect with your target audience to foster meaningful relationships and drive business growth.\"}, {\"icon\": {\"value\": \"StoreIcon\"}, \"title\": \"Free Registration\", \"description\": \"Unlock exclusive benefits and join our community with hassle-free registration.\"}, {\"icon\": {\"value\": \"ChatIcon\"}, \"title\": \"Reliable Shipping\", \"description\": \"Fast, reliable, and hassle free delivery through Chawkbazar logistic network\"}, {\"icon\": {\"value\": \"BullsEyeIcon\"}, \"title\": \"Timely Payments\", \"description\": \"Funds are safely deposited directly to your bank account on a weekly basis.\"}, {\"icon\": {\"value\": \"StoreIcon\"}, \"title\": \"Marketing Tools\", \"description\": \"Find new customers & grow more with advertising and our whole range of marketing tools.\"}, {\"icon\": {\"value\": \"ReceiptIcon\"}, \"title\": \"Support & Training\", \"description\": \"Learn all about e-commerce for free and get help with seller support.\"}], \"purposeTitle\": \"Why Sell with Chawkbazar\", \"faqDescription\": \"Answers to common questions about our services and policies.\", \"guidelineItems\": [{\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"How to register as a seller on Chawkbazar Admin?\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"Benefits of selling product on Chawkbazar Marketplace\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"What is the shipping method for the sellers?\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"How to listing a product in Chawkbazar Marketplace?\"}, {\"link\": null, \"title\": \"How to register as a seller on Chawkbazar Admin?\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"Benefits of selling product on Chawkbazar Marketplace\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"What is the shipping method for the sellers?\"}, {\"link\": \"https://chawkbazar-admin.redq.io/\", \"title\": \"How to listing a product in Chawkbazar Marketplace?\"}], \"guidelineTitle\": \"Guideline for the seller\", \"userStoryTitle\": \"World-class customer stories\", \"commissionTitle\": \"Fee and Commission\", \"sellingStepsItem\": [{\"image\": {\"id\": 2458, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2455/Illustration.png\", \"file_name\": \"Illustration.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2455/conversions/Illustration-thumbnail.jpg\"}, \"title\": \"Signup for free\", \"description\": \"Discover all the necessary steps to successfully create your account here.\"}, {\"image\": {\"id\": 2459, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2456/Illustration-%281%29.png\", \"file_name\": \"Illustration-%281%29.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2456/conversions/Illustration-%281%29-thumbnail.jpg\"}, \"title\": \"Sell Product\", \"description\": \"Unlock profitable opportunities and begin selling your products today.\"}, {\"image\": {\"id\": 2460, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2457/Illustration-%282%29.png\", \"file_name\": \"Illustration-%282%29.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2457/conversions/Illustration-%282%29-thumbnail.jpg\"}, \"title\": \"Earn Money\", \"description\": \"Monetize your potential and start earning today.\"}, {\"image\": {\"id\": 2461, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2458/Illustration-%283%29.png\", \"file_name\": \"Illustration-%283%29.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2458/conversions/Illustration-%283%29-thumbnail.jpg\"}, \"title\": \"Grow Business\", \"description\": \"Elevate your enterprise and empower your business growth with us.\"}], \"sellerOpportunity\": {\"image\": {\"id\": 2470, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2467/app.png\", \"file_name\": \"app.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2467/conversions/app-thumbnail.jpg\"}, \"title\": \"Unlock the universe of Seller Opportunity\", \"buttonLink\": \"https://chawkbazar-admin.redq.io/\", \"buttonName\": \"Register Now\", \"button2Link\": \"https://chawkbazar-admin.redq.io/\", \"button2Name\": \"Documentation\", \"description\": \"We offers high-quality films and the best documentary selection, \\nand the ability to browse alphabetically and by genre\"}, \"sellingStepsTitle\": \"Start Selling In 4 Simple Steps\", \"purposeDescription\": \"Embarking on your digital entrepreneurship journey with Chawkbazar is a breeze. Over 1.4 million sellers have trusted us to nurture their businesses.\", \"guidelineDescription\": \"Starting your online business with Chawkbazar is easy. 14 lakh+ sellers trust us with their business.\", \"userStoryDescription\": \"I\'ve never seen a platform as easy to use, as easy to onboard new users, as easy to scale, and as easy to customize to your own workflow, process, team, clientele, and changing environment.\", \"commissionDescription\": \"Starting your online business with Chawkbazar is easy. 14 lakh+ sellers trust us with their business.\", \"defaultCommissionRate\": 10, \"isMultiCommissionRate\": false, \"sellingStepsDescription\": \"Launching your online business with Chawkbazar is a breeze. Join the 10 million sellers who already trust us with their success.\", \"defaultCommissionDetails\": \"Default Commission\"}', 'en', '2024-06-06 07:30:48', '2024-06-12 08:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` json DEFAULT NULL,
  `banner_image` json DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `parent` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_foreign` (`parent`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `language`, `icon`, `image`, `banner_image`, `details`, `parent`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bags', 'bags', 'en', 'HandBags', '[{\"id\": 26, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/26/bags.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/26/conversions/bags-thumbnail.jpg\"}, {\"id\": 319, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/319/bags.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/319/conversions/bags-thumbnail.jpg\"}]', '[{\"id\": 311, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/311/Bag.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/311/conversions/Bag-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:38:08', '2022-03-01 03:04:03', NULL),
(2, 'Kids', 'kids', 'en', 'Pants', '[{\"id\": 27, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/27/kids.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/27/conversions/kids-thumbnail.jpg\"}, {\"id\": 318, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/318/kids.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/318/conversions/kids-thumbnail.jpg\"}]', '[{\"id\": 310, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/310/Kids.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/310/conversions/Kids-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:38:27', '2022-03-01 03:03:54', NULL),
(3, 'Men', 'men', 'en', 'Wallet', '[{\"id\": 28, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/28/men.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/28/conversions/men-thumbnail.jpg\"}, {\"id\": 317, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/317/man.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/317/conversions/man-thumbnail.jpg\"}]', '[{\"id\": 309, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/309/Men.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/309/conversions/Men-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:38:39', '2022-03-01 03:03:47', NULL),
(4, 'Sneakers', 'sneakers', 'en', 'Accessories', '[{\"id\": 29, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/29/sneekers.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/29/conversions/sneekers-thumbnail.jpg\"}, {\"id\": 316, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/316/sneakers.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/316/conversions/sneakers-thumbnail.jpg\"}]', '[{\"id\": 308, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/308/Sneakers.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/308/conversions/Sneakers-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:39:56', '2022-03-01 03:03:35', NULL),
(5, 'Sports', 'sports', 'en', 'Accessories', '[{\"id\": 30, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/30/sports.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/30/conversions/sports-thumbnail.jpg\"}, {\"id\": 315, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/315/sports.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/315/conversions/sports-thumbnail.jpg\"}]', '[{\"id\": 307, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/307/sports.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/307/conversions/sports-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:40:07', '2022-03-01 03:03:27', NULL),
(6, 'Sunglass', 'sunglass', 'en', 'Eyes', '[{\"id\": 31, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/31/sunglass.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/31/conversions/sunglass-thumbnail.jpg\"}, {\"id\": 314, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/314/sunglass.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/314/conversions/sunglass-thumbnail.jpg\"}]', '[{\"id\": 306, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/306/sunglass.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/306/conversions/sunglass-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:40:22', '2022-03-01 03:03:19', NULL),
(7, 'Watch', 'watch', 'en', 'Accessories', '[{\"id\": 32, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/32/watch.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/32/conversions/watch-thumbnail.jpg\"}, {\"id\": 313, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/313/watch.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/313/conversions/watch-thumbnail.jpg\"}]', '[{\"id\": 305, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/305/watch.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/305/conversions/watch-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', NULL, '2021-10-09 14:40:33', '2022-03-01 03:03:08', NULL),
(8, 'Women', 'women', 'en', 'WomenDress', '[{\"id\": 33, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/33/women.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/33/conversions/women-thumbnail.jpg\"}, {\"id\": 312, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/312/woman.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/312/conversions/woman-thumbnail.jpg\"}]', '[{\"id\": 304, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/304/women.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/304/conversions/women-thumbnail.jpg\"}]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet I feel that I never was a greater artist than now.', NULL, '2021-10-09 14:40:47', '2022-03-01 03:02:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

DROP TABLE IF EXISTS `category_product`;
CREATE TABLE IF NOT EXISTS `category_product` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_product_product_id_foreign` (`product_id`),
  KEY `category_product_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_product`
--

INSERT INTO `category_product` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 8),
(2, 2, 5),
(3, 2, 4),
(4, 2, 3),
(5, 3, 8),
(6, 4, 6),
(7, 4, 5),
(8, 5, 8),
(9, 5, 5),
(10, 6, 7),
(11, 7, 8),
(12, 8, 8),
(13, 9, 8),
(14, 10, 8),
(15, 10, 3),
(16, 11, 8),
(17, 12, 1),
(18, 11, 6),
(19, 11, 3),
(20, 11, 2),
(21, 10, 2),
(22, 14, 5),
(23, 14, 4),
(24, 14, 3),
(25, 13, 8),
(26, 13, 7),
(27, 13, 3),
(28, 15, 8),
(29, 16, 5),
(30, 16, 3),
(31, 17, 3),
(32, 18, 7),
(33, 18, 3),
(34, 19, 7),
(35, 19, 3),
(36, 19, 8),
(37, 20, 8),
(38, 20, 5),
(39, 21, 3),
(40, 21, 5),
(41, 22, 8),
(42, 23, 1),
(43, 24, 1),
(44, 24, 8),
(45, 25, 8),
(46, 25, 3),
(47, 25, 5),
(48, 25, 4),
(49, 26, 8),
(50, 26, 5),
(51, 26, 3),
(52, 26, 1),
(53, 27, 8),
(54, 27, 4),
(55, 27, 5),
(56, 27, 3),
(57, 28, 8),
(58, 28, 5),
(59, 28, 6),
(60, 28, 3),
(61, 29, 2),
(62, 30, 1),
(63, 31, 5),
(64, 31, 3),
(65, 32, 1),
(66, 33, 8),
(67, 34, 8),
(68, 35, 2),
(69, 36, 8),
(70, 37, 7),
(71, 38, 2),
(72, 39, 6),
(73, 40, 6),
(74, 40, 3),
(75, 41, 3),
(76, 41, 8),
(77, 42, 5),
(78, 43, 3),
(79, 44, 1),
(80, 45, 2),
(81, 46, 4),
(82, 47, 3),
(83, 48, 7),
(84, 49, 5),
(85, 49, 3),
(86, 49, 8),
(87, 50, 8),
(88, 51, 1),
(89, 52, 6),
(90, 53, 8),
(91, 54, 7),
(92, 55, 8),
(93, 56, 8),
(94, 57, 2);

-- --------------------------------------------------------

--
-- Table structure for table `category_shop`
--

DROP TABLE IF EXISTS `category_shop`;
CREATE TABLE IF NOT EXISTS `category_shop` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_shop_shop_id_foreign` (`shop_id`),
  KEY `category_shop_category_id_foreign` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` json DEFAULT NULL,
  `data` json DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_slug_unique` (`slug`),
  UNIQUE KEY `cms_pages_path_unique` (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

DROP TABLE IF EXISTS `commissions`;
CREATE TABLE IF NOT EXISTS `commissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_level` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_balance` int NOT NULL,
  `max_balance` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commission` double(8,2) NOT NULL,
  `image` json DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `level`, `sub_level`, `description`, `min_balance`, `max_balance`, `commission`, `image`, `language`, `created_at`, `updated_at`) VALUES
(79, 'Level One', 'Charges for listing a product on the platform.', 'Earn attractive commissions with every sale! Our competitive rates reward your hard work and dedication, motivating you to achieve more. Join our sales team and turn your efforts into substantial earnings.', 200, '3000', 15.00, '{\"id\": 2462, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2459/Frame.png\", \"file_name\": \"Frame.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2459/conversions/Frame-thumbnail.jpg\"}', 'en', '2024-06-11 15:52:37', '2024-06-12 08:31:46'),
(83, 'Level Two', 'Charges for sales in a product on the platform.', 'Get lucrative commissions on each sale! Our affordable prices encourage you to work harder and accomplish more by rewarding your diligence and hard work. Become a member of our sales team and make a big income from your efforts.', 500, '1000', 20.00, '{\"id\": 2467, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2464/Frame.png\", \"file_name\": \"Frame.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/2464/conversions/Frame-thumbnail.jpg\"}', 'en', '2024-06-12 07:58:09', '2024-06-12 08:31:46');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_user_id_foreign` (`user_id`),
  KEY `conversations_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` json DEFAULT NULL,
  `type` enum('fixed','percentage','free_shipping','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `minimum_cart_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `active_from` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_at` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Default value is false but For authenticated customer the value is true',
  `is_approve` tinyint(1) NOT NULL DEFAULT '0',
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_shop_id_foreign` (`shop_id`),
  KEY `coupons_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `language`, `description`, `image`, `type`, `amount`, `minimum_cart_amount`, `active_from`, `expire_at`, `target`, `is_approve`, `shop_id`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'EID2', 'en', NULL, '{\"id\": 246, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/246/2x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/246/conversions/2x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 2.00, 0.00, '2021-10-25T07:34:58.000Z', '2026-11-30T07:34:58.000Z', 0, 1, NULL, NULL, '2021-10-24 22:37:05', '2024-01-14 21:22:15', NULL),
(2, '4OFF', 'en', NULL, '{\"id\": 247, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/247/4x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/247/conversions/4x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 4.00, 0.00, '2021-10-25T07:34:58.000Z', '2027-12-31T07:34:58.000Z', 0, 1, NULL, NULL, '2021-10-24 22:37:29', '2024-01-14 21:22:12', NULL),
(3, 'RAMADAN5', 'en', NULL, '{\"id\": 248, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/248/5x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/248/conversions/5x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 5.00, 0.00, '2021-10-25T07:34:58.000Z', '2030-12-31T07:34:58.000Z', 0, 1, NULL, NULL, '2021-10-24 22:39:35', '2024-01-14 21:22:09', NULL),
(4, '6OFF', 'en', NULL, '{\"id\": 249, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/249/6x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/249/conversions/6x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 6.00, 0.00, '2021-10-25T07:34:58.000Z', '2029-01-31T07:34:58.000Z', 0, 1, NULL, NULL, '2021-10-24 22:40:08', '2024-01-14 21:22:07', NULL),
(5, 'SUMMER8', 'en', NULL, '{\"id\": 250, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/250/8x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/250/conversions/8x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 8.00, 0.00, '2021-10-25T12:32:39.000Z', '2028-12-31T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:35:30', '2024-01-14 21:22:03', NULL),
(6, 'WINTER10', 'en', NULL, '{\"id\": 251, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/251/10x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/251/conversions/10x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 10.00, 0.00, '2021-10-25T12:32:39.000Z', '2026-01-31T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:36:35', '2024-01-14 21:22:00', NULL),
(7, '12OFF', 'en', NULL, '{\"id\": 252, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/252/12x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/252/conversions/12x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 12.00, 0.00, '2021-10-25T12:32:39.000Z', '2027-11-30T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:37:34', '2024-01-14 21:21:57', NULL),
(8, 'SUMMER15', 'en', NULL, '{\"id\": 253, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/253/15x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/253/conversions/15x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 15.00, 0.00, '2021-10-25T12:32:39.000Z', '2028-02-28T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:38:02', '2024-01-14 21:21:52', NULL),
(9, 'CHRISTMAS18', 'en', NULL, '{\"id\": 254, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/254/18x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/254/conversions/18x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 18.00, 0.00, '2021-10-25T12:32:39.000Z', '2029-12-31T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:39:20', '2024-01-14 20:26:16', NULL),
(10, '20OFF', 'en', NULL, '{\"id\": 255, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/255/20x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/255/conversions/20x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 20.00, 0.00, '2021-10-25T12:32:39.000Z', '2030-10-25T12:32:39.000Z', 0, 1, NULL, NULL, '2021-10-25 03:39:51', '2024-01-14 20:26:13', NULL),
(11, 'NEWYEARSALE', 'en', NULL, '{\"id\": 255, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/255/20x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/255/conversions/20x2x-thumbnail-thumbnail.jpg\"}', 'percentage', 10.00, 100.00, '2024-01-15T04:26:51.000Z', '2024-12-31T18:00:00.000Z', 0, 1, 11, 1, '2024-01-14 20:27:14', '2024-01-14 20:36:23', NULL),
(12, 'BLACKFRIDAY', 'en', NULL, '{\"id\": 254, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/254/18x2x-thumbnail.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/254/conversions/18x2x-thumbnail-thumbnail.jpg\"}', 'fixed', 2.00, 500.00, '2024-01-15T04:36:02.519Z', '2025-08-31T18:00:00.000Z', 0, 1, 11, 1, '2024-01-14 20:39:20', '2024-01-14 20:41:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usages`
--

DROP TABLE IF EXISTS `coupon_usages`;
CREATE TABLE IF NOT EXISTS `coupon_usages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `used_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_usages_user_id_foreign` (`user_id`),
  KEY `coupon_usages_order_id_foreign` (`order_id`),
  KEY `coupon_usages_coupon_id_user_id_index` (`coupon_id`,`user_id`),
  KEY `coupon_usages_coupon_id_index` (`coupon_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_times`
--

DROP TABLE IF EXISTS `delivery_times`;
CREATE TABLE IF NOT EXISTS `delivery_times` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposit_product`
--

DROP TABLE IF EXISTS `deposit_product`;
CREATE TABLE IF NOT EXISTS `deposit_product` (
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `deposit_product_resource_id_foreign` (`resource_id`),
  KEY `deposit_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `digital_files`
--

DROP TABLE IF EXISTS `digital_files`;
CREATE TABLE IF NOT EXISTS `digital_files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `attachment_id` bigint UNSIGNED NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fileable_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `download_tokens`
--

DROP TABLE IF EXISTS `download_tokens`;
CREATE TABLE IF NOT EXISTS `download_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `digital_file_id` bigint UNSIGNED DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `download_tokens_digital_file_id_foreign` (`digital_file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dropoff_location_product`
--

DROP TABLE IF EXISTS `dropoff_location_product`;
CREATE TABLE IF NOT EXISTS `dropoff_location_product` (
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `dropoff_location_product_resource_id_foreign` (`resource_id`),
  KEY `dropoff_location_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `faq_title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `faq_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `faq_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faqs_user_id_foreign` (`user_id`),
  KEY `faqs_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `user_id`, `shop_id`, `faq_title`, `slug`, `faq_description`, `faq_type`, `issued_by`, `language`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'What is your return policy?', 'what-is-your-return-policy', 'We have a flexible return policy. If you\'re not satisfied with your purchase, you can return most items within 30 days for a full refund or exchange. Please review our Return Policy for more details.', 'global', 'Super Admin', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(2, 1, NULL, 'Can I track my order?', 'can-i-track-my-order', 'Yes, you can track your order\'s status. Once your order is shipped, you will receive a tracking number via email. You can use this tracking number to monitor the progress of your delivery.', 'global', 'Super Admin', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(3, 1, NULL, 'How long will it take to receive my order?', 'how-long-will-it-take-to-receive-my-order', 'Delivery times may vary depending on your location and the shipping method you choose. Typically, orders are processed and shipped within 1-2 business days. You can check the estimated delivery time during checkout.', 'global', 'Super Admin', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(4, 1, NULL, 'What payment methods do you accept?', 'what-payment-methods-do-you-accept', 'We accept a variety of payment methods, including credit cards (Visa, MasterCard, American Express), PayPal, and more. You can choose your preferred payment option during the checkout process.', 'global', 'Super Admin', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(5, 1, NULL, 'How can I place an order?', 'how-can-i-place-an-order', 'To place an order, simply browse our online store, add the items you want to your cart, and proceed to checkout. Follow the prompts to enter your shipping information and payment details to complete your purchase.', 'global', 'Super Admin', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(6, 1, 1, 'What is the delivery process for furniture purchases?', 'what-is-the-delivery-process-for-furniture-purchases?', 'We offer convenient and reliable furniture delivery services. After making your purchase, our team will contact you to schedule a delivery time that suits your availability. Our delivery professionals will assemble and set up the furniture in your desired room. Please refer to our Delivery Information page for more details. ', 'shop', 'Blythe Knowles', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(7, 1, 1, 'Do you provide warranty coverage for furniture items?', 'do-you-provide-warranty-coverage-for-furniture-items?', 'Yes, many of our furniture items come with manufacturer warranties that cover structural defects and craftsmanship issues. The duration and terms of the warranty may vary by product. You can find warranty information in the product descriptions, or you can contact our customer support team for specific details. ', 'shop', 'Blythe Knowles', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(8, 1, 2, 'What is your return policy for clothing items?', 'what-is-your-return-policy-for-clothing-items?', 'We offer a hassle-free return policy for clothing purchases. If you are not completely satisfied with your clothing item, you can return it within 30 days of purchase, as long as the item is in its original condition with tags attached. For detailed information on our return process, please refer to our Returns and Exchanges page. ', 'shop', 'Urban Threads Emporium', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(9, 1, 2, 'Do you offer plus-size or petite clothing options?', 'do-you-offer-plus-size-or-petite-clothing-options?', 'Yes, we strive to provide a diverse range of clothing sizes to accommodate all body types. Our inventory includes a selection of plus-size and petite clothing options in various styles and designs. You can use our size filters or contact our customer support for assistance in finding the perfect fit. ', 'shop', 'Urban Threads Emporium', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(10, 1, 11, 'How can I determine the right size and style of bag for my needs when shopping online?', 'how-can-i-determine-the-right-size-and-style-of-bag-for-my-needs-when-shopping-online?', 'Finding the perfect bag online is made easier with our selection and helpful tools. Here\'s how to make an informed choice. Each bag product on our website includes detailed descriptions, including dimensions, capacity, and features. Read these descriptions carefully to understand the size and functionality of the bag. We provide high-quality images and, in some cases, videos that showcase the bag from different angles and show it in use. Visual aids can help you assess the bag\'s size and style better. Check out reviews and ratings from other customers who have purchased the same bag. Feedback from others who have used the bag for various purposes can provide valuable insights. We may offer size guides or charts to help you understand the bag\'s dimensions in relation to common items you might carry, such as laptops, books, or clothing. If you have specific questions or need personalized recommendations, our customer support team is here to assist you. Use our live chat feature or contact us via email or phone for expert guidance. Rest assured that we have a flexible returns and exchanges policy. If the bag doesn\'t meet your expectations, you can usually return or exchange it within a specified time frame.  ', 'shop', 'Marny Rose', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(11, 1, 11, 'What measures do you take to ensure the quality and durability of the bags you sell online?', 'what-measures-do-you-take-to-ensure-the-quality-and-durability-of-the-bags-you-sell-online?', 'We are committed to providing high-quality and durable bags to our customers. Here\'s how we ensure the quality and durability of the bags in our online shop. We carefully curate our collection from reputable brands known for their craftsmanship and quality. We partner with brands that have a strong track record in producing durable and long-lasting bags. Each bag product listing includes detailed information about the materials used in its construction. This allows you to assess the bag\'s durability and suitability for your needs. Many of our bags come with manufacturer warranties that cover defects in materials and workmanship. Check the product listing for warranty details. We value customer feedback and consider it when selecting bags for our inventory. Positive reviews and high ratings from satisfied customers are indicative of product quality. In the rare event that you receive a bag with defects or quality issues, our returns and exchanges policy allows you to return or exchange the product for your peace of mind. ', 'shop', 'Marny Rose', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(12, 1, 4, 'How can I find the right makeup products for my skin tone and type when shopping online?', 'how-can-i-find-the-right-makeup-products-for-my-skin-tone-and-type-when-shopping-online?', 'Shopping for makeup online is made easier with our website\'s tools and resources to help you find the perfect products for your skin. Here\'s how to get started. Each makeup product on our website includes detailed descriptions, including shade names, undertones, and ingredients. Read these descriptions carefully to find products that match your skin tone and type. We offer shade matching tools and guides for foundations, concealers, and other complexion products. These tools can help you identify your ideal shade based on your skin undertones. Check out product reviews and ratings left by other customers who have similar skin tones or concerns. Their feedback can provide valuable insights into how a product performs. If you\'re unsure about a product or need personalized recommendations, our customer support team is here to assist you. Use our live chat or contact us via email or phone for expert guidance.  We may offer sample sizes or testers for select products. Trying out samples can be a great way to test shades and formulas before committing to a full-sized product. Rest assured that we have a flexible returns and exchanges policy. If a product doesn\'t meet your expectations, you can usually return or exchange it within a specified time frame.', 'shop', 'Boho Bliss Emporium', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(13, 1, 4, 'How do I ensure the authenticity and quality of the makeup products purchased online?', 'how-do-i-ensure-the-authenticity-and-quality-of-the-makeup-products-purchased-online?', 'We prioritize the authenticity and quality of all our makeup products to ensure a positive shopping experience. Here\'s how we guarantee the authenticity and quality of our products. We are an authorized retailer for all the brands and products we carry. This means that we source our products directly from reputable manufacturers and distributors. Makeup products are delivered in their original, sealed packaging. We do not sell opened or tampered products. We only carry well-known and trusted makeup brands with a proven track record for quality and safety. Each product listing on our website includes detailed information, including brand, ingredients, and usage instructions, to help you make an informed decision. We value customer feedback and take it into account when selecting products for our inventory. Positive reviews and high ratings from satisfied customers are indicative of product quality.  In the unlikely event that you receive a product that doesn\'t meet your expectations, our flexible returns and exchanges policy allows you to return or exchange it for your peace of mind. ', 'shop', 'Boho Bliss Emporium', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(14, 1, 5, 'How do I place an order for bakery products on your website?', 'how-do-i-place-an-order-for-bakery-products-on-your-website?', 'Ordering delicious bakery products from our online shop is easy and convenient. Here\'s a step-by-step guide to placing an order: Visit our website to explore our mouthwatering range of bakery products. You can browse by category, including bread, pastries, cakes, and more. Click on the items you\'d like to purchase to view detailed descriptions, prices, and available options (e.g., flavors, sizes, and quantities). Add your desired products to your virtual shopping cart.  Before proceeding to checkout, review the items in your cart to ensure you\'ve selected everything you want. You can make adjustments, update quantities, or remove items as needed. When you\'re ready to complete your order, proceed to the checkout page. Here, you\'ll provide your delivery information and select your preferred delivery date and time slot. Choose your preferred payment method, such as credit/debit card or digital wallet, and securely enter your payment details. After successful payment, you\'ll receive an order confirmation via email or SMS. This confirmation will include the details of your order, delivery date, and a unique order number. On the scheduled delivery date, our team will carefully prepare your bakery items and deliver them to your doorstep. You\'ll receive a notification when your order is on its way. Once your delicious bakery treats arrive, simply unpack and enjoy your freshly baked goodies. ', 'shop', 'Sleek Streetwear Co.', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(15, 1, 5, 'Do you accommodate special dietary needs or allergies?', 'do-you-accommodate-special-dietary-needs-or-allergies?', 'Yes, we strive to accommodate various dietary needs and allergies to ensure that everyone can enjoy our bakery products. Here\'s how we address specific dietary requirements: Each product on our website includes detailed information about allergens, such as nuts, dairy, eggs, and gluten. You can check these allergen labels to make informed choices. We offer a selection of products tailored to specific dietary preferences and restrictions. This may include gluten-free, vegan, or sugar-free options. You can easily filter products by dietary category on our website to find suitable choices. In some cases, we may be able to customize certain products to meet your specific dietary needs. If you have a special request or dietary requirement, please reach out to our customer support team, and we\'ll do our best to assist you. ', 'shop', 'Sleek Streetwear Co.', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(16, 1, 6, 'How does the online ordering and delivery process work?', 'how-does-the-online-ordering-and-delivery-process-work?', 'Ordering groceries online with us is simple and convenient. Here\'s a step-by-step guide to our process:  Visit our website or mobile app to browse our wide selection of groceries. You can search for specific items or explore categories. Add the products you need to your virtual shopping cart.  Before checkout, review your cart to ensure you have everything you need. You can also customize your order, specify quantities, and make any necessary adjustments.  Proceed to the checkout page to review your order one last time. You can choose your preferred payment method, including credit/debit cards or digital wallets, and complete the transaction securely. Select your preferred delivery time slot. We offer flexible delivery options to accommodate your schedule. Once your order is placed, you will receive an order confirmation via email or SMS. You can track the status of your order through your account. Our dedicated delivery team will carefully pack your groceries and deliver them to your doorstep at the chosen time. You\'ll receive a notification when your order is out for delivery.  Receive your groceries, unpack, and enjoy your fresh and quality products. ', 'shop', 'Ethereal Essence Boutique', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(17, 1, 6, 'What are the delivery fees and minimum order requirements?', 'what-are-the-delivery-fees-and-minimum-order-requirements?', 'We aim to provide affordable and convenient online grocery shopping. Our delivery fees and minimum order requirements are as follows: The delivery fee may vary depending on your location and the time slot you choose. We strive to keep our delivery charges competitive and transparent. You can view the applicable fees during the checkout process. To place an order for delivery, we have a minimum order requirement. This requirement helps us cover the costs associated with packing and delivering your groceries. The minimum order amount may vary based on your location, but you can easily check the specific minimum for your area on our website or app. Please note that we may offer promotions and discounts from time to time, including waived delivery fees for orders over a certain amount. Keep an eye out for these special offers to save even more on your online grocery shopping. ', 'shop', 'Ethereal Essence Boutique', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(18, 1, 7, 'Can I order both physical books and e-books from your store?', 'can-i-order-both-physical-books-and-e-books-from-your-store?', 'Yes, you can choose from a wide selection of physical books and e-books in our store. Simply browse our catalog and select your preferred format for each title. Physical books will be delivered to your address, while e-books can be downloaded instantly upon purchase.', 'shop', 'Xena Ochoa', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(19, 1, 7, 'Do you offer book recommendations or have a book club?', 'do-you-offer-book-recommendations-or-have-a-book-club?', 'We love books, and we\'re here to help you discover new reads! You can explore our Recommended Reads section for curated book recommendations. Additionally, we periodically host virtual book club events where readers can discuss and explore books together. Stay tuned for announcements on our website or social media.', 'shop', 'Xena Ochoa', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(20, 1, 8, 'Do you provide warranty coverage for electronic gadgets?', 'do-you-provide-warranty-coverage-for-electronic-gadgets?', 'Yes, most electronic gadgets come with manufacturer warranties that cover defects and malfunctions. The warranty duration and terms vary by product and brand. You can find warranty information in the product descriptions or contact our customer support for specific details.', 'shop', 'Lavinia Burch', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(21, 1, 8, 'Are your gadgets brand new, or do you offer refurbished options as well?', 'are-your-gadgets-brand-new,-or-do-you-offer-refurbished-options-as-well?', 'We primarily offer brand new gadgets, but we may occasionally have certified refurbished options available. Each product listing will specify whether it is new or refurbished. Refurbished gadgets undergo thorough testing and quality checks to ensure they meet high standards. ', 'shop', 'Lavinia Burch', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(22, 1, 9, 'Can I order prescription medications from your online medicine shop?', 'can-i-order-prescription-medications-from-your-online-medicine-shop?', 'We do not offer prescription medications. Our shop specializes in over-the-counter (OTC) and wellness products. Please consult a healthcare professional for prescription medications and guidance. ', 'shop', 'Claire Miranda', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(23, 1, 9, 'What measures do you take to ensure the authenticity and safety of the medicines you sell?', 'what-measures-do-you-take-to-ensure-the-authenticity-and-safety-of-the-medicines-you-sell?', 'We work exclusively with trusted suppliers and brands to ensure the authenticity and safety of the medicines and wellness products in our inventory. All products are sourced from licensed manufacturers and adhere to strict quality and safety standards. ', 'shop', 'Claire Miranda', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(24, 1, 10, 'Can I return baby care items if they are unopened and unused?', 'can-i-return-baby-care-items-if-they-are-unopened-and-unused?', 'Yes, you can return unopened and unused baby care items within 30 days of purchase. We prioritize the safety and satisfaction of our customers, and our return policy reflects that commitment.', 'shop', 'Drake Cain', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18'),
(25, 1, 10, 'Are your baby care products free from harmful chemicals and safe for infants?', 'are-your-baby-care-products-free-from-harmful-chemicals-and-safe-for-infants?', 'Absolutely. We source baby care products from reputable brands known for their commitment to safety and quality. All our products comply with safety standards and are free from harmful chemicals. You can find detailed ingredient information on product labels and descriptions. ', 'shop', 'Drake Cain', 'en', NULL, '2025-12-02 08:55:18', '2025-12-02 08:55:18');

-- --------------------------------------------------------

--
-- Table structure for table `feature_product`
--

DROP TABLE IF EXISTS `feature_product`;
CREATE TABLE IF NOT EXISTS `feature_product` (
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `feature_product_resource_id_foreign` (`resource_id`),
  KEY `feature_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `positive` tinyint(1) DEFAULT NULL,
  `negative` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedbacks_user_id_foreign` (`user_id`),
  KEY `feedbacks_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flash_sales`
--

DROP TABLE IF EXISTS `flash_sales`;
CREATE TABLE IF NOT EXISTS `flash_sales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` datetime NOT NULL DEFAULT '2025-12-02 10:55:14',
  `end_date` datetime NOT NULL,
  `sale_status` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('percentage','fixed_rate','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `rate` int DEFAULT NULL,
  `sale_builder` json DEFAULT NULL,
  `image` json DEFAULT NULL,
  `cover_image` json DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flash_sale_products`
--

DROP TABLE IF EXISTS `flash_sale_products`;
CREATE TABLE IF NOT EXISTS `flash_sale_products` (
  `flash_sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  KEY `flash_sale_products_flash_sale_id_foreign` (`flash_sale_id`),
  KEY `flash_sale_products_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flash_sale_requests`
--

DROP TABLE IF EXISTS `flash_sale_requests`;
CREATE TABLE IF NOT EXISTS `flash_sale_requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flash_sale_id` bigint UNSIGNED NOT NULL,
  `request_status` tinyint(1) NOT NULL DEFAULT '0',
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `flash_sale_requests_flash_sale_id_foreign` (`flash_sale_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flash_sale_requests_products`
--

DROP TABLE IF EXISTS `flash_sale_requests_products`;
CREATE TABLE IF NOT EXISTS `flash_sale_requests_products` (
  `flash_sale_requests_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `flash_sale_requests_products_flash_sale_requests_id_foreign` (`flash_sale_requests_id`),
  KEY `flash_sale_requests_products_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE IF NOT EXISTS `manufacturers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `image` json DEFAULT NULL,
  `cover_image` json DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `type_id` bigint UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `socials` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `manufacturers_type_id_foreign` (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `custom_properties` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `generated_conversions`, `custom_properties`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(1, 'Marvel\\Database\\Models\\Attachment', 1, 'e0d4cfc9-cb74-4598-8b7a-3715fbfd9aa9', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 1, '2021-10-09 10:30:23', '2021-10-09 10:30:23'),
(2, 'Marvel\\Database\\Models\\Attachment', 2, '578c1f0b-dfdc-46aa-a04f-2ded1e57b6e2', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 2, '2021-10-09 10:30:34', '2021-10-09 10:30:34'),
(3, 'Marvel\\Database\\Models\\Attachment', 3, 'fc6d10dd-804d-49a2-9167-5b68d08f7c08', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 3, '2021-10-09 10:39:00', '2021-10-09 10:39:00'),
(4, 'Marvel\\Database\\Models\\Attachment', 4, 'ab5238bf-6a68-420f-b54b-4e4b94213c9f', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 4, '2021-10-09 10:42:22', '2021-10-09 10:42:22'),
(5, 'Marvel\\Database\\Models\\Attachment', 5, '7a546abd-790f-4328-81cc-698ea70e2363', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 5, '2021-10-09 10:47:15', '2021-10-09 10:47:15'),
(6, 'Marvel\\Database\\Models\\Attachment', 6, '41c98d41-d64d-4d8a-b714-61f69d823ab7', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '[]', '[]', '[]', 6, '2021-10-09 10:47:43', '2021-10-09 10:47:43'),
(7, 'Marvel\\Database\\Models\\Attachment', 7, '93d27a3c-bb8c-42ed-9872-68403647f3ce', 'default', 'user', 'user.png', 'image/png', 's3', 's3', 22159, '[]', '{\"thumbnail\": true}', '[]', '[]', 7, '2021-10-09 10:53:45', '2021-10-09 10:53:45'),
(8, 'Marvel\\Database\\Models\\Attachment', 8, '50541671-4dc2-4669-a80d-43c13854922e', 'default', 'Untitled-4', 'Untitled-4.jpg', 'image/jpeg', 's3', 's3', 192327, '[]', '{\"thumbnail\": true}', '[]', '[]', 8, '2021-10-09 13:20:22', '2021-10-09 13:20:22'),
(9, 'Marvel\\Database\\Models\\Attachment', 9, '45930543-3fde-4c83-932c-b7aed5ca4d81', 'default', 'Untitled-4', 'Untitled-4.jpg', 'image/jpeg', 's3', 's3', 192327, '[]', '{\"thumbnail\": true}', '[]', '[]', 9, '2021-10-09 13:20:25', '2021-10-09 13:20:26'),
(10, 'Marvel\\Database\\Models\\Attachment', 10, 'f34e2f26-0eb6-4b83-bcac-a1d6248854be', 'default', 'logo', 'logo.svg', 'image/svg', 's3', 's3', 4193, '[]', '[]', '[]', '[]', 10, '2021-10-09 13:21:30', '2021-10-09 13:21:30'),
(11, 'Marvel\\Database\\Models\\Attachment', 11, 'c485538e-769f-4d01-95be-e6bb9b6226a5', 'default', 'og-image-01', 'og-image-01.png', 'image/png', 's3', 's3', 20029, '[]', '{\"thumbnail\": true}', '[]', '[]', 11, '2021-10-09 13:21:40', '2021-10-09 13:21:40'),
(12, 'Marvel\\Database\\Models\\Attachment', 12, 'a83f22c6-8eea-4fdd-bf51-8b2eec93c8ba', 'default', 'chawkBazar573_350', 'chawkBazar573_350.png', 'image/png', 's3', 's3', 97336, '[]', '{\"thumbnail\": true}', '[]', '[]', 12, '2021-10-09 13:22:16', '2021-10-09 13:22:16'),
(13, 'Marvel\\Database\\Models\\Attachment', 13, 'f1e9e1e7-9923-4cea-8b0e-42b98beb43bc', 'default', 'Men\'s Collection', 'Men\'s-Collection.jpg', 'image/jpeg', 's3', 's3', 55377, '[]', '{\"thumbnail\": true}', '[]', '[]', 13, '2021-10-09 14:01:35', '2021-10-09 14:01:35'),
(14, 'Marvel\\Database\\Models\\Attachment', 14, 'b618da8d-b5f4-47fe-947e-86a6ee1e0ebf', 'default', 'Men\'s Collection', 'Men\'s-Collection.jpg', 'image/jpeg', 's3', 's3', 55377, '[]', '{\"thumbnail\": true}', '[]', '[]', 14, '2021-10-09 14:02:11', '2021-10-09 14:02:11'),
(15, 'Marvel\\Database\\Models\\Attachment', 15, '03601c65-8744-47b9-951b-40ec15272a78', 'default', 'fashion', 'fashion.png', 'image/png', 's3', 's3', 2574, '[]', '{\"thumbnail\": true}', '[]', '[]', 15, '2021-10-09 14:05:23', '2021-10-09 14:05:23'),
(16, 'Marvel\\Database\\Models\\Attachment', 16, 'b38ee4af-259f-40cc-9920-a7ba40d71e74', 'default', 'Untitled-3', 'Untitled-3.jpg', 'image/jpeg', 's3', 's3', 70753, '[]', '{\"thumbnail\": true}', '[]', '[]', 16, '2021-10-09 14:06:58', '2021-10-09 14:06:58'),
(17, 'Marvel\\Database\\Models\\Attachment', 17, '7e42159e-b818-494c-9372-a3a8188f1a67', 'default', 'chawkb.sports', 'chawkb.sports.png', 'image/png', 's3', 's3', 101358, '[]', '{\"thumbnail\": true}', '[]', '[]', 17, '2021-10-09 14:17:42', '2021-10-09 14:17:42'),
(18, 'Marvel\\Database\\Models\\Attachment', 18, '927ff16a-c484-4699-8ebb-cbc20f3c8f63', 'default', 'chawkbwomen', 'chawkbwomen.jpg', 'image/jpeg', 's3', 's3', 114127, '[]', '{\"thumbnail\": true}', '[]', '[]', 18, '2021-10-09 14:18:48', '2021-10-09 14:18:48'),
(19, 'Marvel\\Database\\Models\\Attachment', 19, '6199d122-7ce6-4478-9a71-1c7d939645d9', 'default', 'chawkbwomen', 'chawkbwomen.jpg', 'image/jpeg', 's3', 's3', 114127, '[]', '{\"thumbnail\": true}', '[]', '[]', 19, '2021-10-09 14:20:47', '2021-10-09 14:20:47'),
(20, 'Marvel\\Database\\Models\\Attachment', 20, '6a60c610-75a7-4ae1-bec0-25b772ffa136', 'default', 'chawkbsunglass', 'chawkbsunglass.jpg', 'image/jpeg', 's3', 's3', 128922, '[]', '{\"thumbnail\": true}', '[]', '[]', 20, '2021-10-09 14:21:10', '2021-10-09 14:21:10'),
(21, 'Marvel\\Database\\Models\\Attachment', 21, '2d7f9338-836c-4bb0-a1fa-974886e0009b', 'default', 'chawkbcoupond', 'chawkbcoupond.jpg', 'image/jpeg', 's3', 's3', 20338, '[]', '{\"thumbnail\": true}', '[]', '[]', 21, '2021-10-09 14:21:30', '2021-10-09 14:21:30'),
(22, 'Marvel\\Database\\Models\\Attachment', 22, 'd1c86cb2-eb2f-4906-8d41-ea4c4fbaba88', 'default', 'chawkbbackpack', 'chawkbbackpack.jpg', 'image/jpeg', 's3', 's3', 288239, '[]', '{\"thumbnail\": true}', '[]', '[]', 22, '2021-10-09 14:21:55', '2021-10-09 14:21:56'),
(23, 'Marvel\\Database\\Models\\Attachment', 23, '3cc168c2-fb34-4f6f-a4bf-fff2406f7fa2', 'default', 'chawkbwomen', 'chawkbwomen.jpg', 'image/jpeg', 's3', 's3', 114127, '[]', '{\"thumbnail\": true}', '[]', '[]', 23, '2021-10-09 14:22:15', '2021-10-09 14:22:15'),
(24, 'Marvel\\Database\\Models\\Attachment', 24, 'f728ce68-3756-4bbe-8e8a-85b18249e344', 'default', 'Banner 5', 'Banner-5.png', 'image/png', 's3', 's3', 117129, '[]', '{\"thumbnail\": true}', '[]', '[]', 24, '2021-10-09 14:23:13', '2021-10-09 14:23:13'),
(25, 'Marvel\\Database\\Models\\Attachment', 25, 'db35c9bc-7bda-4c87-b29c-d48bf55eb6c3', 'default', 'Banner 5', 'Banner-5.png', 'image/png', 's3', 's3', 117129, '[]', '{\"thumbnail\": true}', '[]', '[]', 25, '2021-10-09 14:23:58', '2021-10-09 14:23:58'),
(26, 'Marvel\\Database\\Models\\Attachment', 26, 'af11c038-ae8c-40f1-a9be-61e51d3b78b5', 'default', 'bags', 'bags.png', 'image/png', 's3', 's3', 17083, '[]', '{\"thumbnail\": true}', '[]', '[]', 26, '2021-10-09 14:37:38', '2021-10-09 14:37:38'),
(27, 'Marvel\\Database\\Models\\Attachment', 27, 'e5eb0193-b2d2-4f72-ab33-7a708b647d58', 'default', 'kids', 'kids.png', 'image/png', 's3', 's3', 13277, '[]', '{\"thumbnail\": true}', '[]', '[]', 27, '2021-10-09 14:38:22', '2021-10-09 14:38:23'),
(28, 'Marvel\\Database\\Models\\Attachment', 28, '6ee925f5-b541-4428-b74f-709569dc9442', 'default', 'men', 'men.png', 'image/png', 's3', 's3', 15598, '[]', '{\"thumbnail\": true}', '[]', '[]', 28, '2021-10-09 14:38:34', '2021-10-09 14:38:34'),
(29, 'Marvel\\Database\\Models\\Attachment', 29, '349b57bb-5cb3-4cdc-acc8-c04710bdc3ba', 'default', 'sneekers', 'sneekers.png', 'image/png', 's3', 's3', 17150, '[]', '{\"thumbnail\": true}', '[]', '[]', 29, '2021-10-09 14:38:47', '2021-10-09 14:38:47'),
(30, 'Marvel\\Database\\Models\\Attachment', 30, 'b76fee2e-0d2a-4a68-b890-619976710eff', 'default', 'sports', 'sports.png', 'image/png', 's3', 's3', 23982, '[]', '{\"thumbnail\": true}', '[]', '[]', 30, '2021-10-09 14:40:03', '2021-10-09 14:40:03'),
(31, 'Marvel\\Database\\Models\\Attachment', 31, '9778d275-3c18-4431-b93d-0d14591945d8', 'default', 'sunglass', 'sunglass.png', 'image/png', 's3', 's3', 17982, '[]', '{\"thumbnail\": true}', '[]', '[]', 31, '2021-10-09 14:40:14', '2021-10-09 14:40:14'),
(32, 'Marvel\\Database\\Models\\Attachment', 32, '7540b424-021c-42ad-a368-e5d58524b156', 'default', 'watch', 'watch.png', 'image/png', 's3', 's3', 15779, '[]', '{\"thumbnail\": true}', '[]', '[]', 32, '2021-10-09 14:40:27', '2021-10-09 14:40:27'),
(33, 'Marvel\\Database\\Models\\Attachment', 33, '271f84bc-3ff4-4438-bb7a-741808322635', 'default', 'women', 'women.png', 'image/png', 's3', 's3', 22297, '[]', '{\"thumbnail\": true}', '[]', '[]', 33, '2021-10-09 14:40:40', '2021-10-09 14:40:40'),
(34, 'Marvel\\Database\\Models\\Attachment', 34, '66f9cf31-a969-40cd-8f51-4c9364f982a8', 'default', 'banner-mobile-3', 'banner-mobile-3.jpg', 'image/jpeg', 's3', 's3', 36290, '[]', '{\"thumbnail\": true}', '[]', '[]', 34, '2021-10-09 14:52:47', '2021-10-09 14:52:47'),
(35, 'Marvel\\Database\\Models\\Attachment', 35, '62532294-7ea1-4667-a949-59f40e5ce1ba', 'default', 'banner-1', 'banner-1.jpg', 'image/jpeg', 's3', 's3', 285505, '[]', '{\"thumbnail\": true}', '[]', '[]', 35, '2021-10-09 14:53:33', '2021-10-09 14:53:33'),
(36, 'Marvel\\Database\\Models\\Attachment', 36, 'e3f26c8d-c67c-46da-bf79-484cd0242510', 'default', 'banner-2', 'banner-2.jpg', 'image/jpeg', 's3', 's3', 236636, '[]', '{\"thumbnail\": true}', '[]', '[]', 36, '2021-10-09 14:53:48', '2021-10-09 14:53:49'),
(37, 'Marvel\\Database\\Models\\Attachment', 37, 'c1298ab1-4ddc-4444-9d64-59834437bb9e', 'default', 'banner-3', 'banner-3.jpg', 'image/jpeg', 's3', 's3', 19855, '[]', '{\"thumbnail\": true}', '[]', '[]', 37, '2021-10-09 14:54:17', '2021-10-09 14:54:17'),
(38, 'Marvel\\Database\\Models\\Attachment', 38, '89d69fff-3aae-4c3b-9a24-bd86e2e95c84', 'default', '1', '1.jpg', 'image/jpeg', 's3', 's3', 136581, '[]', '{\"thumbnail\": true}', '[]', '[]', 38, '2021-10-09 14:55:41', '2021-10-09 14:55:41'),
(39, 'Marvel\\Database\\Models\\Attachment', 39, '5b6f6a01-967c-4826-85f3-cc4a6566ecec', 'default', 'AE', 'AE.svg', 'image/svg', 's3', 's3', 2535, '[]', '[]', '[]', '[]', 39, '2021-10-10 10:21:22', '2021-10-10 10:21:22'),
(40, 'Marvel\\Database\\Models\\Attachment', 40, '0d55874e-5a2a-4dbf-bb36-822046aa3c52', 'default', 'logo1', 'logo1.png', 'image/png', 's3', 's3', 4125, '[]', '{\"thumbnail\": true}', '[]', '[]', 40, '2021-10-10 10:21:22', '2021-10-10 10:21:22'),
(41, 'Marvel\\Database\\Models\\Attachment', 41, '608a5395-25eb-41ae-a54a-ef6c41d7aa63', 'default', 'adidas', 'adidas.png', 'image/png', 's3', 's3', 73906, '[]', '{\"thumbnail\": true}', '[]', '[]', 41, '2021-10-10 10:30:48', '2021-10-10 10:30:49'),
(42, 'Marvel\\Database\\Models\\Attachment', 42, 'e5cc1862-9fbd-4a5d-b56e-0f3b2d6676ef', 'default', 'fustion', 'fustion.png', 'image/png', 's3', 's3', 4125, '[]', '{\"thumbnail\": true}', '[]', '[]', 42, '2021-10-10 10:31:51', '2021-10-10 10:31:51'),
(43, 'Marvel\\Database\\Models\\Attachment', 43, '1b0f48a3-6d99-4342-9feb-4d06b84589c1', 'default', 'puma-logo', 'puma-logo.png', 'image/png', 's3', 's3', 47631, '[]', '{\"thumbnail\": true}', '[]', '[]', 43, '2021-10-10 10:50:07', '2021-10-10 10:50:07'),
(44, 'Marvel\\Database\\Models\\Attachment', 44, 'de5506a5-fcca-46b0-8244-3280a0a32a22', 'default', 'vintege', 'vintege.png', 'image/png', 's3', 's3', 4600, '[]', '{\"thumbnail\": true}', '[]', '[]', 44, '2021-10-10 10:50:11', '2021-10-10 10:50:11'),
(45, 'Marvel\\Database\\Models\\Attachment', 45, '9faab4b4-a0a4-48f8-ac60-8373984bf337', 'default', 'dior', 'dior.png', 'image/png', 's3', 's3', 83177, '[]', '{\"thumbnail\": true}', '[]', '[]', 45, '2021-10-10 10:50:50', '2021-10-10 10:50:50'),
(46, 'Marvel\\Database\\Models\\Attachment', 46, '10b0b8a0-c40c-405d-aed1-07dc7ce07d35', 'default', 'logo3', 'logo3.png', 'image/png', 's3', 's3', 7335, '[]', '{\"thumbnail\": true}', '[]', '[]', 46, '2021-10-10 10:50:55', '2021-10-10 10:50:55'),
(47, 'Marvel\\Database\\Models\\Attachment', 47, 'f48b13f5-2866-4844-a4ee-03cdd05aece7', 'default', 'levi-s', 'levi-s.png', 'image/png', 's3', 's3', 104675, '[]', '{\"thumbnail\": true}', '[]', '[]', 47, '2021-10-10 10:51:22', '2021-10-10 10:51:23'),
(48, 'Marvel\\Database\\Models\\Attachment', 48, 'e776c383-e0e3-493a-b06c-42eb188f612f', 'default', 'logo4', 'logo4.png', 'image/png', 's3', 's3', 6376, '[]', '{\"thumbnail\": true}', '[]', '[]', 48, '2021-10-10 10:51:29', '2021-10-10 10:51:29'),
(49, 'Marvel\\Database\\Models\\Attachment', 49, '6d5b12bc-1f24-419d-ac51-903eed99e841', 'default', 'logo5', 'logo5.png', 'image/png', 's3', 's3', 4692, '[]', '{\"thumbnail\": true}', '[]', '[]', 49, '2021-10-10 10:55:12', '2021-10-10 10:55:12'),
(50, 'Marvel\\Database\\Models\\Attachment', 50, 'a96d0581-8ba7-40cc-a9b9-fea407dd8f59', 'default', 'Calvin klein', 'Calvin-klein.png', 'image/png', 's3', 's3', 110247, '[]', '{\"thumbnail\": true}', '[]', '[]', 50, '2021-10-10 10:55:43', '2021-10-10 10:55:43'),
(51, 'Marvel\\Database\\Models\\Attachment', 51, 'b3cdd16b-c382-488f-8635-f3f3d3979c63', 'default', 'logo5', 'logo5.png', 'image/png', 's3', 's3', 4692, '[]', '{\"thumbnail\": true}', '[]', '[]', 51, '2021-10-10 10:55:56', '2021-10-10 10:55:56'),
(52, 'Marvel\\Database\\Models\\Attachment', 52, 'dfcb6825-6d31-44e2-a3ff-b3e475fbd3b6', 'default', 'Calvin klein', 'Calvin-klein.png', 'image/png', 's3', 's3', 110247, '[]', '{\"thumbnail\": true}', '[]', '[]', 52, '2021-10-10 10:56:02', '2021-10-10 10:56:02'),
(53, 'Marvel\\Database\\Models\\Attachment', 53, '9ca7e26d-b715-4845-b0a8-a4a9ada90625', 'default', 'Calvin klein', 'Calvin-klein.png', 'image/png', 's3', 's3', 110247, '[]', '{\"thumbnail\": true}', '[]', '[]', 53, '2021-10-10 10:56:19', '2021-10-10 10:56:19'),
(54, 'Marvel\\Database\\Models\\Attachment', 54, '7469bda5-d785-43f7-9d40-a5d5ce049c14', 'default', 'logo5', 'logo5.png', 'image/png', 's3', 's3', 4692, '[]', '{\"thumbnail\": true}', '[]', '[]', 54, '2021-10-10 10:56:51', '2021-10-10 10:56:52'),
(55, 'Marvel\\Database\\Models\\Attachment', 55, '65a7fa04-9c69-4fdb-8d98-67215cc0af6d', 'default', 'tissot', 'tissot.png', 'image/png', 's3', 's3', 82906, '[]', '{\"thumbnail\": true}', '[]', '[]', 55, '2021-10-10 10:57:50', '2021-10-10 10:57:51'),
(56, 'Marvel\\Database\\Models\\Attachment', 56, '20d3eca3-8fba-4f1a-ac9b-39d1b25c9c1a', 'default', 'logo6', 'logo6.png', 'image/png', 's3', 's3', 10811, '[]', '{\"thumbnail\": true}', '[]', '[]', 56, '2021-10-10 10:57:55', '2021-10-10 10:57:56'),
(57, 'Marvel\\Database\\Models\\Attachment', 57, '35a870be-f8af-4ade-8819-33a31268bd31', 'default', 'nike', 'nike.png', 'image/png', 's3', 's3', 77110, '[]', '{\"thumbnail\": true}', '[]', '[]', 57, '2021-10-10 10:58:24', '2021-10-10 10:58:25'),
(58, 'Marvel\\Database\\Models\\Attachment', 58, '6a9f78b2-b30c-4fb9-950d-2d6232e1c09e', 'default', 'logo7', 'logo7.png', 'image/png', 's3', 's3', 6010, '[]', '{\"thumbnail\": true}', '[]', '[]', 58, '2021-10-10 10:58:29', '2021-10-10 10:58:29'),
(59, 'Marvel\\Database\\Models\\Attachment', 59, '711d928c-6628-44dd-a5ce-482166cfeafc', 'default', 'herschel', 'herschel.png', 'image/png', 's3', 's3', 109736, '[]', '{\"thumbnail\": true}', '[]', '[]', 59, '2021-10-10 10:58:54', '2021-10-10 10:58:54'),
(60, 'Marvel\\Database\\Models\\Attachment', 60, '1251386a-c992-4dd7-9c8c-a8841ffde2a8', 'default', 'logo8', 'logo8.png', 'image/png', 's3', 's3', 7658, '[]', '{\"thumbnail\": true}', '[]', '[]', 60, '2021-10-10 10:59:02', '2021-10-10 10:59:02'),
(61, 'Marvel\\Database\\Models\\Attachment', 61, '0543c3db-f0f0-4a52-9d48-431b59f59ecd', 'default', 'Hollister', 'Hollister.png', 'image/png', 's3', 's3', 98152, '[]', '{\"thumbnail\": true}', '[]', '[]', 61, '2021-10-10 10:59:24', '2021-10-10 10:59:24'),
(62, 'Marvel\\Database\\Models\\Attachment', 62, 'd67f9206-f67a-4d1a-89f0-5d5b5189102c', 'default', 'logo4', 'logo4.png', 'image/png', 's3', 's3', 6376, '[]', '{\"thumbnail\": true}', '[]', '[]', 62, '2021-10-10 10:59:29', '2021-10-10 10:59:29'),
(63, 'Marvel\\Database\\Models\\Attachment', 63, 'ba74fec0-a40c-464e-81d7-afce1ca6f8f8', 'default', 'zara', 'zara.png', 'image/png', 's3', 's3', 129667, '[]', '{\"thumbnail\": true}', '[]', '[]', 63, '2021-10-10 11:00:05', '2021-10-10 11:00:05'),
(64, 'Marvel\\Database\\Models\\Attachment', 64, '3b19edee-d781-4be3-9a43-cc56414970e7', 'default', 'logo10', 'logo10.png', 'image/png', 's3', 's3', 3917, '[]', '{\"thumbnail\": true}', '[]', '[]', 64, '2021-10-10 11:00:17', '2021-10-10 11:00:18'),
(65, 'Marvel\\Database\\Models\\Attachment', 65, '72056399-9e25-456c-9f89-97b3a4e9b3d7', 'default', 'gucci', 'gucci.png', 'image/png', 's3', 's3', 100904, '[]', '{\"thumbnail\": true}', '[]', '[]', 65, '2021-10-10 11:01:05', '2021-10-10 11:01:05'),
(66, 'Marvel\\Database\\Models\\Attachment', 66, '5fdff9e8-ec9c-445d-af37-f9d05407d455', 'default', 'gucci', 'gucci.png', 'image/png', 's3', 's3', 100904, '[]', '{\"thumbnail\": true}', '[]', '[]', 66, '2021-10-10 11:01:07', '2021-10-10 11:01:07'),
(67, 'Marvel\\Database\\Models\\Attachment', 67, '8af00cdc-c08f-4cba-8870-8705eb014898', 'default', 'logo11', 'logo11.png', 'image/png', 's3', 's3', 4064, '[]', '{\"thumbnail\": true}', '[]', '[]', 67, '2021-10-10 11:01:32', '2021-10-10 11:01:32'),
(68, 'Marvel\\Database\\Models\\Attachment', 68, '56ff1b9d-ea9a-4846-966e-5805f698fcc4', 'default', 'under-armour', 'under-armour.png', 'image/png', 's3', 's3', 74071, '[]', '{\"thumbnail\": true}', '[]', '[]', 68, '2021-10-10 11:01:50', '2021-10-10 11:01:50'),
(69, 'Marvel\\Database\\Models\\Attachment', 69, '9e94397a-a717-49ca-810a-6eda82bc5f94', 'default', 'logo12', 'logo12.png', 'image/png', 's3', 's3', 5089, '[]', '{\"thumbnail\": true}', '[]', '[]', 69, '2021-10-10 11:01:55', '2021-10-10 11:01:55'),
(70, 'Marvel\\Database\\Models\\Attachment', 70, '0086ffa4-bdc7-4646-9dc7-46ff312a136a', 'default', 'emporio-armani', 'emporio-armani.png', 'image/png', 's3', 's3', 92425, '[]', '{\"thumbnail\": true}', '[]', '[]', 70, '2021-10-10 11:02:19', '2021-10-10 11:02:19'),
(71, 'Marvel\\Database\\Models\\Attachment', 71, '96bf72d7-c5ba-418e-a793-ea72c99f2d50', 'default', 'vintege', 'vintege.png', 'image/png', 's3', 's3', 4600, '[]', '{\"thumbnail\": true}', '[]', '[]', 71, '2021-10-10 11:02:52', '2021-10-10 11:02:52'),
(72, 'Marvel\\Database\\Models\\Attachment', 72, 'e244d6c4-759e-4472-813c-d4f271a49a09', 'default', 'converse', 'converse.png', 'image/png', 's3', 's3', 61787, '[]', '{\"thumbnail\": true}', '[]', '[]', 72, '2021-10-10 11:03:30', '2021-10-10 11:03:30'),
(73, 'Marvel\\Database\\Models\\Attachment', 73, '0ec4564d-c540-4b62-859c-34888839e428', 'default', 'logo14', 'logo14.png', 'image/png', 's3', 's3', 8038, '[]', '{\"thumbnail\": true}', '[]', '[]', 73, '2021-10-10 11:03:36', '2021-10-10 11:03:36'),
(74, 'Marvel\\Database\\Models\\Attachment', 74, '1861e056-cd2b-4a16-b493-36279e6a28cb', 'default', 'ray-ban', 'ray-ban.png', 'image/png', 's3', 's3', 78389, '[]', '{\"thumbnail\": true}', '[]', '[]', 74, '2021-10-10 11:03:56', '2021-10-10 11:03:57'),
(75, 'Marvel\\Database\\Models\\Attachment', 75, 'd329c582-d24c-4af6-b7f1-90801e667af0', 'default', 'logo15', 'logo15.png', 'image/png', 's3', 's3', 3488, '[]', '{\"thumbnail\": true}', '[]', '[]', 75, '2021-10-10 11:04:00', '2021-10-10 11:04:00'),
(76, 'Marvel\\Database\\Models\\Attachment', 76, 'f22a67df-2a5f-46cc-92d8-c0c74892da63', 'default', 'h&m', 'h&m.png', 'image/png', 's3', 's3', 59308, '[]', '{\"thumbnail\": true}', '[]', '[]', 76, '2021-10-10 11:04:23', '2021-10-10 11:04:23'),
(77, 'Marvel\\Database\\Models\\Attachment', 77, '5ed77ecf-34a1-4727-996d-563ccb4fb250', 'default', 'logo16', 'logo16.png', 'image/png', 's3', 's3', 6871, '[]', '{\"thumbnail\": true}', '[]', '[]', 77, '2021-10-10 11:04:27', '2021-10-10 11:04:27'),
(78, 'Marvel\\Database\\Models\\Attachment', 78, '15530b5d-d84e-41e2-9e24-3eeaeaef728f', 'default', 'p-26-m', 'p-26-m.png', 'image/png', 's3', 's3', 22906, '[]', '{\"thumbnail\": true}', '[]', '[]', 78, '2021-10-10 12:19:50', '2021-10-10 12:19:50'),
(79, 'Marvel\\Database\\Models\\Attachment', 79, '869f67af-5ec4-44fe-a1e0-cab12b915f26', 'default', 'D-2', 'D-2.png', 'image/png', 's3', 's3', 151542, '[]', '{\"thumbnail\": true}', '[]', '[]', 79, '2021-10-10 15:43:39', '2021-10-10 15:43:39'),
(80, 'Marvel\\Database\\Models\\Attachment', 80, '12361c42-2478-4073-8347-8c1ab876bfba', 'default', 'G-1', 'G-1.png', 'image/png', 's3', 's3', 69567, '[]', '{\"thumbnail\": true}', '[]', '[]', 80, '2021-10-10 15:44:46', '2021-10-10 15:44:47'),
(81, 'Marvel\\Database\\Models\\Attachment', 81, '1ab8991e-e68d-4615-a806-c8f6fdac3707', 'default', 'G', 'G.png', 'image/png', 's3', 's3', 69567, '[]', '{\"thumbnail\": true}', '[]', '[]', 81, '2021-10-10 15:44:54', '2021-10-10 15:44:54'),
(82, 'Marvel\\Database\\Models\\Attachment', 82, 'de4b118a-1406-47eb-906d-ceaece7faa00', 'default', 'G-1', 'G-1.png', 'image/png', 's3', 's3', 69567, '[]', '{\"thumbnail\": true}', '[]', '[]', 82, '2021-10-10 15:44:54', '2021-10-10 15:44:54'),
(83, 'Marvel\\Database\\Models\\Attachment', 83, 'e0eeb04c-6704-41a8-989e-1debb35c0058', 'default', 'D', 'D.png', 'image/png', 's3', 's3', 189090, '[]', '{\"thumbnail\": true}', '[]', '[]', 83, '2021-10-10 15:45:30', '2021-10-10 15:45:31'),
(84, 'Marvel\\Database\\Models\\Attachment', 84, '7cc699f5-3fbb-43a0-a4bb-2951f942d1b9', 'default', 'D-1', 'D-1.png', 'image/png', 's3', 's3', 178508, '[]', '{\"thumbnail\": true}', '[]', '[]', 84, '2021-10-10 15:45:31', '2021-10-10 15:45:31'),
(85, 'Marvel\\Database\\Models\\Attachment', 85, '72c8b1ef-60d7-4750-9bb8-dec8e617c028', 'default', 'D', 'D.png', 'image/png', 's3', 's3', 189090, '[]', '{\"thumbnail\": true}', '[]', '[]', 85, '2021-10-10 15:49:03', '2021-10-10 15:49:03'),
(86, 'Marvel\\Database\\Models\\Attachment', 86, '9132d68e-df1c-438a-939d-56b01b26302e', 'default', 'D-1', 'D-1.png', 'image/png', 's3', 's3', 178508, '[]', '{\"thumbnail\": true}', '[]', '[]', 86, '2021-10-10 15:49:03', '2021-10-10 15:49:04'),
(87, 'Marvel\\Database\\Models\\Attachment', 87, '4f455d77-6e4d-4467-95ec-3656e50ea9ce', 'default', 'D-2', 'D-2.png', 'image/png', 's3', 's3', 151542, '[]', '{\"thumbnail\": true}', '[]', '[]', 87, '2021-10-10 15:49:24', '2021-10-10 15:49:24'),
(88, 'Marvel\\Database\\Models\\Attachment', 88, 'e2a9926a-2004-4da7-8af7-2223454b4293', 'default', 'D-3', 'D-3.png', 'image/png', 's3', 's3', 213392, '[]', '{\"thumbnail\": true}', '[]', '[]', 88, '2021-10-10 15:49:24', '2021-10-10 15:49:25'),
(89, 'Marvel\\Database\\Models\\Attachment', 89, 'd0ddad95-df13-41d1-91ca-16e8a1924dd6', 'default', 'D', 'D.png', 'image/png', 's3', 's3', 189090, '[]', '{\"thumbnail\": true}', '[]', '[]', 89, '2021-10-10 15:50:35', '2021-10-10 15:50:35'),
(90, 'Marvel\\Database\\Models\\Attachment', 90, '29504fee-00d3-41d1-b54c-a7896036cd3c', 'default', 'D-1', 'D-1.png', 'image/png', 's3', 's3', 178508, '[]', '{\"thumbnail\": true}', '[]', '[]', 90, '2021-10-10 15:50:35', '2021-10-10 15:50:35'),
(91, 'Marvel\\Database\\Models\\Attachment', 91, 'b2d40ecc-cac2-40e1-b3e7-e68746cf929f', 'default', 'D-2', 'D-2.png', 'image/png', 's3', 's3', 151542, '[]', '{\"thumbnail\": true}', '[]', '[]', 91, '2021-10-10 15:50:35', '2021-10-10 15:50:36'),
(92, 'Marvel\\Database\\Models\\Attachment', 92, 'd49a725a-67f0-405f-be1d-d00c6cb02245', 'default', 'D-3', 'D-3.png', 'image/png', 's3', 's3', 213392, '[]', '{\"thumbnail\": true}', '[]', '[]', 92, '2021-10-10 15:50:36', '2021-10-10 15:50:36'),
(93, 'Marvel\\Database\\Models\\Attachment', 93, 'ddad1e89-a521-4ff5-a610-318f1f740af1', 'default', 'D', 'D.png', 'image/png', 's3', 's3', 189090, '[]', '{\"thumbnail\": true}', '[]', '[]', 93, '2021-10-10 15:50:40', '2021-10-10 15:50:40'),
(94, 'Marvel\\Database\\Models\\Attachment', 94, 'df5343c2-2961-45f0-a9e6-ea7a36efcf9b', 'default', 'D-1', 'D-1.png', 'image/png', 's3', 's3', 178508, '[]', '{\"thumbnail\": true}', '[]', '[]', 94, '2021-10-10 15:50:40', '2021-10-10 15:50:40'),
(95, 'Marvel\\Database\\Models\\Attachment', 95, '41b6b53b-13c4-4877-a183-6452c3340832', 'default', 'D-2', 'D-2.png', 'image/png', 's3', 's3', 151542, '[]', '{\"thumbnail\": true}', '[]', '[]', 95, '2021-10-10 15:50:55', '2021-10-10 15:50:55'),
(96, 'Marvel\\Database\\Models\\Attachment', 96, 'c707c080-a830-4b4f-8279-c461cf1a2e9e', 'default', 'D-3', 'D-3.png', 'image/png', 's3', 's3', 213392, '[]', '{\"thumbnail\": true}', '[]', '[]', 96, '2021-10-10 15:50:55', '2021-10-10 15:50:56'),
(97, 'Marvel\\Database\\Models\\Attachment', 97, '3d40f25f-448e-42f1-80c5-fa7ebe8860f2', 'default', 'A-2', 'A-2.png', 'image/png', 's3', 's3', 117712, '[]', '{\"thumbnail\": true}', '[]', '[]', 97, '2021-10-10 15:52:02', '2021-10-10 15:52:02'),
(98, 'Marvel\\Database\\Models\\Attachment', 98, '33cd1b35-135a-4fde-9908-04adf8a6a2e9', 'default', 'A', 'A.png', 'image/png', 's3', 's3', 104357, '[]', '{\"thumbnail\": true}', '[]', '[]', 98, '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(99, 'Marvel\\Database\\Models\\Attachment', 99, '7714241c-afb4-45b8-8109-13b7435b42cf', 'default', 'A-1', 'A-1.png', 'image/png', 's3', 's3', 79807, '[]', '{\"thumbnail\": true}', '[]', '[]', 99, '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(100, 'Marvel\\Database\\Models\\Attachment', 100, 'c591c18c-c9ac-424b-b56e-fc81b4330b5a', 'default', 'A-2', 'A-2.png', 'image/png', 's3', 's3', 117712, '[]', '{\"thumbnail\": true}', '[]', '[]', 100, '2021-10-10 15:52:19', '2021-10-10 15:52:19'),
(101, 'Marvel\\Database\\Models\\Attachment', 101, '305c097d-5401-4409-aa8c-30d016e59b7f', 'default', 'A-3', 'A-3.png', 'image/png', 's3', 's3', 101611, '[]', '{\"thumbnail\": true}', '[]', '[]', 101, '2021-10-10 15:52:19', '2021-10-10 15:52:20'),
(102, 'Marvel\\Database\\Models\\Attachment', 102, '3d4466e0-b7ad-4ea1-a212-0b029f9da079', 'default', 'A', 'A.png', 'image/png', 's3', 's3', 104357, '[]', '{\"thumbnail\": true}', '[]', '[]', 102, '2021-10-10 15:52:23', '2021-10-10 15:52:24'),
(103, 'Marvel\\Database\\Models\\Attachment', 103, '6ef6d5d9-ce19-4301-aa4c-825c69cbe22b', 'default', 'A-1', 'A-1.png', 'image/png', 's3', 's3', 79807, '[]', '{\"thumbnail\": true}', '[]', '[]', 103, '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(104, 'Marvel\\Database\\Models\\Attachment', 104, '4c4e98bd-cc6d-4c02-951f-72bacfb4c25a', 'default', 'A-2', 'A-2.png', 'image/png', 's3', 's3', 117712, '[]', '{\"thumbnail\": true}', '[]', '[]', 104, '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(105, 'Marvel\\Database\\Models\\Attachment', 105, '93c7eabe-f0bd-490c-8ffc-30c8318463b1', 'default', 'A-3', 'A-3.png', 'image/png', 's3', 's3', 101611, '[]', '{\"thumbnail\": true}', '[]', '[]', 105, '2021-10-10 15:52:24', '2021-10-10 15:52:24'),
(106, 'Marvel\\Database\\Models\\Attachment', 106, 'd0709ed8-dd87-4d25-8bba-ee74080288ab', 'default', 'A-3', 'A-3.png', 'image/png', 's3', 's3', 101611, '[]', '{\"thumbnail\": true}', '[]', '[]', 106, '2021-10-10 15:53:31', '2021-10-10 15:53:31'),
(107, 'Marvel\\Database\\Models\\Attachment', 107, '49f4a7bb-2b39-4312-bc05-1ef4ab166999', 'default', 'H-1', 'H-1.png', 'image/png', 's3', 's3', 38786, '[]', '{\"thumbnail\": true}', '[]', '[]', 107, '2021-10-10 15:58:44', '2021-10-10 15:58:44'),
(108, 'Marvel\\Database\\Models\\Attachment', 108, '87327523-cb78-40c2-90ad-a61b36794fc2', 'default', 'H', 'H.png', 'image/png', 's3', 's3', 39402, '[]', '{\"thumbnail\": true}', '[]', '[]', 108, '2021-10-10 15:59:50', '2021-10-10 15:59:51'),
(109, 'Marvel\\Database\\Models\\Attachment', 109, '961053d3-6064-40c4-9017-ef91edbd8edf', 'default', 'H-1', 'H-1.png', 'image/png', 's3', 's3', 38786, '[]', '{\"thumbnail\": true}', '[]', '[]', 109, '2021-10-10 15:59:54', '2021-10-10 15:59:54'),
(110, 'Marvel\\Database\\Models\\Attachment', 110, '8c275923-8513-41bc-b833-cbb3886da543', 'default', 'B', 'B.png', 'image/png', 's3', 's3', 118665, '[]', '{\"thumbnail\": true}', '[]', '[]', 110, '2021-10-10 16:29:51', '2021-10-10 16:29:51'),
(111, 'Marvel\\Database\\Models\\Attachment', 111, '71bf4347-c3a1-4457-8246-bcbca0b9e67e', 'default', 'B', 'B.png', 'image/png', 's3', 's3', 118665, '[]', '{\"thumbnail\": true}', '[]', '[]', 111, '2021-10-10 16:30:04', '2021-10-10 16:30:04'),
(112, 'Marvel\\Database\\Models\\Attachment', 112, 'e626ee4e-4da5-4891-aba1-5ca67b26eef8', 'default', 'B-1', 'B-1.png', 'image/png', 's3', 's3', 153430, '[]', '{\"thumbnail\": true}', '[]', '[]', 112, '2021-10-10 16:30:04', '2021-10-10 16:30:05'),
(113, 'Marvel\\Database\\Models\\Attachment', 113, '5c2ccddc-7d06-4b7a-9ecd-8f5db0546dff', 'default', 'B-2', 'B-2.png', 'image/png', 's3', 's3', 121711, '[]', '{\"thumbnail\": true}', '[]', '[]', 113, '2021-10-10 16:30:05', '2021-10-10 16:30:05'),
(114, 'Marvel\\Database\\Models\\Attachment', 114, '9ef40531-fd4a-486d-b08a-174efd0f5d0c', 'default', 'B-3', 'B-3.png', 'image/png', 's3', 's3', 120153, '[]', '{\"thumbnail\": true}', '[]', '[]', 114, '2021-10-10 16:30:05', '2021-10-10 16:30:06'),
(115, 'Marvel\\Database\\Models\\Attachment', 115, '178dea41-d21f-4bcd-a399-269c46788521', 'default', 'B-3', 'B-3.png', 'image/png', 's3', 's3', 104577, '[]', '{\"thumbnail\": true}', '[]', '[]', 115, '2021-10-10 16:32:20', '2021-10-10 16:32:20'),
(116, 'Marvel\\Database\\Models\\Attachment', 116, '9d09bbec-1c37-49e5-bbac-2f09315293e4', 'default', 'B', 'B.png', 'image/png', 's3', 's3', 102702, '[]', '{\"thumbnail\": true}', '[]', '[]', 116, '2021-10-10 16:32:27', '2021-10-10 16:32:28'),
(117, 'Marvel\\Database\\Models\\Attachment', 117, '6d48343d-b764-4e56-9150-7b8731abf114', 'default', 'B-1', 'B-1.png', 'image/png', 's3', 's3', 85301, '[]', '{\"thumbnail\": true}', '[]', '[]', 117, '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(118, 'Marvel\\Database\\Models\\Attachment', 118, '6f0f9953-eb54-4160-9be2-713276524ce0', 'default', 'B-2', 'B-2.png', 'image/png', 's3', 's3', 103081, '[]', '{\"thumbnail\": true}', '[]', '[]', 118, '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(119, 'Marvel\\Database\\Models\\Attachment', 119, '41895638-01b3-440e-bc4a-307d61718c48', 'default', 'B-3', 'B-3.png', 'image/png', 's3', 's3', 104577, '[]', '{\"thumbnail\": true}', '[]', '[]', 119, '2021-10-10 16:32:28', '2021-10-10 16:32:28'),
(120, 'Marvel\\Database\\Models\\Attachment', 120, '531ae2a8-71dc-45a6-bacd-92b4c4656909', 'default', 'A', 'A.png', 'image/png', 's3', 's3', 166034, '[]', '{\"thumbnail\": true}', '[]', '[]', 120, '2021-10-10 16:36:45', '2021-10-10 16:36:45'),
(121, 'Marvel\\Database\\Models\\Attachment', 121, '73548aa6-1bf1-4848-a745-52eb8618f2ba', 'default', 'A', 'A.png', 'image/png', 's3', 's3', 166034, '[]', '{\"thumbnail\": true}', '[]', '[]', 121, '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(122, 'Marvel\\Database\\Models\\Attachment', 122, 'c12ec298-9abc-465d-b144-628e04fe0b23', 'default', 'A-1', 'A-1.png', 'image/png', 's3', 's3', 146188, '[]', '{\"thumbnail\": true}', '[]', '[]', 122, '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(123, 'Marvel\\Database\\Models\\Attachment', 123, 'e27b2f37-cf1b-48b7-b517-327b4eb2030f', 'default', 'A-2', 'A-2.png', 'image/png', 's3', 's3', 164163, '[]', '{\"thumbnail\": true}', '[]', '[]', 123, '2021-10-10 16:37:15', '2021-10-10 16:37:15'),
(124, 'Marvel\\Database\\Models\\Attachment', 124, 'ff804049-5467-4508-b25b-a2ee278f1251', 'default', 'A-3', 'A-3.png', 'image/png', 's3', 's3', 120151, '[]', '{\"thumbnail\": true}', '[]', '[]', 124, '2021-10-10 16:37:15', '2021-10-10 16:37:16'),
(125, 'Marvel\\Database\\Models\\Attachment', 125, '9ccc7380-9927-4300-8b6a-64e997105c7d', 'default', 'E- 1', 'E--1.png', 'image/png', 's3', 's3', 162494, '[]', '{\"thumbnail\": true}', '[]', '[]', 125, '2021-10-10 16:42:43', '2021-10-10 16:42:44'),
(126, 'Marvel\\Database\\Models\\Attachment', 126, 'c93c8aac-dbac-4fdd-b48c-c6f9911165e2', 'default', 'E- 1', 'E--1.png', 'image/png', 's3', 's3', 162494, '[]', '{\"thumbnail\": true}', '[]', '[]', 126, '2021-10-10 16:43:08', '2021-10-10 16:43:08'),
(127, 'Marvel\\Database\\Models\\Attachment', 127, '9d6388a9-1f31-491f-a79e-aea755a2a56e', 'default', 'E- 2', 'E--2.png', 'image/png', 's3', 's3', 205349, '[]', '{\"thumbnail\": true}', '[]', '[]', 127, '2021-10-10 16:43:08', '2021-10-10 16:43:09'),
(128, 'Marvel\\Database\\Models\\Attachment', 128, 'af9b9bce-b389-4516-9eb8-8cbb61ccf368', 'default', 'E- 3', 'E--3.png', 'image/png', 's3', 's3', 177260, '[]', '{\"thumbnail\": true}', '[]', '[]', 128, '2021-10-10 16:43:09', '2021-10-10 16:43:09'),
(129, 'Marvel\\Database\\Models\\Attachment', 129, '6b0327a0-19ec-44cc-adb3-06161cca79b2', 'default', 'E-4', 'E-4.png', 'image/png', 's3', 's3', 160022, '[]', '{\"thumbnail\": true}', '[]', '[]', 129, '2021-10-10 16:43:09', '2021-10-10 16:43:09'),
(130, 'Marvel\\Database\\Models\\Attachment', 130, '57b6f2a1-6607-4f4b-8237-30d970f68065', 'default', 'D - 2', 'D---2.png', 'image/png', 's3', 's3', 91374, '[]', '{\"thumbnail\": true}', '[]', '[]', 130, '2021-10-10 16:46:05', '2021-10-10 16:46:05'),
(131, 'Marvel\\Database\\Models\\Attachment', 131, 'a5d72d6d-4896-4370-a4cf-57327b14f218', 'default', 'D - 2', 'D---2.png', 'image/png', 's3', 's3', 91374, '[]', '{\"thumbnail\": true}', '[]', '[]', 131, '2021-10-10 16:46:15', '2021-10-10 16:46:15'),
(132, 'Marvel\\Database\\Models\\Attachment', 132, 'c97f534d-937c-4d27-a291-23e7a19add4e', 'default', 'D- 1', 'D--1.png', 'image/png', 's3', 's3', 91533, '[]', '{\"thumbnail\": true}', '[]', '[]', 132, '2021-10-10 16:46:15', '2021-10-10 16:46:15'),
(133, 'Marvel\\Database\\Models\\Attachment', 133, 'dd7de49d-ba2f-491c-84ef-e2f9cb213240', 'default', '2', '2.png', 'image/png', 's3', 's3', 26330, '[]', '{\"thumbnail\": true}', '[]', '[]', 133, '2021-10-10 16:48:37', '2021-10-10 16:48:37'),
(134, 'Marvel\\Database\\Models\\Attachment', 134, 'f23db25b-362e-44e5-baa6-7e9462f4f5c1', 'default', '2', '2.png', 'image/png', 's3', 's3', 26330, '[]', '{\"thumbnail\": true}', '[]', '[]', 134, '2021-10-10 16:48:42', '2021-10-10 16:48:42'),
(135, 'Marvel\\Database\\Models\\Attachment', 135, 'e842d324-768b-45b9-97d7-021bce197862', 'default', 'D', 'D.png', 'image/png', 's3', 's3', 22877, '[]', '{\"thumbnail\": true}', '[]', '[]', 135, '2021-10-10 16:50:52', '2021-10-10 16:50:52'),
(136, 'Marvel\\Database\\Models\\Attachment', 136, '17df9eff-02ef-49bf-b182-e98a9be4a2e8', 'default', 'D-1', 'D-1.png', 'image/png', 's3', 's3', 22694, '[]', '{\"thumbnail\": true}', '[]', '[]', 136, '2021-10-10 16:50:57', '2021-10-10 16:50:57'),
(137, 'Marvel\\Database\\Models\\Attachment', 137, '21d8fa83-6ba0-4168-af73-267c6b91b692', 'default', 'H-3', 'H-3.png', 'image/png', 's3', 's3', 86616, '[]', '{\"thumbnail\": true}', '[]', '[]', 137, '2021-10-10 18:48:59', '2021-10-10 18:48:59'),
(138, 'Marvel\\Database\\Models\\Attachment', 138, '0b0a88bb-2b4e-4cbe-8ba4-d8ecebf10d66', 'default', 'H', 'H.png', 'image/png', 's3', 's3', 86653, '[]', '{\"thumbnail\": true}', '[]', '[]', 138, '2021-10-10 18:49:10', '2021-10-10 18:49:11'),
(139, 'Marvel\\Database\\Models\\Attachment', 139, 'c22bb20b-099b-4078-9702-ecac84e1a1c6', 'default', 'H-1', 'H-1.png', 'image/png', 's3', 's3', 261785, '[]', '{\"thumbnail\": true}', '[]', '[]', 139, '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(140, 'Marvel\\Database\\Models\\Attachment', 140, 'ec1cec37-4b6b-4795-aaf5-972eb130e1f7', 'default', 'H-2', 'H-2.png', 'image/png', 's3', 's3', 165843, '[]', '{\"thumbnail\": true}', '[]', '[]', 140, '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(141, 'Marvel\\Database\\Models\\Attachment', 141, '7d75d1ec-d3e3-4b5c-bbd1-1718d267f001', 'default', 'H-3', 'H-3.png', 'image/png', 's3', 's3', 86616, '[]', '{\"thumbnail\": true}', '[]', '[]', 141, '2021-10-10 18:49:11', '2021-10-10 18:49:11'),
(142, 'Marvel\\Database\\Models\\Attachment', 142, 'c75200bf-ccf9-48b5-96cf-2edd72e24464', 'default', 'F', 'F.png', 'image/png', 's3', 's3', 66899, '[]', '{\"thumbnail\": true}', '[]', '[]', 142, '2021-10-11 10:13:35', '2021-10-11 10:13:35'),
(143, 'Marvel\\Database\\Models\\Attachment', 143, 'e17c0651-5709-42b6-b13f-17bdb1dc4b9a', 'default', 'F', 'F.png', 'image/png', 's3', 's3', 66899, '[]', '{\"thumbnail\": true}', '[]', '[]', 143, '2021-10-11 10:13:41', '2021-10-11 10:13:42'),
(144, 'Marvel\\Database\\Models\\Attachment', 144, '78ba177f-cd7f-4aae-825b-6de90de1d008', 'default', 'F-1', 'F-1.png', 'image/png', 's3', 's3', 67451, '[]', '{\"thumbnail\": true}', '[]', '[]', 144, '2021-10-11 10:13:42', '2021-10-11 10:13:42'),
(145, 'Marvel\\Database\\Models\\Attachment', 145, '52fe371c-2624-4a9d-adf7-1e01a41ee08e', 'default', 'E', 'E.png', 'image/png', 's3', 's3', 56941, '[]', '{\"thumbnail\": true}', '[]', '[]', 145, '2021-10-11 10:14:38', '2021-10-11 10:14:38'),
(146, 'Marvel\\Database\\Models\\Attachment', 146, '5917448d-544d-45b2-8739-6d06a35c2316', 'default', 'E', 'E.png', 'image/png', 's3', 's3', 56941, '[]', '{\"thumbnail\": true}', '[]', '[]', 146, '2021-10-11 10:14:44', '2021-10-11 10:14:45'),
(147, 'Marvel\\Database\\Models\\Attachment', 147, '0b1713cf-03be-4312-bc58-22779147ec26', 'default', 'E-1', 'E-1.png', 'image/png', 's3', 's3', 57301, '[]', '{\"thumbnail\": true}', '[]', '[]', 147, '2021-10-11 10:14:45', '2021-10-11 10:14:45'),
(148, 'Marvel\\Database\\Models\\Attachment', 148, '19fa3bda-2d92-4600-87e1-5d710833bed9', 'default', 'women5-1', 'women5-1.jpg', 'image/jpeg', 's3', 's3', 286630, '[]', '{\"thumbnail\": true}', '[]', '[]', 148, '2021-10-11 10:19:40', '2021-10-11 10:19:41'),
(149, 'Marvel\\Database\\Models\\Attachment', 149, 'cb7936a9-8aff-4bd0-be0f-ddca24952b14', 'default', 'women5-1', 'women5-1.jpg', 'image/jpeg', 's3', 's3', 286630, '[]', '{\"thumbnail\": true}', '[]', '[]', 149, '2021-10-11 10:19:55', '2021-10-11 10:19:56'),
(150, 'Marvel\\Database\\Models\\Attachment', 150, '69775278-bbf9-422b-bcd0-54ad15ec1b4e', 'default', 'women-14-1', 'women-14-1.jpg', 'image/jpeg', 's3', 's3', 286205, '[]', '{\"thumbnail\": true}', '[]', '[]', 150, '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(151, 'Marvel\\Database\\Models\\Attachment', 151, '011e560e-3d30-4335-8157-224da47efc2e', 'default', 'women-17-1', 'women-17-1.jpg', 'image/jpeg', 's3', 's3', 353669, '[]', '{\"thumbnail\": true}', '[]', '[]', 151, '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(152, 'Marvel\\Database\\Models\\Attachment', 152, 'c89619af-156a-446c-b08f-8436194ee3fd', 'default', 'women-18-1', 'women-18-1.jpg', 'image/jpeg', 's3', 's3', 237944, '[]', '{\"thumbnail\": true}', '[]', '[]', 152, '2021-10-11 10:19:56', '2021-10-11 10:19:56'),
(153, 'Marvel\\Database\\Models\\Attachment', 153, 'a9d67ba1-1e1e-497b-a04c-34fad7cb80a0', 'default', 'G', 'G.png', 'image/png', 's3', 's3', 162972, '[]', '{\"thumbnail\": true}', '[]', '[]', 153, '2021-10-11 10:54:44', '2021-10-11 10:54:45'),
(154, 'Marvel\\Database\\Models\\Attachment', 154, 'b311cd6b-115b-419c-9719-154784ed6329', 'default', 'G', 'G.png', 'image/png', 's3', 's3', 162972, '[]', '{\"thumbnail\": true}', '[]', '[]', 154, '2021-10-11 10:55:02', '2021-10-11 10:55:03'),
(155, 'Marvel\\Database\\Models\\Attachment', 155, '7c9efd28-91dc-456d-a613-8520f3a68c90', 'default', 'G-1', 'G-1.png', 'image/png', 's3', 's3', 215929, '[]', '{\"thumbnail\": true}', '[]', '[]', 155, '2021-10-11 10:55:03', '2021-10-11 10:55:03'),
(156, 'Marvel\\Database\\Models\\Attachment', 156, '521a542a-c06d-4daf-9db1-0bba5aaa6f34', 'default', 'G-2', 'G-2.png', 'image/png', 's3', 's3', 149888, '[]', '{\"thumbnail\": true}', '[]', '[]', 156, '2021-10-11 10:55:03', '2021-10-11 10:55:04'),
(157, 'Marvel\\Database\\Models\\Attachment', 157, 'c06354a2-25c8-4a0a-9b1c-e12f38a8e6e3', 'default', 'G-3', 'G-3.png', 'image/png', 's3', 's3', 203054, '[]', '{\"thumbnail\": true}', '[]', '[]', 157, '2021-10-11 10:55:04', '2021-10-11 10:55:04'),
(158, 'Marvel\\Database\\Models\\Attachment', 158, '9a142676-3c72-415f-96b8-1f67077fa807', 'default', 'Chawkbazar13', 'Chawkbazar13.png', 'image/png', 's3', 's3', 156222, '[]', '{\"thumbnail\": true}', '[]', '[]', 158, '2021-10-11 11:29:34', '2021-10-11 11:29:34'),
(159, 'Marvel\\Database\\Models\\Attachment', 159, 'ef324965-fbb0-49c9-a8fb-a3ae4bc6c8c9', 'default', 'Chawkbazar13', 'Chawkbazar13.png', 'image/png', 's3', 's3', 156222, '[]', '{\"thumbnail\": true}', '[]', '[]', 159, '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(160, 'Marvel\\Database\\Models\\Attachment', 160, '66d13efa-4692-435c-b9b7-3ae859db21b7', 'default', 'Chawkbazar14', 'Chawkbazar14.png', 'image/png', 's3', 's3', 174715, '[]', '{\"thumbnail\": true}', '[]', '[]', 160, '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(161, 'Marvel\\Database\\Models\\Attachment', 161, '766f65a9-fdc4-46ce-97d1-8c5daccce4af', 'default', 'Chawkbazar15', 'Chawkbazar15.png', 'image/png', 's3', 's3', 128366, '[]', '{\"thumbnail\": true}', '[]', '[]', 161, '2021-10-11 11:29:43', '2021-10-11 11:29:43'),
(162, 'Marvel\\Database\\Models\\Attachment', 162, '2012ce91-db37-4e01-b5a6-b687e0bbfa41', 'default', 'Chawkbazar16', 'Chawkbazar16.png', 'image/png', 's3', 's3', 142476, '[]', '{\"thumbnail\": true}', '[]', '[]', 162, '2021-10-11 11:29:43', '2021-10-11 11:29:44'),
(163, 'Marvel\\Database\\Models\\Attachment', 163, '07a52749-10e3-4f53-8f9e-fd0d9aa25f52', 'default', 'Watches-7', 'Watches-7.png', 'image/png', 's3', 's3', 41385, '[]', '{\"thumbnail\": true}', '[]', '[]', 163, '2021-10-11 11:32:08', '2021-10-11 11:32:08'),
(164, 'Marvel\\Database\\Models\\Attachment', 164, 'cfc7612f-faf0-41a3-84c1-34f97a612d2b', 'default', 'Watches-15', 'Watches-15.jpg', 'image/jpeg', 's3', 's3', 86243, '[]', '{\"thumbnail\": true}', '[]', '[]', 164, '2021-10-11 11:33:03', '2021-10-11 11:33:03'),
(165, 'Marvel\\Database\\Models\\Attachment', 165, '9314f01e-a225-4ed4-9697-1eba941d6875', 'default', 'Watches-10', 'Watches-10.jpg', 'image/jpeg', 's3', 's3', 87177, '[]', '{\"thumbnail\": true}', '[]', '[]', 165, '2021-10-11 11:36:34', '2021-10-11 11:36:34'),
(166, 'Marvel\\Database\\Models\\Attachment', 166, 'e8eb3a32-b126-4922-84ff-2990f1f16cae', 'default', 'Watches-1-1', 'Watches-1-1.jpg', 'image/jpeg', 's3', 's3', 87131, '[]', '{\"thumbnail\": true}', '[]', '[]', 166, '2021-10-11 11:36:39', '2021-10-11 11:36:39'),
(167, 'Marvel\\Database\\Models\\Attachment', 167, '0f052264-eabd-46b8-a1ff-530bb791f3a6', 'default', 'Women', 'Women.png', 'image/png', 's3', 's3', 174228, '[]', '{\"thumbnail\": true}', '[]', '[]', 167, '2021-10-11 11:38:13', '2021-10-11 11:38:14'),
(168, 'Marvel\\Database\\Models\\Attachment', 168, '7ad792af-cb18-4b99-8ff6-fa51459eb10c', 'default', 'Women-1', 'Women-1.png', 'image/png', 's3', 's3', 145531, '[]', '{\"thumbnail\": true}', '[]', '[]', 168, '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(169, 'Marvel\\Database\\Models\\Attachment', 169, 'b555c201-9fa5-401f-9a9b-feb1d21be4ee', 'default', 'Women-2', 'Women-2.png', 'image/png', 's3', 's3', 179435, '[]', '{\"thumbnail\": true}', '[]', '[]', 169, '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(170, 'Marvel\\Database\\Models\\Attachment', 170, '2a7729cc-5670-4061-8f7a-fef2140d67ab', 'default', 'Women-3', 'Women-3.png', 'image/png', 's3', 's3', 251358, '[]', '{\"thumbnail\": true}', '[]', '[]', 170, '2021-10-11 11:38:21', '2021-10-11 11:38:21'),
(171, 'Marvel\\Database\\Models\\Attachment', 171, 'ecb0f276-9e60-4bc4-95fd-73bf2068ed62', 'default', 'F-2', 'F-2.png', 'image/png', 's3', 's3', 159627, '[]', '{\"thumbnail\": true}', '[]', '[]', 171, '2021-10-11 11:40:03', '2021-10-11 11:40:04'),
(172, 'Marvel\\Database\\Models\\Attachment', 172, '9caff4b7-6680-4af3-b812-928ae7df54c7', 'default', 'F-3', 'F-3.png', 'image/png', 's3', 's3', 153376, '[]', '{\"thumbnail\": true}', '[]', '[]', 172, '2021-10-11 11:40:14', '2021-10-11 11:40:14'),
(173, 'Marvel\\Database\\Models\\Attachment', 173, '50109951-4dff-4a3c-80f4-2cdb47be573f', 'default', 'C', 'C.png', 'image/png', 's3', 's3', 105937, '[]', '{\"thumbnail\": true}', '[]', '[]', 173, '2021-10-11 11:41:33', '2021-10-11 11:41:33'),
(174, 'Marvel\\Database\\Models\\Attachment', 174, 'bd04a93e-a55b-4aea-aab1-d8d1456e4559', 'default', 'C-1', 'C-1.png', 'image/png', 's3', 's3', 116660, '[]', '{\"thumbnail\": true}', '[]', '[]', 174, '2021-10-11 11:42:02', '2021-10-11 11:42:03'),
(175, 'Marvel\\Database\\Models\\Attachment', 175, '44bd357f-a062-43fb-8600-b5841eaf2cce', 'default', 'C-2', 'C-2.png', 'image/png', 's3', 's3', 105490, '[]', '{\"thumbnail\": true}', '[]', '[]', 175, '2021-10-11 11:42:03', '2021-10-11 11:42:03'),
(176, 'Marvel\\Database\\Models\\Attachment', 176, '1ab95324-3b0f-4a81-8f66-56afcdc3d47e', 'default', 'C-3', 'C-3.png', 'image/png', 's3', 's3', 118729, '[]', '{\"thumbnail\": true}', '[]', '[]', 176, '2021-10-11 11:42:04', '2021-10-11 11:42:04'),
(177, 'Marvel\\Database\\Models\\Attachment', 177, 'b57d2efc-c994-412e-841c-503b7d8b2e2d', 'default', 'j', 'j.png', 'image/png', 's3', 's3', 89179, '[]', '{\"thumbnail\": true}', '[]', '[]', 177, '2021-10-11 11:42:51', '2021-10-11 11:42:51'),
(178, 'Marvel\\Database\\Models\\Attachment', 178, '38f2f007-d773-48f8-a371-479d4272f091', 'default', 'j-1', 'j-1.png', 'image/png', 's3', 's3', 88244, '[]', '{\"thumbnail\": true}', '[]', '[]', 178, '2021-10-11 11:42:54', '2021-10-11 11:42:54'),
(179, 'Marvel\\Database\\Models\\Attachment', 179, 'f0031f01-8b5e-45bc-9b8b-befb9014b05c', 'default', 'Backpack-4', 'Backpack-4.jpg', 'image/jpeg', 's3', 's3', 93976, '[]', '{\"thumbnail\": true}', '[]', '[]', 179, '2021-10-23 10:01:43', '2021-10-23 10:01:43'),
(180, 'Marvel\\Database\\Models\\Attachment', 180, 'e4f8b2c0-d951-4612-b04b-5dfbb504d192', 'default', 'Footwear-1-1', 'Footwear-1-1.jpg', 'image/jpeg', 's3', 's3', 85171, '[]', '{\"thumbnail\": true}', '[]', '[]', 180, '2021-10-23 10:09:07', '2021-10-23 10:09:08'),
(181, 'Marvel\\Database\\Models\\Attachment', 181, '256fb2ee-bb38-4448-baa6-d9bcb11ce003', 'default', 'Backpack-8', 'Backpack-8.jpg', 'image/jpeg', 's3', 's3', 151949, '[]', '{\"thumbnail\": true}', '[]', '[]', 181, '2021-10-23 10:45:54', '2021-10-23 10:45:54'),
(182, 'Marvel\\Database\\Models\\Attachment', 182, '9f8f223f-ff96-4f53-a2f3-6791b473e27b', 'default', 'Footwear-3-1', 'Footwear-3-1.jpg', 'image/jpeg', 's3', 's3', 90207, '[]', '{\"thumbnail\": true}', '[]', '[]', 182, '2021-10-23 10:48:31', '2021-10-23 10:48:32'),
(183, 'Marvel\\Database\\Models\\Attachment', 183, 'e3e61ab1-beeb-4864-88d8-0761110cfe70', 'default', 'Footwear-2-1', 'Footwear-2-1.jpg', 'image/jpeg', 's3', 's3', 90615, '[]', '{\"thumbnail\": true}', '[]', '[]', 183, '2021-10-23 10:48:49', '2021-10-23 10:48:49'),
(184, 'Marvel\\Database\\Models\\Attachment', 184, '1104a69d-cf6a-49b5-871e-5ca4fadca236', 'default', 'Sunglasess-12-1', 'Sunglasess-12-1.jpg', 'image/jpeg', 's3', 's3', 59210, '[]', '{\"thumbnail\": true}', '[]', '[]', 184, '2021-10-23 13:17:44', '2021-10-23 13:17:45'),
(185, 'Marvel\\Database\\Models\\Attachment', 185, '90ab13fb-c1e8-469b-8e55-240739462a8e', 'default', 'Sunglasess-13-1', 'Sunglasess-13-1.jpg', 'image/jpeg', 's3', 's3', 59066, '[]', '{\"thumbnail\": true}', '[]', '[]', 185, '2021-10-23 13:17:53', '2021-10-23 13:17:53'),
(186, 'Marvel\\Database\\Models\\Attachment', 186, '8f8ffe5c-9f6c-4075-9e65-f5dc861719e0', 'default', 'kids-11', 'kids-11.jpg', 'image/jpeg', 's3', 's3', 153687, '[]', '{\"thumbnail\": true}', '[]', '[]', 186, '2021-10-23 16:54:10', '2021-10-23 16:54:10'),
(187, 'Marvel\\Database\\Models\\Attachment', 187, 'c175bbf0-70e1-48d3-8d81-af1d578e783e', 'default', 'kids-25', 'kids-25.jpg', 'image/jpeg', 's3', 's3', 153697, '[]', '{\"thumbnail\": true}', '[]', '[]', 187, '2021-10-23 16:54:14', '2021-10-23 16:54:14'),
(188, 'Marvel\\Database\\Models\\Attachment', 188, '4ab097c0-ae5a-463f-8dea-c8eef04e8b42', 'default', 'Backpack-1', 'Backpack-1.jpg', 'image/jpeg', 's3', 's3', 148089, '[]', '{\"thumbnail\": true}', '[]', '[]', 188, '2021-10-23 17:12:44', '2021-10-23 17:12:44'),
(189, 'Marvel\\Database\\Models\\Attachment', 189, '3555ca4c-404c-4c01-ad15-28a8d3ae8b63', 'default', 'Footwear-4-1', 'Footwear-4-1.jpg', 'image/jpeg', 's3', 's3', 98624, '[]', '{\"thumbnail\": true}', '[]', '[]', 189, '2021-10-23 17:16:20', '2021-10-23 17:16:20'),
(190, 'Marvel\\Database\\Models\\Attachment', 190, '8631100b-06c4-4f73-9a77-50bc55899449', 'default', 'Footwear-5', 'Footwear-5.jpg', 'image/jpeg', 's3', 's3', 98618, '[]', '{\"thumbnail\": true}', '[]', '[]', 190, '2021-10-23 17:16:24', '2021-10-23 17:16:24'),
(191, 'Marvel\\Database\\Models\\Attachment', 191, '9bbac096-8ed4-4c74-9a4a-76ebfd942209', 'default', 'Backpack-5', 'Backpack-5.jpg', 'image/jpeg', 's3', 's3', 129164, '[]', '{\"thumbnail\": true}', '[]', '[]', 191, '2021-10-23 17:20:26', '2021-10-23 17:20:26'),
(192, 'Marvel\\Database\\Models\\Attachment', 192, '3bae879c-b735-42ff-a058-af2bd241f547', 'default', 'women9-1', 'women9-1.jpg', 'image/jpeg', 's3', 's3', 233916, '[]', '{\"thumbnail\": true}', '[]', '[]', 192, '2021-10-23 17:30:08', '2021-10-23 17:30:08'),
(193, 'Marvel\\Database\\Models\\Attachment', 193, '031a6b82-8829-474f-9140-2f6bb6f48ba5', 'default', 'women-22-1', 'women-22-1.jpg', 'image/jpeg', 's3', 's3', 234207, '[]', '{\"thumbnail\": true}', '[]', '[]', 193, '2021-10-23 17:30:11', '2021-10-23 17:30:12'),
(194, 'Marvel\\Database\\Models\\Attachment', 194, 'eebbb509-8bee-4134-b11d-7a0fc4f07a0b', 'default', 'women10@2x-1', 'women10@2x-1.jpg', 'image/jpeg', 's3', 's3', 440922, '[]', '{\"thumbnail\": true}', '[]', '[]', 194, '2021-10-23 18:09:12', '2021-10-23 18:09:12'),
(195, 'Marvel\\Database\\Models\\Attachment', 195, '55c7fe01-88c4-4e7e-af8a-dc604c2909ff', 'default', 'women-13-1', 'women-13-1.jpg', 'image/jpeg', 's3', 's3', 133361, '[]', '{\"thumbnail\": true}', '[]', '[]', 195, '2021-10-23 18:09:16', '2021-10-23 18:09:16'),
(196, 'Marvel\\Database\\Models\\Attachment', 196, '63cc9398-0709-409b-b5d8-b03cd4a0493f', 'default', 'kids-4', 'kids-4.jpg', 'image/jpeg', 's3', 's3', 176721, '[]', '{\"thumbnail\": true}', '[]', '[]', 196, '2021-10-23 18:11:48', '2021-10-23 18:11:48'),
(197, 'Marvel\\Database\\Models\\Attachment', 197, '267b5b3c-2ef7-4478-9e06-751c24a9a2bf', 'default', 'kids-5', 'kids-5.jpg', 'image/jpeg', 's3', 's3', 176416, '[]', '{\"thumbnail\": true}', '[]', '[]', 197, '2021-10-23 18:11:52', '2021-10-23 18:11:52'),
(198, 'Marvel\\Database\\Models\\Attachment', 198, 'd5bc0067-4f6b-4fbb-832e-fabe0d09ee54', 'default', 'Grid-14', 'Grid-14.png', 'image/png', 's3', 's3', 375894, '[]', '{\"thumbnail\": true}', '[]', '[]', 198, '2021-10-23 18:16:05', '2021-10-23 18:16:05'),
(199, 'Marvel\\Database\\Models\\Attachment', 199, '8b0cec88-2220-43bd-a9f6-5081687b2acc', 'default', 'Chawkbazar26', 'Chawkbazar26.png', 'image/png', 's3', 's3', 115114, '[]', '{\"thumbnail\": true}', '[]', '[]', 199, '2021-10-23 18:16:12', '2021-10-23 18:16:13');
INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `generated_conversions`, `custom_properties`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(200, 'Marvel\\Database\\Models\\Attachment', 200, 'f904de66-7d60-4626-991d-6f71b8d05ae5', 'default', 'Watches-16', 'Watches-16.jpg', 'image/jpeg', 's3', 's3', 72716, '[]', '{\"thumbnail\": true}', '[]', '[]', 200, '2021-10-23 18:18:22', '2021-10-23 18:18:22'),
(201, 'Marvel\\Database\\Models\\Attachment', 201, '80aa872c-5f79-43f7-a9a0-d3ed16f8bb6c', 'default', 'Watches-16', 'Watches-16.jpg', 'image/jpeg', 's3', 's3', 72716, '[]', '{\"thumbnail\": true}', '[]', '[]', 201, '2021-10-23 18:18:25', '2021-10-23 18:18:25'),
(202, 'Marvel\\Database\\Models\\Attachment', 202, 'bee26d39-8b1e-4eaf-b674-969c76ff6d07', 'default', 'kids-17', 'kids-17.jpg', 'image/jpeg', 's3', 's3', 142513, '[]', '{\"thumbnail\": true}', '[]', '[]', 202, '2021-10-23 18:20:07', '2021-10-23 18:20:07'),
(203, 'Marvel\\Database\\Models\\Attachment', 203, 'b92b00d6-04f8-473c-bfa9-d993f315c11b', 'default', 'kids-23', 'kids-23.jpg', 'image/jpeg', 's3', 's3', 142426, '[]', '{\"thumbnail\": true}', '[]', '[]', 203, '2021-10-23 18:20:11', '2021-10-23 18:20:11'),
(204, 'Marvel\\Database\\Models\\Attachment', 204, '6f405f94-db87-45d8-ab79-324f209b9006', 'default', 'Sunglasess-15-1', 'Sunglasess-15-1.jpg', 'image/jpeg', 's3', 's3', 49004, '[]', '{\"thumbnail\": true}', '[]', '[]', 204, '2021-10-23 18:22:08', '2021-10-23 18:22:08'),
(205, 'Marvel\\Database\\Models\\Attachment', 205, '85ec3a83-d8ce-4fec-a2c5-ebdc99380145', 'default', 'Sunglasess-5-1', 'Sunglasess-5-1.jpg', 'image/jpeg', 's3', 's3', 48174, '[]', '{\"thumbnail\": true}', '[]', '[]', 205, '2021-10-23 18:23:58', '2021-10-23 18:23:58'),
(206, 'Marvel\\Database\\Models\\Attachment', 206, '96197b45-d274-4c1e-abb7-2727a6a01493', 'default', 'Sunglasess-6', 'Sunglasess-6.jpg', 'image/jpeg', 's3', 's3', 48089, '[]', '{\"thumbnail\": true}', '[]', '[]', 206, '2021-10-23 18:24:02', '2021-10-23 18:24:03'),
(207, 'Marvel\\Database\\Models\\Attachment', 207, '650fc476-8595-4c22-998f-6a0fcf40b3b2', 'default', 'Chawkbazar13', 'Chawkbazar13.png', 'image/png', 's3', 's3', 156222, '[]', '{\"thumbnail\": true}', '[]', '[]', 207, '2021-10-23 18:27:11', '2021-10-23 18:27:11'),
(208, 'Marvel\\Database\\Models\\Attachment', 208, '905d702c-c700-4d4a-a0c0-9941964463d5', 'default', 'Chawkbazar14', 'Chawkbazar14.png', 'image/png', 's3', 's3', 174715, '[]', '{\"thumbnail\": true}', '[]', '[]', 208, '2021-10-23 18:27:14', '2021-10-23 18:27:14'),
(209, 'Marvel\\Database\\Models\\Attachment', 209, '8d1dacea-8dbd-42bf-9860-d43b1512bcbd', 'default', 'Footwear-17', 'Footwear-17.jpg', 'image/jpeg', 's3', 's3', 104214, '[]', '{\"thumbnail\": true}', '[]', '[]', 209, '2021-10-23 18:32:22', '2021-10-23 18:32:22'),
(210, 'Marvel\\Database\\Models\\Attachment', 210, '8560d880-a5e2-40c5-b315-7999cb352c5c', 'default', 'mens-9', 'mens-9.jpg', 'image/jpeg', 's3', 's3', 123759, '[]', '{\"thumbnail\": true}', '[]', '[]', 210, '2021-10-23 18:35:05', '2021-10-23 18:35:05'),
(211, 'Marvel\\Database\\Models\\Attachment', 211, '26af81dc-e940-49dd-a70b-4b57a667bb98', 'default', 'mens-13', 'mens-13.jpg', 'image/jpeg', 's3', 's3', 92148, '[]', '{\"thumbnail\": true}', '[]', '[]', 211, '2021-10-23 18:35:11', '2021-10-23 18:35:11'),
(212, 'Marvel\\Database\\Models\\Attachment', 212, '56d2457b-870c-4eec-8bd4-a6b5ee685838', 'default', 'Backpack-6', 'Backpack-6.jpg', 'image/jpeg', 's3', 's3', 170325, '[]', '{\"thumbnail\": true}', '[]', '[]', 212, '2021-10-23 18:38:52', '2021-10-23 18:38:53'),
(213, 'Marvel\\Database\\Models\\Attachment', 213, '02d63f7e-c487-4129-9293-3c1e6da85dc7', 'default', 'Chawkbazar17', 'Chawkbazar17.png', 'image/png', 's3', 's3', 195028, '[]', '{\"thumbnail\": true}', '[]', '[]', 213, '2021-10-23 18:41:38', '2021-10-23 18:41:38'),
(214, 'Marvel\\Database\\Models\\Attachment', 214, '0f571034-0326-48fe-ba3d-7953b4a642c1', 'default', 'Chawkbazar20', 'Chawkbazar20.png', 'image/png', 's3', 's3', 199843, '[]', '{\"thumbnail\": true}', '[]', '[]', 214, '2021-10-23 18:41:42', '2021-10-23 18:41:42'),
(215, 'Marvel\\Database\\Models\\Attachment', 215, '2379907f-2f2d-4e98-8cb2-a8e5255373b0', 'default', 'Footwear-9', 'Footwear-9.jpg', 'image/jpeg', 's3', 's3', 87066, '[]', '{\"thumbnail\": true}', '[]', '[]', 215, '2021-10-23 18:45:53', '2021-10-23 18:45:54'),
(216, 'Marvel\\Database\\Models\\Attachment', 216, 'f03c9383-37e9-4864-9597-762bc9219b54', 'default', 'Footwear-8', 'Footwear-8.jpg', 'image/jpeg', 's3', 's3', 87079, '[]', '{\"thumbnail\": true}', '[]', '[]', 216, '2021-10-23 18:45:58', '2021-10-23 18:45:58'),
(217, 'Marvel\\Database\\Models\\Attachment', 217, 'f4a65632-52a1-408f-be2d-ba7ad4bdff41', 'default', 'mens-2', 'mens-2.jpg', 'image/jpeg', 's3', 's3', 214505, '[]', '{\"thumbnail\": true}', '[]', '[]', 217, '2021-10-23 18:50:34', '2021-10-23 18:50:34'),
(218, 'Marvel\\Database\\Models\\Attachment', 218, '45cc2e4e-1f02-48fd-ab89-96cb2c9f2a1f', 'default', 'mens-7', 'mens-7.jpg', 'image/jpeg', 's3', 's3', 224468, '[]', '{\"thumbnail\": true}', '[]', '[]', 218, '2021-10-23 18:50:38', '2021-10-23 18:50:38'),
(219, 'Marvel\\Database\\Models\\Attachment', 219, '5c667594-c8a3-450d-a69c-71940147c9f7', 'default', 'Watches-6-1', 'Watches-6-1.jpg', 'image/jpeg', 's3', 's3', 101135, '[]', '{\"thumbnail\": true}', '[]', '[]', 219, '2021-10-23 18:54:47', '2021-10-23 18:54:47'),
(220, 'Marvel\\Database\\Models\\Attachment', 220, '39fe9d49-eded-4904-81d7-f95275d167ab', 'default', 'Watches-7-1', 'Watches-7-1.jpg', 'image/jpeg', 's3', 's3', 101172, '[]', '{\"thumbnail\": true}', '[]', '[]', 220, '2021-10-23 18:54:51', '2021-10-23 18:54:51'),
(221, 'Marvel\\Database\\Models\\Attachment', 221, '625e670b-bcca-4284-aaf8-ea0e85595eae', 'default', 'Footwear-18', 'Footwear-18.jpg', 'image/jpeg', 's3', 's3', 108507, '[]', '{\"thumbnail\": true}', '[]', '[]', 221, '2021-10-23 18:57:11', '2021-10-23 18:57:11'),
(222, 'Marvel\\Database\\Models\\Attachment', 222, 'cae70b88-4091-4748-a1d5-1df5d970b22b', 'default', 'Footwear-19', 'Footwear-19.jpg', 'image/jpeg', 's3', 's3', 108403, '[]', '{\"thumbnail\": true}', '[]', '[]', 222, '2021-10-23 18:57:14', '2021-10-23 18:57:14'),
(223, 'Marvel\\Database\\Models\\Attachment', 223, 'd1752da1-516b-46be-bd93-87cad20336ca', 'default', 'Casual-Wear-4-1', 'Casual-Wear-4-1.jpg', 'image/jpeg', 's3', 's3', 271254, '[]', '{\"thumbnail\": true}', '[]', '[]', 223, '2021-10-23 19:00:46', '2021-10-23 19:00:46'),
(224, 'Marvel\\Database\\Models\\Attachment', 224, '4241eda7-ebab-4b44-bc78-d41566f58eab', 'default', 'Casual-Wear-5-1', 'Casual-Wear-5-1.jpg', 'image/jpeg', 's3', 's3', 341697, '[]', '{\"thumbnail\": true}', '[]', '[]', 224, '2021-10-23 19:01:09', '2021-10-23 19:01:09'),
(225, 'Marvel\\Database\\Models\\Attachment', 225, '59d7e2ee-9a26-4736-8fce-9f305eddfc77', 'default', 'Backpack-7', 'Backpack-7.jpg', 'image/jpeg', 's3', 's3', 130531, '[]', '{\"thumbnail\": true}', '[]', '[]', 225, '2021-10-23 19:04:37', '2021-10-23 19:04:38'),
(226, 'Marvel\\Database\\Models\\Attachment', 226, '913d5e66-9852-496c-ab77-2d823bbb9836', 'default', 'Sunglasess-2-1', 'Sunglasess-2-1.jpg', 'image/jpeg', 's3', 's3', 56380, '[]', '{\"thumbnail\": true}', '[]', '[]', 226, '2021-10-23 19:08:09', '2021-10-23 19:08:09'),
(227, 'Marvel\\Database\\Models\\Attachment', 227, '3a713933-2306-4e9c-b47e-a0b0a383879f', 'default', 'Sunglasess-3-1', 'Sunglasess-3-1.jpg', 'image/jpeg', 's3', 's3', 56209, '[]', '{\"thumbnail\": true}', '[]', '[]', 227, '2021-10-23 19:08:14', '2021-10-23 19:08:14'),
(228, 'Marvel\\Database\\Models\\Attachment', 228, '3eabba42-43ce-4186-9b74-ebde3eba607c', 'default', 'Chawkbazar22', 'Chawkbazar22.png', 'image/png', 's3', 's3', 172407, '[]', '{\"thumbnail\": true}', '[]', '[]', 228, '2021-10-23 19:10:37', '2021-10-23 19:10:37'),
(229, 'Marvel\\Database\\Models\\Attachment', 229, 'ed2e2d6e-ab71-475b-987b-17948d0ae377', 'default', 'Chawkbazar21', 'Chawkbazar21.png', 'image/png', 's3', 's3', 173733, '[]', '{\"thumbnail\": true}', '[]', '[]', 229, '2021-10-23 19:10:41', '2021-10-23 19:10:41'),
(230, 'Marvel\\Database\\Models\\Attachment', 230, '2c39c6de-0e1a-4910-971a-79dfe4dd160d', 'default', 'Watches-4-1', 'Watches-4-1.jpg', 'image/jpeg', 's3', 's3', 80464, '[]', '{\"thumbnail\": true}', '[]', '[]', 230, '2021-10-23 19:13:34', '2021-10-23 19:13:34'),
(231, 'Marvel\\Database\\Models\\Attachment', 231, '958234f7-5736-4f1e-b2ed-1c61421218f4', 'default', 'Watches-4-1', 'Watches-4-1.jpg', 'image/jpeg', 's3', 's3', 80464, '[]', '{\"thumbnail\": true}', '[]', '[]', 231, '2021-10-23 19:13:38', '2021-10-23 19:13:38'),
(232, 'Marvel\\Database\\Models\\Attachment', 232, '7fe19180-4201-4148-b9e3-3f88ed98dde7', 'default', 'Casual-Wear-1-1', 'Casual-Wear-1-1.jpg', 'image/jpeg', 's3', 's3', 210409, '[]', '{\"thumbnail\": true}', '[]', '[]', 232, '2021-10-23 19:16:37', '2021-10-23 19:16:37'),
(233, 'Marvel\\Database\\Models\\Attachment', 233, 'e45ba2aa-45df-417e-b6f9-b6affd19b965', 'default', 'Casual-Wear-8', 'Casual-Wear-8.jpg', 'image/jpeg', 's3', 's3', 210426, '[]', '{\"thumbnail\": true}', '[]', '[]', 233, '2021-10-23 19:16:41', '2021-10-23 19:16:41'),
(234, 'Marvel\\Database\\Models\\Attachment', 234, '6c13e390-e2c5-41c1-a5f9-196d0b07a913', 'default', 'Chawkbazar1', 'Chawkbazar1.png', 'image/png', 's3', 's3', 157232, '[]', '{\"thumbnail\": true}', '[]', '[]', 234, '2021-10-23 19:19:04', '2021-10-23 19:19:04'),
(235, 'Marvel\\Database\\Models\\Attachment', 235, '073d41a9-b117-4f82-964a-a7d0cc05afa1', 'default', 'Chawkbazar2', 'Chawkbazar2.png', 'image/png', 's3', 's3', 172083, '[]', '{\"thumbnail\": true}', '[]', '[]', 235, '2021-10-23 19:19:08', '2021-10-23 19:19:08'),
(236, 'Marvel\\Database\\Models\\Attachment', 236, 'c008c075-f963-4e62-8c69-0f8645d32ada', 'default', 'kids-1', 'kids-1.jpg', 'image/jpeg', 's3', 's3', 240482, '[]', '{\"thumbnail\": true}', '[]', '[]', 236, '2021-10-23 19:21:07', '2021-10-23 19:21:07'),
(237, 'Marvel\\Database\\Models\\Attachment', 237, '459da7af-da15-4dd4-8911-ec56b742ee8e', 'default', 'kids-3', 'kids-3.jpg', 'image/jpeg', 's3', 's3', 193979, '[]', '{\"thumbnail\": true}', '[]', '[]', 237, '2021-10-23 19:21:10', '2021-10-23 19:21:11'),
(238, 'Marvel\\Database\\Models\\Attachment', 238, 'ed43f42d-bb32-4509-88c6-232b17751f67', 'default', 'blaze-fashion', 'blaze-fashion.png', 'image/png', 's3', 's3', 4396, '[]', '{\"thumbnail\": true}', '[]', '[]', 238, '2021-10-25 04:17:45', '2021-10-25 04:17:46'),
(239, 'Marvel\\Database\\Models\\Attachment', 239, '058428fd-16c0-42f4-9f1b-75930d1db83d', 'default', 'club-shoes', 'club-shoes.png', 'image/png', 's3', 's3', 4099, '[]', '{\"thumbnail\": true}', '[]', '[]', 239, '2021-10-25 04:19:30', '2021-10-25 04:19:31'),
(240, 'Marvel\\Database\\Models\\Attachment', 240, 'dcf5e5db-0d24-4de8-911f-a614c9ee5b4d', 'default', 'elegance', 'elegance.png', 'image/png', 's3', 's3', 4140, '[]', '{\"thumbnail\": true}', '[]', '[]', 240, '2021-10-25 04:19:44', '2021-10-25 04:19:44'),
(241, 'Marvel\\Database\\Models\\Attachment', 241, '5ae65d7a-db2a-4690-9542-f1d2a4b784dd', 'default', 'fashadil', 'fashadil.png', 'image/png', 's3', 's3', 4920, '[]', '{\"thumbnail\": true}', '[]', '[]', 241, '2021-10-25 04:20:12', '2021-10-25 04:20:12'),
(242, 'Marvel\\Database\\Models\\Attachment', 242, '28df1088-e666-4689-8f8c-2d5648635e86', 'default', 'fusion', 'fusion.png', 'image/png', 's3', 's3', 3722, '[]', '{\"thumbnail\": true}', '[]', '[]', 242, '2021-10-25 04:20:21', '2021-10-25 04:20:21'),
(243, 'Marvel\\Database\\Models\\Attachment', 243, 'edcf670d-65e7-4d32-8283-8d17eb237b97', 'default', 'hoppister', 'hoppister.png', 'image/png', 's3', 's3', 3895, '[]', '{\"thumbnail\": true}', '[]', '[]', 243, '2021-10-25 04:20:46', '2021-10-25 04:20:46'),
(244, 'Marvel\\Database\\Models\\Attachment', 244, '84a50245-9896-4e04-8443-94e484d69795', 'default', 'hunter-shoes', 'hunter-shoes.png', 'image/png', 's3', 's3', 4868, '[]', '{\"thumbnail\": true}', '[]', '[]', 244, '2021-10-25 04:20:54', '2021-10-25 04:20:55'),
(245, 'Marvel\\Database\\Models\\Attachment', 245, '1b141d53-295a-4a25-97b6-84ca75c70c15', 'default', 'shovia', 'shovia.png', 'image/png', 's3', 's3', 3814, '[]', '{\"thumbnail\": true}', '[]', '[]', 245, '2021-10-25 04:21:03', '2021-10-25 04:21:04'),
(246, 'Marvel\\Database\\Models\\Attachment', 246, 'bc18606f-8d78-4142-aa88-45cb02553114', 'default', '2x2x-thumbnail', '2x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 7616, '[]', '{\"thumbnail\": true}', '[]', '[]', 246, '2021-10-25 04:35:16', '2021-10-25 04:35:16'),
(247, 'Marvel\\Database\\Models\\Attachment', 247, '8dedabb0-d891-480e-9c30-d204f48c14da', 'default', '4x2x-thumbnail', '4x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8640, '[]', '{\"thumbnail\": true}', '[]', '[]', 247, '2021-10-25 04:37:12', '2021-10-25 04:37:12'),
(248, 'Marvel\\Database\\Models\\Attachment', 248, '3a585487-ae1d-423a-88f0-85fac0f0dfd0', 'default', '5x2x-thumbnail', '5x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 7550, '[]', '{\"thumbnail\": true}', '[]', '[]', 248, '2021-10-25 04:39:11', '2021-10-25 04:39:11'),
(249, 'Marvel\\Database\\Models\\Attachment', 249, 'd3b8a812-3be5-4eba-a469-ac4831f4aff4', 'default', '6x2x-thumbnail', '6x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8827, '[]', '{\"thumbnail\": true}', '[]', '[]', 249, '2021-10-25 04:39:48', '2021-10-25 04:39:48'),
(250, 'Marvel\\Database\\Models\\Attachment', 250, 'c876f3d3-9be9-47a7-91f8-e7820d9131a5', 'default', '8x2x-thumbnail', '8x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8050, '[]', '{\"thumbnail\": true}', '[]', '[]', 250, '2021-10-25 09:35:01', '2021-10-25 09:35:01'),
(251, 'Marvel\\Database\\Models\\Attachment', 251, 'fa1d9f4c-d25a-47ed-978c-a8d32155f446', 'default', '10x2x-thumbnail', '10x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8452, '[]', '{\"thumbnail\": true}', '[]', '[]', 251, '2021-10-25 09:36:20', '2021-10-25 09:36:20'),
(252, 'Marvel\\Database\\Models\\Attachment', 252, 'e51f205d-8f6d-4d45-b042-7f78efbad12c', 'default', '12x2x-thumbnail', '12x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8981, '[]', '{\"thumbnail\": true}', '[]', '[]', 252, '2021-10-25 09:37:22', '2021-10-25 09:37:22'),
(253, 'Marvel\\Database\\Models\\Attachment', 253, '9e6bfdca-15da-4add-9ef0-4968b7017be1', 'default', '15x2x-thumbnail', '15x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 7708, '[]', '{\"thumbnail\": true}', '[]', '[]', 253, '2021-10-25 09:37:43', '2021-10-25 09:37:44'),
(254, 'Marvel\\Database\\Models\\Attachment', 254, '551437d3-6df7-48e8-a6d6-543e21fdd4bc', 'default', '18x2x-thumbnail', '18x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 8445, '[]', '{\"thumbnail\": true}', '[]', '[]', 254, '2021-10-25 09:39:01', '2021-10-25 09:39:01'),
(255, 'Marvel\\Database\\Models\\Attachment', 255, 'cbafa86f-6653-4498-ac93-9351214e3332', 'default', '20x2x-thumbnail', '20x2x-thumbnail.jpg', 'image/jpeg', 's3', 's3', 9076, '[]', '{\"thumbnail\": true}', '[]', '[]', 255, '2021-10-25 09:39:38', '2021-10-25 09:39:38'),
(256, 'Marvel\\Database\\Models\\Attachment', 256, '52afee64-6b2e-4e66-b09c-264bd92b3042', 'default', 'logo final2x', 'logo-final2x.png', 'image/png', 's3', 's3', 5922, '[]', '{\"thumbnail\": true}', '[]', '[]', 256, '2021-10-27 06:13:12', '2021-10-27 06:13:13'),
(257, 'Marvel\\Database\\Models\\Attachment', 257, '5f59488f-5ea3-49fe-94f0-c338c842f129', 'default', 'logo final', 'logo-final.png', 'image/png', 's3', 's3', 2799, '[]', '{\"thumbnail\": true}', '[]', '[]', 257, '2021-10-27 06:13:26', '2021-10-27 06:13:26'),
(258, 'Marvel\\Database\\Models\\Attachment', 258, '9cae36a3-799b-4d11-97c3-837ff4bb6091', 'default', 'logo final2x', 'logo-final2x.png', 'image/png', 's3', 's3', 5922, '[]', '{\"thumbnail\": true}', '[]', '[]', 258, '2021-10-27 06:13:39', '2021-10-27 06:13:39'),
(259, 'Marvel\\Database\\Models\\Attachment', 259, 'e930a646-bb3e-460a-866e-5973a3e0e5c3', 'default', 'Group 36179', 'Group-36179.png', 'image/png', 's3', 's3', 10469, '[]', '{\"thumbnail\": true}', '[]', '[]', 259, '2021-10-27 06:50:39', '2021-10-27 06:50:39'),
(260, 'Marvel\\Database\\Models\\Attachment', 260, '0af2dccd-c566-479c-bc68-ef49625523b1', 'default', 'Group 36179', 'Group-36179.png', 'image/png', 's3', 's3', 10469, '[]', '{\"thumbnail\": true}', '[]', '[]', 260, '2021-10-27 06:51:22', '2021-10-27 06:51:22'),
(261, 'Marvel\\Database\\Models\\Attachment', 261, '4d914f86-e3a2-45e1-b578-8f6062756237', 'default', 'Group 36180', 'Group-36180.png', 'image/png', 's3', 's3', 8576, '[]', '{\"thumbnail\": true}', '[]', '[]', 261, '2021-10-27 06:51:59', '2021-10-27 06:51:59'),
(262, 'Marvel\\Database\\Models\\Attachment', 262, '35bb9b60-b79a-4e69-b514-ba72e233eb12', 'default', 'Group 36181', 'Group-36181.png', 'image/png', 's3', 's3', 10841, '[]', '{\"thumbnail\": true}', '[]', '[]', 262, '2021-10-27 06:52:25', '2021-10-27 06:52:26'),
(263, 'Marvel\\Database\\Models\\Attachment', 263, '578fe0db-e435-4b28-82b0-7108cf8642e0', 'default', 'Group 36186', 'Group-36186.png', 'image/png', 's3', 's3', 12336, '[]', '{\"thumbnail\": true}', '[]', '[]', 263, '2021-10-27 06:52:45', '2021-10-27 06:52:45'),
(264, 'Marvel\\Database\\Models\\Attachment', 264, '078c45dd-b5fd-48d0-96b3-3b8a1a47827d', 'default', 'Group 36185', 'Group-36185.png', 'image/png', 's3', 's3', 8009, '[]', '{\"thumbnail\": true}', '[]', '[]', 264, '2021-10-27 06:53:00', '2021-10-27 06:53:00'),
(265, 'Marvel\\Database\\Models\\Attachment', 265, '16c98a5b-f832-492a-a4c2-24a441d0af99', 'default', 'Group 36184', 'Group-36184.png', 'image/png', 's3', 's3', 8723, '[]', '{\"thumbnail\": true}', '[]', '[]', 265, '2021-10-27 06:53:21', '2021-10-27 06:53:21'),
(266, 'Marvel\\Database\\Models\\Attachment', 266, '49ec8d93-e0b5-492f-9e8a-26f28cc77262', 'default', 'Group 36183', 'Group-36183.png', 'image/png', 's3', 's3', 8144, '[]', '{\"thumbnail\": true}', '[]', '[]', 266, '2021-10-27 06:53:39', '2021-10-27 06:53:39'),
(267, 'Marvel\\Database\\Models\\Attachment', 267, '4ab83941-23e0-4e05-846f-5d94c274f6a4', 'default', 'Group 36182', 'Group-36182.png', 'image/png', 's3', 's3', 7958, '[]', '{\"thumbnail\": true}', '[]', '[]', 267, '2021-10-27 06:53:52', '2021-10-27 06:53:52'),
(268, 'Marvel\\Database\\Models\\Attachment', 268, 'f2b48f76-b767-4ec3-86de-587f7ce4490f', 'default', 'Group 36179', 'Group-36179.png', 'image/png', 's3', 's3', 10469, '[]', '{\"thumbnail\": true}', '[]', '[]', 268, '2021-11-08 04:57:52', '2021-11-08 04:57:53'),
(269, 'Marvel\\Database\\Models\\Attachment', 269, '5902275f-091d-47c4-9c6a-8f9ee4cf71de', 'default', 'h&m', 'h&m.png', 'image/png', 's3', 's3', 59308, '[]', '{\"thumbnail\": true}', '[]', '[]', 269, '2021-11-08 05:06:04', '2021-11-08 05:06:04'),
(270, 'Marvel\\Database\\Models\\Attachment', 270, '3bf0f7a2-00f9-4ede-a0dc-d3f59b0344b5', 'default', 'Group 36181', 'Group-36181.png', 'image/png', 's3', 's3', 10841, '[]', '{\"thumbnail\": true}', '[]', '[]', 270, '2021-11-08 05:06:17', '2021-11-08 05:06:17'),
(271, 'Marvel\\Database\\Models\\Attachment', 271, '3fc77740-dc8f-458d-894c-ee18a789fe80', 'default', 'logo16', 'logo16.png', 'image/png', 's3', 's3', 6871, '[]', '{\"thumbnail\": true}', '[]', '[]', 271, '2021-11-08 05:08:47', '2021-11-08 05:08:47'),
(272, 'Marvel\\Database\\Models\\Attachment', 272, '80405012-e2fe-4c6e-a64d-21e255fab974', 'default', 'club-shoes', 'club-shoes.png', 'image/png', 's3', 's3', 4099, '[]', '{\"thumbnail\": true}', '[]', '[]', 272, '2021-11-08 05:09:47', '2021-11-08 05:09:48'),
(273, 'Marvel\\Database\\Models\\Attachment', 273, 'd1120cfc-af4d-49c9-bd6e-5945b00eb7a3', 'default', 'elegance', 'elegance.png', 'image/png', 's3', 's3', 4140, '[]', '{\"thumbnail\": true}', '[]', '[]', 273, '2021-11-08 05:10:19', '2021-11-08 05:10:19'),
(274, 'Marvel\\Database\\Models\\Attachment', 274, '054c424e-1294-45fb-b30f-cfcf0b439228', 'default', 'fashadil', 'fashadil.png', 'image/png', 's3', 's3', 4920, '[]', '{\"thumbnail\": true}', '[]', '[]', 274, '2021-11-08 05:13:54', '2021-11-08 05:13:54'),
(275, 'Marvel\\Database\\Models\\Attachment', 275, '1c3b5d78-cb34-497b-bf65-444701099bdd', 'default', 'fusion', 'fusion.png', 'image/png', 's3', 's3', 3722, '[]', '{\"thumbnail\": true}', '[]', '[]', 275, '2021-11-08 05:22:39', '2021-11-08 05:22:39'),
(276, 'Marvel\\Database\\Models\\Attachment', 276, '577c51f3-c9c4-43aa-a87f-7101e104e94f', 'default', 'hoppister', 'hoppister.png', 'image/png', 's3', 's3', 3895, '[]', '{\"thumbnail\": true}', '[]', '[]', 276, '2021-11-08 05:23:07', '2021-11-08 05:23:07'),
(277, 'Marvel\\Database\\Models\\Attachment', 277, '5895c3af-3ed2-4388-a5c9-c7a6ec594cfc', 'default', 'shovia', 'shovia.png', 'image/png', 's3', 's3', 3814, '[]', '{\"thumbnail\": true}', '[]', '[]', 277, '2021-11-08 05:31:07', '2021-11-08 05:31:07'),
(278, 'Marvel\\Database\\Models\\Attachment', 278, 'cb62d655-8773-4c25-a328-4df39e0990d5', 'default', 'hoppister', 'hoppister.png', 'image/png', 's3', 's3', 3895, '[]', '{\"thumbnail\": true}', '[]', '[]', 278, '2021-11-08 05:31:22', '2021-11-08 05:31:22'),
(279, 'Marvel\\Database\\Models\\Attachment', 279, 'f97af277-1e2f-46ef-8ae8-95006b9585ad', 'default', 'vintege', 'vintege.png', 'image/png', 's3', 's3', 4600, '[]', '{\"thumbnail\": true}', '[]', '[]', 279, '2021-11-08 05:32:20', '2021-11-08 05:32:20'),
(280, 'Marvel\\Database\\Models\\Attachment', 280, '5c121826-93de-42f0-822c-3f111f66041c', 'default', 'elegance', 'elegance.png', 'image/png', 's3', 's3', 4140, '[]', '{\"thumbnail\": true}', '[]', '[]', 280, '2021-11-08 05:33:04', '2021-11-08 05:33:04'),
(281, 'Marvel\\Database\\Models\\Attachment', 281, 'b10ea837-6211-4f5d-8d66-f63b5d34606d', 'default', 'shovia', 'shovia.png', 'image/png', 's3', 's3', 3814, '[]', '{\"thumbnail\": true}', '[]', '[]', 281, '2021-11-08 05:33:10', '2021-11-08 05:33:10'),
(282, 'Marvel\\Database\\Models\\Attachment', 282, '9788d3ab-fa28-477f-9533-4436eb8be002', 'default', 'Group 36179', 'Group-36179.png', 'image/png', 's3', 's3', 10469, '[]', '{\"thumbnail\": true}', '[]', '[]', 282, '2021-11-08 05:33:38', '2021-11-08 05:33:38'),
(283, 'Marvel\\Database\\Models\\Attachment', 283, '8a0f738c-0227-40cc-8f60-32d388c5723b', 'default', 'Group 36180', 'Group-36180.png', 'image/png', 's3', 's3', 8576, '[]', '{\"thumbnail\": true}', '[]', '[]', 283, '2021-11-08 05:34:26', '2021-11-08 05:34:27'),
(284, 'Marvel\\Database\\Models\\Attachment', 284, '81f157de-7b12-4e5f-8504-ef05ef951b02', 'default', 'Group 36184', 'Group-36184.png', 'image/png', 's3', 's3', 8723, '[]', '{\"thumbnail\": true}', '[]', '[]', 284, '2021-11-08 05:35:08', '2021-11-08 05:35:08'),
(285, 'Marvel\\Database\\Models\\Attachment', 285, '44020116-1071-4476-bf65-04c48025294a', 'default', 'Group 36183', 'Group-36183.png', 'image/png', 's3', 's3', 8144, '[]', '{\"thumbnail\": true}', '[]', '[]', 285, '2021-11-08 05:35:40', '2021-11-08 05:35:40'),
(286, 'Marvel\\Database\\Models\\Attachment', 286, 'acd0d18e-3207-40eb-927c-7c5a7fc41abc', 'default', 'Group 36186', 'Group-36186.png', 'image/png', 's3', 's3', 12336, '[]', '{\"thumbnail\": true}', '[]', '[]', 286, '2021-11-08 05:36:06', '2021-11-08 05:36:06'),
(287, 'Marvel\\Database\\Models\\Attachment', 287, '7f2fbb19-13c1-4c84-b808-91ab80a74d83', 'default', 'Group 36185', 'Group-36185.png', 'image/png', 's3', 's3', 8009, '[]', '{\"thumbnail\": true}', '[]', '[]', 287, '2021-11-08 05:36:39', '2021-11-08 05:36:39'),
(288, 'Marvel\\Database\\Models\\Attachment', 288, 'fa22cc43-c253-467b-b91d-719c5b6e2305', 'default', 'Group 36182', 'Group-36182.png', 'image/png', 's3', 's3', 7958, '[]', '{\"thumbnail\": true}', '[]', '[]', 288, '2021-11-08 05:37:03', '2021-11-08 05:37:04'),
(289, 'Marvel\\Database\\Models\\Attachment', 289, 'f5fc0c6a-4c76-427a-bf00-d85acbfbc96d', 'default', 'Group 36181', 'Group-36181.png', 'image/png', 's3', 's3', 10841, '[]', '{\"thumbnail\": true}', '[]', '[]', 289, '2021-11-08 05:37:20', '2021-11-08 05:37:20'),
(290, 'Marvel\\Database\\Models\\Attachment', 290, 'aa4ded0c-3ce0-4e04-a8b9-3c0d134c38fd', 'default', 'blaze-fashion', 'blaze-fashion.png', 'image/png', 's3', 's3', 4396, '[]', '{\"thumbnail\": true}', '[]', '[]', 290, '2021-11-08 05:38:18', '2021-11-08 05:38:18'),
(291, 'Marvel\\Database\\Models\\Attachment', 291, '39e2b374-59ac-4685-9e4e-df815af59030', 'default', 'hunter-shoes', 'hunter-shoes.png', 'image/png', 's3', 's3', 4868, '[]', '{\"thumbnail\": true}', '[]', '[]', 291, '2021-11-08 05:38:41', '2021-11-08 05:38:41'),
(292, 'Marvel\\Database\\Models\\Attachment', 292, '6c571e48-7b98-442f-8794-5c1cba555d7d', 'default', '10', '10.png', 'image/png', 's3', 's3', 85139, '[]', '{\"thumbnail\": true}', '[]', '[]', 292, '2021-11-08 08:01:31', '2021-11-08 08:01:32'),
(293, 'Marvel\\Database\\Models\\Attachment', 293, '7f17adfc-ada9-456b-846d-1c023809dbdd', 'default', '50png', '50png.png', 'image/png', 's3', 's3', 74640, '[]', '{\"thumbnail\": true}', '[]', '[]', 293, '2021-11-08 08:02:00', '2021-11-08 08:02:01'),
(294, 'Marvel\\Database\\Models\\Attachment', 294, '6f9601e8-e831-464f-8375-5cad58493160', 'default', '30 (1)', '30-(1).png', 'image/png', 's3', 's3', 78032, '[]', '{\"thumbnail\": true}', '[]', '[]', 294, '2021-11-08 08:03:23', '2021-11-08 08:03:23'),
(295, 'Marvel\\Database\\Models\\Attachment', 295, '9be534af-5241-4ea7-b74c-cd51447f200b', 'default', '400', '400.png', 'image/png', 's3', 's3', 40011, '[]', '{\"thumbnail\": true}', '[]', '[]', 295, '2021-11-08 08:03:53', '2021-11-08 08:03:53'),
(296, 'Marvel\\Database\\Models\\Attachment', 296, '0314e081-3d0a-4fea-afc7-d396977106de', 'default', '2 0(1)', '2-0(1).png', 'image/png', 's3', 's3', 83535, '[]', '{\"thumbnail\": true}', '[]', '[]', 296, '2021-11-08 08:04:44', '2021-11-08 08:04:44'),
(297, 'Marvel\\Database\\Models\\Attachment', 297, '91ea6d36-e5c4-4a8f-955b-d78c4bdf92da', 'default', 'store_owner', 'store_owner.png', 'image/png', 's3', 's3', 100825, '[]', '{\"thumbnail\": true}', '[]', '[]', 297, '2021-11-25 04:21:27', '2021-11-25 04:21:27'),
(298, 'Marvel\\Database\\Models\\Attachment', 298, 'b9caaed6-e9af-4576-8a27-c7f8d6860851', 'default', 'pro pic 3', 'pro-pic-3.jpg', 'image/jpeg', 's3', 's3', 53425, '[]', '{\"thumbnail\": true}', '[]', '[]', 298, '2021-11-27 15:16:54', '2021-11-27 15:16:54'),
(299, 'Marvel\\Database\\Models\\Attachment', 299, '8a4aa6da-0792-4cdb-9b0f-05c61859649c', 'default', 'Footwear-15', 'Footwear-15.jpg', 'image/jpeg', 's3', 's3', 101509, '[]', '{\"thumbnail\": true}', '[]', '[]', 299, '2021-11-27 15:53:40', '2021-11-27 15:53:40'),
(300, 'Marvel\\Database\\Models\\Attachment', 300, '2a8c0202-978d-44e6-ba01-d55528197446', 'default', 'Footwear-14', 'Footwear-14.jpg', 'image/jpeg', 's3', 's3', 101268, '[]', '{\"thumbnail\": true}', '[]', '[]', 300, '2021-11-27 15:53:48', '2021-11-27 15:53:48'),
(301, 'Marvel\\Database\\Models\\Attachment', 301, '99fea1d1-b6ba-42d3-a53a-96681ec94b7e', 'default', 'Chawkbazar7', 'Chawkbazar7.png', 'image/png', 's3', 's3', 168959, '[]', '{\"thumbnail\": true}', '[]', '[]', 301, '2021-11-28 10:58:08', '2021-11-28 10:58:08'),
(302, 'Marvel\\Database\\Models\\Attachment', 302, '7025ca15-5cde-446c-b13d-208588a2fc38', 'default', 'Chawkbazar6', 'Chawkbazar6.png', 'image/png', 's3', 's3', 147835, '[]', '{\"thumbnail\": true}', '[]', '[]', 302, '2021-11-28 10:58:21', '2021-11-28 10:58:21'),
(303, 'Marvel\\Database\\Models\\Attachment', 303, '76ed22a1-c6b7-4724-afa7-4cd608f1ff80', 'default', 'Chawkbazar8', 'Chawkbazar8.png', 'image/png', 's3', 's3', 143537, '[]', '{\"thumbnail\": true}', '[]', '[]', 303, '2021-11-28 10:58:21', '2021-11-28 10:58:21'),
(304, 'Marvel\\Database\\Models\\Attachment', 304, '96d8c79a-e380-4f96-8958-b7f1d5faf7f7', 'default', 'women', 'women.png', 'image/png', 's3', 's3', 211997, '[]', '{\"thumbnail\": true}', '[]', '[]', 304, '2022-01-10 15:47:49', '2022-01-10 15:47:49'),
(305, 'Marvel\\Database\\Models\\Attachment', 305, '2f826614-79c1-43ba-b02f-47868540f9df', 'default', 'watch', 'watch.png', 'image/png', 's3', 's3', 66512, '[]', '{\"thumbnail\": true}', '[]', '[]', 305, '2022-01-10 15:48:06', '2022-01-10 15:48:06'),
(306, 'Marvel\\Database\\Models\\Attachment', 306, '72a82265-823a-4104-80f4-4e518cf98c05', 'default', 'sunglass', 'sunglass.png', 'image/png', 's3', 's3', 51428, '[]', '{\"thumbnail\": true}', '[]', '[]', 306, '2022-01-10 15:48:25', '2022-01-10 15:48:25'),
(307, 'Marvel\\Database\\Models\\Attachment', 307, 'dec42b05-efb2-44b5-9881-03ee8b0c1f53', 'default', 'sports', 'sports.png', 'image/png', 's3', 's3', 94018, '[]', '{\"thumbnail\": true}', '[]', '[]', 307, '2022-01-10 15:48:39', '2022-01-10 15:48:39'),
(308, 'Marvel\\Database\\Models\\Attachment', 308, 'd7eb0210-4815-41d6-b54b-0d24cfa0a9c2', 'default', 'Sneakers', 'Sneakers.png', 'image/png', 's3', 's3', 107699, '[]', '{\"thumbnail\": true}', '[]', '[]', 308, '2022-01-10 15:48:49', '2022-01-10 15:48:49'),
(309, 'Marvel\\Database\\Models\\Attachment', 309, '3769565a-6079-434e-a324-8407c48d2a20', 'default', 'Men', 'Men.png', 'image/png', 's3', 's3', 97423, '[]', '{\"thumbnail\": true}', '[]', '[]', 309, '2022-01-10 15:49:00', '2022-01-10 15:49:00'),
(310, 'Marvel\\Database\\Models\\Attachment', 310, '2040026e-9fa3-4cdb-97af-ecf63e9e9dcf', 'default', 'Kids', 'Kids.png', 'image/png', 's3', 's3', 258731, '[]', '{\"thumbnail\": true}', '[]', '[]', 310, '2022-01-10 15:49:12', '2022-01-10 15:49:12'),
(311, 'Marvel\\Database\\Models\\Attachment', 311, '55d1ca02-4df6-456c-a1ff-47fa249f2542', 'default', 'Bag', 'Bag.png', 'image/png', 's3', 's3', 58543, '[]', '{\"thumbnail\": true}', '[]', '[]', 311, '2022-01-10 15:49:22', '2022-01-10 15:49:22'),
(312, 'Marvel\\Database\\Models\\Attachment', 312, '7e2521e6-0313-4e7b-93c5-004ed6b2f49f', 'default', 'woman', 'woman.png', 'image/png', 's3', 's3', 1980, '[]', '{\"thumbnail\": true}', '[]', '[]', 312, '2022-03-01 03:02:27', '2022-03-01 03:02:27'),
(313, 'Marvel\\Database\\Models\\Attachment', 313, '8129bdf0-17b7-4ff5-848d-1983388283e0', 'default', 'watch', 'watch.png', 'image/png', 's3', 's3', 1295, '[]', '{\"thumbnail\": true}', '[]', '[]', 313, '2022-03-01 03:03:07', '2022-03-01 03:03:07'),
(314, 'Marvel\\Database\\Models\\Attachment', 314, '10f2ad49-6af9-4e90-baf6-e00e3fe3c13a', 'default', 'sunglass', 'sunglass.png', 'image/png', 's3', 's3', 1873, '[]', '{\"thumbnail\": true}', '[]', '[]', 314, '2022-03-01 03:03:17', '2022-03-01 03:03:17'),
(315, 'Marvel\\Database\\Models\\Attachment', 315, '91c2e5e5-fcc8-4fe1-b141-1752d1789712', 'default', 'sports', 'sports.png', 'image/png', 's3', 's3', 3394, '[]', '{\"thumbnail\": true}', '[]', '[]', 315, '2022-03-01 03:03:25', '2022-03-01 03:03:26'),
(316, 'Marvel\\Database\\Models\\Attachment', 316, '6c37309b-7fd6-4248-aef6-3c602d62f746', 'default', 'sneakers', 'sneakers.png', 'image/png', 's3', 's3', 2709, '[]', '{\"thumbnail\": true}', '[]', '[]', 316, '2022-03-01 03:03:33', '2022-03-01 03:03:34'),
(317, 'Marvel\\Database\\Models\\Attachment', 317, '77ceb54b-188f-47c6-96c3-60d2af661ec6', 'default', 'man', 'man.png', 'image/png', 's3', 's3', 2006, '[]', '{\"thumbnail\": true}', '[]', '[]', 317, '2022-03-01 03:03:45', '2022-03-01 03:03:45'),
(318, 'Marvel\\Database\\Models\\Attachment', 318, '47351507-46cd-47e2-9503-f0f2495d4d90', 'default', 'kids', 'kids.png', 'image/png', 's3', 's3', 2467, '[]', '{\"thumbnail\": true}', '[]', '[]', 318, '2022-03-01 03:03:52', '2022-03-01 03:03:53'),
(319, 'Marvel\\Database\\Models\\Attachment', 319, 'b1525c82-00ba-493f-a82f-93f89851a2cc', 'default', 'bags', 'bags.png', 'image/png', 's3', 's3', 1870, '[]', '{\"thumbnail\": true}', '[]', '[]', 319, '2022-03-01 03:04:01', '2022-03-01 03:04:01'),
(320, 'Marvel\\Database\\Models\\Attachment', 320, 'fd145aae-bbea-4561-979f-5a5747a4c511', 'default', 'Footwear-1', 'Footwear-1.png', 'image/png', 's3', 's3', 248495, '[]', '{\"thumbnail\": true}', '[]', '[]', 320, '2022-03-02 02:47:15', '2022-03-02 02:47:16'),
(321, 'Marvel\\Database\\Models\\Attachment', 321, '26ff42eb-91ae-4f55-a6aa-ac0937b441ff', 'default', 'Footwear-1', 'Footwear-1.png', 'image/png', 's3', 's3', 248495, '[]', '{\"thumbnail\": true}', '[]', '[]', 321, '2022-03-02 02:47:25', '2022-03-02 02:47:25'),
(322, 'Marvel\\Database\\Models\\Attachment', 322, '5be0e57b-6304-4847-8aba-c350e69cf92f', 'default', 'watch', 'watch.png', 'image/png', 's3', 's3', 276201, '[]', '{\"thumbnail\": true}', '[]', '[]', 322, '2022-03-02 02:47:48', '2022-03-02 02:47:48'),
(323, 'Marvel\\Database\\Models\\Attachment', 323, '10cf0e96-74a0-441c-a0d6-7ca5ef6727e3', 'default', 'Footwear', 'Footwear.png', 'image/png', 's3', 's3', 202102, '[]', '{\"thumbnail\": true}', '[]', '[]', 323, '2022-03-02 02:49:48', '2022-03-02 02:49:48'),
(324, 'Marvel\\Database\\Models\\Attachment', 324, '4daaee67-5685-46c3-a7e5-a2dfbc0e60d4', 'default', '68747470733a2f2f796176757a63656c696b65722e6769746875622e696f2f73616d706c652d696d616765732f696d6167652d313032312e6a7067', '68747470733a2f2f796176757a63656c696b65722e6769746875622e696f2f73616d706c652d696d616765732f696d6167652d313032312e6a7067.jpg', 'image/jpeg', 'public', 'public', 165378, '[]', '{\"thumbnail\": true}', '[]', '[]', 1, '2025-12-02 10:10:20', '2025-12-02 10:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2020_04_17_194830_create_permission_tables', 1),
(6, '2020_06_02_051901_create_marvel_tables', 1),
(7, '2020_10_26_163529_create_media_table', 1),
(8, '2021_04_17_051901_create_new_marvel_tables', 1),
(9, '2021_08_08_051901_create_wallet_table', 1),
(10, '2021_09_26_051901_create_product_type_table', 1),
(11, '2021_10_12_193855_create_reviews_table', 1),
(12, '2022_01_19_051901_create_rental_tables', 1),
(13, '2022_01_31_051901_create_marvel_languages_tables', 1),
(14, '2022_03_23_051901_create_marvel_delivery_time_tables', 1),
(15, '2022_03_23_051902_create_marvel_store_notice_tables', 1),
(16, '2022_03_24_124527_add_columns_to_table', 1),
(17, '2022_04_11_094659_create_jobs_table', 1),
(18, '2022_05_09_070829_create_messages_table', 1),
(19, '2023_05_10_154638_add_column_to_order_table', 1),
(20, '2023_07_12_030502_create_notify_logs_table', 1),
(21, '2023_07_19_162433_create_faqs_table', 1),
(22, '2023_07_25_053633_create_terms_and_conditions_table', 1),
(23, '2023_08_10_161757_add_sold_quantity_column_to_products_table', 1),
(24, '2023_08_14_173253_create_flash_sales_table', 1),
(25, '2023_08_15_061447_add_is_featured_column_to_products_table', 1),
(26, '2023_08_28_114418_create_refund_policies_table', 1),
(27, '2023_09_07_061715_create_refund_reasons_table', 1),
(28, '2023_10_16_090210_add_digitial_file_tracker_column_to_variation_options_table', 1),
(29, '2023_10_19_055742_add_note_column_to_flash_sale_requests_table', 1),
(30, '2023_11_28_090210_add_new_two_column_to_coupons_table', 1),
(31, '2023_12_12_162216_create_became_sellers_table', 1),
(32, '2024_01_02_063637_create_transfer_history_table', 1),
(33, '2024_02_07_162216_create_commissions_table', 1),
(34, '2024_12_27_000001_create_coupon_usages_table', 2),
(35, '2024_12_28_000001_create_platform_commissions_table', 2),
(36, '2025_12_06_000001_create_cms_pages_table', 2),
(37, '2025_12_06_000002_add_editor_role_permission', 2),
(38, '2025_12_21_000001_add_puck_fields_to_cms_pages', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'Marvel\\Database\\Models\\User', 6),
(2, 'Marvel\\Database\\Models\\User', 1),
(2, 'Marvel\\Database\\Models\\User', 2),
(2, 'Marvel\\Database\\Models\\User', 3),
(2, 'Marvel\\Database\\Models\\User', 4),
(2, 'Marvel\\Database\\Models\\User', 5),
(2, 'Marvel\\Database\\Models\\User', 6),
(2, 'Marvel\\Database\\Models\\User', 7),
(2, 'Marvel\\Database\\Models\\User', 8),
(2, 'Marvel\\Database\\Models\\User', 9),
(2, 'Marvel\\Database\\Models\\User', 10),
(2, 'Marvel\\Database\\Models\\User', 11),
(2, 'Marvel\\Database\\Models\\User', 12),
(2, 'Marvel\\Database\\Models\\User', 13),
(2, 'Marvel\\Database\\Models\\User', 14),
(3, 'Marvel\\Database\\Models\\User', 1),
(3, 'Marvel\\Database\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'Marvel\\Database\\Models\\User', 6),
(2, 'Marvel\\Database\\Models\\User', 1),
(4, 'Marvel\\Database\\Models\\User', 3),
(4, 'Marvel\\Database\\Models\\User', 4),
(4, 'Marvel\\Database\\Models\\User', 5),
(4, 'Marvel\\Database\\Models\\User', 7),
(4, 'Marvel\\Database\\Models\\User', 8),
(4, 'Marvel\\Database\\Models\\User', 9),
(4, 'Marvel\\Database\\Models\\User', 10),
(4, 'Marvel\\Database\\Models\\User', 14);

-- --------------------------------------------------------

--
-- Table structure for table `notify_logs`
--

DROP TABLE IF EXISTS `notify_logs`;
CREATE TABLE IF NOT EXISTS `notify_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `receiver` bigint UNSIGNED NOT NULL,
  `sender` bigint UNSIGNED DEFAULT NULL,
  `notify_type` text COLLATE utf8mb4_unicode_ci,
  `notify_receiver_type` text COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `notify_tracker` text COLLATE utf8mb4_unicode_ci,
  `notify_text` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notify_logs_receiver_foreign` (`receiver`),
  KEY `notify_logs_sender_foreign` (`sender`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordered_files`
--

DROP TABLE IF EXISTS `ordered_files`;
CREATE TABLE IF NOT EXISTS `ordered_files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `digital_file_id` bigint UNSIGNED NOT NULL,
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordered_files_digital_file_id_foreign` (`digital_file_id`),
  KEY `ordered_files_tracking_number_foreign` (`tracking_number`),
  KEY `ordered_files_customer_id_foreign` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `customer_contact` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double NOT NULL,
  `sales_tax` double DEFAULT NULL,
  `paid_total` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  `cancelled_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cancelled_tax` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cancelled_delivery_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `payment_gateway` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `altered_payment_gateway` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` json DEFAULT NULL,
  `billing_address` json DEFAULT NULL,
  `logistics_provider` bigint UNSIGNED DEFAULT NULL,
  `delivery_fee` double DEFAULT NULL,
  `delivery_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` enum('order-pending','order-processing','order-completed','order-cancelled','order-refunded','order-failed','order-at-local-facility','order-out-for-delivery','order-pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'order-pending',
  `payment_status` enum('payment-pending','payment-processing','payment-success','payment-failed','payment-reversal','payment-refunded','payment-cash-on-delivery','payment-cash','payment-wallet','payment-awaiting-for-approval','payment-pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment-pending',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_tracking_number_unique` (`tracking_number`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_shop_id_foreign` (`shop_id`),
  KEY `orders_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `tracking_number`, `customer_id`, `customer_contact`, `customer_name`, `amount`, `sales_tax`, `paid_total`, `total`, `note`, `cancelled_amount`, `cancelled_tax`, `cancelled_delivery_fee`, `language`, `coupon_id`, `parent_id`, `shop_id`, `discount`, `payment_gateway`, `altered_payment_gateway`, `shipping_address`, `billing_address`, `logistics_provider`, `delivery_fee`, `delivery_time`, `order_status`, `payment_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(13, '20231120240869', 3, '19365141641631', 'Customer', 4050, 81, 4181, 4181, 'This is a order note. \n\nLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-20 04:14:43', '2023-11-20 04:15:05'),
(14, '20231120168783', 3, '19365141641631', 'Customer', 450, 0, 450, 450, NULL, 0.00, 0.00, 0.00, 'en', NULL, 13, 1, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-20 04:14:43', '2023-11-20 04:18:05'),
(15, '20231120866806', 3, '19365141641631', 'Customer', 3600, 0, 3600, 3600, NULL, 0.00, 0.00, 0.00, 'en', NULL, 13, 9, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-20 04:14:43', '2023-11-20 04:22:22'),
(16, '20231120549600', 3, '19365141641631', 'Customer', 300, 6, 356, 356, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-20 14:13:23', '2023-11-20 14:13:35'),
(17, '20231120730602', 3, '19365141641631', 'Customer', 300, 0, 300, 300, NULL, 0.00, 0.00, 0.00, 'en', NULL, 16, 2, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-20 14:13:23', '2023-11-20 14:13:35'),
(18, '20231120879243', 3, '19365141641631', 'Customer', 90, 1.8, 141.8, 141.8, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-20 15:24:51', '2023-11-20 15:26:00'),
(19, '20231120994480', 3, '19365141641631', 'Customer', 90, 0, 90, 90, NULL, 0.00, 0.00, 0.00, 'en', NULL, 18, 11, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-20 15:24:51', '2023-11-20 15:25:02'),
(20, '20231121197985', 3, '19365141641631', 'Customer', 90, 1.8, 141.8, 141.8, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-21 01:42:29', '2023-11-21 01:43:14'),
(21, '20231121698019', 3, '19365141641631', 'Customer', 90, 0, 90, 90, NULL, 0.00, 0.00, 0.00, 'en', NULL, 20, 11, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 01:42:29', '2023-11-21 01:42:38'),
(22, '20231121820421', 3, '19365141641631', 'Customer', 800, 16, 866, 866, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'CASH_ON_DELIVERY', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-cash-on-delivery', NULL, '2023-11-21 02:46:52', '2023-11-21 02:47:37'),
(23, '20231121316572', 3, '19365141641631', 'Customer', 800, 0, 800, 800, NULL, 0.00, 0.00, 0.00, 'en', NULL, 22, 5, 0, 'CASH_ON_DELIVERY', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-completed', 'payment-cash-on-delivery', NULL, '2023-11-21 02:46:52', '2023-11-21 02:47:09'),
(24, '20231121773316', 3, '19365141641631', 'Customer', 30, 1.6, 81.6, 81.6, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-21 10:25:38', '2023-11-21 10:37:46'),
(25, '20231121403368', 3, '19365141641631', 'Customer', 30, 0, 30, 30, NULL, 0.00, 0.00, 0.00, 'en', NULL, 24, 1, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:25:38', '2023-11-21 10:26:59'),
(26, '20231121655602', 3, '19365141641631', 'Customer', 120, 3.6, 173.6, 173.6, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:01', '2023-11-21 10:27:14'),
(27, '20231121290528', 3, '19365141641631', 'Customer', 120, 0, 120, 120, NULL, 0.00, 0.00, 0.00, 'en', NULL, 26, 6, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:01', '2023-11-21 10:27:14'),
(28, '20231121842836', 3, '19365141641631', 'Customer', 80, 17.98, 147.98, 147.98, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:43', '2023-11-21 10:28:01'),
(29, '20231121583084', 3, '19365141641631', 'Customer', 80, 0, 80, 80, NULL, 0.00, 0.00, 0.00, 'en', NULL, 28, 11, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:43', '2023-11-21 10:28:01'),
(30, '20231121416277', 3, '19365141641631', 'Customer', 2996, 17.98, 3063.98, 3063.98, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:45', '2023-11-21 10:28:06'),
(31, '20231121284250', 3, '19365141641631', 'Customer', 2996, 0, 2996, 2996, NULL, 0.00, 0.00, 0.00, 'en', NULL, 30, 10, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:45', '2023-11-21 10:28:06'),
(32, '20231121898606', 3, '19365141641631', 'Customer', 3500, 17.98, 3567.98, 3567.98, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:49', '2023-11-21 10:28:10'),
(33, '20231121805753', 3, '19365141641631', 'Customer', 3500, 0, 3500, 3500, NULL, 0.00, 0.00, 0.00, 'en', NULL, 32, 9, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:26:49', '2023-11-21 10:28:10'),
(34, '20231121241917', 3, '19365141641631', 'Customer', 20, 0, 0, 0, NULL, 87.98, 17.98, 50.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-cancelled', 'payment-success', NULL, '2023-11-21 10:26:51', '2023-11-21 10:38:24'),
(35, '20231121457017', 3, '19365141641631', 'Customer', 20, 0, 0, 0, NULL, 20.00, 0.00, 0.00, 'en', NULL, 34, 2, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-cancelled', 'payment-success', NULL, '2023-11-21 10:26:51', '2023-11-21 10:38:24'),
(36, '20231121761430', 3, '19365141641631', 'Customer', 180, 17.98, 247.98, 247.98, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:27:00', '2023-11-21 10:30:31'),
(37, '20231121315754', 3, '19365141641631', 'Customer', 180, 0, 180, 180, NULL, 0.00, 0.00, 0.00, 'en', NULL, 36, 8, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:27:00', '2023-11-21 10:30:31'),
(38, '20231121898061', 3, '19365141641631', 'Customer', 25, 17.98, 92.98, 92.98, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-success', NULL, '2023-11-21 10:27:04', '2023-11-21 10:39:55'),
(39, '20231121200980', 3, '19365141641631', 'Customer', 25, 0, 25, 25, NULL, 0.00, 0.00, 0.00, 'en', NULL, 38, 7, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-processing', 'payment-success', NULL, '2023-11-21 10:27:04', '2023-11-21 10:30:42'),
(40, '20231121721451', 3, '19365141641631', 'Customer', 1350, 27, 1427, 1427, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-at-local-facility', 'payment-pending', NULL, '2023-11-21 10:43:04', '2023-11-21 10:46:09'),
(41, '20231121278525', 3, '19365141641631', 'Customer', 1350, 0, 1350, 1350, NULL, 0.00, 0.00, 0.00, 'en', NULL, 40, 4, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-pending', 'payment-pending', NULL, '2023-11-21 10:43:04', '2023-11-21 10:43:04'),
(42, '20231121360684', 3, '19365141641631', 'Customer', 1350, 27, 1427, 1427, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-pending', 'payment-pending', NULL, '2023-11-21 10:44:38', '2023-11-21 10:44:38'),
(43, '20231121673899', 3, '19365141641631', 'Customer', 1350, 0, 1350, 1350, NULL, 0.00, 0.00, 0.00, 'en', NULL, 42, 4, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-pending', 'payment-pending', NULL, '2023-11-21 10:44:38', '2023-11-21 10:44:38'),
(44, '20231121977270', 3, '19365141641631', 'Customer', 300, 6, 356, 356, NULL, 0.00, 0.00, 0.00, 'en', NULL, NULL, NULL, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 50, 'Express Delivery', 'order-completed', 'payment-pending', NULL, '2023-11-21 10:51:13', '2023-11-21 11:55:27'),
(45, '20231121512622', 3, '19365141641631', 'Customer', 300, 0, 300, 300, NULL, 0.00, 0.00, 0.00, 'en', NULL, 44, 1, 0, 'STRIPE', NULL, '{\"zip\": \"10022\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"1780 Angus Road\"}', '{\"zip\": \"10001\", \"city\": \"New York\", \"state\": \"New York\", \"country\": \"US\", \"street_address\": \"260 Terry Lane\"}', NULL, 0, 'Express Delivery', 'order-completed', 'payment-pending', NULL, '2023-11-21 10:51:13', '2023-11-21 11:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
CREATE TABLE IF NOT EXISTS `order_product` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variation_option_id` bigint UNSIGNED DEFAULT NULL,
  `order_quantity` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` double NOT NULL,
  `subtotal` double NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_product_order_id_foreign` (`order_id`),
  KEY `order_product_product_id_foreign` (`product_id`),
  KEY `order_product_variation_option_id_foreign` (`variation_option_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_product`
--

INSERT INTO `order_product` (`id`, `order_id`, `product_id`, `variation_option_id`, `order_quantity`, `unit_price`, `subtotal`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, 13, 53, 95, '3', 150, 450, NULL, '2023-11-20 04:14:43', '2023-11-20 04:14:43'),
(4, 13, 31, 117, '2', 1800, 3600, NULL, '2023-11-20 04:14:43', '2023-11-20 04:14:43'),
(5, 14, 53, 95, '3', 150, 450, NULL, '2023-11-20 04:14:43', '2023-11-20 04:14:43'),
(6, 15, 31, 117, '2', 1800, 3600, NULL, '2023-11-20 04:14:43', '2023-11-20 04:14:43'),
(7, 16, 4, NULL, '1', 300, 300, NULL, '2023-11-20 14:13:23', '2023-11-20 14:13:23'),
(8, 17, 4, NULL, '1', 300, 300, NULL, '2023-11-20 14:13:23', '2023-11-20 14:13:23'),
(9, 18, 57, 100, '1', 90, 90, NULL, '2023-11-20 15:24:51', '2023-11-20 15:24:51'),
(10, 19, 57, 100, '1', 90, 90, NULL, '2023-11-20 15:24:51', '2023-11-20 15:24:51'),
(11, 20, 57, 100, '1', 90, 90, NULL, '2023-11-21 01:42:29', '2023-11-21 01:42:29'),
(12, 21, 57, 100, '1', 90, 90, NULL, '2023-11-21 01:42:29', '2023-11-21 01:42:29'),
(13, 22, 14, 142, '2', 400, 800, NULL, '2023-11-21 02:46:52', '2023-11-21 02:46:52'),
(14, 23, 14, 142, '2', 400, 800, NULL, '2023-11-21 02:46:52', '2023-11-21 02:46:52'),
(15, 24, 50, 93, '1', 30, 30, NULL, '2023-11-21 10:25:38', '2023-11-21 10:25:38'),
(16, 25, 50, 93, '1', 30, 30, NULL, '2023-11-21 10:25:38', '2023-11-21 10:25:38'),
(17, 26, 19, NULL, '1', 120, 120, NULL, '2023-11-21 10:26:01', '2023-11-21 10:26:01'),
(18, 27, 19, NULL, '1', 120, 120, NULL, '2023-11-21 10:26:01', '2023-11-21 10:26:01'),
(19, 28, 57, 108, '1', 80, 80, NULL, '2023-11-21 10:26:43', '2023-11-21 10:26:43'),
(20, 29, 57, 108, '1', 80, 80, NULL, '2023-11-21 10:26:43', '2023-11-21 10:26:43'),
(21, 30, 37, NULL, '1', 2996, 2996, NULL, '2023-11-21 10:26:45', '2023-11-21 10:26:45'),
(22, 31, 37, NULL, '1', 2996, 2996, NULL, '2023-11-21 10:26:45', '2023-11-21 10:26:45'),
(23, 32, 31, 151, '1', 3500, 3500, NULL, '2023-11-21 10:26:49', '2023-11-21 10:26:49'),
(24, 33, 31, 151, '1', 3500, 3500, NULL, '2023-11-21 10:26:49', '2023-11-21 10:26:49'),
(25, 34, 1, 2, '1', 20, 20, NULL, '2023-11-21 10:26:51', '2023-11-21 10:26:51'),
(26, 35, 1, 2, '1', 20, 20, NULL, '2023-11-21 10:26:51', '2023-11-21 10:26:51'),
(27, 36, 27, 62, '1', 180, 180, NULL, '2023-11-21 10:27:00', '2023-11-21 10:27:00'),
(28, 37, 27, 62, '1', 180, 180, NULL, '2023-11-21 10:27:00', '2023-11-21 10:27:00'),
(29, 38, 21, 44, '1', 25, 25, NULL, '2023-11-21 10:27:04', '2023-11-21 10:27:04'),
(30, 39, 21, 44, '1', 25, 25, NULL, '2023-11-21 10:27:04', '2023-11-21 10:27:04'),
(31, 40, 11, NULL, '1', 1350, 1350, NULL, '2023-11-21 10:43:04', '2023-11-21 10:43:04'),
(32, 41, 11, NULL, '1', 1350, 1350, NULL, '2023-11-21 10:43:04', '2023-11-21 10:43:04'),
(33, 42, 11, NULL, '1', 1350, 1350, NULL, '2023-11-21 10:44:38', '2023-11-21 10:44:38'),
(34, 43, 11, NULL, '1', 1350, 1350, NULL, '2023-11-21 10:44:38', '2023-11-21 10:44:38'),
(35, 44, 53, 95, '2', 150, 300, NULL, '2023-11-21 10:51:13', '2023-11-21 10:51:13'),
(36, 45, 53, 95, '2', 150, 300, NULL, '2023-11-21 10:51:13', '2023-11-21 10:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `order_wallet_points`
--

DROP TABLE IF EXISTS `order_wallet_points`;
CREATE TABLE IF NOT EXISTS `order_wallet_points` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` double DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_wallet_points_order_id_foreign` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ownership_transfers`
--

DROP TABLE IF EXISTS `ownership_transfers`;
CREATE TABLE IF NOT EXISTS `ownership_transfers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_identifier` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `to` bigint UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED NOT NULL,
  `status` enum('processing','approved','pending','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ownership_transfers_from_foreign` (`from`),
  KEY `ownership_transfers_shop_id_foreign` (`shop_id`),
  KEY `ownership_transfers_to_foreign` (`to`),
  KEY `ownership_transfers_created_by_foreign` (`created_by`),
  KEY `ownership_transfers_id_transaction_identifier_created_at_index` (`id`,`transaction_identifier`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
CREATE TABLE IF NOT EXISTS `participants` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `type` enum('shop','user') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `message_id` bigint UNSIGNED NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0',
  `last_read` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `participants_conversation_id_foreign` (`conversation_id`),
  KEY `participants_user_id_foreign` (`user_id`),
  KEY `participants_shop_id_foreign` (`shop_id`),
  KEY `participants_message_id_foreign` (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('vendor@demo.com', '7K1YNKmWPRJtyWHS', '2026-01-06 09:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_intents`
--

DROP TABLE IF EXISTS `payment_intents`;
CREATE TABLE IF NOT EXISTS `payment_intents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `tracking_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_gateway` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_intent_info` json DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_intents_order_id_foreign` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `method_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_gateway_id` bigint UNSIGNED DEFAULT NULL,
  `default_card` tinyint(1) DEFAULT '0',
  `fingerprint` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `network` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last4` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_check` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_methods_method_key_unique` (`method_key`),
  UNIQUE KEY `payment_methods_fingerprint_unique` (`fingerprint`),
  KEY `payment_methods_payment_gateway_id_foreign` (`payment_gateway_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'api', '2021-06-27 01:13:00', '2021-06-27 01:13:00'),
(2, 'customer', 'api', '2021-06-27 01:13:00', '2021-06-27 01:13:00'),
(3, 'store_owner', 'api', '2021-06-27 01:13:00', '2021-06-27 01:13:00'),
(4, 'staff', 'api', '2021-06-27 01:13:00', '2021-06-27 01:13:00'),
(5, 'editor', 'api', '2026-01-06 09:11:31', '2026-01-06 09:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(5, 'Marvel\\Database\\Models\\User', 8, 'auth_token', '6e3b8be5fc5658ad14b80b2cbbe306992e7919850e64b7e036fcf16f0b9c83bb', '[\"*\"]', '2025-12-02 10:04:24', NULL, '2025-12-02 10:04:11', '2025-12-02 10:04:24'),
(7, 'Marvel\\Database\\Models\\User', 10, 'auth_token', 'c3c4ac4aa443e114152b339b9550aebaa0d3e17396d460949db6b71c7037360f', '[\"*\"]', '2025-12-02 10:17:22', NULL, '2025-12-02 10:14:47', '2025-12-02 10:17:22'),
(8, 'Marvel\\Database\\Models\\User', 11, 'auth_token', '40da732b422d95977492059948f020bf0e1f8fccb0ba3d83ee3a9f1743c8758a', '[\"*\"]', NULL, NULL, '2026-01-06 09:07:30', '2026-01-06 09:07:30'),
(9, 'Marvel\\Database\\Models\\User', 14, 'auth_token', '6755c05e14c1eceaae7e80ed6548b78e1b68048664a6ac0904451328d535792a', '[\"*\"]', NULL, NULL, '2026-01-06 09:45:52', '2026-01-06 09:45:52'),
(10, 'Marvel\\Database\\Models\\User', 14, 'auth_token', '75fe88b4368fc43c560d175994d9c097a2404118314922c9c6be2c9fe6574fd5', '[\"*\"]', NULL, NULL, '2026-01-06 09:49:00', '2026-01-06 09:49:00'),
(11, 'Marvel\\Database\\Models\\User', 11, 'auth_token', '249210dffc75d60e242ff0aeb2e3adf4fc82fae328cd15c3833dafca806fa919', '[\"*\"]', NULL, NULL, '2026-01-10 13:13:34', '2026-01-10 13:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `person_product`
--

DROP TABLE IF EXISTS `person_product`;
CREATE TABLE IF NOT EXISTS `person_product` (
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `person_product_resource_id_foreign` (`resource_id`),
  KEY `person_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_location_product`
--

DROP TABLE IF EXISTS `pickup_location_product`;
CREATE TABLE IF NOT EXISTS `pickup_location_product` (
  `resource_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  KEY `pickup_location_product_resource_id_foreign` (`resource_id`),
  KEY `pickup_location_product_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_commissions`
--

DROP TABLE IF EXISTS `platform_commissions`;
CREATE TABLE IF NOT EXISTS `platform_commissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL COMMENT 'Commission percentage applied',
  `commission_amount` decimal(10,2) NOT NULL COMMENT 'Platform commission in currency',
  `shop_earnings` decimal(10,2) NOT NULL COMMENT 'Shop earnings after commission',
  `commission_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'tier or custom',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `platform_commissions_order_id_index` (`order_id`),
  KEY `platform_commissions_shop_id_index` (`shop_id`),
  KEY `platform_commissions_created_at_index` (`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type_id` bigint UNSIGNED NOT NULL,
  `price` double DEFAULT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `sale_price` double DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `min_price` double(8,2) DEFAULT NULL,
  `max_price` double(8,2) DEFAULT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `sold_quantity` int NOT NULL DEFAULT '0',
  `in_stock` tinyint(1) NOT NULL DEFAULT '1',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '0',
  `in_flash_sale` int NOT NULL DEFAULT '0',
  `shipping_class_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('under_review','approved','rejected','publish','unpublish','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('visibility_private','visibility_public','visibility_protected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'visibility_public',
  `product_type` enum('simple','variable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'simple',
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `height` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `length` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` json DEFAULT NULL,
  `video` json DEFAULT NULL,
  `gallery` json DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `author_id` bigint UNSIGNED DEFAULT NULL,
  `manufacturer_id` bigint UNSIGNED DEFAULT NULL,
  `is_digital` tinyint(1) NOT NULL DEFAULT '0',
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `external_product_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_product_button_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blocked_dates` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_type_id_foreign` (`type_id`),
  KEY `products_shipping_class_id_foreign` (`shipping_class_id`),
  KEY `products_shop_id_foreign` (`shop_id`),
  KEY `products_author_id_foreign` (`author_id`),
  KEY `products_manufacturer_id_foreign` (`manufacturer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `type_id`, `price`, `shop_id`, `sale_price`, `language`, `min_price`, `max_price`, `sku`, `quantity`, `sold_quantity`, `in_stock`, `is_taxable`, `in_flash_sale`, `shipping_class_id`, `status`, `visibility`, `product_type`, `unit`, `height`, `width`, `length`, `image`, `video`, `gallery`, `deleted_at`, `created_at`, `updated_at`, `author_id`, `manufacturer_id`, `is_digital`, `is_external`, `external_product_url`, `external_product_button_text`, `blocked_dates`) VALUES
(1, 'Hoppister Tops', 'hoppister-tops', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 13, NULL, 2, NULL, 'en', 20.00, 25.00, NULL, 1000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 301, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/301/Chawkbazar7.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/301/conversions/Chawkbazar7-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-10 12:08:54', '2021-12-14 06:05:17', NULL, NULL, 0, 0, NULL, NULL, NULL),
(2, 'Pike Green Thunder', 'pike-green-thunder', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 14, NULL, 2, NULL, 'en', 599.00, 2000.00, NULL, 2000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 Pair', NULL, NULL, NULL, '{\"id\": 299, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/299/Footwear-15.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/299/conversions/Footwear-15-thumbnail.jpg\"}', NULL, '[{\"id\": 300, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/300/Footwear-14.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/300/conversions/Footwear-14-thumbnail.jpg\"}]', NULL, '2021-10-10 12:17:39', '2021-12-14 06:05:49', NULL, NULL, 0, 0, NULL, NULL, NULL),
(3, 'Levi Blue top', 'levi-blue-top', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 16, NULL, 2, NULL, 'en', 180.00, 600.00, NULL, 3500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 97, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/97/A-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/97/conversions/A-2-thumbnail.jpg\"}', NULL, '[{\"id\": 99, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/99/A-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/99/conversions/A-1-thumbnail.jpg\"}, {\"id\": 100, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/100/A-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/100/conversions/A-2-thumbnail.jpg\"}, {\"id\": 106, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/106/A-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/106/conversions/A-3-thumbnail.jpg\"}]', NULL, '2021-10-10 15:57:17', '2021-12-14 06:06:34', NULL, NULL, 0, 0, NULL, NULL, NULL),
(4, 'Dido Pilot Glass', 'dido-pilot-glass', 'Polarized sunglasses reduce glare reflected off of roads, bodies of water, snow and other horizontal surfaces.Restore true color.Vision lenses are 400UV rated, meaning it can block UVA and UVB radiation.', 15, 350, 2, 300, 'en', 350.00, 350.00, 'kjkjnjk894561230', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 107, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/107/H-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/107/conversions/H-1-thumbnail.jpg\"}', NULL, '[{\"id\": 108, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/108/H.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/108/conversions/H-thumbnail.jpg\"}, {\"id\": 109, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/109/H-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/109/conversions/H-1-thumbnail.jpg\"}]', NULL, '2021-10-10 16:27:52', '2021-12-14 06:06:41', NULL, NULL, 0, 0, NULL, NULL, NULL),
(5, 'Hopister Yellow', 'hopister-yellow', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 13, NULL, 3, NULL, 'en', 80.00, 100.00, NULL, 1000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 Pcs', NULL, NULL, NULL, '{\"id\": 110, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/110/B.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/110/conversions/B-thumbnail.jpg\"}', NULL, '[{\"id\": 112, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/112/B-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/112/conversions/B-1-thumbnail.jpg\"}, {\"id\": 113, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/113/B-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/113/conversions/B-2-thumbnail.jpg\"}, {\"id\": 114, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/114/B-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/114/conversions/B-3-thumbnail.jpg\"}]', NULL, '2021-10-10 16:31:40', '2021-12-14 06:06:49', NULL, NULL, 0, 0, NULL, NULL, NULL),
(6, 'Tippot Classic', 'tippot-classic', 'The new-model Submariner now features Rolex’s powerhouse calibre 3235 Perpetual movement. An upgrade from the calibre 3135 movement,', 1, 1250, 3, 1200, 'en', 1250.00, 1250.00, 'sdgiaogkdaovmalkm', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 115, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/115/B-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/115/conversions/B-3-thumbnail.jpg\"}', NULL, '[{\"id\": 117, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/117/B-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/117/conversions/B-1-thumbnail.jpg\"}, {\"id\": 118, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/118/B-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/118/conversions/B-2-thumbnail.jpg\"}, {\"id\": 119, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/119/B-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/119/conversions/B-3-thumbnail.jpg\"}]', NULL, '2021-10-10 16:34:45', '2021-12-14 06:06:55', NULL, NULL, 0, 0, NULL, NULL, NULL),
(7, 'Darmani Woolen Comfort', 'darmani-woolen-comfort', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 11, NULL, 3, NULL, 'en', 500.00, 800.00, NULL, 4500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 120, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/120/A.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/120/conversions/A-thumbnail.jpg\"}', NULL, '[{\"id\": 123, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/123/A-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/123/conversions/A-2-thumbnail.jpg\"}, {\"id\": 124, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/124/A-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/124/conversions/A-3-thumbnail.jpg\"}]', NULL, '2021-10-10 16:38:32', '2021-12-14 06:07:33', NULL, NULL, 0, 0, NULL, NULL, NULL),
(8, 'P & M Tokyo Talkies', 'p-m-tokyo-talkies', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 3, NULL, 3, NULL, 'en', 50.00, 1500.00, NULL, 3000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 125, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/125/E--1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/125/conversions/E--1-thumbnail.jpg\"}', NULL, '[{\"id\": 128, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/128/E--3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/128/conversions/E--3-thumbnail.jpg\"}, {\"id\": 129, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/129/E-4.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/129/conversions/E-4-thumbnail.jpg\"}]', NULL, '2021-10-10 16:45:43', '2021-12-14 06:08:31', NULL, NULL, 0, 0, NULL, NULL, NULL),
(9, 'Pior Womes Bangles', 'pior-womes-bangles', 'Structured buffed nappa leather top handle bag in ‘scarlet’ red. Carry handle at top. Detachable and adjustable shoulder strap with lanyard clasp fastening.', 6, 1200, 4, 1150, 'en', 1200.00, 1200.00, 'sdvvsdf4544ddfgh+', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pcs', NULL, NULL, NULL, '{\"id\": 130, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/130/D---2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/130/conversions/D---2-thumbnail.jpg\"}', NULL, '[{\"id\": 132, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/132/D--1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/132/conversions/D--1-thumbnail.jpg\"}]', NULL, '2021-10-10 16:47:11', '2021-12-14 06:09:06', NULL, NULL, 0, 0, NULL, NULL, NULL),
(10, 'Tuma Style Cap', 'tuma-style-cap', 'Structured buffed nappa leather top handle bag in ‘scarlet’ red. Carry handle at top. Detachable and adjustable shoulder strap with lanyard clasp fastening.', 12, 170, 4, 150, 'en', 170.00, 170.00, '+898998', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 Pc', NULL, NULL, NULL, '{\"id\": 296, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/296/2-0%281%29.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/296/conversions/2-0%281%29-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-10 16:50:30', '2021-12-14 06:09:11', NULL, NULL, 0, 0, NULL, NULL, NULL),
(11, 'Tay Ben Aviator', 'tay-ben-aviator', 'Polarized sunglasses reduce glare reflected off of roads, bodies of water, snow and other horizontal surfaces.Restore true color.Vision lenses are 400UV rated, meaning it can block UVA and UVB radiation.', 3, 1500, 4, 1350, 'en', 1500.00, 1500.00, '1500654545', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 295, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/295/400.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/295/conversions/400-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-10 16:53:57', '2021-12-14 06:09:16', NULL, NULL, 0, 0, NULL, NULL, NULL),
(12, 'Zara Army Bag', 'zara-army-bag', 'Structured buffed nappa leather top handle bag in ‘scarlet’ red. Carry handle at top. Detachable and adjustable shoulder strap with lanyard clasp fastening.', 8, 300, 4, 260, 'en', 300.00, 300.00, 'h3', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 292, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/292/10.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/292/conversions/10-thumbnail.jpg\"}', NULL, '[{\"id\": 139, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/139/H-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/139/conversions/H-1-thumbnail.jpg\"}, {\"id\": 140, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/140/H-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/140/conversions/H-2-thumbnail.jpg\"}, {\"id\": 141, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/141/H-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/141/conversions/H-3-thumbnail.jpg\"}]', NULL, '2021-10-10 18:49:55', '2021-12-14 06:09:37', NULL, NULL, 0, 0, NULL, NULL, NULL),
(13, 'Pissot Super Dry', 'pissot-super-dry', 'The new-model Submariner now features Rolex’s powerhouse calibre 3235 Perpetual movement. An upgrade from the calibre 3135 movement, it now features a more efficient skeletonized Chronergy escapement and longer power reserve.', 6, 280, 5, 250, 'en', 280.00, 280.00, '89657412330', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 322, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/322/watch.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/322/conversions/watch-thumbnail.jpg\"}', NULL, '[{\"id\": 143, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/143/F.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/143/conversions/F-thumbnail.jpg\"}, {\"id\": 144, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/144/F-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/144/conversions/F-1-thumbnail.jpg\"}]', NULL, '2021-10-11 10:14:25', '2022-03-02 02:47:50', NULL, NULL, 0, 0, NULL, NULL, NULL),
(14, 'Tuma Grey', 'tuma-grey', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 7, NULL, 5, NULL, 'en', 400.00, 1000.00, NULL, 3000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pair', NULL, NULL, NULL, '{\"id\": 294, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/294/30-%281%29.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/294/conversions/30-%281%29-thumbnail.jpg\"}', NULL, '[{\"id\": 147, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/147/E-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/147/conversions/E-1-thumbnail.jpg\"}]', NULL, '2021-10-11 10:16:13', '2021-12-14 06:10:38', NULL, NULL, 0, 0, NULL, NULL, NULL),
(15, 'Neutral Scoop Neck Top', 'neutral-scoop-neck-top', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use', 3, NULL, 5, NULL, 'en', 30.00, 1000.00, NULL, 3500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 148, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/148/women5-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/148/conversions/women5-1-thumbnail.jpg\"}', NULL, '[{\"id\": 150, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/150/women-14-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/150/conversions/women-14-1-thumbnail.jpg\"}, {\"id\": 151, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/151/women-17-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/151/conversions/women-17-1-thumbnail.jpg\"}, {\"id\": 152, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/152/women-18-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/152/conversions/women-18-1-thumbnail.jpg\"}]', NULL, '2021-10-11 10:24:46', '2021-12-14 06:11:41', NULL, NULL, 0, 0, NULL, NULL, NULL),
(16, 'Paddidas Grey T shirt', 'paddidas-grey-t-shirt', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use. Casual wear became popular within the Western world', 13, NULL, 5, NULL, 'en', 20.00, 1000.00, NULL, 3500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 153, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/153/G.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/153/conversions/G-thumbnail.jpg\"}', NULL, '[{\"id\": 156, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/156/G-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/156/conversions/G-2-thumbnail.jpg\"}, {\"id\": 157, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/157/G-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/157/conversions/G-3-thumbnail.jpg\"}]', NULL, '2021-10-11 10:56:12', '2021-12-14 06:12:16', NULL, NULL, 0, 0, NULL, NULL, NULL),
(17, 'Vittione Highlander', 'vittione-highlander', 'Fendi began life in 1925 as a fur and leather speciality store in Rome.', 10, NULL, 6, NULL, 'en', 750.00, 800.00, NULL, 1000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 158, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/158/Chawkbazar13.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/158/conversions/Chawkbazar13-thumbnail.jpg\"}', NULL, '[{\"id\": 160, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/160/Chawkbazar14.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/160/conversions/Chawkbazar14-thumbnail.jpg\"}, {\"id\": 161, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/161/Chawkbazar15.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/161/conversions/Chawkbazar15-thumbnail.jpg\"}, {\"id\": 162, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/162/Chawkbazar16.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/162/conversions/Chawkbazar16-thumbnail.jpg\"}]', NULL, '2021-10-11 11:30:59', '2021-12-14 06:12:25', NULL, NULL, 0, 0, NULL, NULL, NULL),
(18, 'Pucchi Fasion watch', 'pucchi-fasion-watch', 'The 2020 Submariner Rolex is now powered by the calibre 3230 Perpetual movement, a brand-new movement that incorporates a Chronergy escapement', 1, 1200, 6, 1000, 'en', 1200.00, 1200.00, '/89465+21320', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 163, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/163/Watches-7.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/163/conversions/Watches-7-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-11 11:33:30', '2021-12-14 06:15:17', NULL, NULL, 0, 0, NULL, NULL, NULL),
(19, 'Parmani Submariner', 'parmani-submariner', 'The 2020 Submariner Rolex is now powered by the calibre 3230 Perpetual movement, a brand-new movement that incorporates a Chronergy escapement', 12, 1500, 6, 120, 'en', 1500.00, 1500.00, '5/9784615', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 165, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/165/Watches-10.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/165/conversions/Watches-10-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-11 11:37:33', '2021-12-14 06:15:23', NULL, NULL, 0, 0, NULL, NULL, NULL),
(20, 'Black Crew V neck Tops', 'black-crew-v-neck-tops', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 4, NULL, 6, NULL, 'en', 22.00, 30.00, NULL, 1500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 167, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/167/Women.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/167/conversions/Women-thumbnail.jpg\"}', NULL, '[{\"id\": 169, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/169/Women-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/169/conversions/Women-2-thumbnail.jpg\"}, {\"id\": 170, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/170/Women-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/170/conversions/Women-3-thumbnail.jpg\"}]', NULL, '2021-10-11 11:39:30', '2021-12-14 06:15:29', NULL, NULL, 0, 0, NULL, NULL, NULL),
(21, 'Pk Warm Stripes', 'pk-warm-stripes', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 10, NULL, 7, NULL, 'en', 18.00, 40.00, NULL, 1000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 171, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/171/F-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/171/conversions/F-2-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-11 11:41:08', '2021-12-14 06:15:43', NULL, NULL, 0, 0, NULL, NULL, NULL),
(22, 'Funder Armor Yellow Tops', 'funder-armor-yellow-tops', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 16, NULL, 7, NULL, 'en', 30.00, 35.00, NULL, 1500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pcs', NULL, NULL, NULL, '{\"id\": 173, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/173/C.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/173/conversions/C-thumbnail.jpg\"}', NULL, '[{\"id\": 175, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/175/C-2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/175/conversions/C-2-thumbnail.jpg\"}, {\"id\": 176, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/176/C-3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/176/conversions/C-3-thumbnail.jpg\"}]', NULL, '2021-10-11 11:42:33', '2021-12-14 06:15:50', NULL, NULL, 0, 0, NULL, NULL, NULL),
(23, 'Tuma Kidsa bag', 'tuma-kidsa-bag', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 3, NULL, 7, NULL, 'en', 40.00, 50.00, NULL, 1500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pcs', NULL, NULL, NULL, '{\"id\": 177, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/177/j.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/177/conversions/j-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-11 11:44:01', '2021-12-14 06:15:58', NULL, NULL, 0, 0, NULL, NULL, NULL),
(24, 'Chevis Womens Bag', 'chevis-womens-bag', 'Fendi began life in 1925 as a fur and leather speciality store in Rome. Despite growing into one of the world’s most renowned luxury labels, the business has retained its family feel, with a focus on fine detail, Italian craftsmanship and the support of local artisans.', 5, NULL, 7, NULL, 'en', 75.00, 80.00, NULL, 1500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 179, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/179/Backpack-4.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/179/conversions/Backpack-4-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-11 11:45:37', '2021-12-14 06:16:05', NULL, NULL, 0, 0, NULL, NULL, NULL),
(25, 'Addidas FuelCell Propel V2 Running Shoes', 'addidas-fuelcell-propel-v2-running-shoes', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 7, NULL, 8, NULL, 'en', 45.00, 50.00, NULL, 2000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pair', NULL, NULL, NULL, '{\"id\": 323, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/323/Footwear.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/323/conversions/Footwear-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 10:40:16', '2022-03-02 02:49:59', NULL, NULL, 0, 0, NULL, NULL, NULL),
(26, 'Alex Maqueeen Shoulder Bag', 'alex-maqueeen-shoulder-bag', 'Luxury British fashion house Alexander McQueen is famed for its exquisitely designed handbags and accessories, as showcased through this stunning black Box bag.', 16, 250, 8, 220, 'en', 250.00, 250.00, '8468fas4d86f4asd8fsdafsdaf+', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 181, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/181/Backpack-8.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/181/conversions/Backpack-8-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 10:47:15', '2021-12-14 06:16:21', NULL, NULL, 0, 0, NULL, NULL, NULL),
(27, 'Armani Retaliate Shoes', 'armani-retaliate-shoes', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 7, NULL, 8, NULL, 'en', 180.00, 200.00, NULL, 2000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pair', NULL, NULL, NULL, '{\"id\": 182, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/182/Footwear-3-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/182/conversions/Footwear-3-1-thumbnail.jpg\"}', NULL, '[{\"id\": 183, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/183/Footwear-2-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/183/conversions/Footwear-2-1-thumbnail.jpg\"}]', NULL, '2021-10-23 10:50:44', '2021-12-14 06:16:30', NULL, NULL, 0, 0, NULL, NULL, NULL),
(28, 'Armani 269S Sunglasses', 'armani-269s-sunglasses', 'Polarized sunglasses reduce glare reflected off of roads, bodies of water, snow and other horizontal surfaces.Restore true color.Vision lenses are 400UV rated, meaning it can block UVA and UVB radiation.', 13, 120, 8, 80, 'en', 120.00, 120.00, 'asdaeq34234sdasdasd', 500, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 184, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/184/Sunglasess-12-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/184/conversions/Sunglasess-12-1-thumbnail.jpg\"}', NULL, '[{\"id\": 185, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/185/Sunglasess-13-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/185/conversions/Sunglasess-13-1-thumbnail.jpg\"}]', NULL, '2021-10-23 13:19:23', '2021-12-14 06:16:38', NULL, NULL, 0, 0, NULL, NULL, NULL),
(29, 'Armani Checked Shirt', 'armani-checked-shirt', 'Children’s clothing/ kids wear is usually more casual than adult clothing, fit play and rest. Hosiery is usually used. More recently, however, tons of childrenswear is heavily influenced by trends in adult fashion', 16, NULL, 8, NULL, 'en', 500.00, 900.00, NULL, 300, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 186, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/186/kids-11.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/186/conversions/kids-11-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 17:07:58', '2022-03-02 02:49:24', NULL, NULL, 0, 0, NULL, NULL, NULL),
(30, 'Chanel Shoulder Bag', 'chanel-shoulder-bag', '100% Authenticity Guaranteed Chanel Classic Jumbo Single Flap Black Caviar Shoulder Bag', 12, 1500, 9, 1300, 'en', 1500.00, 1500.00, 'adsasfsdar34543654fddsfdsf', 300, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 188, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/188/Backpack-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/188/conversions/Backpack-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 17:13:50', '2021-12-14 06:16:52', NULL, NULL, 0, 0, NULL, NULL, NULL),
(31, 'Converse Blazing Black', 'converse-blazing-black', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 14, NULL, 9, NULL, 'en', 1800.00, 5000.00, NULL, 2650, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 189, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/189/Footwear-4-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/189/conversions/Footwear-4-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 17:18:14', '2021-12-14 06:16:58', NULL, NULL, 0, 0, NULL, NULL, NULL),
(32, 'Givenchy Shoulder Bag', 'givenchy-shoulder-bag', 'Established in 1952, Givenchy’s stance on contemporary elegance is perfectly captured through the brand’s premium accessory collections. Crafted from calf leather.', 8, 1500, 9, 1450, 'en', 1500.00, 1500.00, 'sadasds342343fsdfsdf', 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 191, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/191/Backpack-5.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/191/conversions/Backpack-5-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 17:21:20', '2021-12-14 06:17:04', NULL, NULL, 0, 0, NULL, NULL, NULL),
(33, 'Gucci Challenger', 'gucci-challenger', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use', 15, NULL, 9, NULL, 'en', 899.00, 1000.00, NULL, 200, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 192, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/192/women9-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/192/conversions/women9-1-thumbnail.jpg\"}', NULL, '[{\"id\": 193, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/193/women-22-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/193/conversions/women-22-1-thumbnail.jpg\"}]', NULL, '2021-10-23 17:57:31', '2021-12-14 06:17:10', NULL, NULL, 0, 0, NULL, NULL, NULL),
(34, 'H & Dri-FIT Fleece', 'h-dri-fit-fleece', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use', 13, 650, 9, 550, 'en', 650.00, 650.00, 'asadq24234sadasd', 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 194, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/194/women10%402x-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/194/conversions/women10%402x-1-thumbnail.jpg\"}', NULL, '[{\"id\": 195, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/195/women-13-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/195/conversions/women-13-1-thumbnail.jpg\"}]', NULL, '2021-10-23 18:10:22', '2021-12-14 06:17:15', NULL, NULL, 0, 0, NULL, NULL, NULL),
(35, 'H&M Boys Top', 'h-m-boys-top', 'Children’s clothing/ kids wear is usually more casual than adult clothing, fit play and rest. Hosiery is usually used. More recently, however, tons of childrenswear is heavily influenced by trends in adult fashion', 9, NULL, 10, NULL, 'en', 350.00, 1000.00, NULL, 1100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 196, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/196/kids-4.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/196/conversions/kids-4-thumbnail.jpg\"}', NULL, '[{\"id\": 197, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/197/kids-5.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/197/conversions/kids-5-thumbnail.jpg\"}]', NULL, '2021-10-23 18:14:02', '2021-12-14 06:17:20', NULL, NULL, 0, 0, NULL, NULL, NULL),
(36, 'Hermes Carlton London', 'hermes-carlton-london', 'Off-White self-striped knitted midi A-line dress, has a scoop neck, sleeveless, straight hem', 6, NULL, 10, NULL, 'en', 300.00, 650.00, NULL, 2700, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 198, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/198/Grid-14.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/198/conversions/Grid-14-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:17:24', '2021-12-14 06:17:26', NULL, NULL, 0, 0, NULL, NULL, NULL),
(37, 'Hermes Galaxy Watch 3', 'hermes-galaxy-watch-3', 'The Original watch featuring polished rose gold stainless steel case, black dial with minimalist rose gold markers, and a black genuine leather band. The Horse logo lettering on dial and at buckle closure.', 4, 3200, 10, 2996, 'en', 3200.00, 3200.00, 'sadsafsr234234sdfsdsd', 15, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 200, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/200/Watches-16.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/200/conversions/Watches-16-thumbnail.jpg\"}', NULL, '[{\"id\": 201, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/201/Watches-16.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/201/conversions/Watches-16-thumbnail.jpg\"}]', NULL, '2021-10-23 18:19:15', '2021-12-14 06:17:31', NULL, NULL, 0, 0, NULL, NULL, NULL),
(38, 'Hermes Grey', 'hermes-grey', 'Children’s clothing/ kids wear is usually more casual than adult clothing, fit play and rest. Hosiery is usually used. More recently, however, tons of childrenswear is heavily influenced by trends in adult fashion', 10, 650, 10, 620, 'en', 650.00, 650.00, 'adasds324234fdsfsdf', 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 202, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/202/kids-17.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/202/conversions/kids-17-thumbnail.jpg\"}', NULL, '[{\"id\": 203, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/203/kids-23.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/203/conversions/kids-23-thumbnail.jpg\"}]', NULL, '2021-10-23 18:20:55', '2021-12-14 06:19:56', NULL, NULL, 0, 0, NULL, NULL, NULL),
(39, 'Hermes179S Sunglasses', 'hermes179s-sunglasses', 'Polarized sunglasses reduce glare reflected off of roads, bodies of water, snow and other horizontal surfaces.Restore true color.Vision lenses are 400UV rated, meaning it can block UVA and UVB radiation.', 15, 250, 10, 230, 'en', 250.00, 250.00, 'saffrwe435tgdfhdf', 250, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 204, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/204/Sunglasess-15-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/204/conversions/Sunglasess-15-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:22:59', '2021-12-14 06:19:51', NULL, NULL, 0, 0, NULL, NULL, NULL),
(40, 'Hipster Hexagonal Polarized Sunglasses', 'hipster-hexagonal-polarized-sunglasses', 'The Original watch featuring polished rose gold stainless steel case, black dial with minimalist rose gold markers, and a black genuine leather band. The Horse logo lettering on dial and at buckle closure.', 2, 300, 3, 279, 'en', 300.00, 300.00, 'dsafra453tgv', 497, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 205, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/205/Sunglasess-5-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/205/conversions/Sunglasess-5-1-thumbnail.jpg\"}', NULL, '[{\"id\": 206, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/206/Sunglasess-6.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/206/conversions/Sunglasess-6-thumbnail.jpg\"}]', NULL, '2021-10-23 18:25:08', '2021-12-14 06:19:45', NULL, NULL, 0, 0, NULL, NULL, NULL),
(41, 'Louise Vuitton Highlander', 'louise-vuitton-highlander', 'Fendi began life in 1925 as a fur and leather speciality store in Rome.', 11, NULL, 3, NULL, 'en', 950.00, 1150.00, NULL, 698, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 207, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/207/Chawkbazar13.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/207/conversions/Chawkbazar13-thumbnail.jpg\"}', NULL, '[{\"id\": 208, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/208/Chawkbazar14.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/208/conversions/Chawkbazar14-thumbnail.jpg\"}]', NULL, '2021-10-23 18:30:19', '2021-12-14 06:19:40', NULL, NULL, 0, 0, NULL, NULL, NULL),
(42, 'Louise Vutton Feel the Air', 'louise-vutton-feel-the-air', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 14, NULL, 11, NULL, 'en', 240.00, 260.00, NULL, 1200, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 209, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/209/Footwear-17.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/209/conversions/Footwear-17-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:33:38', '2021-12-14 06:19:34', NULL, NULL, 0, 0, NULL, NULL, NULL),
(43, 'Louise Vutton Pure Black Shirt', 'louise-vutton-pure-black-shirt', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use. Casual wear became popular within the Western world', 1, NULL, 11, NULL, 'en', 75.00, 90.00, NULL, 700, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 210, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/210/mens-9.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/210/conversions/mens-9-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:36:54', '2021-12-14 06:19:28', NULL, NULL, 0, 0, NULL, NULL, NULL),
(44, 'Mac Nordace Laptop Bag', 'mac-nordace-laptop-bag', 'Bewitching black, plush padding and faux-fur lining surround and cradle your 15.6 macbook™ in trendsetting luxury. It is the perfect accessory for every season and all occasions.', 8, 550, 11, 500, 'en', 550.00, 550.00, 'sdfas4335sddasd', 50, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 212, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/212/Backpack-6.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/212/conversions/Backpack-6-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:40:27', '2021-12-14 06:19:23', NULL, NULL, 0, 0, NULL, NULL, NULL),
(45, 'Maniac Red Boys', 'maniac-red-boys', 'Sporty essentials, these Under Armour athletic shorts are smooth and lightweight in moisture-wicking material.', 3, 15, 1, 12, 'en', 15.00, 15.00, 'ghfhg765675fhgfhg', 48, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 213, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/213/Chawkbazar17.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/213/conversions/Chawkbazar17-thumbnail.jpg\"}', NULL, '[{\"id\": 214, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/214/Chawkbazar20.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/214/conversions/Chawkbazar20-thumbnail.jpg\"}]', NULL, '2021-10-23 18:42:55', '2021-12-14 06:19:18', NULL, NULL, 0, 0, NULL, NULL, NULL),
(46, 'Nike Aviator', 'nike-aviator', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 7, NULL, 1, NULL, 'en', 160.00, 180.00, NULL, 650, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 215, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/215/Footwear-9.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/215/conversions/Footwear-9-thumbnail.jpg\"}', NULL, '[{\"id\": 216, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/216/Footwear-8.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/216/conversions/Footwear-8-thumbnail.jpg\"}]', NULL, '2021-10-23 18:49:36', '2021-12-14 06:19:13', NULL, NULL, 0, 0, NULL, NULL, NULL),
(47, 'Nike Black', 'nike-black', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use. Casual wear became popular within the Western world', 12, NULL, 1, NULL, 'en', 100.00, 120.00, NULL, 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 217, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/217/mens-2.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/217/conversions/mens-2-thumbnail.jpg\"}', NULL, '[{\"id\": 218, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/218/mens-7.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/218/conversions/mens-7-thumbnail.jpg\"}]', NULL, '2021-10-23 18:53:15', '2021-12-14 06:19:07', NULL, NULL, 0, 0, NULL, NULL, NULL),
(48, 'Nike Car Wheel Watch', 'nike-car-wheel-watch', 'The Original watch featuring polished rose gold stainless steel case, black dial with minimalist rose gold markers, and a black genuine leather band. The Horse logo lettering on dial and at buckle closure.', 6, 250, 1, 230, 'en', 250.00, 250.00, 'sfsdfdfg4354354sfdsdf', 50, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 219, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/219/Watches-6-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/219/conversions/Watches-6-1-thumbnail.jpg\"}', NULL, '[{\"id\": 220, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/220/Watches-7-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/220/conversions/Watches-7-1-thumbnail.jpg\"}]', NULL, '2021-10-23 18:56:11', '2021-12-14 06:19:02', NULL, NULL, 0, 0, NULL, NULL, NULL),
(49, 'Nike Comfy Vapor Maxpro', 'nike-comfy-vapor-maxpro', 'Footwear refers to garments worn on the feet, which originally serves to purpose of protection against adversities of the environment, usually regarding ground textures and temperature.', 7, NULL, 1, NULL, 'en', 220.00, 250.00, 'sdfsdfsd43435dsdasd', 2000, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 321, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/321/Footwear-1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/321/conversions/Footwear-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 18:59:10', '2022-03-02 02:47:28', NULL, NULL, 0, 0, NULL, NULL, NULL),
(50, 'Nike Pro Mesh Top with Leggins', 'nike-pro-mesh-top-with-leggins', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use', 2, NULL, 1, NULL, 'en', 30.00, 35.00, NULL, 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 223, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/223/Casual-Wear-4-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/223/conversions/Casual-Wear-4-1-thumbnail.jpg\"}', NULL, '[{\"id\": 224, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/224/Casual-Wear-5-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/224/conversions/Casual-Wear-5-1-thumbnail.jpg\"}]', NULL, '2021-10-23 19:03:36', '2021-12-14 06:18:51', NULL, NULL, 0, 0, NULL, NULL, NULL),
(51, 'Philip Lim Leather Shoulder Bag', 'philip-lim-leather-shoulder-bag', 'Structured buffed nappa leather top handle bag in ‘scarlet’ red. Carry handle at top. Detachable and adjustable shoulder strap with lanyard clasp fastening.', 8, 260, 1, 250, 'en', 260.00, 260.00, 'sadsade3432435654gfdg', 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 225, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/225/Backpack-7.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/225/conversions/Backpack-7-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 19:05:45', '2021-12-14 06:18:46', NULL, NULL, 0, 0, NULL, NULL, NULL),
(52, 'Reyban Havana Phantos Sunglasses', 'reyban-havana-phantos-sunglasses', 'Polarized sunglasses reduce glare reflected off of roads, bodies of water, snow and other horizontal surfaces.Restore true color.Vision lenses are 400UV rated, meaning it can block UVA and UVB radiation.', 9, 100, 1, 80, 'en', 100.00, 100.00, 'ffgd56tgdfsd', 50, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 226, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/226/Sunglasess-2-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/226/conversions/Sunglasess-2-1-thumbnail.jpg\"}', NULL, '[{\"id\": 227, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/227/Sunglasess-3-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/227/conversions/Sunglasess-3-1-thumbnail.jpg\"}]', NULL, '2021-10-23 19:09:25', '2021-12-14 06:18:41', NULL, NULL, 0, 0, NULL, NULL, NULL),
(53, 'Roadster Women Round Neck', 'roadster-women-round-neck', 'Fendi began life in 1925 as a fur and leather speciality store in Rome.', 11, NULL, 1, NULL, 'en', 150.00, 200.00, NULL, 100, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 228, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/228/Chawkbazar22.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/228/conversions/Chawkbazar22-thumbnail.jpg\"}', NULL, '[{\"id\": 229, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/229/Chawkbazar21.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/229/conversions/Chawkbazar21-thumbnail.jpg\"}]', NULL, '2021-10-23 19:12:41', '2021-12-14 06:18:36', NULL, NULL, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `type_id`, `price`, `shop_id`, `sale_price`, `language`, `min_price`, `max_price`, `sku`, `quantity`, `sold_quantity`, `in_stock`, `is_taxable`, `in_flash_sale`, `shipping_class_id`, `status`, `visibility`, `product_type`, `unit`, `height`, `width`, `length`, `image`, `video`, `gallery`, `deleted_at`, `created_at`, `updated_at`, `author_id`, `manufacturer_id`, `is_digital`, `is_external`, `external_product_url`, `external_product_button_text`, `blocked_dates`) VALUES
(54, 'The Horse Original', 'the-horse-original', 'The Original watch featuring polished rose gold stainless steel case, black dial with minimalist rose gold markers, and a black genuine leather band. The Horse logo lettering on dial and at buckle closure.', 4, 200, 11, 190, 'en', 200.00, 200.00, 'csdcsd77sdasda', 250, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 230, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/230/Watches-4-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/230/conversions/Watches-4-1-thumbnail.jpg\"}', NULL, '[{\"id\": 231, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/231/Watches-4-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/231/conversions/Watches-4-1-thumbnail.jpg\"}]', NULL, '2021-10-23 19:14:33', '2021-12-14 06:18:31', NULL, NULL, 0, 0, NULL, NULL, NULL),
(55, 'White Oxford Shirt', 'white-oxford-shirt', 'Casual wear (casual attire or clothing) may be a Western code that’s relaxed, occasional, spontaneous and fitted to everyday use', 1, NULL, 11, NULL, 'en', 10.00, 40.00, NULL, 1080, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 232, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/232/Casual-Wear-1-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/232/conversions/Casual-Wear-1-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 19:18:00', '2021-12-14 06:18:27', NULL, NULL, 0, 0, NULL, NULL, NULL),
(56, 'Zara Miss Chase', 'zara-miss-chase', 'Fendi began life in 1925 as a fur and leather speciality store in Rome.', 2, 100, 11, 90, 'en', 100.00, 100.00, 'cscascas67789adasd', 120, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'simple', '1 pc', NULL, NULL, NULL, '{\"id\": 234, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/234/Chawkbazar1.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/234/conversions/Chawkbazar1-thumbnail.jpg\"}', NULL, '[{\"id\": 235, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/235/Chawkbazar2.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/235/conversions/Chawkbazar2-thumbnail.jpg\"}]', NULL, '2021-10-23 19:20:09', '2021-12-14 06:18:21', NULL, NULL, 0, 0, NULL, NULL, NULL),
(57, 'Zara Monte Carlo', 'zara-monte-carlo', 'Children’s clothing/ kids wear is usually more casual than adult clothing, fit play and rest. Hosiery is usually used. More recently, however, tons of childrenswear is heavily influenced by trends in adult fashion', 10, NULL, 11, NULL, 'en', 80.00, 100.00, NULL, 740, 0, 1, 0, 0, NULL, 'publish', 'visibility_public', 'variable', '1 pc', NULL, NULL, NULL, '{\"id\": 236, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/236/kids-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/236/conversions/kids-1-thumbnail.jpg\"}', NULL, '[]', NULL, '2021-10-23 19:22:38', '2021-12-14 06:18:15', NULL, NULL, 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products_meta`
--

DROP TABLE IF EXISTS `products_meta`;
CREATE TABLE IF NOT EXISTS `products_meta` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'null',
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_meta_product_id_foreign` (`product_id`),
  KEY `products_meta_key_index` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_tag`
--

DROP TABLE IF EXISTS `product_tag`;
CREATE TABLE IF NOT EXISTS `product_tag` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `tag_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_tag_product_id_foreign` (`product_id`),
  KEY `product_tag_tag_id_foreign` (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_tag`
--

INSERT INTO `product_tag` (`id`, `product_id`, `tag_id`) VALUES
(1, 1, 13),
(2, 1, 14),
(3, 1, 7),
(4, 2, 2),
(5, 2, 12),
(6, 2, 13),
(7, 3, 5),
(9, 6, 9),
(10, 12, 10),
(11, 12, 9),
(12, 11, 9),
(13, 11, 10),
(14, 11, 8),
(15, 11, 7),
(16, 10, 5),
(17, 10, 12),
(18, 10, 14),
(19, 10, 9),
(20, 10, 10),
(21, 12, 6),
(22, 11, 4),
(23, 9, 12),
(24, 9, 9),
(25, 9, 7),
(26, 8, 13),
(27, 8, 9),
(28, 8, 7),
(29, 7, 14),
(30, 7, 9),
(31, 7, 7),
(32, 6, 5),
(33, 6, 8),
(34, 6, 7),
(35, 5, 13),
(36, 5, 12),
(37, 5, 9),
(38, 5, 7),
(39, 4, 11),
(40, 4, 9),
(41, 4, 4),
(42, 4, 2),
(43, 4, 8),
(44, 3, 9),
(45, 3, 7),
(46, 13, 10),
(47, 13, 2),
(48, 14, 12),
(49, 14, 8),
(50, 14, 10),
(51, 15, 13),
(52, 15, 12),
(53, 15, 7),
(54, 16, 13),
(55, 16, 8),
(56, 17, 14),
(57, 17, 8),
(59, 19, 12),
(60, 19, 8),
(61, 19, 2),
(63, 19, 7),
(64, 20, 5),
(65, 20, 13),
(66, 20, 7),
(67, 21, 13),
(68, 21, 12),
(69, 21, 8),
(70, 22, 13),
(71, 22, 12),
(72, 22, 7),
(73, 23, 6),
(74, 23, 11),
(75, 24, 12),
(76, 24, 6),
(77, 24, 7),
(78, 23, 9),
(79, 25, 13),
(80, 25, 12),
(81, 25, 8),
(82, 25, 2),
(83, 26, 12),
(84, 26, 6),
(85, 27, 13),
(86, 27, 12),
(87, 27, 2),
(88, 28, 12),
(89, 28, 8),
(90, 28, 7),
(91, 29, 13),
(92, 29, 11),
(93, 30, 12),
(94, 30, 6),
(95, 31, 12),
(96, 31, 8),
(97, 32, 12),
(98, 32, 13),
(99, 33, 7),
(100, 34, 7),
(101, 35, 11),
(102, 36, 13),
(103, 36, 7),
(104, 37, 8),
(105, 38, 14),
(106, 39, 12),
(107, 39, 2),
(108, 40, 13),
(109, 40, 12),
(110, 41, 14),
(111, 42, 12),
(112, 42, 8),
(113, 43, 13),
(114, 43, 8),
(115, 44, 12),
(116, 44, 6),
(117, 45, 13),
(118, 46, 12),
(119, 46, 2),
(120, 47, 13),
(121, 47, 8),
(122, 48, 8),
(123, 49, 13),
(124, 49, 12),
(125, 50, 13),
(126, 50, 7),
(128, 52, 4),
(130, 53, 13),
(131, 53, 7),
(132, 54, 8),
(133, 54, 7),
(134, 55, 13),
(135, 55, 7),
(136, 56, 2),
(137, 56, 7),
(138, 57, 13),
(139, 57, 11),
(140, 37, 15),
(141, 36, 15),
(142, 35, 15),
(143, 34, 15),
(144, 33, 15),
(145, 32, 15),
(146, 31, 15),
(147, 30, 15),
(148, 29, 15),
(149, 28, 15),
(150, 26, 15),
(151, 24, 15),
(152, 51, 6),
(153, 18, 8),
(154, 18, 13),
(155, 9, 10),
(157, 49, 10),
(158, 25, 10);

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE IF NOT EXISTS `providers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `provider_user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `providers_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_user_id_foreign` (`user_id`),
  KEY `questions_shop_id_foreign` (`shop_id`),
  KEY `questions_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

DROP TABLE IF EXISTS `refunds`;
CREATE TABLE IF NOT EXISTS `refunds` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL DEFAULT '0',
  `status` enum('approved','pending','rejected','processing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `images` json DEFAULT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `refund_policy_id` bigint UNSIGNED DEFAULT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `refund_reason_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refunds_order_id_foreign` (`order_id`),
  KEY `refunds_customer_id_foreign` (`customer_id`),
  KEY `refunds_shop_id_foreign` (`shop_id`),
  KEY `refunds_refund_policy_id_foreign` (`refund_policy_id`),
  KEY `refunds_refund_reason_id_foreign` (`refund_reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refund_policies`
--

DROP TABLE IF EXISTS `refund_policies`;
CREATE TABLE IF NOT EXISTS `refund_policies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target` enum('vendor','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vendor',
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `status` enum('approved','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refund_policies_slug_unique` (`slug`),
  KEY `refund_policies_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `refund_policies`
--

INSERT INTO `refund_policies` (`id`, `title`, `slug`, `description`, `target`, `language`, `status`, `shop_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Vendor Return Policy', 'vendor-return-policy', 'Our vendor return policy ensures that you can return products within 30 days of purchase if they are damaged or not as described.', 'vendor', 'en', 'approved', 1, '2025-12-02 08:55:18', NULL, NULL),
(2, 'Customer Return Policy', 'customer-return-policy', 'Our customer return policy allows you to return products within 14 days of purchase for a full refund, no questions asked.', 'customer', 'en', 'approved', 2, '2025-12-02 08:55:18', NULL, NULL),
(3, 'Electronics Return Policy', 'electronics-return-policy', 'For electronics, our return policy extends to 60 days. We stand by the quality of our electronic products.', 'customer', 'en', 'approved', 1, '2025-12-02 08:55:18', NULL, NULL),
(4, 'Furniture Return Policy', 'furniture-return-policy', 'Our furniture return policy allows you to return furniture within 7 days if it doesn\'t meet your expectations. Customer satisfaction is our priority.', 'customer', 'en', 'approved', 1, '2025-12-02 08:55:18', NULL, NULL),
(5, 'Custom Orders Policy', 'custom-orders-policy', 'Please note that custom orders are not eligible for returns or refunds. We craft custom items to your specifications.', 'customer', 'en', 'approved', 2, '2025-12-02 08:55:18', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `refund_reasons`
--

DROP TABLE IF EXISTS `refund_reasons`;
CREATE TABLE IF NOT EXISTS `refund_reasons` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `refund_reasons`
--

INSERT INTO `refund_reasons` (`id`, `name`, `slug`, `language`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Product Not as Described', 'product-not-as-described', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(2, 'Wrong Item Shipped', 'wrong-item-shipped', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(3, 'Damaged Item', 'damaged-item', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(4, 'Cancelled Order', 'cancelled-order', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(5, 'Late Delivery', 'late-delivery', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(6, 'Item Not Needed', 'item-not-needed', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(7, 'Changed Mind', 'changed-mind', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL),
(8, 'Others', 'others', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
CREATE TABLE IF NOT EXISTS `resources` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `image` json DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `price` double DEFAULT NULL,
  `type` enum('DROPOFF_LOCATION','PICKUP_LOCATION','PERSON','DEPOSIT','FEATURES') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variation_option_id` bigint UNSIGNED DEFAULT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` double DEFAULT NULL,
  `photos` json DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_shop_id_foreign` (`shop_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_order_id_foreign` (`order_id`),
  KEY `reviews_variation_option_id_foreign` (`variation_option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'api', '2023-08-11 08:57:33', '2023-08-11 08:57:33'),
(2, 'store_owner', 'api', '2023-08-11 08:57:33', '2023-08-11 08:57:33'),
(3, 'staff', 'api', '2023-08-11 08:57:33', '2023-08-11 08:57:33'),
(4, 'customer', 'api', '2023-08-11 08:57:33', '2023-08-11 08:57:33'),
(5, 'editor', 'api', '2026-01-06 09:11:31', '2026-01-06 09:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(3, 1),
(3, 2),
(4, 3),
(5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `options` json NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_language_unique` (`language`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `options`, `language`, `created_at`, `updated_at`) VALUES
(1, '{\"seo\": {\"ogImage\": null, \"ogTitle\": null, \"metaTags\": null, \"metaTitle\": null, \"canonicalUrl\": null, \"ogDescription\": null, \"twitterHandle\": null, \"metaDescription\": null, \"twitterCardType\": null}, \"logo\": {\"id\": \"258\", \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/258/logo-final2x.png\", \"file_name\": \"logo-final2x.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/258/conversions/logo-final2x-thumbnail.jpg\"}, \"useAi\": false, \"useOtp\": false, \"currency\": \"USD\", \"siteLink\": \"https://chawkbazar.redq.io/\", \"smsEvent\": {\"admin\": {\"refundOrder\": false, \"paymentOrder\": false, \"statusChangeOrder\": false}, \"vendor\": {\"refundOrder\": false, \"paymentOrder\": false, \"statusChangeOrder\": false}, \"customer\": {\"refundOrder\": false, \"paymentOrder\": false, \"statusChangeOrder\": false}}, \"taxClass\": \"1\", \"defaultAi\": \"openai\", \"siteTitle\": \"ChawkBazar\", \"emailEvent\": {\"admin\": {\"refundOrder\": false, \"paymentOrder\": false, \"statusChangeOrder\": false}, \"vendor\": {\"refundOrder\": false, \"createReview\": false, \"paymentOrder\": false, \"createQuestion\": false, \"statusChangeOrder\": false}, \"customer\": {\"refundOrder\": false, \"paymentOrder\": false, \"answerQuestion\": false, \"statusChangeOrder\": false}}, \"promoPopup\": {\"image\": {\"id\": 327, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/327/Chawkbazar-img.png\", \"file_name\": \"Chawkbazar-img.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/327/conversions/Chawkbazar-img-thumbnail.jpg\"}, \"title\": \"Get 25% Discount\", \"popUpDelay\": 5000, \"description\": \"Subscribe to the mailing list to receive updates on new arrivals, special offers and our promotions.\", \"popUpNotShow\": {\"title\": \"Don\'t show this popup again\", \"popUpExpiredIn\": 7}, \"isPopUpNotShow\": true, \"popUpExpiredIn\": 1}, \"enableTerms\": true, \"maintenance\": {\"image\": {\"id\": 346, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/346/background.png\", \"file_name\": \"background.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/346/conversions/background-thumbnail.jpg\"}, \"start\": \"2025-12-02T10:55:18.403556Z\", \"title\": \"Site is under Maintenance\", \"until\": \"2025-12-03T10:55:18.403569Z\", \"description\": \"We are currently undergoing essential maintenance to elevate your browsing experience. Our team is working diligently to implement improvements that will bring you an even more seamless and enjoyable interaction with our site. During this period, you may experience temporary inconveniences. We appreciate your patience and understanding. Thank you for being a part of our community, and we look forward to unveiling the enhanced features and content soon.\", \"aboutUsTitle\": \"About Us\", \"overlayColor\": null, \"buttonTitleOne\": \"Notify Me\", \"buttonTitleTwo\": \"Contact Us\", \"contactUsTitle\": \"Contact Us\", \"isOverlayColor\": false, \"newsLetterTitle\": \"Subscribe Newsletter\", \"overlayColorRange\": null, \"aboutUsDescription\": \"Welcome to Chawkbazar, your go-to destination for curated excellence. Discover a fusion of style, quality, and affordability in every click. Join our community and elevate your shopping experience with us!\", \"newsLetterDescription\": \"Stay in the loop! Subscribe to our newsletter for exclusive deals and the latest trends delivered straight to your inbox. Elevate your shopping experience with insider access.\"}, \"server_info\": {\"memory_limit\": \"128M\", \"post_max_size\": 8192, \"max_input_time\": \"-1\", \"max_execution_time\": \"0\", \"upload_max_filesize\": 2048}, \"app_settings\": {\"trust\": true, \"last_checking_time\": \"2025-12-02T10:55:08.327860Z\"}, \"collapseLogo\": {\"id\": 345, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/345/favicon-black.png\", \"file_name\": \"favicon-black.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/345/conversions/favicon-black-thumbnail.jpg\"}, \"deliveryTime\": [{\"title\": \"Express Delivery\", \"description\": \"90 min express delivery\"}, {\"title\": \"Morning\", \"description\": \"8.00 AM - 11.00 AM\"}, {\"title\": \"Noon\", \"description\": \"11.00 AM - 2.00 PM\"}, {\"title\": \"Afternoon\", \"description\": \"2.00 PM - 5.00 PM\"}, {\"title\": \"Evening\", \"description\": \"5.00 PM - 8.00 PM\"}], \"externalLink\": \"https://redq.io\", \"externalText\": \"REDQ\", \"freeShipping\": false, \"isPromoPopUp\": true, \"reviewSystem\": {\"name\": \"Give purchased product a review only for one time. (By default)\", \"value\": \"review_single_time\"}, \"signupPoints\": 100, \"siteSubtitle\": \"Your next ecommerce\", \"useGoogleMap\": false, \"copyrightText\": \"Copyright © REDQ. All rights reserved worldwide.\", \"enableCoupons\": true, \"guestCheckout\": true, \"shippingClass\": \"1\", \"StripeCardOnly\": false, \"contactDetails\": {\"contact\": \"+129290122122\", \"socials\": [{\"url\": \"https://www.facebook.com/redqinc\", \"icon\": \"FacebookIcon\"}, {\"url\": \"https://twitter.com/RedqTeam\", \"icon\": \"TwitterIcon\"}, {\"url\": \"https://www.instagram.com/redqteam\", \"icon\": \"InstagramIcon\"}], \"website\": \"https://redq.io\", \"location\": {\"lat\": 42.9585979, \"lng\": -76.9087202, \"zip\": null, \"city\": null, \"state\": \"NY\", \"country\": \"United States\", \"formattedAddress\": \"NY State Thruway, New York, USA\"}, \"emailAddress\": \"demo@demo.com\"}, \"paymentGateway\": [{\"name\": \"stripe\", \"title\": \"Stripe\"}], \"currencyOptions\": {\"formation\": \"en-US\", \"fractions\": 2}, \"isProductReview\": false, \"maxShopDistance\": 1000, \"pushNotification\": {\"all\": {\"order\": false, \"message\": false, \"storeNotice\": false}}, \"useEnableGateway\": false, \"enableReviewPopup\": false, \"useCashOnDelivery\": true, \"freeShippingAmount\": 0, \"isUnderMaintenance\": false, \"minimumOrderAmount\": 0, \"useMustVerifyEmail\": false, \"maximumQuestionLimit\": 5, \"currencyToWalletRatio\": 3, \"isMultiCommissionRate\": false, \"mailchimpSubscribeText\": \"Thank you for subscribing\", \"enableEmailForDigitalProduct\": false}', 'en', '2025-12-02 08:55:18', '2025-12-02 08:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_classes`
--

DROP TABLE IF EXISTS `shipping_classes`;
CREATE TABLE IF NOT EXISTS `shipping_classes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `is_global` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `type` enum('fixed','percentage','free_shipping','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_classes`
--

INSERT INTO `shipping_classes` (`id`, `name`, `amount`, `is_global`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Global', 50, '1', 'fixed', '2021-10-25 02:06:16', '2021-10-25 02:06:16'),
(2, 'Free Shippping To USA', 3, '1', 'free_shipping', '2021-11-28 05:36:37', '2021-11-28 05:37:22');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
CREATE TABLE IF NOT EXISTS `shops` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` json DEFAULT NULL,
  `logo` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `address` json DEFAULT NULL,
  `settings` json DEFAULT NULL,
  `notifications` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shops_owner_id_foreign` (`owner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `owner_id`, `name`, `slug`, `description`, `cover_image`, `logo`, `is_active`, `address`, `settings`, `notifications`, `created_at`, `updated_at`) VALUES
(1, 1, 'Blythe Knowles', 'chic-haven-boutique', 'Iste dolor quaerat u', '{\"id\": 9, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/9/Untitled-4.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/9/conversions/Untitled-4-thumbnail.jpg\"}', '{\"id\": 333, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/333/logo04.png\", \"file_name\": \"logo04.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/333/conversions/logo04-thumbnail.jpg\"}', 1, '{\"zip\": \"50848\", \"city\": \"Nisi esse voluptate\", \"state\": \"Voluptas natus et re\", \"country\": \"Expedita tempora occ\", \"street_address\": \"Qui vel non sunt as\"}', '{\"contact\": \"2779888888888\", \"socials\": [], \"website\": \"https://www.wypiceky.me\", \"location\": [], \"notifications\": []}', NULL, '2021-10-09 13:24:30', '2023-11-22 10:01:47'),
(2, 1, 'Urban Threads Emporium', 'urban-threads-emporium', 'Elevate your urban style at Urban Threads Emporium.From streetwear essentials to statement pieces, our emporium offers a diverse range of contemporary fashion.Unleash your individuality with our carefully curated selection.', '{\"id\": 16, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/16/Untitled-3.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/16/conversions/Untitled-3-thumbnail.jpg\"}', '{\"id\": 15, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/15/fashion.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/15/conversions/fashion-thumbnail.jpg\"}', 1, '{\"zip\": \"02210\", \"city\": \"Boston\", \"state\": \"Massachusetts\", \"country\": \"USA\", \"street_address\": \"4360 Hampton Meadows\"}', '{\"contact\": \"01236547852\", \"socials\": [], \"website\": null, \"location\": []}', NULL, '2021-10-09 13:57:34', '2023-11-21 10:19:36'),
(3, 1, 'Anthony Dudley', 'velvet-vogue-closet', 'Quod aliquid et mini', '{\"id\": 338, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/338/shop-banner011.png\", \"file_name\": \"shop-banner011.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/338/conversions/shop-banner011-thumbnail.jpg\"}', '{\"id\": 342, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/342/logo08.png\", \"file_name\": \"logo08.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/342/conversions/logo08-thumbnail.jpg\"}', 1, '{\"zip\": \"96275\", \"city\": \"Laboris officiis sun\", \"state\": \"Reprehenderit ipsum\", \"country\": \"Veniam inventore od\", \"street_address\": \"Necessitatibus quia\"}', '{\"contact\": \"214876556\", \"socials\": [{\"url\": \"Iusto illum dolor o\", \"icon\": \"InstagramIcon\"}], \"website\": \"https://www.ketyky.org.au\", \"location\": [], \"notifications\": []}', NULL, '2021-06-27 00:46:14', '2023-11-22 10:04:56'),
(4, 1, 'Boho Bliss Emporium', 'boho-bliss-emporium', 'The clothing shop is the best shop around the city. This is being run under the store owner and our aim is to provide quality product and hassle free customer service.', '{\"id\": \"886\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/884/Untitled-4.jpg\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/884/conversions/Untitled-4-thumbnail.jpg\"}', '{\"id\": \"896\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/894/fashion.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/894/conversions/fashion-thumbnail.jpg\"}', 1, '{\"zip\": \"62656\", \"city\": \"Lincoln\", \"state\": \"Illinois\", \"country\": \"USA\", \"street_address\": \"4885  Spring Street\"}', '{\"contact\": \"212901921221\", \"socials\": [{\"url\": \"https://www.facebook.com/\", \"icon\": \"FacebookIcon\"}], \"website\": \"https://redq.io\", \"location\": {\"lat\": 40.1576691, \"lng\": -89.38529779999999, \"city\": \"Lincoln\", \"state\": \"IL\", \"country\": \"United States\", \"formattedAddress\": \"IL-121, Lincoln, IL, USA\"}, \"notifications\": {\"email\": null}}', NULL, '2021-06-27 00:47:10', '2023-11-21 10:19:51'),
(5, 1, 'Sleek Streetwear Co.', 'sleek-streetwear-co', 'Step into urban sophistication with Sleek Streetwear Co. Our curated collection blends street style with sleek design, offering a range of contemporary streetwear. Redefine your urban wardrobe with our stylish essentials.', '{\"id\": \"889\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/887/Untitled-1-%281%29.jpg\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/887/conversions/Untitled-1-%281%29-thumbnail.jpg\"}', '{\"id\": \"888\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/886/Backpack.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/886/conversions/Backpack-thumbnail.jpg\"}', 1, '{\"zip\": \"35203\", \"city\": \"Michigan\", \"state\": \"Alabama\", \"country\": \"USA\", \"street_address\": \"1740  Bedford Street\"}', '{\"contact\": \"212901921221\", \"socials\": [{\"url\": \"https://www.facebook.com/\", \"icon\": \"FacebookIcon\"}, {\"url\": \"https://www.instagram.com/\", \"icon\": \"InstagramIcon\"}], \"website\": \"https://redq.io\", \"location\": {\"lat\": -37.1374024, \"lng\": 174.9685924, \"zip\": \"2579\", \"city\": \"Ramarama\", \"state\": \"Auckland\", \"country\": \"New Zealand\", \"formattedAddress\": \"Waharau Lane, Ramarama 2579, New Zealand\"}, \"notifications\": {\"email\": null}}', NULL, '2021-06-27 00:47:23', '2023-11-21 10:19:48'),
(6, 1, 'Ethereal Essence Boutique', 'ethereal-essence-boutique', 'Discover ethereal beauty at Ethereal Essence Boutique. Our collection of exquisite designs and delicate silhouettes transports you to a realm of timeless elegance. Embrace the enchantment of fashion with our ethereal pieces.', '{\"id\": \"890\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/888/Untitled-3.jpg\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/888/conversions/Untitled-3-thumbnail.jpg\"}', '{\"id\": \"891\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/889/Makeup.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/889/conversions/Makeup-thumbnail.jpg\"}', 1, '{\"zip\": \"70001\", \"city\": \"Metairie\", \"state\": \"Louisiana\", \"country\": \"USA\", \"street_address\": \"2960  Rose Avenue\"}', '{\"contact\": \"7196321822\", \"socials\": [{\"url\": \"https://www.instagram.com/\", \"icon\": \"InstagramIcon\"}, {\"url\": \"https://www.twitter.com/\", \"icon\": \"TwitterIcon\"}], \"website\": \"https://redq.io\", \"location\": {\"lat\": 51.5176117, \"lng\": -0.210149, \"state\": \"England\", \"country\": \"United Kingdom\", \"formattedAddress\": \"Ladbroke Grove, London, UK\"}, \"notifications\": {\"email\": null}}', NULL, '2021-06-27 00:47:49', '2023-11-21 10:19:46'),
(7, 1, 'Xena Ochoa', 'casual-comfort-corner', 'Explicabo Aut aliqu', '{\"id\": 336, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/336/shop-banner07.png\", \"file_name\": \"shop-banner07.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/336/conversions/shop-banner07-thumbnail.jpg\"}', '{\"id\": 344, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/344/logo4.png\", \"file_name\": \"logo4.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/344/conversions/logo4-thumbnail.jpg\"}', 1, '{\"zip\": \"97375\", \"city\": \"Aut perferendis haru\", \"state\": \"Voluptatem consequat\", \"country\": \"Ipsam enim elit acc\", \"street_address\": \"Quod accusantium pra\"}', '{\"contact\": \"743000007766\", \"socials\": [{\"url\": \"Ipsam delectus occa\", \"icon\": \"FacebookIcon\"}, {\"url\": \"Culpa corrupti qui\", \"icon\": \"InstagramIcon\"}], \"website\": \"https://www.wohykulomiqin.cc\", \"location\": [], \"notifications\": []}', NULL, '2021-06-27 00:48:11', '2023-11-23 01:57:48'),
(8, 1, 'Lavinia Burch', 'velvet-vibes-emporium', 'Qui eum dicta asperi', '{\"id\": 334, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/334/shop-banner04.png\", \"file_name\": \"shop-banner04.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/334/conversions/shop-banner04-thumbnail.jpg\"}', '{\"id\": \"893\", \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/891/Group-36321.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/891/conversions/Group-36321-thumbnail.jpg\"}', 1, '{\"zip\": \"49970\", \"city\": \"Provident do in del\", \"state\": \"Adipisci aliquip odi\", \"country\": \"Sint totam inventor\", \"street_address\": \"Sed dolores sit des\"}', '{\"contact\": \"7399999999999\", \"socials\": [{\"url\": \"Incidunt commodo vi\", \"icon\": \"FacebookIcon\"}, {\"url\": \"Illum ut molestias\", \"icon\": \"InstagramIcon\"}, {\"url\": \"Fuga Voluptas et qu\", \"icon\": \"TwitterIcon\"}], \"website\": \"https://www.nog.ca\", \"location\": [], \"notifications\": {\"email\": null}}', NULL, '2021-06-27 00:48:23', '2023-11-22 10:02:32'),
(9, 1, 'Claire Miranda', 'denim-delight-co', 'Et consequatur sunt', '{\"id\": 332, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/332/shop-banner03.png\", \"file_name\": \"shop-banner03.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/332/conversions/shop-banner03-thumbnail.jpg\"}', '{\"id\": 1613, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1613/Publisher-logo.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1613/conversions/Publisher-logo-thumbnail.jpg\"}', 1, '{\"zip\": \"72091\", \"city\": \"Et eveniet omnis ad\", \"state\": \"Nobis et asperiores\", \"country\": \"Officiis consectetur\", \"street_address\": \"Eum quisquam irure m\"}', '{\"contact\": \"5799789787\", \"socials\": [], \"website\": \"https://www.bawiwomanor.org\", \"location\": [], \"notifications\": {\"email\": null}}', NULL, '2021-12-07 14:47:07', '2023-11-22 10:01:11'),
(10, 1, 'Drake Cain', 'quirk-and-charm-boutique', 'Lorem sapiente accus', '{\"id\": 1723, \"original\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1723/Gadget-banner.png\", \"file_name\": \"Gadget-banner.png\", \"thumbnail\": \"https://pickbazarlaravel.s3.ap-southeast-1.amazonaws.com/1723/conversions/Gadget-banner-thumbnail.jpg\"}', '{\"id\": 343, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/343/logo5.png\", \"file_name\": \"logo5.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/343/conversions/logo5-thumbnail.jpg\"}', 1, '{\"zip\": \"22395\", \"city\": \"Voluptatem amet po\", \"state\": \"Odio aut ea neque re\", \"country\": \"Occaecat ut excepteu\", \"street_address\": \"Voluptatem deleniti\"}', '{\"contact\": \"78298867\", \"socials\": [], \"website\": \"https://www.puk.biz\", \"location\": [], \"notifications\": {\"email\": null}}', NULL, '2023-10-02 05:38:16', '2023-11-23 01:56:51'),
(11, 1, 'Marny Rose', 'cozy-couture-corner', 'Voluptatem odio qui', '{\"id\": 329, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/329/shop-banner010.png\", \"file_name\": \"shop-banner010.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/329/conversions/shop-banner010-thumbnail.jpg\"}', '{\"id\": 328, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/328/logo01.png\", \"file_name\": \"logo01.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/328/conversions/logo01-thumbnail.jpg\"}', 1, '{\"zip\": \"25196\", \"city\": \"Laboris voluptatibus\", \"state\": \"Ut rerum necessitati\", \"country\": \"Quae qui dolore ea s\", \"street_address\": \"Quis eligendi aliqua\"}', '{\"contact\": \"53290798686\", \"socials\": [], \"website\": \"https://www.wajok.mobi\", \"location\": [], \"notifications\": {\"email\": null}}', NULL, '2023-10-02 14:42:55', '2023-11-22 09:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `store_notices`
--

DROP TABLE IF EXISTS `store_notices`;
CREATE TABLE IF NOT EXISTS `store_notices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `priority` enum('high','medium','low') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `notice` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `effective_from` datetime NOT NULL DEFAULT '2025-12-02 10:55:13',
  `expired_at` datetime NOT NULL,
  `type` enum('all_vendor','specific_vendor','all_shop','specific_shop') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_notices_created_by_foreign` (`created_by`),
  KEY `store_notices_updated_by_foreign` (`updated_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_notice_read`
--

DROP TABLE IF EXISTS `store_notice_read`;
CREATE TABLE IF NOT EXISTS `store_notice_read` (
  `store_notice_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  KEY `store_notice_read_store_notice_id_foreign` (`store_notice_id`),
  KEY `store_notice_read_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_notice_shop`
--

DROP TABLE IF EXISTS `store_notice_shop`;
CREATE TABLE IF NOT EXISTS `store_notice_shop` (
  `store_notice_id` bigint UNSIGNED DEFAULT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  KEY `store_notice_shop_store_notice_id_foreign` (`store_notice_id`),
  KEY `store_notice_shop_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `store_notice_user`
--

DROP TABLE IF EXISTS `store_notice_user`;
CREATE TABLE IF NOT EXISTS `store_notice_user` (
  `store_notice_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  KEY `store_notice_user_store_notice_id_foreign` (`store_notice_id`),
  KEY `store_notice_user_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` json DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `language`, `icon`, `image`, `details`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'Sports', 'new-sports', 'en', NULL, '{\"id\": 17, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/17/chawkb.sports.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/17/conversions/chawkb.sports-thumbnail.jpg\"}', NULL, '2021-10-09 14:17:51', '2021-10-09 14:17:51', NULL),
(4, 'Sunglass', 'exclusive-sunglasses', 'en', NULL, '{\"id\": 20, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/20/chawkbsunglass.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/20/conversions/chawkbsunglass-thumbnail.jpg\"}', NULL, '2021-10-09 14:21:17', '2021-10-09 14:21:17', NULL),
(5, 'Coupons', 'product-coupons', 'en', NULL, '{\"id\": 21, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/21/chawkbcoupond.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/21/conversions/chawkbcoupond-thumbnail.jpg\"}', NULL, '2021-10-09 14:21:38', '2021-10-09 16:19:47', NULL),
(6, 'Backpack', 'new-backpack', 'en', 'HandBags', '{\"id\": 22, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/22/chawkbbackpack.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/22/conversions/chawkbbackpack-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:21:58', '2021-11-29 04:01:58', NULL),
(7, 'Women\'s Collection', 'womens-collection', 'en', 'WomenDress', '{\"id\": 23, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/23/chawkbwomen.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/23/conversions/chawkbwomen-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:22:18', '2021-11-29 04:02:04', NULL),
(8, 'Men\'s Collection', 'mens-collection', 'en', 'Pants', '{\"id\": 25, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/25/Banner-5.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/25/conversions/Banner-5-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:24:03', '2021-11-29 04:02:09', NULL),
(9, 'Flash Sale', 'flash-sale', 'en', 'Accessories', '[]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:26:01', '2021-11-29 04:02:13', NULL),
(10, 'Featured Products', 'featured-products', 'en', 'Accessories', '[]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:41:16', '2021-11-29 04:02:22', NULL),
(11, 'Kids Collection', 'kids-collection', 'en', 'Skirts', '{\"id\": 34, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/34/banner-mobile-3.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/34/conversions/banner-mobile-3-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:52:56', '2021-11-29 04:02:30', NULL),
(12, 'Winter Collection', 'winter-collection', 'en', 'Accessories', '{\"id\": 35, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/35/banner-1.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/35/conversions/banner-1-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:53:38', '2021-11-29 04:02:33', NULL),
(13, 'Gift Collection', 'gift-collection', 'en', 'ShoulderBags', '{\"id\": 36, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/36/banner-2.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/36/conversions/banner-2-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:54:07', '2021-11-29 04:02:43', NULL),
(14, 'Winter Offer', 'winter-offer', 'en', 'Accessories', '{\"id\": 37, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/37/banner-3.jpg\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/37/conversions/banner-3-thumbnail.jpg\"}', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-09 14:54:24', '2021-11-29 04:02:47', NULL),
(15, 'On Sale', 'on-sale', 'en', 'Accessories', '[]', 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine.', '2021-10-26 04:24:07', '2021-11-29 04:02:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tax_classes`
--

DROP TABLE IF EXISTS `tax_classes`;
CREATE TABLE IF NOT EXISTS `tax_classes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` double NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_global` int DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `on_shipping` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_classes`
--

INSERT INTO `tax_classes` (`id`, `country`, `state`, `zip`, `city`, `rate`, `name`, `is_global`, `priority`, `on_shipping`, `created_at`, `updated_at`) VALUES
(1, 'United States', 'ny', '10001', 'ny', 2, 'Global', NULL, NULL, 1, '2021-10-25 02:05:58', '2021-11-28 05:25:09'),
(2, 'USA', 'NY', '1001', 'NY', 5, 'USA Tax', NULL, NULL, 1, '2021-11-28 05:38:04', '2021-11-28 05:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `terms_and_conditions`
--

DROP TABLE IF EXISTS `terms_and_conditions`;
CREATE TABLE IF NOT EXISTS `terms_and_conditions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `terms_and_conditions_user_id_foreign` (`user_id`),
  KEY `terms_and_conditions_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terms_and_conditions`
--

INSERT INTO `terms_and_conditions` (`id`, `user_id`, `shop_id`, `title`, `slug`, `description`, `type`, `issued_by`, `is_approved`, `language`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Disclaimers and Limitation of Liability', 'disclaimers-and-limitation-of-liability', 'The Website is provided \"as is\" and \"as available\" without any warranties, either expressed or implied. Pickbazar shall not be liable for any direct, indirect, incidental, special, consequential, or punitive damages resulting from the use or inability to use the Website.', 'global', 'Super Admin', 1, 'en', NULL, '2025-12-02 08:55:18', NULL),
(2, 1, NULL, 'Intellectual Property', 'intellectual-property', 'The Website and its original content, features, and functionality are owned by [Your Company] and are protected by international copyright, trademark, and other intellectual property laws.', 'global', 'Super Admin', 1, 'en', NULL, '2025-12-02 08:55:18', NULL),
(3, 1, NULL, 'Privacy Policy', 'privacy-policy', 'Your use of the Website is also governed by our Privacy Policy, which can be found [link to Privacy Policy]. By using the Website, you consent to the practices described in the Privacy Policy.', 'global', 'Super Admin', 1, 'en', NULL, '2025-12-02 08:55:18', NULL),
(4, 1, NULL, 'Use of the Website', 'use-of-the-website', 'You must be at least [Age] years old to use this Website. By using the Website, you represent and warrant that you are at least [Age] years old. You agree to use the Website for lawful purposes only and in a manner that does not infringe upon the rights of others.', 'global', 'Super Admin', 1, 'en', NULL, '2025-12-02 08:55:18', NULL),
(5, 1, NULL, 'Acceptance of Terms', 'acceptance-of-terms', 'By using this Website, you agree to comply with and be bound by these terms and conditions. If you do not agree to these terms, please do not use the Website.', 'global', 'Super Admin', 1, 'en', NULL, '2025-12-02 08:55:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `settings` json DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promotional_sliders` json DEFAULT NULL,
  `images` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`, `settings`, `slug`, `language`, `icon`, `promotional_sliders`, `images`, `created_at`, `updated_at`) VALUES
(1, 'Fusion', '[]', 'fusion', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 41, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/41/adidas.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/41/conversions/adidas-thumbnail.jpg\"}, {\"id\": 42, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/42/fustion.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/42/conversions/fustion-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 284, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/284/Group-36184.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/284/conversions/Group-36184-thumbnail.jpg\"}]}]', '2021-10-10 10:31:55', '2021-11-29 03:59:03'),
(2, 'Vintgae', '[]', 'vintgae', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 43, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/43/puma-logo.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/43/conversions/puma-logo-thumbnail.jpg\"}, {\"id\": 44, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/44/vintege.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/44/conversions/vintege-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 285, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/285/Group-36183.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/285/conversions/Group-36183-thumbnail.jpg\"}]}]', '2021-10-10 10:50:14', '2021-11-29 03:58:59'),
(3, 'Masteriod', '[]', 'masteriod', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 45, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/45/dior.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/45/conversions/dior-thumbnail.jpg\"}, {\"id\": 46, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/46/logo3.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/46/conversions/logo3-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 286, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/286/Group-36186.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/286/conversions/Group-36186-thumbnail.jpg\"}]}]', '2021-10-10 10:50:57', '2021-11-29 03:58:56'),
(4, 'Hoppister', '[]', 'hoppister', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 47, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/47/levi-s.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/47/conversions/levi-s-thumbnail.jpg\"}, {\"id\": 48, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/48/logo4.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/48/conversions/logo4-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 287, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/287/Group-36185.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/287/conversions/Group-36185-thumbnail.jpg\"}]}]', '2021-10-10 10:51:31', '2021-11-29 03:58:53'),
(5, 'Klien Shoes', '[]', 'klien-shoes', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 53, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/53/Calvin-klein.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/53/conversions/Calvin-klein-thumbnail.jpg\"}, {\"id\": 54, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/54/logo5.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/54/conversions/logo5-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 288, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/288/Group-36182.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/288/conversions/Group-36182-thumbnail.jpg\"}]}]', '2021-10-10 10:56:53', '2021-11-29 03:58:49'),
(6, 'Ceseare', '[]', 'ceseare', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 55, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/55/tissot.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/55/conversions/tissot-thumbnail.jpg\"}, {\"id\": 56, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/56/logo6.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/56/conversions/logo6-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 289, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/289/Group-36181.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/289/conversions/Group-36181-thumbnail.jpg\"}]}]', '2021-10-10 10:57:58', '2021-11-29 03:58:45'),
(7, 'AB Shoes', '[]', 'ab-shoes', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 57, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/57/nike.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/57/conversions/nike-thumbnail.jpg\"}, {\"id\": 58, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/58/logo7.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/58/conversions/logo7-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 283, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/283/Group-36180.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/283/conversions/Group-36180-thumbnail.jpg\"}]}]', '2021-10-10 10:58:30', '2021-11-29 03:58:41'),
(8, 'Phonix Bags', '[]', 'phonix-bags', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 59, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/59/herschel.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/59/conversions/herschel-thumbnail.jpg\"}, {\"id\": 60, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/60/logo8.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/60/conversions/logo8-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 282, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/282/Group-36179.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/282/conversions/Group-36179-thumbnail.jpg\"}]}]', '2021-10-10 10:59:04', '2021-11-29 03:58:37'),
(9, 'Hipster', '[]', 'hipster', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 61, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/61/Hollister.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/61/conversions/Hollister-thumbnail.jpg\"}, {\"id\": 62, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/62/logo4.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/62/conversions/logo4-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 281, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/281/shovia.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/281/conversions/shovia-thumbnail.jpg\"}]}]', '2021-10-10 10:59:33', '2021-11-29 03:58:33'),
(10, 'Fania Fashion', '[]', 'fania-fashion', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 63, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/63/zara.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/63/conversions/zara-thumbnail.jpg\"}, {\"id\": 64, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/64/logo10.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/64/conversions/logo10-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 291, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/291/hunter-shoes.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/291/conversions/hunter-shoes-thumbnail.jpg\"}]}]', '2021-10-10 11:00:19', '2021-11-29 03:58:29'),
(11, 'Hairstore VIntage', '[]', 'hairstore-vintage', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 66, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/66/gucci.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/66/conversions/gucci-thumbnail.jpg\"}, {\"id\": 67, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/67/logo11.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/67/conversions/logo11-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 276, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/276/hoppister.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/276/conversions/hoppister-thumbnail.jpg\"}]}]', '2021-10-10 11:01:33', '2021-11-29 03:58:25'),
(12, 'T Fashion', '[]', 't-fashion', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 68, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/68/under-armour.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/68/conversions/under-armour-thumbnail.jpg\"}, {\"id\": 69, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/69/logo12.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/69/conversions/logo12-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 275, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/275/fusion.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/275/conversions/fusion-thumbnail.jpg\"}]}]', '2021-10-10 11:02:00', '2021-11-29 03:58:22'),
(13, 'Vintage Design', '[]', 'vintage-design', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 70, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/70/emporio-armani.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/70/conversions/emporio-armani-thumbnail.jpg\"}, {\"id\": 71, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/71/vintege.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/71/conversions/vintege-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 274, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/274/fashadil.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/274/conversions/fashadil-thumbnail.jpg\"}]}]', '2021-10-10 11:03:01', '2021-11-29 03:58:18'),
(14, 'Vint Shoes', '[]', 'vint-shoes', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 72, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/72/converse.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/72/conversions/converse-thumbnail.jpg\"}, {\"id\": 73, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/73/logo14.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/73/conversions/logo14-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 273, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/273/elegance.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/273/conversions/elegance-thumbnail.jpg\"}]}]', '2021-10-10 11:03:38', '2021-11-29 03:58:14'),
(15, 'Roseban', '[]', 'roseban', 'en', 'DressIcon', NULL, '[{\"key\": \"grid-layout\", \"image\": [{\"id\": 74, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/74/ray-ban.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/74/conversions/ray-ban-thumbnail.jpg\"}, {\"id\": 75, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/75/logo15.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/75/conversions/logo15-thumbnail.jpg\"}]}, {\"key\": \"slider-layout\", \"image\": [{\"id\": 272, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/272/club-shoes.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/272/conversions/club-shoes-thumbnail.jpg\"}]}]', '2021-10-10 11:04:02', '2021-11-29 03:58:10'),
(16, 'HM trades', '[]', 'hm-trades', 'en', 'DressIcon', NULL, '[{\"key\": \"slider-layout\", \"image\": [{\"id\": 290, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/290/blaze-fashion.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/290/conversions/blaze-fashion-thumbnail.jpg\"}]}, {\"key\": \"grid-layout\", \"image\": [{\"id\": 269, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/269/h%26m.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/269/conversions/h%26m-thumbnail.jpg\"}, {\"id\": 271, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/271/logo16.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/271/conversions/logo16-thumbnail.jpg\"}]}]', '2021-10-10 11:04:29', '2021-11-29 03:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `shop_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `is_active`, `shop_id`) VALUES
(1, 'Shop Owner', 'vendor@demo.com', '2025-12-02 12:13:55', '$2y$10$5l2e.NYkxAHFeZWMOqvFoew6GjT0/0bB42wukw3I1l.trTbN951kW', NULL, '2021-10-09 13:39:49', '2021-11-25 04:21:22', 1, NULL),
(3, 'Customer', 'customer@demo.com', '2025-12-02 12:13:55', '$2y$10$DeU1iilF9mg/BBqypizpZ.ysFjuIoHHIycxHmZrAvqTasTErs3P8G', NULL, '2021-11-25 04:22:18', '2021-11-25 04:22:18', 1, NULL),
(4, 'customer2', 'customer2@demo.com', '2025-12-02 12:13:55', '$2y$10$UVs.WftC2iIdLQsHz9Tbdu7OmUXG3P7wyjHvJqCunyJ7JE8ekyXr.', NULL, '2022-03-17 12:15:08', '2022-03-17 12:15:08', 1, NULL),
(5, 'customer3', 'customer3@demo.com', '2025-12-02 12:13:55', '$2y$10$UVs.WftC2iIdLQsHz9Tbdu7OmUXG3P7wyjHvJqCunyJ7JE8ekyXr.', NULL, '2022-03-17 14:25:39', '2022-03-17 14:25:39', 1, NULL),
(6, 'admin@admin.com', 'admin@admin.com', '2025-12-02 08:55:50', '$2y$10$maFHnY/Kgo.Uafo7ZL2PXeqlOiZqdbRcnCtoAx16YLy5nrjFkimLa', NULL, '2025-12-02 08:55:50', '2025-12-02 08:55:50', 1, NULL),
(10, 'karim', 'karimkimo327@gmail.com', '2025-12-02 10:14:47', '$2y$10$kefczPTKwePh93MaDgdrAeOqYlkh0dnfpr7OFQsSdCriSQPfIyo6y', NULL, '2025-12-02 10:14:47', '2025-12-02 10:14:47', 1, NULL),
(9, 'kareem', 'kareemahmedsaad1999@gmail.com', '2025-12-02 12:13:55', '$2y$10$/fknyn21G3xDI0JRl0hDBuh/6RXhBqzOi3wTToXxdhbI8T12PYrK.', NULL, '2025-12-02 10:09:18', '2025-12-02 10:09:18', 1, NULL),
(11, 'John Doe', 'john@example.com', '2026-01-06 09:03:44', '$2y$10$ee.RV1QNhefmzi6FOMziP.u8pCGoC4tUvEVfEWE92n4FjsAsVuRtW', NULL, '2026-01-06 09:03:44', '2026-01-06 09:03:44', 1, NULL),
(12, 'Kareem Doe', 'kareemm@example.com', '2026-01-06 09:33:10', '$2y$10$fPrTgoRiYXjRi2jKmXvwEOoznHxe2748GALl.KJjpDxWwhq8Z2hKi', NULL, '2026-01-06 09:33:10', '2026-01-06 09:33:10', 1, NULL),
(13, 'Kareem Doe', 'kareemmm@example.com', '2026-01-06 09:33:56', '$2y$10$9kW457bmFF.caoNj01z5UexIpPZcs0twJaJQc2ViF./c7pVZTXpI2', NULL, '2026-01-06 09:33:56', '2026-01-06 09:33:56', 1, NULL),
(14, 'Kareem Doe', 'test999@example.com', '2026-01-06 09:45:52', '$2y$10$WduXx3MAqjYWacttWkyureH8uWd2eHl0FlmDqCFhlAlJ47mafsrtO', NULL, '2026-01-06 09:45:52', '2026-01-06 09:45:52', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `avatar` json DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `socials` json DEFAULT NULL,
  `contact` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notifications` json DEFAULT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_profiles_customer_id_foreign` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `avatar`, `bio`, `socials`, `contact`, `notifications`, `customer_id`, `created_at`, `updated_at`) VALUES
(2, '{\"id\": 297, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/297/store_owner.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/297/conversions/store_owner-thumbnail.jpg\"}', 'This is the store owner and we have 6 shops under our banner. We are running all the shops to give our customers hassle-free service and quality products. Our goal is to provide best possible customer service and products for our clients', NULL, '12365141641631', NULL, 1, '2021-08-18 10:17:53', '2021-11-25 04:21:29'),
(3, '{\"id\": 326, \"original\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/326/man.png\", \"thumbnail\": \"https://chawkbazarlaravel.s3.ap-southeast-1.amazonaws.com/326/conversions/man-thumbnail.jpg\"}', '', NULL, '19365141641631', NULL, 3, '2021-08-18 10:17:53', '2021-08-18 10:17:53'),
(4, '{\"id\": 324, \"original\": \"http://localhost:8000/storage/324/68747470733a2f2f796176757a63656c696b65722e6769746875622e696f2f73616d706c652d696d616765732f696d6167652d313032312e6a7067.jpg\", \"thumbnail\": \"http://localhost:8000/storage/324/conversions/68747470733a2f2f796176757a63656c696b65722e6769746875622e696f2f73616d706c652d696d616765732f696d6167652d313032312e6a7067-thumbnail.jpg\"}', NULL, NULL, NULL, NULL, 9, '2025-12-02 10:10:36', '2025-12-02 10:10:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_shop`
--

DROP TABLE IF EXISTS `user_shop`;
CREATE TABLE IF NOT EXISTS `user_shop` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `shop_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_shop_shop_id_foreign` (`shop_id`),
  KEY `user_shop_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_options`
--

DROP TABLE IF EXISTS `variation_options`;
CREATE TABLE IF NOT EXISTS `variation_options` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` json DEFAULT NULL,
  `price` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `quantity` bigint UNSIGNED NOT NULL,
  `sold_quantity` int NOT NULL DEFAULT '0',
  `is_disable` tinyint(1) NOT NULL DEFAULT '0',
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` json NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `digital_file_tracker` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_digital` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `variation_options_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variation_options`
--

INSERT INTO `variation_options` (`id`, `title`, `image`, `price`, `sale_price`, `language`, `quantity`, `sold_quantity`, `is_disable`, `sku`, `options`, `product_id`, `digital_file_tracker`, `created_at`, `updated_at`, `is_digital`) VALUES
(1, 'Red', NULL, '25', '20', 'en', 500, 0, 0, '156156654g654sf64', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 1, NULL, '2021-10-10 12:08:54', '2021-12-14 06:05:17', 0),
(2, 'Blue', NULL, '25', '20', 'en', 500, 0, 0, 'a5da6546afa', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 1, NULL, '2021-10-10 12:08:54', '2021-12-14 06:05:17', 0),
(14, 'Blue', NULL, '100', '80', 'en', 500, 0, 0, '89456413', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 5, NULL, '2021-10-10 16:31:40', '2021-12-14 06:06:49', 0),
(15, 'Yellow', NULL, '100', '80', 'en', 500, 0, 0, 'fdsgdfbdfsndhjkdfm', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 5, NULL, '2021-10-10 16:31:40', '2021-12-14 06:06:49', 0),
(18, 'Yellow/Medium', NULL, '100', '80', 'en', 500, 0, 0, '8964bdfhtzvcb', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 5, NULL, '2021-10-10 16:31:40', '2021-10-10 18:54:50', 0),
(36, 'Red', NULL, '800', '750', 'en', 500, 0, 0, '750755054654', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 17, NULL, '2021-10-11 11:30:59', '2021-12-14 06:12:25', 0),
(37, 'Blue', NULL, '800', '750', 'en', 500, 0, 0, '569874/9', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 17, NULL, '2021-10-11 11:30:59', '2021-12-14 06:12:25', 0),
(40, 'Red', NULL, '30', '22', 'en', 500, 0, 0, '9s874bd6515v', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 20, NULL, '2021-10-11 11:39:30', '2021-12-14 06:15:29', 0),
(41, 'Blue', NULL, '30', '22', 'en', 500, 0, 0, 'hs8456dfs+54sdfa6', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 20, NULL, '2021-10-11 11:39:30', '2021-12-14 06:15:29', 0),
(42, 'Yellow', NULL, '30', '22', 'en', 500, 0, 0, 'd8g4a5fd6g4df564gdf', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 20, NULL, '2021-10-11 11:39:30', '2021-12-14 06:15:29', 0),
(43, 'Red', NULL, '40', '18', 'en', 500, 0, 0, '484512', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 21, NULL, '2021-10-11 11:41:08', '2021-12-14 06:15:43', 0),
(44, 'Blue', NULL, '40', '25', 'en', 500, 0, 0, '984fa5s6d1', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 21, NULL, '2021-10-11 11:41:08', '2021-12-14 06:15:43', 0),
(46, 'Red', NULL, '35', '30', 'en', 500, 0, 0, '561s156sd1', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 22, NULL, '2021-10-11 11:42:33', '2021-12-14 06:15:50', 0),
(47, 'Blue', NULL, '35', '30', 'en', 500, 0, 0, '516as651f56sd1+', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 22, NULL, '2021-10-11 11:42:33', '2021-12-14 06:15:50', 0),
(48, 'Yellow', NULL, '35', '30', 'en', 500, 0, 0, '56sa1fg5sdf156asd1f+', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 22, NULL, '2021-10-11 11:42:33', '2021-12-14 06:15:50', 0),
(49, 'Red', NULL, '50', '40', 'en', 500, 0, 0, '894a8sfd6598', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 23, NULL, '2021-10-11 11:44:01', '2021-12-14 06:15:58', 0),
(50, 'Blue', NULL, '50', '40', 'en', 500, 0, 0, '56a45sda64fas+', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 23, NULL, '2021-10-11 11:44:01', '2021-12-14 06:15:58', 0),
(51, 'Yellow', NULL, '50', '40', 'en', 500, 0, 0, 'asd54f5s4afasd654', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 23, NULL, '2021-10-11 11:44:01', '2021-12-14 06:15:58', 0),
(52, 'Red', NULL, '80', '75', 'en', 500, 0, 0, '844f84sd8a++++', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 24, NULL, '2021-10-11 11:45:37', '2021-12-14 06:16:05', 0),
(53, 'Blue', NULL, '80', '75', 'en', 500, 0, 0, '5fa45sda4f56asdf+++', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 24, NULL, '2021-10-11 11:45:37', '2021-12-14 06:16:05', 0),
(54, 'Yellow', NULL, '80', '75', 'en', 500, 0, 0, 's54fs64fsda564fsg894ga++++', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 24, NULL, '2021-10-11 11:45:37', '2021-12-14 06:16:05', 0),
(55, '7', NULL, '50', '45', 'en', 500, 0, 0, '56848+4+', '[{\"name\": \"Size\", \"value\": \"7\"}]', 25, NULL, '2021-10-23 10:40:16', '2021-12-14 06:16:14', 0),
(56, '8', NULL, '50', '45', 'en', 500, 0, 0, '8514684156+++', '[{\"name\": \"Size\", \"value\": \"8\"}]', 25, NULL, '2021-10-23 10:40:16', '2021-12-14 06:16:14', 0),
(57, '9', NULL, '50', '45', 'en', 500, 0, 0, '65afdss', '[{\"name\": \"Size\", \"value\": \"9\"}]', 25, NULL, '2021-10-23 10:40:16', '2021-12-14 06:16:14', 0),
(58, '10', NULL, '50', '45', 'en', 500, 0, 0, '6541651+51651', '[{\"name\": \"Size\", \"value\": \"10\"}]', 25, NULL, '2021-10-23 10:40:16', '2021-12-14 06:16:14', 0),
(59, '7', NULL, '200', '180', 'en', 500, 0, 0, '51d654sd65g4d65', '[{\"name\": \"Size\", \"value\": \"7\"}]', 27, NULL, '2021-10-23 10:50:44', '2021-12-14 06:16:30', 0),
(60, '8', NULL, '200', '180', 'en', 500, 0, 0, 'asdfsdgasd4g56465', '[{\"name\": \"Size\", \"value\": \"8\"}]', 27, NULL, '2021-10-23 10:50:44', '2021-12-14 06:16:30', 0),
(61, '9', NULL, '200', '180', 'en', 500, 0, 0, 'fadsfsda4a56', '[{\"name\": \"Size\", \"value\": \"9\"}]', 27, NULL, '2021-10-23 10:50:44', '2021-12-14 06:16:30', 0),
(62, '10', NULL, '200', '180', 'en', 500, 0, 0, '5a165sdf56a4', '[{\"name\": \"Size\", \"value\": \"10\"}]', 27, NULL, '2021-10-23 10:50:44', '2021-12-14 06:16:30', 0),
(63, 'Red', NULL, '800', '500', 'en', 150, 0, 0, 'asdasd3423432sdasdad', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 29, NULL, '2021-10-23 17:07:58', '2021-12-14 06:16:45', 0),
(64, 'Yellow', NULL, '850', '550', 'en', 100, 0, 0, 'asdasd3244234546nghjghj', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 29, NULL, '2021-10-23 17:07:58', '2021-12-14 06:16:45', 0),
(65, 'Blue', NULL, '900', '600', 'en', 50, 0, 0, 'hfghty7676yfghf', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 29, NULL, '2021-10-23 17:07:58', '2021-12-14 06:16:45', 0),
(74, '7', NULL, '2000', '1800', 'en', 150, 0, 0, 'vcvcgd64564tgdfgdfgdf', '[{\"name\": \"Size\", \"value\": \"7\"}]', 31, NULL, '2021-10-23 17:18:14', '2021-12-14 06:16:58', 0),
(75, 'Red', NULL, '1000', '900', 'en', 100, 0, 0, 'wdwqe324234fsfsfs', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 33, NULL, '2021-10-23 17:57:31', '2021-12-14 06:17:10', 0),
(76, 'Blue', NULL, '1000', '899', 'en', 100, 0, 0, 'sadasda897989879asda', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 33, NULL, '2021-10-23 17:57:31', '2021-12-14 06:17:10', 0),
(78, 'Small', NULL, '400', '350', 'en', 100, 0, 0, 'sdfsdr34354fddsfs', '[{\"name\": \"Size\", \"value\": \"Small\"}]', 35, NULL, '2021-10-23 18:14:03', '2021-12-14 06:17:20', 0),
(81, 'Red', NULL, '1150', '950', 'en', 100, 0, 0, 'czczc32423dadasda', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 41, NULL, '2021-10-23 18:30:19', '2021-12-14 06:19:40', 0),
(82, 'Blue', NULL, '1150', '950', 'en', 98, 0, 0, 'fgergtert544tffd', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 41, NULL, '2021-10-23 18:30:19', '2021-12-14 06:19:40', 0),
(83, '7', NULL, '260', '250', 'en', 100, 0, 0, 'sfsdf3423dsfdsfsaf', '[{\"name\": \"Size\", \"value\": \"7\"}]', 42, NULL, '2021-10-23 18:33:38', '2021-12-14 06:19:34', 0),
(84, '8', NULL, '260', '250', 'en', 100, 0, 0, 'dsfsdf435dsdfasd', '[{\"name\": \"Size\", \"value\": \"8\"}]', 42, NULL, '2021-10-23 18:33:38', '2021-12-14 06:19:34', 0),
(85, 'Small', NULL, '90', '75', 'en', 100, 0, 0, 'fdger543rfsds', '[{\"name\": \"Size\", \"value\": \"Small\"}]', 43, NULL, '2021-10-23 18:36:54', '2021-12-14 06:19:28', 0),
(86, 'Medium', NULL, '90', '75', 'en', 100, 0, 0, 'asdasd343232dsasd', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 43, NULL, '2021-10-23 18:36:54', '2021-12-14 06:19:28', 0),
(87, '7', NULL, '180', '160', 'en', 50, 0, 0, 'gfdgd454sdfsdfs', '[{\"name\": \"Size\", \"value\": \"7\"}]', 46, NULL, '2021-10-23 18:49:36', '2021-12-14 06:19:13', 0),
(88, '8', NULL, '180', '160', 'en', 50, 0, 0, 'gjgyt565hfghfgh', '[{\"name\": \"Size\", \"value\": \"8\"}]', 46, NULL, '2021-10-23 18:49:36', '2021-12-14 06:19:13', 0),
(89, '9', NULL, '180', '160', 'en', 50, 0, 0, 'fddg546fgdfgd', '[{\"name\": \"Size\", \"value\": \"9\"}]', 46, NULL, '2021-10-23 18:49:36', '2021-12-14 06:19:13', 0),
(90, 'Medium', NULL, '120', '100', 'en', 50, 0, 0, 'hgjgh565etgdfgd', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 47, NULL, '2021-10-23 18:53:15', '2021-12-14 06:19:07', 0),
(91, 'Large', NULL, '120', '100', 'en', 50, 0, 0, 'kghjgh5464dfssf', '[{\"name\": \"Size\", \"value\": \"Large\"}]', 47, NULL, '2021-10-23 18:53:15', '2021-12-14 06:19:07', 0),
(92, 'Small', NULL, '35', '30', 'en', 50, 0, 0, 'fhhf6565gbcvbvc', '[{\"name\": \"Size\", \"value\": \"Small\"}]', 50, NULL, '2021-10-23 19:03:36', '2021-12-14 06:18:51', 0),
(93, 'Medium', NULL, '35', '30', 'en', 50, 0, 0, 'hvhjghj7756tgfdgdf', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 50, NULL, '2021-10-23 19:03:36', '2021-12-14 06:18:51', 0),
(94, 'Blue', NULL, '200', '150', 'en', 50, 0, 0, 'ghjgy654645ygfhfg', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 53, NULL, '2021-10-23 19:12:41', '2021-12-14 06:18:36', 0),
(95, 'Yellow', NULL, '200', '150', 'en', 50, 0, 0, 'vdfdvfdv786876dsasd', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 53, NULL, '2021-10-23 19:12:41', '2021-12-14 06:18:36', 0),
(98, 'Small', NULL, '40', '35', 'en', 80, 0, 0, 'sfsdfsy786sdfsd', '[{\"name\": \"Size\", \"value\": \"Small\"}]', 55, NULL, '2021-10-23 19:18:00', '2021-12-14 06:18:27', 0),
(99, 'Small', NULL, '100', '90', 'en', 120, 0, 0, 'ghfh5645tgdfd', '[{\"name\": \"Size\", \"value\": \"Small\"}]', 57, NULL, '2021-10-23 19:22:38', '2021-12-14 06:18:15', 0),
(100, 'Medium', NULL, '100', '90', 'en', 120, 0, 0, 'kjhk4564dfgfd', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 57, NULL, '2021-10-23 19:22:38', '2021-12-14 06:18:15', 0),
(101, '7', NULL, '250', '220', 'en', 500, 0, 0, '8s4f6sdg8d45', '[{\"name\": \"Size\", \"value\": \"7\"}]', 49, NULL, '2021-10-24 04:10:20', '2021-12-14 06:18:57', 0),
(102, '8', NULL, '250', '220', 'en', 500, 0, 0, '8dgsdf566', '[{\"name\": \"Size\", \"value\": \"8\"}]', 49, NULL, '2021-10-24 04:10:20', '2021-12-14 06:18:57', 0),
(103, '9', NULL, '250', '220', 'en', 500, 0, 0, '4dsa6f4af5asd', '[{\"name\": \"Size\", \"value\": \"9\"}]', 49, NULL, '2021-10-24 04:10:20', '2021-12-14 06:18:57', 0),
(104, '10', NULL, '250', '220', 'en', 500, 0, 0, 'f65a4sd56f4g8a4', '[{\"name\": \"Size\", \"value\": \"10\"}]', 49, NULL, '2021-10-24 04:10:20', '2021-12-14 06:18:57', 0),
(108, 'Large', NULL, '100', '80', 'en', 500, 0, 0, '56sdf1g65151', '[{\"name\": \"Size\", \"value\": \"Large\"}]', 57, NULL, '2021-11-28 10:13:10', '2021-12-14 06:18:15', 0),
(109, 'Medium', NULL, '40', '30', 'en', 500, 0, 0, '515xcz1v8d4ga489', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 55, NULL, '2021-11-28 10:14:10', '2021-12-14 06:18:27', 0),
(110, 'Large', NULL, '40', '10', 'en', 500, 0, 0, 'ds5g4fd84gdr84b', '[{\"name\": \"Size\", \"value\": \"Large\"}]', 55, NULL, '2021-11-28 10:14:10', '2021-12-14 06:18:27', 0),
(112, '10', NULL, '180', '160', 'en', 500, 0, 0, 's4f6sda84ds8951', '[{\"name\": \"Size\", \"value\": \"10\"}]', 46, NULL, '2021-11-28 10:41:02', '2021-12-14 06:19:13', 0),
(113, 'Large', NULL, '90', '75', 'en', 500, 0, 0, 'tr7gs14x2.35+65v2', '[{\"name\": \"Size\", \"value\": \"Large\"}]', 43, NULL, '2021-11-28 10:41:28', '2021-12-14 06:19:28', 0),
(114, '9', NULL, '260', '240', 'en', 500, 0, 0, '8d4fd8a4gwer/hge489b51', '[{\"name\": \"Size\", \"value\": \"9\"}]', 42, NULL, '2021-11-28 10:42:03', '2021-12-14 06:19:34', 0),
(115, '10', NULL, '260', '240', 'en', 500, 0, 0, 'd4fgsdhsd7hsd/h984', '[{\"name\": \"Size\", \"value\": \"10\"}]', 42, NULL, '2021-11-28 10:42:03', '2021-12-14 06:19:34', 0),
(116, 'Yellow', NULL, '1150', '950', 'en', 500, 0, 0, 'geebgsfv', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 41, NULL, '2021-11-28 10:42:37', '2021-12-14 06:19:40', 0),
(117, '8', NULL, '2000', '1800', 'en', 500, 0, 0, 'dgs4gd6f565fdb1fd5b165', '[{\"name\": \"Size\", \"value\": \"8\"}]', 31, NULL, '2021-11-28 10:55:46', '2021-12-14 06:16:58', 0),
(118, 'Red', NULL, '420', '350', 'en', 500, 0, 0, '5g1s6dfg56sfd1g65d1', '[{\"name\": \"Color\", \"value\": \"Red\"}]', 36, NULL, '2021-11-28 10:56:55', '2021-12-14 06:17:26', 0),
(120, 'Red/Small', NULL, '200', '180', 'en', 500, 0, 0, 'fa984v1 651651', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 3, NULL, '2021-11-28 10:59:08', '2021-12-14 06:06:34', 0),
(121, 'Red/Small', NULL, '650', '550', 'en', 500, 0, 0, '9re74a1b2655v #02', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 7, NULL, '2021-11-28 11:00:22', '2021-12-14 06:07:33', 0),
(122, 'Red/Medium', NULL, '650', '580', 'en', 500, 0, 0, '84fa8486dsa4f655sdf26', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 7, NULL, '2021-11-28 11:00:22', '2021-12-14 06:07:33', 0),
(123, 'Red/Large', NULL, '650', '580', 'en', 500, 0, 0, '5f56ad4f651503153250', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Large\"}]', 7, NULL, '2021-11-28 11:00:22', '2021-12-14 06:07:33', 0),
(124, 'Red/Small', NULL, '65', '50', 'en', 500, 0, 0, 'dage9gr8eg84f85484', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 8, NULL, '2021-11-28 11:00:52', '2021-12-14 06:08:31', 0),
(125, 'Red/Small', NULL, '30', NULL, 'en', 500, 0, 0, 'wertyuiolp;[\'', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 15, NULL, '2021-11-28 11:09:00', '2021-12-14 06:11:41', 0),
(126, 'Red/Small', NULL, '25', '20', 'en', 500, 0, 0, 'asdfghjk', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 16, NULL, '2021-11-28 11:09:28', '2021-12-14 06:12:16', 0),
(127, 'Blue', NULL, '350', '300', 'en', 1000, 0, 0, '5g34fdfg56sfd1g65d1', '[{\"name\": \"Color\", \"value\": \"Blue\"}]', 36, NULL, '2021-12-14 06:01:12', '2021-12-14 06:17:26', 0),
(128, 'Yellow', NULL, '650', '500', 'en', 1200, 0, 0, '5g1s6dfg56sfd1g45df', '[{\"name\": \"Color\", \"value\": \"Yellow\"}]', 36, NULL, '2021-12-14 06:01:12', '2021-12-14 06:17:26', 0),
(129, 'Medium', NULL, '1000', '899', 'en', 1000, 0, 0, 'sdfsdr34354fd1fd3', '[{\"name\": \"Size\", \"value\": \"Medium\"}]', 35, NULL, '2021-12-14 06:01:40', '2021-12-14 06:17:20', 0),
(130, '7', NULL, '1000', '599', 'en', 1000, 0, 0, 'sdfsdr34354ffsa', '[{\"name\": \"Size\", \"value\": \"7\"}]', 2, NULL, '2021-12-14 06:05:49', '2021-12-14 06:05:49', 0),
(131, '8', NULL, '2000', '1999', 'en', 1000, 0, 0, 'sdfsdr34354fdlh', '[{\"name\": \"Size\", \"value\": \"8\"}]', 2, NULL, '2021-12-14 06:05:49', '2021-12-14 06:05:49', 0),
(132, 'Red/Medium', NULL, '300', '250', 'en', 1000, 0, 0, 'sdfsdr3435443d', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 3, NULL, '2021-12-14 06:06:34', '2021-12-14 06:06:34', 0),
(133, 'Blue/Small', NULL, '400', '387', 'en', 1000, 0, 0, 'sdfsdr34354fd34j', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 3, NULL, '2021-12-14 06:06:34', '2021-12-14 06:06:34', 0),
(134, 'Blue/Medium', NULL, '600', '500', 'en', 1000, 0, 0, 'sdfsdr34354fdwer', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 3, NULL, '2021-12-14 06:06:34', '2021-12-14 06:06:34', 0),
(135, 'Yellow/Small', NULL, '600', '500', 'en', 1000, 0, 0, 'sdfsdr34354fdlen', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 7, NULL, '2021-12-14 06:07:33', '2021-12-14 06:07:33', 0),
(136, 'Yellow/Medium', NULL, '700', '550', 'en', 1000, 0, 0, 'sdfsdr34354fdlj', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 7, NULL, '2021-12-14 06:07:33', '2021-12-14 06:07:33', 0),
(137, 'Yellow/Large', NULL, '800', '650', 'en', 1000, 0, 0, 'sdfsdr34354fdmed', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Large\"}]', 7, NULL, '2021-12-14 06:07:33', '2021-12-14 06:07:33', 0),
(138, 'Red/Large', NULL, '100', '75', 'en', 500, 0, 0, 'sdfsdr34354fdfjr', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Large\"}]', 8, NULL, '2021-12-14 06:08:31', '2021-12-14 06:08:31', 0),
(139, 'Blue/Small', NULL, '1000', '599', 'en', 1000, 0, 0, 'sdfsdr34354f2fb', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 8, NULL, '2021-12-14 06:08:31', '2021-12-14 06:08:31', 0),
(140, 'Blue/Large', NULL, '1500', '899', 'en', 1000, 0, 0, 'sdfsdr34354f23fj', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Large\"}]', 8, NULL, '2021-12-14 06:08:31', '2021-12-14 06:08:31', 0),
(141, '7', NULL, '1000', '599', 'en', 1000, 0, 0, 'sdfsdr34354dsf', '[{\"name\": \"Size\", \"value\": \"7\"}]', 14, NULL, '2021-12-14 06:10:38', '2021-12-14 06:10:38', 0),
(142, '8', NULL, '450', '400', 'en', 1000, 0, 0, 'sdfsdr3435dfdds', '[{\"name\": \"Size\", \"value\": \"8\"}]', 14, NULL, '2021-12-14 06:10:38', '2021-12-14 06:10:38', 0),
(143, '9', NULL, '1000', '499', 'en', 1000, 0, 0, 'sdfsdr343sdfsf', '[{\"name\": \"Size\", \"value\": \"9\"}]', 14, NULL, '2021-12-14 06:10:38', '2021-12-14 06:10:38', 0),
(144, 'Red/Medium', NULL, '100', '59', 'en', 1000, 0, 0, 'dsfsdfsdfsdf', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 15, NULL, '2021-12-14 06:11:41', '2021-12-14 06:11:41', 0),
(145, 'Blue/Small', NULL, '500', '399', 'en', 1000, 0, 0, 'sdfjdshkjsdfhk', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 15, NULL, '2021-12-14 06:11:41', '2021-12-14 06:11:41', 0),
(146, 'Blue/Medium', NULL, '1000', '599', 'en', 1000, 0, 0, 'sdlfjdslifjsdf', '[{\"name\": \"Color\", \"value\": \"Blue\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 15, NULL, '2021-12-14 06:11:41', '2021-12-14 06:11:41', 0),
(147, 'Red/Medium', NULL, '100', '99', 'en', 1000, 0, 0, 'sdlkfjdslifj', '[{\"name\": \"Color\", \"value\": \"Red\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 16, NULL, '2021-12-14 06:12:16', '2021-12-14 06:12:16', 0),
(148, 'Yellow/Small', NULL, '500', '200', 'en', 1000, 0, 0, 'sdilfhjdliksjfh', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Small\"}]', 16, NULL, '2021-12-14 06:12:16', '2021-12-14 06:12:16', 0),
(149, 'Yellow/Medium', NULL, '1000', '239', 'en', 1000, 0, 0, 'sdlfjsldjsdf', '[{\"name\": \"Color\", \"value\": \"Yellow\"}, {\"name\": \"Size\", \"value\": \"Medium\"}]', 16, NULL, '2021-12-14 06:12:16', '2021-12-14 06:12:16', 0),
(150, '9', NULL, '4000', '3999', 'en', 1000, 0, 0, 'adflkjsfljdsfs', '[{\"name\": \"Size\", \"value\": \"9\"}]', 31, NULL, '2021-12-14 06:13:54', '2021-12-14 06:16:58', 0),
(151, '10', NULL, '5000', '3500', 'en', 1000, 0, 0, 'sdlkfjhsdlfj', '[{\"name\": \"Size\", \"value\": \"10\"}]', 31, NULL, '2021-12-14 06:13:54', '2021-12-14 06:16:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
CREATE TABLE IF NOT EXISTS `wallets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `total_points` double NOT NULL DEFAULT '0',
  `points_used` double NOT NULL DEFAULT '0',
  `available_points` double NOT NULL DEFAULT '0',
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_customer_id_foreign` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `total_points`, `points_used`, `available_points`, `customer_id`, `created_at`, `updated_at`) VALUES
(1, 100, 0, 100, 7, '2025-12-02 09:40:34', '2025-12-02 09:40:34'),
(2, 100, 0, 100, 8, '2025-12-02 09:42:10', '2025-12-02 09:42:10'),
(3, 100, 0, 100, 9, '2025-12-02 10:09:18', '2025-12-02 10:09:18'),
(4, 100, 0, 100, 10, '2025-12-02 10:14:47', '2025-12-02 10:14:47'),
(5, 100, 0, 100, 14, '2026-01-06 09:45:52', '2026-01-06 09:45:52');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `variation_option_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wishlists_user_id_foreign` (`user_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  KEY `wishlists_variation_option_id_foreign` (`variation_option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraws`
--

DROP TABLE IF EXISTS `withdraws`;
CREATE TABLE IF NOT EXISTS `withdraws` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` bigint UNSIGNED NOT NULL,
  `amount` double(8,2) NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('approved','pending','on_hold','rejected','processing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `details` text COLLATE utf8mb4_unicode_ci,
  `note` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdraws_shop_id_foreign` (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `withdraws`
--

INSERT INTO `withdraws` (`id`, `shop_id`, `amount`, `payment_method`, `status`, `details`, `note`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 500.00, 'cash', 'approved', 'need to with draw 500', NULL, NULL, '2021-11-28 04:39:42', '2021-11-28 05:24:08'),
(2, 2, 250.00, 'cash', 'on_hold', 'Need to withdraw 250', 'urgently required', NULL, '2021-11-28 05:15:08', '2021-11-28 05:20:31'),
(3, 2, 6500.00, 'cash', 'rejected', 'need to withdraw', NULL, NULL, '2021-11-28 05:17:48', '2021-11-28 05:20:51'),
(4, 2, 600.00, 'cash', 'on_hold', 'need urgently', 'need payment', NULL, '2021-11-28 05:21:20', '2021-11-28 15:13:39');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
