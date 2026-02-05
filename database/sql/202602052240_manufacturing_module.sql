CREATE DATABASE  IF NOT EXISTS `ch` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ch`;
-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: ch
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `daily_manufacturing_schedule_items`
--

DROP TABLE IF EXISTS `daily_manufacturing_schedule_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_manufacturing_schedule_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint unsigned NOT NULL,
  `production_order_item_id` bigint unsigned NOT NULL,
  `scheduled_qty` decimal(18,4) NOT NULL,
  `requisitioned_qty` decimal(18,4) DEFAULT '0.0000' COMMENT 'Qty that has been requisitioned',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_schedule_items_schedule` (`schedule_id`),
  KEY `idx_schedule_items_order_item` (`production_order_item_id`),
  CONSTRAINT `fk_schedule_items_order_item` FOREIGN KEY (`production_order_item_id`) REFERENCES `production_order_items` (`id`),
  CONSTRAINT `fk_schedule_items_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `daily_manufacturing_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_manufacturing_schedules`
--

DROP TABLE IF EXISTS `daily_manufacturing_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_manufacturing_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: DMS{YYMM}{BRANCH}{INCREMENT}',
  `schedule_date` date NOT NULL,
  `production_order_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Schedule notes',
  `branch_id` bigint unsigned NOT NULL,
  `team_id` bigint unsigned DEFAULT NULL COMMENT 'Manufacturing team',
  `machine_id` bigint unsigned DEFAULT NULL COMMENT 'Manufacturing machine',
  `status` enum('pending','approved') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_schedules_reference` (`reference`),
  KEY `idx_daily_schedules_date` (`schedule_date`),
  KEY `idx_daily_schedules_order` (`production_order_id`),
  KEY `idx_daily_schedules_status` (`status`),
  KEY `fk_daily_schedules_branch` (`branch_id`),
  KEY `fk_daily_schedules_approved_by` (`approved_by`),
  KEY `fk_daily_schedules_created_by` (`created_by`),
  KEY `fk_schedules_team` (`team_id`),
  KEY `fk_schedules_machine` (`machine_id`),
  CONSTRAINT `fk_daily_schedules_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_daily_schedules_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_daily_schedules_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_daily_schedules_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`),
  CONSTRAINT `fk_schedules_machine` FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_schedules_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `experience` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary` decimal(18,2) DEFAULT NULL,
  `vacation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1=Active, 0=Inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_employees_status` (`status`),
  KEY `idx_employees_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_reservations`
--

