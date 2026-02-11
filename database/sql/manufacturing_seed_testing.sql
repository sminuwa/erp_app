-- =====================================================
-- MANUFACTURING MODULE SEED DATA FOR TESTING
-- =====================================================
-- Purpose: Covers edge cases for all manufacturing features
-- Environment: Uses existing DB records:
--   Users:      1 (admin), 2 (Bello Karaye Anas), 3 (Zakari Ahmad)
--   Branch:     3 (ABHQ - Head Office)
--   Categories: 8 (Ambient Dairy), 10 (Baby Food)
--   Products:   1,2,3,4,5,6,7,8,9,10 (existing)
--   Stores:     7 (ABMS output), 12 (FBMS source/raw materials)
--   Gen Accts:  1 (used as placeholder for additional costs)
--   Teams:      1 (testing team - existing)
--   Machines:   1 (MCH0001 - existing)
--   Staff:      2 (Nuhu Ibrahim, branch 3 - existing)
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. MANUFACTURING STAFF (branch 3)
-- =====================================================
INSERT INTO `manufacturing_staff`
  (`id`, `employment_id`, `name`, `phone_number`, `address`, `branch_id`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`)
VALUES
  (10, 'EMP-MFG-010', 'Abubakar Musa',      '08012345678', '15 Factory Road, Abuja',      3, 1, 1, NULL, NOW(), NOW()),
  (11, 'EMP-MFG-011', 'Fatima Ibrahim',      '08087654321', '22 Industrial Estate, Abuja', 3, 1, 1, NULL, NOW(), NOW()),
  (12, 'EMP-MFG-012', 'Yusuf Abdullahi',     '08055551234', '7 Production Street, Abuja',  3, 1, 2, NULL, NOW(), NOW()),
  (13, 'EMP-MFG-013', 'Zainab Sule',         '08099887766', '44 Mill Road, Abuja',         3, 0, 2, NULL, NOW(), NOW()) -- inactive staff edge case
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `status` = VALUES(`status`);

-- =====================================================
-- 2. TEAM MEMBERS (link staff to team 1)
-- =====================================================
INSERT INTO `manufacturing_team_members` (`team_id`, `staff_id`, `created_at`)
VALUES
  (1, 2,  NOW()),
  (1, 10, NOW()),
  (1, 11, NOW()),
  (1, 12, NOW())
ON DUPLICATE KEY UPDATE `team_id` = `team_id`;

-- =====================================================
-- 3. MANUFACTURING BOMs
-- =====================================================
-- BOM 10: Single product BOM - Product 6 (TT Enriched Tomato Mix 70gx50) - Category 10
INSERT INTO `manufacturing_boms`
  (`id`, `reference`, `category_id`, `description`, `bom_type`, `finish_product_id`, `output_store_id`,
   `actual_output`, `accepted_excess`, `accepted_shortage`, `main_raw_material_id`,
   `labor_cost`, `power_cost`, `other_cost`,
   `total_material_cost`, `total_other_cost`, `total_cost_per_unit`,
   `branch_id`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`)
