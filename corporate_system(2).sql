-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 25, 2025 at 08:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `corporate_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_levels`
--

CREATE TABLE `access_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `level` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user access levels';

--
-- Dumping data for table `access_levels`
--

INSERT INTO `access_levels` (`id`, `name`, `level`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 100, 'Full system access', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(2, 'Manager', 80, 'Department manager access', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(3, 'Supervisor', 60, 'Team supervisor access', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(4, 'Staff', 40, 'Regular staff access', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(5, 'Basic', 20, 'Basic system access with limited features', '2025-08-21 14:34:51', '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL,
  `action_type` varchar(255) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks user activities in the system';

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity_type`, `description`, `ip_address`, `user_agent`, `entity_type`, `entity_id`, `old_values`, `new_values`, `created_at`) VALUES
(1, NULL, 'security', 'Account lockout triggered for email: admin@example.com', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, '2025-08-21 14:40:58'),
(2, NULL, 'security', 'Account lockout triggered for email: admin@example.com', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, '2025-08-21 14:41:12'),
(3, NULL, 'security', 'Account lockout triggered for email: admin@example.com', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, '2025-08-21 14:43:21'),
(4, NULL, 'security', 'Account lockout triggered for email: admin@example.com', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, '2025-08-21 14:43:23'),
(5, NULL, 'security', 'Account lockout triggered for email: admin@example.com', '127.0.0.1', NULL, NULL, NULL, NULL, NULL, '2025-08-21 14:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores departments (secretarias) information';

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Secretaria de Educação', 'EDU', 'Responsável pela gestão das políticas de educação.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(2, 'Secretaria de Saúde', 'SAU', 'Gerencia os serviços de saúde e políticas sanitárias.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(3, 'Secretaria de Obras', 'OBR', 'Planeja e executa obras públicas.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(4, 'Secretaria de Meio Ambiente', 'AMB', 'Cuida da preservação ambiental e políticas ecológicas.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(5, 'Secretaria de Segurança Pública', 'SEG', 'Coordena as ações de segurança e policiamento.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(6, 'Secretaria de Transportes', 'TRA', 'Administra o transporte público e infraestrutura viária.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(7, 'Secretaria de Cultura', 'CUL', 'Promove ações e eventos culturais.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08'),
(8, 'Secretaria de Esportes', 'ESP', 'Incentiva a prática esportiva e eventos esportivos.', 1, '2025-08-21 18:01:08', '2025-08-21 18:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') NOT NULL,
  `failure_reason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks login attempts for security purposes';

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `email`, `ip_address`, `user_agent`, `attempt_time`, `status`, `failure_reason`) VALUES
(1, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:40:17', 'failed', 'Invalid password'),
(2, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:40:21', 'failed', 'Invalid password'),
(3, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:40:34', 'failed', 'Invalid password'),
(4, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:40:41', 'failed', 'Invalid password'),
(5, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:40:58', 'failed', 'Invalid password'),
(6, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:41:12', 'failed', 'Invalid password'),
(7, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:43:21', 'failed', 'Invalid password'),
(8, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:43:23', 'failed', 'Invalid password'),
(9, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:44:42', 'failed', 'Invalid password'),
(10, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:46:26', 'success', NULL),
(11, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:46:49', 'success', NULL),
(12, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 14:56:23', 'success', NULL),
(13, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 16:58:49', 'success', NULL),
(14, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 17:02:55', 'success', NULL),
(15, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 17:24:07', 'success', NULL),
(16, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 17:25:24', 'success', NULL),
(17, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 17:41:29', 'success', NULL),
(18, NULL, '123@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 18:21:04', 'success', NULL),
(19, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 18:58:26', 'success', NULL),
(20, NULL, 'zanataxandyzv07@gmail.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 19:01:46', 'success', NULL),
(21, NULL, 'alexandre332@gmail.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 19:02:40', 'success', NULL),
(22, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 19:07:48', 'success', NULL),
(23, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 19:11:59', 'success', NULL),
(24, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 19:19:51', 'success', NULL),
(25, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 20:20:50', 'success', NULL),
(26, NULL, 'zanataxandyzv07@gmail.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 20:32:42', 'failed', 'Invalid password'),
(27, NULL, 'zanataxandyzv07@gmail.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 20:32:52', 'failed', 'Invalid password'),
(28, NULL, 'zanataxandyzv07@gmail.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 20:33:23', 'failed', 'Invalid password'),
(29, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 20:34:57', 'success', NULL),
(30, NULL, 'zanataxandyzv07@gmail.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 20:35:20', 'failed', 'Invalid password'),
(31, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 20:36:09', 'success', NULL),
(32, NULL, 'admin@example.com', '172.25.5.126', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-21 20:37:04', 'success', NULL),
(33, NULL, 'root@1.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-21 20:49:56', 'success', NULL),
(34, NULL, 'zanataxandyzv07@gmail.com', '172.19.2.146', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-25 11:51:12', 'failed', 'Invalid password'),
(35, NULL, 'admin@example.com', '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:141.0) Gecko/20100101 Firefox/141.0', '2025-08-25 11:51:40', 'success', NULL),
(36, NULL, 'admin@example.com', '172.19.2.146', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36', '2025-08-25 13:33:45', 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user notifications';

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores system permissions';

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'users_view', 'View Users', 'Can view user listings', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(2, 'users_create', 'Create Users', 'Can create new users', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(3, 'users_edit', 'Edit Users', 'Can edit existing users', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(4, 'users_delete', 'Delete Users', 'Can delete users', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(5, 'roles_manage', 'Manage Roles', 'Can manage user roles', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(6, 'reports_view', 'View Reports', 'Can view system reports', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(7, 'reports_create', 'Create Reports', 'Can create system reports', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(8, 'settings_view', 'View Settings', 'Can view system settings', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(9, 'settings_edit', 'Edit Settings', 'Can edit system settings', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(10, 'logs_view', 'View Logs', 'Can view system logs', '2025-08-21 14:34:51', '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `departments_id` int(11) NOT NULL,
  `vehicle` varchar(50) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `initial_km` float NOT NULL,
  `final_km` float DEFAULT NULL,
  `destination` varchar(255) NOT NULL,
  `stop_point` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL DEFAULT current_timestamp(),
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user roles';

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_system_role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'System administrator with full access', 1, '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(2, 'manager', 'Department manager', 1, '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(3, 'supervisor', 'Team supervisor', 1, '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(4, 'staff', 'Regular staff member', 1, '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(5, 'guest', 'Limited access guest role', 1, '2025-08-21 14:34:51', '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps permissions to roles';

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`) VALUES
(1, 1, '2025-08-21 14:34:51'),
(1, 2, '2025-08-21 14:34:51'),
(1, 3, '2025-08-21 14:34:51'),
(1, 4, '2025-08-21 14:34:51'),
(1, 5, '2025-08-21 14:34:51'),
(1, 6, '2025-08-21 14:34:51'),
(1, 7, '2025-08-21 14:34:51'),
(1, 8, '2025-08-21 14:34:51'),
(1, 9, '2025-08-21 14:34:51'),
(1, 10, '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` bigint(20) UNSIGNED DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores active user sessions';

-- --------------------------------------------------------

--
-- Table structure for table `station_prices`
--

CREATE TABLE `station_prices` (
  `id` int(11) NOT NULL,
  `station_name` varchar(255) NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `price` decimal(5,2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `station_prices`
--

INSERT INTO `station_prices` (`id`, `station_name`, `fuel_type`, `price`, `name`, `timestamp`) VALUES
(26, 'BRESCANSIN & BRESCANSIN LTD.', 'Diesel S10', 4.56, 'ALEXANDRE ZANATA DE OLIVEIRA VASCONCELOS', '2025-05-26 10:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `subdepartments`
--

CREATE TABLE `subdepartments` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores subdepartments information';

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `data_type` enum('string','integer','float','boolean','json','datetime') NOT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores system configuration settings';

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `data_type`, `is_public`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'fal o l', 'string', 1, 'Name of the system displayed in UI', '2025-08-21 14:34:51', '2025-08-21 14:56:09'),
(2, 'site_logo', 'logo.png', 'string', 1, 'Logo file name', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(3, 'login_attempts', '5', 'integer', 0, 'Max number of failed login attempts before lockout', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(4, 'lockout_time', '30', 'integer', 0, 'Lockout time in minutes after failed login attempts', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(5, 'session_timeout', '120', 'integer', 0, 'Session timeout in minutes', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(6, 'maintenance_mode', 'false', 'boolean', 0, 'System maintenance mode', '2025-08-21 14:34:51', '2025-08-21 14:34:51'),
(7, 'default_language', 'en', 'string', 1, 'Default system language', '2025-08-21 14:34:51', '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cpf` varchar(16) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `subdepartment_id` int(11) DEFAULT NULL,
  `access_level_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `recovery_token` varchar(255) DEFAULT NULL,
  `recovery_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores user account information';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `name`, `cpf`, `department_id`, `subdepartment_id`, `access_level_id`, `phone`, `avatar`, `last_login`, `is_active`, `email_verified`, `recovery_token`, `recovery_expires`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$VRs3O84q0bfeoD2gRI50oeqAyNzlXrSun00vVuzAklX7OBB5nJrGe', 'System Administrator', '0', NULL, NULL, 1, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-08-21 14:34:51', '2025-08-21 14:45:44'),
(3, '1', '1@example.com', '$2y$10$e.s.WlULSi21OF6D4f/rHubwXaK111VhvsFUtwtOyVvwQ7gL6Zx8m', 'Aleexa Ndndsds Djdsjjds', '323.233.323-23', 1, NULL, 2, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-08-21 18:07:40', '2025-08-21 18:07:40'),
(7, '123', '123@example.com', '$2y$10$X0JBerSZH4RAZZgJLAjldO9Cb58PZjTCOAP9AE61pf9eSOiPp8WLe', 'Aleexa Ndndsds Djdsjjds', '323.233.323-23', 2, NULL, 2, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-08-21 18:11:13', '2025-08-21 18:11:13'),
(9, 'alexandre332', 'alexandre332@gmail.com', '$2y$12$0eCERTeyLKa1reUc5xazcOPsKCKyBvZyEmcB6rB9STaSGyZJsloBu', 'Aeokew Weihewhwe Ewihwehiewhiwe', '221.323.232-23', 8, NULL, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-08-21 18:39:14', '2025-08-21 18:39:14'),
(10, 'zanataxandyzv07', 'zanataxandyzv07@gmail.com', '$2y$12$xiuFF.p/oWOj4EKMY.Zov.u9JhK.TaURdvyG9aoPl3IlRQDWeyCv2', 'Alexandre Zanata Tesgs', '829.292.929-98', 3, NULL, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-08-21 19:01:11', '2025-08-21 19:01:11'),
(11, 'root', 'root@1.com', '$2y$12$mwLoEEo/oh473pXQCIa4P.qCW955ButEZl6VRMAwbVhA9yCtGhJiG', 'Afsnewe Wehjewjhew Ewjwejwej', '232.332.322-33', 5, NULL, 5, NULL, NULL, NULL, 0, 0, NULL, NULL, '2025-08-21 20:49:39', '2025-08-21 20:49:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps roles to users';

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `created_at`) VALUES
(1, 1, '2025-08-21 14:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle` varchar(512) DEFAULT NULL,
  `fuel` enum('Gasoline','Ethanol','Diesel-S10','Diesel-S500') DEFAULT NULL,
  `registry` varchar(100) DEFAULT NULL,
  `license_plate` varchar(512) DEFAULT NULL,
  `renavam` varchar(100) DEFAULT NULL,
  `chassis` varchar(512) DEFAULT NULL,
  `brand` varchar(512) DEFAULT NULL,
  `model_year` varchar(512) DEFAULT NULL,
  `type` varchar(512) DEFAULT NULL,
  `departments_id` int(11) DEFAULT NULL,
  `status` enum('active','in use','blocked') DEFAULT 'active',
  `tank_capacity` int(4) NOT NULL,
  `next_maintenance_km` float DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `last_maintenance_km` float DEFAULT NULL,
  `last_maintenance_date` date DEFAULT NULL,
  `maintenance_interval_km` float DEFAULT 10000,
  `factory_value` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle`, `fuel`, `registry`, `license_plate`, `renavam`, `chassis`, `brand`, `model_year`, `type`, `departments_id`, `status`, `tank_capacity`, `next_maintenance_km`, `next_maintenance_date`, `last_maintenance_km`, `last_maintenance_date`, `maintenance_interval_km`, `factory_value`) VALUES
(1, 'C-18', 'Gasoline', '815199', 'NJV-9862', 'not available', 'not available', 'CG', '2016/2016', 'HONDA/ CG 160 START', 1, 'active', 12, NULL, NULL, NULL, NULL, 10000, 35.00),
(2, 'C-19', 'Gasoline', '815200', 'JZH-1084', '770821260', '9BWGB07X12P003260', 'CG', '2001/2002', 'HONDA/ CG 160 START', 1, 'active', 16, NULL, NULL, NULL, NULL, 10000, 35.00),
(3, 'C-22', 'Gasoline', '827840', 'QCZ-3549', '0117177509', '9C2KC2200JR180646', 'CG', '2018/2018', 'HONDA/ CG 160 FAN', 1, 'active', 20, NULL, NULL, NULL, NULL, 10000, 35.00),
(4, 'C-23', 'Gasoline', '827854', 'QCZ-3519', 'not available', 'not available', 'CG', '2018/2018', 'HONDA/ CG 160 FAN', 1, 'in use', 20, NULL, NULL, NULL, NULL, 10000, 35.00),
(5, 'C-24', 'Gasoline', '827851', 'QCZ-3589', 'not available', 'not available', 'CG', '2018/2018', 'MOTO HONDA, BRAND CG 160 TITAN', 2, 'in use', 20, NULL, NULL, NULL, NULL, 10000, 35.00),
(6, 'C-25', 'Gasoline', '78670', 'QCE-1501', '117889331', '9C2KC2220JROO3263', 'CG ', '2018/2018', 'MOTO HONDA CG 160 CARGO', 1, 'active', 20, NULL, NULL, NULL, NULL, 10000, 35.00),
(7, 'C-34', 'Gasoline', '104424', 'RRQ-9I36', '1323618276', '99KPCKBYJPM714145', 'HAOJUE', '2022/2023', 'MOTORCYCLE HAOJUE DK150CBS', 2, 'active', 12, 20940, '2026-02-03', 10940, '2025-08-07', 10000, 36.00),
(8, 'C-35', 'Gasoline', '104426', 'RRQ-9I96', '1323621234', '99KPCKBYJPM714187', 'HAOJUE', '2022/2023', 'MOTORCYCLE HAOJUE DK150CBS', 1, 'in use', 12, NULL, NULL, NULL, NULL, 10000, 36.00),
(9, 'C-36', 'Gasoline', '104428', 'RRR-0I56', '1323660825', '99KPCKBYJPM714338', 'HAOJUE', '2022/2023', 'MOTORCYCLE HAOJUE DK150CBS', 1, 'active', 12, NULL, NULL, NULL, NULL, 10000, 36.00),
(10, 'C-37', 'Gasoline', '104599', 'RRR-0I26', '1323660345', '99KPCKBYJPM714375', 'HAOJUE', '2022/2023', 'MOTORCYCLE HAOJUE DK150CBS', 1, 'active', 12, NULL, NULL, NULL, NULL, 10000, 36.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_levels`
--
ALTER TABLE `access_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `level` (`level`);

--
-- Indexes for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_activity_date` (`created_at`),
  ADD KEY `idx_activity_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_department_name` (`name`),
  ADD KEY `idx_department_code` (`code`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_ip` (`ip_address`),
  ADD KEY `idx_login_email` (`email`),
  ADD KEY `idx_login_time` (`attempt_time`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notification_user` (`user_id`),
  ADD KEY `idx_notification_read` (`is_read`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_permission_code` (`code`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_role_name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_user_id` (`user_id`),
  ADD KEY `idx_session_expires` (`expires_at`);

--
-- Indexes for table `station_prices`
--
ALTER TABLE `station_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subdepartments`
--
ALTER TABLE `subdepartments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `idx_subdepartment_name` (`name`),
  ADD KEY `idx_subdepartment_code` (`code`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `access_level_id` (`access_level_id`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_username` (`username`),
  ADD KEY `idx_user_department` (`department_id`),
  ADD KEY `idx_user_subdepartment` (`subdepartment_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_levels`
--
ALTER TABLE `access_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `station_prices`
--
ALTER TABLE `station_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `subdepartments`
--
ALTER TABLE `subdepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subdepartments`
--
ALTER TABLE `subdepartments`
  ADD CONSTRAINT `subdepartments_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`subdepartment_id`) REFERENCES `subdepartments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`access_level_id`) REFERENCES `access_levels` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
