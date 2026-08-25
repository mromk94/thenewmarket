# The New Age Marketplace

A premium, multi-vendor e-commerce marketplace with affiliate storefronts, built in PHP 8.1 and MySQL. Curated products, vendor self-service, admin moderation, commission tracking, secure checkout, and content-managed pages — ready for cPanel / shared hosting with no Node.js or Docker required.

---

## Features

- Customer storefront with search, filters, categories and product reviews
- Vendor registration, storefront, product management and wallet
- Affiliate marketplace: vendors promote admin products and earn commission
- Admin dashboard with product, vendor, user, category, coupon, email template and page management
- Cart and checkout with server-side price locking and coupon support
- Payment abstraction with a `TestPaymentProvider` (Paystack/Flutterwave/Stripe ready)
- Customer and vendor wallets with an immutable ledger
- Order lifecycle and affiliate commission recording
- Customer reviews and ratings
- Editable content pages (about, contact, terms, privacy)
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

1. Create a MySQL database and user in cPanel.
2. Upload `thenewmarket.zip` and extract it to a folder outside `public_html`.
3. Point your addon domain document root to the `public/` folder.

   Alternatively, if `public_html` is the document root, extract the zip directly into `public_html` — the included `.htaccess` will route traffic into `public/` automatically.
4. Make `storage/logs`, `storage/cache`, and `storage/uploads` writable.
5. Visit `https://yourdomain.com/install` and complete the web installer.
6. Enable HTTPS in cPanel.
7. After installation, delete `public/install.php`.
8. Configure cPanel cron:

   ```
   /usr/local/bin/php /home/username/marketplace/cron/cron.php >> /home/username/marketplace/storage/logs/cron.log 2>&1
   ```

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
