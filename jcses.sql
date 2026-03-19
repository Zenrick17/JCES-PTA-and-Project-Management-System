-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.15.0.7171
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for s1707_JCSES
CREATE DATABASE IF NOT EXISTS `s1707_JCSES` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `s1707_JCSES`;

-- Dumping structure for view jcses_pta_system.active_parent_students
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `active_parent_students` (
	`parentID` BIGINT(20) UNSIGNED NOT NULL,
	`parent_first_name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`parent_last_name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`phone` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`studentID` BIGINT(20) UNSIGNED NOT NULL,
	`student_name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`grade_level` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`section` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`relationship_type` ENUM('mother','father','guardian','grandparent','sibling','other') NOT NULL COLLATE 'utf8mb4_unicode_ci'
);

-- Dumping structure for table jcses_pta_system.announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `announcementID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('important','notice','update','event') NOT NULL DEFAULT 'notice',
  `audience` enum('everyone','parents','teachers','administrator','principal','staff') NOT NULL DEFAULT 'everyone',
  `created_by` bigint(20) unsigned NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`announcementID`),
  KEY `announcements_category_index` (`category`),
  KEY `announcements_audience_index` (`audience`),
  KEY `announcements_is_active_index` (`is_active`),
  KEY `announcements_published_at_index` (`published_at`),
  KEY `announcements_expires_at_index` (`expires_at`),
  KEY `announcements_created_by_foreign` (`created_by`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.announcements: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.cache: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.cache_locks: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.dashboard_metrics
CREATE TABLE IF NOT EXISTS `dashboard_metrics` (
  `metricID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metric_name` varchar(100) NOT NULL,
  `metric_category` enum('enrollment','projects','financial','participation','system') NOT NULL,
  `current_value` decimal(15,2) NOT NULL,
  `target_value` decimal(15,2) DEFAULT NULL,
  `unit_of_measure` varchar(20) DEFAULT NULL,
  `calculation_method` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`metricID`),
  KEY `dashboard_metrics_metric_category_index` (`metric_category`),
  KEY `dashboard_metrics_is_active_index` (`is_active`),
  KEY `dashboard_metrics_display_order_index` (`display_order`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.dashboard_metrics: ~6 rows (approximately)

-- Dumping structure for table jcses_pta_system.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.financial_reconciliations
CREATE TABLE IF NOT EXISTS `financial_reconciliations` (
  `reconciliationID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reconciliation_period` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_system_amount` decimal(12,2) NOT NULL,
  `total_bank_amount` decimal(12,2) NOT NULL,
  `discrepancy_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reconciliation_status` enum('pending','completed','discrepancy_found') NOT NULL DEFAULT 'pending',
  `reconciled_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reconciled_by` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`reconciliationID`),
  KEY `financial_reconciliations_reconciliation_period_index` (`reconciliation_period`),
  KEY `financial_reconciliations_reconciled_date_index` (`reconciled_date`),
  KEY `financial_reconciliations_reconciled_by_foreign` (`reconciled_by`),
  CONSTRAINT `financial_reconciliations_reconciled_by_foreign` FOREIGN KEY (`reconciled_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.financial_reconciliations: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.job_batches: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.jobs: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.migrations: ~16 rows (approximately)

-- Dumping structure for table jcses_pta_system.parent_student_relationships
CREATE TABLE IF NOT EXISTS `parent_student_relationships` (
  `relationshipID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentID` bigint(20) unsigned NOT NULL,
  `studentID` bigint(20) unsigned NOT NULL,
  `relationship_type` enum('mother','father','guardian','grandparent','sibling','other') NOT NULL,
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`relationshipID`),
  UNIQUE KEY `unique_parent_student` (`parentID`,`studentID`),
  KEY `parent_student_relationships_studentID_foreign` (`studentID`),
  CONSTRAINT `parent_student_relationships_parentID_foreign` FOREIGN KEY (`parentID`) REFERENCES `parents` (`parentID`) ON DELETE CASCADE,
  CONSTRAINT `parent_student_relationships_studentID_foreign` FOREIGN KEY (`studentID`) REFERENCES `students` (`studentID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.parent_student_relationships: ~12 rows (approximately)

-- Dumping structure for table jcses_pta_system.parents
CREATE TABLE IF NOT EXISTS `parents` (
  `parentID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `account_status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `userID` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`parentID`),
  UNIQUE KEY `parents_email_unique` (`email`),
  UNIQUE KEY `parents_userID_unique` (`userID`),
  KEY `parents_email_index` (`email`),
  KEY `parents_phone_index` (`phone`),
  KEY `parents_last_name_first_name_index` (`last_name`,`first_name`),
  CONSTRAINT `parents_userID_foreign` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping structure for table jcses_pta_system.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.payment_receipts
CREATE TABLE IF NOT EXISTS `payment_receipts` (
  `receiptID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paymentID` bigint(20) unsigned NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `receipt_content` text NOT NULL,
  `generated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `generated_by` bigint(20) unsigned NOT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `print_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`receiptID`),
  UNIQUE KEY `payment_receipts_receipt_number_unique` (`receipt_number`),
  KEY `payment_receipts_receipt_number_index` (`receipt_number`),
  KEY `payment_receipts_generated_date_index` (`generated_date`),
  KEY `payment_receipts_paymentID_foreign` (`paymentID`),
  KEY `payment_receipts_generated_by_foreign` (`generated_by`),
  CONSTRAINT `payment_receipts_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION,
  CONSTRAINT `payment_receipts_paymentID_foreign` FOREIGN KEY (`paymentID`) REFERENCES `payment_transactions` (`paymentID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.payment_receipts: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.payment_transactions
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `paymentID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parentID` bigint(20) unsigned NOT NULL,
  `projectID` bigint(20) unsigned NOT NULL,
  `contributionID` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','check','bank_transfer') NOT NULL,
  `transaction_status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_number` varchar(50) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `processed_by` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`paymentID`),
  UNIQUE KEY `payment_transactions_receipt_number_unique` (`receipt_number`),
  KEY `payment_transactions_parentID_index` (`parentID`),
  KEY `payment_transactions_projectID_index` (`projectID`),
  KEY `payment_transactions_transaction_date_index` (`transaction_date`),
  KEY `payment_transactions_receipt_number_index` (`receipt_number`),
  KEY `payment_transactions_transaction_status_index` (`transaction_status`),
  KEY `payment_transactions_contributionID_foreign` (`contributionID`),
  KEY `payment_transactions_processed_by_foreign` (`processed_by`),
  CONSTRAINT `payment_transactions_contributionID_foreign` FOREIGN KEY (`contributionID`) REFERENCES `project_contributions` (`contributionID`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_parentID_foreign` FOREIGN KEY (`parentID`) REFERENCES `parents` (`parentID`) ON DELETE CASCADE,
  CONSTRAINT `payment_transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION,
  CONSTRAINT `payment_transactions_projectID_foreign` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.payment_transactions: ~9 rows (approximately)

-- Dumping structure for table jcses_pta_system.project_contributions
CREATE TABLE IF NOT EXISTS `project_contributions` (
  `contributionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` bigint(20) unsigned NOT NULL,
  `parentID` bigint(20) unsigned NOT NULL,
  `contribution_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','check','bank_transfer','gcash') NOT NULL,
  `payment_status` enum('pending','completed','refunded','failed') NOT NULL DEFAULT 'pending',
  `contribution_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `receipt_number` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`contributionID`),
  UNIQUE KEY `project_contributions_receipt_number_unique` (`receipt_number`),
  KEY `project_contributions_projectID_index` (`projectID`),
  KEY `project_contributions_parentID_index` (`parentID`),
  KEY `project_contributions_contribution_date_index` (`contribution_date`),
  KEY `project_contributions_contribution_amount_index` (`contribution_amount`),
  KEY `project_contributions_processed_by_foreign` (`processed_by`),
  CONSTRAINT `project_contributions_parentID_foreign` FOREIGN KEY (`parentID`) REFERENCES `parents` (`parentID`) ON DELETE CASCADE,
  CONSTRAINT `project_contributions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL,
  CONSTRAINT `project_contributions_projectID_foreign` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.project_contributions: ~49 rows (approximately)

-- Dumping structure for view jcses_pta_system.project_financial_summary
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `project_financial_summary` (
	`projectID` BIGINT(20) UNSIGNED NOT NULL,
	`project_name` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`target_budget` DECIMAL(12,2) NOT NULL,
	`current_amount` DECIMAL(12,2) NOT NULL,
	`completion_percentage` DECIMAL(21,6) NULL,
	`total_contributions` BIGINT(21) NOT NULL,
	`project_status` ENUM('created','active','in_progress','completed','archived','cancelled') NOT NULL COLLATE 'utf8mb4_unicode_ci'
);

-- Dumping structure for table jcses_pta_system.project_milestones
CREATE TABLE IF NOT EXISTS `project_milestones` (
  `milestoneID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` bigint(20) unsigned NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `target_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`milestoneID`),
  KEY `project_milestones_projectID_index` (`projectID`),
  KEY `project_milestones_sort_order_index` (`sort_order`),
  CONSTRAINT `project_milestones_projectID_foreign` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.project_milestones: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.project_updates
CREATE TABLE IF NOT EXISTS `project_updates` (
  `updateID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `projectID` bigint(20) unsigned NOT NULL,
  `update_title` varchar(200) NOT NULL,
  `update_description` text NOT NULL,
  `milestone_achieved` varchar(200) DEFAULT NULL,
  `progress_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `update_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`updateID`),
  KEY `project_updates_projectID_index` (`projectID`),
  KEY `project_updates_update_date_index` (`update_date`),
  KEY `project_updates_updated_by_foreign` (`updated_by`),
  CONSTRAINT `project_updates_projectID_foreign` FOREIGN KEY (`projectID`) REFERENCES `projects` (`projectID`) ON DELETE CASCADE,
  CONSTRAINT `project_updates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.project_updates: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `projectID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `goals` text NOT NULL,
  `target_budget` decimal(12,2) NOT NULL,
  `current_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `start_date` date NOT NULL,
  `target_completion_date` date NOT NULL,
  `actual_completion_date` date DEFAULT NULL,
  `project_status` enum('created','active','in_progress','completed','archived','cancelled') NOT NULL DEFAULT 'created',
  `created_by` bigint(20) unsigned NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`projectID`),
  KEY `projects_project_status_index` (`project_status`),
  KEY `projects_created_date_index` (`created_date`),
  KEY `projects_current_amount_index` (`current_amount`),
  KEY `projects_start_date_target_completion_date_index` (`start_date`,`target_completion_date`),
  KEY `projects_created_by_foreign` (`created_by`),
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.projects: ~4 rows (approximately)

-- Dumping structure for table jcses_pta_system.refunds
CREATE TABLE IF NOT EXISTS `refunds` (
  `refundID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `paymentID` bigint(20) unsigned NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_reason` text NOT NULL,
  `refund_status` enum('pending','approved','completed','rejected') NOT NULL DEFAULT 'pending',
  `requested_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_date` timestamp NULL DEFAULT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`refundID`),
  KEY `refunds_refund_status_index` (`refund_status`),
  KEY `refunds_requested_date_index` (`requested_date`),
  KEY `refunds_paymentID_foreign` (`paymentID`),
  KEY `refunds_requested_by_foreign` (`requested_by`),
  KEY `refunds_processed_by_foreign` (`processed_by`),
  CONSTRAINT `refunds_paymentID_foreign` FOREIGN KEY (`paymentID`) REFERENCES `payment_transactions` (`paymentID`) ON DELETE CASCADE,
  CONSTRAINT `refunds_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL,
  CONSTRAINT `refunds_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.refunds: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.report_execution_log
CREATE TABLE IF NOT EXISTS `report_execution_log` (
  `executionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reportID` bigint(20) unsigned NOT NULL,
  `execution_start` timestamp NOT NULL DEFAULT current_timestamp(),
  `execution_end` timestamp NULL DEFAULT NULL,
  `execution_status` enum('running','completed','failed','cancelled') NOT NULL DEFAULT 'running',
  `record_count` int(11) NOT NULL DEFAULT 0,
  `file_size_bytes` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `executed_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`executionID`),
  KEY `report_execution_log_reportID_index` (`reportID`),
  KEY `report_execution_log_execution_start_index` (`execution_start`),
  KEY `report_execution_log_execution_status_index` (`execution_status`),
  KEY `report_execution_log_executed_by_foreign` (`executed_by`),
  CONSTRAINT `report_execution_log_executed_by_foreign` FOREIGN KEY (`executed_by`) REFERENCES `users` (`userID`) ON DELETE SET NULL,
  CONSTRAINT `report_execution_log_reportID_foreign` FOREIGN KEY (`reportID`) REFERENCES `reports` (`reportID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.report_execution_log: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.report_recipients
CREATE TABLE IF NOT EXISTS `report_recipients` (
  `recipientID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reportID` bigint(20) unsigned NOT NULL,
  `userID` bigint(20) unsigned NOT NULL,
  `recipient_email` varchar(150) NOT NULL,
  `delivery_method` enum('email','download','both') NOT NULL DEFAULT 'email',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`recipientID`),
  KEY `report_recipients_reportID_index` (`reportID`),
  KEY `report_recipients_is_active_index` (`is_active`),
  KEY `report_recipients_userID_foreign` (`userID`),
  CONSTRAINT `report_recipients_reportID_foreign` FOREIGN KEY (`reportID`) REFERENCES `reports` (`reportID`) ON DELETE CASCADE,
  CONSTRAINT `report_recipients_userID_foreign` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.report_recipients: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.reports
CREATE TABLE IF NOT EXISTS `reports` (
  `reportID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_name` varchar(200) NOT NULL,
  `report_type` enum('participation','financial','project_analytics','custom','automated') NOT NULL,
  `report_description` text DEFAULT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `generated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `generated_by` bigint(20) unsigned NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_format` enum('pdf','excel','csv','html') NOT NULL,
  `is_scheduled` tinyint(1) NOT NULL DEFAULT 0,
  `schedule_frequency` enum('daily','weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `next_run_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`reportID`),
  KEY `reports_report_type_index` (`report_type`),
  KEY `reports_generated_date_index` (`generated_date`),
  KEY `reports_is_scheduled_next_run_date_index` (`is_scheduled`,`next_run_date`),
  KEY `reports_generated_by_foreign` (`generated_by`),
  CONSTRAINT `reports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.reports: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.role_permissions
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `rolePermissionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `roleID` bigint(20) unsigned NOT NULL,
  `permissionID` bigint(20) unsigned NOT NULL,
  `granted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `granted_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`rolePermissionID`),
  UNIQUE KEY `unique_role_permission` (`roleID`,`permissionID`),
  KEY `role_permissions_permissionID_foreign` (`permissionID`),
  KEY `role_permissions_granted_by_foreign` (`granted_by`),
  CONSTRAINT `role_permissions_granted_by_foreign` FOREIGN KEY (`granted_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION,
  CONSTRAINT `role_permissions_permissionID_foreign` FOREIGN KEY (`permissionID`) REFERENCES `user_permissions` (`permissionID`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_roleID_foreign` FOREIGN KEY (`roleID`) REFERENCES `user_roles` (`roleID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.role_permissions: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.schedules
CREATE TABLE IF NOT EXISTS `schedules` (
  `scheduleID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `scheduled_date` datetime NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `category` enum('meeting','event','maintenance','academic','review','other') NOT NULL DEFAULT 'other',
  `priority` enum('high','medium','low') NOT NULL DEFAULT 'medium',
  `visibility` enum('everyone','administrator','principal','teacher','parent') NOT NULL DEFAULT 'everyone',
  `created_by` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`scheduleID`),
  KEY `schedules_scheduled_date_index` (`scheduled_date`),
  KEY `schedules_category_index` (`category`),
  KEY `schedules_priority_index` (`priority`),
  KEY `schedules_visibility_index` (`visibility`),
  KEY `schedules_is_active_index` (`is_active`),
  KEY `schedules_is_completed_index` (`is_completed`),
  KEY `schedules_created_by_foreign` (`created_by`),
  CONSTRAINT `schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.schedules: ~8 rows (approximately)

-- Dumping structure for table jcses_pta_system.security_audit_log
CREATE TABLE IF NOT EXISTS `security_audit_log` (
  `logID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userID` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_affected` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) NOT NULL DEFAULT 1,
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`logID`),
  KEY `security_audit_log_userID_index` (`userID`),
  KEY `security_audit_log_timestamp_index` (`timestamp`),
  KEY `security_audit_log_action_index` (`action`),
  CONSTRAINT `security_audit_log_userID_foreign` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2539 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.security_audit_log: ~2,388 rows (approximately)

-- Dumping structure for table jcses_pta_system.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.sessions: ~6 rows (approximately)

-- Dumping structure for table jcses_pta_system.students
CREATE TABLE IF NOT EXISTS `students` (
  `studentID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(150) NOT NULL,
  `grade_level` varchar(20) NOT NULL,
  `section` varchar(50) DEFAULT NULL,
  `enrollment_status` enum('active','transferred','graduated','dropped') NOT NULL DEFAULT 'active',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `academic_year` varchar(20) NOT NULL,
  `enrollment_date` date NOT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`studentID`),
  KEY `students_grade_level_index` (`grade_level`),
  KEY `students_academic_year_index` (`academic_year`),
  KEY `students_enrollment_status_index` (`enrollment_status`),
  KEY `students_is_archived_index` (`is_archived`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.students: ~41 rows (approximately)

-- Dumping structure for table jcses_pta_system.user_permissions
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `permissionID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  `permission_description` text DEFAULT NULL,
  `module_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`permissionID`),
  UNIQUE KEY `user_permissions_permission_name_unique` (`permission_name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.user_permissions: ~14 rows (approximately)

-- Dumping structure for table jcses_pta_system.user_role_assignments
CREATE TABLE IF NOT EXISTS `user_role_assignments` (
  `assignmentID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userID` bigint(20) unsigned NOT NULL,
  `roleID` bigint(20) unsigned NOT NULL,
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`assignmentID`),
  KEY `user_role_assignments_userID_index` (`userID`),
  KEY `user_role_assignments_is_active_index` (`is_active`),
  KEY `user_role_assignments_roleID_foreign` (`roleID`),
  KEY `user_role_assignments_assigned_by_foreign` (`assigned_by`),
  CONSTRAINT `user_role_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`userID`) ON DELETE NO ACTION,
  CONSTRAINT `user_role_assignments_roleID_foreign` FOREIGN KEY (`roleID`) REFERENCES `user_roles` (`roleID`) ON DELETE CASCADE,
  CONSTRAINT `user_role_assignments_userID_foreign` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.user_role_assignments: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.user_roles
CREATE TABLE IF NOT EXISTS `user_roles` (
  `roleID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`roleID`),
  UNIQUE KEY `user_roles_role_name_unique` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.user_roles: ~4 rows (approximately)

-- Dumping structure for table jcses_pta_system.user_sessions
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `sessionID` varchar(128) NOT NULL,
  `userID` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sessionID`),
  KEY `user_sessions_userID_index` (`userID`),
  KEY `user_sessions_last_activity_index` (`last_activity`),
  KEY `user_sessions_is_active_index` (`is_active`),
  CONSTRAINT `user_sessions_userID_foreign` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.user_sessions: ~0 rows (approximately)

-- Dumping structure for table jcses_pta_system.users
CREATE TABLE IF NOT EXISTS `users` (
  `userID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('parent','administrator','teacher','principal') NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `password_changed_date` timestamp NULL DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `account_locked_until` timestamp NULL DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_username_index` (`username`),
  KEY `users_user_type_index` (`user_type`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_is_archived_index` (`is_archived`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table jcses_pta_system.users: ~13 rows (approximately)

-- Dumping structure for trigger jcses_pta_system.update_project_amount_after_contribution
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER update_project_amount_after_contribution
	AFTER INSERT ON project_contributions
	FOR EACH ROW
	BEGIN
		UPDATE projects
		SET current_amount = (
			SELECT COALESCE(SUM(contribution_amount), 0)
			FROM project_contributions
			WHERE projectID = NEW.projectID AND payment_status = 'completed'
		)
		WHERE projectID = NEW.projectID;
	END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger jcses_pta_system.update_project_amount_after_contribution_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER update_project_amount_after_contribution_update
	AFTER UPDATE ON project_contributions
	FOR EACH ROW
	BEGIN
		UPDATE projects
		SET current_amount = (
			SELECT COALESCE(SUM(contribution_amount), 0)
			FROM project_contributions
			WHERE projectID = NEW.projectID AND payment_status = 'completed'
		)
		WHERE projectID = NEW.projectID;
	END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `active_parent_students`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `active_parent_students` AS SELECT
		p.parentID,
		p.first_name AS parent_first_name,
		p.last_name AS parent_last_name,
		p.email,
		p.phone,
		s.studentID,
		s.student_name,
		s.grade_level,
		s.section,
		psr.relationship_type
	FROM parents p
	JOIN parent_student_relationships psr ON p.parentID = psr.parentID
	JOIN students s ON psr.studentID = s.studentID
	WHERE p.account_status = 'active' AND s.enrollment_status = 'active' 
;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `project_financial_summary`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `project_financial_summary` AS SELECT
		p.projectID,
		p.project_name,
		p.target_budget,
		p.current_amount,
		(p.current_amount / p.target_budget * 100) AS completion_percentage,
		COUNT(pc.contributionID) AS total_contributions,
		p.project_status
	FROM projects p
	LEFT JOIN project_contributions pc ON p.projectID = pc.projectID
	WHERE pc.payment_status = 'completed' OR pc.payment_status IS NULL
	GROUP BY p.projectID, p.project_name, p.target_budget, p.current_amount, p.project_status 
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
