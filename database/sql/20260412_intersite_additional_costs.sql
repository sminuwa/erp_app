-- Intersite Additional Costs tables and permissions
-- Idempotent: safe to run multiple times

-- 1. CREATE intersite_additional_costs TABLE (Safe via IF NOT EXISTS)
CREATE TABLE IF NOT EXISTS `intersite_additional_costs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `intersite_transfer_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `cost_mode` enum('by_amount','by_qty') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'by_amount',
  `amount` decimal(15,2) NOT NULL,
  `date` date NOT NULL,
  `truck_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `waybill_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `branch_id` bigint unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_by` bigint unsigned NOT NULL,
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `intersite_additional_costs_reference_unique` (`reference`),
  KEY `intersite_additional_costs_intersite_transfer_id_index` (`intersite_transfer_id`),
  KEY `intersite_additional_costs_supplier_id_index` (`supplier_id`),
  KEY `intersite_additional_costs_branch_id_index` (`branch_id`),
  KEY `intersite_additional_costs_status_index` (`status`),
  CONSTRAINT `intersite_additional_costs_intersite_transfer_id_foreign` FOREIGN KEY (`intersite_transfer_id`) REFERENCES `intersite_transfers` (`id`),
  CONSTRAINT `intersite_additional_costs_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `intersite_additional_costs_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  CONSTRAINT `intersite_additional_costs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `intersite_additional_costs_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CREATE intersite_additional_cost_items TABLE (Safe via IF NOT EXISTS)
CREATE TABLE IF NOT EXISTS `intersite_additional_cost_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `intersite_additional_cost_id` bigint unsigned NOT NULL,
  `intersite_transfer_product_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` decimal(20,6) NOT NULL,
  `original_cost` decimal(15,2) NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `additional_unit_cost` decimal(15,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `iac_items_cost_id_index` (`intersite_additional_cost_id`),
  KEY `intersite_additional_cost_items_product_id_index` (`product_id`),
  CONSTRAINT `iac_items_cost_id_foreign` FOREIGN KEY (`intersite_additional_cost_id`) REFERENCES `intersite_additional_costs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `iac_items_transfer_product_id_foreign` FOREIGN KEY (`intersite_transfer_product_id`) REFERENCES `intersite_transfer_products` (`id`),
  CONSTRAINT `intersite_additional_cost_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. PERMISSIONS (Safe via INSERT IGNORE)
INSERT IGNORE INTO `permissions` (`name`, `description`, `guard_name`, `active`, `created_at`, `updated_at`) VALUES
('intersite.additional_cost.index', 'View Intersite Additional Costs', 'web', 1, NOW(), NOW()),
('intersite.additional_cost.create', 'Create Intersite Additional Cost', 'web', 1, NOW(), NOW()),
('intersite.additional_cost.show', 'View Intersite Additional Cost Details', 'web', 1, NOW(), NOW()),
('intersite.additional_cost.edit', 'Edit Intersite Additional Cost', 'web', 1, NOW(), NOW()),
('intersite.additional_cost.post', 'Post/Reverse Intersite Additional Cost', 'web', 1, NOW(), NOW()),
('intersite.additional_cost.delete', 'Delete Intersite Additional Cost', 'web', 1, NOW(), NOW());
