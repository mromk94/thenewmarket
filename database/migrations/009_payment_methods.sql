CREATE TABLE IF NOT EXISTS payment_methods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('crypto','bank','manual') NOT NULL,
    name VARCHAR(255) NOT NULL,
    currency VARCHAR(20) NOT NULL,
    network VARCHAR(100) DEFAULT NULL,
    wallet_address VARCHAR(255) DEFAULT NULL,
    bank_name VARCHAR(255) DEFAULT NULL,
    account_name VARCHAR(255) DEFAULT NULL,
    account_number VARCHAR(255) DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    qr_image VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payment_proofs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    payment_method_id INT UNSIGNED NOT NULL,
    reference VARCHAR(255) DEFAULT NULL,
    receipt_image VARCHAR(500) DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_status (status),
    CONSTRAINT fk_payment_proofs_method FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT
);
