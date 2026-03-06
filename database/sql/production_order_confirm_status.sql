-- Add confirmed_by and confirmed_at columns to production_orders table
-- New status flow: pending -> confirmed -> approved -> closed

ALTER TABLE `production_orders`
    MODIFY COLUMN status ENUM('pending', 'confirmed', 'approved', 'closed') 
    NOT NULL DEFAULT 'pending',
    ADD COLUMN `confirmed_by` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `status`,
    ADD COLUMN `confirmed_at` TIMESTAMP NULL DEFAULT NULL AFTER `confirmed_by`;

-- Add foreign key constraint
ALTER TABLE `production_orders`
    ADD CONSTRAINT `production_orders_confirmed_by_foreign`
    FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

INSERT INTO `permissions` (`name`, `description`, `guard_name`, `created_at`, `updated_at`)
VALUES ('manufacturing.production_orders.confirm', 'Confirm Production Order', 'web', NOW(), NOW());

-- Rollback (if needed):
-- ALTER TABLE `production_orders` DROP FOREIGN KEY `production_orders_confirmed_by_foreign`;
-- ALTER TABLE `production_orders` DROP COLUMN `confirmed_by`, DROP COLUMN `confirmed_at`;
-- DELETE FROM `permissions` WHERE `name` = 'manufacturing.production_orders.confirm';
