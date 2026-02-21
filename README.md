# Shaniena Empire - E-Commerce Platform

Custom PHP e-commerce platform with multi-country support, multiple payment gateways, courier integration, and PWA offline capabilities.

## Tech Stack

- PHP 8.x (custom MVC, no framework)
- MySQL 8.0
- jQuery 3.7 / DataTables 1.13.6
- Bootstrap 5 (Soft UI Dashboard for admin)
- PHPMailer (Brevo SMTP)
- Redis (session handling)
- Composer (dependency management)

## Features

### Storefront
- Product catalog with variants, attributes, categories & brands
- Multi-country pricing & currency conversion
- Cart system with session-based tracking
- Customer registration with email & phone verification
- Order tracking with AWB number
- Blog & announcements
- Support ticket system
- Referral system

### Payments
- SenangPay (FPX / credit card)
- Bayarcash (FPX / DuitNow)
- Stripe (international cards)
- Cash on Delivery (COD) with zone-based charges

### Shipping
- J&T Express (API integration + AWB printing)
- Ninjavan
- Poslaju
- DHL eCommerce (international)
- Pickup hub support
- Zone-based postage calculation (West/East Malaysia, international)

### Admin Panel
- Dashboard with live visitor count & real-time sales stats
- Role-based access control (HQ, Account, Staff Admin, Sales, Logistic)
- Order management with bulk status updates & AWB generation
- Product CRUD with variant management & stock control
- Staff management
- Courier & shipping settings
- Sales reports with period comparison
- Activity logging
- Customer service ticket management

### PWA (Progressive Web App)
- Installable on mobile home screen (Android & iOS)
- Offline fallback page when network unavailable
- Service worker with smart caching strategies:
  - **Pre-cache**: Core CSS, JS, logos for instant loading
  - **Cache-first**: Static assets & CDN resources
  - **Network-first**: HTML pages (falls back to cache, then offline page)
  - **Network-only**: POST requests, payment gateways, live data
- Install prompt: Native prompt on Android, share instructions on iOS Safari
- Theme color: `#e53637`

## Project Structure

```
index.php                # Entry point
router.php               # Route dispatcher
route/
  routes.php             # All GET/POST route definitions
config/
  mainConfig.php         # DB connection, session, domain vars
  function.php           # Global helper functions
  sales-compare.php      # Sales comparison helpers
  redis.php              # Redis session handler
controller/
  Auth/                  # Admin login
  Member/                # Dashboard, staff, profile
  Order/                 # Order status pages
  Product/               # Product CRUD
  Setting/               # Delivery, COD, policies
  Ecom/                  # Storefront, checkout, cart, payments
  API/                   # Mobile app API
  shop/                  # Shop auth & product pages
model/                   # 34 model classes (BaseModel + domain models)
view/
  Admin/                 # Admin panel views (Soft UI Dashboard)
  ecom/                  # Storefront views (e-{page}-keya88.php)
  shop/                  # Shop pages
assets/
  admin/                 # Admin CSS, JS (Soft UI)
  ecom/                  # Storefront CSS, JS, images, fonts
  images/                # Logos, product images
manifest.json            # PWA web app manifest
service-worker.js        # PWA service worker (cache version: shaniena-v1)
offline.html             # PWA offline fallback page
EmailTemplate/           # Email HTML templates
vendor/                  # Composer packages
```

## Setup

1. Clone the repo
```bash
git clone https://github.com/wafazz/custom-ecom.git
cd custom-ecom
```

2. Install dependencies
```bash
composer install
```

3. Setup config
```bash
cp config/mainConfig.php.example config/mainConfig.php
```
Edit `config/mainConfig.php` — set your DB credentials and domain URL.

4. Import database
```bash
mysql -u root -p your_db_name < migration.sql
mysql -u root -p your_db_name < sample_data.sql   # optional
```

5. Run locally
```bash
php -S localhost:8000 router.php
```

## Payment & Shipping Keys

Configure these in the admin panel or directly in the database:
- **SenangPay** — `senangpay_api` table
- **Bayarcash** — `bayarcash_setting` table
- **Stripe** — `stripe_setting` table
- **J&T Express** — `jt_setting` table
- **Ninjavan** — `ninjavan_setting` table
- **Poslaju** — `poslaju_setting` table
- **DHL** — `dhl` table
- **SMTP (Brevo)** — `config/function.php`

## Architecture

- **Routing**: Flat route array in `route/routes.php` → `Controller@method`
- **Models**: All extend `BaseModel` with `find()`, `findAll()`, `create()`, `update()`, `query()`, `execute()` — zero raw SQL in controllers
- **Views**: PHP templates with variables passed directly from controllers
- **Sessions**: Redis-backed via `config/redis.php`
- **PWA**: Service worker registered in `e-footer-keya88.php`, manifest linked in `e-header-keya88.php`
