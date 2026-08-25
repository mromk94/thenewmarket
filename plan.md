# PROJECT: MODERN MULTI-VENDOR E-COMMERCE MARKETPLACE

## 0. ROLE AND PRIMARY OBJECTIVE

You are acting as a senior full-stack architect, PHP engineer, database engineer, UI/UX designer, security engineer, QA engineer, and deployment engineer.

Your task is to design and build a complete, production-ready e-commerce marketplace website.

The application must be:

* PHP-based.
* Designed to run reliably on standard shared cPanel hosting.
* Compatible with Namecheap shared hosting.
* Developed and tested locally on the developer's PC before deployment.
* Database-driven.
* Mobile responsive.
* Modern, premium, elegant, glassy, and visually sophisticated.
* Easy for non-technical users to understand.
* Secure.
* Maintainable.
* Structured so the application can grow later without requiring a complete rewrite.

DO NOT immediately start writing the entire application.

First understand the requirements, create the architecture, create the database design, define the modules, define the routes/pages, define the permissions, and create a sequential implementation plan.

Then build the application phase by phase.

Do not skip phases.

After every major phase, test the implementation before proceeding.

---

# 1. HOSTING CONSTRAINT — VERY IMPORTANT

The final application must be suitable for ordinary Namecheap shared hosting using cPanel.

Design the architecture around capabilities normally available on PHP shared hosting.

Prefer:

* PHP 8.x
* MySQL/MariaDB
* HTML5
* CSS3
* JavaScript
* AJAX/fetch where useful
* Bootstrap or Tailwind CSS only if it can be deployed without requiring a permanent Node.js runtime
* Composer only where practical and where dependencies can be installed before deployment
* PHP sessions
* MySQL
* Cron jobs where available
* SMTP
* Local/server-side file storage for uploaded images where appropriate

Avoid requiring:

* Node.js runtime in production
* Docker
* Kubernetes
* Redis as a mandatory dependency
* Kafka
* WebSockets as a mandatory dependency
* Long-running background workers
* Server processes that must remain permanently running
* Infrastructure that requires VPS/root access

If a modern feature normally requires infrastructure unavailable on shared hosting, design a cPanel-compatible alternative.

The final deployment should ideally be achievable by:

1. Uploading the application files.
2. Creating/importing the MySQL database.
3. Configuring environment variables/configuration.
4. Creating required folders and permissions.
5. Configuring cron jobs if required.
6. Configuring the domain/subdomain.
7. Testing the application.

---

# 2. DEVELOPMENT ENVIRONMENT

The application must first be developed locally on the developer's PC.

Create a clear local development environment.

The developer should be able to run the application locally before anything is uploaded to cPanel.

Provide:

* Local PHP environment instructions.
* MySQL database setup.
* Environment configuration.
* Database migration/installation process.
* Seed/demo data.
* Admin account creation process.
* Vendor demo account.
* Customer demo account.
* Local image upload testing.
* Email testing strategy.
* Payment testing strategy.
* Error logging.

The application should have a clear configuration system so development and production settings can be separated.

For example:

Development:

* local database
* local URLs
* test SMTP
* debug enabled

Production:

* Namecheap database
* production URL
* production SMTP
* debug disabled

Never hard-code production credentials.

---

# 3. APPLICATION ARCHITECTURE

Before coding, determine the cleanest PHP architecture suitable for shared hosting.

Prefer a structured MVC-style architecture or another clean PHP architecture that provides:

* Controllers
* Models
* Services
* Repositories where useful
* Middleware/authentication
* Views/templates
* Routes
* Database layer
* Validation
* Authentication
* Authorization
* File handling
* Email services
* Payment services
* Logging

Keep business logic out of HTML templates.

Keep database queries out of presentation files wherever practical.

The codebase should be modular.

A future developer should be able to understand the project without reverse engineering it.

---

# 4. BRANDING SYSTEM

The website does not have a final name yet.

DO NOT hard-code a company name throughout the application.

Instead, create a centralized dynamic branding system.

The Admin should be able to configure the website identity from the Admin Dashboard.

Admin settings should include:

* Website name
* Short website name
* Logo
* Favicon
* Website tagline
* Website description
* Contact email
* Support email
* Sender name
* Sender email
* Phone number
* Address
* Social media links
* Currency
* Currency symbol
* Default timezone
* Website URL
* Email footer
* Email signature
* Terms URL
* Privacy Policy URL
* Refund Policy URL

The configured brand name should automatically appear in:

* Website header
* Footer
* Browser title
* Login page
* Registration page
* Password reset emails
* Order emails
* Vendor emails
* Admin emails
* Notifications
* Receipts
* Invoices
* System messages
* Metadata where appropriate

The Admin should be able to change the branding without modifying source code.

---

# 5. SMTP / EMAIL CONFIGURATION

Create an Admin settings area for SMTP.

Allow configuration of:

* SMTP host
* SMTP port
* SMTP username
* SMTP password
* Encryption type
* Sender email
* Sender name
* Reply-to email

