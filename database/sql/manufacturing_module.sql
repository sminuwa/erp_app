-- =====================================================
-- MANUFACTURING MODULE DATABASE SCHEMA
-- ERP Application - Self-contained SQL Installation
-- =====================================================
-- Run this file to create all manufacturing tables
-- Ensure the database connection is active before running
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- SECTION 1: SETUP TABLES
-- =====================================================

-- Table: manufacturing_machines (Pot/Machine Details)
CREATE TABLE IF NOT EXISTS `manufacturing_machines` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `capacity` DECIMAL(18,4) NULL,
    `capacity_unit` VARCHAR(50) NULL COMMENT 'Unit of capacity (kg, liters, units, etc.)',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` TINYINT DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_manufacturing_machines_code` (`code`),
    KEY `idx_manufacturing_machines_branch` (`branch_id`),
    KEY `idx_manufacturing_machines_status` (`status`),
    CONSTRAINT `fk_manufacturing_machines_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_manufacturing_machines_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_staff (Manufacturing Staff Setup)
CREATE TABLE IF NOT EXISTS `manufacturing_staff` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `employment_id` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `phone_number` VARCHAR(20) NULL,
    `address` TEXT NULL,
    `bvn` VARCHAR(20) NULL COMMENT 'Bank Verification Number',
    `nin` VARCHAR(20) NULL COMMENT 'National Identification Number',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` TINYINT DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_manufacturing_staff_employment_id` (`employment_id`),
    KEY `idx_manufacturing_staff_branch` (`branch_id`),
    KEY `idx_manufacturing_staff_status` (`status`),
    CONSTRAINT `fk_manufacturing_staff_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_manufacturing_staff_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_teams (Production Team Setup)
CREATE TABLE IF NOT EXISTS `manufacturing_teams` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `ledger_balance` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'For penalty tracking',
    `status` TINYINT DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_manufacturing_teams_branch` (`branch_id`),
    KEY `idx_manufacturing_teams_status` (`status`),
    CONSTRAINT `fk_manufacturing_teams_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_manufacturing_teams_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_team_supervisors (Team Supervisors - ABTC Staff)
CREATE TABLE IF NOT EXISTS `manufacturing_team_supervisors` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `team_id` BIGINT UNSIGNED NOT NULL,
    `employee_id` BIGINT UNSIGNED NOT NULL COMMENT 'Links to employees table (ABTC Staff)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_team_supervisors_team` (`team_id`),
    KEY `idx_team_supervisors_employee` (`employee_id`),
    CONSTRAINT `fk_team_supervisors_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_team_supervisors_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_team_members (Team Members)
CREATE TABLE IF NOT EXISTS `manufacturing_team_members` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `team_id` BIGINT UNSIGNED NOT NULL,
    `staff_id` BIGINT UNSIGNED NOT NULL COMMENT 'Links to manufacturing_staff',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_team_members_team` (`team_id`),
    KEY `idx_team_members_staff` (`staff_id`),
    CONSTRAINT `fk_team_members_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_team_members_staff` FOREIGN KEY (`staff_id`) REFERENCES `manufacturing_staff`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_boms (Bill of Materials - Both Batch and Single)
