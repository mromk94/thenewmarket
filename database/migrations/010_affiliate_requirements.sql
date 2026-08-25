ALTER TABLE vendors ADD COLUMN IF NOT EXISTS kyc_verified TINYINT(1) DEFAULT 0 AFTER status;
ALTER TABLE products ADD COLUMN IF NOT EXISTS affiliate_require_min_balance DECIMAL(15,4) DEFAULT 0 AFTER affiliate_commission_value,
                    ADD COLUMN IF NOT EXISTS affiliate_require_kyc TINYINT(1) DEFAULT 0 AFTER affiliate_require_min_balance,
                    ADD COLUMN IF NOT EXISTS affiliate_require_min_sales INT UNSIGNED DEFAULT 0 AFTER affiliate_require_kyc;
