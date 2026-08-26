ALTER TABLE products
    ADD COLUMN IF NOT EXISTS delivery_rate DECIMAL(15,4) NULL AFTER price;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS delivery_status VARCHAR(50) DEFAULT 'pending' AFTER status,
    ADD COLUMN IF NOT EXISTS delivery_stage VARCHAR(100) DEFAULT 'Order placed' AFTER delivery_status,
    ADD COLUMN IF NOT EXISTS tracking_number VARCHAR(255) NULL AFTER delivery_stage,
    ADD COLUMN IF NOT EXISTS delivery_notes TEXT NULL AFTER tracking_number;