VALUES
  -- Single: 100 units output, product 6 as finish product
  (10, 'BOM2602ABHQ0010', 10, 'BOM for TT Enriched Tomato Mix 70gx50 (Single Product)', 'single',
   6, 7, 100.0000, 5.0000, 3.0000, NULL,
   5000.00, 2500.00, 1500.00, 45000.00, 9000.00, 540.00,
   3, 1, 1, NULL, NOW(), NOW()),

  -- Batch: 1 batch produces ~1000 units of product 7 (Tasty T Jollof Mix)
  (11, 'BOM2602ABHQ0011', 10, 'BOM for Tasty T Jollof Mix 70gX50 (Batch Production)', 'batch',
   7, 7, 1.0000, 3.0000, 2.0000, 1, -- main_raw_material = product 1
   8000.00, 4000.00, 2000.00, 60000.00, 14000.00, 74000.00,
   3, 1, 1, NULL, NOW(), NOW()),

  -- Single: product 8 - for return/rework testing - Category 8
  (12, 'BOM2602ABHQ0012', 8, 'BOM for Tasty T Joilof Mix 210g X 24 (Single)', 'single',
   8, 7, 50.0000, 4.0000, 2.0000, NULL,
   3000.00, 1500.00, 750.00, 25000.00, 5250.00, 607.50,
   3, 1, 1, NULL, NOW(), NOW()),

  -- Inactive BOM - edge case: should not appear in active BOM selects
  (13, 'BOM2602ABHQ0013', 10, 'BOM for TT Enriched Tomato Mix 70gx50 - INACTIVE', 'single',
   6, 7, 100.0000, 5.0000, 3.0000, NULL,
   5000.00, 2500.00, 1500.00, 45000.00, 9000.00, 540.00,
   3, 0, 1, NULL, NOW(), NOW()) -- status=0 (inactive)
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`), `status` = VALUES(`status`);

-- =====================================================
-- 4. BOM MATERIALS
-- =====================================================
INSERT INTO `manufacturing_bom_materials`
  (`bom_id`, `product_id`, `quantity`, `source_store_id`, `unit_cost`, `total_cost`, `created_at`, `updated_at`)
VALUES
  -- BOM 10 (single, 100 units) - raw materials: products 1 and 2
  (10, 1, 50.0000,  12, 450.00, 22500.00, NOW(), NOW()),
  (10, 2, 80.0000,  12, 281.25, 22500.00, NOW(), NOW()),

  -- BOM 11 (batch, 1 batch) - raw materials: products 3 and 4
  (11, 3, 100.0000, 12, 350.00, 35000.00, NOW(), NOW()),
  (11, 4, 50.0000,  12, 500.00, 25000.00, NOW(), NOW()),

  -- BOM 12 (single, 50 units) - raw materials: products 5 and 1
  (12, 5, 30.0000,  12, 500.00, 15000.00, NOW(), NOW()),
  (12, 1, 20.0000,  12, 500.00, 10000.00, NOW(), NOW()),

  -- BOM 13 (inactive) - raw materials: products 1 and 2
  (13, 1, 50.0000,  12, 450.00, 22500.00, NOW(), NOW()),
  (13, 2, 80.0000,  12, 281.25, 22500.00, NOW(), NOW());

-- =====================================================
-- 5. MATERIALS REQUISITIONS (received = ready for manufacturing)
-- =====================================================
INSERT INTO `materials_requisitions`
  (`id`, `reference`, `requisition_date`, `bom_id`, `quantity`, `branch_id`, `status`,
   `notes`, `approved_by`, `approved_at`, `issued_by`, `issued_at`, `received_by`, `received_at`,
   `created_by`, `created_at`, `updated_at`)
VALUES
  -- For Single Manufacturing 10 (SPM 10 - to be posted)
  (10, 'MRF2602ABHQ0010', '2026-01-10', 10, 2.0000, 3, 'received',
   'Seed: single manufacturing posted test', 1, '2026-01-10 09:00:00',
   2, '2026-01-10 10:00:00', 1, '2026-01-10 11:00:00', 1, '2026-01-10 08:00:00', '2026-01-10 11:00:00'),

  -- For Single Manufacturing 11 (SPM 11 - pending/deletable)
  (11, 'MRF2602ABHQ0011', '2026-01-15', 10, 1.0000, 3, 'received',
   'Seed: single manufacturing pending test', 1, '2026-01-15 09:00:00',
   2, '2026-01-15 10:00:00', 1, '2026-01-15 11:00:00', 1, '2026-01-15 08:00:00', '2026-01-15 11:00:00'),

  -- For Batch Production 10 (BP 10 - posted, no conversion yet)
  (12, 'MRF2602ABHQ0012', '2026-01-20', 11, 1.0000, 3, 'received',
   'Seed: batch production posted no conversion', 1, '2026-01-20 09:00:00',
   2, '2026-01-20 10:00:00', 1, '2026-01-20 11:00:00', 1, '2026-01-20 08:00:00', '2026-01-20 11:00:00'),

  -- For Batch Production 11 (BP 11 - posted, partially converted)
  (13, 'MRF2602ABHQ0013', '2026-01-25', 11, 1.0000, 3, 'received',
   'Seed: batch production partial conversion', 1, '2026-01-25 09:00:00',
   2, '2026-01-25 10:00:00', 1, '2026-01-25 11:00:00', 1, '2026-01-25 08:00:00', '2026-01-25 11:00:00'),

  -- For Single Manufacturing 12 (SPM 12 - posted, for return/rework)
  (14, 'MRF2602ABHQ0014', '2026-02-01', 12, 4.0000, 3, 'received',
   'Seed: single manufacturing for return and rework test', 1, '2026-02-01 09:00:00',
   2, '2026-02-01 10:00:00', 1, '2026-02-01 11:00:00', 1, '2026-02-01 08:00:00', '2026-02-01 11:00:00'),

  -- For Batch Production 12 (BP 12 - pending/deletable)
  (15, 'MRF2602ABHQ0015', '2026-02-08', 11, 1.0000, 3, 'received',
   'Seed: pending batch production deletable test', 1, '2026-02-08 09:00:00',
   2, '2026-02-08 10:00:00', 1, '2026-02-08 11:00:00', 1, '2026-02-08 08:00:00', '2026-02-08 11:00:00'),

  -- For Single Manufacturing 13 (SPM 13 - posted, January, different category for report filter test)
  (16, 'MRF2602ABHQ0016', '2026-01-05', 12, 2.0000, 3, 'received',
   'Seed: early January manufacturing for date range report test', 1, '2026-01-05 09:00:00',
   2, '2026-01-05 10:00:00', 1, '2026-01-05 11:00:00', 1, '2026-01-05 08:00:00', '2026-01-05 11:00:00')
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);

-- =====================================================
-- 6. REQUISITION ITEMS
-- =====================================================
INSERT INTO `materials_requisition_items`
  (`requisition_id`, `product_id`, `source_store_id`, `required_qty`, `issued_qty`, `received_qty`, `unit_cost`, `total_cost`, `created_at`, `updated_at`)
VALUES
  -- REQ 10 (BOM 10 x2 = 2 units)
  (10, 1, 12, 100.0000, 100.0000, 100.0000, 450.00, 45000.00, NOW(), NOW()),
  (10, 2, 12, 160.0000, 160.0000, 160.0000, 281.25, 45000.00, NOW(), NOW()),

  -- REQ 11 (BOM 10 x1)
  (11, 1, 12, 50.0000,  50.0000,  50.0000,  450.00, 22500.00, NOW(), NOW()),
  (11, 2, 12, 80.0000,  80.0000,  80.0000,  281.25, 22500.00, NOW(), NOW()),

  -- REQ 12 (BOM 11 x1 batch)
  (12, 3, 12, 100.0000, 100.0000, 100.0000, 350.00, 35000.00, NOW(), NOW()),
  (12, 4, 12,  50.0000,  50.0000,  50.0000, 500.00, 25000.00, NOW(), NOW()),

  -- REQ 13 (BOM 11 x1 batch)
  (13, 3, 12, 100.0000, 100.0000, 100.0000, 350.00, 35000.00, NOW(), NOW()),
  (13, 4, 12,  50.0000,  50.0000,  50.0000, 500.00, 25000.00, NOW(), NOW()),

  -- REQ 14 (BOM 12 x4)
  (14, 5, 12, 120.0000, 120.0000, 120.0000, 500.00, 60000.00, NOW(), NOW()),
  (14, 1, 12,  80.0000,  80.0000,  80.0000, 500.00, 40000.00, NOW(), NOW()),

  -- REQ 15 (BOM 11 x1 batch)
  (15, 3, 12, 100.0000, 100.0000, 100.0000, 350.00, 35000.00, NOW(), NOW()),
  (15, 4, 12,  50.0000,  50.0000,  50.0000, 500.00, 25000.00, NOW(), NOW()),

  -- REQ 16 (BOM 12 x2)
  (16, 5, 12,  60.0000,  60.0000,  60.0000, 500.00, 30000.00, NOW(), NOW()),
  (16, 1, 12,  40.0000,  40.0000,  40.0000, 500.00, 20000.00, NOW(), NOW());

-- =====================================================
-- 7. SINGLE PRODUCT MANUFACTURING
-- =====================================================
INSERT INTO `single_product_manufacturing`
  (`id`, `reference`, `manufacturing_date`, `requisition_id`, `batch_number`, `team_id`, `machine_id`,
   `quantity`, `bom_id`, `finish_product_id`, `output_store_id`,
   `total_material_cost`, `total_other_cost`, `total_cost`, `unit_cost`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`, `notes`)
