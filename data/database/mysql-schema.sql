-- MySQL schema for Thư Viện AI (Fresh Install)
-- Usage:
--   mysql -u <user> -p -h <host> -P <port> < src/php-backend/tools/mysql-schema.sql

-- Create database
CREATE DATABASE IF NOT EXISTS `thuvien_ai`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `thuvien_ai`;

-- Users table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NULL,
  `display_name` VARCHAR(255) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'user',
  `is_active` BOOLEAN DEFAULT TRUE,
  `failed_login_count` INT DEFAULT 0,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_users_username` (`username`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active_created` (`is_active`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs table
CREATE TABLE `logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `detail` VARCHAR(500) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(500) NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_logs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  KEY `idx_logs_user_time` (`user_id`, `timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI query history
CREATE TABLE `ai_query_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `query` TEXT NOT NULL,
  `response` LONGTEXT NOT NULL,
  `model` VARCHAR(100),
  `tokens_used` INT DEFAULT 0,
  `prompt_tokens` INT DEFAULT 0,
  `completion_tokens` INT DEFAULT 0,
  `latency_ms` INT DEFAULT 0,
  `status` VARCHAR(30) DEFAULT 'ok',
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_aiqh_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_user_timestamp` (`user_id`, `timestamp`),
  INDEX `idx_model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Documents table
CREATE TABLE `documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50) NOT NULL,
  `mime_type` VARCHAR(100) NULL,
  `file_size` BIGINT NOT NULL,
  `checksum` VARCHAR(128) NULL,
  `content` LONGTEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_docs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_created` (`user_id`, `created_at`),
  INDEX `idx_docs_checksum` (`checksum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: admin seed (admin/admin) will be auto-created by PHP on first connection
-- via Database::ensureSingleAdmin().