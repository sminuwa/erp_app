-- Daily Manufacturing Schedule Fixes
-- Add edit permission for daily schedules

-- Add the 'manufacturing.schedules.edit' permission
INSERT INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`)
SELECT 'manufacturing.schedules.edit', 'web', NOW(), NOW()
FROM dual
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `name` = 'manufacturing.schedules.edit'
);

-- Grant to admin role (adjust role_id as needed)
-- INSERT INTO `role_has_permissions` (`permission_id`, `role_id`)
-- SELECT p.id, r.id FROM permissions p, roles r
-- WHERE p.name = 'manufacturing.schedules.edit' AND r.name = 'admin';
