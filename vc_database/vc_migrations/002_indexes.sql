-- Migration 002: Performance Indexes for MySQL

DROP INDEX IF EXISTS idx_users_status ON users;
CREATE INDEX idx_users_status ON users(status);

DROP INDEX IF EXISTS idx_users_created_at ON users;
CREATE INDEX idx_users_created_at ON users(created_at);

DROP INDEX IF EXISTS idx_orders_user_id ON orders;
CREATE INDEX idx_orders_user_id ON orders(user_id);

DROP INDEX IF EXISTS idx_orders_status ON orders;
CREATE INDEX idx_orders_status ON orders(status);

DROP INDEX IF EXISTS idx_orders_created_at ON orders;
CREATE INDEX idx_orders_created_at ON orders(created_at);

DROP INDEX IF EXISTS idx_order_items_order_id ON order_items;
CREATE INDEX idx_order_items_order_id ON order_items(order_id);

DROP INDEX IF EXISTS idx_order_items_plan_id ON order_items;
CREATE INDEX idx_order_items_plan_id ON order_items(plan_id);

DROP INDEX IF EXISTS idx_payments_order_id ON payments;
CREATE INDEX idx_payments_order_id ON payments(order_id);

DROP INDEX IF EXISTS idx_payments_status ON payments;
CREATE INDEX idx_payments_status ON payments(status);

DROP INDEX IF EXISTS idx_payments_transaction_id ON payments;
CREATE INDEX idx_payments_transaction_id ON payments(transaction_id);

DROP INDEX IF EXISTS idx_subscriptions_user_id ON subscriptions;
CREATE INDEX idx_subscriptions_user_id ON subscriptions(user_id);

DROP INDEX IF EXISTS idx_subscriptions_plan_id ON subscriptions;
CREATE INDEX idx_subscriptions_plan_id ON subscriptions(plan_id);

DROP INDEX IF EXISTS idx_subscriptions_order_id ON subscriptions;
CREATE INDEX idx_subscriptions_order_id ON subscriptions(order_id);

DROP INDEX IF EXISTS idx_subscriptions_status ON subscriptions;
CREATE INDEX idx_subscriptions_status ON subscriptions(status);

DROP INDEX IF EXISTS idx_subscriptions_expires_at ON subscriptions;
CREATE INDEX idx_subscriptions_expires_at ON subscriptions(expires_at);

DROP INDEX IF EXISTS idx_subscription_tokens_status ON subscription_tokens;
CREATE INDEX idx_subscription_tokens_status ON subscription_tokens(status);

DROP INDEX IF EXISTS idx_subscription_access_subscription ON subscription_access;
CREATE INDEX idx_subscription_access_subscription ON subscription_access(subscription_id);

DROP INDEX IF EXISTS idx_subscription_access_node ON subscription_access;
CREATE INDEX idx_subscription_access_node ON subscription_access(node_id);

DROP INDEX IF EXISTS idx_subscription_events_subscription ON subscription_events;
CREATE INDEX idx_subscription_events_subscription ON subscription_events(subscription_id);

DROP INDEX IF EXISTS idx_subscription_events_created_at ON subscription_events;
CREATE INDEX idx_subscription_events_created_at ON subscription_events(created_at);

DROP INDEX IF EXISTS idx_subscription_traffic_subscription_date ON subscription_traffic;
CREATE INDEX idx_subscription_traffic_subscription_date ON subscription_traffic(subscription_id, date);

DROP INDEX IF EXISTS idx_nodes_status ON vpn_nodes;
CREATE INDEX idx_nodes_status ON vpn_nodes(status);

DROP INDEX IF EXISTS idx_nodes_country ON vpn_nodes;
CREATE INDEX idx_nodes_country ON vpn_nodes(country);

DROP INDEX IF EXISTS idx_node_health_node_time ON node_health_logs;
CREATE INDEX idx_node_health_node_time ON node_health_logs(node_id, checked_at);

DROP INDEX IF EXISTS idx_notifications_user_read ON notifications;
CREATE INDEX idx_notifications_user_read ON notifications(user_id, read_at);

DROP INDEX IF EXISTS idx_support_tickets_user ON support_tickets;
CREATE INDEX idx_support_tickets_user ON support_tickets(user_id);

DROP INDEX IF EXISTS idx_support_tickets_staff ON support_tickets;
CREATE INDEX idx_support_tickets_staff ON support_tickets(assigned_staff_id);

DROP INDEX IF EXISTS idx_support_tickets_status ON support_tickets;
CREATE INDEX idx_support_tickets_status ON support_tickets(status);

DROP INDEX IF EXISTS idx_audit_logs_user ON audit_logs;
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);

DROP INDEX IF EXISTS idx_audit_logs_entity ON audit_logs;
CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type, entity_id);

DROP INDEX IF EXISTS idx_audit_logs_created ON audit_logs;
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);