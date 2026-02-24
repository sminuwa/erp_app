-- manufacturing_batch_materials_cost_removal.sql
-- ============================================================
-- Remove unit_cost and total_cost from batch_production_materials
--
-- Purpose: BOM materials should store quantities only.
-- Costs are now fetched LIVE from branch_product_prices at:
--   - Display time (show/print views)
--   - Posting time (GL entries and stock card deductions)
-- Aggregate costs (total_material_cost, wip_value) on batch_productions
-- are recomputed and FROZEN at batch posting time.
-- ============================================================

ALTER TABLE batch_production_materials
    DROP COLUMN unit_cost,
    DROP COLUMN total_cost;

-- ============================================================
-- Rollback (if needed):
-- ALTER TABLE batch_production_materials
--     ADD COLUMN unit_cost decimal(18,2) NOT NULL DEFAULT '0.00' AFTER quantity,
--     ADD COLUMN total_cost decimal(18,2) NOT NULL DEFAULT '0.00' AFTER unit_cost;
-- ============================================================


-- Add batch conversion report permission
INSERT IGNORE INTO permissions (name, description, guard_name, active, created_at, updated_at)
VALUES ('manufacturing.reports.batch_conversion', 'View Batch Conversion Report', 'web', 1, NOW(), NOW());

INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name = 'manufacturing.reports.batch_conversion';

-- Clear permission cache (run after: php artisan cache:forget spatie.permission.cache)


-- Add GL account control entries for overhead costs
INSERT INTO general_account_controls (code, general_account_id, created_at, updated_at) VALUES
('manufacturing_wip', 1239, NOW(), NOW()), --1239 demosite id
ON DUPLICATE KEY UPDATE 
    general_account_id = VALUES(general_account_id),
    updated_at = NOW();