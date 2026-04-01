-- ============================================================================
-- Manufacturing Module Migrations (IDEMPOTENT — safe to re-run)
-- Run these in order. Each section corresponds to one Laravel migration.
-- Generated from migrations: 2026_03_30_100000 through 2026_03_30_100007
-- ============================================================================


-- ============================================================================
-- Helper procedures for idempotent DDL operations
-- ============================================================================

DROP PROCEDURE IF EXISTS drop_fk_if_exists;
DELIMITER //
CREATE PROCEDURE drop_fk_if_exists(IN tbl VARCHAR(255), IN fk VARCHAR(255))
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl
          AND CONSTRAINT_NAME = fk AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ) THEN
        SET @q = CONCAT('ALTER TABLE `', tbl, '` DROP FOREIGN KEY `', fk, '`');
        PREPARE stmt FROM @q; EXECUTE stmt; DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS drop_col_if_exists;
DELIMITER //
CREATE PROCEDURE drop_col_if_exists(IN tbl VARCHAR(255), IN col VARCHAR(255))
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
    ) THEN
        SET @q = CONCAT('ALTER TABLE `', tbl, '` DROP COLUMN `', col, '`');
        PREPARE stmt FROM @q; EXECUTE stmt; DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS add_col_if_not_exists;
DELIMITER //
CREATE PROCEDURE add_col_if_not_exists(IN tbl VARCHAR(255), IN col VARCHAR(255), IN col_def TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = tbl AND COLUMN_NAME = col
    ) THEN
        SET @q = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN ', col_def);
        PREPARE stmt FROM @q; EXECUTE stmt; DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

DROP PROCEDURE IF EXISTS add_fk_if_not_exists;
DELIMITER //
CREATE PROCEDURE add_fk_if_not_exists(IN fk_name VARCHAR(255), IN fk_def TEXT)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_NAME = fk_name
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ) THEN
        SET @q = fk_def;
        PREPARE stmt FROM @q; EXECUTE stmt; DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;


-- ============================================================================
-- 0. Fix ENUM status columns to accept new status values
--    MUST run BEFORE any data changes that use the new statuses
-- ============================================================================

-- production_orders: add 'rejected', 'adjustment_needed'
ALTER TABLE `production_orders`
    MODIFY COLUMN `status` ENUM('pending','confirmed','approved','closed','rejected','adjustment_needed') NOT NULL DEFAULT 'pending';

-- daily_manufacturing_schedules: add 'received'
ALTER TABLE `daily_manufacturing_schedules`
    MODIFY COLUMN `status` ENUM('pending','approved','received') NOT NULL DEFAULT 'pending';

-- materials_requisitions: add 'verified'
ALTER TABLE `materials_requisitions`
    MODIFY COLUMN `status` ENUM('pending','approved','issued','verified','received') NOT NULL DEFAULT 'pending';

-- single_product_manufacturing: add 'qc_verified'
ALTER TABLE `single_product_manufacturing`
    MODIFY COLUMN `status` ENUM('pending','qc_verified','posted') NOT NULL DEFAULT 'pending';

-- batch_productions: add 'qc_verified'
ALTER TABLE `batch_productions`
    MODIFY COLUMN `status` ENUM('pending','qc_verified','posted','fully_converted') NOT NULL DEFAULT 'pending';

-- manufacturing_reworks: add 'qc_verified'
ALTER TABLE `manufacturing_reworks`
    MODIFY COLUMN `status` ENUM('pending','qc_verified','posted') NOT NULL DEFAULT 'pending';


-- ============================================================================
-- 1. Add reject/adjust columns to production_orders
-- Migration: 2026_03_30_100000_add_reject_adjust_to_production_orders
-- ============================================================================

CALL add_col_if_not_exists('production_orders', 'rejected_by',      '`rejected_by` BIGINT UNSIGNED NULL AFTER `closed_at`');
CALL add_col_if_not_exists('production_orders', 'rejected_at',      '`rejected_at` TIMESTAMP NULL AFTER `rejected_by`');
CALL add_col_if_not_exists('production_orders', 'rejection_reason', '`rejection_reason` TEXT NULL AFTER `rejected_at`');
CALL add_col_if_not_exists('production_orders', 'adjusted_by',      '`adjusted_by` BIGINT UNSIGNED NULL AFTER `rejection_reason`');
CALL add_col_if_not_exists('production_orders', 'adjusted_at',      '`adjusted_at` TIMESTAMP NULL AFTER `adjusted_by`');
CALL add_col_if_not_exists('production_orders', 'adjustment_reason','`adjustment_reason` TEXT NULL AFTER `adjusted_at`');