VALUES
  -- SPM 10: POSTED Jan - Category 10 (Baby Food) - for returns, rework, additional costs, REPORTS
  (10, 'SPM2602ABHQ0010', '2026-01-12', 10, 'BATCH20260112001', 1, 1,
   200.0000, 10, 6, 7,
   90000.00, 9000.00, 99000.00, 495.00,
   3, 'posted', 1, '2026-01-12 14:00:00', 1, '2026-01-12 08:00:00', '2026-01-12 14:00:00',
   'Seed: posted single - tests returns, reworks, costs'),

  -- SPM 11: PENDING Jan - for testing delete workflow
  (11, 'SPM2602ABHQ0011', '2026-01-17', 11, 'BATCH20260117001', 1, 1,
   100.0000, 10, 6, 7,
   45000.00, 9000.00, 54000.00, 540.00,
   3, 'pending', NULL, NULL, 2, '2026-01-17 08:00:00', '2026-01-17 08:00:00',
   'Seed: pending single - can be deleted'),

  -- SPM 12: POSTED Feb - Category 8 (Ambient Dairy) - for returns, rework, reports
  (12, 'SPM2602ABHQ0012', '2026-02-03', 14, 'BATCH20260203001', 1, NULL,
   200.0000, 12, 8, 7,
   100000.00, 5250.00, 105250.00, 526.25,
   3, 'posted', 1, '2026-02-03 15:00:00', 1, '2026-02-03 08:00:00', '2026-02-03 15:00:00',
   'Seed: posted single Feb - tests cross-category report filtering'),

  -- SPM 13: POSTED early Jan - different date for date range testing
  (13, 'SPM2602ABHQ0013', '2026-01-06', 16, 'BATCH20260106001', 1, NULL,
   100.0000, 12, 8, 7,
   50000.00, 5250.00, 55250.00, 552.50,
   3, 'posted', 1, '2026-01-06 14:00:00', 1, '2026-01-06 08:00:00', '2026-01-06 14:00:00',
   'Seed: posted single early Jan - tests date range report filtering')