Include a:

"Send Test Email"

function.

Email configuration must be stored securely.

Do not expose SMTP passwords to normal users.

Create reusable email templates.

Important email events should include:

Customer:

* Registration
* Email verification
* Password reset
* Order confirmation
* Payment confirmation
* Order status update
* Refund notification
* Wallet transaction
* Account notification

Vendor:

* Registration
* Vendor approval
* Product approval
* Product rejection
* Sale notification
* Affiliate approval
* Affiliate sale
* Commission notification
* Withdrawal request
* Withdrawal status

Admin:

* New vendor registration
* Product awaiting approval
* New order
* Refund request
* Withdrawal request
* Important system alerts

---

# 6. THREE PRIMARY USER TYPES

The application has three major account types:

## A. ADMIN

Admin has global access.

Admin can manage:

* Users
* Vendors
* Products
* Categories
* Orders
* Payments
* Refunds
* Wallets
* Affiliates
* Commissions
* Withdrawals
* Reviews
* Coupons
* Settings
* Branding
* SMTP
* Content
* Site configuration
* Reports
* Audit logs
* Notifications

Admin should have a completely separate Admin Dashboard.

---

## B. CUSTOMER / USER

Customers are normal shoppers.

Customers can:

* Register
* Login
* Browse products
* Search
* Filter
* View product details
* Add products to cart
* Update cart
* Checkout
* Make payments
* View orders
* Track order status
* Request refunds
* Receive wallet credits
* View wallet balance
* View transaction history
* Save products
* Manage profile
* Manage addresses
* Leave reviews where permitted
* Receive notifications

---

## C. VENDOR

Vendors have their own account and storefront.

Vendors can:

* Register
* Login
* Create their vendor profile
* Create products
* Upload product images
* Submit products for approval
* View approval status
* Edit products
* Manage approved products
* View their shop
* View sales
* View orders involving their products
* View commissions
* View wallet
* View affiliate products
* Affiliate eligible Admin products
* Request withdrawals
* View transaction history
* Receive notifications

Vendors must NOT have unrestricted Admin privileges.

---

# 7. VENDOR PRODUCT SYSTEM

There are two primary product ownership models.

## MODEL A — ADMIN-OWNED PRODUCT

Admin creates a product.

Example:

Product:
"Premium Leather Bag"

Owner:
Admin

The Admin can publish it directly.

---

## MODEL B — VENDOR-OWNED PRODUCT

Vendor creates a product.

The product enters:

PENDING APPROVAL

The Admin reviews it.

Admin can:

* Approve
* Reject
* Request changes
* Disable
* Delete

A vendor product must not become publicly available until it has been approved.

---

# 8. AFFILIATE MARKETPLACE SYSTEM

This is a major feature.

A vendor should be able to browse products created by the Admin.

For example:

Admin owns:

"Premium Watch"

The vendor sees:

"Available for Affiliate"

The vendor clicks:

"Promote in My Shop"

The product then appears in the vendor's storefront as an affiliate product.

IMPORTANT:

The vendor does NOT become the owner of the product.

The Admin remains the actual seller/product owner.

The vendor simply becomes the affiliate/referral source.

---

# 9. AFFILIATE SALE LOGIC

Implement proper affiliate attribution.

Example:

Admin product:

$100

Affiliate commission:

10%

Customer purchases product through Vendor A's storefront.

Sale:

$100

Admin receives the main sale revenue.

Vendor A receives:

$10 affiliate commission

The exact percentage must be configurable.

Affiliate commission should support:

* Global default commission
* Product-specific commission
* Vendor-specific commission if desired
* Commission percentage
* Commission amount
* Commission status

Commission statuses:

* Pending
* Approved
* Available
* Paid
* Cancelled
* Reversed

If an order is refunded or cancelled, the affiliate commission must be appropriately reversed according to the configured business rules.

---

# 10. AFFILIATE ATTRIBUTION

Create a reliable affiliate attribution system.

Track:

* Affiliate vendor
* Product
* Order
* Order item
* Customer
* Commission percentage
* Commission amount
* Timestamp
* Attribution method
* Commission status

Avoid relying solely on browser cookies.

Whenever possible, store the affiliate relationship directly with the cart/order/order-item data.

The database should preserve the source vendor even if the customer later checks out.

Example:

ORDER

Order #10025

Customer:
John

Product:
Premium Watch

Product owner:
Admin

Affiliate vendor:
Vendor A

Sale amount:
$100

Affiliate commission:
$10

This information must remain available permanently for reporting.

---

# 11. VENDOR STOREFRONTS

Every approved vendor should have a public storefront.

Example:

/vendor/vendor-name

The storefront should contain:

* Vendor logo/profile image
* Vendor name
* Vendor description
* Products
* Affiliate products
* Categories
* Ratings where implemented
* Store statistics where appropriate
* Follow/store interaction if implemented later

Clearly distinguish:

