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
-- Table structure for table `batch_conversions`
--

DROP TABLE IF EXISTS `batch_conversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch_conversions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: BCN{YYMM}{BRANCH}{INCREMENT}',
  `conversion_date` date NOT NULL,
  `batch_production_id` bigint unsigned NOT NULL,
  `produced_qty` decimal(18,4) NOT NULL,
  `finish_product_id` bigint unsigned NOT NULL,
  `output_store_id` bigint unsigned NOT NULL,
  `wip_cost_deducted` decimal(18,2) NOT NULL COMMENT 'Cost moved from WIP',
  `labor_cost` decimal(18,2) DEFAULT '0.00',
  `power_cost` decimal(18,2) DEFAULT '0.00',
  `other_cost` decimal(18,2) DEFAULT '0.00',
  `total_cost` decimal(18,2) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_batch_conversions_reference` (`reference`),
  KEY `idx_batch_conversions_date` (`conversion_date`),
  KEY `idx_batch_conversions_batch` (`batch_production_id`),
  KEY `idx_batch_conversions_status` (`status`),
  KEY `fk_batch_conversions_product` (`finish_product_id`),
  KEY `fk_batch_conversions_store` (`output_store_id`),
  KEY `fk_batch_conversions_branch` (`branch_id`),
  KEY `fk_batch_conversions_posted_by` (`posted_by`),
  KEY `fk_batch_conversions_created_by` (`created_by`),
  CONSTRAINT `fk_batch_conversions_batch` FOREIGN KEY (`batch_production_id`) REFERENCES `batch_productions` (`id`),
  CONSTRAINT `fk_batch_conversions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_batch_conversions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_batch_conversions_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_batch_conversions_product` FOREIGN KEY (`finish_product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_batch_conversions_store` FOREIGN KEY (`output_store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batch_production_materials`
--

DROP TABLE IF EXISTS `batch_production_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch_production_materials` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_production_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(18,4) NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_batch_materials_batch` (`batch_production_id`),
  KEY `idx_batch_materials_product` (`product_id`),
  KEY `fk_batch_materials_store` (`store_id`),
  CONSTRAINT `fk_batch_materials_batch` FOREIGN KEY (`batch_production_id`) REFERENCES `batch_productions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_batch_materials_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `fk_batch_materials_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `batch_productions`
--

DROP TABLE IF EXISTS `batch_productions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batch_productions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: BPC{YYMM}{BRANCH}{INCREMENT}',
  `production_date` date NOT NULL,
  `requisition_id` bigint unsigned NOT NULL COMMENT 'Must be batch BOM requisition',
  `batch_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto: BATCH{YYYYMMDD}{INCREMENT}',
  `team_id` bigint unsigned NOT NULL,
  `machine_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(18,4) DEFAULT '1.0000' COMMENT 'Default 1',
  `bom_id` bigint unsigned NOT NULL,
  `total_material_cost` decimal(18,2) DEFAULT '0.00',
  `wip_value` decimal(18,2) DEFAULT '0.00' COMMENT 'Work in Progress value',
  `converted_qty` decimal(18,4) DEFAULT '0.0000' COMMENT 'Qty converted to finished goods',
  `remaining_qty` decimal(18,4) DEFAULT '1.0000' COMMENT 'Qty remaining in WIP',
  `branch_id` bigint unsigned NOT NULL,
  `status` enum('pending','posted','fully_converted') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Production notes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_batch_productions_reference` (`reference`),
  UNIQUE KEY `uk_batch_productions_batch` (`batch_number`),
  KEY `idx_batch_productions_date` (`production_date`),
  KEY `idx_batch_productions_status` (`status`),
  KEY `idx_batch_productions_team` (`team_id`),
  KEY `fk_batch_productions_requisition` (`requisition_id`),
  KEY `fk_batch_productions_machine` (`machine_id`),
  KEY `fk_batch_productions_bom` (`bom_id`),
  KEY `fk_batch_productions_posted_by` (`posted_by`),
  KEY `fk_batch_productions_created_by` (`created_by`),
  KEY `idx_batch_productions_branch_status` (`branch_id`,`status`),
  CONSTRAINT `fk_batch_productions_bom` FOREIGN KEY (`bom_id`) REFERENCES `manufacturing_boms` (`id`),
  CONSTRAINT `fk_batch_productions_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `fk_batch_productions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_batch_productions_machine` FOREIGN KEY (`machine_id`) REFERENCES `manufacturing_machines` (`id`),
  CONSTRAINT `fk_batch_productions_posted_by` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_batch_productions_requisition` FOREIGN KEY (`requisition_id`) REFERENCES `materials_requisitions` (`id`),
  CONSTRAINT `fk_batch_productions_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`)
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

-- Dump completed on 2026-02-08 17:01:30
