CREATE TABLE IF NOT EXISTS deposit_methods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('crypto','bank') NOT NULL,
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

CREATE TABLE IF NOT EXISTS vendor_deposits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    deposit_method_id INT UNSIGNED NOT NULL,
    amount DECIMAL(15,4) NOT NULL,
    currency VARCHAR(20) NOT NULL,
    reference VARCHAR(255) DEFAULT NULL,
    receipt_image VARCHAR(500) DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    CONSTRAINT fk_vendor_deposits_method FOREIGN KEY (deposit_method_id) REFERENCES deposit_methods(id) ON DELETE RESTRICT
);
