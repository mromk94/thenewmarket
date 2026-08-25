CREATE TABLE IF NOT EXISTS pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(60) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    meta_description TEXT,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO pages (slug, title, content, meta_description) VALUES
('about', 'About Us', '<p>The New Age Marketplace is a premium multi-vendor platform built to connect curated products, trusted vendors and affiliate storefronts in one elegant experience.</p>', 'Learn more about The New Age Marketplace.'),
('contact', 'Contact Us', '<p>Have a question? Reach out to our support team.</p><p>Email: support@example.com</p>', 'Get in touch with The New Age Marketplace.'),
('terms', 'Terms of Service', '<p>By using this marketplace, you agree to our terms. Vendors and affiliates must follow platform guidelines. All sales are subject to approval and our refund policy.</p>', 'Terms of Service for The New Age Marketplace.'),
('privacy', 'Privacy Policy', '<p>We respect your privacy. Personal data is used only to operate the marketplace, process orders and communicate with you. We do not sell your information.</p>', 'Privacy Policy for The New Age Marketplace.');