"Sold by Vendor"

and

"Affiliate Product"

where necessary.

---

# 12. CUSTOMER WALLET

Customers should have an account balance/wallet.

The wallet is primarily intended for:

* Refund credits
* Store credits
* Promotional credits
* Other approved account credits

The customer should be able to see:

* Current balance
* Available balance
* Pending balance if applicable
* Transaction history

Transactions should include:

* Credit
* Debit
* Refund
* Adjustment
* Promotion
* Purchase deduction if the business model later supports wallet payments

Every wallet transaction must be recorded in an immutable ledger.

Never simply modify the balance without creating a transaction record.

---

# 13. VENDOR WALLET

Every vendor should have a financial account/wallet.

Track:

* Total sales
* Affiliate earnings
* Available balance
* Pending earnings
* Withdrawn amount
* Refund deductions
* Commission reversals
* Adjustments

Example:

Vendor earns:

$25 affiliate commission

Wallet:

Pending:
$25

After order eligibility period:

Available:
$25

Vendor requests withdrawal:

$20

Remaining:
$5

Every transaction must be recorded.

---

# 14. WALLET LEDGER

Create a proper ledger system.

Never rely only on:

users.balance

Instead create transaction records.

Each transaction should have:

* ID
* User/vendor ID
* Transaction type
* Amount
* Currency
* Direction
* Reference
* Related order
* Related commission
* Description
* Status
* Created date
* Updated date

Use database transactions when modifying balances.

Prevent:

* Double credits
* Double deductions
* Duplicate refunds
* Duplicate commissions

---

# 15. PRODUCT SYSTEM

Products should support:

* Product name
* Slug
* Description
* Short description
* SKU
* Price
* Compare-at price
* Sale price
* Currency
* Stock
* Inventory status
* Product images
* Thumbnail
* Gallery
* Category
* Subcategory
* Vendor
* Product owner
* Affiliate eligibility
* Affiliate commission
* Status
* Approval status
* Featured status
* Visibility
* Created date
* Updated date

Product statuses:

* Draft
* Pending
* Approved
* Published
* Rejected
* Suspended
* Archived

---

# 16. PRODUCT IMAGES

The visual identity of the site depends heavily on product photography.

Create a sophisticated image system.

Support:

* Main product image
* Multiple gallery images
* Thumbnail
* Image ordering
* Image deletion
* Image replacement

Images should be optimized where possible.

Do not allow unlimited giant uploads.

Implement:

* File type validation
* MIME validation
* File size limits
* Safe filenames
* Image dimension checks
* Secure upload handling

Do not allow executable files to be uploaded as images.

---

# 17. CATEGORIES

Admin should manage:

* Categories
* Subcategories
* Category images
* Category descriptions
* Category ordering
* Visibility

Products can belong to categories.

Build the database so the category system can grow later.

---

# 18. SEARCH AND DISCOVERY

Customers should have a strong product discovery experience.

Implement:

* Search
* Category browsing
* Filtering
* Sorting
* Price filtering
* Availability filtering
* Featured products
* New products
* Popular products where data allows
* Vendor filtering
* Affiliate/vendor storefront discovery

Search should be optimized for MySQL shared hosting.

Do not introduce Elasticsearch unless absolutely necessary.

---

# 19. HOMEPAGE DESIGN

The homepage should feel premium.

Visual direction:

* Classy
* Modern
* Minimal
* Glassmorphism
* Soft shadows
* Floating cards
* Large product imagery
* Strong typography
* Smooth transitions
* Subtle animations
* Spacious layouts
* Premium commerce aesthetic

Avoid making the interface look like a generic template.

The homepage should feel like a premium digital marketplace.

---

# 20. HERO SECTION

The hero should be visually powerful.

Use:

* Large imagery
* Large typography
* Product cards
* Floating glass panels
* Subtle motion
* Strong CTA

Possible sections:

Hero

Featured Products

Shop by Category

Trending Products

New Arrivals

Featured Vendors

Affiliate Marketplace

Why Shop With Us

Customer Reviews

Newsletter

Footer

The exact structure can be refined during design.

---

# 21. GLASSMORPHISM DESIGN SYSTEM

Create a reusable design system.

Use:

* Glass cards
* Translucent backgrounds
* Soft borders
* Blur effects
* Subtle shadows
* Rounded corners
* Floating elements
* Layered UI
* Elegant hover states
* Smooth transitions

Do not overuse glass effects.

The design should remain readable and fast.

Accessibility and readability take priority over visual effects.

---

# 22. ANIMATIONS

Animations should be subtle and premium.

Examples:

* Product card hover
* Image transitions
* Button hover
* Cart updates
* Modal appearance
* Page transitions
* Floating cards
* Scroll reveal
* Loading skeletons
* Toast notifications

Avoid excessive animations.

Respect:

prefers-reduced-motion

where practical.

---

# 23. NAVIGATION

Create a clean navigation system.

Desktop:

Logo

Shop

Categories

Vendors

