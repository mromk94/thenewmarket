# The New Age Marketplace

A modern, multi-vendor e-commerce marketplace built with PHP 8.1+, MySQL, and a custom lightweight MVC architecture. Designed to run on standard cPanel / Namecheap shared hosting without Node.js, Docker, Redis, or background workers.

---

## Features

- Customer storefront with search, filters, and category browsing
- Vendor registration, storefront, product management, and wallet
- Affiliate marketplace: vendors promote admin products and earn commission
- Admin dashboard and user/vendor/product management (skeleton ready for expansion)
- Cart and checkout with server-side price locking
- Payment abstraction with a `TestPaymentProvider` (Paystack/Flutterwave/Stripe ready)
- Customer and vendor wallets with an immutable ledger
- Order lifecycle and affiliate commission recording
- CSRF, XSS, SQL-injection, and rate-limiting protections
- SEO sitemap, robots.txt, and responsive glassmorphism UI

---

## Local Development

### Requirements

- PHP 8.1+ with `pdo_mysql`, `mbstring`, `json`, `fileinfo`
- MySQL 5.7+ or MariaDB 10.6+
- Composer

### Setup

1. Clone or extract the project.
2. Copy the environment file:

   ```bash
   cp .env.example .env
   ```

3. Edit `.env` with your local database credentials.
4. Install dependencies:

   ```bash
   composer install
   ```

5. Create the database and seed data:

   ```bash
   php install.php
   ```

6. Start the local server:

   ```bash
   php -S localhost:8000 -t public
   ```

7. Visit `http://localhost:8000`.

### Demo Accounts

| Role  | Email                        | Password      |
|-------|------------------------------|---------------|
| Admin | `admin@thenewage.local`      | `admin123`    |
| Customer | `customer@thenewage.local` | `customer123` |
| Vendor | `vendor@thenewage.local`    | `vendor123`   |

---

## cPanel / Namecheap Deployment

1. On your PC, run:

   ```bash
   composer install --no-dev
   php install.php
   ```

2. Export the production database (`mysqldump` or export from phpMyAdmin).
3. Create a MySQL database, user, and password in cPanel.
4. Upload the project files to your hosting account.
5. Recommended structure:

   - Place `public/` contents into `public_html` (or point the addon domain document root to `public/`).
   - Keep `app/`, `config/`, `database/`, `storage/`, `vendor/`, `.env`, and `cron/` outside the document root.

6. Import the database in cPanel phpMyAdmin.
7. Create `.env` from `.env.example` with production values:

   ```
   APP_ENV=production
   APP_URL=https://yourdomain.com
   APP_DEBUG=false
   DB_HOST=localhost
   DB_DATABASE=your_db
   DB_USERNAME=your_user
   DB_PASSWORD=your_password
   ```

8. Create the `storage/logs`, `storage/cache`, and `storage/uploads` directories and make them writable (`755` or `775`).
9. Configure cPanel cron:

   ```
   /usr/local/bin/php /home/username/marketplace/cron/cron.php >> /home/username/marketplace/storage/logs/cron.log 2>&1
   ```

10. Enable HTTPS in cPanel.
11. Delete or remove `install.php` from the server after installation.

---

## Security Notes

- `.env` must never be web-accessible. The `public/.htaccess` already denies dot files.
- Keep `app/`, `config/`, `storage/`, and `vendor/` outside the web root if possible.
- All financial calculations happen server-side; never trust browser-submitted prices or balances.
- CSRF tokens are required on all state-changing forms.
- Login attempts are rate-limited in session.
- Set `APP_DEBUG=false` and `APP_ENV=production` on live sites.
- Use HTTPS so the `secure` session cookie flag takes effect.

---

## Roadmap for the Next Maintainer

- Implement real Paystack/Flutterwave/Stripe drivers in `app/Payments/`.
- Build out full admin CRUD for products, categories, vendors, orders, refunds, and withdrawals.
- Add email sending via PHPMailer and the `email_templates` table.
- Add refund workflow with wallet credits and commission reversal.
- Add image upload and thumbnail generation.
- Add customer reviews and coupons.
- Add advanced reporting and analytics.