ON DUPLICATE KEY UPDATE `notes` = VALUES(`notes`);

-- =====================================================
-- 8. SINGLE PRODUCT MANUFACTURING MATERIALS
-- =====================================================
INSERT INTO `single_product_manufacturing_materials`
  (`manufacturing_id`, `product_id`, `store_id`, `quantity`, `unit_cost`, `total_cost`, `created_at`)
VALUES
  -- SPM 10 materials (BOM 10 x2 units)
  (10, 1, 12, 100.0000, 450.00,  45000.00, NOW()),
  (10, 2, 12, 160.0000, 281.25,  45000.00, NOW()),

  -- SPM 12 materials (BOM 12 x4 units)
  (12, 5, 12, 120.0000, 500.00,  60000.00, NOW()),
  (12, 1, 12,  80.0000, 500.00,  40000.00, NOW()),

  -- SPM 13 materials (BOM 12 x2 units)
  (13, 5, 12,  60.0000, 500.00,  30000.00, NOW()),
  (13, 1, 12,  40.0000, 500.00,  20000.00, NOW());

-- =====================================================
-- 9. BATCH PRODUCTIONS
-- =====================================================
INSERT INTO `batch_productions`
  (`id`, `reference`, `production_date`, `requisition_id`, `batch_number`, `team_id`, `machine_id`,
   `quantity`, `bom_id`, `total_material_cost`, `wip_value`, `converted_qty`, `remaining_qty`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`, `notes`)
VALUES
  -- BP 10: POSTED Jan - no conversions yet - for pending conversion test
  (10, 'BPC2602ABHQ0010', '2026-01-22', 12, 'BATCH20260122001', 1, 1,
   1.0000, 11, 60000.00, 74000.00, 0.0000, 1.0000,
   3, 'posted', 1, '2026-01-22 14:00:00', 1, '2026-01-22 08:00:00', '2026-01-22 14:00:00',
   'Seed: posted batch - awaiting conversion'),

  -- BP 11: POSTED Jan - partially converted (500 units out of ~1000)
  (11, 'BPC2602ABHQ0011', '2026-01-28', 13, 'BATCH20260128001', 1, 1,
   1.0000, 11, 60000.00, 74000.00, 500.0000, 0.5000,
   3, 'posted', 1, '2026-01-28 14:00:00', 1, '2026-01-28 08:00:00', '2026-01-28 16:00:00',
   'Seed: posted batch - partially converted edge case'),

  -- BP 12: PENDING Feb - for testing delete workflow
  (12, 'BPC2602ABHQ0012', '2026-02-09', 15, 'BATCH20260209001', 1, NULL,
   1.0000, 11, 60000.00, 74000.00, 0.0000, 1.0000,
   3, 'pending', NULL, NULL, 2, '2026-02-09 08:00:00', '2026-02-09 08:00:00',
   'Seed: pending batch - can be deleted')
ON DUPLICATE KEY UPDATE `notes` = VALUES(`notes`);

-- =====================================================
-- 10. BATCH PRODUCTION MATERIALS
-- =====================================================
INSERT INTO `batch_production_materials`
  (`batch_production_id`, `product_id`, `store_id`, `quantity`, `unit_cost`, `total_cost`, `created_at`)
VALUES
  -- BP 10 materials (BOM 11 x1 batch)
  (10, 3, 12, 100.0000, 350.00, 35000.00, NOW()),
  (10, 4, 12,  50.0000, 500.00, 25000.00, NOW()),

  -- BP 11 materials (BOM 11 x1 batch)
  (11, 3, 12, 100.0000, 350.00, 35000.00, NOW()),
  (11, 4, 12,  50.0000, 500.00, 25000.00, NOW()),

  -- BP 12 materials (BOM 11 x1 batch)
  (12, 3, 12, 100.0000, 350.00, 35000.00, NOW()),
  (12, 4, 12,  50.0000, 500.00, 25000.00, NOW());

-- =====================================================
-- 11. BATCH CONVERSIONS
-- =====================================================
INSERT INTO `batch_conversions`
  (`id`, `reference`, `conversion_date`, `batch_production_id`, `produced_qty`, `finish_product_id`, `output_store_id`,
   `wip_cost_deducted`, `labor_cost`, `power_cost`, `other_cost`, `total_cost`, `unit_cost`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`)