Affiliate Marketplace where appropriate

Search

Account

Cart

Mobile:

Compact header

Search

Cart

Account/menu

Use a mobile navigation system that is easy for non-technical users.

---

# 24. CART

The cart is a critical page.

Create a polished cart experience.

The cart should display:

* Product image
* Product name
* Vendor
* Affiliate/vendor attribution where applicable
* Unit price
* Quantity
* Subtotal
* Remove
* Save for later if implemented
* Cart total

Include:

Subtotal

Discount

Shipping

Tax where applicable

Total

CTA:

"Continue to Checkout"

Cart updates should feel immediate.

Use AJAX where appropriate without compromising reliability.

---

# 25. CHECKOUT

Create a clean checkout flow.

Steps should be simple.

Possible structure:

1. Contact information
2. Delivery/billing information
3. Order summary
4. Payment
5. Confirmation

Do not make checkout unnecessarily complicated.

Display clear pricing.

Prevent accidental duplicate orders.

Use idempotency/order protection where possible.

---

# 26. ORDER SYSTEM

Create a complete order lifecycle.

Statuses should include:

* Pending Payment
* Paid
* Processing
* Shipped
* Delivered
* Cancelled
* Refunded
* Partially Refunded

Orders should contain:

* Customer
* Billing details
* Shipping details
* Items
* Product owner
* Vendor
* Affiliate vendor
* Prices
* Discounts
* Taxes
* Shipping
* Total
* Payment reference
* Order status
* Timestamps

Order items should preserve historical pricing.

If a product price changes later, old orders must not change.

---

# 27. REFUND SYSTEM

Customers should be able to request refunds.

Create:

* Refund request
* Refund reason
* Order reference
* Order item
* Amount
* Status
* Admin review
* Approval/rejection
* Refund method
* Wallet credit where appropriate
* Transaction record

Possible statuses:

* Requested
* Under Review
* Approved
* Rejected
* Processing
* Completed
* Cancelled

Refunds must correctly affect:

* Customer wallet
* Vendor earnings
* Affiliate commissions
* Order financial records

Prevent duplicate refunds.

---

# 28. ADMIN DASHBOARD

Create a completely separate Admin Dashboard.

The dashboard should provide an overview of:

* Revenue
* Orders
* Customers
* Vendors
* Products
* Pending approvals
* Refunds
* Affiliate commissions
* Withdrawals
* Wallet activity

Use elegant dashboard cards and charts where useful.

Admin should have clear navigation.

Suggested:

Dashboard

Orders

Products

Categories

Vendors

Customers

Affiliates

Wallets

Refunds

Withdrawals

Reviews

Coupons

Reports

Notifications

Settings

Branding

SMTP

Audit Logs

---

# 29. ADMIN PRODUCT MANAGEMENT

Admin should be able to:

* Create
* Edit
* Delete
* Publish
* Unpublish
* Feature
* Suspend
* Archive

Products.

Admin should also control:

* Affiliate eligibility
* Affiliate percentage
* Inventory
* Pricing
* Images
* Category
* Visibility

---

# 30. VENDOR APPROVAL

Vendor registration should support approval.

Vendor statuses:

* Pending
* Approved
* Rejected
* Suspended
* Banned

Admin should see vendor applications.

Admin can:

* Review
* Approve
* Reject
* Suspend

The system should notify the vendor of the decision.

---

# 31. VENDOR DASHBOARD

Create a polished vendor dashboard.

Show:

* Sales
* Orders
* Earnings
* Affiliate earnings
* Available balance
* Pending balance
* Products
* Product approvals
* Affiliate products
* Withdrawals
* Notifications

Keep the language simple.

For example:

Instead of:

"Commission Settlement"

Use:

"Money you've earned"

Instead of:

"Transaction Ledger"

Use:

"Money history"

The interface should be understandable to non-technical people.

---

# 32. CUSTOMER DASHBOARD

Customer dashboard should include:

* Overview
* Orders
* Wallet
* Saved items
* Addresses
* Profile
* Notifications
* Refunds
* Security settings

Use simple language.

---

# 33. AUTHENTICATION

Implement secure authentication.

Features:

* Registration
* Login
* Logout
* Email verification
* Forgot password
* Reset password
* Password change
* Session management
* Remember me where appropriate
* Account status

Use secure password hashing.

Never store plaintext passwords.

Use CSRF protection.

Protect authentication endpoints against abuse.

---

# 34. ROLE-BASED ACCESS CONTROL

Do not rely on hiding buttons.

Every protected route must enforce permissions server-side.

Roles:

ADMIN

VENDOR

CUSTOMER

A customer must never be able to access vendor/admin functionality by manually entering a URL.

A vendor must never be able to access admin functionality.

---

# 35. SECURITY

Security must be treated as a first-class requirement.

Implement protection against:

* SQL injection
* XSS
* CSRF
* Session attacks
* Broken access control
* File upload attacks
* Authentication abuse
* Password attacks
* Parameter tampering
* IDOR
* Unauthorized wallet manipulation
* Unauthorized order manipulation
* Duplicate payment processing

