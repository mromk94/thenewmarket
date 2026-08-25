INSERT IGNORE INTO email_templates (`key`, subject, body) VALUES
('customer_order_placed', 'Your order has been placed - {{order_number}}', '<h2>Thank you for your order!</h2><p>Order number: {{order_number}}</p><p>Total: {{currency_symbol}}{{total}}</p><p>Status: {{status}}</p><p><a href="{{order_url}}">View order</a></p>'),
('customer_order_paid', 'Payment confirmed - {{order_number}}', '<h2>Payment confirmed</h2><p>Order {{order_number}} has been paid.</p><p>Total: {{currency_symbol}}{{total}}</p><p><a href="{{order_url}}">View order</a></p>'),
('customer_refund_approved', 'Refund approved', '<h2>Refund approved</h2><p>Order: {{order_number}}</p><p>Amount: {{currency_symbol}}{{amount}}</p><p>The credit has been added to your wallet.</p>'),
('vendor_approved', 'Your vendor account has been approved', '<h2>Welcome!</h2><p>Your vendor account {{business_name}} has been approved.</p><p>Start listing products and promoting affiliate products.</p>'),
('vendor_product_approved', 'Your product has been approved', '<h2>Product approved</h2><p>{{product_name}} is now published and available for sale.</p>'),
('vendor_withdrawal_approved', 'Withdrawal approved', '<h2>Withdrawal approved</h2><p>Amount: {{currency_symbol}}{{amount}}</p><p>It will be sent to your payout method soon.</p>');
