INSERT INTO roles (name, display_name) VALUES
('admin', 'Administrator'),
('vendor', 'Vendor'),
('customer', 'Customer')
ON DUPLICATE KEY UPDATE display_name = display_name;

INSERT INTO email_templates (`key`, subject, body) VALUES
('customer.register', 'Welcome to {{site_name}}', 'Hello {{first_name}},\n\nWelcome to {{site_name}}.'),
('password.reset', 'Password reset request', 'Click to reset: {{url}}'),
('order.confirmation', 'Your order has been placed', 'Order {{order_number}} total: {{total}}')
ON DUPLICATE KEY UPDATE subject = VALUES(subject), body = VALUES(body);