Use prepared statements/ORM/database abstraction safely.

Escape output.

Validate input.

Authorize every sensitive operation server-side.

Never trust:

* Hidden form fields
* Browser prices
* Browser user IDs
* Browser role information
* Browser wallet balances
* Browser commission amounts

All financial calculations must be performed server-side.

---

# 36. FINANCIAL INTEGRITY

This is extremely important.

The browser must NEVER determine:

* Final price
* Wallet credit
* Wallet debit
* Commission
* Refund amount
* Vendor earnings

The server must calculate these values.

Use database transactions for financial operations.

For example:

Order creation:

BEGIN TRANSACTION

Validate cart

Validate stock

Calculate prices

Create order

Create order items

Create payment record

Create affiliate attribution

COMMIT

If anything fails:

ROLLBACK

---

# 37. PAYMENT ARCHITECTURE

Do not permanently hard-code one payment provider.

Create a payment service abstraction.

Example:

PaymentProvider

Then providers can later include:

* Paystack
* Flutterwave
* Stripe
* Other providers

The exact provider can be configured later.

Support:

* Payment initiation
* Payment callback/webhook
* Payment verification
* Failed payment
* Successful payment
* Duplicate callback protection

Never mark an order paid simply because the browser says payment succeeded.

Verify payment server-side.

---

# 38. NOTIFICATION SYSTEM

Create an internal notification system.

Users should receive notifications for important events.

Notification fields:

* Recipient
* Type
* Title
* Message
* Link
* Read/unread
* Created time

Create:

* Notification dropdown
* Notification page
* Mark as read
* Mark all as read

---

# 39. AUDIT LOG

Create an Admin audit log.

Record sensitive actions.

Examples:

Admin approved vendor.

Admin rejected product.

Admin changed commission.

Admin changed branding.

Admin adjusted wallet.

Admin approved refund.

Admin suspended user.

Audit fields:

* Actor
* Action
* Entity
* Entity ID
* Previous value where appropriate
* New value where appropriate
* IP address
* Timestamp

Do not expose sensitive secrets in logs.

---

# 40. ADMIN SETTINGS

Create centralized settings.

Settings should include:

GENERAL

* Site name
* Currency
* Timezone
* Contact details

BRANDING

* Logo
* Favicon
* Colors
* Site title
* Tagline

EMAIL

* SMTP

COMMERCE

* Tax
* Shipping
* Affiliate defaults
* Refund settings

PAYMENT

* Payment provider configuration

SECURITY

* Session settings
* Login protection

SEO

* Default title
* Description
* Social sharing image

LEGAL

* Terms
* Privacy
* Refund policy

---

# 41. SEO

Build the website with SEO in mind.

Implement:

* Clean URLs
* Slugs
* Meta titles
* Meta descriptions
* Canonical URLs
* Open Graph metadata
* Structured data where appropriate
* Sitemap
* Robots.txt
* Product schema where appropriate

Admin should eventually be able to control SEO metadata.

---

# 42. PERFORMANCE

The site must be fast on shared hosting.

Implement sensible caching strategies that work on PHP shared hosting.

Examples:

* Browser caching
* Static asset caching
* Optimized database queries
* Pagination
* Image optimization
* Lazy loading
* Minified production assets
* Avoid unnecessary queries
* Avoid N+1 database problems

Do not introduce infrastructure that requires Redis unless optional.

---

# 43. DATABASE DESIGN

Before implementation, design the relational database.

Expected entities may include:

users

roles

user_profiles

vendors

vendor_profiles

products

product_images

categories

category_products

affiliate_products

affiliate_relationships

affiliate_commissions

carts

cart_items

orders

order_items

payments

refunds

wallets

wallet_transactions

withdrawals

addresses

reviews

notifications

coupons

settings

email_templates

audit_logs

sessions if needed

Do not blindly use this list.

Design the correct normalized schema based on the actual requirements.

Use:

* Primary keys
* Foreign keys
* Indexes
* Unique constraints
* Appropriate data types
* Timestamps

Financial amounts should use appropriate decimal types, not floating-point values.

---

# 44. DATABASE MIGRATIONS

Create database migrations or a reliable installation mechanism.

The project should be installable on a fresh database.

Provide:

* Schema
* Seed data
* Demo accounts
* Default settings

Create an installation process that can initialize the production database safely.

---

# 45. DEMO DATA

Create realistic demo data for local development.

Include:

* Admin account
* Customer accounts
* Vendor accounts
* Categories
* Admin products
* Vendor products
* Affiliate products
* Orders
* Wallet transactions
* Affiliate commissions

Do not use fake production credentials.

Clearly label development/demo credentials.

---

# 46. RESPONSIVE DESIGN

The website must work beautifully on:

* Desktop
* Laptop
* Tablet
* Mobile

Pay special attention to mobile shopping.

