-- VPN Commerce Database
-- PostgreSQL 14+
-- User -> Orders -> Subscriptions -> Subscription Tokens
-- Supports multiple active subscriptions per user, RBAC, nodes, traffic,
-- payments, coupons, referrals, support, notifications and audit logs.

BEGIN;

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- =========================
-- ENUM-LIKE CHECKED TABLES
-- =========================

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    username VARCHAR(100) UNIQUE,
    password_hash TEXT NOT NULL,
    full_name VARCHAR(255),
    phone VARCHAR(50),
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('pending','active','inactive','suspended','banned')),
    email_verified_at TIMESTAMPTZ,
    last_login_at TIMESTAMPTZ,
    last_login_ip INET,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ
);

CREATE TABLE user_profiles (
    user_id BIGINT PRIMARY KEY REFERENCES users(id) ON DELETE CASCADE,
    avatar_url TEXT,
    timezone VARCHAR(100) DEFAULT 'UTC',
    locale VARCHAR(20) DEFAULT 'en',
    company_name VARCHAR(255),
    tax_id VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE user_addresses (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(30) NOT NULL DEFAULT 'billing',
    recipient_name VARCHAR(255),
    phone VARCHAR(50),
    address_line1 TEXT,
    address_line2 TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    postal_code VARCHAR(30),
    country_code CHAR(2),
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================
-- RBAC
-- =========================

CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(150) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE user_roles (
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, role_id)
);

-- =========================
-- VPN PRODUCTS / PLANS
-- =========================

CREATE TABLE vpn_plans (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    duration_days INTEGER NOT NULL CHECK (duration_days > 0),
    traffic_limit_bytes BIGINT CHECK (traffic_limit_bytes IS NULL OR traffic_limit_bytes >= 0),
    device_limit INTEGER NOT NULL DEFAULT 1 CHECK (device_limit > 0),
    speed_limit_mbps INTEGER CHECK (speed_limit_mbps IS NULL OR speed_limit_mbps > 0),
    max_connections INTEGER NOT NULL DEFAULT 1 CHECK (max_connections > 0),
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('draft','active','inactive','archived')),
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE plan_prices (
    id BIGSERIAL PRIMARY KEY,
    plan_id BIGINT NOT NULL REFERENCES vpn_plans(id) ON DELETE CASCADE,
    currency VARCHAR(10) NOT NULL,
    amount NUMERIC(18,2) NOT NULL CHECK (amount >= 0),
    billing_period VARCHAR(30),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE plan_features (
    id BIGSERIAL PRIMARY KEY,
    plan_id BIGINT NOT NULL REFERENCES vpn_plans(id) ON DELETE CASCADE,
    feature_key VARCHAR(100) NOT NULL,
    feature_value TEXT,
    sort_order INTEGER NOT NULL DEFAULT 0,
    UNIQUE(plan_id, feature_key)
);

-- =========================
-- VPN NODES
-- =========================

CREATE TABLE node_groups (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','inactive')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE vpn_nodes (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    hostname VARCHAR(255),
    ip_address INET,
    country VARCHAR(100),
    city VARCHAR(100),
    provider VARCHAR(255),
    port INTEGER CHECK (port IS NULL OR port BETWEEN 1 AND 65535),
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('pending','active','maintenance','offline','disabled')),
    capacity INTEGER NOT NULL DEFAULT 0 CHECK (capacity >= 0),
    current_users INTEGER NOT NULL DEFAULT 0 CHECK (current_users >= 0),
    traffic_limit_bytes BIGINT CHECK (traffic_limit_bytes IS NULL OR traffic_limit_bytes >= 0),
    api_url TEXT,
    api_key_encrypted TEXT,
    metadata JSONB NOT NULL DEFAULT '{}'::jsonb,
    last_health_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE node_group_members (
    node_group_id BIGINT NOT NULL REFERENCES node_groups(id) ON DELETE CASCADE,
    node_id BIGINT NOT NULL REFERENCES vpn_nodes(id) ON DELETE CASCADE,
    priority INTEGER NOT NULL DEFAULT 100,
    PRIMARY KEY (node_group_id, node_id)
);

CREATE TABLE plan_node_groups (
    plan_id BIGINT NOT NULL REFERENCES vpn_plans(id) ON DELETE CASCADE,
    node_group_id BIGINT NOT NULL REFERENCES node_groups(id) ON DELETE CASCADE,
    PRIMARY KEY (plan_id, node_group_id)
);

CREATE TABLE node_health_logs (
    id BIGSERIAL PRIMARY KEY,
    node_id BIGINT NOT NULL REFERENCES vpn_nodes(id) ON DELETE CASCADE,
    status VARCHAR(30) NOT NULL,
    latency_ms INTEGER,
    cpu_percent NUMERIC(5,2),
    memory_percent NUMERIC(5,2),
    active_connections INTEGER,
    checked_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================
-- ORDERS
-- =========================

CREATE TABLE coupons (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    discount_type VARCHAR(30) NOT NULL
        CHECK (discount_type IN ('percent','fixed')),
    discount_value NUMERIC(18,2) NOT NULL CHECK (discount_value >= 0),
    max_uses INTEGER CHECK (max_uses IS NULL OR max_uses > 0),
    used_count INTEGER NOT NULL DEFAULT 0 CHECK (used_count >= 0),
    min_order_amount NUMERIC(18,2) CHECK (min_order_amount IS NULL OR min_order_amount >= 0),
    starts_at TIMESTAMPTZ,
    expires_at TIMESTAMPTZ,
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','inactive','expired')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE orders (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id),
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending'
        CHECK (status IN ('pending','paid','processing','completed','cancelled','refunded','failed')),
    currency VARCHAR(10) NOT NULL,
    subtotal NUMERIC(18,2) NOT NULL DEFAULT 0 CHECK (subtotal >= 0),
    discount NUMERIC(18,2) NOT NULL DEFAULT 0 CHECK (discount >= 0),
    tax NUMERIC(18,2) NOT NULL DEFAULT 0 CHECK (tax >= 0),
    total NUMERIC(18,2) NOT NULL DEFAULT 0 CHECK (total >= 0),
    coupon_id BIGINT REFERENCES coupons(id) ON DELETE SET NULL,
    notes TEXT,
    paid_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE order_items (
    id BIGSERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    plan_id BIGINT NOT NULL REFERENCES vpn_plans(id),
    quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity > 0),
    unit_price NUMERIC(18,2) NOT NULL CHECK (unit_price >= 0),
    total_price NUMERIC(18,2) NOT NULL CHECK (total_price >= 0),
    plan_snapshot JSONB NOT NULL DEFAULT '{}'::jsonb,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    order_id BIGINT NOT NULL REFERENCES orders(id),
    payment_method VARCHAR(50) NOT NULL,
    provider VARCHAR(100),
    transaction_id VARCHAR(255),
    amount NUMERIC(18,2) NOT NULL CHECK (amount >= 0),
    currency VARCHAR(10) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending'
        CHECK (status IN ('pending','processing','paid','failed','cancelled','refunded','partially_refunded')),
    provider_response JSONB,
    paid_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(provider, transaction_id)
);

CREATE TABLE invoices (
    id BIGSERIAL PRIMARY KEY,
    order_id BIGINT UNIQUE NOT NULL REFERENCES orders(id),
    invoice_number VARCHAR(100) UNIQUE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'issued'
        CHECK (status IN ('draft','issued','paid','void','refunded')),
    currency VARCHAR(10) NOT NULL,
    subtotal NUMERIC(18,2) NOT NULL DEFAULT 0,
    discount NUMERIC(18,2) NOT NULL DEFAULT 0,
    tax NUMERIC(18,2) NOT NULL DEFAULT 0,
    total NUMERIC(18,2) NOT NULL DEFAULT 0,
    issued_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    due_at TIMESTAMPTZ,
    paid_at TIMESTAMPTZ
);

CREATE TABLE coupon_usages (
    id BIGSERIAL PRIMARY KEY,
    coupon_id BIGINT NOT NULL REFERENCES coupons(id),
    user_id BIGINT NOT NULL REFERENCES users(id),
    order_id BIGINT NOT NULL REFERENCES orders(id),
    discount_amount NUMERIC(18,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(coupon_id, order_id)
);

-- =========================
-- SUBSCRIPTIONS
-- One user -> many subscriptions
-- =========================

CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id),
    plan_id BIGINT NOT NULL REFERENCES vpn_plans(id),
    order_id BIGINT REFERENCES orders(id) ON DELETE SET NULL,
    order_item_id BIGINT REFERENCES order_items(id) ON DELETE SET NULL,

    subscription_uuid UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,

    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('pending','active','expired','suspended','cancelled','revoked')),

    started_at TIMESTAMPTZ NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,

    traffic_limit_bytes BIGINT CHECK (traffic_limit_bytes IS NULL OR traffic_limit_bytes >= 0),
    traffic_used_bytes BIGINT NOT NULL DEFAULT 0 CHECK (traffic_used_bytes >= 0),

    device_limit INTEGER NOT NULL DEFAULT 1 CHECK (device_limit > 0),
    max_connections INTEGER NOT NULL DEFAULT 1 CHECK (max_connections > 0),

    auto_renew BOOLEAN NOT NULL DEFAULT FALSE,

    plan_snapshot JSONB NOT NULL DEFAULT '{}'::jsonb,

    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    cancelled_at TIMESTAMPTZ,

    CHECK (expires_at > started_at)
);

CREATE TABLE subscription_tokens (
    id BIGSERIAL PRIMARY KEY,
    subscription_id BIGINT NOT NULL REFERENCES subscriptions(id) ON DELETE CASCADE,
    token_hash TEXT NOT NULL UNIQUE,
    token_prefix VARCHAR(30),
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','disabled','revoked')),
    expires_at TIMESTAMPTZ,
    last_used_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(subscription_id)
);

CREATE TABLE subscription_access (
    id BIGSERIAL PRIMARY KEY,
    subscription_id BIGINT NOT NULL REFERENCES subscriptions(id) ON DELETE CASCADE,
    protocol VARCHAR(50) NOT NULL,
    node_id BIGINT REFERENCES vpn_nodes(id) ON DELETE SET NULL,
    external_client_id VARCHAR(255),
    config_data JSONB NOT NULL DEFAULT '{}'::jsonb,
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','disabled','revoked')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE subscription_events (
    id BIGSERIAL PRIMARY KEY,
    subscription_id BIGINT NOT NULL REFERENCES subscriptions(id) ON DELETE CASCADE,
    event_type VARCHAR(50) NOT NULL,
    old_status VARCHAR(30),
    new_status VARCHAR(30),
    old_expires_at TIMESTAMPTZ,
    new_expires_at TIMESTAMPTZ,
    metadata JSONB NOT NULL DEFAULT '{}'::jsonb,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE subscription_traffic (
    id BIGSERIAL PRIMARY KEY,
    subscription_id BIGINT NOT NULL REFERENCES subscriptions(id) ON DELETE CASCADE,
    date DATE NOT NULL,
    upload_bytes BIGINT NOT NULL DEFAULT 0 CHECK (upload_bytes >= 0),
    download_bytes BIGINT NOT NULL DEFAULT 0 CHECK (download_bytes >= 0),
    total_bytes BIGINT NOT NULL DEFAULT 0 CHECK (total_bytes >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(subscription_id, date)
);

-- =========================
-- DEVICES
-- =========================

CREATE TABLE user_devices (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    device_id VARCHAR(255) NOT NULL,
    device_name VARCHAR(255),
    platform VARCHAR(50),
    app_version VARCHAR(50),
    last_ip INET,
    last_seen_at TIMESTAMPTZ,
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','disabled','blocked')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(user_id, device_id)
);

CREATE TABLE subscription_devices (
    subscription_id BIGINT NOT NULL REFERENCES subscriptions(id) ON DELETE CASCADE,
    device_id BIGINT NOT NULL REFERENCES user_devices(id) ON DELETE CASCADE,
    first_used_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    last_used_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY(subscription_id, device_id)
);

-- =========================
-- REFERRAL / AFFILIATE
-- =========================

CREATE TABLE referral_codes (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    code VARCHAR(100) UNIQUE NOT NULL,
    commission_percent NUMERIC(5,2) NOT NULL DEFAULT 0
        CHECK (commission_percent >= 0 AND commission_percent <= 100),
    status VARCHAR(30) NOT NULL DEFAULT 'active'
        CHECK (status IN ('active','inactive')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE referrals (
    id BIGSERIAL PRIMARY KEY,
    referrer_id BIGINT NOT NULL REFERENCES users(id),
    referred_user_id BIGINT NOT NULL REFERENCES users(id),
    referral_code_id BIGINT REFERENCES referral_codes(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(referred_user_id)
);

CREATE TABLE commissions (
    id BIGSERIAL PRIMARY KEY,
    referral_id BIGINT NOT NULL REFERENCES referrals(id) ON DELETE CASCADE,
    order_id BIGINT NOT NULL REFERENCES orders(id),
    user_id BIGINT NOT NULL REFERENCES users(id),
    amount NUMERIC(18,2) NOT NULL DEFAULT 0 CHECK (amount >= 0),
    currency VARCHAR(10) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending'
        CHECK (status IN ('pending','approved','paid','cancelled')),
    paid_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================
-- SUPPORT
-- =========================

CREATE TABLE support_tickets (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id),
    assigned_staff_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    subject VARCHAR(255) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'open'
        CHECK (status IN ('open','pending','resolved','closed')),
    priority VARCHAR(30) NOT NULL DEFAULT 'normal'
        CHECK (priority IN ('low','normal','high','urgent')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE support_messages (
    id BIGSERIAL PRIMARY KEY,
    ticket_id BIGINT NOT NULL REFERENCES support_tickets(id) ON DELETE CASCADE,
    sender_id BIGINT NOT NULL REFERENCES users(id),
    message TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================
-- NOTIFICATIONS
-- =========================

CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSONB NOT NULL DEFAULT '{}'::jsonb,
    read_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE announcements (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft'
        CHECK (status IN ('draft','published','archived')),
    starts_at TIMESTAMPTZ,
    ends_at TIMESTAMPTZ,
    created_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================
-- SYSTEM / SECURITY
-- =========================

CREATE TABLE system_settings (
    id BIGSERIAL PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(50) NOT NULL DEFAULT 'string',
    is_secret BOOLEAN NOT NULL DEFAULT FALSE,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id BIGINT,
    old_data JSONB,
    new_data JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE webhook_logs (
    id BIGSERIAL PRIMARY KEY,
    provider VARCHAR(100),
    event_type VARCHAR(150),
    event_id VARCHAR(255),
    payload JSONB,
    status VARCHAR(30) NOT NULL DEFAULT 'received'
        CHECK (status IN ('received','processed','failed','ignored')),
    error_message TEXT,
    processed_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE(provider, event_id)
);

-- =========================
-- INDEXES
-- =========================

CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_created_at ON users(created_at);

CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);

CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_plan_id ON order_items(plan_id);

CREATE INDEX idx_payments_order_id ON payments(order_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_payments_transaction_id ON payments(transaction_id);

CREATE INDEX idx_subscriptions_user_id ON subscriptions(user_id);
CREATE INDEX idx_subscriptions_plan_id ON subscriptions(plan_id);
CREATE INDEX idx_subscriptions_order_id ON subscriptions(order_id);
CREATE INDEX idx_subscriptions_status ON subscriptions(status);
CREATE INDEX idx_subscriptions_expires_at ON subscriptions(expires_at);

CREATE INDEX idx_subscription_tokens_hash ON subscription_tokens(token_hash);
CREATE INDEX idx_subscription_tokens_status ON subscription_tokens(status);

CREATE INDEX idx_subscription_access_subscription ON subscription_access(subscription_id);
CREATE INDEX idx_subscription_access_node ON subscription_access(node_id);

CREATE INDEX idx_subscription_events_subscription ON subscription_events(subscription_id);
CREATE INDEX idx_subscription_events_created_at ON subscription_events(created_at);

CREATE INDEX idx_subscription_traffic_subscription_date
    ON subscription_traffic(subscription_id, date);

CREATE INDEX idx_nodes_status ON vpn_nodes(status);
CREATE INDEX idx_nodes_country ON vpn_nodes(country);
CREATE INDEX idx_node_health_node_time ON node_health_logs(node_id, checked_at);

CREATE INDEX idx_notifications_user_read
    ON notifications(user_id, read_at);

CREATE INDEX idx_support_tickets_user ON support_tickets(user_id);
CREATE INDEX idx_support_tickets_staff ON support_tickets(assigned_staff_id);
CREATE INDEX idx_support_tickets_status ON support_tickets(status);

CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type, entity_id);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);

-- =========================
-- UPDATED_AT TRIGGER
-- =========================

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_users_updated
BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_profiles_updated
BEFORE UPDATE ON user_profiles
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_addresses_updated
BEFORE UPDATE ON user_addresses
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_plans_updated
BEFORE UPDATE ON vpn_plans
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_nodes_updated
BEFORE UPDATE ON vpn_nodes
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_node_groups_updated
BEFORE UPDATE ON node_groups
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_orders_updated
BEFORE UPDATE ON orders
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_payments_updated
BEFORE UPDATE ON payments
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_subscriptions_updated
BEFORE UPDATE ON subscriptions
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_subscription_access_updated
BEFORE UPDATE ON subscription_access
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_tickets_updated
BEFORE UPDATE ON support_tickets
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE TRIGGER trg_announcements_updated
BEFORE UPDATE ON announcements
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- =========================
-- DEFAULT ROLES
-- =========================

INSERT INTO roles (name, description) VALUES
('super_admin', 'Full system access'),
('admin', 'Administrative access'),
('manager', 'Management access'),
('support', 'Customer support staff'),
('sales', 'Sales staff'),
('finance', 'Finance and payment staff'),
('technical', 'VPN technical staff'),
('customer', 'Normal customer')
ON CONFLICT (name) DO NOTHING;

-- =========================
-- DEFAULT PERMISSIONS
-- =========================

INSERT INTO permissions (name, description) VALUES
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

('audit_logs.view','View audit logs')
ON CONFLICT (name) DO NOTHING;

-- Grant every permission to super_admin
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r CROSS JOIN permissions p
WHERE r.name = 'super_admin'
ON CONFLICT DO NOTHING;

-- Customer baseline permissions
INSERT INTO role_permissions (role_id, permission_id)
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
WHERE r.name = 'customer'
ON CONFLICT DO NOTHING;

COMMIT;

-- =========================
-- USEFUL VIEWS
-- =========================

CREATE OR REPLACE VIEW active_user_subscriptions AS
SELECT
    s.id AS subscription_id,
    s.subscription_uuid,
    s.user_id,
    u.email,
    u.username,
    s.plan_id,
    p.name AS plan_name,
    s.status,
    s.started_at,
    s.expires_at,
    s.traffic_limit_bytes,
    s.traffic_used_bytes,
    s.device_limit,
    s.auto_renew
FROM subscriptions s
JOIN users u ON u.id = s.user_id
JOIN vpn_plans p ON p.id = s.plan_id
WHERE s.status = 'active'
  AND s.expires_at > NOW();

CREATE OR REPLACE VIEW subscription_usage AS
SELECT
    s.id AS subscription_id,
    s.user_id,
    s.plan_id,
    s.traffic_limit_bytes,
    s.traffic_used_bytes,
    CASE
        WHEN s.traffic_limit_bytes IS NULL THEN NULL
        WHEN s.traffic_limit_bytes = 0 THEN 100
        ELSE ROUND((s.traffic_used_bytes::NUMERIC / s.traffic_limit_bytes::NUMERIC) * 100, 2)
    END AS traffic_percent,
    s.started_at,
    s.expires_at,
    s.status
FROM subscriptions s;
