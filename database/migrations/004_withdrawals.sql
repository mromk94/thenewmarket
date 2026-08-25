CREATE TABLE IF NOT EXISTS vendor_withdrawals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    amount DECIMAL(15,4) NOT NULL,
    currency VARCHAR(20) NOT NULL,
    method VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);
