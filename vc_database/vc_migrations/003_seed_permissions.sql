-- Migration 003: Seed Default Roles & Permissions for MySQL

-- DEFAULT ROLES
INSERT IGNORE INTO roles (name, description) VALUES
('super_admin', 'Full system access'),
('admin', 'Administrative access'),
('manager', 'Management access'),
('support', 'Customer support staff'),
('sales', 'Sales staff'),
('finance', 'Finance and payment staff'),
('technical', 'VPN technical staff'),
('customer', 'Normal customer');

-- DEFAULT PERMISSIONS
INSERT IGNORE INTO permissions (name, description) VALUES
('dashboard.view','View dashboard'),

('users.view','View users'),
('users.create','Create users'),
('users.edit','Edit users'),
('users.delete','Delete users'),
('users.suspend','Suspend users'),

('orders.view','View orders'),
('orders.create','Create orders'),
('orders.edit','Edit orders'),
('orders.cancel','Cancel orders'),

('payments.view','View payments'),
('payments.refund','Refund payments'),

('subscriptions.view','View subscriptions'),
('subscriptions.create','Create subscriptions'),
('subscriptions.edit','Edit subscriptions'),
('subscriptions.suspend','Suspend subscriptions'),
('subscriptions.renew','Renew subscriptions'),
('subscriptions.revoke','Revoke subscriptions'),

('plans.view','View VPN plans'),
('plans.create','Create VPN plans'),
('plans.edit','Edit VPN plans'),
('plans.delete','Delete VPN plans'),

('nodes.view','View VPN nodes'),
('nodes.create','Create VPN nodes'),
('nodes.edit','Edit VPN nodes'),
('nodes.delete','Delete VPN nodes'),
('nodes.health','View node health'),

('staff.view','View staff'),
('staff.manage','Manage staff'),
('roles.manage','Manage roles'),
('permissions.manage','Manage permissions'),

('coupons.view','View coupons'),
('coupons.manage','Manage coupons'),

('affiliate.view','View affiliate'),
('affiliate.manage','Manage affiliate'),

('support.view','View support'),
('support.manage','Manage support'),

('settings.view','View settings'),
('settings.manage','Manage settings'),

('audit_logs.view','View audit logs');

-- Grant every permission to super_admin
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r CROSS JOIN permissions p
WHERE r.name = 'super_admin';

-- Customer baseline permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.name IN (
    'dashboard.view',
    'orders.view',
    'subscriptions.view',
    'subscriptions.renew',
    'plans.view',
    'support.view',
    'support.manage'
)
WHERE r.name = 'customer';