VALUES
  -- BC 10: POSTED - partial conversion from BP 11 (500 units = half batch)
  (10, 'BCN2602ABHQ0010', '2026-01-30', 11,
   500.0000, 7, 7, 37000.00, 4000.00, 2000.00, 1000.00, 44000.00, 88.00,
   3, 'posted', 1, '2026-01-30 14:00:00', 1, '2026-01-30 08:00:00', '2026-01-30 14:00:00'),

  -- BC 11: PENDING - conversion from BP 10 (pending = not yet posted)
  (11, 'BCN2602ABHQ0011', '2026-02-05', 10,
   800.0000, 7, 7, 59200.00, 4000.00, 2000.00, 1000.00, 66200.00, 82.75,
   3, 'pending', NULL, NULL, 1, '2026-02-05 08:00:00', '2026-02-05 08:00:00'),

  -- BC 12: POSTED - full conversion from new batch production (ID 11, second conversion)
  -- This shows a batch production with multiple conversions
  (12, 'BCN2602ABHQ0012', '2026-02-07', 11,
   500.0000, 7, 7, 37000.00, 4000.00, 2000.00, 1000.00, 44000.00, 88.00,
   3, 'posted', 1, '2026-02-07 14:00:00', 2, '2026-02-07 08:00:00', '2026-02-07 14:00:00')
ON DUPLICATE KEY UPDATE `reference` = VALUES(`reference`);

