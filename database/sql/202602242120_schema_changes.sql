-- 1. DROP COLUMNS SAFELY
-- This block checks if the column exists before dropping to prevent errors on re-run
DROP PROCEDURE IF EXISTS DropCostsIfExist;
DELIMITER //
CREATE PROCEDURE DropCostsIfExist()
BEGIN
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'batch_production_materials' AND COLUMN_NAME = 'unit_cost' AND TABLE_SCHEMA = DATABASE()) THEN
        ALTER TABLE batch_production_materials DROP COLUMN unit_cost;
    END IF;
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'batch_production_materials' AND COLUMN_NAME = 'total_cost' AND TABLE_SCHEMA = DATABASE()) THEN
        ALTER TABLE batch_production_materials DROP COLUMN total_cost;
    END IF;
END //
DELIMITER ;
CALL DropCostsIfExist();
DROP PROCEDURE DropCostsIfExist;

-- 2. PERMISSIONS (Safe via IGNORE)
INSERT IGNORE INTO permissions (name, description, guard_name, active, created_at, updated_at)
VALUES ('manufacturing.reports.batch_conversion', 'View Batch Conversion Report', 'web', 1, NOW(), NOW());

-- 3. ROLE MAPPING (Safe via INSERT IGNORE and subquery check)
-- Added a NOT EXISTS check to be extra sure we don't duplicate relationships
INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name = 'manufacturing.reports.batch_conversion'
AND NOT EXISTS (
    SELECT 1 FROM role_has_permissions rhp 
    WHERE rhp.permission_id = p.id AND rhp.role_id = rp.role_id
);

-- 4. GL CONTROLS (Safe via ON DUPLICATE KEY)
INSERT INTO general_account_controls (code, general_account_id, created_at, updated_at)
VALUES ('manufacturing_wip', 1239, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    general_account_id = VALUES(general_account_id),
    updated_at = NOW();

-- 5. ADD PACKAGING MATERIAL COLUMNS TO BATCH_CONVERSIONS (Safe via IF NOT EXISTS check)
DROP PROCEDURE IF EXISTS AddMaterialColumns;
DELIMITER //
CREATE PROCEDURE AddMaterialColumns()
BEGIN
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'batch_conversions' AND COLUMN_NAME = 'material_product_id' AND TABLE_SCHEMA = DATABASE()) THEN
        ALTER TABLE batch_conversions
            ADD COLUMN material_product_id BIGINT UNSIGNED DEFAULT NULL AFTER output_store_id,
            ADD COLUMN material_store_id BIGINT UNSIGNED DEFAULT NULL AFTER material_product_id,
            ADD COLUMN material_qty DECIMAL(18,4) DEFAULT 0.0000 AFTER material_store_id,
            ADD COLUMN material_unit_cost DECIMAL(18,2) DEFAULT 0.00 AFTER material_qty,
            ADD COLUMN material_cost DECIMAL(18,2) DEFAULT 0.00 AFTER material_unit_cost;
    END IF;
END //
DELIMITER ;
CALL AddMaterialColumns();
DROP PROCEDURE AddMaterialColumns;