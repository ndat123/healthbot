-- Migration: Create reports table
-- Run this SQL script directly in your MySQL database

CREATE TABLE IF NOT EXISTS `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL COMMENT 'User Analytics, AI Analytics, Health Analytics',
  `type` enum('pdf','excel','csv') NOT NULL DEFAULT 'pdf',
  `status` enum('pending','ready','failed') NOT NULL DEFAULT 'pending',
  `file_path` varchar(255) DEFAULT NULL,
  `data` json DEFAULT NULL COMMENT 'Store report data',
  `generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_category_status_index` (`category`,`status`),
  KEY `reports_generated_at_index` (`generated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