-- =====================================================
-- 12. MANUFACTURING ADDITIONAL COSTS
-- =====================================================
-- NOTE: account_id=1 is used as placeholder (any valid general_account id)
INSERT INTO `manufacturing_additional_costs`
  (`id`, `reference`, `cost_date`, `production_type`, `production_id`, `account_id`, `amount`, `description`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`)
VALUES
  -- PENDING cost on Single Manufacturing 10
  (10, 'MAC2602ABHQ0010', '2026-01-13', 'single_product', 10, 1,
   5000.00, 'Extra labor cost for overtime production run',
   3, 'pending', NULL, NULL, 1, '2026-01-13 09:00:00', '2026-01-13 09:00:00'),

  -- POSTED cost on Single Manufacturing 12
  (11, 'MAC2602ABHQ0011', '2026-02-04', 'single_product', 12, 1,
   3500.00, 'Equipment maintenance cost during production',
   3, 'posted', 1, '2026-02-04 14:00:00', 1, '2026-02-04 09:00:00', '2026-02-04 14:00:00'),

  -- PENDING cost on Batch Conversion 10 (production_type = batch_conversion)
  (12, 'MAC2602ABHQ0012', '2026-02-01', 'batch_conversion', 10, 1,
   8000.00, 'Additional packaging cost for batch conversion',
   3, 'pending', NULL, NULL, 2, '2026-02-01 10:00:00', '2026-02-01 10:00:00'),

  -- POSTED cost on Batch Conversion 10
  (13, 'MAC2602ABHQ0013', '2026-02-02', 'batch_conversion', 10, 1,
   2500.00, 'Quality control testing cost',
   3, 'posted', 1, '2026-02-02 12:00:00', 1, '2026-02-02 10:00:00', '2026-02-02 12:00:00')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- =====================================================
-- 13. MANUFACTURING PENALTIES
-- =====================================================
INSERT INTO `manufacturing_penalties`
  (`id`, `reference`, `penalty_date`, `penalty_type`, `team_id`, `staff_id`, `description`,
   `amount_charged`, `total_loss_incurred`, `production_reference`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`)
VALUES
  -- PENDING team penalty - linked to a production reference
  (10, 'PEN2602ABHQ0010', '2026-01-20', 'team', 1, NULL,
   'Production target missed - 20% below target for January week 3',
   15000.00, 25000.00, 'SPM2602ABHQ0010',
   3, 'pending', NULL, NULL, 1, '2026-01-20 09:00:00', '2026-01-20 09:00:00'),

  -- POSTED staff penalty - equipment damage
  (11, 'PEN2602ABHQ0011', '2026-01-25', 'staff', NULL, 2,
   'Equipment damage due to careless handling of production machine',
   5000.00, 12000.00, NULL,
   3, 'posted', 1, '2026-01-25 14:00:00', 1, '2026-01-25 09:00:00', '2026-01-25 14:00:00'),

  -- PENDING staff penalty - raw material wastage
  (12, 'PEN2602ABHQ0012', '2026-02-02', 'staff', NULL, 10,
   'Raw material wastage beyond acceptable shortage threshold',
   3000.00, 8000.00, 'BPC2602ABHQ0010',
   3, 'pending', NULL, NULL, 2, '2026-02-02 10:00:00', '2026-02-02 10:00:00'),

  -- PENDING team penalty - no production reference (standalone)
  (13, 'PEN2602ABHQ0013', '2026-02-08', 'team', 1, NULL,
   'Batch production contamination - entire batch scrapped',
   50000.00, 74000.00, 'BPC2602ABHQ0011',
   3, 'pending', NULL, NULL, 1, '2026-02-08 10:00:00', '2026-02-08 10:00:00')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- =====================================================
-- 14. MANUFACTURING RETURNS
-- =====================================================
INSERT INTO `manufacturing_returns`
  (`id`, `reference`, `return_date`, `production_type`, `production_id`, `reason`, `return_qty`,
   `total_cost_returned`, `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`)
