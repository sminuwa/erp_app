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
