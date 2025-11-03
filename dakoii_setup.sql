-- ============================================
-- Dakoii Admin Portal - Database Setup Script
-- ============================================
-- This script creates the dakoii_users table and inserts the default user
-- 
-- Database: corres_db
-- Default User:
--   Name: Free Kenny
--   Username: fkenny
--   Password: dakoii
-- ============================================

USE corres_db;

-- Create dakoii_users table
CREATE TABLE IF NOT EXISTS `dakoii_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default user (Free Kenny)
-- Password is hashed using PASSWORD_DEFAULT (bcrypt)
-- Plain text password: dakoii
-- Note: The hash below is a placeholder. Run generate_password.php to get the actual hash
-- Or use the run_migration.php script which will hash it automatically
INSERT INTO `dakoii_users` (`name`, `username`, `password`, `created_at`, `updated_at`)
VALUES (
  'Free Kenny',
  'fkenny',
  '$2y$10$YourHashWillBeGeneratedHere',
  NOW(),
  NOW()
) ON DUPLICATE KEY UPDATE
  `name` = 'Free Kenny',
  `updated_at` = NOW();

-- Create migrations table if it doesn't exist
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(255) NOT NULL,
  `class` VARCHAR(255) NOT NULL,
  `group` VARCHAR(255) NOT NULL,
  `namespace` VARCHAR(255) NOT NULL,
  `time` INT(11) NOT NULL,
  `batch` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Record the migration
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES (
  '2025-11-03-162500',
  'App\\Database\\Migrations\\CreateDakoiiUsersTable',
  'default',
  'App',
  UNIX_TIMESTAMP(),
  1
) ON DUPLICATE KEY UPDATE `time` = UNIX_TIMESTAMP();

-- Display success message
SELECT 'Database setup completed successfully!' AS Status;
SELECT 'You can now login to Dakoii Admin Portal at: http://localhost/corres/dakoii' AS Message;
SELECT 'Username: fkenny' AS Credentials;
SELECT 'Password: dakoii' AS Password;