DROP TABLE IF EXISTS `inventory_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_reservations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., DailyManufacturingSchedule, MaterialsRequisition',
  `reference_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `reserved_qty` decimal(18,4) NOT NULL,
  `status` enum('reserved','released','consumed') COLLATE utf8mb4_unicode_ci DEFAULT 'reserved',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reservations_reference` (`reference_type`,`reference_id`),
  KEY `idx_reservations_product` (`product_id`),
  KEY `idx_reservations_store` (`store_id`),
  KEY `idx_reservations_status` (`status`),
  KEY `idx_reservations_product_store_status` (`product_id`,`store_id`,`status`),
  CONSTRAINT `fk_reservations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_reservations_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_additional_costs`
--

DROP TABLE IF EXISTS `manufacturing_additional_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_additional_costs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: MAC{YYMM}{BRANCH}{INCREMENT}',
  `cost_date` date NOT NULL,
  `production_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'single_product or batch_conversion',
  `production_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL COMMENT 'General Account',
  `amount` decimal(18,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_additional_costs_reference` (`reference`),
  KEY `idx_additional_costs_date` (`cost_date`),
  KEY `idx_additional_costs_production` (`production_type`,`production_id`),
  KEY `idx_additional_costs_status` (`status`),
  KEY `fk_additional_costs_branch` (`branch_id`),
  KEY `fk_additional_costs_posted_by` (`posted_by`),
  KEY `fk_additional_costs_created_by` (`created_by`),
  KEY `idx_additional_costs_poly` (`production_type`,`production_id`),
  KEY `fk_additional_costs_account` (`account_id`),
  CONSTRAINT `fk_additional_costs_account` FOREIGN KEY (`account_id`) REFERENCES `general_accounts` (`id`),
  CONSTRAINT `fk_additional_costs_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_additional_costs_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_additional_costs_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_bom_materials`
--

DROP TABLE IF EXISTS `manufacturing_bom_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_bom_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bom_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL COMMENT 'Raw material product',
  `quantity` decimal(18,4) NOT NULL COMMENT 'Required qty per batch/unit',
  `source_store_id` bigint unsigned NOT NULL COMMENT 'Where to get raw material',
  `unit_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Current unit cost (calculated)',
  `total_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'qty * unit_cost',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bom_materials_bom` (`bom_id`),
  KEY `idx_bom_materials_product` (`product_id`),
  KEY `fk_bom_materials_store` (`source_store_id`),
  CONSTRAINT `fk_bom_materials_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bom_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_bom_materials_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_boms`
--

DROP TABLE IF EXISTS `manufacturing_boms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_boms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: BOM{YYMM}{BRANCH}{INCREMENT}',
  `approval_document_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Memo reference',
  `category_id` bigint unsigned DEFAULT NULL COMMENT 'Product category',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., BoM for Best Choice Emulsion Paint 3040 Drum',
  `bom_type` enum('batch','single') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of BOM',
  `finish_product_id` bigint unsigned NOT NULL COMMENT 'Link to products table',
  `output_store_id` bigint unsigned NOT NULL COMMENT 'Store for finished goods',
  `actual_output` decimal(18,4) DEFAULT '1.0000' COMMENT 'For batch: expected output qty',
  `accepted_excess` decimal(18,4) DEFAULT '0.0000' COMMENT 'Acceptable excess percentage',
  `accepted_shortage` decimal(18,4) DEFAULT '0.0000' COMMENT 'Acceptable shortage percentage',
  `main_raw_material_id` bigint unsigned DEFAULT NULL COMMENT 'Output determinant (batch only)',
  `labor_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Other cost: labor',
  `power_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Other cost: power',
  `other_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Other cost: misc',
  `total_material_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Calculated sum of materials',
  `total_other_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'labor + power + other',
  `total_cost_per_unit` decimal(18,2) DEFAULT '0.00' COMMENT 'Total cost / actual_output',
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint DEFAULT '1' COMMENT '1=active, 0=inactive',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_manufacturing_boms_reference` (`reference`),
  KEY `idx_manufacturing_boms_type` (`bom_type`),
  KEY `idx_manufacturing_boms_status` (`status`),
  KEY `idx_manufacturing_boms_branch` (`branch_id`),
  KEY `idx_manufacturing_boms_finish_product` (`finish_product_id`),
  KEY `fk_manufacturing_boms_output_store` (`output_store_id`),
  KEY `fk_manufacturing_boms_created_by` (`created_by`),
  KEY `fk_manufacturing_boms_updated_by` (`updated_by`),
  KEY `idx_manufacturing_boms_category` (`category_id`),
  KEY `idx_manufacturing_boms_main_raw` (`main_raw_material_id`),
  CONSTRAINT `fk_manufacturing_boms_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_manufacturing_boms_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `fk_manufacturing_boms_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_manufacturing_boms_finish_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_manufacturing_boms_main_raw_material` FOREIGN KEY (`main_raw_material_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_manufacturing_boms_output_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `fk_manufacturing_boms_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_machines`
--

DROP TABLE IF EXISTS `manufacturing_machines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_machines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` decimal(18,4) DEFAULT NULL,
  `capacity_unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Unit of capacity (kg, liters, units, etc.)',
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint DEFAULT '1' COMMENT '1=active, 0=inactive',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_manufacturing_machines_code` (`code`),
  KEY `idx_manufacturing_machines_branch` (`branch_id`),
  KEY `idx_manufacturing_machines_status` (`status`),
  KEY `fk_manufacturing_machines_created_by` (`created_by`),
  KEY `fk_manufacturing_machines_updated_by` (`updated_by`),
  CONSTRAINT `fk_manufacturing_machines_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_manufacturing_machines_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_manufacturing_machines_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_penalties`
--

DROP TABLE IF EXISTS `manufacturing_penalties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_penalties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: PEN{YYMM}{BRANCH}{INCREMENT}',
  `penalty_date` date NOT NULL,
  `penalty_type` enum('team','staff') COLLATE utf8mb4_unicode_ci NOT NULL,
  `team_id` bigint unsigned DEFAULT NULL,
  `staff_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_charged` decimal(18,2) NOT NULL,
  `total_loss_incurred` decimal(18,2) DEFAULT '0.00',
  `production_reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link to production doc',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_penalties_reference` (`reference`),
  KEY `idx_penalties_date` (`penalty_date`),
  KEY `idx_penalties_type` (`penalty_type`),
  KEY `idx_penalties_team` (`team_id`),
  KEY `idx_penalties_staff` (`staff_id`),
  KEY `idx_penalties_status` (`status`),
  KEY `fk_penalties_branch` (`branch_id`),
  KEY `fk_penalties_posted_by` (`posted_by`),
  KEY `fk_penalties_created_by` (`created_by`),
  CONSTRAINT `fk_penalties_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_penalties_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_penalties_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_penalties_staff` FOREIGN KEY (`staff_id`) REFERENCES `manufacturing_staff` (`id`),
  CONSTRAINT `fk_penalties_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_return_materials`
--

DROP TABLE IF EXISTS `manufacturing_return_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_return_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_return_materials_return` (`return_id`),
  KEY `idx_return_materials_product` (`product_id`),
  KEY `fk_return_materials_store` (`store_id`),
  CONSTRAINT `fk_return_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_return_materials_return` FOREIGN KEY (`return_id`) REFERENCES `manufacturing_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_return_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_returns`
--

DROP TABLE IF EXISTS `manufacturing_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: MRT{YYMM}{BRANCH}{INCREMENT}',
  `return_date` date NOT NULL,
  `production_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'single_product or batch_conversion',
  `production_id` bigint unsigned NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_qty` decimal(18,4) NOT NULL,
  `total_cost_returned` decimal(18,2) DEFAULT '0.00',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_returns_reference` (`reference`),
  KEY `idx_returns_date` (`return_date`),
  KEY `idx_returns_production` (`production_type`,`production_id`),
  KEY `idx_returns_status` (`status`),
  KEY `fk_returns_branch` (`branch_id`),
  KEY `fk_returns_posted_by` (`posted_by`),
  KEY `fk_returns_created_by` (`created_by`),
  KEY `idx_returns_poly` (`production_type`,`production_id`),
  CONSTRAINT `fk_returns_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_returns_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_returns_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_rework_materials`
--

DROP TABLE IF EXISTS `manufacturing_rework_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_rework_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rework_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rework_materials_rework` (`rework_id`),
  KEY `idx_rework_materials_product` (`product_id`),
  KEY `fk_rework_materials_store` (`store_id`),
  CONSTRAINT `fk_rework_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_rework_materials_rework` FOREIGN KEY (`rework_id`) REFERENCES `manufacturing_reworks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rework_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_reworks`
--

DROP TABLE IF EXISTS `manufacturing_reworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_reworks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: MRW{YYMM}{BRANCH}{INCREMENT}',
  `rework_date` date NOT NULL,
  `production_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_id` bigint unsigned NOT NULL,
  `labor_cost` decimal(18,2) DEFAULT '0.00',
  `power_cost` decimal(18,2) DEFAULT '0.00',
  `other_cost` decimal(18,2) DEFAULT '0.00',
  `total_material_cost` decimal(18,2) DEFAULT '0.00',
  `total_cost` decimal(18,2) DEFAULT '0.00',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_reworks_reference` (`reference`),
  KEY `idx_reworks_date` (`rework_date`),
  KEY `idx_reworks_production` (`production_type`,`production_id`),
  KEY `idx_reworks_status` (`status`),
  KEY `fk_reworks_branch` (`branch_id`),
  KEY `fk_reworks_posted_by` (`posted_by`),
  KEY `fk_reworks_created_by` (`created_by`),
  KEY `idx_reworks_poly` (`production_type`,`production_id`),
  CONSTRAINT `fk_reworks_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_reworks_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_reworks_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_staff`
--

DROP TABLE IF EXISTS `manufacturing_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_staff` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employment_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `bvn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bank Verification Number',
  `nin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'National Identification Number',
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint DEFAULT '1' COMMENT '1=active, 0=inactive',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_manufacturing_staff_employment_id` (`employment_id`),
  KEY `idx_manufacturing_staff_branch` (`branch_id`),
  KEY `idx_manufacturing_staff_status` (`status`),
  KEY `fk_manufacturing_staff_created_by` (`created_by`),
  KEY `fk_manufacturing_staff_updated_by` (`updated_by`),
  CONSTRAINT `fk_manufacturing_staff_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_manufacturing_staff_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_manufacturing_staff_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_team_members`
--

DROP TABLE IF EXISTS `manufacturing_team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_team_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `staff_id` bigint unsigned NOT NULL COMMENT 'Links to manufacturing_staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_team_members_team_staff` (`team_id`,`staff_id`),
  KEY `idx_team_members_team` (`team_id`),
  KEY `idx_team_members_staff` (`staff_id`),
  CONSTRAINT `fk_team_members_staff` FOREIGN KEY (`staff_id`) REFERENCES `manufacturing_staff` (`id`),
  CONSTRAINT `fk_team_members_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_team_supervisors`
--

DROP TABLE IF EXISTS `manufacturing_team_supervisors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_team_supervisors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `team_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL COMMENT 'Links to employees table (ABTC Staff)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_team_supervisors_team_employee` (`team_id`,`employee_id`),
  KEY `idx_team_supervisors_team` (`team_id`),
  KEY `idx_team_supervisors_employee` (`employee_id`),
  CONSTRAINT `fk_team_supervisors_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk_team_supervisors_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturing_teams`
--

DROP TABLE IF EXISTS `manufacturing_teams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturing_teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `ledger_balance` decimal(18,2) DEFAULT '0.00' COMMENT 'For penalty tracking',
  `status` tinyint DEFAULT '1' COMMENT '1=active, 0=inactive',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_manufacturing_teams_branch` (`branch_id`),
  KEY `idx_manufacturing_teams_status` (`status`),
  KEY `fk_manufacturing_teams_created_by` (`created_by`),
  KEY `fk_manufacturing_teams_updated_by` (`updated_by`),
  CONSTRAINT `fk_manufacturing_teams_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_manufacturing_teams_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_manufacturing_teams_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `materials_requisition_items`
--

DROP TABLE IF EXISTS `materials_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materials_requisition_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `requisition_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `source_store_id` bigint unsigned NOT NULL,
  `required_qty` decimal(18,4) NOT NULL,
  `issued_qty` decimal(18,4) DEFAULT '0.0000',
  `received_qty` decimal(18,4) DEFAULT '0.0000',
  `unit_cost` decimal(18,2) DEFAULT '0.00',
  `total_cost` decimal(18,2) DEFAULT '0.00' COMMENT 'Total cost (qty * unit_cost)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_requisition_items_requisition` (`requisition_id`),
  KEY `idx_requisition_items_product` (`product_id`),
  KEY `fk_requisition_items_store` (`source_store_id`),
  CONSTRAINT `fk_requisition_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_requisition_items_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_requisition_items_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `materials_requisitions`
--

DROP TABLE IF EXISTS `materials_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materials_requisitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: MRF{YYMM}{BRANCH}{INCREMENT}',
  `requisition_date` date NOT NULL,
  `schedule_id` bigint unsigned DEFAULT NULL COMMENT 'If linked to schedule',
  `bom_id` bigint unsigned DEFAULT NULL COMMENT 'If linked directly to BOM (single product)',
  `quantity` decimal(18,4) DEFAULT '1.0000' COMMENT 'Quantity for direct BOM requisitions',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','approved','issued','received') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Requisition notes',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `issued_by` bigint unsigned DEFAULT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_requisitions_reference` (`reference`),
  KEY `idx_requisitions_date` (`requisition_date`),
  KEY `idx_requisitions_schedule` (`schedule_id`),
  KEY `idx_requisitions_bom` (`bom_id`),
  KEY `idx_requisitions_status` (`status`),
  KEY `fk_requisitions_approved_by` (`approved_by`),
  KEY `fk_requisitions_issued_by` (`issued_by`),
  KEY `fk_requisitions_received_by` (`received_by`),
  KEY `fk_requisitions_created_by` (`created_by`),
  KEY `idx_requisitions_branch_status` (`branch_id`,`status`),
  KEY `idx_requisitions_quantity` (`quantity`),
  KEY `idx_requisitions_notes` (`notes`(255)),
  CONSTRAINT `fk_requisitions_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_requisitions_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms` (`id`),
  CONSTRAINT `fk_requisitions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_requisitions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_requisitions_issued_by` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_requisitions_received_by` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_requisitions_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `daily_manufacturing_schedules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_order_items`
--

DROP TABLE IF EXISTS `production_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `production_order_id` bigint unsigned NOT NULL,
  `bom_id` bigint unsigned NOT NULL,
  `quantity_to_produce` decimal(18,4) NOT NULL COMMENT 'Qty to produce',
  `scheduled_qty` decimal(18,4) DEFAULT '0.0000' COMMENT 'Qty already scheduled',
  `produced_qty` decimal(18,4) DEFAULT '0.0000' COMMENT 'Qty actually produced',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`production_order_id`),
  KEY `idx_order_items_bom` (`bom_id`),
  CONSTRAINT `fk_order_items_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms` (`id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_order_materials`
--

DROP TABLE IF EXISTS `production_order_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_order_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `production_order_id` bigint unsigned NOT NULL,
  `production_order_item_id` bigint unsigned DEFAULT NULL COMMENT 'Nullable because materials are aggregated across items',
  `product_id` bigint unsigned NOT NULL COMMENT 'Raw material',
  `required_qty` decimal(18,4) NOT NULL,
  `source_store_id` bigint unsigned NOT NULL,
  `unit_cost` decimal(18,4) DEFAULT '0.0000',
  `total_cost` decimal(18,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_materials_order` (`production_order_id`),
  KEY `idx_order_materials_item` (`production_order_item_id`),
  KEY `idx_order_materials_product` (`product_id`),
  KEY `fk_order_materials_store` (`source_store_id`),
  CONSTRAINT `fk_order_materials_item` FOREIGN KEY (`production_order_item_id`) REFERENCES `production_order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_materials_order` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_order_materials_store` FOREIGN KEY (`source_store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_orders`
--

DROP TABLE IF EXISTS `production_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: PRO{YYMM}{BRANCH}{INCREMENT}',
  `order_date` date DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','closed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `total_finish_goods_qty` decimal(18,4) DEFAULT '0.0000',
  `processed_qty` decimal(18,4) DEFAULT '0.0000' COMMENT 'Updated by schedules',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint unsigned DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_production_orders_reference` (`reference`),
  KEY `idx_production_orders_status` (`status`),
  KEY `idx_production_orders_branch` (`branch_id`),
  KEY `idx_production_orders_dates` (`start_date`,`end_date`),
  KEY `fk_production_orders_approved_by` (`approved_by`),
  KEY `fk_production_orders_closed_by` (`closed_by`),
  KEY `fk_production_orders_created_by` (`created_by`),
  CONSTRAINT `fk_production_orders_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_production_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_production_orders_closed_by` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_production_orders_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `single_product_manufacturing`
--

DROP TABLE IF EXISTS `single_product_manufacturing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `single_product_manufacturing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: SPM{YYMM}{BRANCH}{INCREMENT}',
  `manufacturing_date` date NOT NULL,
  `requisition_id` bigint unsigned NOT NULL COMMENT 'Must be single BOM requisition',
  `batch_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: BATCH{YYYYMMDD}{INCREMENT}',
  `team_id` bigint unsigned NOT NULL,
  `machine_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `bom_id` bigint unsigned NOT NULL,
  `finish_product_id` bigint unsigned NOT NULL,
  `output_store_id` bigint unsigned NOT NULL,
  `total_material_cost` decimal(18,2) DEFAULT '0.00',
  `total_other_cost` decimal(18,2) DEFAULT '0.00',
  `total_cost` decimal(18,2) DEFAULT '0.00',
  `unit_cost` decimal(18,2) DEFAULT '0.00',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Manufacturing notes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_single_manufacturing_reference` (`reference`),
  UNIQUE KEY `uk_single_manufacturing_batch` (`batch_number`),
  KEY `idx_single_manufacturing_date` (`manufacturing_date`),
  KEY `idx_single_manufacturing_status` (`status`),
  KEY `idx_single_manufacturing_team` (`team_id`),
  KEY `fk_single_manufacturing_requisition` (`requisition_id`),
  KEY `fk_single_manufacturing_machine` (`machine_id`),
  KEY `fk_single_manufacturing_bom` (`bom_id`),
  KEY `fk_single_manufacturing_product` (`finish_product_id`),
  KEY `fk_single_manufacturing_store` (`output_store_id`),
  KEY `fk_single_manufacturing_branch` (`branch_id`),
  KEY `fk_single_manufacturing_posted_by` (`posted_by`),
  KEY `fk_single_manufacturing_created_by` (`created_by`),
  CONSTRAINT `fk_single_manufacturing_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms` (`id`),
  CONSTRAINT `fk_single_manufacturing_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_single_manufacturing_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_single_manufacturing_machine` FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines` (`id`),
  CONSTRAINT `fk_single_manufacturing_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_single_manufacturing_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_single_manufacturing_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions` (`id`),
  CONSTRAINT `fk_single_manufacturing_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores` (`id`),
  CONSTRAINT `fk_single_manufacturing_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `single_product_manufacturing_materials`
--

DROP TABLE IF EXISTS `single_product_manufacturing_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `single_product_manufacturing_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `manufacturing_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_single_mfg_materials_manufacturing` (`manufacturing_id`),
  KEY `idx_single_mfg_materials_product` (`product_id`),
  KEY `fk_single_mfg_materials_store` (`store_id`),
  CONSTRAINT `fk_single_mfg_materials_manufacturing` FOREIGN KEY (`manufacturing_id`) REFERENCES `single_product_manufacturing` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_single_mfg_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_single_mfg_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-05 22:42:22

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
