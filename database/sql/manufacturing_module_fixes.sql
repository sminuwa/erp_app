-- Manufacturing Module Database Fixes
-- Run this SQL script to apply all database-level fixes
-- Date: 2026-02-05

-- ===========================================
-- Fix 1: Update remaining_qty default value
-- ===========================================
ALTER TABLE `batch_productions`
MODIFY COLUMN `remaining_qty` DECIMAL(18,4) DEFAULT 1.0000 COMMENT 'Qty remaining in WIP';

-- ===========================================
-- Fix 2: Add FK constraints on updated_by fields
-- ===========================================

-- Manufacturing machines
ALTER TABLE `manufacturing_machines`
ADD CONSTRAINT `fk_manufacturing_machines_updated_by`
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Manufacturing staff
ALTER TABLE `manufacturing_staff`
ADD CONSTRAINT `fk_manufacturing_staff_updated_by`
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Manufacturing teams
ALTER TABLE `manufacturing_teams`
ADD CONSTRAINT `fk_manufacturing_teams_updated_by`
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Manufacturing BOMs
ALTER TABLE `manufacturing_boms`
ADD CONSTRAINT `fk_manufacturing_boms_updated_by`
FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- ===========================================
-- Fix 3: Add unique constraints to junction tables
-- ===========================================

-- Remove any existing duplicates in team members
DELETE t1 FROM manufacturing_team_members t1
INNER JOIN manufacturing_team_members t2
WHERE t1.id > t2.id
  AND t1.team_id = t2.team_id
  AND t1.staff_id = t2.staff_id;

-- Remove any existing duplicates in team supervisors
DELETE t1 FROM manufacturing_team_supervisors t1
INNER JOIN manufacturing_team_supervisors t2
WHERE t1.id > t2.id
  AND t1.team_id = t2.team_id
  AND t1.employee_id = t2.employee_id;

-- Add unique constraints
ALTER TABLE `manufacturing_team_members`
ADD UNIQUE KEY `uk_team_members_team_staff` (`team_id`, `staff_id`);

ALTER TABLE `manufacturing_team_supervisors`
ADD UNIQUE KEY `uk_team_supervisors_team_employee` (`team_id`, `employee_id`);

-- ===========================================
-- Fix 4: Add performance indexes
-- ===========================================

-- Composite index for inventory reservations
ALTER TABLE `inventory_reservations`
ADD INDEX `idx_reservations_product_store_status` (`product_id`, `store_id`, `status`);

-- Index on category_id for manufacturing_boms
ALTER TABLE `manufacturing_boms`
ADD INDEX `idx_manufacturing_boms_category` (`category_id`),
ADD INDEX `idx_manufacturing_boms_main_raw` (`main_raw_material_id`);

-- Composite index for batch production queries
ALTER TABLE `batch_productions`
ADD INDEX `idx_batch_productions_branch_status` (`branch_id`, `status`);

-- Composite index for materials requisition queries
ALTER TABLE `materials_requisitions`
ADD INDEX `idx_requisitions_branch_status` (`branch_id`, `status`);

-- Polymorphic lookup indexes
ALTER TABLE `manufacturing_additional_costs`
ADD INDEX `idx_additional_costs_poly` (`production_type`, `production_id`);

ALTER TABLE `manufacturing_returns`
ADD INDEX `idx_returns_poly` (`production_type`, `production_id`);

ALTER TABLE `manufacturing_reworks`
ADD INDEX `idx_reworks_poly` (`production_type`, `production_id`);

-- ===========================================
-- VERIFICATION QUERIES
-- ===========================================

-- Verify FK constraints were added
SELECT
    TABLE_NAME,
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND COLUMN_NAME = 'updated_by'
  AND REFERENCED_TABLE_NAME = 'users';

-- Verify unique constraints were added
SHOW INDEXES FROM manufacturing_team_members WHERE Key_name = 'uk_team_members_team_staff';
SHOW INDEXES FROM manufacturing_team_supervisors WHERE Key_name = 'uk_team_supervisors_team_employee';

-- Verify remaining_qty default value
SHOW COLUMNS FROM batch_productions LIKE 'remaining_qty';

-- Verify performance indexes
SHOW INDEXES FROM batch_productions WHERE Key_name = 'idx_batch_productions_branch_status';
SHOW INDEXES FROM materials_requisitions WHERE Key_name = 'idx_requisitions_branch_status';
SHOW INDEXES FROM inventory_reservations WHERE Key_name = 'idx_reservations_product_store_status';

-- ===========================================
-- ROLLBACK SCRIPT (if needed)
-- ===========================================

-- To rollback these changes, run:
/*
-- Remove FK constraints
ALTER TABLE `manufacturing_machines` DROP FOREIGN KEY `fk_manufacturing_machines_updated_by`;
ALTER TABLE `manufacturing_staff` DROP FOREIGN KEY `fk_manufacturing_staff_updated_by`;
ALTER TABLE `manufacturing_teams` DROP FOREIGN KEY `fk_manufacturing_teams_updated_by`;
ALTER TABLE `manufacturing_boms` DROP FOREIGN KEY `fk_manufacturing_boms_updated_by`;

-- Remove unique constraints
ALTER TABLE `manufacturing_team_members` DROP INDEX `uk_team_members_team_staff`;
ALTER TABLE `manufacturing_team_supervisors` DROP INDEX `uk_team_supervisors_team_employee`;

-- Remove indexes
ALTER TABLE `inventory_reservations` DROP INDEX `idx_reservations_product_store_status`;
ALTER TABLE `manufacturing_boms` DROP INDEX `idx_manufacturing_boms_category`;
ALTER TABLE `manufacturing_boms` DROP INDEX `idx_manufacturing_boms_main_raw`;
ALTER TABLE `batch_productions` DROP INDEX `idx_batch_productions_branch_status`;
ALTER TABLE `materials_requisitions` DROP INDEX `idx_requisitions_branch_status`;
ALTER TABLE `manufacturing_additional_costs` DROP INDEX `idx_additional_costs_poly`;
ALTER TABLE `manufacturing_returns` DROP INDEX `idx_returns_poly`;
ALTER TABLE `manufacturing_reworks` DROP INDEX `idx_reworks_poly`;

-- Revert remaining_qty default
ALTER TABLE `batch_productions`
MODIFY COLUMN `remaining_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Qty remaining in WIP';
*/
