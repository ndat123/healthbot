-- Migration: Create notifications table
-- Run this SQL script directly in your MySQL database

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general' COMMENT 'general, reminder, appointment, health, newsletter, etc.',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `icon` varchar(255) DEFAULT NULL COMMENT 'icon class or path',
  `action_url` varchar(255) DEFAULT NULL COMMENT 'URL to navigate when clicked',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL COMMENT 'Additional data',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  KEY `notifications_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