The following must work perfectly on small screens:

* Navigation
* Search
* Product cards
* Product pages
* Cart
* Checkout
* Account
* Vendor dashboard
* Admin dashboard

---

# 47. ACCESSIBILITY

Implement reasonable accessibility.

Use:

* Semantic HTML
* Keyboard navigation
* Proper labels
* Alt text
* Sufficient contrast
* Focus states
* Accessible forms
* Accessible dialogs

Do not sacrifice usability for visual effects.

---

# 48. ERROR HANDLING

Create friendly error handling.

Users should not see:

* SQL errors
* PHP stack traces
* File paths
* Credentials
* Debug information

Production:

Display:

"Something went wrong. Please try again."

Log the technical error privately.

Create appropriate:

* 404 page
* 403 page
* 419/CSRF error
* 500 page

---

# 49. ADMIN MEDIA MANAGEMENT

Consider creating a simple media management system.

Admin can upload:

* Logo
* Favicon
* Product images
* Category images
* Promotional images

Use safe upload handling.

---

# 50. CONTENT MANAGEMENT

Allow Admin to manage basic website content without editing code.

Examples:

* Homepage hero
* Homepage sections
* About content
* Contact information
* Footer content
* Terms
* Privacy
* Refund policy

Do not build an unnecessarily complicated CMS.

Keep it simple.

---

# 51. URL STRUCTURE

Create clean URLs.

Example:

/

/shop

/product/product-slug

/category/category-slug

/vendor/vendor-slug

/cart

/checkout

/account

/account/orders

/account/wallet

/account/settings

/login

/register

/vendor/dashboard

/vendor/products

/vendor/affiliates

/vendor/wallet

/admin

/admin/orders

/admin/products

/admin/vendors

/admin/settings

These are examples.

Adjust according to the final architecture.

---

# 52. DESIGN LANGUAGE

The design should feel:

Premium

Modern

Elegant

Clean

Alive

Soft

Expensive

Trustworthy

Avoid:

* Cheap-looking gradients
* Excessive neon
* Clutter
* Tiny text
* Overly technical language
* Generic bootstrap appearance
* Excessive borders
* Excessive animation

Use large imagery and generous whitespace.

The website should feel like a premium digital marketplace.

---

# 53. LANGUAGE STYLE

The target audience includes people who are not technical.

Therefore:

DO NOT use unnecessarily technical language.

Instead of:

"Authentication"

say:

"Sign in"

Instead of:

"Transaction ledger"

say:

"Money history"

Instead of:

"Affiliate attribution"

say:

"Your referral"

Instead of:

"Pending settlement"

say:

"Money waiting to become available"

Make labels short and understandable.

---

# 54. ADMIN SIMPLICITY

Even though the backend is sophisticated, the UI should remain simple.

Every important action should have:

* Clear title
* Clear explanation
* Obvious button
* Confirmation when destructive
* Success message
* Error message

Avoid confusing administrators with technical database terminology.

---

# 55. CONFIRMATION SYSTEM

For destructive operations:

* Delete
* Suspend
* Reject
* Refund
* Wallet adjustment

Require confirmation.

For financial actions, show the amount and reason clearly.

---

# 56. LOGGING

Implement application logging.

Log:

* Authentication failures
* Important exceptions
* Payment events
* Webhook events
* Refund events
* Wallet changes
* Affiliate commission changes
* Admin security events

Do not log passwords, SMTP passwords, payment secrets, or sensitive credentials.

---

# 57. CRON JOBS

If background tasks are required, design them around cPanel cron.

Possible tasks:

* Expire abandoned carts
* Process eligible commissions
* Send queued emails
* Clean temporary files
* Generate reports
* Cleanup sessions
* Other scheduled tasks

Do not require permanent background workers.

---

# 58. BACKUP STRATEGY

Prepare the system for backups.

Document:

* Database backup
* Uploaded images
* Configuration
* Restore procedure

Never assume a hosting backup alone is sufficient.

---

# 59. DEPLOYMENT PACKAGE

At the end of development, prepare a production deployment package suitable for cPanel.

The project should include documentation explaining:

1. Create MySQL database.
2. Create database user.
3. Assign privileges.
4. Upload application.
5. Configure environment.
6. Import database/migrations.
7. Configure storage directories.
8. Configure file permissions.
9. Configure SMTP.
10. Configure payment provider.
11. Configure domain.
12. Enable HTTPS.
13. Configure cron jobs.
14. Create Admin account.
15. Test production.
16. Disable debugging.

The application should not assume root access.

---

# 60. .ENV / CONFIGURATION

Separate secrets from source code.

Production configuration should support:

* APP_ENV
* APP_URL
* DB_HOST
* DB_DATABASE
* DB_USERNAME
* DB_PASSWORD
* SMTP_HOST
* SMTP_PORT
* SMTP_USERNAME
* SMTP_PASSWORD
* PAYMENT_KEYS
* Other secrets

Do not commit production secrets into source control.