CALL add_fk_if_not_exists('production_orders_rejected_by_foreign',
    'ALTER TABLE `production_orders` ADD CONSTRAINT `production_orders_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
CALL add_fk_if_not_exists('production_orders_adjusted_by_foreign',
    'ALTER TABLE `production_orders` ADD CONSTRAINT `production_orders_adjusted_by_foreign` FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');


-- ============================================================================
-- 2. Create production_order_checklists table
-- Migration: 2026_03_30_100001_create_production_order_checklists_table
-- ============================================================================

CREATE TABLE IF NOT EXISTS `production_order_checklists` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `production_order_id` BIGINT UNSIGNED NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `is_checked` TINYINT(1) NOT NULL DEFAULT 0,
    `checked_by` BIGINT UNSIGNED NULL,
    `checked_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `production_order_checklists_production_order_id_foreign`
        FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `production_order_checklists_checked_by_foreign`
        FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data migration: create checklist rows for ALL existing production orders (pre-checked so they are not blocked)
-- Only inserts for production_orders that don't already have checklist rows
INSERT INTO `production_order_checklists` (`production_order_id`, `category`, `label`, `is_checked`, `checked_by`, `checked_at`, `created_at`, `updated_at`)
SELECT
    po.`id`,
    cat.`category`,
    cat.`label`,
    1,
    po.`created_by`,
    NOW(),
    NOW(),
    NOW()
FROM `production_orders` po
CROSS JOIN (
    SELECT 'machines' AS `category`, 'Machines are available and operational' AS `label`
    UNION ALL
    SELECT 'staffs', 'Required staff are assigned and present'
    UNION ALL
    SELECT 'raw_materials', 'Raw materials are in stock and ready'
    UNION ALL
    SELECT 'factory_condition', 'Factory conditions are safe and ready'
) cat
WHERE NOT EXISTS (
    SELECT 1 FROM `production_order_checklists` poc
    WHERE poc.`production_order_id` = po.`id` AND poc.`category` = cat.`category`
);


-- ============================================================================
-- 3. Update daily_manufacturing_schedules (drop team/machine, add receive)
-- Migration: 2026_03_30_100002_update_daily_manufacturing_schedules
-- ============================================================================

-- Drop foreign keys first (safe if they don't exist)
CALL drop_fk_if_exists('daily_manufacturing_schedules', 'daily_manufacturing_schedules_team_id_foreign');
CALL drop_fk_if_exists('daily_manufacturing_schedules', 'daily_manufacturing_schedules_machine_id_foreign');

-- Drop columns (safe if they don't exist)
CALL drop_col_if_exists('daily_manufacturing_schedules', 'team_id');
CALL drop_col_if_exists('daily_manufacturing_schedules', 'machine_id');

-- Add new columns
CALL add_col_if_not_exists('daily_manufacturing_schedules', 'received_by', '`received_by` BIGINT UNSIGNED NULL AFTER `approved_at`');
CALL add_col_if_not_exists('daily_manufacturing_schedules', 'received_at', '`received_at` TIMESTAMP NULL AFTER `received_by`');

CALL add_fk_if_not_exists('daily_manufacturing_schedules_received_by_foreign',
    'ALTER TABLE `daily_manufacturing_schedules` ADD CONSTRAINT `daily_manufacturing_schedules_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');


-- ============================================================================
-- 4. Create manufacturing_work_orders table
-- Migration: 2026_03_30_100003_create_manufacturing_work_orders_table
-- ============================================================================

CREATE TABLE IF NOT EXISTS `manufacturing_work_orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(255) NOT NULL,
    `work_order_date` DATE NOT NULL,
    `daily_schedule_id` BIGINT UNSIGNED NOT NULL,
    `machine_id` BIGINT UNSIGNED NULL,
    `team_id` BIGINT UNSIGNED NULL,
    `notes` TEXT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `manufacturing_work_orders_reference_unique` (`reference`),
    CONSTRAINT `manufacturing_work_orders_daily_schedule_id_foreign`
        FOREIGN KEY (`daily_schedule_id`) REFERENCES `daily_manufacturing_schedules` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `manufacturing_work_orders_machine_id_foreign`
        FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines` (`id`) ON DELETE SET NULL,
    CONSTRAINT `manufacturing_work_orders_team_id_foreign`
        FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`) ON DELETE SET NULL,
    CONSTRAINT `manufacturing_work_orders_branch_id_foreign`
        FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `manufacturing_work_orders_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `manufacturing_work_orders_posted_by_foreign`
        FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 5. Create manufacturing_work_order_items table
-- Migration: 2026_03_30_100004_create_manufacturing_work_order_items_table
-- ============================================================================

CREATE TABLE IF NOT EXISTS `manufacturing_work_order_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `work_order_id` BIGINT UNSIGNED NOT NULL,
    `schedule_item_id` BIGINT UNSIGNED NOT NULL,
    `planned_qty` DECIMAL(15,4) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `manufacturing_work_order_items_work_order_id_foreign`
        FOREIGN KEY (`work_order_id`) REFERENCES `manufacturing_work_orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `manufacturing_work_order_items_schedule_item_id_foreign`
        FOREIGN KEY (`schedule_item_id`) REFERENCES `daily_manufacturing_schedule_items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- 6. Update materials_requisitions (add work_order_id, verify columns, data migration)
-- Migration: 2026_03_30_100005_update_materials_requisitions
-- ============================================================================

CALL add_col_if_not_exists('materials_requisitions', 'work_order_id', '`work_order_id` BIGINT UNSIGNED NULL AFTER `schedule_id`');
CALL add_col_if_not_exists('materials_requisitions', 'verified_by',   '`verified_by` BIGINT UNSIGNED NULL AFTER `received_at`');
CALL add_col_if_not_exists('materials_requisitions', 'verified_at',   '`verified_at` TIMESTAMP NULL AFTER `verified_by`');

CALL add_fk_if_not_exists('materials_requisitions_work_order_id_foreign',
    'ALTER TABLE `materials_requisitions` ADD CONSTRAINT `materials_requisitions_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `manufacturing_work_orders` (`id`) ON DELETE SET NULL');
CALL add_fk_if_not_exists('materials_requisitions_verified_by_foreign',
    'ALTER TABLE `materials_requisitions` ADD CONSTRAINT `materials_requisitions_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');

-- Data migration: promote all ISSUED requisitions to VERIFIED so they can still be received
-- (idempotent: re-running won't affect already-verified rows)
UPDATE `materials_requisitions`
SET `status` = 'verified',
    `verified_by` = `issued_by`,
    `verified_at` = `issued_at`
WHERE `status` = 'issued';


-- ============================================================================
-- 7. Add QC verified columns to single_product_manufacturing, batch_productions, manufacturing_reworks
-- Migration: 2026_03_30_100006_add_qc_verified_to_manufacturing_tables
-- ============================================================================

-- Single Product Manufacturing
CALL add_col_if_not_exists('single_product_manufacturing', 'qc_verified_by', '`qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`');
CALL add_col_if_not_exists('single_product_manufacturing', 'qc_verified_at', '`qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`');
CALL add_fk_if_not_exists('single_product_manufacturing_qc_verified_by_foreign',
    'ALTER TABLE `single_product_manufacturing` ADD CONSTRAINT `single_product_manufacturing_qc_verified_by_foreign` FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');

-- Batch Productions
CALL add_col_if_not_exists('batch_productions', 'qc_verified_by', '`qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`');
CALL add_col_if_not_exists('batch_productions', 'qc_verified_at', '`qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`');
CALL add_fk_if_not_exists('batch_productions_qc_verified_by_foreign',
    'ALTER TABLE `batch_productions` ADD CONSTRAINT `batch_productions_qc_verified_by_foreign` FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');

-- Manufacturing Reworks
CALL add_col_if_not_exists('manufacturing_reworks', 'qc_verified_by', '`qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`');
CALL add_col_if_not_exists('manufacturing_reworks', 'qc_verified_at', '`qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`');
CALL add_fk_if_not_exists('manufacturing_reworks_qc_verified_by_foreign',
    'ALTER TABLE `manufacturing_reworks` ADD CONSTRAINT `manufacturing_reworks_qc_verified_by_foreign` FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');


-- ============================================================================
-- 8. Add margin fields to manufacturing_boms
-- Migration: 2026_03_30_100007_add_margin_to_manufacturing_boms
-- ============================================================================

CALL add_col_if_not_exists('manufacturing_boms', 'margin_per_piece',      '`margin_per_piece` DECIMAL(15,4) NULL AFTER `total_cost_per_unit`');
CALL add_col_if_not_exists('manufacturing_boms', 'margin_gl_account_id',  '`margin_gl_account_id` BIGINT UNSIGNED NULL AFTER `margin_per_piece`');

CALL add_fk_if_not_exists('manufacturing_boms_margin_gl_account_id_foreign',
    'ALTER TABLE `manufacturing_boms` ADD CONSTRAINT `manufacturing_boms_margin_gl_account_id_foreign` FOREIGN KEY (`margin_gl_account_id`) REFERENCES `general_accounts` (`id`) ON DELETE SET NULL');


-- ============================================================================
-- 9. Insert new permissions
-- ============================================================================

-- Work Orders (new module - all permissions are new)
INSERT IGNORE INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('manufacturing.work_orders.index',   'View Work Orders List',      'web', 1, NOW(), NOW()),
('manufacturing.work_orders.create',  'Create Work Order',          'web', 1, NOW(), NOW()),
('manufacturing.work_orders.show',    'View Work Order Details',    'web', 1, NOW(), NOW()),
('manufacturing.work_orders.post',    'Post Work Order',            'web', 1, NOW(), NOW()),
('manufacturing.work_orders.delete',  'Delete Work Order',          'web', 1, NOW(), NOW());

-- Production Orders: reject & adjust
INSERT IGNORE INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('manufacturing.production_orders.reject', 'Reject Production Order', 'web', 1, NOW(), NOW()),
('manufacturing.production_orders.adjust', 'Request Adjustment on Production Order', 'web', 1, NOW(), NOW());

-- QC Verify permissions
INSERT IGNORE INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('manufacturing.single_manufacturing.qc_verify', 'QC Verify Single Product Manufacturing', 'web', 1, NOW(), NOW()),
('manufacturing.batch_production.qc_verify',     'QC Verify Batch Production',             'web', 1, NOW(), NOW()),
('manufacturing.reworks.qc_verify',              'QC Verify Manufacturing Rework',         'web', 1, NOW(), NOW());

-- ============================================================================
-- 10. Assign new permissions to roles
--     This assigns ALL new permissions to every role that already has
--     'manufacturing.production_orders.confirm' (i.e. existing manufacturing roles).
--     Adjust the WHERE clause if you want to target specific roles instead.
-- ============================================================================

INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.`id`, rp.`role_id`
FROM `permissions` p
CROSS JOIN (
    SELECT DISTINCT `role_id`
    FROM `role_has_permissions`
    WHERE `permission_id` = (
        SELECT `id` FROM `permissions` WHERE `name` = 'manufacturing.production_orders.confirm' LIMIT 1
    )
) rp
WHERE p.`name` IN (
    'manufacturing.work_orders.index',
    'manufacturing.work_orders.create',
    'manufacturing.work_orders.show',
    'manufacturing.work_orders.post',
    'manufacturing.work_orders.delete',
    'manufacturing.production_orders.reject',
    'manufacturing.production_orders.adjust',
    'manufacturing.single_manufacturing.qc_verify',
    'manufacturing.batch_production.qc_verify',
    'manufacturing.reworks.qc_verify'
);


-- ============================================================================
-- Record migrations in Laravel's migrations table
-- (uses NOT EXISTS to skip rows that were already recorded)
-- ============================================================================

INSERT INTO `migrations` (`migration`, `batch`)
SELECT m.`migration`, (SELECT COALESCE(MAX(batch), 0) + 1 FROM `migrations`) AS `batch`
FROM (
    SELECT '2026_03_30_100000_add_reject_adjust_to_production_orders' AS `migration`
    UNION ALL SELECT '2026_03_30_100001_create_production_order_checklists_table'
    UNION ALL SELECT '2026_03_30_100002_update_daily_manufacturing_schedules'
    UNION ALL SELECT '2026_03_30_100003_create_manufacturing_work_orders_table'
    UNION ALL SELECT '2026_03_30_100004_create_manufacturing_work_order_items_table'
    UNION ALL SELECT '2026_03_30_100005_update_materials_requisitions'
    UNION ALL SELECT '2026_03_30_100006_add_qc_verified_to_manufacturing_tables'
    UNION ALL SELECT '2026_03_30_100007_add_margin_to_manufacturing_boms'
) m
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` ex WHERE ex.`migration` = m.`migration`
);


-- ============================================================================
-- Cleanup: remove helper procedures
-- ============================================================================

DROP PROCEDURE IF EXISTS drop_fk_if_exists;
DROP PROCEDURE IF EXISTS drop_col_if_exists;
DROP PROCEDURE IF EXISTS add_col_if_not_exists;
DROP PROCEDURE IF EXISTS add_fk_if_not_exists;