VALUES
  -- PENDING return - partial return from SPM 10 (Single)
  (10, 'MRT2602ABHQ0010', '2026-01-14', 'single_product', 10,
   'Customer returned defective batch - wrong concentration ratio',
   20.0000, 9900.00,
   3, 'pending', NULL, NULL, 1, '2026-01-14 09:00:00', '2026-01-14 09:00:00'),

  -- POSTED return - small return from SPM 12 (Single)
  (11, 'MRT2602ABHQ0011', '2026-02-05', 'single_product', 12,
   'Finished goods failed quality inspection - packaging defects',
   10.0000, 5262.50,
   3, 'posted', 1, '2026-02-05 12:00:00', 1, '2026-02-05 09:00:00', '2026-02-05 12:00:00'),

  -- PENDING return - from Batch Conversion 10 (Batch)
  (12, 'MRT2602ABHQ0012', '2026-02-01', 'batch_conversion', 10,
   'Batch returned from retail outlet - products expired before sale',
   50.0000, 4400.00,
   3, 'pending', NULL, NULL, 2, '2026-02-01 11:00:00', '2026-02-01 11:00:00'),

  -- PENDING return - second return from SPM 10 (tests multiple returns)
  (13, 'MRT2602ABHQ0013', '2026-01-16', 'single_product', 10,
   'Second partial return - damaged during warehouse storage',
   5.0000, 2475.00,
   3, 'pending', NULL, NULL, 1, '2026-01-16 11:00:00', '2026-01-16 11:00:00')
ON DUPLICATE KEY UPDATE `reason` = VALUES(`reason`);

-- =====================================================
-- 15. RETURN MATERIALS (raw material credits)
-- =====================================================
INSERT INTO `manufacturing_return_materials`
  (`return_id`, `product_id`, `store_id`, `quantity`, `unit_cost`, `total_cost`, `created_at`)
VALUES
  -- Return 10 raw material credits (from SPM 10 BOM 10)
  (10, 1, 12, 10.0000, 450.00,  4500.00, NOW()),
  (10, 2, 12, 16.0000, 281.25,  4500.00, NOW()),

  -- Return 11 raw material credits (from SPM 12 BOM 12)
  (11, 5, 12,  6.0000, 500.00,  3000.00, NOW()),
  (11, 1, 12,  4.0000, 500.00,  2000.00, NOW()),

  -- Return 12 raw material credits (from BC 10)
  (12, 3, 12, 20.0000, 350.00,  7000.00, NOW()),
  (12, 4, 12, 10.0000, 500.00,  5000.00, NOW()),

  -- Return 13 raw material credits (second partial from SPM 10)
  (13, 1, 12,  2.5000, 450.00,  1125.00, NOW()),
  (13, 2, 12,  4.0000, 281.25,  1125.00, NOW());

-- =====================================================
-- 16. MANUFACTURING REWORKS
-- =====================================================
INSERT INTO `manufacturing_reworks`
  (`id`, `reference`, `rework_date`, `production_type`, `production_id`,
   `labor_cost`, `power_cost`, `other_cost`, `total_material_cost`, `total_cost`,
   `branch_id`, `status`, `posted_by`, `posted_at`, `created_by`, `created_at`, `updated_at`)
VALUES
  -- PENDING rework on SPM 10 (Single)
  (10, 'MRW2602ABHQ0010', '2026-01-15', 'single_product', 10,
   3000.00, 1500.00, 500.00, 12000.00, 17000.00,
   3, 'pending', NULL, NULL, 1, '2026-01-15 09:00:00', '2026-01-15 09:00:00'),

  -- POSTED rework on SPM 12 (Single)
  (11, 'MRW2602ABHQ0011', '2026-02-06', 'single_product', 12,
   2000.00, 1000.00, 500.00, 8000.00, 11500.00,
   3, 'posted', 1, '2026-02-06 15:00:00', 1, '2026-02-06 09:00:00', '2026-02-06 15:00:00'),

  -- PENDING rework on Batch Conversion 10 (Batch)
  (12, 'MRW2602ABHQ0012', '2026-02-02', 'batch_conversion', 10,
   4000.00, 2000.00, 1000.00, 18000.00, 25000.00,
   3, 'pending', NULL, NULL, 2, '2026-02-02 09:00:00', '2026-02-02 09:00:00')