If .env is used, ensure it cannot be publicly downloaded through the web server.

---

# 61. INSTALLER

If practical, create a secure installation/setup process.

It should check:

* PHP version
* Required extensions
* Database connectivity
* Storage permissions
* Configuration
* Required directories

Do not leave an open installer accessible after production installation.

---

# 62. TESTING STRATEGY

Testing must happen throughout development.

Create tests for:

AUTH

* Registration
* Login
* Logout
* Password reset
* Role permissions

PRODUCTS

* Product creation
* Product approval
* Product publishing
* Inventory

AFFILIATES

* Affiliate registration
* Affiliate product selection
* Attribution
* Commission calculation
* Commission reversal

ORDERS

* Cart
* Checkout
* Order creation
* Duplicate protection

WALLETS

* Credit
* Debit
* Refund
* Commission
* Withdrawal

SECURITY

* Unauthorized admin access
* Unauthorized vendor access
* CSRF
* XSS
* SQL injection
* IDOR

---

# 63. CRITICAL FINANCIAL TEST

Create automated/manual tests for this exact scenario:

Admin owns product.

Product price = $100.

Vendor A affiliates the product.

Commission = 10%.

Customer purchases through Vendor A.

Expected:

Admin sale = $100

Vendor affiliate commission = $10

Customer order total = $100

Vendor balance must receive exactly $10 according to commission settlement rules.

Then refund the order.

Verify:

Order becomes refunded.

Admin financial record adjusts correctly.

Vendor commission is reversed.

Customer receives refund according to configured refund method.

No duplicate transactions are created.

Run the same test multiple times.

The system must prevent duplicate financial operations.

---

# 64. USER EXPERIENCE TEST

Perform the entire journey as a customer:

1. Open website.
2. Browse homepage.
3. Search product.
4. Open product.
5. Add to cart.
6. Open cart.
7. Checkout.
8. Pay/test payment.
9. Receive order confirmation.
10. Open account.
11. View order.
12. Request refund.
13. Receive wallet credit where applicable.

Then test:

Vendor:

1. Register.
2. Wait for approval.
3. Login.
4. Create product.
5. Submit product.
6. Admin approves.
7. Product becomes public.
8. Vendor browses Admin products.
9. Vendor affiliates product.
10. Customer buys through vendor storefront.
11. Vendor sees commission.
12. Vendor sees wallet balance.
13. Vendor requests withdrawal.

Admin:

1. Login.
2. View dashboard.
3. Approve vendor.
4. Approve product.
5. Manage affiliate percentage.
6. View order.
7. View commission.
8. Process refund.
9. Adjust branding.
10. Configure SMTP.
11. Send test email.
12. Review audit log.

---

# 65. DEVELOPMENT PHASES

Build the application sequentially.

## PHASE 1 — REQUIREMENTS AND ARCHITECTURE

Do not code yet.

Produce:

* Architecture
* Module list
* Database design
* User roles
* Permission matrix
* Route map
* File/folder structure
* Deployment strategy
* Technology decisions

Stop and review the architecture before implementation.

---

## PHASE 2 — LOCAL ENVIRONMENT

Set up:

* PHP
* MySQL
* Local server
* Project structure
* Configuration
* Database connection
* Error handling
* Logging

Verify the application loads locally.

---

## PHASE 3 — DATABASE

Implement:

* Schema
* Migrations/install mechanism
* Indexes
* Constraints
* Seed data

Verify database integrity.

---

## PHASE 4 — CORE APPLICATION

Implement:

* Routing
* Controllers
* Models
* Views
* Services
* Validation
* Error handling
* Security middleware

---

## PHASE 5 — AUTHENTICATION AND ROLES

Implement:

* Customer registration
* Vendor registration
* Admin authentication
* Login
* Logout
* Password reset
* Email verification
* RBAC
* Session security

Test all permissions.

---

## PHASE 6 — ADMIN SYSTEM

Build Admin Dashboard.

Implement:

* Dashboard
* Users
* Vendors
* Products
* Categories
* Orders
* Refunds
* Wallets
* Affiliates
* Settings
* Branding
* SMTP
* Audit logs

---

## PHASE 7 — CUSTOMER SYSTEM

Build:

* Homepage
* Shop
* Search
* Product pages
* Cart
* Checkout
* Account
* Orders
* Wallet
* Refunds
* Notifications

---

## PHASE 8 — VENDOR SYSTEM

Build:

* Vendor registration
* Approval
* Vendor dashboard
* Vendor storefront
* Product management
* Product approval workflow
* Vendor wallet
* Withdrawals

---

## PHASE 9 — AFFILIATE SYSTEM

Build:

* Admin affiliate configuration
* Vendor affiliate marketplace
* Affiliate product selection
* Vendor storefront affiliate products
* Attribution
* Commission calculation
* Commission ledger
* Commission reversal

Test thoroughly.

---

## PHASE 10 — PAYMENTS

Implement the payment abstraction.

Connect the selected payment provider.

