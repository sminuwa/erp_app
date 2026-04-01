-- ============================================================================
-- Manufacturing Module Migrations
-- Run these in order. Each section corresponds to one Laravel migration.
-- Generated from migrations: 2026_03_30_100000 through 2026_03_30_100007
-- ============================================================================

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

ALTER TABLE `production_orders`
    ADD COLUMN `rejected_by` BIGINT UNSIGNED NULL AFTER `closed_at`,
    ADD COLUMN `rejected_at` TIMESTAMP NULL AFTER `rejected_by`,
    ADD COLUMN `rejection_reason` TEXT NULL AFTER `rejected_at`,
    ADD COLUMN `adjusted_by` BIGINT UNSIGNED NULL AFTER `rejection_reason`,
    ADD COLUMN `adjusted_at` TIMESTAMP NULL AFTER `adjusted_by`,
    ADD COLUMN `adjustment_reason` TEXT NULL AFTER `adjusted_at`;

ALTER TABLE `production_orders`
    ADD CONSTRAINT `production_orders_rejected_by_foreign`
        FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `production_orders_adjusted_by_foreign`
        FOREIGN KEY (`adjusted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;


-- ============================================================================
-- 2. Create production_order_checklists table
-- Migration: 2026_03_30_100001_create_production_order_checklists_table
-- ============================================================================

CREATE TABLE `production_order_checklists` (
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
) cat;


-- ============================================================================
-- 3. Update daily_manufacturing_schedules (drop team/machine, add receive)
-- Migration: 2026_03_30_100002_update_daily_manufacturing_schedules
-- ============================================================================

-- Drop foreign keys first
ALTER TABLE `daily_manufacturing_schedules`
    DROP FOREIGN KEY `daily_manufacturing_schedules_team_id_foreign`;

ALTER TABLE `daily_manufacturing_schedules`
    DROP FOREIGN KEY `daily_manufacturing_schedules_machine_id_foreign`;

ALTER TABLE `daily_manufacturing_schedules`
    DROP COLUMN `team_id`,
    DROP COLUMN `machine_id`;

ALTER TABLE `daily_manufacturing_schedules`
    ADD COLUMN `received_by` BIGINT UNSIGNED NULL AFTER `approved_at`,
    ADD COLUMN `received_at` TIMESTAMP NULL AFTER `received_by`;

ALTER TABLE `daily_manufacturing_schedules`
    ADD CONSTRAINT `daily_manufacturing_schedules_received_by_foreign`
        FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;


-- ============================================================================
-- 4. Create manufacturing_work_orders table
-- Migration: 2026_03_30_100003_create_manufacturing_work_orders_table
-- ============================================================================

CREATE TABLE `manufacturing_work_orders` (
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

CREATE TABLE `manufacturing_work_order_items` (
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

ALTER TABLE `materials_requisitions`
    ADD COLUMN `work_order_id` BIGINT UNSIGNED NULL AFTER `schedule_id`,
    ADD COLUMN `verified_by` BIGINT UNSIGNED NULL AFTER `received_at`,
    ADD COLUMN `verified_at` TIMESTAMP NULL AFTER `verified_by`;

ALTER TABLE `materials_requisitions`
    ADD CONSTRAINT `materials_requisitions_work_order_id_foreign`
        FOREIGN KEY (`work_order_id`) REFERENCES `manufacturing_work_orders` (`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `materials_requisitions_verified_by_foreign`
        FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Data migration: promote all ISSUED requisitions to VERIFIED so they can still be received
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
ALTER TABLE `single_product_manufacturing`
    ADD COLUMN `qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`,
    ADD COLUMN `qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`;

ALTER TABLE `single_product_manufacturing`
    ADD CONSTRAINT `single_product_manufacturing_qc_verified_by_foreign`
        FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Batch Productions
ALTER TABLE `batch_productions`
    ADD COLUMN `qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`,
    ADD COLUMN `qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`;

ALTER TABLE `batch_productions`
    ADD CONSTRAINT `batch_productions_qc_verified_by_foreign`
        FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Manufacturing Reworks
ALTER TABLE `manufacturing_reworks`
    ADD COLUMN `qc_verified_by` BIGINT UNSIGNED NULL AFTER `status`,
    ADD COLUMN `qc_verified_at` TIMESTAMP NULL AFTER `qc_verified_by`;

ALTER TABLE `manufacturing_reworks`
    ADD CONSTRAINT `manufacturing_reworks_qc_verified_by_foreign`
        FOREIGN KEY (`qc_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;


-- ============================================================================
-- 8. Add margin fields to manufacturing_boms
-- Migration: 2026_03_30_100007_add_margin_to_manufacturing_boms
-- ============================================================================

ALTER TABLE `manufacturing_boms`
    ADD COLUMN `margin_per_piece` DECIMAL(15,4) NULL AFTER `total_cost_per_unit`,
    ADD COLUMN `margin_gl_account_id` BIGINT UNSIGNED NULL AFTER `margin_per_piece`;

ALTER TABLE `manufacturing_boms`
    ADD CONSTRAINT `manufacturing_boms_margin_gl_account_id_foreign`
        FOREIGN KEY (`margin_gl_account_id`) REFERENCES `general_accounts` (`id`) ON DELETE SET NULL;


-- ============================================================================
-- 9. Insert new permissions
-- ============================================================================

-- Work Orders (new module - all permissions are new)
INSERT INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('manufacturing.work_orders.index',   'View Work Orders List',      'web', 1, NOW(), NOW()),
('manufacturing.work_orders.create',  'Create Work Order',          'web', 1, NOW(), NOW()),
('manufacturing.work_orders.show',    'View Work Order Details',    'web', 1, NOW(), NOW()),
('manufacturing.work_orders.post',    'Post Work Order',            'web', 1, NOW(), NOW()),
('manufacturing.work_orders.delete',  'Delete Work Order',          'web', 1, NOW(), NOW());

-- Production Orders: reject & adjust
INSERT INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('manufacturing.production_orders.reject', 'Reject Production Order', 'web', 1, NOW(), NOW()),
('manufacturing.production_orders.adjust', 'Request Adjustment on Production Order', 'web', 1, NOW(), NOW());

-- QC Verify permissions
INSERT INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
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
-- Record migrations in Laravel's migrations table (optional, keeps artisan in sync)
-- ============================================================================

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_03_30_100000_add_reject_adjust_to_production_orders', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100001_create_production_order_checklists_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100002_update_daily_manufacturing_schedules', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100003_create_manufacturing_work_orders_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100004_create_manufacturing_work_order_items_table', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100005_update_materials_requisitions', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100006_add_qc_verified_to_manufacturing_tables', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m)),
('2026_03_30_100007_add_margin_to_manufacturing_boms', (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM `migrations`) AS m));
