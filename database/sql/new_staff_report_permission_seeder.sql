-- Add the new permission
INSERT INTO permissions (name, guard_name, created_at, updated_at) 
VALUES ('manufacturing.reports.team_ledger', 'web', NOW(), NOW());

-- Also add the submenu permission if needed
INSERT INTO permissions (name, guard_name, created_at, updated_at) 
SELECT 'submenu.manufacturing.reports', 'web', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE name = 'submenu.manufacturing.reports');

-- Assign to the same roles that have manufacturing.reports.history
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT 
    (SELECT id FROM permissions WHERE name = 'manufacturing.reports.team_ledger'),
    role_id
FROM role_has_permissions 
WHERE permission_id = (SELECT id FROM permissions WHERE name = 'manufacturing.reports.history');