Implement:

* Payment initiation
* Verification
* Callback/webhook
* Duplicate protection
* Failed payments
* Successful payments

---

## PHASE 11 — WALLET AND REFUNDS

Implement:

* Customer wallet
* Vendor wallet
* Ledger
* Refunds
* Withdrawals
* Commission settlement

Perform financial integrity testing.

---

## PHASE 12 — PREMIUM UI

Now polish the visual experience.

Implement:

* Glassmorphism
* Hero sections
* Product photography
* Floating cards
* Animations
* Responsive layout
* Mobile navigation
* Loading states
* Empty states
* Error states
* Toasts
* Modals

Do not let visual effects compromise performance.

---

## PHASE 13 — SEO AND PERFORMANCE

Implement:

* SEO metadata
* Sitemap
* Robots
* Structured data
* Image optimization
* Lazy loading
* Asset optimization
* Database indexing
* Query optimization
* Caching

---

## PHASE 14 — SECURITY AUDIT

Perform a security review.

Test:

* Authentication
* Authorization
* Sessions
* CSRF
* XSS
* SQL injection
* Uploads
* IDOR
* Wallet manipulation
* Price manipulation
* Commission manipulation
* Payment manipulation

Fix every issue found.

---

## PHASE 15 — FULL QA

Test every major user journey.

Test:

* Desktop
* Mobile
* Tablet
* Different browsers
* Slow connections
* Empty states
* Failed payments
* Refunds
* Vendor rejection
* Product rejection
* Suspended accounts
* Out-of-stock products

---

## PHASE 16 — PRODUCTION PREPARATION

Prepare:

* Production configuration
* Database export/migrations
* Deployment instructions
* cPanel instructions
* Cron instructions
* SMTP setup
* Payment setup
* HTTPS
* File permissions
* Error logging
* Backup instructions

---

# 66. IMPORTANT DEVELOPMENT RULES

Follow these rules throughout development:

1. Do not skip architecture.
2. Do not blindly generate thousands of lines of code.
3. Build in small verified phases.
4. Test every phase.
5. Do not break existing functionality while adding new functionality.
6. Do not hard-code the brand name.
7. Do not hard-code financial values.
8. Do not trust browser-submitted financial values.
9. Do not expose secrets.
10. Do not assume VPS infrastructure.
11. Keep everything compatible with cPanel shared hosting.
12. Keep the interface understandable to ordinary users.
13. Keep the UI premium and visually polished.
14. Use reusable components.
15. Avoid unnecessary dependencies.
16. Optimize database queries.
17. Use secure server-side authorization.
18. Preserve financial history.
19. Use database transactions for financial operations.
20. Never create duplicate financial records.
21. Never expose debug information in production.
22. Do not leave installation tools unsecured.
23. Document deployment.
24. Keep the project maintainable.

---

# 67. WHEN YOU ENCOUNTER A DESIGN OR TECHNICAL DECISION

Do not randomly choose a solution.

Evaluate:

1. Is it compatible with PHP shared hosting?
2. Is it secure?
3. Is it scalable enough?
4. Is it simple to maintain?
5. Does it reduce unnecessary complexity?
6. Does it preserve future upgrade options?
7. Does it provide a good user experience?

Choose the simplest production-quality solution.

---

# 68. FINAL DELIVERABLE

The final result should be a complete PHP e-commerce marketplace that can be developed and tested locally and then deployed to Namecheap cPanel.

It should contain:

* Customer marketplace
* Vendor marketplace
* Admin dashboard
* Vendor dashboard
* Product management
* Product approval
* Affiliate marketplace
* Affiliate commissions
* Customer wallet
* Vendor wallet
* Refund system
* Order system
* Cart
* Checkout
* Payment integration architecture
* Notifications
* SMTP configuration
* Branding configuration
* SEO
* Security
* Audit logging
* Responsive design
* Premium glassmorphism UI
* Documentation
* Deployment package

---

# 69. MOST IMPORTANT INSTRUCTION

DO NOT start by creating random pages.

Start with:

PHASE 1 — ARCHITECTURE.

First provide:

1. Recommended PHP architecture.
2. Recommended folder structure.
3. Complete database schema.
4. Entity relationships.
5. User roles and permissions.
6. Complete module list.
7. Route map.
8. Wallet/financial architecture.
9. Affiliate architecture.
10. Authentication architecture.
11. Security architecture.
12. Payment architecture.
13. cPanel deployment architecture.
14. Local development setup.
15. Sequential implementation roadmap.

Then wait for the architecture to be reviewed before proceeding to implementation.

Once implementation begins, work phase-by-phase.

At the end of every phase:

* Test the phase.
* Identify problems.
* Fix problems.
* Confirm what was completed.
* State what remains.
* Then proceed to the next phase.

The objective is not simply to produce a visually attractive website.

The objective is to produce a **real, secure, maintainable, production-ready PHP marketplace that can actually run on Namecheap shared cPanel hosting.**
