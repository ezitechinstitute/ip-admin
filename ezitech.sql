-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 02:43 PM
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
-- Database: `ezitech`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `credit` double(10,2) NOT NULL,
  `debit` double(10,2) NOT NULL,
  `balance` double(10,2) NOT NULL DEFAULT 0.00,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int(11) NOT NULL,
  `image` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `loginas` varchar(255) NOT NULL DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `image`, `name`, `email`, `password`, `loginas`, `created_at`) VALUES
(1, 'data:image/jpeg;base64,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', 'Admin', 'admin@ezitech.org', 'Admin@7773', 'Admin', '2024-06-12 13:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_logo` varchar(255) DEFAULT NULL,
  `smtp_active_check` tinyint(1) NOT NULL DEFAULT 0,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` varchar(255) DEFAULT NULL,
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_email` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `notify_intern_reg` tinyint(1) NOT NULL DEFAULT 1,
  `notify_expense` tinyint(1) NOT NULL DEFAULT 1,
  `pagination_limit` int(11) NOT NULL DEFAULT 15,
  `interview_timeout` int(11) NOT NULL DEFAULT 30,
  `internship_duration` int(11) NOT NULL DEFAULT 6,
  `expense_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`expense_categories`)),
  `export_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`export_permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `author_name` varchar(255) NOT NULL,
  `author_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_counts_5', 'a:2:{s:6:\"Active\";i:1;s:9:\"Interview\";i:1;}', 1773486119),
('laravel_cache_mgr_perms_5', 'O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1773422724);

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
-- Table structure for table `complete_test`
--

CREATE TABLE `complete_test` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `technology` varchar(255) NOT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_projects`
--

CREATE TABLE `curriculum_projects` (
  `cp_id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_id` bigint(20) UNSIGNED NOT NULL,
  `project_title` varchar(255) NOT NULL,
  `project_description` text NOT NULL,
  `sequence_order` int(11) NOT NULL,
  `duration_weeks` int(11) NOT NULL,
  `assigned_supervisor` int(11) DEFAULT NULL,
  `learning_objectives` text DEFAULT NULL,
  `deliverables` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `curriculum_projects`
--

INSERT INTO `curriculum_projects` (`cp_id`, `curriculum_id`, `project_title`, `project_description`, `sequence_order`, `duration_weeks`, `assigned_supervisor`, `learning_objectives`, `deliverables`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'e-com', 'test', 1, 2, 2, 'Learning Objectives', 'Deliverables', 1, '2026-03-13 06:26:00', '2026-03-13 06:26:00'),
(2, 1, 'e-com', 't', 2, 2, 20, NULL, NULL, 1, '2026-03-13 07:19:28', '2026-03-13 07:19:28');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_supervisor_mapping`
--

CREATE TABLE `curriculum_supervisor_mapping` (
  `mapping_id` bigint(20) UNSIGNED NOT NULL,
  `cp_id` bigint(20) UNSIGNED NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `assigned_date` datetime NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leaves`
--

CREATE TABLE `employee_leaves` (
  `leave_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text NOT NULL,
  `days` int(11) NOT NULL,
  `leave_status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `intern_accounts`
--

CREATE TABLE `intern_accounts` (
  `int_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `int_technology` varchar(255) NOT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `int_status` varchar(255) NOT NULL DEFAULT 'Test',
  `review` varchar(255) DEFAULT NULL,
  `reset_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intern_accounts`
--

INSERT INTO `intern_accounts` (`int_id`, `eti_id`, `name`, `email`, `phone`, `password`, `int_technology`, `start_date`, `int_status`, `review`, `reset_token`) VALUES
(5467, '10', 'Nida Saeed', 'nida.saeed@example.com', '03006789012', '$2y$12$tvuZIPXDVWn24rFiMk/tQOvZYDU34bc56giT74OMyaZcWHiTRvc/2', 'Next.js', '2026-03-14', 'Active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `intern_attendance`
--

CREATE TABLE `intern_attendance` (
  `id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `start_shift` datetime NOT NULL,
  `end_shift` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_curriculum_assignment`
--

CREATE TABLE `intern_curriculum_assignment` (
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `curriculum_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `current_project_index` int(11) NOT NULL DEFAULT 1,
  `assigned_date` datetime NOT NULL DEFAULT current_timestamp(),
  `start_date` date DEFAULT NULL,
  `expected_end_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `status` enum('active','completed','paused','cancelled') NOT NULL DEFAULT 'active',
  `completion_percentage` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_feedback`
--

CREATE TABLE `intern_feedback` (
  `id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Open','Resolved') DEFAULT 'Open',
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_fees`
--

CREATE TABLE `intern_fees` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_leaves`
--

CREATE TABLE `intern_leaves` (
  `leave_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text NOT NULL,
  `technology` varchar(255) NOT NULL,
  `intern_type` varchar(255) NOT NULL,
  `days` int(11) NOT NULL,
  `leave_status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_projects`
--

CREATE TABLE `intern_projects` (
  `project_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` varchar(250) NOT NULL,
  `end_date` varchar(250) NOT NULL,
  `duration` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `project_marks` varchar(250) NOT NULL,
  `obt_marks` double(10,2) NOT NULL,
  `description` text NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `pstatus` varchar(10) NOT NULL DEFAULT 'Ongoing',
  `createdat` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_project_progress`
--

CREATE TABLE `intern_project_progress` (
  `progress_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `cp_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') NOT NULL DEFAULT 'pending',
  `progress_percentage` int(11) NOT NULL DEFAULT 0,
  `supervisor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supervisor_remarks` text DEFAULT NULL,
  `marks_obtained` double DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_remaining_amounts`
--

CREATE TABLE `intern_remaining_amounts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intern_table`
--

CREATE TABLE `intern_table` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `cnic` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `join_date` varchar(255) NOT NULL,
  `birth_date` varchar(255) NOT NULL,
  `university` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `interview_type` varchar(255) NOT NULL,
  `technology` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Interview',
  `intern_type` varchar(255) NOT NULL,
  `interview_date` varchar(255) NOT NULL DEFAULT 'Onsite',
  `interview_time` varchar(255) NOT NULL DEFAULT 'Onsite',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `intern_table`
--

INSERT INTO `intern_table` (`id`, `name`, `email`, `city`, `phone`, `cnic`, `gender`, `image`, `join_date`, `birth_date`, `university`, `country`, `interview_type`, `technology`, `duration`, `status`, `intern_type`, `interview_date`, `interview_time`, `created_at`) VALUES
(1, 'Ali Khan', 'ali.khan@example.com', 'Lahore', '03001234567', '35202-1234567-1', 'Male', '', '2026-03-01', '2003-01-15', 'GCU Lahore', 'Pakistan', 'Onsite', 'Laravel', '6 Months', 'Interview', 'Remote', '2026-03-10', '10:00', '2026-03-13 17:13:18'),
(2, 'Sara Ahmed', 'sara.ahmed@example.com', 'Karachi', '03007654321', '35202-2345678-2', 'Female', '', '2026-03-05', '2002-12-20', 'IBA Karachi', 'Pakistan', 'Remote', 'React', '6 Months', 'Interview', 'Onsite', '2026-03-11', '11:00', '2026-03-13 17:13:18'),
(3, 'Usman Riaz', 'usman.riaz@example.com', 'Islamabad', '03009876543', '35202-3456789-3', 'Male', '', '2026-03-10', '2003-02-28', 'NUST', 'Pakistan', 'Onsite', 'Node.js', '6 Months', 'Interview', 'Remote', '2026-03-12', '12:00', '2026-03-13 17:13:18'),
(4, 'Ayesha Ali', 'ayesha.ali@example.com', 'Faisalabad', '03006543210', '35202-4567890-4', 'Female', '', '2026-03-12', '2002-11-10', 'GCUF', 'Pakistan', 'Remote', 'Next.js', '6 Months', 'Interview', 'Onsite', '2026-03-13', '13:00', '2026-03-13 17:13:18'),
(5, 'Hamza Javed', 'hamza.javed@example.com', 'Multan', '03005432109', '35202-5678901-5', 'Male', '', '2026-03-13', '2003-03-05', 'Bahauddin Zakariya University', 'Pakistan', 'Onsite', 'Angular', '6 Months', 'Interview', 'Remote', '2026-03-14', '14:00', '2026-03-13 17:13:18'),
(6, 'Hina Tariq', 'hina.tariq@example.com', 'Peshawar', '03002345678', '35202-6789012-6', 'Female', '', '2026-03-14', '2002-09-25', 'UET Peshawar', 'Pakistan', 'Remote', 'Vue.js', '6 Months', 'Interview', 'Onsite', '2026-03-15', '15:00', '2026-03-13 17:13:18'),
(7, 'Faisal Mehmood', 'faisal.mehmood@example.com', 'Rawalpindi', '03003456789', '35202-7890123-7', 'Male', '', '2026-03-15', '2003-04-18', 'NUST', 'Pakistan', 'Onsite', 'Laravel', '6 Months', 'Interview', 'Remote', '2026-03-16', '16:00', '2026-03-13 17:13:18'),
(8, 'Zoya Khan', 'zoya.khan@example.com', 'Quetta', '03004567890', '35202-8901234-8', 'Female', '', '2026-03-16', '2002-07-12', 'BUITEMS', 'Pakistan', 'Remote', 'React', '6 Months', 'Interview', 'Onsite', '2026-03-17', '17:00', '2026-03-13 17:13:18'),
(9, 'Shahzad Iqbal', 'shahzad.iqbal@example.com', 'Sialkot', '03005678901', '35202-9012345-9', 'Male', '', '2026-03-17', '2003-05-20', 'UET Lahore', 'Pakistan', 'Onsite', 'Node.js', '6 Months', 'Interview', 'Remote', '2026-03-18', '18:00', '2026-03-13 17:13:18'),
(10, 'Nida Saeed', 'nida.saeed@example.com', 'Hyderabad', '03006789012', '35202-0123456-0', 'Female', '', '2026-03-18', '2002-06-08', 'NED Karachi', 'Pakistan', 'Remote', 'Next.js', '6 Months', 'Active', 'Onsite', '2026-03-19', '19:00', '2026-03-13 17:13:18');

-- --------------------------------------------------------

--
-- Table structure for table `intern_tasks`
--

CREATE TABLE `intern_tasks` (
  `task_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text NOT NULL,
  `task_start` varchar(255) NOT NULL,
  `task_end` varchar(255) NOT NULL,
  `task_duration` int(11) NOT NULL,
  `task_days` int(11) NOT NULL,
  `task_points` double NOT NULL,
  `task_obt_points` double NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `task_status` varchar(255) NOT NULL DEFAULT 'Ongoing',
  `task_approve` tinyint(1) DEFAULT NULL,
  `review` text NOT NULL,
  `task_screenshot` longtext NOT NULL,
  `task_live_url` text NOT NULL,
  `task_git_url` text NOT NULL,
  `submit_description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_test`
--

CREATE TABLE `interview_test` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `technology` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `inv_id` varchar(255) NOT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `intern_email` varchar(255) NOT NULL,
  `intern_id` varchar(255) DEFAULT NULL,
  `technology` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 6000.00,
  `received_amount` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_date` varchar(255) DEFAULT NULL,
  `next_due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `received_by` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `approval_status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `invoice_type` varchar(255) NOT NULL DEFAULT 'internship'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_approvals`
--

CREATE TABLE `invoice_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `inv_id` varchar(255) NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `requested_by_name` varchar(255) NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by_name` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `knowledge_bases`
--

CREATE TABLE `knowledge_bases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `visibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`visibility`)),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager_accounts`
--

CREATE TABLE `manager_accounts` (
  `manager_id` int(11) NOT NULL,
  `assigned_manager` bigint(20) UNSIGNED DEFAULT NULL,
  `eti_id` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `join_date` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `comission` double(10,2) NOT NULL DEFAULT 1000.00,
  `department` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `loginas` varchar(255) NOT NULL DEFAULT 'Manager',
  `emergency_contact` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager_accounts`
--

INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(1, NULL, 'ETI-MANAGER-001', 'data:image/jpeg;base64,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', 'Muzammil Javed', 'onsitemanager@ezitech.org', '+92 337 7777860', '01/07/2024', 'Wa33aziz', 1000.00, '', 0, 'Manager', 0, '2024-07-28 14:08:01', '2025-12-10 16:51:55', 0.00),
(2, NULL, 'ETI-MANAGER-002', 'data:image/jpeg;base64,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', 'Kashif Saeed', 'remotemanager@ezitech.org', '+92 334 4444722', '01/07/2024', 'Kashif@1122', 1000.00, '', 1, 'Manager', 0, '2024-07-28 14:08:01', '2025-01-22 18:31:39', 0.00);
INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(5, NULL, 'ETI-MANAGER-003', 'data:image/jpeg;base64,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', 'Global Manager', 'globalmanager@ezitech.org', '+92 336 6666559', '01/07/2024', 'Ezitech@786', 1000.00, '', 1, 'Manager', 0, '2024-07-28 14:08:01', '2025-10-02 15:28:51', 0.00),
(13, NULL, 'ETI-SUPERVISOR-382', 'data:image/jpeg;base64,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', 'Qamar Naveed', 'qamar@ezitech.org', '03176349954', '2024-02-21', '12542', 1500.00, 'Web Development', 1, 'Supervisor', 2147483647, '2024-10-06 21:45:06', '2025-03-19 12:56:56', 0.00);
INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(14, NULL, 'ETI-SUPERVISOR-348', 'data:image/jpeg;base64,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', 'Aamir Jamil', 'aamir@ezitech.org', '', '2024-10-01', 'Ezitech@786', 1000.00, 'Python Development', 0, 'Supervisor', 0, '2024-10-21 15:49:35', '2025-06-23 17:05:51', 0.00),
(15, NULL, 'ETI-SUPERVISOR-755', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/4gHYSUNDX1BST0ZJTEUAAQEAAAHIAAAAAAQwAABtbnRyUkdCIFhZWiAH4AABAAEAAAAAAABhY3NwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAQAA9tYAAQAAAADTLQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAlkZXNjAAAA8AAAACRyWFlaAAABFAAAABRnWFlaAAABKAAAABRiWFlaAAABPAAAABR3dHB0AAABUAAAABRyVFJDAAABZAAAAChnVFJDAAABZAAAAChiVFJDAAABZAAAAChjcHJ0AAABjAAAADxtbHVjAAAAAAAAAAEAAAAMZW5VUwAAAAgAAAAcAHMAUgBHAEJYWVogAAAAAAAAb6IAADj1AAADkFhZWiAAAAAAAABimQAAt4UAABjaWFlaIAAAAAAAACSgAAAPhAAAts9YWVogAAAAAAAA9tYAAQAAAADTLXBhcmEAAAAAAAQAAAACZmYAAPKnAAANWQAAE9AAAApbAAAAAAAAAABtbHVjAAAAAAAAAAEAAAAMZW5VUwAAACAAAAAcAEcAbwBvAGcAbABlACAASQBuAGMALgAgADIAMAAxADb/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCAGfAZ8DASIAAhEBAxEB/8QAHQABAAEEAwEAAAAAAAAAAAAAAAgCBAUJAQMHBv/EAFIQAAEDAgMEBQYLBwEDCgcAAAABAgMEBQYHEQgSITETQVFhcRVVgZSy0QkiMjU3QmJydZGhFBYjJLGz4fBSY8EXJSczNFNlk6PCOENzgpKi8f/EABwBAQADAQEBAQEAAAAAAAAAAAAEBgcFAQMCCP/EADgRAAEDAgIJAgUCBQUBAAAAAAABAgMEBQYREhMUITFBUVJxNJEiNWFygSOhFSQyscEWJWKC0UL/2gAMAwEAAhEDEQA/APDfIVm80UPq0fuHkKzeaKH1aP3F+D+iNkg7E9j+e9ol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQrN5oofVo/cX4GyQdiew2iXuUsPIVm80UPq0fuHkKzeaKH1aP3F+BskHYnsNol7lLDyFZvNFD6tH7h5Cs3mih9Wj9xfgbJB2J7DaJe5Sw8hWbzRQ+rR+4eQLKvO00SeFMz3F+BssPJiew2iXvUsP3esfmuj9WZ7h+71j810fqzPcX4GzR9qew2iXvUsP3esfmuj9WZ7h+71j810fqzPcX4GzR9qew2iXvUsP3esfmuj9WZ7h+71j810fqzPcX4GzR9qew2iXvUsP3esfmuj9WZ7h+79l6rXR+mmZ7i/A2WJeLU9htEvepYeQbNy8kUPq0fuMdiCx2aO0VD22miTTc4pTsRU+MncfQGOxE1XWapROxvttI9ZTQpTPyanDoSaGolWpYmmvHqZEAHQOeAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADH39yJaKlO5vttMgY/ECf8z1K6cfi+20jVvpn+CVQ7qli/VDIAAkkUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA9yAAB+cwAAegAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFhf/mep8G+20vywxBws1T4N9tpEr/SyfapKot9SxPqhfgAlkUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAZgDkmo0VdEamqquiHumS+yVmHmq+K5V8K2OyP0clVUs+NInXuR818V0QhV1ypbcxXVD0T6cyVSUNRXu0YG5qeJW+31t3rIrdbKSaqqZ3I2OKFive5V7ETip7NJse53QYPTFTsPNc/TfW3Nk1qkj0+Vu8te7XUnllLs85eZRUbG4ftDZa9yJ01fUoj53qnYq/JTuQ9QWNm7up2mc3DHMz5f5NuTU68zQKDBDNUq1i/EvTkaVaukrLfUyUdwpZaWohcrZIpmq1zFTgqKi8UOpF1Nqmb+zZlznBSvfd7U2jujWr0VxpURkqL9pU+UniQTzl2VMx8pJJrglG69WRrlVK6kYq9G3q6RicW+JZLPi2kuOTJV0H9FK5dcLVlvcro002dU5Hi4HJdNNV7gW/cu9N6FY55cwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAY/EPzLUeDfbaZAx+IfmWo8G+20iV/ppPtUlUHqmeTIAAlkUAAAAAAAAAAAAAAAAAAAAAAAAAGZwrg7E+NrtHZMK2aquNZKujY4I1dp3uXk1O9T5SzMgbpyrkn1P1Gx0rtCNFVTDKuia6Kvgfe5X5I5h5t3BtLhWySOpkVOlrpfiU8ada7/J3g3UlLkvsG0VGkN8zbqW1k66OS1wO/hN++763gnAl/ZLBabBb4rTZ7fBR0cCI2OGFm4xqJ2IhQrxjaOPOGg3rwVS82fB8k2UtZmidDwLJXY1wJlx0F5xHHFiC9t0d0s8adBE77EfLh2qSKpoI4GJGyNrWt4NRqaIiHcjW6JwOdE7DOqmsnrHrJO5VVTQ6K309AzQhbkcaN1OdECrpx0OiWVGKq9IiInMiquRMVcjudoiamMutXbKaimnu0sEVKxNZXTKiMRvXqq8NPE8bzl2ssvMqGSW5lel4vSN1bQ0qo7dX/eP5NT9SCObe0XmTnBVPZe7q6jtSOVY7bSuVsSJ9rrevjoWO04arboukiK1vUrd3xLR25FZmjndD7Taruuz9cr5M7LCikW9JOn7ZUUa7tC5dePBflO728CPnDq5cgjWouqc9NPQDX7bRfw+BIdNXZdTJa2r2yVZdFG59AACeQwAAAAAAAAAAAAAAAAAAAAAAAAAAAY/EPzLUeDfbaZAx+IfmWo8G+20iV/ppPtUlUHqmeTIAAlkUAAAAAAAAAAAAAAAAAAADmeOXR4gHZTQVFbUx0VHTyT1Eq6MijarnOXsRE4qew5N7LGY+bdTDXsonWiy7yK+uq41ajk+w3m5f0J25Q7MmW+UlNHPbLY2uu+iJJcqpqPlX7qcmJ3IVS8YrpLdnHCum/8AYs1pwvWXJUlf8LF68SJ+SuxBizFz6e+ZkrLYrQqo79kb/wBqmb+qMRfz7icOX+VGBctbW204OsMFBEifGka1Fkl73vXi5T6xsLmtREVNE5HaxqtTRVMwud7rbs/Sndk3onA0y2WSktbco25u6qUtha3TRV4FegVdE1KXPRNOGpydyIdnipVroUdJwVdUTQw+JcYYdwja5bviO609vpIU1dLM9Gp6O1SGWdG3lUVLqix5SUm4zix11qW8V74mf8VJ9Ba6q5uRsDV88jmXC7U1tbnKu/oSmzNzwwFlVbHV2LL3BTyK1VipmrvTTLpya1OfiQYzr2z8eZiS1Fowg5+HbK5FZ/Cd/NSova9Pkp3J+Z4HfsQXvFF0nvGILrU19ZO7WSaoernL+fLwQx+iJwTkahZ8H01AqSVPxv8A2QzO7Ysqq9dVD8LSuSWSaR00r3Pe9Vc5znKqqq9a69ZTr3HALgjEamiiZIhVP61zXeoAXgui/wBQfo83cgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWF/42apTub7bS/LC/wDzPU+DfbaRK9P5aT7VJVF6lmXVC/ABLIoAAAAAAAAAAAAAAzy4niuRAFXTTv4GTw7hq/YtucVmw1aam41ky6Mip2K5fT2J3qTAyS2Df+pxBm9VKu8jXstNM7RG9ekj+vsVEOPc77RWpirM/N3ROJ1rXaKu6PRIW7upF/LbKLHua1zS3YPsctS1F0kqnIrYIvvP5J/UnBknsT4JwJ0F7xvuYhvLUSRI5G60sDvsM+sveveSGw3hWxYUtkNnw9Z6a30dO1GshgjRrURPBOPpM3upoiGXXfFdXdP02ros6c/c0u1YVpqB2tlTSeWlLSRUsbIKeJrI2IjWtamiNTsRE5IXbW6c0TgNxEOVVE5roVfLeri1NbopopwCcgrkTmUPlY3m5EPG85dp7LnKKmkgra7yld0TWO3Uj0dJ/wDcvJiePE+0MEtS7Qhbmp8KmqhpGaczskPXq2thpIHzTysijYm8973IiNTt1XgRgzp23cH4L/aLHgRI8QXZirGsjXL+zRO73p8rwQijnDtP5kZvSS0lVXOtVlfwZQUb1a1yfbcnFy6dXI8gTXRE7OrsNAtGClRUmr1/6meXfGjpEWKhRcup9fmLmvjrNO6yXTGF9mqt529HTou7BCnUjGdneuq958giaKq9vMA0SCmipY0ihTJqcihzyyVL9OVc1AOS/sNgveKLlFZ8PWupuFZOujIYGK9zl7NE/wCPA/csscDdORckPy1jpF0WJmpj+HWfXZeZVY5zRuiWvB1jnq1RdJZ1buwxJ2uevBP6knskthCqqmQX/NuoWJq7r22qmf8AG07JX9Xg0mVhjB2HcHWyGz4btFNb6OFuiRwRo1F4aarpzXvUot4xtFAqxUKaS93IutowfLVZSVu5vQglmXsjWzKDI664yvt2fccQs6BrUj+LTwb8jUcjU5uXTXivaRWTw0NnO21o3Z+vvfJTp/6iGsdeak7B1XNXU75p3aTtI5+K6SG31LYadMk0TgAFwKwAAAAAAAAAAAAAAAAAAAAAAAAAAADGYkVfI1Qic9G+20yZjMR/NNR91vttI1b6Z/glUHqmeTJgAkkUAAAAAAAAAA50PUsntnLMTOSeOay0TaG076JLcatdyJE147ic3r4cO1UItXWQ0MazTuyahIpqaWrlSGFM1U8vhhlqZWQU8bpJZHI1jGIqq5exETiqkj8mNinG2PugvONXSYfs79HpE9n81Ozub9TxXj3EscmtlTLrKdkNfDSJdryxPj3CsYivRfsM5M/qe2xRpGu6iInAza841kn/AEaBMk6mg2fBrY/1K/evQ+GyzydwHlXa22zCViipl0TpKhyb00q9rn8/y0TuPvUYiaIirog0UqKLJK+ZyvkXNVL5DTxUzEjibkhxuoF4cjkxt9vVsw7bZ7veK6Gko6ZqvlmlejWsanWqqflEVdyH0c5GIrlL50m6uinxeZGbeBssLW66YuvlPSNRu8yJV1lk7msTVV/Ii3nXt308Esthylpm1DtVY+7VMaoxq9sTF+X4roniQ6xPirEeM7o+9YpvFRcq2RVc6WZ+qpqvJE5InciIXCzYQqa/KSo+Fn7qUy8Ywp6POKm+J/XkhIfOfbexjjZJ7Ll/G+wWt+rHVOqLUytVFTnyYi93EjNNUVFVNJUVc8k0sjlc6SVyuc5V61VeKlANMt9ppbYzKBuS81M3rrlU3F+c78/pyAC8Ai69v5HTX4UzUhb1BUyN8z2xQsdJI9d1rGoqqq9SaJxU9Nym2dMys36uN9ktS0lq3kSW41fxIUTr3V+uvhqTtyY2ScucqliuTqZt6vDWo5a6rYiq13NdxnJvjxUq93xVR23NjV0n/Q79qw3W3NUerdFnVSJuTOxljzMJYLxi1H4es0io5EmZ/MTt691n1fF2hObLHJTAGVVtbb8KWSGGTdRJaqRqPnkVO168dO5OB6A2FWtRqIiInV1aFSRoiKi6egy2532tuzs5HZN6IahbbBR21vwJmvUMja3giFapog1OFdzTQ5C9TtbkTJDwPbb47P8AfEX/AL2n/utNZC8zZrttSMXIG+Ir2ovSU+iKqf8AeIayTV8B5rRPX/kZHjdf59vgAAvRUQAAMwAqonFVKnskiVqSxPZvJqm81U1TtPyrmpuVQqLyQpAB+kXMIAAAAAAAAAAAAAAAAAADGYj+aaj7rfbaZMxmI/mmo+6322kat9M/wSqD1TPJkwASSKAAAAAAAAANf1PS8pNoDMfJytZ5CurpbW9yLJbqpVdA9F04p1tVdOaHmi/JXt5mw3LvZ8y6zf2eMG/vFamx3BLQ1Iq+nTcnYvHT4yfKTuUrWI7hS0UbG1jNJjlyX6HfsNDU1kzlpH6L27/J9Xkztc5c5othtVTVtsl9enxqGqeiJI7r6N3JU16uZ7tDNHIuqPR3Uaxc39kzMrKeea8WyCW9WWNyvZWUifxoU56vYnFvi0ymTO2PmBlo6Gz4mSTEFmY5GOZUOX9phb9l689OxSj1WF4auNaizv0k7c96F0pMSTUciU91arf+XJTZbqhyec5X55Zf5tW9KrCl9hlmRE6Wlk+LNEunJWL/AMNUPQWyI5dEXXqKhLFJA9Y5WqipyUuMNTFUMR8TkVF6HaY+8Wi3X63T2q7UUNXSVDVZLDMxHse3sVF4KX6BEPmiqm9D6uaj0yUhnnRsI2q5JPesqJkt9SiOe62TO1hkXnpGv1F7uRDHFuCcVYCu0ljxdZqq3Vkaqu7PGqbzU+s1eSp3m5hzEXVdT5PHmWOCsxrVJacW2Gnr4XN0a57f4jO9r+aehS22jFlTQK2Oo+NifsU67YQp6zOSn+F37KaedF0RdOC8UC8ObV4kqc5dhjFWGZJbxlfLJe6BeP7DLolREnY1eT0/Iv8AJXYSvV66K9ZsTvt9M7R6WyBdJnt7HvT5PgnEvy4ttuo1+l+OZRP9NV6z6hGb+vIjXgTLrGWZd3SzYNsFTcZ0VOldG34kSa83u5NJs5L7C+GsMOgv2ZMrL1cWK16UTOFLE7XrReMnp4EksGYCwrgS1w2XC1lprfSxfUhZorl7XLzcvep9IjGpyQz+74tq7jnFD8DP3X8l/tGEqWiylmTScWdutVFa6WOkoKSKngiajI44mI1rGpyRETghdxtRNdEKwVRd65qW5rUamScAcap2odcsqRpqp5fmvtDZeZRUbpMQ3iOWue3+Db6dUfPIv3erxU+kMEk70jiaqqvQ+M9TDTN05nIifU9NmqIoo+ldIjWpxVXLoR5zp2ysAZapPabBIzEF7bqzoaZ6dFE/7b+XoQiVnLtbZiZqPmttvndYbI5ValNTPVJZU7Xyc117DwxdXLvOcqqvNV6y/WjBLpESW4Ll/wAf/SgXnGSNVYaD8r/4ffZo535hZuV76nFd5kWlR2sNBCu7TxJ1aN615cVPgQDRaSkio40iiaiJ9CgT1EtW9ZJlzUAFzb7dX3asit9topqqoncjWRQsV7nKq6aIiH3e5I2q56oiHzY10i6LUzVS27j6HBOX2Mcxbu2x4OsVVcqlVRHrC34sSdr3LwahJLJfYVv+IXQXzNSV9soV0e22xO1qHp2PcnBno48SbOB8usIZfWqKzYSslNb6ZiIi9ExEc7vc7m5e9SkXjGcFO1YaL4ndeRcLRhKoq3I+q+FpGnJXYSsGHpaa+5ozx3itZpIygj1/Zonfa63+nge1Zj7OmV+Y9iZZrrhqlpXU8fR0tTRxJFLTppoiNVvV3LwPVEY1OKINxNddVM5nvFdVTa+SRdLkaJT2Sip4dQ1iZGsfOjY9zFywdNd7JDJiGxx/HWanZ/FiYnPfYnHh2oeCPR0aq2RqtVF00XguvYbraiBkqKx6I5qt0VFPAc6dj7LvM1s92tkDbBfHoqpVUzE6OV3+8j5L4lws+NXxrqq9FVOpULtgtM1loly+hrP7wfZ5p5UYoyixCuHsSpSvc7V0E9PKj2SsRdN7Tm3wVD41rXyaJGxXquiIjU1VVXqNHhqop4kmYubVM9mhkp5FikTJxSuqeBVuvVqua1VRE3lVE1RE7fA98ya2Pcw8znQXS9wyYesj9HpPOz+NK37DPDrUkJnVkTl9k/s3YlgwvZ2JWLBEk9bLo+olXpG6qrl4oncnA4Nbiqjp6htNF8TlXLdyO1S4drKmndUOTRaiZ7zX8munFU9AOG/JQ5LKi5lfam7eAAenoAAAAAAMbiJE8kVGvY322mSMdiJNbPUcdODPbaRq30z/AASqD1TPJkQASSKAAAAAAAAAo7Ta1su8cgsE8edrZ/VTVL169hta2XE/6AsEp/4ZH/VSg499NF93+C84H9XJ4PU300c7FbIxHNVNFavJU7NCPGdWxzgPMnp7zh6NMPXt6KvS07E6GZ3Vvs5J4tJHM4oFZqnDmZpSVc9E5JIHKimjVdDBXM0J25mpfG+VGbWz/iGOrrqeqt74Xb1LdKF7uhfouvyk5fdce+ZK7eNVb2wWTNumdURN0a2600a76J2yMTn4t/Im1fcPWjEVumtd7t8FbSTt3ZIZmI9rk8FIhZ2bCNDWJPfcpKptFUcXutlQ9eid/wDTdzb4LwLlFfKC9MSK6syfw0k/yU6eyV1mfrrY7Nif/JLHC2NMOYztUV5wzeKWvpJ2o9skMiPREXqXTkvcpnUfw7TUbZ8QZv7POKOip33Gw1sT16WllaqQzoi8dW8nN704kxslNuPCOL1gs2YjG2G6KiMbU660s7uHJ3Nir2LwOXcsM1FKmvpl1ka804nStuKYKl2oqUVkn13ErkXVNThzdeRaUNwpq6FlTSTtmhlRHMfGu81U7UVOBdo9F6itcFyXiWlHI5M03oUrEi8F0DIWM1RGomvYVI7VdCo8yTofopRqJyKgD0HWsqJrxPmccZi4Uy9tEt8xXeqe30sTVdrJIiOfp1NbzcvgeJ7UW0tiPJxnknDmDquWpqI/iXSpYqUcSr2Knynd2qEAMbY/xfmJdXXjGF9qbjUK7eZ0jviR9zW8mp4FnseF57smtc5Gs/cqV7xRFbF1MaZv/YknnTt2YgxD09jytp32uheisdcJm6zvTtYnJviuqkUq64V91rJrhdKyerqahyulmnkV73qvaqnQnBNAajbrPSW1ujA1M+q8TM7hdaq5uzndmnTkPBAAdfPcczdyBU1quVGoi6rw4JxPvMr8kMxM26xlPhSxyuplXSStmRWU8adqu6/BNSdGS+xtl/lw2C6YgYy/31ioqTzxp0MLvsM7U7V4lcu2J6K1tyz0ndELBasO1dzdmjdFvVSJuTOyPmPmqsF2r6dbFYnLqtVUtVssqf7uNeK+K6ITtyl2eMuco6Rn7vWdktwcmktwqER88nboq/JTuQ9OhpY4GtjjaiNamiInJEO1G6Iia/oZfdcRVt1cumuTeSJ/k0y14dpLa1FRM3dVKEhRE0QqRqppx5FXI43kOFuQsH0KjrWRU58C1ul2orXRyVtdUx08ELVe+WRyNa1E5qqryIp517dGG8NpPY8s42Xq5tVY1rXa/ssLk5qmnGRfAmUdBU3B6R07c1/Y59fc6a3N053ZfQkljTH2FsBWma94rvVNb6WJuquleiK7uanNy9yIQszq27Lte2zWLKmBaGlcisW51DUWZ6ctWMX5PivE8Gnqc39orFfRyvuOIq+R3xWNb/Bp01XRERPisTv5krMkNhGzWZYMQZqVLbnXJpI22xLpBGqdT3c3r+hbo7ZbLCzWXB+sk7U4FSludyvi6qgboR9y8SMGXeS2bGfF6krKOCoqWSO1qbtXuckaarz31+UvchOLJTY/y9ywSK7XSJL9fGoirVVUaLHG7r3GLwTxXVT3O02SgstFDb7VRQ0lNAzdjiiYjWNTuRC/bHuppqn5HHumI6q4fpxfBH0Todi14apqL9Wb45OqlEUEbG7rWojeWiIeMbYrWps/Yp0T/wCVF/cae2ImiaHim2L/APD9ilO2KL+405du9ZH9yf3OrdMm0MnhTVqnIAH9BmBNXcAAD9AAAAAAAxmJeNmqE7m+20yZjMS8bNUeDfbaRq30z/BKoPVM8mTABJIoAAAAAAAATcAqa8NdO/0mzvZFx3hS9ZN4Zw/b73TS3K1ULaerpekRJY3ovW3n18zWIX1kv17wxc4r1h261VuroF1jmp5FY5PHTmncpXsRWVbzToxrslbvQ7livH8IqFkVuaO4m6OJ7VbqinYjkXkpBTJTbwnpkp8P5u0/SMXRrLxTt46cNOkjT9VT8iZmFMZYdxjbYbxhq7U1fRzJqyWGRHJ6dOXpMgr7VV2x+hUN3deRrNuvNJc2/ou39DP6odb41d9XU7N5F5A53HcdY+MzByqwVmdapLPjDD8FbE5qo2RW7ssa9rHpxavgQizr2GsXYQSovmXLnXu1t1e6lc7Sqhbz4f8AeIidnE2InTMxJW6LyOtbrzV2tf0XfDzReCnGudjpLm3425O6pxNVWVG0ZmjkncUtsVRNUW6OTdntNertG6cFRirxjX9O7rJ2ZM7UuXebcUdJHW+Sbw5E37fWORrtdOTHcnejiXeb2zTlzm3SSS3W3NobqjVSO40qIyVq9W91PTuUgnmxsv5oZO1S3SCCW5WqJ+9Dc6Brt+PjwVzU+NGvfy7yzK60Yjair+lN7IqlVRt2w07PPWxf2Q2lMmi0130TU7Ee13JdTW9k7tr42wHJFZMbslxBZm6M6VztKqBPHk9ETqXjw5k5st848B5pW1tywffIKlEanSQOduzRL2PYvFpWrnZay2O/Ubm1eab09y02u+0l0b+muTuaKfenC8iljt5EXUrOQh2jDX/DVoxLbpbXfLVBXUs7VbJFPEj2uRe5SHudWwZE9s1/ykquge1Fe61VL1Vru6N68vB2pNs6ZPjcNF9B0KG51Vtfp07svpyU5lfaaS4tynair1y3mmPEWFcSYQuctmxPZqm3VkKq10c7FTXTrReSp3oYtfi8+zX0G3zMPKXAmaFsfa8X2CCsRyaMl3d2WNepWvTiikYaf4PGjbjR09RjB78Mo7pEhSPSqXVfkK75KJ36Gi2/HFNNH/Npk5OnMzuvwbUwyIlKuk1f28kOsK4OxPje7w2LCtlqbjWTuRqMhYq7uvW5eTU71Jm5LbB1Db3U9+zXqW186KkjLZTuVsTF7JHc3+CEm8BZV4Ly0tMdowhZKehhaiI5zW6ySd7nrxVT7BjUYiadRWrvjCpr11dN8DP3LHaMIQUapLUppO/Yx9lsFssNDFbbRboaKlhYjWQwsRjGp2IicC/3FRU0ReZ3HGuhUHKrl0ncS4sjaxNFvA5ON5uumpS5+6mvA8+zSzuwDlNbX1+K73DDKuvRUrF35pV+yxOPp5H0iiknejImqqryQ/E88dMxZJFyRD7+WaNiaq9E0PCc5trPL7KfpLfFVpe7yiLpRUciO3V+27ijf6kTs59svH+YzprPhN0mHrM9Vj/hP/mp289HOT5Pg38zDZRbKWZebczLvcYX2a0Su35K6sa7pJtV5sYvxnKvavAttLhqKlalRdXo1vbzKZWYmmrH6i2MVy93IwuZ2fmbGed0bbKyoqG0csu7T2m3I7cdqqaIqJxkXx4dyHrOSGwpiPErKa+Znzvs9uciPbbol/mZG666OXkz+pK3KPZ1y4ylo41sVobUXFWaS3CpRHzPXr0X6qdyHqkUTY+DU0Q/FdidsbVp7UzVs4Z81PrQYYWd203V2m7pyQ+XwPlthLLyzx2XCljp6CnjTRViYm/Iva53Ny+J9PHEjU1RunoO4FTc98jtN65qpcIoWQs0I0yQp0+Lpoc7zU6ylZGomqqfO4ux5hfA9rlu+KLzS26liRVV88iN14ckReKr3IGsdIuixM1USSMhbpPXJD6JZo0XRXpqvLvI77aWOsK2/J284Yq75SsutyaxlPSb6LI7RyKq7vUmidZ8nbNtGmx1nBYMBYIs+7aK2uSCor6pNHzN0XgxnVxTmpF/a3p6qDP/ABQtQr92WWKaLeXVEY6NmiJryTVF4FqsNhmluDI6j4d2knXcVO+36FLe91Pk5FXRPIPSDlV1XVTg2NFzMiamXAAA9P0AAAAAADGYj+aajwb7bTJmMxHwtFQvc322kat9M/wSqD1TPJkwASSKAAAAAAAAABz5gABePFT67LzNbHeVlzbc8G36ek1ejpadXKsE3c5nJfE+RB8Z6eKqYsc7Uch9Ip5aZ2nC5WqbDMldtrBmNUgsmPmsw/eJNGNkc/Wlmdy4OX5C8uCknqSvpqyBlRSTsmiemrXsdvIqduppU3U6uvvPX8ndpvMjKGoigoa/ypZ0VN+31aq5Ebr9R3Nq/oZ5eMEZI6agXf2l8s+NVarYa3h1Nq7V1TUaIeK5P7UWXWbVPHTUNxbbbwqfHt9Y5Gya/ZXk5O/9D2SKVHKmipxTXmZ/UU0tI9Y526Kp1NCpauGsjSSF2aKdyonYhbVdFT1MT4ZYmOjkTdc1zdUVO9OSlzrqg07z4IvND7uaipkqbiMOdOxPgfHvTXrBqsw9eHIr1ZGz+Wmf9pv1VXtQhZibBGbmz5iqKqqorhZa2J38tcaRy9DKndInBU+yptwVjV4Khh8S4Ww/ii2S2nENqpq+jnarHwzxI5qp/wACx2zEs9C3U1KJJF0UrVzwzBVrrabOOROaEN8ltvSJHQ2PN2kRipoxt1pWLuqvbIzXh4oTGw/imyYltsV3sN0p6+jnaj2TQSo9qovh48iG+dGwcxHTXvKKpVvHfda6qRVRevSJ/V4Lw7yOeG8d5u7P2J3U1FLX2mpgfpPbqtHLDLp/tMXgqcOaHVms9vvjVntbtF3Nq8DjxXq4WJyQXJuk3hpG25NVTXU5REIxZJ7a+CseLT2LGO5YLy/SNFkf/LTP+w/q1XqUkpS1sNTE2WnlZIx6atc1yKioVGrop6KTVztVFLjR3Kmr2I+B6KXOidg0TsQpSROsqRUXkRScNE7BonYUufulrVV8FJDJU1M8cMcaK5znu3URE5rr1HmfI8VzU4qXUjtEQweKcYYdwhbJrvia701vpIGq58s8iNan58yO+du2/g/BjKiy4BYzEF3brGsrV/loHcvjO+svcn5kM7zijODaFxTHTVElxvtdNJ/DpIGL0MKdzE4NRO1Sz23DNRVok1R+nH1Uq1yxRDTrqaT45OiEic6NvCoqunsOUNP0bOLFutSzi7vjZ1dyuI+YPyzzd2gcSSVdFFXXeZ79am51rl6KJOxXrw9CdhJfJXYQo6bob7m5OlTM1Eey1wP0jb3Su+t4IS/sGGrNhu3w2yyWynoqWFNGRQxo1qJ6DoyXq32ZNTa2I53Ny7/Y50Nnr7y9Jbm5Ubx0ep4BkvsXYAy96C8YmZHiK9M0crp2fy8LvsMXn4uJHQ00cLWxsYjWtRGtROCIickO7c4rx59xyiadZUaqtqK6RZKh2kpb6Ogp6BmhAxEQ4axG8kOeBwrtCiSbcaq6pwIxMKnu0XXuLSuuVJb6Z9XXVEcEMbVc+R7ka1qdqqvBDxnOXasy6ynZLROrkvF53V3LfRvRzmr9t3JqfqQRzc2kMys3qmWK63V1BaVcvR26kcrI0b1I/revjwLBa8M1t0VHIitZ1UrV1xPR21dBHaT+iEsM6tuPC2EnTWPLqOK/XNurXVOv8rEv3tdXrr2fmQjx7mZjbMu7uvOML9U1suqrHGr1SKFFXXRjE4In6nzC/wCuBwajaMOUdqRFa3Sd1UzC536sub1WR+TeiH1uUVfPbM1cJ3CCZY3xXil+Nr1LK1F/RVPV9uSh/Zc86io1RUqrdTSovciOb/7THbJ2ScmbOOvKdXXPpbZhySGrmdHp0kkm9qxiKvJNWrqvYSh2tNmuhzFs9XmDaaqeDEFpoVRjFdrFPFHq5WqnU7sU4lwvFNR36NVXg1Wr9MzrUFsqZ7JIjU56Sfjia6wERUTRyaKnMF6TLLNOBUvoAAAAAAAAADG4jRVs9QidjfbaZIx2IeFoqF7m+20jVvpn+CVReoZ9yGRABJIoAAAAAAAAAAAAAAAAB4qIoOynnnpZ2VVLM+GaNyOZIxytc1U5Kip1klsmNtvGmCf2azY9bJf7S34nTqv81C3h18nomnXopGUHOuNppbmzRqG/nmT6C5VVufpQO3dORt/y7zcwNmfamXTCN+p6xqom/Ei7ssa9jmLxQ+zZK1ya6mmLDeKcSYOucV6wxeqq2V0SorJoH7q+CpycncqKTHyT28qaZkNhzbpm08qaMbdKZqqx69sjPq+KcDM7vg6poUWWm+JnTmho9nxfBVqkdV8Ll58ibKOReQcmvUYfD2JrLie3Q3awXOmr6KdEVk0EiPa7wVDMIuqlOVFauS8S5Me2RM2rmUOj3mqmh8NmTk1gPNK1utuLrHDUrppHO34s0S6c2vTin9D704VEXmfqKR8L0kjXJfofmaCOoYscqZoa386tinGuA/2i+YHbJiCzt1kdE1ulTAxOpWp8vTtb+R8nlJtRZnZOTx2qpqJLpaYnIx9trnKjok14oxypqzwXgbR5ImKxUVOH5nimc2yvlvm1BLWS0fki9K1VZcaNqNc53V0jeTk/UttHiZlRElNdmabeS80KbW4ZlpX7Ran6K806l/k7tKZcZtU7IrVdmUd13U6W3VT0ZK1y/wCzr8pO9D1pKliJrqunPXTgarc1NnbNTJO4pdJqWeot8Em/DdbfvOazTkrtOManZLtY54T4RTB8mKXoiJuLcGs0rHt5bqyJ/XTU+kuEo6vKa2yorF4/Q+cOLJKJqx3Fio5OH1J15ybUOW+UVO+lrrilyvGi9HbqVyOeq/aXk30kFs1tpTNDOmrW2OqZaK2zOVkVroN74+q8N9U4vU5yo2ZMz85qtLusM9vtcz9+e51zVTpU7WIvF695OzJvZly2yjp45rdbv2+77qI+41aI6TXTjuJyYnch9XfwnDiIifqzf2PiqXXEbt+cUf8AcifkvsP4uxqlNfsfSPsFqdpI2mREWqnavcv/AFaePHuJw5d5SYJyxtTLVhCxU9FHoiSSo3WWVU63PXiqn2LImtTRE0RDtK5cb1V3Nf1nbuicC0WyyUttTONM3deZQjERETQqTgmhyUq7Q5CqiHZOdUOt8qNVNOswWL8aYcwXbJbzie8U1uo4U+NLPIjU17E7V7kIZZ1beVXWJLYcoqVYI+LH3aqZ8brTWONf0VfyOjb7VV3R2jTtXLryOXcbxS2xmlO7f0JX5m51YAyttrq/Fd+ggeiax0zHI6eRepGsTjx/Ig1nJtr46x9+02XBfSYds0mrFe1381M3lxci/ETTqQj9fMQ3vE9xku+ILrU3CtmXV888iucvuTuTgY5G6f8A8NNs+D6WiRJKn43fshml0xfVVqqyBFa39yuSR80r553vkkkcrnve5Vc5V5qq9ZSAXJGo1NFCpZqq5u3qAAfpFyPFb0PfdkTPKhyjxhUWq80kkluxE6GB0sabzoZUcqNdp2Lv6E2dpvHNywHkze79a6FameaH9lTjokaS6tV69yamsXAsC1ONrBCia79ypm/+q02wZuYShxtlnfMLzQtkWtoJWMaqapvo3Vq/noZZi6npqa6RTKn9WSr+DSsMT1E1smiReCLkag9VXi5dVXn4g7Kinlo6iWknbpLA90b07HNXRf1Q6zT4nI6Nqt4KiKZw5rmvVHcQAD9ngAAAAAAMbiL5oqE7m+20yRjMRrpaKhe5vttI1Zvp3p9CVQ76lifVDJgAkkUAAAAAAAAAAAAAAAAAAAAAAA9zPT7fLLObMLKS4rW4QvssMD3I6WjkVXwS+LFXRF0604k4cl9tfAeO2wWnGOmHby74usz/AOWnd9h68tex35muc4cq6dS8ewrlzwxRXPeqaLuqf5OxbMQVdtfotXNnRTdbSVkFXGyaCVkkb0RzXNVFRUXrTQudUU1UZQ7UGZmUUsdLSXJ11tCfKoK16va1O1jlXVq/oToyc2qsuc14o6OK5Ntd5eib1vq3IjtfsO5P9BmF1w3W2tyqqaTU5p/k061Ylo7i1EVdF3RT3ApRqnXHPHKidG5F15Kdqalf+hY89JNxa19uprjTSUlXEyWKVqtex7Uc1yLzRUXmh5DTbJWStLixcYRYThWo3t9tKrlWlR2uu90XLX9D2k401PvFUTQIrYnKiL0I01HBUKjpWoqp1Lelo4qOJsNPGxkbGo1rWpoiJ2Ih3I3kvDh3FZxyXuPiu9c14khERqZIFXRNSlz0RNVRdCiaeOJiukejU7TwjOfa3y+ysjmttLWMvd7aio2ipXou4v8AvHJqjf1UkU1LNVvSOFqqpHqq2CiYskzskQ9vrrnRW6lfWV1RHDDE1XPfI9Gtaic9VUixnXty4Vwx01ky4hbfbi1VY6r1VKWJ3inFy+HAidmxtF5lZu1ciXu8OpLXvL0VupVVkTU6t7jq9fE8v0REROw0Gz4JVujNXr/1M7u+M9Y7U0abup9Tj3M7HOZl0ddcZ3+orpFcqsi3t2GJF6mM5NPlgDQaenipGauFuSFImnlqH6c66SgAH2PiAAAAAAfTZYNWTMrCsWmqOvFHr/5zDcIrN+JGLppu6JqakshbW28Zy4OoHaaPu8D+P2Xb3/tNuDEVUTuMnx67Otjb9DTcDJpUsqr1NUe01gj9xM6sR2pI0bT1VR+30+iKiIybV+nZwdqh5cTN+ENwW2Gsw7jmGHTpEdbp3InNU1exVX/8kIZF6w1WbbbY3LxRMl/BS7/SrSXCRnJVzT8gAHeOMAAAAAADG4i+aKhO1Ge20yRjMRfNNR91vttI1Z6d/glUHqmeTJgAkkUAAAAAAAAAAAAAAAAAAAAAAAAAAALxQqiklgmZUQyujkicj2PY5Uc1yclRU5KUnKacT8uaj0ycmaHqLo/E3cpsq2IsW4jxflE+rxLeKi4z0twlpopZ3bzkja1qo3XmumvWSJauqaoRe+D8VVybrNfPE/sRkoI/kmCXpjY7hM1qZJpG6WJ7pLfC5y5rolYAOYdYFD14KhWUOTVFCgjJt4YtxNhPLG3Nw1eqm3OuFybTVL6d2698W45Vbvc0Rd1DXO/eke6WVyvke7ec5y6qq9a6rxNgXwh/0ZWPuvLdP/KkNfvHjr2mu4JhjS3azLeq8TIsZuctfo5rkiAAFzKgiKnEAAHoAAAAAAAAB6nswtY7PfB29w3bi13HuaptZa9G8nJpp28zS7a7pcLLcKe62msmpKulekkM0L917HJyVFQ95tu3Hnfb7T5NlqLXVytaiMqpqX+Jw5K7RdFX0FCxTh2rudS2op8sssi64Zv1NbIXxTovXMlRtrvwxU5J3WG9XOCnrGvjnt8blTekna5NGtTnxTVNTWm3TRNOOvHU+mx1mNjPMm7LesZXyevqOKMa9dI4k7GNTg3+p813Hcw5aJLPSrDKuaqufg4l+ujLxVLNG3JEAALCcbPMAAHgAAAMZiP5pqPut9tpkzGYj+aaj7rfbaRq30z/AASqD1TPJkwASSKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAqZpkbE/g/OGTdZ+MVCf/pGSgjTRCL3wfir/AMjdb+M1HsRkomcvSYHfPmU33G54f+WxeCoAHLOwDheRyUrrxQ8dwBE34Q/6MbH+Mt/tSGv5ea+JsB+ER4ZYWPTzyn9qQ1+mwYL+Won1MgxmujccvoAAXEqYAAAAAAAAAAAAAAAAAAAAAAAAAAAMZiP5pqPut9tpkzGYj+aaj7rfbaRq30z/AASqD1TPJkwASSKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbEfg/Pocrfxmo9iMlGzl6SLnwfn0OVv4zUexGSjZy9Jgd8+ZTfcbnh/5dF4KgAcs7AOPrKcnH1lPHcARM+ES+jCx/jKf2pDX6bAvhEvovsf40n9mQ1+mxYLT/bWr9VMfxqn+4/gAAt5VAAAAAAAAAAAAAAAAAAAAAAAAAAAAYzEfzTUfdb7bTJmMxH801H3W+20jVvpn+CVQeqZ5MmACSRQAAAAAAAAAAAAAAAAAAAAAAAAAAAAADYj8H59Dlb+M1HsRko2cvSRc+D8+hyt/Gaj2IyUbOXpMDvnzKb7jc8P/LovBUADlnYBx9ZTk4+sp47gCJnwiX0X2P8AGk/syGv02BfCJfRfY/xpP7Mhr9NiwX8sb5Ux/GvzH8AAFvKoAAAAAAAAAAAAAAAAAAAAAAAAAAADGYj+aaj7rfbaZMxmI/mmo+6322kat9M/wSqD1TPJkwASSKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbEfg/Pocrfxmo9iMlGzl6SLnwfn0OVv4zUexGSjZy9Jgd8+ZTfcbnh/5dF4KgAcs7AOPrKcnH1lPHcARM+ES+i+x/jSf2ZDX6bAvhEvovsf40n9mQ1+mxYL+WN8qY/jX5j+AAC3lUAAAAAAAAAAAAAAAAAAAAAAAAAAABjMR/NNR91vttMmYzEfzTUfdb7bSNW+mf4JVB6pnkyYAJJFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANiPwfn0OVv4zUexGSjZy9JFz4Pz6HK38ZqPYjJRs5ekwO+fMpvuNzw/8ui8FQAOWdgHH1lOTj6ynjuAImfCJfRfY/xpP7Mhr9NgXwiX0X2P8aT+zIa/TYsF/LG+VMfxr8x/AABbyqAAAAAAAAAAAAAAAAAAAAAAAAAAAAxmI/mmo+6322mTMZiP5pqPut9tpGrfTP8ABKoPVM8mTABJIoAAAAAAAAAAAAAAAAAAAAAAAAAAAAABsR+D8+hyt/Gaj2IyUbOXpIufB+fQ5W/jNR7EZKNnL0mB3z5lN9xueH/l0XgqAByzsA4+spycfWU8dwBEz4RL6L7H+NJ/ZkNfpsC+ES+i+x/jSf2ZDX6bFgv5Y3ypj+NfmP4AALeVQAAAAAAAAAAAAAAAAAAAAAAAAAAAGNxGn/NFQvc322mSMbiP5nqPFntIRq300nglUHqmeTJAAkkUAAAAAAAAAAAAAAAAAAAAAAAAAAAAHOmoTjkeKuRsR+D8+hut/Gaj2IyUTOXpIvfB+ccmqz8Zn9iMlDH8kwO+/MpvuN0w/wDLovBUADlnYBx9Y5KH8lXsCpmCJ/wiSL/yX2P8ZT+zIa/DYF8Ig7/oxsaf+Mp/akNfy8zYMErna2r9VMfxrn/EfwcAAuBVAAAAAAAAAAAAAAAAAAAAAAAAAAAAY3EfzPUeLPaQyRjcR/M9R4s9pCNW+mk8Eqg9UzyZIAEkigAAAAAAAAAAAAAAAAAAAAAAAAAAA5OAASz2ONpHB+Wlpfl7jHpKKOurnz09fqiwtc5Gpuv4at5c1J5W270V0pIq23VcVRTzNR8ckT0cxydypzNLS6LwVPE9Zya2lMf5N1UUNFWPuNkR2sttqH6sRuqa9GvNi/oZ5f8ACDql76ukX4l3qnUu9ixalE1tNVf0pwXobWmO3vjKvMq1TtPJ8nNojL3OG3sfYq9Ke5MbrPbp3aTRronJPrJx5oeppMxUVyckTVTNp4ZKV2rmRUU02Cphqm6cLkVCvXr1MfeL3bbJb57ldq2KlpoGq6SWVyNa1O1VXgeX5z7SuX2TtDJHdK5K68bu9FbaZyLKq/aXk1O9TX3m/tDZhZx1r0vde6ktKPcsNtp3aRNTXgr/APbXx4Hcs+HKq6uRctFnVThXbElLbG5Iuk/oh6htg7R+E81qakwThKKWpprZW/tElwX4scjkarVaxF4uT4y8e4jAE0TTRE4cE4dRyvE1+122K1U6U8XAye4V81xm10y7zgAHRIAAAAAAAAAAAAAAAAAAAAAAAAAAAAMbiP5nqPFntIZIxuI/meo8We0hGrfTSeCVQeqZ5MkACSRQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOvUALvPMkVMlQurTdbpYrhFdLPcKiiq6dyPimgkVj2r3Kh75Ubb+b8uCG4ZZJSMuafw3XVGfxFj00+Sqbu/wB5HkHPq7VRVrkkmjRXJwJtLcKqiarIH5IvE76+4XC61styulbPV1U7lfJLM9Xvcq9aqvE6ACe1rWNRjEyROREV7nuV796rzAAPTwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAGNxH8z1Hiz2kMkY3EfzPUeLPaQjVvppPBKoPVM8mSABJIoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMbiP5nqPFntIZIxuI/meo8We0hGrfTSeCVQeqZ5MkDr6dvZ+pz0zf9L/g+mvi7kPxqJe1SsHWk7O79fccrMzm3ig18Xcg2eXtUrBR0zf8AS/4OOnb2fqNfF3INRL2qdgKOmb/pf8HHTt7F/Ma+LuQbPL2qdgOvp29i/mOnZ1oo18Xcg2eXtU7AUdOzqTU46dvcNfF3INnl7VOwHX07e79TnpmjXxdyDZ5e1SsHX07B07fEa+LuQbPL2qdgKOmb2fqcLO1Oz/XoGvi7kGzy9qnYDr6Zo6Zo18Xcg2eXtU7AUdM3/S/4HTN/0v8Aga+LuQ81EnapWCnpY+1P19w6RnUqfmvuGvi7kPdnl7VKgU9I3tT819w6VnWqfmvuGvi7kGzy9qlQKelj/wBpP19w6VmvBf6+4a+LuQbPL2KVAp6Rvag6ViJzTXxX3DXxdyDZ5e1SoHX0zez9ThamNOGn6/4Gvi7kGzy9qnaDqSpj60H7TH2fr/ga+LuQbPL2qdoOpKmPrQq6Zv8Apf8AA18Xcg2eXtUrB19O3s/U56ZqjXxdyDZ5e1SsFHSprpwOOmRF04fn/ga+LuQbPL2qdgOtaiNOCp+v+B+0Rry/r/ga+LuQbPL2KdgOtJmr2fn/AIKukjT6yfr7hr4u5DxKebm1SoFDpmJy/r/gJMxV4qifn7hr4u5D3Z5e1SsHCyx/7Sfr7jjpY/8AaT9fcNfF3INnl7FKgU9I3rVP1KUnavV+v+Br4u5DzZpu1TsMZiThZqhfue0hkUkRU14GOxEqOs1Qjl0T4ntJ7iNWTw7M/wCJOBKoaeVtSxVavE//2Q==', 'Umair Hassan', 'umair@ezitech.org', '', '2024-10-22', 'Ezitech@@1122', 1000.00, 'Marketing', 0, 'Supervisor', 0, '2024-10-22 17:38:29', '2025-08-27 21:08:49', 0.00);
INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(16, NULL, 'ETI-SUPERVISOR-300', 'data:image/jpeg;base64,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', 'Ibhrahim Shah', 'ibrahim@ezitech.org', '', '2024-10-24', 'Ezitech@786', 1000.00, 'Graphics', 1, 'Supervisor', 0, '2024-10-24 17:47:28', '2025-06-19 13:54:36', 0.00),
(17, 5, 'ETI-SUPERVISOR-563', 'data:image/jpeg;base64,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', 'Shafqat Khan', 'shafqat@ezitech.org', '', '2024-10-29', 'Ezitech@1122', 1000.00, 'Mobile App Development', 1, 'Supervisor', 0, '2024-10-29 19:36:10', '2026-03-13 16:45:02', 0.00);
INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(18, NULL, 'ETI-MANAGER-713', 'data:image/jpeg;base64,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', 'Ashan Amer', 'ashan@ezitech.org', '+92 336 666559', '2025-01-07', 'Ashan@1122', 1000.00, '', 0, 'Manager', 0, '2025-01-07 16:34:15', '2025-01-21 15:00:31', 0.00),
(19, NULL, 'ETI-MANAGER-207', '', 'Test', 'test@ezitech.org', '03176349954', '2025-01-31', '1234', 1000.00, '', 0, 'Manager', 0, '2025-01-31 16:26:28', '2025-01-31 16:27:22', 0.00);
INSERT INTO `manager_accounts` (`manager_id`, `assigned_manager`, `eti_id`, `image`, `name`, `email`, `contact`, `join_date`, `password`, `comission`, `department`, `status`, `loginas`, `emergency_contact`, `created_at`, `updated_at`, `balance`) VALUES
(20, 5, 'ETI-SUPERVISOR-228', 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAASABIAAD/4QCMRXhpZgAATU0AKgAAAAgABQESAAMAAAABAAEAAAEaAAUAAAABAAAASgEbAAUAAAABAAAAUgEoAAMAAAABAAIAAIdpAAQAAAABAAAAWgAAAAAAAABIAAAAAQAAAEgAAAABAAOgAQADAAAAAQABAACgAgAEAAAAAQAAAoCgAwAEAAAAAQAAAoAAAAAA/8AAEQgCgAKAAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/bAEMABgYGBgYGCgYGCg4KCgoOEg4ODg4SFxISEhISFxwXFxcXFxccHBwcHBwcHCIiIiIiIicnJycnLCwsLCwsLCwsLP/bAEMBBwcHCwoLEwoKEy4fGh8uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLi4uLv/dAAQAKP/aAAwDAQACEQMRAD8A4T7Faf8APFPyo+xWn/PFPyq1RX3Xsodj4n2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+xWn/PFPyo+xWn/ADxT8qtUUeyh2D2su5V+xWn/ADxT8qPsVp/zxT8qtUUeyh2D2su5V+x2v/PJP++aPsdr/wA8k/75q1RR7KHYn2s/5ir9jtf+eSf980fY7X/nkn/fNWqKPZQ7B7Wf8xV+x2v/ADyT/vmj7Ha/88k/75q1RR7KHYPaz/mKv2O1/wCeSf8AfNH2O1/55J/3zVqij2UOwe1n/MVfsdr/AM8k/wC+aPsdr/zyT/vmrVFHsodg9rP+Yq/Y7X/nkn/fNH2O1/55J/3zVqij2UOwe1n/ADFX7FZ/88U/Kj7Faf8APFPyq1RR7KHYftZ9yr9itP8Anin5Vmava26WTOiKpBXBA561u1k63/x4t/vL/OufFU4qm2kbYapJ1Vdn/9DjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsnWv+PFv95f51rVla1/x4N/vL/OubFfwmb4X+Kj/0eOooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyta/48G/3l/nWrWVrX/Hg3+8v86wxX8ORvhf4qP/S46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArK1r/jwb/eX+datZWtf8eDf7y/zrmxf8ORvhf4qP//T46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooqQCiiinfoFgoooouAUUUUwCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyta/48G/3l/nWrWVrX/Hg3+8v865sX/Dkb4X+Kj//U46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiilCsxwoyTUyklqwSb0QlT21tcXkqwWsZkkbooGa7vw78PtV1fbPeD7NB6t1Yewr3HRfDOk6JCI7OMBscuRlj+NeRis1jD3aerPTw2WTn709jy3w58MpJtt1rjFB1Ea9fxNdTrfw20q/hzp4FtMowD1U/UV6aAKXGa8KWMquXNc9yOBpqPLY+RdZ8N6roUpS9iOzPDLyp/GsGvs26s7e7haG5jWRG6qwyK8h8R/DCOUvc6I2xjyYz0P09K9fC5un7tXQ8nF5VKPvUjxCirl9p95ps5tr2JonXswqnXt05xkrxdzx5wlHSQUUUVoIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsrWv8Ajwb/AHl/nWrWVrX/AB4N/vL/ADrmxf8ADkb4X+Kj/9XjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoAya0NN0nUNWmFvp8TOx9BwPqe1e1eHfhnbWxW51k+bJ1EY6D6+teficwp0la+p2YbBTq9NDynQ/Cmra9IPs0ZWLvI4wP8A69e6+HfAOk6MFnkXz5h/E/QH2Hau3htobeMRQIEVegAwKsAcV87icfUrdbI9/DZfTpajVXbwKkFFFcZ3pWCiiigYUmKM0hNAGLq2h6brEJg1CISDsf4h9DXh3iP4b3+nlrjSibiEfw/xD/Gvd9Q1ax0uEz3sixqPU9fwrxXxH8TJ7rdb6Gvlx9PMbr+Ar0cB7fm/d7HkZh7Fx9/c8ndHjcpINrKcEEYIptSTTSTyNLKxZ2OST1JqOvqo3tqfOStfQKKKKYgoooqgCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyta/48G/3l/nWrWVrX/Hg3+8v865sX/Dkb4X+Kj/1uOooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiipAKKACeB1Nd14f8BatrJWaZfItj1ZvvH6Csa2JhSV5s1pYepVdoI4mGGW4cRQKXZugAya9V8OfDK4ugLrWiYo+0a/eP1PavVdD8KaVoUQW2jDSd5H+9+ddPivnsVmlSfuw0R7+FyuMPenqZ2maTY6TCLeyjWNQO3U/U1qAU7FFeU3d3Z60YqKshMUtFFAwooooAQ9KSkdgoyTgVwXiLx7pWhholbz7gdEXsfc9q0p0p1HywVzGrXhTV5s7ee4hgQyysEVepY4ArynxH8TLW13W2kDzpOQW/gH+NeVa94s1bX3P2mQpFniNOF/H1rmMA17uFylL3qp4WLzVyfLTNHUdWv9Vm8+/kLt6E8D6Cs+iivahBQVoqx48pObvIKKKKsAoooqQCiiiqAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArK1r/jwb/eX+datZWtf8eDf7y/zrmxf8ORvhf4qP/X46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiikwCiirthp17qMwt7KFpXJ7Dj8fSolUjBXmyoQlN8sSieK39E8N6trsoS0jITvIwwo/GvVPDfwxiixc64d7dRGOg+p7163bWkFrEsFugRF6BRgV4mKzb7NI9jC5S371RnB+G/h7pmkbbi7/wBIuB/E33QfYV6KigDAGAOlOoFeFUqSqO8me5SpRprlih1FFFRY1CiiincApCcUmaz7/UrPTYTPeSrGi9SxxQk5OyJlNRV5F8sAK53WvE2maJEZLyUBuyjlj9BXlviH4nvIWttDXA6ec39BXkd1dXF7O091I0khPLMc16+FyqU/eqaI8fFZoo6UzvPEHxD1TVS0FkTbQnjjliPr2rzwksdxJJPUnk0UV9DQoQpK0EeFUrzqfGwooorYzCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsrWv8Ajwb/AHl/nWrWVrX/AB4N/vL/ADrmxf8ADkb4X+Kj/9DjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooopNpK7BK+iDvTo4ZJZBFGpZj0A5Ndj4f8E6vrrq+0wwn+Nh1HsO9e66D4O0nQYw0UYkm/56MMn8PSvJxeaU4K0NWelhstqVHeWiPKPDnw3vb/AG3OrnyIeuwfeI/pXt2laLp2kQiKwiCAdTj5j9TWuFA6U6vn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LfbS54s9LBZg6K5Wj69muYYIy8rBQOpJwK8X8a+Po54pNL0dg24ENJ2x6CvLLzWtWvx/pVzI+eME8flWYevNZ4bKVB802XiczlUXLFCDNLRRXsHkPUKKKKooKKKKACiiigAooooAKKKKACsnW/8Ajxb/AHl/nWtWTrf/AB4t/vL/ADrmxX8JnThP4kT/1uOooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiikwPoP4T/8gKX/AK7H+Qr1TvXlfwo/5AUv/XY/yFeq18Vi/wCNL1PscH/Cj6DqKKK5zqCiiigAooooAQ1VvP8Aj1l/3G/lVs1Vvf8Aj2l/3G/lTj8SJn8LPjS4/wBfJ/vH+dRVLP8A6+T/AHj/ADqKvuaeyPiJ/EwooorQkKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiipAKKKKoAooooAKKKKACiiigAooooAKKKKACiiigArJ1v8A48W/3l/nWtWTrf8Ax4t/vL/OubFfwmdWE/iI/9fjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooqQPoL4Tf8gKX/ruf5CvVq8p+E3/ICl/67n+Qr1avi8X/ABpep9jg/wCFH0HUUUVznUFFFFABRRRQAVVvf+PaT/cb+VWqq3v/AB7Sf7jfyqo7omfws+NLj/Xyf7x/nUVS3H+vk/3j/Ooq+4p7Hw8/jYUUUVoIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyda/wCPFv8AeX+da1ZOtf8AHi3+8v8AOubFfwmdWE/iI//Q46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKkD6C+E3/ICl/67n+Qr1avKfhN/yApf+u5/kK9Wr4vF/wAaXqfY4P8AhR9B1FFFc51BRRRQAUUUUAFVb3/j2k/3G/lVqqt7/wAe0n+438qqO6Jn8LPjS4/18n+8f51FUtx/r5P94/zqKvuKex8PP42FFFFaCCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsnWv8Ajxb/AHl/nWtWTrX/AB4t/vL/ADrmxX8JnVhP4iP/0eOooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiipA+gvhN/yApf+u5/kK9Wryn4Tf8gKX/ruf5CvVq+Lxf8AGl6n2OD/AIUfQdRRRXOdQUUUUAFFFFABVW9/49pP9xv5Vaqre/8AHtJ/uN/KqjuiZ/Cz40uP9fJ/vH+dRVLcf6+T/eP86ir7insfDz+NhRRRWggooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArJ1r/AI8W/wB5f51rVk61/wAeLf7y/wA658V/CZ1YT+Ij/9LjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooqQPoL4Tf8gKX/ruf5CvVq8p+E3/ICl/67n+Qr1avi8X/ABpep9jg/wCFH0HUUUVznUFFFFABRRRQAVVvf+PaT/cb+VWqq3v/AB7Sf7jfyqo7omfws+NLj/Xyf7x/nUVS3H+vk/3j/Ooq+4p7Hw8/jYUUUVoIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyda/wCPFv8AeX+da1ZOtf8AHi3+8v8AOufFfwmdWE/iI//T46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKkD6C+E3/ICl/67n+Qr1avKfhN/yApf+u5/kK9Wr4vF/wAaXqfY4P8AhR9B1FFFc51BRRRQAUUUUAFVb3/j2k/3G/lVqqt7/wAe0n+438qqO6Jn8LPjS4/18n+8f51FUtx/r5P94/zqKvuKex8PP42FFFFaCCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsnWv8Ajxb/AHl/nWtWTrX/AB4t/vL/ADrnxX8JnVhP4iP/1OOooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiipA+gvhN/yApf+u5/kK9Wryn4Tf8gKX/ruf5CvVq+Lxf8AGl6n2OD/AIUfQdRRRXOdQUUUUAFFFFABVW9/49pP9xv5Vaqre/8AHtJ/uN/KqjuiZ/Cz40uP9fJ/vH+dRVLcf6+T/eP86ir7insfDz+NhRRRWggooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArJ1r/AI8W/wB5f51rVk61/wAeLf7y/wA658V/CZ1YT+Ij/9XjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooqQPoL4Tf8gKX/ruf5CvVq8p+E3/ICl/67n+Qr1avi8X/ABpep9jg/wCFH0HUUUVznUFFFFABRRRQAVVvf+PaT/cb+VWqq3v/AB7Sf7jfyqo7omfws+NLj/Xyf7x/nUVS3H+vk/3j/Ooq+4p7Hw8/jYUUUVoIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKyda/wCPFv8AeX+da1ZOtf8AHi3+8v8AOufFfwmdWE/iI//W46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKkD6C+E3/ICl/67n+Qr1avKfhN/yApf+u5/kK9Wr4vF/wAaXqfY4P8AhR9B1FFFc51BRRRQAUUUUAFVb3/j2k/3G/lVqqt7/wAe0n+438qqO6Jn8LPjS4/18n+8f51FUtx/r5P94/zqKvuKex8PP42FFFFaCCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsjW/8AkHt/vL/OtesjXP8Ajwb/AHl/mK58X/CkdGF/io//1+Oooor74+ECiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiipA+gvhN/yApf+u5/kK9Wryn4Tf8gKX/ruf5CvVq+Lxf8AGl6n2OD/AIUfQdRRRXOdQUUUUAFFFFABVW9/49pP9xv5Vaqre/8AHtJ/uN/KqjuiZ/Cz40uP9fJ/vH+dRVLcf6+T/eP86ir7insfDz+NhRRRWggooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArI13/AI8D9V/mK16yNd/48D9V/mK58X/CkdGF/io//9DjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooopMTPoL4Tf8gKX/ruf5CvVq8q+E//ACApf+ux/kK9Vr4nF/xpep9lg/4UfQdRRRWB1BRRRQAUUUUAFVL3/j1k/wBxv5VaNVLz/j2lH+w38qcfiRM/hZ8bT/6+T/eP86iqWf8A18n+8f51FX3NN3SPh6nxsKKKK0EFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFZGu/8eB+q/zFa9ZGu/8AHgfqv8xXPi/4Ujowv8VH/9HjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooE/I9J8D+M7fQENhfJiF23eYOxPHNfQVnqFrfQrcWkiyI3QqcivjTp0rd0PxHqWgzebZOShOWjJ4P4V4eMyvnfPT3PXweZOn7k1ofXWRSgVwnhrxxpmuoI2YQ3HdD3+ld0rZr5+pTlB8s0fQUqsai5osfRRRUmoUh60maydU1ey0i3NxeyiNR+v0pqLbsiZTUVds1HdQuTXm3irx3p2lRtaWxE1wcjaDwO3NeeeJ/iFe6oWttMzBbngt0Y/4CvNiS3LHJPJJ6mvbweVX9+qeFjMzb92kK7F3LnuSfzptFFfQpW0R4bbbuwooopgFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFZGu/8eB+q/zFa9ZGu/8AHgfqv8xXPi/4Ujowv8VH/9LjqKKK++PhAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKT8gY5XkjYPESrA5BBwR9DXqfhn4kXVlts9YJkhGAJP4l+vrXlVFctfCwrK00a0MROi/dZ9jafqVnqUC3FnIsiMMgg1eeRVXcxwBXyHo2ualoc3m2EhUd1PQ/UV0Wv/ABA1bWIltof9Hjx8208k/X0rw5ZPU57R2Pcjm0VD3tz0/wAT/EKy0rdaadie4PGQeBXg+qatqGsTtcX8xcnoM8D6Cs7OeT1PNFezhcBTorbU8jEY2dZ6vQKKKK7jlWgUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABWRrv8Ax4H6r/MVr1ka7/x4H6r/ADFc+L/hSOjC/wAVH//T46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArI13/jwP1X+YrXrI13/AI8D9V/mK58X/CkdGF/io//U46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArI13/jwP1X+YrXrI13/AI8D9V/mK58X/CkdGF/io//V46iiivvj4QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArI13/jwP1X+YrXrI1v/AI8W/wB5f5isMV/DkdGF/io//9bjqKq/bbT/AJ6r+dH220/56r+dfde1h3PifYVOxaoqr9ttP+eq/nR9ttP+eq/nR7WHcPYVOxaoqr9us/8AnqtH22z/AOeq/nR7WHcPYVOxaoqr9ttP+eq/nR9ttP8Anqv50e1h3D2E+xaoqr9ttP8Anqv50fbbP/nqv50e1h3D2FTsWqKq/bbT/nqv50fbbT/nqv50e1h3D2FTsWqKq/bbT/nqv50fbbT/AJ6r+dHtYdw9hU7Fqiqv220/56r+dH22z/56r+dHtYdw9hU7Fqiqv220/wCeq/nR9ttP+eq/nR7WHcPYVOxaoqr9ttP+eq/nR9ttP+eq/nR7WHcPYVOxaoqr9ttP+ey0fbbT/nqv50e1h3D2E+xaoqr9ttP+eq/nR9stP+eq/nR7WHcPYVOxaoqr9ttP+eq/nR9ttP8Anqv50e1h3D2FTsWqKq/bbT/nqv50fbLT/nqv50e1h3D2FTsWqKrfbLT/AJ6rSfbLX/nov50e1h3D2FTsWqKrfa7X/notL9rtv+ei0e2h3D2FTsWKKr/a7b++KPtVt/z0FHtodx+wqfyliioBc25/jFL58H98UvbQ7h7Cp/KTUVD58H94Uvnw/wB4flR7aH8wvq9T+UloqHz4f7wpfPg/vD9aPbU+4fV6n8pLRUXnwf3h+tJ58H98Ue2p9w+r1P5SaioftFv/AHxQbi3H8Yp+2h3D6vU/lJqKg+02/dxR9qtv+ei0e2h3D2FTsT0VW+12v/PRaPtlr/z0X86Paw7h7Cp2LNFVvtlr/wA9F/Ok+22v/PRfzo9rDuHsJ9i1RVX7baf89V/Oj7baf89V/Oj2sO4ewn2LVFVfttp/z1X86Pttr/z1X86Paw7h7CfYtUVV+22n/PVfzo+22n/PVfzo9rDuHsJ9i1RVX7baf89V/Oj7baf89V/Oj2sO4ewqdi1RVX7baf8APVfzpfttn/z1X86Paw7i9hU7FmiqxvLT/nqv50n22z/56rS9tDuHsKnYtUVV+2Wo/wCWq/nR9ttf+eq/nR7aHcPYVOxaoqr9ts/+eq/nR9ts/wDnqtCrQ7j9hU7Fqiq322z/AOeq0fbLP/nqv50/aw7i9hU7FmiqpvLP/nqv50fbbT/nqv50e1h3H7Cp2LVFVvtln/z1X86Ptln/AM9V/Oj2sO4vYVOxZoqt9ss/+eq/nR9ss/8AnqtHtYdw9hU7Fmiq32yz/wCeq/nSfbbT/nqv50e1h3H7Cp2LVFVft1p/z0X86X7bZ/8APVfzo9rDuHsKnYs1ka3/AMg9vqv8xV77ZZ/89l/OsvWLmCWyMcbBmLDgfWufE1YunJJm+GpTVRNo/9k=', 'Ahmed', 'ml@ezitech.org', '', '2025-02-10', '1234', 1000.00, 'Machine Learning', 1, 'Supervisor', 0, '2025-02-11 02:41:27', '2026-03-13 16:44:21', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `manager_complaints`
--

CREATE TABLE `manager_complaints` (
  `id` int(11) NOT NULL,
  `eti_id` int(11) NOT NULL,
  `complaint_name` varchar(255) NOT NULL,
  `complaint_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manager_permissions`
--

CREATE TABLE `manager_permissions` (
  `manager_p_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `tech_id` int(11) NOT NULL,
  `interview_type` enum('Remote','Onsite') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manager_permissions`
--

INSERT INTO `manager_permissions` (`manager_p_id`, `manager_id`, `tech_id`, `interview_type`, `created_at`) VALUES
(67, 1, 1, 'Remote', '2024-12-19 08:03:39'),
(68, 1, 2, 'Remote', '2024-12-19 08:03:39'),
(69, 1, 3, 'Remote', '2024-12-19 08:03:39'),
(70, 1, 4, 'Remote', '2024-12-19 08:03:39'),
(72, 1, 6, 'Remote', '2024-12-19 08:03:39'),
(73, 1, 8, 'Remote', '2024-12-19 08:03:39'),
(74, 1, 10, 'Remote', '2024-12-19 08:03:39'),
(75, 1, 11, 'Remote', '2024-12-19 08:03:39'),
(76, 1, 13, 'Remote', '2024-12-19 08:03:39'),
(77, 1, 17, 'Remote', '2024-12-19 08:03:39'),
(78, 1, 18, 'Remote', '2024-12-19 08:03:39'),
(79, 1, 19, 'Remote', '2024-12-19 08:03:39'),
(81, 1, 21, 'Remote', '2024-12-19 08:03:39'),
(82, 1, 22, 'Remote', '2024-12-19 08:03:39'),
(84, 1, 23, 'Remote', '2024-12-19 08:03:39'),
(86, 1, 24, 'Remote', '2024-12-19 08:03:39'),
(88, 1, 25, 'Remote', '2024-12-19 08:03:39'),
(89, 1, 35, 'Remote', '2024-12-19 08:03:39'),
(98, 18, 6, 'Remote', '2025-01-07 08:37:06'),
(102, 18, 10, 'Remote', '2025-01-07 08:37:06'),
(104, 18, 11, 'Remote', '2025-01-07 08:37:06'),
(112, 18, 19, 'Remote', '2025-01-07 08:37:06'),
(114, 18, 21, 'Remote', '2025-01-07 08:37:06'),
(126, 18, 1, 'Remote', '2025-01-13 05:43:17'),
(127, 18, 2, 'Remote', '2025-01-13 05:43:17'),
(136, 2, 1, 'Onsite', '2025-04-09 06:04:01'),
(137, 2, 2, 'Onsite', '2025-04-09 06:04:01'),
(138, 2, 3, 'Onsite', '2025-04-09 06:04:01'),
(139, 2, 4, 'Onsite', '2025-04-09 06:04:01'),
(140, 2, 6, 'Onsite', '2025-04-09 06:04:01'),
(141, 2, 8, 'Onsite', '2025-04-09 06:04:01'),
(142, 2, 10, 'Onsite', '2025-04-09 06:04:01'),
(143, 2, 11, 'Onsite', '2025-04-09 06:04:01'),
(144, 2, 13, 'Onsite', '2025-04-09 06:04:01'),
(145, 2, 17, 'Onsite', '2025-04-09 06:04:01'),
(146, 2, 18, 'Onsite', '2025-04-09 06:04:01'),
(147, 2, 19, 'Onsite', '2025-04-09 06:04:01'),
(148, 2, 21, 'Onsite', '2025-04-09 06:04:01'),
(149, 2, 22, 'Onsite', '2025-04-09 06:04:01'),
(150, 2, 23, 'Onsite', '2025-04-09 06:04:01'),
(151, 2, 24, 'Onsite', '2025-04-09 06:04:01'),
(152, 2, 25, 'Onsite', '2025-04-09 06:04:01'),
(153, 2, 35, 'Onsite', '2025-04-09 06:04:01'),
(180, 2, 1, 'Remote', '2025-12-10 08:53:17'),
(181, 2, 2, 'Remote', '2025-12-10 08:53:17'),
(182, 2, 3, 'Remote', '2025-12-10 08:53:17'),
(183, 2, 4, 'Remote', '2025-12-10 08:53:17'),
(184, 2, 6, 'Remote', '2025-12-10 08:53:17'),
(185, 2, 8, 'Remote', '2025-12-10 08:53:17'),
(186, 2, 10, 'Remote', '2025-12-10 08:53:17'),
(187, 2, 11, 'Remote', '2025-12-10 08:53:17'),
(188, 2, 13, 'Remote', '2025-12-10 08:53:17'),
(189, 2, 17, 'Remote', '2025-12-10 08:53:17'),
(190, 2, 18, 'Remote', '2025-12-10 08:53:17'),
(191, 2, 19, 'Remote', '2025-12-10 08:53:17'),
(192, 2, 21, 'Remote', '2025-12-10 08:53:17'),
(193, 2, 22, 'Remote', '2025-12-10 08:53:17'),
(194, 2, 23, 'Remote', '2025-12-10 08:53:17'),
(195, 2, 24, 'Remote', '2025-12-10 08:53:17'),
(196, 2, 25, 'Remote', '2025-12-10 08:53:17'),
(197, 2, 35, 'Remote', '2025-12-10 08:53:17'),
(200, 5, 37, 'Remote', '2026-03-13 16:30:44'),
(201, 5, 37, 'Onsite', '2026-03-13 16:30:44'),
(202, 5, 38, 'Remote', '2026-03-13 16:30:44'),
(203, 5, 38, 'Onsite', '2026-03-13 16:30:44');

-- --------------------------------------------------------

--
-- Table structure for table `manager_roles`
--

CREATE TABLE `manager_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `manager_id` int(11) NOT NULL,
  `permission_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manager_roles`
--

INSERT INTO `manager_roles` (`id`, `manager_id`, `permission_key`, `created_at`, `updated_at`) VALUES
(175, 5, 'manager_dashboard_view', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(176, 5, 'manager_dashboard_greetings', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(177, 5, 'manager_statistics', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(178, 5, 'manager_dashboard_kpi_overview', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(179, 5, 'manager_dashboard_interview_pipeline', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(180, 5, 'view_my_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(181, 5, 'statistics_my_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(182, 5, 'excel_my_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(183, 5, 'edit_status_my_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(184, 5, 'remove_my_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(185, 5, 'view_active_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(186, 5, 'excel_active_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(187, 5, 'edit_status_active_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(188, 5, 'remove_active_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(189, 5, 'view_new_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(190, 5, 'excel_new_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(191, 5, 'edit_status_new_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(192, 5, 'remove_new_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(193, 5, 'view_contact_with', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(194, 5, 'excel_contact_with', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(195, 5, 'edit_status_contact_with', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(196, 5, 'remove_contact_with', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(197, 5, 'view_interview_test', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(198, 5, 'excel_interview_test', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(199, 5, 'edit_status_interview_test', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(200, 5, 'remove_interview_test', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(201, 5, 'view_test_completed', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(202, 5, 'excel_test_completed', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(203, 5, 'edit_status_test_completed', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(204, 5, 'remove_test_completed', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(205, 5, 'view_manager_international_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(206, 5, 'excel_manager_international_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(207, 5, 'edit_status_manager_international_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(208, 5, 'remove_manager_international_interns', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(209, 5, 'view_manager_remaining_amount', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(210, 5, 'view_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(211, 5, 'excel_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(212, 5, 'accept_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(213, 5, 'reject_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(214, 5, 'offer_letter_send_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(215, 5, 'view_reason_manager_offer_letter_request', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(216, 5, 'view_manager_offer_letter_template', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(217, 5, 'add_new_manager_offer_letter_template', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(218, 5, 'edit_manager_offer_letter_template', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(219, 5, 'delete_manager_offer_letter_template', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(220, 5, 'preview_manager_offer_letter_template', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(221, 5, 'view_manager_payment_receipt', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(222, 5, 'view_manager_profile_settings', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(223, 5, 'view_manager_knowledge_base', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(224, 5, 'view_manager_curriculum', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(225, 5, 'create_manager_curriculum', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(226, 5, 'edit_manager_curriculum', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(227, 5, 'delete_manager_curriculum', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(228, 5, 'manage_manager_curriculum_projects', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(229, 5, 'view_intern_leaves', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(230, 5, 'approve_supervisor_leaves', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(231, 5, 'view_supervisor_attendance', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(232, 5, 'view_attendance_calendar', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(233, 5, 'view_invoice_dashboard', '2026-03-14 07:00:00', '2026-03-14 07:00:00'),
(234, 5, 'create_invoice', '2026-03-14 07:00:00', '2026-03-14 07:00:00');

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
(1, '0001_01_01_000002_create_jobs_table', 1),
(2, '2026_02_10_075912_create_supervisor_leaves_table', 1),
(3, '2026_02_10_080316_create_employee_leaves_table', 1),
(4, '2026_02_12_145209_create_knowledge_bases_table', 1),
(5, '2026_02_12_200605_add_status_and_resolved_at_to_feedbacks_table', 1),
(6, '2026_02_14_070251_create_admin_settings_table', 1),
(7, '2026_02_15_151457_create_password_otp_resets_table', 1),
(8, '2026_02_26_190048_create_manager_roles_table', 1),
(9, '2026_02_28_044058_create_offer_letter_templates_table', 1),
(10, '2026_03_04_114136_add_invoice_type_to_invoices_table', 1),
(11, '2026_03_04_175633_modify_invoice_status_column', 1),
(12, '2026_03_10_183135_add_assigned_manager_to_manager_accounts_table', 1),
(13, '2026_03_13_102146_create_technology_curriculum_table', 2),
(14, '2026_03_13_102150_create_curriculum_projects_table', 3),
(15, '2026_03_13_102203_create_intern_curriculum_assignment_table', 4),
(16, '2026_03_13_102211_create_curriculum_supervisor_mapping_table', 5),
(17, '2026_03_13_102218_create_intern_project_progress_table', 5),
(18, '2026_03_13_105350_create_sessions_table', 6),
(19, '2026_03_13_162517_create_cache_table', 7),
(20, '2026_03_12_091145_add_period_to_withdraw_requests_table', 8),
(21, '2026_03_12_105739_add_balance_to_manager_accounts_table', 8),
(22, '2026_03_13_150346_create_transactions_table', 9),
(23, '2026_03_13_153759_create_invoice_approvals_table', 10),
(24, '2026_03_13_153834_add_approval_status_to_invoices_table', 10),
(25, '2026_03_13_160708_add_invoice_columns_to_invoices_table', 11),
(26, '2026_03_13_160732_create_audit_logs_table', 11),
(27, '2026_03_13_181242_add_timestamps_to_invoices_table', 11),
(28, '2026_03_13_181650_make_screenshot_nullable_in_invoices', 11),
(29, '2026_03_13_182036_add_invoice_id_to_transactions_table', 11),
(30, '2026_03_13_183002_add_inv_id_to_transactions_table', 11),
(31, '2026_03_13_183425_add_approval_status_to_invoices_table', 11),
(32, '2026_03_13_191045_add_payment_date_to_transactions_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `offer_letter_requests`
--

CREATE TABLE `offer_letter_requests` (
  `id` int(11) NOT NULL,
  `offer_letter_id` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ezi_id` varchar(50) NOT NULL,
  `intern_status` varchar(50) DEFAULT NULL,
  `tech` varchar(100) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','reject','accept') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offer_letter_templates`
--

CREATE TABLE `offer_letter_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`content`)),
  `manager_id` int(11) NOT NULL,
  `can_use_other_template` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offer_letter_templates`
--

INSERT INTO `offer_letter_templates` (`id`, `title`, `content`, `manager_id`, `can_use_other_template`, `status`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Template', '\"Template Name\\r\\nTemplate\\r\\n Content (Placeholders: name, email, join_date, end_date, technology, duration)\"', 5, 0, 1, 0, '2026-03-13 12:00:19', '2026-03-13 12:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `office_location`
--

CREATE TABLE `office_location` (
  `id` int(11) NOT NULL,
  `lati` double(10,4) NOT NULL,
  `longi` double(10,4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `udated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_otp_resets`
--

CREATE TABLE `password_otp_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `recipient_type` enum('Manager','Supervisor') NOT NULL,
  `recipient_id` varchar(50) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `admin_account_no` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Pending','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `task_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `t_start_date` varchar(255) NOT NULL,
  `t_end_date` varchar(255) NOT NULL,
  `task_days` int(11) NOT NULL,
  `task_duration` int(11) NOT NULL,
  `task_obt_mark` double(10,2) NOT NULL,
  `task_mark` double(10,2) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `task_status` varchar(255) NOT NULL DEFAULT 'Ongoing',
  `approved` tinyint(1) DEFAULT NULL,
  `review` text NOT NULL,
  `task_screenshot` longtext NOT NULL,
  `task_live_url` text NOT NULL,
  `task_git_url` text NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
('afgaB0x2vV9wU4K2DBTJqTPsmWrXmRuLuuC0fYX7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic0VzaHN0RVBmcUh5VDd0T042bUNSR09sOXBvWGZZaExheWVXV25rdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYW5hZ2VyL2ludm9pY2VzIjtzOjU6InJvdXRlIjtzOjE4OiJpbnZvaWNlcy5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjU0OiJsb2dpbl9tYW5hZ2VyXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTt9', 1773495768),
('ba0BzldzArJp3UW53dX0GKh4GwA0CeuU5SqR4AB9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWExvaWJSczczZ2ZUOHIzSzdKdkU2czhWVURKTWVFS1ZCVEgweUJHdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYW5hZ2VycyI7czo1OiJyb3V0ZSI7czo4OiJtYW5hZ2VycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1773489600);

-- --------------------------------------------------------

--
-- Table structure for table `shift_table`
--

CREATE TABLE `shift_table` (
  `shift_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `intern_email` varchar(255) NOT NULL,
  `start_shift` time NOT NULL,
  `end_shift` time NOT NULL,
  `onsite_remote` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_attendance`
--

CREATE TABLE `supervisor_attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supervisor_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_complaints`
--

CREATE TABLE `supervisor_complaints` (
  `id` int(11) NOT NULL,
  `eti_id` int(11) NOT NULL,
  `complaint_name` varchar(255) NOT NULL,
  `complaint_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_leaves`
--

CREATE TABLE `supervisor_leaves` (
  `leave_id` bigint(20) UNSIGNED NOT NULL,
  `supervisor_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text NOT NULL,
  `days` int(11) NOT NULL,
  `leave_status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_permissions`
--

CREATE TABLE `supervisor_permissions` (
  `sup_p_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `tech_id` int(11) NOT NULL,
  `internship_type` enum('Remote','Onsite') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor_permissions`
--

INSERT INTO `supervisor_permissions` (`sup_p_id`, `manager_id`, `tech_id`, `internship_type`, `created_at`) VALUES
(1, 13, 1, 'Remote', '2024-10-06 21:47:08'),
(2, 13, 1, 'Onsite', '2024-10-06 21:47:08'),
(3, 13, 2, 'Remote', '2024-10-06 21:47:08'),
(4, 13, 2, 'Onsite', '2024-10-06 21:47:08'),
(5, 13, 3, 'Remote', '2024-10-06 21:47:08'),
(6, 13, 3, 'Onsite', '2024-10-06 21:47:08'),
(7, 14, 8, 'Remote', '2024-10-21 15:52:34'),
(8, 14, 8, 'Onsite', '2024-10-21 15:52:34'),
(9, 14, 11, 'Remote', '2024-10-21 15:52:34'),
(10, 14, 11, 'Onsite', '2024-10-21 15:52:34'),
(11, 14, 13, 'Remote', '2024-10-21 15:52:34'),
(12, 14, 13, 'Onsite', '2024-10-21 15:52:34'),
(13, 15, 6, 'Remote', '2024-10-22 17:38:57'),
(14, 15, 6, 'Onsite', '2024-10-22 17:38:57'),
(15, 15, 18, 'Remote', '2024-10-22 17:38:57'),
(16, 15, 18, 'Onsite', '2024-10-22 17:38:57'),
(17, 15, 25, 'Remote', '2024-10-22 17:38:57'),
(18, 15, 25, 'Onsite', '2024-10-22 17:38:57'),
(19, 13, 35, 'Remote', '2024-10-23 18:13:45'),
(20, 13, 35, 'Onsite', '2024-10-23 18:13:45'),
(21, 16, 17, 'Remote', '2024-10-24 17:47:49'),
(22, 16, 17, 'Onsite', '2024-10-24 17:47:49'),
(25, 16, 21, 'Remote', '2024-11-01 14:05:52'),
(26, 16, 21, 'Onsite', '2024-11-01 14:05:52'),
(27, 16, 6, 'Remote', '2024-11-19 14:04:51'),
(28, 16, 6, 'Onsite', '2024-11-19 14:04:51'),
(29, 16, 25, 'Remote', '2024-11-19 14:04:51'),
(30, 16, 25, 'Onsite', '2024-11-19 14:04:51'),
(33, 16, 19, 'Remote', '2025-01-09 19:59:46'),
(34, 16, 19, 'Onsite', '2025-01-09 19:59:46'),
(35, 13, 4, 'Remote', '2025-01-29 20:15:15'),
(36, 13, 4, 'Onsite', '2025-01-29 20:15:15'),
(45, 17, 37, 'Remote', '2026-03-13 16:44:51'),
(46, 17, 37, 'Onsite', '2026-03-13 16:44:51'),
(47, 17, 38, 'Remote', '2026-03-13 16:44:51'),
(48, 17, 38, 'Onsite', '2026-03-13 16:44:51'),
(49, 20, 37, 'Remote', '2026-03-13 16:45:13'),
(50, 20, 37, 'Onsite', '2026-03-13 16:45:13'),
(51, 20, 38, 'Remote', '2026-03-13 16:45:13'),
(52, 20, 38, 'Onsite', '2026-03-13 16:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `technologies`
--

CREATE TABLE `technologies` (
  `tech_id` int(11) NOT NULL,
  `technology` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technologies`
--

INSERT INTO `technologies` (`tech_id`, `technology`, `status`, `created_at`, `updated_at`) VALUES
(37, 'Next.js', 1, '2026-03-13 11:23:32', '2026-03-13 06:23:32'),
(38, 'Python Django', 1, '2026-03-13 11:23:39', '2026-03-13 06:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `technology_curriculum`
--

CREATE TABLE `technology_curriculum` (
  `curriculum_id` bigint(20) UNSIGNED NOT NULL,
  `tech_id` int(11) NOT NULL,
  `curriculum_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_projects` int(11) NOT NULL DEFAULT 0,
  `total_duration_weeks` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technology_curriculum`
--

INSERT INTO `technology_curriculum` (`curriculum_id`, `tech_id`, `curriculum_name`, `description`, `total_projects`, `total_duration_weeks`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 37, 'test1', 'test disc', 2, 3, 1, 5, '2026-03-13 06:24:23', '2026-03-13 11:11:24');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `inv_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'payment',
  `method` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_date` date NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_by_name` varchar(255) NOT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `uni_id` int(11) NOT NULL,
  `uti` varchar(255) NOT NULL,
  `uni_name` varchar(255) NOT NULL,
  `uni_email` varchar(255) NOT NULL,
  `uni_password` varchar(8) NOT NULL,
  `uni_phone` varchar(255) NOT NULL,
  `uni_status` tinyint(1) NOT NULL DEFAULT 1,
  `account_status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_balances`
--

CREATE TABLE `user_balances` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_code`
--

CREATE TABLE `verification_code` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_feedback`
--

CREATE TABLE `video_feedback` (
  `id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tech` varchar(255) NOT NULL,
  `videoUrl` text NOT NULL,
  `status` varchar(255) DEFAULT 'Pending',
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `req_id` int(11) NOT NULL,
  `eti_id` varchar(255) NOT NULL,
  `req_by` int(11) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `ac_no` varchar(255) NOT NULL,
  `ac_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `amount` double(10,2) NOT NULL,
  `req_status` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `period` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdraw_requests`
--

INSERT INTO `withdraw_requests` (`req_id`, `eti_id`, `req_by`, `bank`, `ac_no`, `ac_name`, `description`, `date`, `amount`, `req_status`, `created_at`, `updated_at`, `period`) VALUES
(1, 'ETI-MANAGER-003', 5, 'limited', '00202221312', 'afz', 'dd fdafwsd', '2026-03-13', 2000.00, 0, '2026-03-13 18:05:45', '2026-03-13 18:05:45', '123'),
(2, 'ETI-MANAGER-003', 5, 'limited', '00202221312', 'afz', '1233', '2026-03-13', 2.00, 0, '2026-03-13 18:06:03', '2026-03-13 18:06:03', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_user_type_index` (`user_id`,`user_type`);

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
-- Indexes for table `complete_test`
--
ALTER TABLE `complete_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `curriculum_projects`
--
ALTER TABLE `curriculum_projects`
  ADD PRIMARY KEY (`cp_id`),
  ADD KEY `curriculum_projects_curriculum_id_foreign` (`curriculum_id`),
  ADD KEY `curriculum_projects_assigned_supervisor_foreign` (`assigned_supervisor`);

--
-- Indexes for table `curriculum_supervisor_mapping`
--
ALTER TABLE `curriculum_supervisor_mapping`
  ADD PRIMARY KEY (`mapping_id`),
  ADD KEY `curriculum_supervisor_mapping_cp_id_foreign` (`cp_id`),
  ADD KEY `curriculum_supervisor_mapping_supervisor_id_foreign` (`supervisor_id`),
  ADD KEY `curriculum_supervisor_mapping_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  ADD PRIMARY KEY (`leave_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `intern_accounts`
--
ALTER TABLE `intern_accounts`
  ADD PRIMARY KEY (`int_id`),
  ADD KEY `eti_id` (`eti_id`);

--
-- Indexes for table `intern_attendance`
--
ALTER TABLE `intern_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `intern_curriculum_assignment`
--
ALTER TABLE `intern_curriculum_assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `intern_curriculum_assignment_curriculum_id_foreign` (`curriculum_id`),
  ADD KEY `intern_curriculum_assignment_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `intern_feedback`
--
ALTER TABLE `intern_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `intern_fees`
--
ALTER TABLE `intern_fees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `intern_leaves`
--
ALTER TABLE `intern_leaves`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `etikey` (`eti_id`);

--
-- Indexes for table `intern_projects`
--
ALTER TABLE `intern_projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `internId` (`eti_id`) USING BTREE,
  ADD KEY `supkey` (`assigned_by`);

--
-- Indexes for table `intern_project_progress`
--
ALTER TABLE `intern_project_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `intern_project_progress_assignment_id_foreign` (`assignment_id`),
  ADD KEY `intern_project_progress_cp_id_foreign` (`cp_id`);

--
-- Indexes for table `intern_remaining_amounts`
--
ALTER TABLE `intern_remaining_amounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `intern_table`
--
ALTER TABLE `intern_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `intern_tasks`
--
ALTER TABLE `intern_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `intenrkey` (`eti_id`),
  ADD KEY `tasksupkey` (`assigned_by`);

--
-- Indexes for table `interview_test`
--
ALTER TABLE `interview_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_approvals`
--
ALTER TABLE `invoice_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_approvals_invoice_id_foreign` (`invoice_id`);

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
-- Indexes for table `knowledge_bases`
--
ALTER TABLE `knowledge_bases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manager_accounts`
--
ALTER TABLE `manager_accounts`
  ADD PRIMARY KEY (`manager_id`);

--
-- Indexes for table `manager_complaints`
--
ALTER TABLE `manager_complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eti_id` (`eti_id`);

--
-- Indexes for table `manager_permissions`
--
ALTER TABLE `manager_permissions`
  ADD PRIMARY KEY (`manager_p_id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `tech_id` (`tech_id`);

--
-- Indexes for table `manager_roles`
--
ALTER TABLE `manager_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_roles_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_letter_requests`
--
ALTER TABLE `offer_letter_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offer_letter_templates_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `office_location`
--
ALTER TABLE `office_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_otp_resets`
--
ALTER TABLE `password_otp_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_otp_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `taskkey` (`project_id`),
  ADD KEY `etikey1` (`eti_id`),
  ADD KEY `assignby` (`assigned_by`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shift_table`
--
ALTER TABLE `shift_table`
  ADD PRIMARY KEY (`shift_id`),
  ADD UNIQUE KEY `eti_id` (`eti_id`,`intern_email`) USING BTREE;

--
-- Indexes for table `supervisor_attendance`
--
ALTER TABLE `supervisor_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisor_complaints`
--
ALTER TABLE `supervisor_complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eti_id` (`eti_id`);

--
-- Indexes for table `supervisor_leaves`
--
ALTER TABLE `supervisor_leaves`
  ADD PRIMARY KEY (`leave_id`);

--
-- Indexes for table `supervisor_permissions`
--
ALTER TABLE `supervisor_permissions`
  ADD PRIMARY KEY (`sup_p_id`),
  ADD KEY `skey` (`manager_id`),
  ADD KEY `tkey` (`tech_id`);

--
-- Indexes for table `technologies`
--
ALTER TABLE `technologies`
  ADD PRIMARY KEY (`tech_id`);

--
-- Indexes for table `technology_curriculum`
--
ALTER TABLE `technology_curriculum`
  ADD PRIMARY KEY (`curriculum_id`),
  ADD KEY `technology_curriculum_tech_id_foreign` (`tech_id`),
  ADD KEY `technology_curriculum_created_by_foreign` (`created_by`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_invoice_id_payment_date_index` (`invoice_id`,`payment_date`),
  ADD KEY `transactions_created_by_index` (`created_by`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`uni_id`);

--
-- Indexes for table `user_balances`
--
ALTER TABLE `user_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verification_code`
--
ALTER TABLE `verification_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `video_feedback`
--
ALTER TABLE `video_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `reqkey1` (`req_by`),
  ADD KEY `reqkey2` (`eti_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complete_test`
--
ALTER TABLE `complete_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3606;

--
-- AUTO_INCREMENT for table `curriculum_projects`
--
ALTER TABLE `curriculum_projects`
  MODIFY `cp_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `curriculum_supervisor_mapping`
--
ALTER TABLE `curriculum_supervisor_mapping`
  MODIFY `mapping_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leaves`
--
ALTER TABLE `employee_leaves`
  MODIFY `leave_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intern_accounts`
--
ALTER TABLE `intern_accounts`
  MODIFY `int_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5468;

--
-- AUTO_INCREMENT for table `intern_attendance`
--
ALTER TABLE `intern_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12648;

--
-- AUTO_INCREMENT for table `intern_curriculum_assignment`
--
ALTER TABLE `intern_curriculum_assignment`
  MODIFY `assignment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intern_feedback`
--
ALTER TABLE `intern_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `intern_fees`
--
ALTER TABLE `intern_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intern_leaves`
--
ALTER TABLE `intern_leaves`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `intern_projects`
--
ALTER TABLE `intern_projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `intern_project_progress`
--
ALTER TABLE `intern_project_progress`
  MODIFY `progress_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intern_remaining_amounts`
--
ALTER TABLE `intern_remaining_amounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=481;

--
-- AUTO_INCREMENT for table `intern_table`
--
ALTER TABLE `intern_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25395;

--
-- AUTO_INCREMENT for table `intern_tasks`
--
ALTER TABLE `intern_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT for table `interview_test`
--
ALTER TABLE `interview_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=533;

--
-- AUTO_INCREMENT for table `invoice_approvals`
--
ALTER TABLE `invoice_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_bases`
--
ALTER TABLE `knowledge_bases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_accounts`
--
ALTER TABLE `manager_accounts`
  MODIFY `manager_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `manager_complaints`
--
ALTER TABLE `manager_complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `manager_permissions`
--
ALTER TABLE `manager_permissions`
  MODIFY `manager_p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `manager_roles`
--
ALTER TABLE `manager_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `offer_letter_requests`
--
ALTER TABLE `offer_letter_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `office_location`
--
ALTER TABLE `office_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_otp_resets`
--
ALTER TABLE `password_otp_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT for table `shift_table`
--
ALTER TABLE `shift_table`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=371;

--
-- AUTO_INCREMENT for table `supervisor_attendance`
--
ALTER TABLE `supervisor_attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor_complaints`
--
ALTER TABLE `supervisor_complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supervisor_leaves`
--
ALTER TABLE `supervisor_leaves`
  MODIFY `leave_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor_permissions`
--
ALTER TABLE `supervisor_permissions`
  MODIFY `sup_p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `technologies`
--
ALTER TABLE `technologies`
  MODIFY `tech_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `technology_curriculum`
--
ALTER TABLE `technology_curriculum`
  MODIFY `curriculum_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `uni_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `user_balances`
--
ALTER TABLE `user_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=597;

--
-- AUTO_INCREMENT for table `verification_code`
--
ALTER TABLE `verification_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10985;

--
-- AUTO_INCREMENT for table `video_feedback`
--
ALTER TABLE `video_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `curriculum_projects`
--
ALTER TABLE `curriculum_projects`
  ADD CONSTRAINT `curriculum_projects_assigned_supervisor_foreign` FOREIGN KEY (`assigned_supervisor`) REFERENCES `manager_accounts` (`manager_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `curriculum_projects_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `technology_curriculum` (`curriculum_id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_supervisor_mapping`
--
ALTER TABLE `curriculum_supervisor_mapping`
  ADD CONSTRAINT `curriculum_supervisor_mapping_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `manager_accounts` (`manager_id`),
  ADD CONSTRAINT `curriculum_supervisor_mapping_cp_id_foreign` FOREIGN KEY (`cp_id`) REFERENCES `curriculum_projects` (`cp_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_supervisor_mapping_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `manager_accounts` (`manager_id`);

--
-- Constraints for table `intern_curriculum_assignment`
--
ALTER TABLE `intern_curriculum_assignment`
  ADD CONSTRAINT `intern_curriculum_assignment_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `manager_accounts` (`manager_id`),
  ADD CONSTRAINT `intern_curriculum_assignment_curriculum_id_foreign` FOREIGN KEY (`curriculum_id`) REFERENCES `technology_curriculum` (`curriculum_id`);

--
-- Constraints for table `intern_projects`
--
ALTER TABLE `intern_projects`
  ADD CONSTRAINT `internkey` FOREIGN KEY (`eti_id`) REFERENCES `intern_accounts` (`eti_id`),
  ADD CONSTRAINT `supkey` FOREIGN KEY (`assigned_by`) REFERENCES `manager_accounts` (`manager_id`);

--
-- Constraints for table `intern_project_progress`
--
ALTER TABLE `intern_project_progress`
  ADD CONSTRAINT `intern_project_progress_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `intern_curriculum_assignment` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intern_project_progress_cp_id_foreign` FOREIGN KEY (`cp_id`) REFERENCES `curriculum_projects` (`cp_id`);

--
-- Constraints for table `intern_tasks`
--
ALTER TABLE `intern_tasks`
  ADD CONSTRAINT `intenrkey` FOREIGN KEY (`eti_id`) REFERENCES `intern_accounts` (`eti_id`),
  ADD CONSTRAINT `tasksupkey` FOREIGN KEY (`assigned_by`) REFERENCES `manager_accounts` (`manager_id`);

--
-- Constraints for table `invoice_approvals`
--
ALTER TABLE `invoice_approvals`
  ADD CONSTRAINT `invoice_approvals_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `manager_complaints`
--
ALTER TABLE `manager_complaints`
  ADD CONSTRAINT `manager_complaints_ibfk_1` FOREIGN KEY (`eti_id`) REFERENCES `intern_accounts` (`int_id`) ON DELETE CASCADE;

--
-- Constraints for table `manager_roles`
--
ALTER TABLE `manager_roles`
  ADD CONSTRAINT `manager_roles_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `manager_accounts` (`manager_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offer_letter_templates`
--
ALTER TABLE `offer_letter_templates`
  ADD CONSTRAINT `offer_letter_templates_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `manager_accounts` (`manager_id`);

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `intkey` FOREIGN KEY (`eti_id`) REFERENCES `intern_accounts` (`eti_id`),
  ADD CONSTRAINT `projkey` FOREIGN KEY (`project_id`) REFERENCES `intern_projects` (`project_id`),
  ADD CONSTRAINT `superkey` FOREIGN KEY (`assigned_by`) REFERENCES `manager_accounts` (`manager_id`);

--
-- Constraints for table `technology_curriculum`
--
ALTER TABLE `technology_curriculum`
  ADD CONSTRAINT `technology_curriculum_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `manager_accounts` (`manager_id`),
  ADD CONSTRAINT `technology_curriculum_tech_id_foreign` FOREIGN KEY (`tech_id`) REFERENCES `technologies` (`tech_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
