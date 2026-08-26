ALTER TABLE reviews
    ADD COLUMN IF NOT EXISTS order_id INT UNSIGNED NULL AFTER customer_id,
    ADD INDEX IF NOT EXISTS idx_order (order_id);
