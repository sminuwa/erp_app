-- manufacturing_gl_overhead_fix.sql
-- ============================================================
-- Manufacturing GL Overhead Fix
-- Adds individual cost columns and GL account control entries
-- for manufacturing overhead (labour, power, other costs)
-- ============================================================

-- Add individual cost columns to single_product_manufacturing
ALTER TABLE single_product_manufacturing
ADD COLUMN labor_cost decimal(18,2) DEFAULT '0.00' AFTER total_material_cost,
ADD COLUMN power_cost decimal(18,2) DEFAULT '0.00' AFTER labor_cost,
ADD COLUMN other_cost decimal(18,2) DEFAULT '0.00' AFTER power_cost;

-- Add individual cost columns to batch_productions
ALTER TABLE batch_productions
ADD COLUMN labor_cost decimal(18,2) DEFAULT '0.00' AFTER total_material_cost,
ADD COLUMN power_cost decimal(18,2) DEFAULT '0.00' AFTER labor_cost,
ADD COLUMN other_cost decimal(18,2) DEFAULT '0.00' AFTER power_cost;

-- Add GL account control entries for overhead costs
INSERT INTO general_account_controls (code, general_account_id, created_at, updated_at) VALUES
('manufacturing_wip', 1240, NOW(), NOW()),
('manufacturing_labour', 860, NOW(), NOW()),
('manufacturing_power', 805, NOW(), NOW()),
('manufacturing_other', 600, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    general_account_id = VALUES(general_account_id),
    updated_at = NOW();

-- ============================================================
-- IMPORTANT: After running this SQL, configure each control
-- entry with the correct GL account ID via the admin panel or:
--
-- UPDATE general_account_controls SET general_account_id = 860 WHERE code = 'manufacturing_labour';
-- UPDATE general_account_controls SET general_account_id = 805 WHERE code = 'manufacturing_power';
-- UPDATE general_account_controls SET general_account_id = 600 WHERE code = 'manufacturing_other';
-- ============================================================

-- Rollback:
-- ALTER TABLE single_product_manufacturing DROP COLUMN labor_cost, DROP COLUMN power_cost, DROP COLUMN other_cost;
-- ALTER TABLE batch_productions DROP COLUMN labor_cost, DROP COLUMN power_cost, DROP COLUMN other_cost;
-- DELETE FROM general_account_controls WHERE code IN ('manufacturing_labour', 'manufacturing_power', 'manufacturing_other');



-- new_reports_permission_seeder.sql
-- Add new manufacturing report permissions
INSERT INTO permissions (name, description, guard_name, active, created_at, updated_at) VALUES
('manufacturing.reports.orders', 'View Manufacturing Orders Report', 'web', 1, NOW(), NOW()),
('manufacturing.reports.schedules', 'View Manufacturing Schedules Report', 'web', 1, NOW(), NOW()),
('manufacturing.reports.requisitions', 'View Manufacturing Requisitions Report', 'web', 1, NOW(), NOW());

-- Assign to the same roles that have manufacturing.reports.history
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name IN ('manufacturing.reports.orders', 'manufacturing.reports.schedules', 'manufacturing.reports.requisitions');

-- Also add team_ledger permission if not yet added
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('manufacturing.reports.team_ledger', 'web', NOW(), NOW());

INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name = 'manufacturing.reports.team_ledger';

-- Clear permission cache (run after: php artisan cache:forget spatie.permission.cache)



-- update_manufacturing_additional_cost_status.sql
ALTER TABLE `manufacturing_additional_costs` 
MODIFY COLUMN `status` ENUM('pending', 'posted', 'reversed') 
COLLATE utf8mb4_unicode_ci 
DEFAULT 'pending';