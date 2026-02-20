-- Add new manufacturing report permissions
INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES
('manufacturing.reports.orders', 'web', NOW(), NOW()),
('manufacturing.reports.schedules', 'web', NOW(), NOW()),
('manufacturing.reports.requisitions', 'web', NOW(), NOW());

-- Assign to the same roles that have manufacturing.reports.history
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name IN ('manufacturing.reports.orders', 'manufacturing.reports.schedules', 'manufacturing.reports.requisitions');

-- Also add team_ledger permission if not yet added
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at)
VALUES ('manufacturing.reports.team_ledger', 'web', NOW(), NOW());

INSERT IGNORE INTO role_has_permissions (permission_id, role_id)
SELECT p.id, rp.role_id
FROM permissions p
CROSS JOIN (
    SELECT DISTINCT role_id
    FROM role_has_permissions
    WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history')
) rp
WHERE p.name = 'manufacturing.reports.team_ledger';

-- Clear permission cache (run after: php artisan cache:forget spatie.permission.cache)
