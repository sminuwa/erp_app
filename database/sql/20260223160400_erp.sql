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
-- Table structure for table `area_managers`
--

DROP TABLE IF EXISTS `area_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area_managers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `manager_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `manager_id` (`manager_id`),
  KEY `branch_id` (`branch_id`),
  KEY `status` (`status`),
  CONSTRAINT `area_managers_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`),
  CONSTRAINT `area_managers_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `action` varchar(200) NOT NULL,
  `roles` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user` (`user_id`),
  KEY `role` (`roles`)
) ENGINE=InnoDB AUTO_INCREMENT=506872 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `account_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `account_type` enum('Current','Savings','Credit','Domiciliary','Cash') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_no` (`account_no`,`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_branches`
--

DROP TABLE IF EXISTS `bank_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sortcode` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bank_branches_name_unique` (`name`),
  KEY `bank_branches_bank_id_foreign` (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bank_transactions`
--

DROP TABLE IF EXISTS `bank_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_transactions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `bank_account_id` bigint NOT NULL,
  `trans_date` date NOT NULL,
  `cr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `dr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `ref_no` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `trans_date` (`trans_date`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `ref_no` (`ref_no`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `banks_name_unique` (`name`),
  UNIQUE KEY `banks_abbreviation_unique` (`abbreviation`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `labor_cost` decimal(18,2) DEFAULT '0.00',
  `power_cost` decimal(18,2) DEFAULT '0.00',
  `other_cost` decimal(18,2) DEFAULT '0.00',
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branch_product_prices`
--

DROP TABLE IF EXISTS `branch_product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_product_prices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `branch_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `selling_price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `cost_price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `retail_selling_price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `whole_selling_price` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_id_2` (`branch_id`,`product_id`),
  KEY `branch_id` (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24304 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `long_name` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budget_expenditures`
--

DROP TABLE IF EXISTS `budget_expenditures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budget_expenditures` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `branch_id` bigint DEFAULT NULL,
  `general_account_id` bigint DEFAULT NULL,
  `budget_year` year NOT NULL,
  `proposed_budget` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `category_id` (`general_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1373 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budgets` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `branch_id` bigint DEFAULT NULL,
  `category_id` bigint DEFAULT NULL,
  `budget_year` year NOT NULL,
  `proposed_budget` decimal(10,2) DEFAULT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') NOT NULL,
  `month1` decimal(10,2) DEFAULT NULL,
  `month2` decimal(10,2) DEFAULT NULL,
  `month3` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3973 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cash_movements`
--

DROP TABLE IF EXISTS `cash_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(18,2) NOT NULL,
  `source_account_id` bigint unsigned NOT NULL,
  `destination_account_id` bigint unsigned NOT NULL,
  `withdraw_by` bigint unsigned NOT NULL,
  `deposited_by` bigint unsigned NOT NULL,
  `sent_by` bigint NOT NULL,
  `type` enum('Deposit','Withdraw','Both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Deposit',
  `slip_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_withdraw` date DEFAULT NULL,
  `date_deposit` date DEFAULT NULL,
  `captured_by` bigint NOT NULL,
  `source_balance_before` decimal(18,2) DEFAULT '0.00',
  `destination_balance_before` decimal(18,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_movements_source_account_id_foreign` (`source_account_id`),
  KEY `cash_movements_destination_account_id_foreign` (`destination_account_id`),
  KEY `cash_movements_withdraw_by_foreign` (`withdraw_by`),
  KEY `cash_movements_deposited_by_foreign` (`deposited_by`),
  KEY `slip_no` (`slip_no`,`date_withdraw`,`date_deposit`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_account` bigint unsigned NOT NULL,
  `cost_account` bigint unsigned NOT NULL,
  `revenue_account` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_2` (`code`),
  KEY `code` (`code`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ch_favorites`
--

DROP TABLE IF EXISTS `ch_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ch_favorites` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `favorite_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ch_messages`
--

DROP TABLE IF EXISTS `ch_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ch_messages` (
  `id` bigint NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `from_id` bigint NOT NULL,
  `to_id` bigint NOT NULL,
  `body` varchar(5000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `prefix` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_limits`
--

DROP TABLE IF EXISTS `credit_limits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_limits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_limits_customer_id_foreign` (`customer_id`),
  KEY `credit_limits_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_note_details`
--

DROP TABLE IF EXISTS `credit_note_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_note_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_id` bigint unsigned NOT NULL,
  `store_product_id` bigint unsigned NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `original_quantity_sold` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `sold_price` double(18,2) NOT NULL,
  `total` double(18,2) NOT NULL,
  `selling_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `cost_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `avail_qty_before_sale` decimal(50,6) NOT NULL DEFAULT '0.000000' COMMENT 'This is a qty available in stock before current sale',
  `status` tinyint(1) DEFAULT '1',
  `last_modified_by` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`credit_note_id`),
  KEY `order_details_product_id_foreign` (`store_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=733 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `customer_id` bigint NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `comment` text,
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_ledgers`
--

DROP TABLE IF EXISTS `customer_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `order_id` bigint NOT NULL DEFAULT '0',
  `systemid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Nil',
  `cr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `dr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `payment_mode` enum('Cash','Credit','Cheque') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Cash',
  `bank_account_id` bigint DEFAULT '0',
  `teller_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `customer_ledgers_customer_id_foreign` (`customer_id`),
  KEY `systemid` (`systemid`),
  KEY `ref` (`ref`),
  KEY `dr` (`dr`),
  KEY `date` (`date`),
  KEY `payment_mode` (`payment_mode`),
  KEY `teller_no` (`teller_no`),
  KEY `order_id` (`order_id`),
  KEY `cr` (`cr`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=716 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_age` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT '0.00',
  `credit_limit` decimal(18,2) DEFAULT '0.00',
  `type` enum('Wholesale','Retail') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Retail',
  `branch_id` bigint NOT NULL,
  `relation_officer` bigint unsigned DEFAULT NULL,
  `referred_by` bigint unsigned DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customers_name_index` (`name`),
  KEY `email` (`email`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=19081 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `debit_notes`
--

DROP TABLE IF EXISTS `debit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `debit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `comment` text,
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
-- Table structure for table `expense_items`
--

DROP TABLE IF EXISTS `expense_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_items_name_unique` (`name`),
  UNIQUE KEY `expense_items_code_unique` (`code`),
  KEY `expense_items_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_item_id` bigint unsigned NOT NULL,
  `captured_by` bigint unsigned NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `month` int DEFAULT NULL,
  `year` year DEFAULT NULL,
  `day` int DEFAULT NULL,
  `payment_mode` enum('Cash','Cheque') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `impress` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `reason` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('Completed','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_account_controls`
--

DROP TABLE IF EXISTS `general_account_controls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_account_controls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `general_account_id` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `general_account_id` (`general_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_account_ledgers`
--

DROP TABLE IF EXISTS `general_account_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_account_ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_id` bigint unsigned NOT NULL,
  `model_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint NOT NULL DEFAULT '0',
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Nil',
  `credit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.00',
  `debit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `order_by` int DEFAULT '1' COMMENT 'This is to specify the ordering based on credit or debit. 2 for debit, 1 for credit.',
  `user_id` bigint NOT NULL DEFAULT '0',
  `receipt_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ref` (`reference`),
  KEY `dr` (`debit`),
  KEY `date` (`date`),
  KEY `model_id` (`model_id`),
  KEY `model_id_2` (`model_id`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=832546 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `general_accounts`
--

DROP TABLE IF EXISTS `general_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(30) NOT NULL,
  `number` varchar(30) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `is_control` int DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1240 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inter_banks`
--

DROP TABLE IF EXISTS `inter_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inter_banks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `source_account_id` bigint NOT NULL,
  `destination_account_id` bigint NOT NULL,
  `created_by` bigint NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `intersite_transfer_products`
--

DROP TABLE IF EXISTS `intersite_transfer_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intersite_transfer_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `intersite_transfer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `cost_price` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4950 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `intersite_transfer_receives`
--

DROP TABLE IF EXISTS `intersite_transfer_receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intersite_transfer_receives` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `intersite_transfer_id` bigint unsigned NOT NULL,
  `source_store_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `cost_price` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4614 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `intersite_transfers`
--

DROP TABLE IF EXISTS `intersite_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intersite_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `source_branch_id` bigint unsigned NOT NULL,
  `destination_branch_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `vehicle_no` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0=pending, 1=posted, 2=received',
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1389 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interstore_transfer_details`
--

DROP TABLE IF EXISTS `interstore_transfer_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interstore_transfer_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `interstore_transfer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `source_store_id` bigint unsigned NOT NULL,
  `destination_store_id` bigint unsigned NOT NULL,
  `quantity` decimal(50,6) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3081 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `interstore_transfers`
--

DROP TABLE IF EXISTS `interstore_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interstore_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `date` date DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1381 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journal_items`
--

DROP TABLE IF EXISTS `journal_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned NOT NULL,
  `account_type` varchar(100) NOT NULL,
  `credit` decimal(20,2) NOT NULL DEFAULT '0.00',
  `debit` decimal(20,2) NOT NULL DEFAULT '0.00',
  `description` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `journal_id` (`journal_id`,`account_id`),
  KEY `account_id` (`account_id`),
  KEY `credit` (`credit`),
  KEY `debit` (`debit`)
) ENGINE=InnoDB AUTO_INCREMENT=5675 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `branch_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loan_collectors`
--

DROP TABLE IF EXISTS `loan_collectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_collectors` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `reg_code` varchar(10) NOT NULL,
  `registered_by` bigint NOT NULL,
  `branch_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_code_2` (`reg_code`),
  KEY `name` (`name`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `reg_code` (`reg_code`),
  KEY `registered_by` (`registered_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loan_payments`
--

DROP TABLE IF EXISTS `loan_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_payments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `loan_id` bigint NOT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `payment_mode` enum('Cash','Cheque') NOT NULL DEFAULT 'Cash',
  `bank_account_id` bigint NOT NULL,
  `cheque_no` varchar(20) DEFAULT NULL,
  `receipt_no` varchar(20) NOT NULL,
  `received_by` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`),
  KEY `payment_mode` (`payment_mode`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `cheque_no` (`cheque_no`),
  KEY `receipt_no` (`receipt_no`),
  KEY `received_by` (`received_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `loan_collector_id` bigint NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_mode` enum('Cash','Cheque') NOT NULL DEFAULT 'Cash',
  `bank_account_id` bigint NOT NULL,
  `date` date NOT NULL,
  `granted_by` bigint NOT NULL,
  `receipt_no` varchar(20) NOT NULL,
  `due_date` date NOT NULL,
  `amount_paid` decimal(18,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(18,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `loan_collector_id` (`loan_collector_id`),
  KEY `payment_mode` (`payment_mode`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `granted_by` (`granted_by`),
  KEY `receipt_no` (`receipt_no`),
  KEY `due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `status` enum('pending','posted','reversed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `reason` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `user_id` bigint unsigned NOT NULL COMMENT 'Links to users table (ABTC Staff)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_team_supervisors_team_employee` (`team_id`,`user_id`),
  KEY `idx_team_supervisors_team` (`team_id`),
  KEY `idx_team_supervisors_employee` (`user_id`),
  CONSTRAINT `fk_team_supervisors_employee` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_team_supervisors_team` FOREIGN KEY (`team_id`) REFERENCES `manufacturing_teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'App\\Models\\User',
  `model_id` bigint unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'App\\Models\\User',
  `model_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `role_id` (`role_id`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `new_products`
--

DROP TABLE IF EXISTS `new_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `new_products` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `store_product_id` bigint unsigned NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(50,6) NOT NULL,
  `original_quantity_sold` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `sold_price` double(50,6) NOT NULL,
  `total` double(50,6) NOT NULL,
  `selling_price` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `cost_price` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `avail_qty_before_sale` decimal(50,6) NOT NULL COMMENT 'This is a qty available in stock before current sale',
  `status` tinyint(1) DEFAULT '1',
  `last_modified_by` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`store_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=154228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_invoice_details`
--

DROP TABLE IF EXISTS `order_invoice_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoice_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint DEFAULT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `last_modified_by` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`product_id`),
  CONSTRAINT `order_invoice_details_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `order_invoices` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_invoices`
--

DROP TABLE IF EXISTS `order_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `customer_id` bigint unsigned NOT NULL,
  `order_date` datetime NOT NULL,
  `order_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` int NOT NULL,
  `total` double(18,2) NOT NULL,
  `invoice_no` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sold_by` bigint DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `order_date` (`order_date`,`invoice_no`,`branch_id`),
  KEY `reference_2` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_receipt_trackings`
--

DROP TABLE IF EXISTS `order_receipt_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_receipt_trackings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `receipt_id` bigint unsigned NOT NULL,
  `applied_amount` decimal(18,2) NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `applied_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `applied_by` (`applied_by`),
  KEY `idx_order_receipt` (`order_id`,`receipt_id`),
  KEY `idx_receipt_id` (`receipt_id`),
  KEY `idx_branch_id` (`branch_id`),
  CONSTRAINT `order_receipt_trackings_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_receipt_trackings_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_receipt_trackings_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_receipt_trackings_ibfk_4` FOREIGN KEY (`applied_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_invoice_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `order_date` datetime NOT NULL,
  `order_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` int NOT NULL,
  `sub_total` double(18,2) NOT NULL,
  `vat` double(8,2) NOT NULL,
  `total` double(18,2) NOT NULL,
  `pay` double(18,2) DEFAULT NULL,
  `due` double(18,2) DEFAULT NULL,
  `discount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `refund` decimal(18,2) NOT NULL DEFAULT '0.00',
  `payment_mode` enum('Cash','Cheque','Credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Cash',
  `due_date` datetime DEFAULT NULL,
  `payment_status` enum('Paid','Not Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Paid',
  `invoice_no` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sold_by` bigint DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `issued_by` bigint DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `comment` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'comment my store keeper',
  `branch_id` bigint NOT NULL,
  `show_vat` tinyint DEFAULT '0',
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `order_date` (`order_date`,`due_date`,`invoice_no`,`branch_id`),
  KEY `reference_2` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=85003 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_modes`
--

DROP TABLE IF EXISTS `payment_modes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_modes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `receipt_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint NOT NULL,
  `model_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `charged_account_id` bigint NOT NULL,
  `charged_account_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26347 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pending_inventories`
--

DROP TABLE IF EXISTS `pending_inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pending_inventories` (
  `code` varchar(9) DEFAULT NULL,
  `description` varchar(30) DEFAULT NULL,
  `quantity` decimal(7,4) DEFAULT NULL,
  `Unit` varchar(3) DEFAULT NULL,
  `cost_price` varchar(9) DEFAULT NULL,
  `store` varchar(5) DEFAULT NULL,
  `retail_selling_price` int DEFAULT NULL,
  `whole_selling_price` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `period_settings`
--

DROP TABLE IF EXISTS `period_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `period_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `period_open` date DEFAULT NULL,
  `period_close` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=783 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions_backup`
--

DROP TABLE IF EXISTS `permissions_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions_backup` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=691 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `200` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_expire_settings`
--

DROP TABLE IF EXISTS `product_expire_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_expire_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `no_of_days` int NOT NULL DEFAULT '120',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_foreign` (`user_id`),
  KEY `product_foreign` (`product_id`),
  CONSTRAINT `product_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users1` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_new`
--

DROP TABLE IF EXISTS `product_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_new` (
  `code` varchar(9) DEFAULT NULL,
  `name` varchar(37) DEFAULT NULL,
  `category` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_unit_measures`
--

DROP TABLE IF EXISTS `product_unit_measures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_unit_measures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('division','multiple') NOT NULL,
  `value` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_valuations`
--

DROP TABLE IF EXISTS `product_valuations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_valuations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) DEFAULT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` varchar(100) NOT NULL,
  `cost_price` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7662 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `information` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `category_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `barcode` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_status` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `name` (`name`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18832 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products_template`
--

DROP TABLE IF EXISTS `products_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_template` (
  `code` varchar(9) DEFAULT NULL,
  `name` varchar(34) DEFAULT NULL,
  `unit_of_measure` varchar(3) DEFAULT NULL,
  `category_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products_upload`
--

DROP TABLE IF EXISTS `products_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_upload` (
  `code` varchar(7) DEFAULT NULL,
  `name` varchar(37) DEFAULT NULL,
  `information` varchar(10) DEFAULT NULL,
  `unit` varchar(3) DEFAULT NULL,
  `category` varchar(3) DEFAULT NULL,
  `company` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformer_details`
--

DROP TABLE IF EXISTS `proformer_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proformer_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `unit_cost` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) DEFAULT '1',
  `last_modified_by` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proformers`
--

DROP TABLE IF EXISTS `proformers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proformers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `order_date` datetime NOT NULL,
  `order_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_products` int NOT NULL,
  `total` double(18,2) NOT NULL,
  `discount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `payment_mode` enum('Cash','Cheque','Credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Cash',
  `due_date` datetime DEFAULT NULL,
  `payment_status` enum('Paid','Not Paid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Paid',
  `invoice_no` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sold_by` bigint DEFAULT NULL,
  `modified_by` int DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `order_date` (`order_date`,`due_date`,`invoice_no`,`branch_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_expenses`
--

DROP TABLE IF EXISTS `purchase_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) DEFAULT NULL,
  `purchase_id` bigint unsigned NOT NULL,
  `supplier_id` bigint NOT NULL,
  `name` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint unsigned NOT NULL,
  `updated_by` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1371 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_product_requests`
--

DROP TABLE IF EXISTS `purchase_product_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_product_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `purchase_id` bigint NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `expire_date` date DEFAULT NULL,
  `unit_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=764 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_products`
--

DROP TABLE IF EXISTS `purchase_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint NOT NULL,
  `quantity` decimal(20,6) NOT NULL,
  `expire_date` date DEFAULT NULL,
  `unit_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  KEY `purchase_id_2` (`purchase_id`,`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8579 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchase_requests`
--

DROP TABLE IF EXISTS `purchase_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `invoice` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` datetime NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `wbno` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `waybill_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_reg_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `reference_2` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `atc_no` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` datetime NOT NULL,
  `purchase_mode` enum('Cash','Credit','Cash/Credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `purchase_request_id` bigint unsigned DEFAULT NULL,
  `wbno` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `waybill_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter_phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_updated_by_foreign` (`updated_by`),
  KEY `invoice` (`atc_no`,`purchase_date`,`wbno`,`waybill_no`),
  KEY `reference_2` (`reference`)
) ENGINE=InnoDB AUTO_INCREMENT=2473 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `receipts`
--

DROP TABLE IF EXISTS `receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receipts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `remaining_balance` decimal(50,2) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `receipt_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint NOT NULL,
  `model_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `charged_account_id` bigint NOT NULL,
  `charged_account_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `receipt_no_2` (`receipt_no`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=100269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_debit_items`
--

DROP TABLE IF EXISTS `return_debit_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_debit_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_debit_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint NOT NULL,
  `current_quantity` int NOT NULL,
  `original_quantity_purchased` int NOT NULL DEFAULT '0',
  `current_unit_cost` double(18,2) NOT NULL,
  `original_unit_cost` decimal(18,2) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`return_debit_id`),
  KEY `order_details_product_id_foreign` (`product_id`),
  CONSTRAINT `return_debit_items_ibfk_1` FOREIGN KEY (`return_debit_id`) REFERENCES `return_debits` (`id`),
  CONSTRAINT `return_debit_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `return_debits`
--

DROP TABLE IF EXISTS `return_debits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_debits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint unsigned NOT NULL,
  `reference` varchar(100) NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `comment` text,
  `date` date DEFAULT NULL,
  `created_by` bigint NOT NULL,
  `posted_by` bigint NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `purchase_id` (`purchase_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `reference_2` (`reference`),
  CONSTRAINT `return_debits_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
  CONSTRAINT `return_debits_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ro_budgets`
--

DROP TABLE IF EXISTS `ro_budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ro_budgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `staff_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `budget_year` year NOT NULL,
  `quarter` enum('Q1','Q2','Q3','Q4') NOT NULL,
  `month1` decimal(10,2) DEFAULT NULL,
  `month2` decimal(10,2) DEFAULT NULL,
  `month3` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `category_id` (`category_id`),
  KEY `ro_budgets_ibfk_1` (`staff_id`),
  KEY `budget_year` (`budget_year`,`quarter`,`month1`,`month2`,`month3`),
  CONSTRAINT `ro_budgets_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ro_budgets_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `ro_budgets_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sals_edit_logs`
--

DROP TABLE IF EXISTS `sals_edit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sals_edit_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `order_detail_id` bigint NOT NULL,
  `edited_qty` int NOT NULL,
  `user_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abc',
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'xyz',
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abcd@gmail.com',
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'logo.png',
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kaduna',
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nigeria',
  `branch_id` bigint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopping_carts`
--

DROP TABLE IF EXISTS `shopping_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shopping_carts` (
  `identifier` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `instance` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `labor_cost` decimal(18,2) DEFAULT '0.00',
  `power_cost` decimal(18,2) DEFAULT '0.00',
  `other_cost` decimal(18,2) DEFAULT '0.00',
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_adjustment_details`
--

DROP TABLE IF EXISTS `stock_adjustment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustment_details` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_adjustment_id` bigint unsigned NOT NULL,
  `store_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `quantity` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `cost_price` decimal(50,6) NOT NULL DEFAULT '0.000000',
  `expiry_date` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9861 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_adjustments`
--

DROP TABLE IF EXISTS `stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustments` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) DEFAULT NULL,
  `branch_id` bigint NOT NULL,
  `date` date NOT NULL,
  `operation` enum('in','out') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_by` bigint DEFAULT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_balances`
--

DROP TABLE IF EXISTS `stock_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_balances` (
  `store` varchar(5) DEFAULT NULL,
  `product` varchar(9) DEFAULT NULL,
  `quantity` varchar(10) DEFAULT NULL,
  `cost_price` decimal(8,2) DEFAULT NULL,
  `retail_selling_price` decimal(8,2) DEFAULT NULL,
  `whole_selling_price` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_cards`
--

DROP TABLE IF EXISTS `stock_cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_cards` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `cr` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `dr` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `qty_before` decimal(10,6) DEFAULT '0.000000',
  `qty_after` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `cost` varchar(100) DEFAULT NULL,
  `refno` varchar(20) NOT NULL,
  `type` int NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `charged_account` varchar(100) DEFAULT NULL,
  `user_id` bigint NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `priority` int NOT NULL DEFAULT '2' COMMENT 'Display on the report: 1= opening balance, 2= Purchase, 3=Sale, 4 = Transfer, 5 = Adjustment',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `refno` (`refno`)
) ENGINE=InnoDB AUTO_INCREMENT=172802 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_product_balances`
--

DROP TABLE IF EXISTS `store_product_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_product_balances` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `qty` int NOT NULL,
  `store_product_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `date` (`date`,`store_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_product_batches`
--

DROP TABLE IF EXISTS `store_product_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_product_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_no` varchar(100) NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `existing_quantity` varchar(100) DEFAULT NULL,
  `new_quantity` varchar(100) DEFAULT NULL,
  `total_quantity` varchar(100) DEFAULT NULL,
  `existing_cost_price` varchar(100) DEFAULT NULL,
  `new_cost_price` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_no` (`batch_no`,`store_id`,`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_product_prices`
--

DROP TABLE IF EXISTS `store_product_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_product_prices` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `cost_price` decimal(12,2) DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_product_prices_product_id_foreign` (`product_id`),
  KEY `store_product_prices_updated_by_foreign` (`updated_by`),
  KEY `store_id` (`store_id`),
  KEY `store_id_2` (`store_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `store_products`
--

DROP TABLE IF EXISTS `store_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `qty_available` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_id` (`store_id`,`product_id`),
  UNIQUE KEY `store_id_2` (`store_id`,`product_id`),
  KEY `store_products_store_id_foreign` (`store_id`),
  KEY `store_products_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33519 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stores_branch_id_foreign` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `supplier_ledgers`
--

DROP TABLE IF EXISTS `supplier_ledgers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_id` bigint NOT NULL,
  `description` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Ref` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `dr` decimal(18,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `payment_mode` enum('Credit','Cash','Cheque','Credit Note') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_id` bigint DEFAULT NULL,
  `teller_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_ledgers_supplier_id_ref_cr_dr_date_index` (`supplier_id`,`Ref`,`cr`,`dr`,`date`),
  KEY `bank_account_id` (`bank_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_holder` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` enum('Current','Savings') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint DEFAULT NULL,
  `opening_balance` decimal(18,2) NOT NULL DEFAULT '0.00',
  `category` int NOT NULL DEFAULT '0' COMMENT '0=Market, 1=trasporters',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_bank_id_foreign` (`bank_id`)
) ENGINE=InnoDB AUTO_INCREMENT=517 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_purchase_products`
--

DROP TABLE IF EXISTS `temp_purchase_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_purchase_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `expire_date` date DEFAULT NULL,
  `unit_price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `purchase_id_2` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `temp_purchases`
--

DROP TABLE IF EXISTS `temp_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `temp_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `invoice` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` datetime NOT NULL,
  `purchase_mode` enum('Cash','Credit','Cash/Credit') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash',
  `wbno` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_store_id` bigint unsigned NOT NULL,
  `destination_store_id` bigint unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `waybill_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_reg_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transporter` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_supplier_id_foreign` (`supplier_id`),
  KEY `purchases_source_store_id_foreign` (`source_store_id`),
  KEY `purchases_destination_store_id_foreign` (`destination_store_id`),
  KEY `purchases_updated_by_foreign` (`updated_by`),
  KEY `invoice` (`invoice`,`purchase_date`,`wbno`,`source_store_id`,`destination_store_id`,`waybill_no`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transfer_products`
--

DROP TABLE IF EXISTS `transfer_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transfer_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_id` bigint DEFAULT '0',
  `transfer_id` bigint DEFAULT '0',
  `source_store_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `destination_store_id` bigint unsigned NOT NULL,
  `qty_transfered` int NOT NULL DEFAULT '0',
  `qty_available` int DEFAULT '0' COMMENT 'This is qty available before tranfer',
  `transfered_by` bigint unsigned NOT NULL,
  `nature` enum('Transfer','Sale','Purchase') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sale',
  `stock_in_out` enum('in','out') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'out',
  `refno` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_no` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_date` date DEFAULT NULL,
  `type` enum('interstore','intersite') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'interstore',
  `status` enum('Completed','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_products_source_store_id_foreign` (`source_store_id`),
  KEY `transfer_products_product_id_foreign` (`product_id`),
  KEY `transfer_products_destination_store_id_foreign` (`destination_store_id`),
  KEY `transfer_date` (`transfer_date`),
  KEY `refno` (`refno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_access_sites`
--

DROP TABLE IF EXISTS `user_access_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_access_sites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `branch_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `company_id` (`branch_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `user_access_sites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_access_sites_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1272 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `othernames` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personnel_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `cadre_id` int DEFAULT NULL,
  `signature` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `photo_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_old` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `contact_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_qualification` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highest_qualification` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_of_origin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_of_residence` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `active_status` int NOT NULL DEFAULT '1',
  `avatar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dark_mode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `messenger_color` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sale_representative` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date_range_start` date DEFAULT NULL,
  `date_range_end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`(191)),
  KEY `position_id` (`position_id`),
  KEY `position_id_2` (`position_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1630927302530 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-23 16:04:22
