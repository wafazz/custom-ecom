# Custom Ecom

Custom PHP ecommerce platform with multi-country support, multiple payment gateways, and courier integration.

## Tech Stack

- PHP 8.x (vanilla, no framework)
- MySQL 8.0
- jQuery / DataTables
- PHPMailer (Brevo SMTP)

## Features

- Product catalog with variants, attributes, categories & brands
- Multi-country pricing & currency conversion
- Cart system with session-based tracking
- Payment: Billplz, SenangPay, Stripe
- Shipping: J&T Express, DHL eCommerce
- Admin panel with role-based access (HQ, Account, Staff Admin, Sales, Logistic)
- Member registration with email verification
- Customer service ticket system
- Visitor analytics & abandon cart tracking
- AWB printing & bulk shipping

## Project Structure

```
index.php              # Entry point & router
config/
  mainConfig.php       # DB connection & global vars (copy from .example)
  function.php         # Helper functions (mail, etc)
route/
  routes.php           # All route definitions
controller/            # Controllers (Auth, Ecom, Order, Product, etc)
model/                 # Models (BaseModel, Cart, Order, Product)
view/
  Admin/               # Admin panel views
  ecom/                # Storefront views
  shop/                # Shop pages
assets/                # CSS, JS, images, fonts
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
- **Billplz** — `billplz` table
- **SenangPay** — `senangpay_api` table
- **Stripe** — `stripe_setting` table
- **J&T Express** — `jt_setting` table
- **DHL** — `dhl` table
- **SMTP (Brevo)** — `config/function.php`
