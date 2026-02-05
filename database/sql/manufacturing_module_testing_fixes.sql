-- Manufacturing Module Testing Fixes
-- Run this SQL script to fix issues discovered during testing
-- Date: 2026-02-05

-- ===========================================
-- Fix 1: Create employees table (if not exists)
-- ===========================================
CREATE TABLE IF NOT EXISTS `employees` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `email` VARCHAR(191) NULL,
    `phone` VARCHAR(50) NULL,
    `address` TEXT NULL,
    `experience` VARCHAR(100) NULL,
    `photo` VARCHAR(255) NULL,
    `salary` DECIMAL(18,2) NULL,
    `vacation` VARCHAR(100) NULL,
    `city` VARCHAR(100) NULL,
    `status` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Inactive',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_employees_status` (`status`),
    KEY `idx_employees_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================================
-- Fix 2: Add order_date column to production_orders
-- ===========================================
-- Check if column exists first, then add if not present
SET @dbname = DATABASE();
SET @tablename = 'production_orders';
SET @columnname = 'order_date';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " DATE NULL AFTER `reference`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ===========================================
-- VERIFICATION QUERIES
-- ===========================================

-- Verify employees table exists
SHOW TABLES LIKE 'employees';

-- Verify employees table structure
DESCRIBE employees;

-- Verify order_date column was added
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'production_orders'
  AND COLUMN_NAME = 'order_date';

-- ===========================================
-- ROLLBACK SCRIPT (if needed)
-- ===========================================

-- To rollback these changes, run:
/*
-- Remove order_date column
ALTER TABLE `production_orders` DROP COLUMN `order_date`;

-- Drop employees table (WARNING: This will delete all employee data)
-- Only uncomment if you're absolutely sure!
-- DROP TABLE IF EXISTS `employees`;
*/
