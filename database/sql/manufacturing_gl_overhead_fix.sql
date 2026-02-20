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
('manufacturing_labour', NULL, NOW(), NOW()),
('manufacturing_power', NULL, NOW(), NOW()),
('manufacturing_other', NULL, NOW(), NOW());

-- ============================================================
-- IMPORTANT: After running this SQL, configure each control
-- entry with the correct GL account ID via the admin panel or:
--
-- UPDATE general_account_controls SET general_account_id = <LABOUR_GL_ID> WHERE code = 'manufacturing_labour';
-- UPDATE general_account_controls SET general_account_id = <POWER_GL_ID> WHERE code = 'manufacturing_power';
-- UPDATE general_account_controls SET general_account_id = <OTHER_GL_ID> WHERE code = 'manufacturing_other';
-- ============================================================

-- Rollback:
-- ALTER TABLE single_product_manufacturing DROP COLUMN labor_cost, DROP COLUMN power_cost, DROP COLUMN other_cost;
-- ALTER TABLE batch_productions DROP COLUMN labor_cost, DROP COLUMN power_cost, DROP COLUMN other_cost;
-- DELETE FROM general_account_controls WHERE code IN ('manufacturing_labour', 'manufacturing_power', 'manufacturing_other');
