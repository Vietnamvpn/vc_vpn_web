-- Migration 002: Performance Indexes for MySQL

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

CREATE INDEX idx_subscription_tokens_status ON subscription_tokens(status);

CREATE INDEX idx_subscription_access_subscription ON subscription_access(subscription_id);
CREATE INDEX idx_subscription_access_node ON subscription_access(node_id);

CREATE INDEX idx_subscription_events_subscription ON subscription_events(subscription_id);
CREATE INDEX idx_subscription_events_created_at ON subscription_events(created_at);

CREATE INDEX idx_subscription_traffic_subscription_date ON subscription_traffic(subscription_id, date);

CREATE INDEX idx_nodes_status ON vpn_nodes(status);
CREATE INDEX idx_nodes_country ON vpn_nodes(country);
CREATE INDEX idx_node_health_node_time ON node_health_logs(node_id, checked_at);

CREATE INDEX idx_notifications_user_read ON notifications(user_id, read_at);

CREATE INDEX idx_support_tickets_user ON support_tickets(user_id);
CREATE INDEX idx_support_tickets_staff ON support_tickets(assigned_staff_id);
CREATE INDEX idx_support_tickets_status ON support_tickets(status);

CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_entity ON audit_logs(entity_type, entity_id);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);