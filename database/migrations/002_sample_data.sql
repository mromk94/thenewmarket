INSERT INTO categories (name, slug, description, is_visible, sort_order) VALUES
('Electronics', 'electronics', 'Gadgets and devices', 1, 1),
('Fashion', 'fashion', 'Clothing and accessories', 1, 2),
('Home', 'home', 'Home and living', 1, 3)
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO products (owner_id, vendor_id, name, slug, description, short_description, sku, price, compare_at_price, stock_qty, inventory_status, category_id, is_affiliate_eligible, affiliate_commission_type, affiliate_commission_value, status, featured, visibility) VALUES
(
    (SELECT id FROM users WHERE email = 'admin@thenewage.local'),
    NULL,
    'Premium Watch',
    'premium-watch',
    'A premium watch for the new age. Water resistant, stainless steel, and elegant design.',
    'Stylish premium watch with 10-year warranty.',
    'WATCH-001',
    100.0000,
    120.0000,
    50,
    'in_stock',
    (SELECT id FROM categories WHERE slug = 'electronics'),
    1,
    'percentage',
    10.0000,
    'published',
    1,
    'public'
)
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO products (owner_id, vendor_id, name, slug, description, short_description, sku, price, compare_at_price, stock_qty, inventory_status, category_id, is_affiliate_eligible, affiliate_commission_type, affiliate_commission_value, status, featured, visibility) VALUES
(
    (SELECT id FROM users WHERE email = 'admin@thenewage.local'),
    NULL,
    'Premium Leather Bag',
    'premium-leather-bag',
    'Handcrafted leather bag with premium finishes.',
    'Durable leather bag for everyday use.',
    'BAG-001',
    150.0000,
    180.0000,
    30,
    'in_stock',
    (SELECT id FROM categories WHERE slug = 'fashion'),
    1,
    'percentage',
    15.0000,
    'published',
    0,
    'public'
)
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO products (owner_id, vendor_id, name, slug, description, short_description, sku, price, compare_at_price, stock_qty, inventory_status, category_id, is_affiliate_eligible, affiliate_commission_type, affiliate_commission_value, status, featured, visibility) VALUES
(
    (SELECT id FROM users WHERE email = 'admin@thenewage.local'),
    NULL,
    'Wireless Headphones',
    'wireless-headphones',
    'Noise cancelling wireless headphones with 20-hour battery.',
    'High-quality wireless audio.',
    'HEAD-001',
    80.0000,
    99.0000,
    100,
    'in_stock',
    (SELECT id FROM categories WHERE slug = 'electronics'),
    1,
    'percentage',
    10.0000,
    'published',
    0,
    'public'
)
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO products (owner_id, vendor_id, name, slug, description, short_description, sku, price, compare_at_price, stock_qty, inventory_status, category_id, is_affiliate_eligible, affiliate_commission_type, affiliate_commission_value, status, featured, visibility) VALUES
(
    (SELECT id FROM users WHERE email = 'vendor@thenewage.local'),
    (SELECT id FROM vendors WHERE slug = 'demo-vendor'),
    'Handmade Scarf',
    'handmade-scarf',
    'Soft handmade scarf crafted by our partner vendor.',
    'Cozy handmade scarf in multiple colors.',
    'SCARF-001',
    35.0000,
    45.0000,
    20,
    'in_stock',
    (SELECT id FROM categories WHERE slug = 'fashion'),
    0,
    'percentage',
    0.0000,
    'published',
    0,
    'public'
)
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO product_images (product_id, file_path, is_thumbnail, sort_order)
SELECT id, '', 1, 1 FROM products;

INSERT INTO vendor_affiliate_products (vendor_id, product_id)
VALUES (
    (SELECT id FROM vendors WHERE slug = 'demo-vendor'),
    (SELECT id FROM products WHERE slug = 'premium-watch')
)
ON DUPLICATE KEY UPDATE vendor_id = vendor_id;
