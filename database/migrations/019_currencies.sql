CREATE TABLE IF NOT EXISTS currencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(3) NOT NULL,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    exchange_rate DECIMAL(15,8) NOT NULL DEFAULT 1.00000000,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_code (code)
) ENGINE=InnoDB;

INSERT IGNORE INTO currencies (code, name, symbol, exchange_rate, is_active, is_default) VALUES
('USD', 'US Dollar', '$', 1.00000000, 1, 1);
