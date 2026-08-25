CREATE TABLE IF NOT EXISTS refunds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    customer_id INT UNSIGNED NOT NULL,
    amount DECIMAL(15,4) NOT NULL,
    currency VARCHAR(20) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status)
);
