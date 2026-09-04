-- VC VPN Commerce schema for MySQL 8.0
-- The original PostgreSQL dump is retained separately for reference.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NULL UNIQUE,
    username VARCHAR(100) NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    status ENUM('pending','active','inactive','suspended','banned') NOT NULL DEFAULT 'active',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_profiles (
    user_id BIGINT UNSIGNED NOT NULL PRIMARY KEY,
    avatar_url TEXT NULL,
    timezone VARCHAR(100) NOT NULL DEFAULT 'UTC',
    locale VARCHAR(20) NOT NULL DEFAULT 'en',
    company_name VARCHAR(255) NULL,
    tax_id VARCHAR(100) NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vpn_plans (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    duration_days INT NOT NULL,
    traffic_limit_bytes BIGINT NULL,
    device_limit INT NOT NULL DEFAULT 1,
    speed_limit_mbps INT NULL,
    max_connections INT NOT NULL DEFAULT 1,
    status ENUM('draft','active','inactive','archived') NOT NULL DEFAULT 'active',
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS plan_prices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    plan_id BIGINT UNSIGNED NOT NULL,
    currency VARCHAR(10) NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    billing_period VARCHAR(30) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_plan_prices_plan FOREIGN KEY (plan_id) REFERENCES vpn_plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('pending','paid','processing','completed','cancelled','refunded','failed') NOT NULL DEFAULT 'pending',
    currency VARCHAR(10) NOT NULL,
    subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
    discount DECIMAL(18,2) NOT NULL DEFAULT 0,
    tax DECIMAL(18,2) NOT NULL DEFAULT 0,
    total DECIMAL(18,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    provider VARCHAR(100) NULL,
    transaction_id VARCHAR(255) NULL,
    amount DECIMAL(18,2) NOT NULL,
    currency VARCHAR(10) NOT NULL,
    status ENUM('pending','processing','paid','failed','cancelled','refunded','partially_refunded') NOT NULL DEFAULT 'pending',
    provider_response JSON NULL,
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_payment_provider_transaction (provider, transaction_id),
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscriptions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    subscription_uuid CHAR(36) NOT NULL UNIQUE,
    status ENUM('pending','active','expired','suspended','cancelled','revoked') NOT NULL DEFAULT 'active',
    started_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    traffic_limit_bytes BIGINT NULL,
    traffic_used_bytes BIGINT NOT NULL DEFAULT 0,
    device_limit INT NOT NULL DEFAULT 1,
    max_connections INT NOT NULL DEFAULT 1,
    auto_renew TINYINT(1) NOT NULL DEFAULT 0,
    plan_snapshot JSON NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    cancelled_at DATETIME NULL,
    CONSTRAINT fk_subscriptions_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id) REFERENCES vpn_plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO roles (name, description) VALUES
('super_admin', 'Full system access'),
('admin', 'Administrative access'),
('manager', 'Management access'),
('support', 'Customer support staff'),
('sales', 'Sales staff'),
('finance', 'Finance and payment staff'),
('technical', 'VPN technical staff'),
('customer', 'Normal customer');

INSERT IGNORE INTO permissions (name, description) VALUES
('dashboard.view','View dashboard'), ('users.view','View users'), ('users.create','Create users'),
('users.edit','Edit users'), ('users.delete','Delete users'), ('users.suspend','Suspend users'),
('orders.view','View orders'), ('orders.create','Create orders'), ('orders.edit','Edit orders'),
('orders.cancel','Cancel orders'), ('payments.view','View payments'), ('payments.refund','Refund payments'),
('subscriptions.view','View subscriptions'), ('subscriptions.create','Create subscriptions'),
('subscriptions.edit','Edit subscriptions'), ('subscriptions.suspend','Suspend subscriptions'),
('subscriptions.renew','Renew subscriptions'), ('subscriptions.revoke','Revoke subscriptions'),
('plans.view','View VPN plans'), ('plans.create','Create VPN plans'), ('plans.edit','Edit VPN plans'),
('plans.delete','Delete VPN plans'), ('nodes.view','View VPN nodes'), ('nodes.create','Create VPN nodes'),
('nodes.edit','Edit VPN nodes'), ('nodes.delete','Delete VPN nodes'), ('nodes.health','View node health'),
('staff.view','View staff'), ('staff.manage','Manage staff'), ('roles.manage','Manage roles'),
('permissions.manage','Manage permissions'), ('coupons.view','View coupons'), ('coupons.manage','Manage coupons'),
('affiliate.view','View affiliate'), ('affiliate.manage','Manage affiliate'), ('support.view','View support'),
('support.manage','Manage support'), ('settings.view','View settings'), ('settings.manage','Manage settings'),
('audit_logs.view','View audit logs');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r CROSS JOIN permissions p WHERE r.name = 'super_admin';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name IN
('dashboard.view','orders.view','subscriptions.view','subscriptions.renew','plans.view','support.view','support.manage')
WHERE r.name = 'customer';
