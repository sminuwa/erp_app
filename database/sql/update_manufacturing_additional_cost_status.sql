ALTER TABLE `manufacturing_additional_costs` 
MODIFY COLUMN `status` ENUM('pending', 'posted', 'reversed') 
COLLATE utf8mb4_unicode_ci 
DEFAULT 'pending';