ON DUPLICATE KEY UPDATE `rework_date` = VALUES(`rework_date`);

-- =====================================================
-- 17. REWORK MATERIALS (additional raw materials consumed)
-- =====================================================
INSERT INTO `manufacturing_rework_materials`
  (`rework_id`, `product_id`, `store_id`, `quantity`, `unit_cost`, `total_cost`, `created_at`)
VALUES
  -- Rework 10 materials (re-processing SPM 10 BOM 10)
  (10, 1, 12, 20.0000, 450.00, 9000.00, NOW()),
  (10, 2, 12, 10.0000, 300.00, 3000.00, NOW()),

  -- Rework 11 materials (re-processing SPM 12 BOM 12)
  (11, 5, 12, 10.0000, 500.00, 5000.00, NOW()),
  (11, 1, 12,  6.0000, 500.00, 3000.00, NOW()),

  -- Rework 12 materials (re-processing Batch Conversion 10)
  (12, 3, 12, 30.0000, 350.00, 10500.00, NOW()),
  (12, 4, 12, 15.0000, 500.00,  7500.00, NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- SUMMARY OF SEED DATA
-- =====================================================
-- BOMs:
--   10 (single, product 6, cat 10, active)
--   11 (batch,  product 7, cat 10, active)
--   12 (single, product 8, cat  8, active)
--   13 (single, product 6, cat 10, INACTIVE - edge case)
--
-- Single Product Manufacturing:
--   SPM 10 - POSTED Jan 12 - 200 units - BOM 10 (cat 10)
--   SPM 11 - PENDING Jan 17 - 100 units - BOM 10 (deletable)
--   SPM 12 - POSTED Feb  3 - 200 units - BOM 12 (cat 8)
--   SPM 13 - POSTED Jan  6 -  100 units - BOM 12 (cat 8, early Jan for date range test)
--
-- Batch Productions:
--   BP 10 - POSTED Jan 22 - no conversions yet
--   BP 11 - POSTED Jan 28 - partially converted (BC 10 + BC 12)
--   BP 12 - PENDING Feb 9 - (deletable)
--
-- Batch Conversions:
--   BC 10 - POSTED Jan 30 - 500 units from BP 11
--   BC 11 - PENDING Feb  5 - 800 units from BP 10 (not yet posted)
--   BC 12 - POSTED Feb  7 - 500 units from BP 11 (second conversion, fully converts BP 11)
--
-- Additional Costs:
--   AC 10 - PENDING on SPM 10
--   AC 11 - POSTED  on SPM 12
--   AC 12 - PENDING on BC 10
--   AC 13 - POSTED  on BC 10
--
-- Penalties:
--   PEN 10 - PENDING team (team 1, ref SPM)
--   PEN 11 - POSTED  staff (staff 2)
--   PEN 12 - PENDING staff (staff 10, ref BP)
--   PEN 13 - PENDING team  (team 1, ref BP)
--
-- Returns:
--   MRT 10 - PENDING - SPM 10 (partial return, 20 units)
--   MRT 11 - POSTED  - SPM 12 (10 units returned)
--   MRT 12 - PENDING - BC 10  (50 units returned)
--   MRT 13 - PENDING - SPM 10 (second partial return, 5 units - tests multiple returns)
--
-- Reworks:
--   MRW 10 - PENDING - SPM 10
--   MRW 11 - POSTED  - SPM 12
--   MRW 12 - PENDING - BC 10
--
-- EDGE CASES COVERED:
--   1. Pending records (deletable)
--   2. Posted records (non-deletable)
--   3. Inactive BOM (should not show in dropdowns)
--   4. Partially converted batch production
--   5. Multiple returns on same production (SPM 10 has MRT 10 + MRT 13)
--   6. Both team and staff penalties
--   7. Additional costs on both single and batch
--   8. Reworks on both single and batch
--   9. Date range filtering (Jan 6 through Feb 9)
--  10. Category filtering (cat 8 vs cat 10)
--  11. Batch production with no conversions (BP 10)
--  12. Multiple conversions on same batch (BP 11 has BC 10 + BC 12)
-- =====================================================