CREATE TABLE IF NOT EXISTS `manufacturing_boms` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: BOM{YYMM}{BRANCH}{INCREMENT}',
    `approval_document_no` VARCHAR(100) NULL COMMENT 'Memo reference',
    `category_id` BIGINT UNSIGNED NULL COMMENT 'Product category',
    `description` VARCHAR(255) NOT NULL COMMENT 'e.g., BoM for Best Choice Emulsion Paint 3040 Drum',
    `bom_type` ENUM('batch', 'single') NOT NULL COMMENT 'Type of BOM',
    `finish_product_id` BIGINT UNSIGNED NOT NULL COMMENT 'Link to products table',
    `output_store_id` BIGINT UNSIGNED NOT NULL COMMENT 'Store for finished goods',
    `actual_output` DECIMAL(18,4) DEFAULT 1.0000 COMMENT 'For batch: expected output qty',
    `accepted_excess` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Acceptable excess percentage',
    `accepted_shortage` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Acceptable shortage percentage',
    `main_raw_material_id` BIGINT UNSIGNED NULL COMMENT 'Output determinant (batch only)',
    `labor_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Other cost: labor',
    `power_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Other cost: power',
    `other_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Other cost: misc',
    `total_material_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Calculated sum of materials',
    `total_other_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'labor + power + other',
    `total_cost_per_unit` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Total cost / actual_output',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` TINYINT DEFAULT 1 COMMENT '1=active, 0=inactive',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_manufacturing_boms_reference` (`reference`),
    KEY `idx_manufacturing_boms_type` (`bom_type`),
    KEY `idx_manufacturing_boms_status` (`status`),
    KEY `idx_manufacturing_boms_branch` (`branch_id`),
    KEY `idx_manufacturing_boms_finish_product` (`finish_product_id`),
    CONSTRAINT `fk_manufacturing_boms_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
    CONSTRAINT `fk_manufacturing_boms_finish_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_manufacturing_boms_output_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores`(`id`),
    CONSTRAINT `fk_manufacturing_boms_main_raw_material` FOREIGN KEY (`main_raw_material_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_manufacturing_boms_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_manufacturing_boms_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_bom_materials (BOM Line Items - Raw Materials)
CREATE TABLE IF NOT EXISTS `manufacturing_bom_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `bom_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT 'Raw material product',
    `quantity` DECIMAL(18,4) NOT NULL COMMENT 'Required qty per batch/unit',
    `source_store_id` BIGINT UNSIGNED NOT NULL COMMENT 'Where to get raw material',
    `unit_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Current unit cost (calculated)',
    `total_cost` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'qty * unit_cost',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_bom_materials_bom` (`bom_id`),
    KEY `idx_bom_materials_product` (`product_id`),
    CONSTRAINT `fk_bom_materials_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bom_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_bom_materials_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 2: PROCESSING TABLES
-- =====================================================

-- Table: production_orders
CREATE TABLE IF NOT EXISTS `production_orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: PRO{YYMM}{BRANCH}{INCREMENT}',
    `order_date` DATE NULL COMMENT 'Date when order was created',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `status` ENUM('pending', 'approved', 'closed') DEFAULT 'pending',
    `total_finish_goods_qty` DECIMAL(18,4) DEFAULT 0.0000,
    `processed_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Updated by schedules',
    `notes` TEXT NULL,
    `approved_by` BIGINT UNSIGNED NULL,
    `approved_at` TIMESTAMP NULL,
    `closed_by` BIGINT UNSIGNED NULL,
    `closed_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_production_orders_reference` (`reference`),
    KEY `idx_production_orders_status` (`status`),
    KEY `idx_production_orders_branch` (`branch_id`),
    KEY `idx_production_orders_dates` (`start_date`, `end_date`),
    CONSTRAINT `fk_production_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_production_orders_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_production_orders_closed_by` FOREIGN KEY (`closed_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_production_orders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: production_order_items (Order Line Items - BOM Selections)
CREATE TABLE IF NOT EXISTS `production_order_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `production_order_id` BIGINT UNSIGNED NOT NULL,
    `bom_id` BIGINT UNSIGNED NOT NULL,
    `quantity_to_produce` DECIMAL(18,4) NOT NULL COMMENT 'Qty to produce',
    `scheduled_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Qty already scheduled',
    `produced_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Qty actually produced',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_order_items_order` (`production_order_id`),
    KEY `idx_order_items_bom` (`bom_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: production_order_materials (Calculated Raw Materials Needed)
CREATE TABLE IF NOT EXISTS `production_order_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `production_order_id` BIGINT UNSIGNED NOT NULL,
    `production_order_item_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL COMMENT 'Raw material',
    `required_qty` DECIMAL(18,4) NOT NULL,
    `source_store_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_order_materials_order` (`production_order_id`),
    KEY `idx_order_materials_item` (`production_order_item_id`),
    KEY `idx_order_materials_product` (`product_id`),
    CONSTRAINT `fk_order_materials_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_materials_item` FOREIGN KEY (`production_order_item_id`) REFERENCES `production_order_items`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_order_materials_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: daily_manufacturing_schedules
CREATE TABLE IF NOT EXISTS `daily_manufacturing_schedules` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: DMS{YYMM}{BRANCH}{INCREMENT}',
    `schedule_date` DATE NOT NULL,
    `production_order_id` BIGINT UNSIGNED NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved') DEFAULT 'pending',
    `approved_by` BIGINT UNSIGNED NULL,
    `approved_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_daily_schedules_reference` (`reference`),
    KEY `idx_daily_schedules_date` (`schedule_date`),
    KEY `idx_daily_schedules_order` (`production_order_id`),
    KEY `idx_daily_schedules_status` (`status`),
    CONSTRAINT `fk_daily_schedules_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders`(`id`),
    CONSTRAINT `fk_daily_schedules_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_daily_schedules_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_daily_schedules_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: daily_manufacturing_schedule_items
CREATE TABLE IF NOT EXISTS `daily_manufacturing_schedule_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `schedule_id` BIGINT UNSIGNED NOT NULL,
    `production_order_item_id` BIGINT UNSIGNED NOT NULL,
    `scheduled_qty` DECIMAL(18,4) NOT NULL,
    `requisitioned_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Qty that has been requisitioned',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_schedule_items_schedule` (`schedule_id`),
    KEY `idx_schedule_items_order_item` (`production_order_item_id`),
    CONSTRAINT `fk_schedule_items_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `daily_manufacturing_schedules`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_schedule_items_order_item` FOREIGN KEY (`production_order_item_id`) REFERENCES `production_order_items`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: inventory_reservations (For Tracking Reserved Inventory)
CREATE TABLE IF NOT EXISTS `inventory_reservations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference_type` VARCHAR(100) NOT NULL COMMENT 'e.g., DailyManufacturingSchedule, MaterialsRequisition',
    `reference_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NOT NULL,
    `reserved_qty` DECIMAL(18,4) NOT NULL,
    `status` ENUM('reserved', 'released', 'consumed') DEFAULT 'reserved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_reservations_reference` (`reference_type`, `reference_id`),
    KEY `idx_reservations_product` (`product_id`),
    KEY `idx_reservations_store` (`store_id`),
    KEY `idx_reservations_status` (`status`),
    CONSTRAINT `fk_reservations_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_reservations_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: materials_requisitions
CREATE TABLE IF NOT EXISTS `materials_requisitions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: MRF{YYMM}{BRANCH}{INCREMENT}',
    `requisition_date` DATE NOT NULL,
    `schedule_id` BIGINT UNSIGNED NULL COMMENT 'If linked to schedule',
    `bom_id` BIGINT UNSIGNED NULL COMMENT 'If linked directly to BOM (single product)',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'approved', 'issued', 'received') DEFAULT 'pending',
    `approved_by` BIGINT UNSIGNED NULL,
    `approved_at` TIMESTAMP NULL,
    `issued_by` BIGINT UNSIGNED NULL,
    `issued_at` TIMESTAMP NULL,
    `received_by` BIGINT UNSIGNED NULL,
    `received_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_requisitions_reference` (`reference`),
    KEY `idx_requisitions_date` (`requisition_date`),
    KEY `idx_requisitions_schedule` (`schedule_id`),
    KEY `idx_requisitions_bom` (`bom_id`),
    KEY `idx_requisitions_status` (`status`),
    CONSTRAINT `fk_requisitions_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `daily_manufacturing_schedules`(`id`),
    CONSTRAINT `fk_requisitions_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms`(`id`),
    CONSTRAINT `fk_requisitions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_requisitions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_requisitions_issued_by` FOREIGN KEY (`issued_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_requisitions_received_by` FOREIGN KEY (`received_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_requisitions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: materials_requisition_items
CREATE TABLE IF NOT EXISTS `materials_requisition_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `requisition_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `source_store_id` BIGINT UNSIGNED NOT NULL,
    `required_qty` DECIMAL(18,4) NOT NULL,
    `issued_qty` DECIMAL(18,4) DEFAULT 0.0000,
    `received_qty` DECIMAL(18,4) DEFAULT 0.0000,
    `unit_cost` DECIMAL(18,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_requisition_items_requisition` (`requisition_id`),
    KEY `idx_requisition_items_product` (`product_id`),
    CONSTRAINT `fk_requisition_items_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_requisition_items_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_requisition_items_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: single_product_manufacturing
CREATE TABLE IF NOT EXISTS `single_product_manufacturing` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: SPM{YYMM}{BRANCH}{INCREMENT}',
    `manufacturing_date` DATE NOT NULL,
    `requisition_id` BIGINT UNSIGNED NOT NULL COMMENT 'Must be single BOM requisition',
    `batch_number` VARCHAR(50) NOT NULL COMMENT 'Auto: BATCH{YYYYMMDD}{INCREMENT}',
    `team_id` BIGINT UNSIGNED NOT NULL,
    `machine_id` BIGINT UNSIGNED NULL,
    `quantity` DECIMAL(18,4) NOT NULL,
    `bom_id` BIGINT UNSIGNED NOT NULL,
    `finish_product_id` BIGINT UNSIGNED NOT NULL,
    `output_store_id` BIGINT UNSIGNED NOT NULL,
    `total_material_cost` DECIMAL(18,2) DEFAULT 0.00,
    `total_other_cost` DECIMAL(18,2) DEFAULT 0.00,
    `total_cost` DECIMAL(18,2) DEFAULT 0.00,
    `unit_cost` DECIMAL(18,2) DEFAULT 0.00,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_single_manufacturing_reference` (`reference`),
    UNIQUE KEY `uk_single_manufacturing_batch` (`batch_number`),
    KEY `idx_single_manufacturing_date` (`manufacturing_date`),
    KEY `idx_single_manufacturing_status` (`status`),
    KEY `idx_single_manufacturing_team` (`team_id`),
    CONSTRAINT `fk_single_manufacturing_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions`(`id`),
    CONSTRAINT `fk_single_manufacturing_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams`(`id`),
    CONSTRAINT `fk_single_manufacturing_machine` FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines`(`id`),
    CONSTRAINT `fk_single_manufacturing_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms`(`id`),
    CONSTRAINT `fk_single_manufacturing_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_single_manufacturing_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores`(`id`),
    CONSTRAINT `fk_single_manufacturing_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_single_manufacturing_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_single_manufacturing_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: single_product_manufacturing_materials
CREATE TABLE IF NOT EXISTS `single_product_manufacturing_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `manufacturing_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(18,4) NOT NULL,
    `unit_cost` DECIMAL(18,2) NOT NULL,
    `total_cost` DECIMAL(18,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_single_mfg_materials_manufacturing` (`manufacturing_id`),
    KEY `idx_single_mfg_materials_product` (`product_id`),
    CONSTRAINT `fk_single_mfg_materials_manufacturing` FOREIGN KEY (`manufacturing_id`) REFERENCES `single_product_manufacturing`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_single_mfg_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_single_mfg_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: batch_productions
CREATE TABLE IF NOT EXISTS `batch_productions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: BPC{YYMM}{BRANCH}{INCREMENT}',
    `production_date` DATE NOT NULL,
    `requisition_id` BIGINT UNSIGNED NOT NULL COMMENT 'Must be batch BOM requisition',
    `batch_number` VARCHAR(50) NOT NULL COMMENT 'Auto: BATCH{YYYYMMDD}{INCREMENT}',
    `team_id` BIGINT UNSIGNED NOT NULL,
    `machine_id` BIGINT UNSIGNED NULL,
    `quantity` DECIMAL(18,4) DEFAULT 1.0000 COMMENT 'Default 1',
    `bom_id` BIGINT UNSIGNED NOT NULL,
    `total_material_cost` DECIMAL(18,2) DEFAULT 0.00,
    `wip_value` DECIMAL(18,2) DEFAULT 0.00 COMMENT 'Work in Progress value',
    `converted_qty` DECIMAL(18,4) DEFAULT 0.0000 COMMENT 'Qty converted to finished goods',
    `remaining_qty` DECIMAL(18,4) DEFAULT 1.0000 COMMENT 'Qty remaining in WIP',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted', 'fully_converted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_batch_productions_reference` (`reference`),
    UNIQUE KEY `uk_batch_productions_batch` (`batch_number`),
    KEY `idx_batch_productions_date` (`production_date`),
    KEY `idx_batch_productions_status` (`status`),
    KEY `idx_batch_productions_team` (`team_id`),
    CONSTRAINT `fk_batch_productions_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions`(`id`),
    CONSTRAINT `fk_batch_productions_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams`(`id`),
    CONSTRAINT `fk_batch_productions_machine` FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines`(`id`),
    CONSTRAINT `fk_batch_productions_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms`(`id`),
    CONSTRAINT `fk_batch_productions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_batch_productions_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_batch_productions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: batch_production_materials
CREATE TABLE IF NOT EXISTS `batch_production_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `batch_production_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(18,4) NOT NULL,
    `unit_cost` DECIMAL(18,2) NOT NULL,
    `total_cost` DECIMAL(18,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_batch_materials_batch` (`batch_production_id`),
    KEY `idx_batch_materials_product` (`product_id`),
    CONSTRAINT `fk_batch_materials_batch` FOREIGN KEY (`batch_production_id`) REFERENCES `batch_productions`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_batch_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_batch_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: batch_conversions
CREATE TABLE IF NOT EXISTS `batch_conversions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: BCN{YYMM}{BRANCH}{INCREMENT}',
    `conversion_date` DATE NOT NULL,
    `batch_production_id` BIGINT UNSIGNED NOT NULL,
    `produced_qty` DECIMAL(18,4) NOT NULL,
    `finish_product_id` BIGINT UNSIGNED NOT NULL,
    `output_store_id` BIGINT UNSIGNED NOT NULL,
    `wip_cost_deducted` DECIMAL(18,2) NOT NULL COMMENT 'Cost moved from WIP',
    `labor_cost` DECIMAL(18,2) DEFAULT 0.00,
    `power_cost` DECIMAL(18,2) DEFAULT 0.00,
    `other_cost` DECIMAL(18,2) DEFAULT 0.00,
    `total_cost` DECIMAL(18,2) NOT NULL,
    `unit_cost` DECIMAL(18,2) NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_batch_conversions_reference` (`reference`),
    KEY `idx_batch_conversions_date` (`conversion_date`),
    KEY `idx_batch_conversions_batch` (`batch_production_id`),
    KEY `idx_batch_conversions_status` (`status`),
    CONSTRAINT `fk_batch_conversions_batch` FOREIGN KEY (`batch_production_id`) REFERENCES `batch_productions`(`id`),
    CONSTRAINT `fk_batch_conversions_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_batch_conversions_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores`(`id`),
    CONSTRAINT `fk_batch_conversions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_batch_conversions_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_batch_conversions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_additional_costs
CREATE TABLE IF NOT EXISTS `manufacturing_additional_costs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: MAC{YYMM}{BRANCH}{INCREMENT}',
    `cost_date` DATE NOT NULL,
    `production_type` VARCHAR(50) NOT NULL COMMENT 'single_product or batch_conversion',
    `production_id` BIGINT UNSIGNED NOT NULL,
    `account_id` BIGINT UNSIGNED NOT NULL COMMENT 'General Account',
    `amount` DECIMAL(18,2) NOT NULL,
    `description` TEXT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_additional_costs_reference` (`reference`),
    KEY `idx_additional_costs_date` (`cost_date`),
    KEY `idx_additional_costs_production` (`production_type`, `production_id`),
    KEY `idx_additional_costs_status` (`status`),
    CONSTRAINT `fk_additional_costs_account` FOREIGN KEY (`account_id`) REFERENCES `general_accounts`(`id`),
    CONSTRAINT `fk_additional_costs_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_additional_costs_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_additional_costs_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_penalties
CREATE TABLE IF NOT EXISTS `manufacturing_penalties` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: PEN{YYMM}{BRANCH}{INCREMENT}',
    `penalty_date` DATE NOT NULL,
    `penalty_type` ENUM('team', 'staff') NOT NULL,
    `team_id` BIGINT UNSIGNED NULL,
    `staff_id` BIGINT UNSIGNED NULL,
    `description` TEXT NOT NULL,
    `amount_charged` DECIMAL(18,2) NOT NULL,
    `total_loss_incurred` DECIMAL(18,2) DEFAULT 0.00,
    `production_reference` VARCHAR(50) NULL COMMENT 'Link to production doc',
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_penalties_reference` (`reference`),
    KEY `idx_penalties_date` (`penalty_date`),
    KEY `idx_penalties_type` (`penalty_type`),
    KEY `idx_penalties_team` (`team_id`),
    KEY `idx_penalties_staff` (`staff_id`),
    KEY `idx_penalties_status` (`status`),
    CONSTRAINT `fk_penalties_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams`(`id`),
    CONSTRAINT `fk_penalties_staff` FOREIGN KEY (`staff_id`) REFERENCES `manufacturing_staff`(`id`),
    CONSTRAINT `fk_penalties_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_penalties_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_penalties_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_returns
CREATE TABLE IF NOT EXISTS `manufacturing_returns` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: MRT{YYMM}{BRANCH}{INCREMENT}',
    `return_date` DATE NOT NULL,
    `production_type` VARCHAR(50) NOT NULL COMMENT 'single_product or batch_conversion',
    `production_id` BIGINT UNSIGNED NOT NULL,
    `reason` TEXT NOT NULL,
    `return_qty` DECIMAL(18,4) NOT NULL,
    `total_cost_returned` DECIMAL(18,2) DEFAULT 0.00,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_returns_reference` (`reference`),
    KEY `idx_returns_date` (`return_date`),
    KEY `idx_returns_production` (`production_type`, `production_id`),
    KEY `idx_returns_status` (`status`),
    CONSTRAINT `fk_returns_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_returns_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_returns_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_return_materials
CREATE TABLE IF NOT EXISTS `manufacturing_return_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `return_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(18,4) NOT NULL,
    `unit_cost` DECIMAL(18,2) NOT NULL,
    `total_cost` DECIMAL(18,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_return_materials_return` (`return_id`),
    KEY `idx_return_materials_product` (`product_id`),
    CONSTRAINT `fk_return_materials_return` FOREIGN KEY (`return_id`) REFERENCES `manufacturing_returns`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_return_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_return_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_reworks
CREATE TABLE IF NOT EXISTS `manufacturing_reworks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reference` VARCHAR(50) NOT NULL COMMENT 'Auto: MRW{YYMM}{BRANCH}{INCREMENT}',
    `rework_date` DATE NOT NULL,
    `production_type` VARCHAR(50) NOT NULL,
    `production_id` BIGINT UNSIGNED NOT NULL,
    `labor_cost` DECIMAL(18,2) DEFAULT 0.00,
    `power_cost` DECIMAL(18,2) DEFAULT 0.00,
    `other_cost` DECIMAL(18,2) DEFAULT 0.00,
    `total_material_cost` DECIMAL(18,2) DEFAULT 0.00,
    `total_cost` DECIMAL(18,2) DEFAULT 0.00,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'posted') DEFAULT 'pending',
    `posted_by` BIGINT UNSIGNED NULL,
    `posted_at` TIMESTAMP NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_reworks_reference` (`reference`),
    KEY `idx_reworks_date` (`rework_date`),
    KEY `idx_reworks_production` (`production_type`, `production_id`),
    KEY `idx_reworks_status` (`status`),
    CONSTRAINT `fk_reworks_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`),
    CONSTRAINT `fk_reworks_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`),
    CONSTRAINT `fk_reworks_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: manufacturing_rework_materials
CREATE TABLE IF NOT EXISTS `manufacturing_rework_materials` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `rework_id` BIGINT UNSIGNED NOT NULL,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `store_id` BIGINT UNSIGNED NOT NULL,
    `quantity` DECIMAL(18,4) NOT NULL,
    `unit_cost` DECIMAL(18,2) NOT NULL,
    `total_cost` DECIMAL(18,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_rework_materials_rework` (`rework_id`),
    KEY `idx_rework_materials_product` (`product_id`),
    CONSTRAINT `fk_rework_materials_rework` FOREIGN KEY (`rework_id`) REFERENCES `manufacturing_reworks`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rework_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    CONSTRAINT `fk_rework_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 3: ADD GENERAL ACCOUNT CONTROL FOR WIP
-- =====================================================

-- NOTE: After running this script, you need to manually configure the Manufacturing WIP account:
-- 1. Create a General Account for "Manufacturing Work in Progress" (Asset type)
-- 2. Then insert the control record:
--    INSERT INTO `general_account_controls` (`code`, `general_account_id`, `created_at`, `updated_at`)
--    VALUES ('manufacturing_wip', <YOUR_WIP_ACCOUNT_ID>, NOW(), NOW());
--
-- Example (replace 999 with your actual WIP account ID):
-- INSERT INTO `general_account_controls` (`code`, `general_account_id`, `created_at`, `updated_at`)
-- SELECT 'manufacturing_wip', 999, NOW(), NOW()
-- FROM DUAL
-- WHERE NOT EXISTS (
--     SELECT 1 FROM `general_account_controls` WHERE `code` = 'manufacturing_wip'
-- );

-- =====================================================
-- SECTION 4: MANUFACTURING PERMISSIONS
-- =====================================================

-- Insert manufacturing permissions
INSERT INTO `permissions` (`name`, `description`, `guard_name`, `created_at`, `updated_at`) VALUES
-- Menu permissions
('menu.manufacturing', 'Access Manufacturing Module', 'web', NOW(), NOW()),
('submenu.manufacturing.setup', 'Access Manufacturing Setup Menu', 'web', NOW(), NOW()),
('submenu.manufacturing.processing', 'Access Manufacturing Processing Menu', 'web', NOW(), NOW()),
('submenu.manufacturing.reports', 'Access Manufacturing Reports Menu', 'web', NOW(), NOW()),

-- BOM permissions
('manufacturing.boms.index', 'View Bill of Materials List', 'web', NOW(), NOW()),
('manufacturing.boms.create', 'Create Bill of Materials', 'web', NOW(), NOW()),
('manufacturing.boms.edit', 'Edit Bill of Materials', 'web', NOW(), NOW()),
('manufacturing.boms.delete', 'Delete Bill of Materials', 'web', NOW(), NOW()),
('manufacturing.boms.show', 'View Bill of Materials Details', 'web', NOW(), NOW()),

-- Machine permissions
('manufacturing.machines.index', 'View Manufacturing Machines List', 'web', NOW(), NOW()),
('manufacturing.machines.create', 'Create Manufacturing Machine', 'web', NOW(), NOW()),
('manufacturing.machines.edit', 'Edit Manufacturing Machine', 'web', NOW(), NOW()),
('manufacturing.machines.delete', 'Delete Manufacturing Machine', 'web', NOW(), NOW()),

-- Staff permissions
('manufacturing.staff.index', 'View Manufacturing Staff List', 'web', NOW(), NOW()),
('manufacturing.staff.create', 'Create Manufacturing Staff', 'web', NOW(), NOW()),
('manufacturing.staff.edit', 'Edit Manufacturing Staff', 'web', NOW(), NOW()),
('manufacturing.staff.delete', 'Delete Manufacturing Staff', 'web', NOW(), NOW()),

-- Team permissions
('manufacturing.teams.index', 'View Production Teams List', 'web', NOW(), NOW()),
('manufacturing.teams.create', 'Create Production Team', 'web', NOW(), NOW()),
('manufacturing.teams.edit', 'Edit Production Team', 'web', NOW(), NOW()),
('manufacturing.teams.delete', 'Delete Production Team', 'web', NOW(), NOW()),
('manufacturing.teams.show', 'View Production Team Details', 'web', NOW(), NOW()),

-- Production Order permissions
('manufacturing.production_orders.index', 'View Production Orders List', 'web', NOW(), NOW()),
('manufacturing.production_orders.create', 'Create Production Order', 'web', NOW(), NOW()),
('manufacturing.production_orders.show', 'View Production Order Details', 'web', NOW(), NOW()),
('manufacturing.production_orders.approve', 'Approve Production Order', 'web', NOW(), NOW()),
('manufacturing.production_orders.close', 'Close Production Order', 'web', NOW(), NOW()),
('manufacturing.production_orders.delete', 'Delete Production Order', 'web', NOW(), NOW()),

-- Schedule permissions
('manufacturing.schedules.index', 'View Daily Manufacturing Schedules List', 'web', NOW(), NOW()),
('manufacturing.schedules.create', 'Create Daily Manufacturing Schedule', 'web', NOW(), NOW()),
('manufacturing.schedules.show', 'View Daily Manufacturing Schedule Details', 'web', NOW(), NOW()),
('manufacturing.schedules.approve', 'Approve Daily Manufacturing Schedule', 'web', NOW(), NOW()),
('manufacturing.schedules.delete', 'Delete Daily Manufacturing Schedule', 'web', NOW(), NOW()),

-- Requisition permissions
('manufacturing.requisitions.index', 'View Materials Requisitions List', 'web', NOW(), NOW()),
('manufacturing.requisitions.create', 'Create Materials Requisition', 'web', NOW(), NOW()),
('manufacturing.requisitions.show', 'View Materials Requisition Details', 'web', NOW(), NOW()),
('manufacturing.requisitions.approve', 'Approve Materials Requisition', 'web', NOW(), NOW()),
('manufacturing.requisitions.issue', 'Issue Materials Requisition', 'web', NOW(), NOW()),
('manufacturing.requisitions.receive', 'Receive Materials Requisition', 'web', NOW(), NOW()),
('manufacturing.requisitions.delete', 'Delete Materials Requisition', 'web', NOW(), NOW()),

-- Single Manufacturing permissions
('manufacturing.single_manufacturing.index', 'View Single Product Manufacturing List', 'web', NOW(), NOW()),
('manufacturing.single_manufacturing.create', 'Create Single Product Manufacturing', 'web', NOW(), NOW()),
('manufacturing.single_manufacturing.show', 'View Single Product Manufacturing Details', 'web', NOW(), NOW()),
('manufacturing.single_manufacturing.post', 'Post Single Product Manufacturing', 'web', NOW(), NOW()),
('manufacturing.single_manufacturing.print', 'Print Single Product Manufacturing', 'web', NOW(), NOW()),
('manufacturing.single_manufacturing.delete', 'Delete Single Product Manufacturing', 'web', NOW(), NOW()),

-- Batch Production permissions
('manufacturing.batch_production.index', 'View Batch Production List', 'web', NOW(), NOW()),
('manufacturing.batch_production.create', 'Create Batch Production', 'web', NOW(), NOW()),
('manufacturing.batch_production.show', 'View Batch Production Details', 'web', NOW(), NOW()),
('manufacturing.batch_production.post', 'Post Batch Production', 'web', NOW(), NOW()),
('manufacturing.batch_production.print', 'Print Batch Production', 'web', NOW(), NOW()),
('manufacturing.batch_production.delete', 'Delete Batch Production', 'web', NOW(), NOW()),

-- Batch Conversion permissions
('manufacturing.batch_conversion.index', 'View Batch Conversion List', 'web', NOW(), NOW()),
('manufacturing.batch_conversion.create', 'Create Batch Conversion', 'web', NOW(), NOW()),
('manufacturing.batch_conversion.show', 'View Batch Conversion Details', 'web', NOW(), NOW()),
('manufacturing.batch_conversion.post', 'Post Batch Conversion', 'web', NOW(), NOW()),
('manufacturing.batch_conversion.print', 'Print Batch Conversion', 'web', NOW(), NOW()),
('manufacturing.batch_conversion.delete', 'Delete Batch Conversion', 'web', NOW(), NOW()),

-- Additional Costs permissions
('manufacturing.additional_costs.index', 'View Manufacturing Additional Costs List', 'web', NOW(), NOW()),
('manufacturing.additional_costs.create', 'Create Manufacturing Additional Cost', 'web', NOW(), NOW()),
('manufacturing.additional_costs.show', 'View Manufacturing Additional Cost Details', 'web', NOW(), NOW()),
('manufacturing.additional_costs.post', 'Post Manufacturing Additional Cost', 'web', NOW(), NOW()),
('manufacturing.additional_costs.delete', 'Delete Manufacturing Additional Cost', 'web', NOW(), NOW()),

-- Penalties permissions
('manufacturing.penalties.index', 'View Manufacturing Penalties List', 'web', NOW(), NOW()),
('manufacturing.penalties.create', 'Create Manufacturing Penalty', 'web', NOW(), NOW()),
('manufacturing.penalties.show', 'View Manufacturing Penalty Details', 'web', NOW(), NOW()),
('manufacturing.penalties.post', 'Post Manufacturing Penalty', 'web', NOW(), NOW()),
('manufacturing.penalties.delete', 'Delete Manufacturing Penalty', 'web', NOW(), NOW()),

-- Returns permissions
('manufacturing.returns.index', 'View Manufacturing Returns List', 'web', NOW(), NOW()),
('manufacturing.returns.create', 'Create Manufacturing Return', 'web', NOW(), NOW()),
('manufacturing.returns.show', 'View Manufacturing Return Details', 'web', NOW(), NOW()),
('manufacturing.returns.post', 'Post Manufacturing Return', 'web', NOW(), NOW()),
('manufacturing.returns.delete', 'Delete Manufacturing Return', 'web', NOW(), NOW()),

-- Reworks permissions
('manufacturing.reworks.index', 'View Manufacturing Reworks List', 'web', NOW(), NOW()),
('manufacturing.reworks.create', 'Create Manufacturing Rework', 'web', NOW(), NOW()),
('manufacturing.reworks.show', 'View Manufacturing Rework Details', 'web', NOW(), NOW()),
('manufacturing.reworks.post', 'Post Manufacturing Rework', 'web', NOW(), NOW()),
('manufacturing.reworks.delete', 'Delete Manufacturing Rework', 'web', NOW(), NOW()),

-- Report permissions
('manufacturing.reports.history', 'View Manufacturing History Report', 'web', NOW(), NOW()),
('manufacturing.reports.teams', 'View Manufacturing Teams Report', 'web', NOW(), NOW())
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`), `updated_at` = NOW();

-- =====================================================
-- SECTION 5: ASSIGN PERMISSIONS TO SUPER ADMIN (Role ID 1)
-- =====================================================
-- This assigns all manufacturing permissions to Super Admin role
-- Change role_id if your admin role has a different ID

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, 1
FROM `permissions` p
WHERE p.name LIKE 'menu.manufacturing%'
   OR p.name LIKE 'submenu.manufacturing%'
   OR p.name LIKE 'manufacturing.%'
ON DUPLICATE KEY UPDATE `role_id` = `role_id`;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- END OF MANUFACTURING MODULE SCHEMA
-- =====================================================
