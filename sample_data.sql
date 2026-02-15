-- ============================================================
-- Sample Data - Shaniena Ecom
-- Run AFTER migration.sql
-- Date: 2026-02-16
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

-- ============================================================
-- ABOUT US / POLICY / T&C
-- ============================================================

INSERT INTO `about_us` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, '<h2>Welcome to Shaniena</h2><p>We are a Malaysian-based online store offering premium skincare and beauty products. Our mission is to provide high-quality products at affordable prices with fast delivery across Malaysia and Southeast Asia.</p>', NOW(), NOW());

INSERT INTO `policy` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, '<h3>Return & Refund Policy</h3><p>We accept returns within 7 days of delivery. Products must be unopened and in original packaging. Refunds will be processed within 5-7 business days.</p><h3>Shipping Policy</h3><p>Orders are processed within 1-2 business days. Delivery takes 2-5 business days for West Malaysia and 5-7 business days for East Malaysia.</p>', NOW(), NOW());

INSERT INTO `terms_conditions` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, '<h3>Terms & Conditions</h3><p>By using our website and placing orders, you agree to abide by these terms. All prices are in the local currency of your selected country. We reserve the right to cancel orders suspected of fraud.</p>', NOW(), NOW());

-- ============================================================
-- IMAGE SETTINGS (Logo)
-- ============================================================

INSERT INTO `image_setting` (`id`, `use_type`, `image_path`, `use_link`, `sorting`, `created_at`, `updated_at`) VALUES
(1, 'logo', 'assets/images/logo/shaniena-logo.png', NULL, 0, NOW(), NOW()),
(2, 'logo', 'assets/images/logo/shaniena-logo-white.png', NULL, 1, NOW(), NOW()),
(3, 'slider', 'assets/images/slider/banner-1.jpg', NULL, 0, NOW(), NOW()),
(4, 'slider', 'assets/images/slider/banner-2.jpg', NULL, 1, NOW(), NOW());

-- ============================================================
-- COUNTRIES
-- ============================================================

INSERT INTO `list_country` (`id`, `name`, `sign`, `rate`, `phone_code`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Malaysia', 'MYR', 1.00, '+60', NOW(), NOW(), 1),
(2, 'Singapore', 'SGD', 0.30, '+65', NOW(), NOW(), 1),
(3, 'Brunei', 'BND', 0.30, '+673', NOW(), NOW(), 0),
(4, 'Indonesia', 'IDR', 3600.00, '+62', NOW(), NOW(), 0),
(5, 'Thailand', 'THB', 7.80, '+66', NOW(), NOW(), 0);

-- ============================================================
-- STATES (Malaysia)
-- ============================================================

INSERT INTO `state` (`id`, `country_id`, `shipping_zone`, `state_code`, `name`) VALUES
(1, 1, 1, 'JHR', 'Johor'),
(2, 1, 1, 'KDH', 'Kedah'),
(3, 1, 1, 'KTN', 'Kelantan'),
(4, 1, 1, 'MLK', 'Melaka'),
(5, 1, 1, 'NSN', 'Negeri Sembilan'),
(6, 1, 1, 'PHG', 'Pahang'),
(7, 1, 1, 'PRK', 'Perak'),
(8, 1, 1, 'PLS', 'Perlis'),
(9, 1, 1, 'PNG', 'Pulau Pinang'),
(10, 1, 1, 'SGR', 'Selangor'),
(11, 1, 1, 'TRG', 'Terengganu'),
(12, 1, 1, 'KUL', 'W.P. Kuala Lumpur'),
(13, 1, 1, 'PJY', 'W.P. Putrajaya'),
(14, 1, 1, 'LBN', 'W.P. Labuan'),
(15, 1, 2, 'SBH', 'Sabah'),
(16, 1, 2, 'SWK', 'Sarawak');

INSERT INTO `state_my` (`state_code`, `state_name`) VALUES
('JHR', 'Johor'),
('KDH', 'Kedah'),
('KTN', 'Kelantan'),
('MLK', 'Melaka'),
('NSN', 'Negeri Sembilan'),
('PHG', 'Pahang'),
('PRK', 'Perak'),
('PLS', 'Perlis'),
('PNG', 'Pulau Pinang'),
('SGR', 'Selangor'),
('TRG', 'Terengganu'),
('KUL', 'W.P. Kuala Lumpur'),
('PJY', 'W.P. Putrajaya'),
('LBN', 'W.P. Labuan'),
('SBH', 'Sabah'),
('SWK', 'Sarawak');

-- ============================================================
-- POSTAGE COST
-- ============================================================

INSERT INTO `postage_cost` (`id`, `country_id`, `shipping_zone`, `currency`, `first_kilo`, `next_kilo`) VALUES
(1, 1, 1, 'MYR', 6.00, 2.00),
(2, 1, 2, 'MYR', 9.00, 3.00),
(3, 2, NULL, 'SGD', 15.00, 5.00);

-- ============================================================
-- BRANDS
-- ============================================================

INSERT INTO `brands` (`id`, `name`, `slug`, `image`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Shaniena Skincare', 'shaniena-skincare', 'assets/images/brands/shaniena.png', 'Premium Malaysian skincare brand', NOW(), NOW()),
(2, 'Glow Beauty', 'glow-beauty', 'assets/images/brands/glow.png', 'Natural glow beauty products', NOW(), NOW()),
(3, 'Pure Essence', 'pure-essence', 'assets/images/brands/pure.png', 'Organic and pure ingredients', NOW(), NOW());

-- ============================================================
-- CATEGORIES
-- ============================================================

INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `description`, `parent_id`, `sort_order`) VALUES
(1, 'Skincare', 'skincare', 'assets/images/categories/skincare.jpg', 'All skincare products', NULL, 1),
(2, 'Cleanser', 'cleanser', 'assets/images/categories/cleanser.jpg', 'Face cleansers and washes', 1, 1),
(3, 'Moisturizer', 'moisturizer', 'assets/images/categories/moisturizer.jpg', 'Face and body moisturizers', 1, 2),
(4, 'Serum', 'serum', 'assets/images/categories/serum.jpg', 'Face serums and treatments', 1, 3),
(5, 'Sunscreen', 'sunscreen', 'assets/images/categories/sunscreen.jpg', 'Sun protection products', 1, 4),
(6, 'Body Care', 'body-care', 'assets/images/categories/bodycare.jpg', 'Body care products', NULL, 2),
(7, 'Body Lotion', 'body-lotion', 'assets/images/categories/body-lotion.jpg', 'Body lotions and creams', 6, 1),
(8, 'Body Scrub', 'body-scrub', 'assets/images/categories/body-scrub.jpg', 'Exfoliating body scrubs', 6, 2),
(9, 'Makeup', 'makeup', 'assets/images/categories/makeup.jpg', 'Makeup and cosmetics', NULL, 3),
(10, 'Set & Bundle', 'set-bundle', 'assets/images/categories/bundle.jpg', 'Value sets and bundles', NULL, 4);

-- ============================================================
-- PRODUCTS
-- ============================================================

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `type`, `category_id`, `brand_id`, `price_capital`, `status`, `weight`, `length`, `width`, `height`) VALUES
(1, 'Hydra Glow Cleanser 150ml', 'hydra-glow-cleanser-150ml', 'Gentle foaming cleanser that removes dirt and impurities while keeping skin hydrated. Suitable for all skin types.', 'simple', 2, 1, 15.00, 1, 200, 5, 5, 15),
(2, 'Vitamin C Brightening Serum 30ml', 'vitamin-c-brightening-serum-30ml', 'Powerful vitamin C serum that brightens skin, reduces dark spots and evens skin tone. Contains 15% vitamin C.', 'simple', 4, 1, 25.00, 1, 100, 4, 4, 10),
(3, 'Daily UV Shield SPF50 PA+++ 50ml', 'daily-uv-shield-spf50-50ml', 'Lightweight sunscreen with broad spectrum protection. Non-greasy formula, suitable for daily use under makeup.', 'simple', 5, 1, 18.00, 1, 120, 4, 4, 12),
(4, 'Rose Water Toner 200ml', 'rose-water-toner-200ml', 'Refreshing rose water toner that balances skin pH and tightens pores. Made with natural rose extract.', 'simple', 4, 2, 12.00, 1, 250, 6, 6, 18),
(5, 'Collagen Night Cream 50g', 'collagen-night-cream-50g', 'Rich night cream packed with collagen and hyaluronic acid. Repairs and rejuvenates skin overnight.', 'variable', 3, 1, 30.00, 1, 150, 6, 6, 6),
(6, 'Tea Tree Body Lotion 250ml', 'tea-tree-body-lotion-250ml', 'Soothing body lotion with tea tree oil. Moisturizes and helps prevent body acne.', 'simple', 7, 3, 10.00, 1, 300, 7, 7, 18),
(7, 'Coffee Body Scrub 200g', 'coffee-body-scrub-200g', 'Exfoliating body scrub made with real coffee grounds. Removes dead skin cells and improves circulation.', 'simple', 8, 3, 12.00, 1, 250, 8, 8, 8),
(8, 'Complete Skincare Set (5pcs)', 'complete-skincare-set-5pcs', 'Complete skincare routine set including cleanser, toner, serum, moisturizer and sunscreen. Perfect starter kit.', 'simple', 10, 1, 80.00, 1, 800, 20, 15, 10),
(9, 'Niacinamide Serum 30ml', 'niacinamide-serum-30ml', '10% Niacinamide serum for pore minimizing and oil control. Suitable for oily and combination skin.', 'simple', 4, 2, 20.00, 1, 100, 4, 4, 10),
(10, 'Aloe Vera Gel Moisturizer 100ml', 'aloe-vera-gel-moisturizer-100ml', 'Lightweight gel moisturizer with 92% aloe vera. Cooling and hydrating for all skin types.', 'variable', 3, 3, 8.00, 1, 150, 5, 5, 8);

-- ============================================================
-- PRODUCT IMAGES
-- ============================================================

INSERT INTO `product_image` (`id`, `product_id`, `image`) VALUES
(1, 1, 'assets/images/products/hydra-cleanser-1.jpg'),
(2, 1, 'assets/images/products/hydra-cleanser-2.jpg'),
(3, 2, 'assets/images/products/vitc-serum-1.jpg'),
(4, 2, 'assets/images/products/vitc-serum-2.jpg'),
(5, 3, 'assets/images/products/uv-shield-1.jpg'),
(6, 4, 'assets/images/products/rose-toner-1.jpg'),
(7, 5, 'assets/images/products/collagen-cream-1.jpg'),
(8, 5, 'assets/images/products/collagen-cream-2.jpg'),
(9, 6, 'assets/images/products/teatree-lotion-1.jpg'),
(10, 7, 'assets/images/products/coffee-scrub-1.jpg'),
(11, 8, 'assets/images/products/skincare-set-1.jpg'),
(12, 8, 'assets/images/products/skincare-set-2.jpg'),
(13, 9, 'assets/images/products/niacinamide-1.jpg'),
(14, 10, 'assets/images/products/aloe-gel-1.jpg');

-- ============================================================
-- PRODUCT ATTRIBUTES & VARIANTS
-- ============================================================

INSERT INTO `product_attributes` (`id`, `name`) VALUES
(1, 'Size'),
(2, 'Shade');

INSERT INTO `product_attribute_values` (`id`, `attribute_id`, `value`) VALUES
(1, 1, '30g'),
(2, 1, '50g'),
(3, 1, '100ml'),
(4, 1, '200ml'),
(5, 2, 'Light'),
(6, 2, 'Medium'),
(7, 2, 'Dark');

-- Simple products get 1 variant each
INSERT INTO `product_variants` (`id`, `product_id`, `variant_name`, `sku`, `price_retail`, `price_sale`, `stock`, `image`, `max_purchase`, `status`) VALUES
(1, 1, 'Hydra Glow Cleanser 150ml', 'HGC-150', 39.90, 29.90, 500, NULL, 10, 1),
(2, 2, 'Vitamin C Serum 30ml', 'VCS-30', 69.90, 49.90, 300, NULL, 5, 1),
(3, 3, 'UV Shield SPF50 50ml', 'UVS-50', 49.90, 39.90, 400, NULL, 10, 1),
(4, 4, 'Rose Water Toner 200ml', 'RWT-200', 35.90, 25.90, 350, NULL, 10, 1),
-- Variable product: Collagen Night Cream (2 sizes)
(5, 5, 'Collagen Night Cream - 30g', 'CNC-30', 59.90, 45.90, 200, NULL, 5, 1),
(6, 5, 'Collagen Night Cream - 50g', 'CNC-50', 89.90, 69.90, 150, NULL, 5, 1),
(7, 6, 'Tea Tree Body Lotion 250ml', 'TTL-250', 29.90, 22.90, 400, NULL, 10, 1),
(8, 7, 'Coffee Body Scrub 200g', 'CBS-200', 34.90, 27.90, 300, NULL, 10, 1),
(9, 8, 'Complete Skincare Set', 'CSS-5PC', 199.90, 149.90, 100, NULL, 3, 1),
(10, 9, 'Niacinamide Serum 30ml', 'NCS-30', 59.90, 44.90, 250, NULL, 5, 1),
-- Variable product: Aloe Vera Gel (2 sizes)
(11, 10, 'Aloe Vera Gel - 50ml', 'AVG-50', 19.90, 15.90, 500, NULL, 10, 1),
(12, 10, 'Aloe Vera Gel - 100ml', 'AVG-100', 29.90, 22.90, 350, NULL, 10, 1);

-- Link variable product variants to attribute values
INSERT INTO `variant_attribute_values` (`variant_id`, `attribute_value_id`) VALUES
(5, 1),   -- CNC-30 -> Size: 30g
(6, 2),   -- CNC-50 -> Size: 50g
(11, 1),  -- AVG-50 -> Size: 30g (close enough)
(12, 3);  -- AVG-100 -> Size: 100ml

-- ============================================================
-- COUNTRY PRODUCT PRICES (Singapore)
-- ============================================================

INSERT INTO `list_country_product_price` (`id`, `country_id`, `product_id`, `market_price`, `sale_price`) VALUES
(1, 2, 1, 15.90, 11.90),
(2, 2, 2, 25.90, 19.90),
(3, 2, 3, 19.90, 15.90),
(4, 2, 5, 35.90, 27.90),
(5, 2, 8, 79.90, 59.90);

-- ============================================================
-- MEMBER HQ (Admin Users)
-- ============================================================

INSERT INTO `member_hq` (`id`, `email`, `password`, `sec_pin`, `f_name`, `l_name`, `phone`, `role`, `created_at`, `updated_at`, `status`) VALUES
(1, 'admin@shaniena.com', '4debdd6fb38631052e6123348c6b0a3f31d38ca8bc57e7ec1030964ea0a2d941', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'Admin', 'Shaniena', '', 1, NOW(), NOW(), '1'),
(2, 'sales@shaniena.com', '7702d287eaa45b493377521ea80a4b95cf343525b2399a377b8c7b773587c3b9', '7702d287eaa45b493377521ea80a4b95cf343525b2399a377b8c7b773587c3b9', 'Sarah', 'Ahmad', '0123456789', 4, NOW(), NOW(), '1'),
(3, 'logistic@shaniena.com', '7702d287eaa45b493377521ea80a4b95cf343525b2399a377b8c7b773587c3b9', '7702d287eaa45b493377521ea80a4b95cf343525b2399a377b8c7b773587c3b9', 'Ali', 'Hassan', '0129876543', 5, NOW(), NOW(), '1');

-- ============================================================
-- MEMBERS (Customers)
-- ============================================================

INSERT INTO `members` (`id`, `email`, `password`, `name`, `phone`, `address_1`, `address_2`, `city`, `postcode`, `state`, `verification_code`, `verification_status`, `status`) VALUES
(1, 'aisyah@gmail.com', '$2y$10$qHr8zuc72ZD4IiQ6PiMDp.UWC3sbiM8owdAfMybuw9N7yKVX90wma', 'Aisyah Binti Rahman', '0171234567', 'No 12, Jalan Melor', 'Taman Bunga', 'Shah Alam', '40000', 'Selangor', '577508', 'confirm', 'active'),
(2, 'nurul@gmail.com', '$2y$10$eBeBJ1pCmWdDqJwtKFk9cO.E4C470jJgIZP01SL9vq4./Y0BWGVU2', 'Nurul Hidayah', '0182345678', 'Blok A-12-3, Residensi Harmoni', '', 'Petaling Jaya', '47301', 'Selangor', '975529', 'confirm', 'active'),
(3, 'farah@gmail.com', '$2y$10$szvrO7WTho4zyKIAOqCn.uMIMQlfoI8Kfkmkjj3SNKBZkY4zQC66i', 'Farah Nadia', '0193456789', '45, Lorong Cempaka 3', 'Taman Sri Rampai', 'Kuala Lumpur', '53300', 'W.P. Kuala Lumpur', '983301', 'confirm', 'active'),
(4, 'siti@gmail.com', '$2y$10$sEeJAu8qFfUV3vlNAj/22ufUSfml3gcgTF23ptIcX9U1IuZJg0hd6', 'Siti Aminah', '0134567890', '88, Jalan Hang Tuah', '', 'Melaka', '75100', 'Melaka', '982066', 'confirm', 'active'),
(5, 'diana@gmail.com', '$2y$10$g4CWBl7TCIDpUpImQ15jC.mtppcmF4ZaCLaTvEfx.qPOfP.paNe8O', 'Diana Syazwani', '0145678901', 'No 7, Jalan Mahsuri', 'Bayan Lepas', 'Penang', '11900', 'Pulau Pinang', '358951', 'unconfirm', 'inactive');

-- ============================================================
-- SAMPLE ORDERS
-- ============================================================

INSERT INTO `customer_orders` (`id`, `session_id`, `order_to`, `product_var_id`, `total_qty`, `total_price`, `postage_cost`, `currency_sign`, `country_id`, `country`, `state`, `city`, `postcode`, `address_2`, `address_1`, `customer_name`, `customer_name_last`, `customer_phone`, `customer_email`, `status`, `payment_channel`, `payment_code`, `payment_url`, `ship_channel`, `courier_service`, `awb_number`, `tracking_url`, `remark_comment`, `tracking_milestone`, `to_myr_rate`, `myr_value_include_postage`, `myr_value_without_postage`, `printed_awb`) VALUES
(1, 'sess_abc123def456', 1, '[{"pv_id":1,"qty":2},{"pv_id":2,"qty":1}]', 3, 109.70, 6.00, 'MYR', 1, 'Malaysia', 'Selangor', 'Shah Alam', '40000', 'Taman Bunga', 'No 12, Jalan Melor', 'Aisyah', 'Rahman', '0171234567', 'aisyah@gmail.com', 3, 'billplz', 'BP-ORDER001', '', 'jt', 'J&T Express', '631838533514', 'https://www.jtexpress.my/tracking', '', '[]', 1.00, 115.70, 109.70, 1),
(2, 'sess_ghi789jkl012', 1, '[{"pv_id":3,"qty":1},{"pv_id":10,"qty":2}]', 3, 129.70, 6.00, 'MYR', 1, 'Malaysia', 'Selangor', 'Petaling Jaya', '47301', '', 'Blok A-12-3, Residensi Harmoni', 'Nurul', 'Hidayah', '0182345678', 'nurul@gmail.com', 3, 'stripe', 'pi_stripe002', '', 'jt', 'J&T Express', '631841051155', 'https://www.jtexpress.my/tracking', '', '[]', 1.00, 135.70, 129.70, 1),
(3, 'sess_mno345pqr678', 1, '[{"pv_id":9,"qty":1}]', 1, 149.90, 6.00, 'MYR', 1, 'Malaysia', 'W.P. Kuala Lumpur', 'Kuala Lumpur', '53300', 'Taman Sri Rampai', '45, Lorong Cempaka 3', 'Farah', 'Nadia', '0193456789', 'farah@gmail.com', 1, 'billplz', 'BP-ORDER003', '', '', '', '', '', 'Please deliver before 5pm', '[]', 1.00, 155.90, 149.90, 0),
(4, 'sess_stu901vwx234', 1, '[{"pv_id":6,"qty":1},{"pv_id":7,"qty":1}]', 2, 92.80, 6.00, 'MYR', 1, 'Malaysia', 'Melaka', 'Melaka', '75100', '', '88, Jalan Hang Tuah', 'Siti', 'Aminah', '0134567890', 'siti@gmail.com', 2, 'senangpay', 'SP-ORDER004', '', 'dhl', 'DHL eCommerce', '', '', '', '[]', 1.00, 98.80, 92.80, 0),
(5, 'sess_yz567abc890', 2, '[{"pv_id":2,"qty":1}]', 1, 19.90, 15.00, 'SGD', 2, 'Singapore', 'Singapore', 'Singapore', '238801', '', '1 Orchard Road #01-01', 'Lim', 'Wei Ting', '+6591234567', 'weiting@gmail.com', 0, '', '', '', '', '', '', '', '', '[]', 0.30, 10.47, 5.97, 0);

-- ============================================================
-- ORDER DETAILS (hash for tracking page)
-- ============================================================

INSERT INTO `order_details` (`id`, `order_id`, `hash_code`) VALUES
(1, 1, 'a1b2c3d4e5f6g7h8i9j0'),
(2, 2, 'k1l2m3n4o5p6q7r8s9t0'),
(3, 3, 'u1v2w3x4y5z6a7b8c9d0'),
(4, 4, 'e1f2g3h4i5j6k7l8m9n0'),
(5, 5, 'o1p2q3r4s5t6u7v8w9x0');

-- ============================================================
-- J&T CODES (for shipped orders)
-- ============================================================

INSERT INTO `jt_code` (`id`, `order_id`, `awb`, `jt_code`) VALUES
(1, 1, '631838533514', '300-E15-PJ324'),
(2, 2, '631841051155', '300-E15-PJ324');

-- ============================================================
-- STOCK CONTROL
-- ============================================================

INSERT INTO `stock_control` (`id`, `p_id`, `pv_id`, `stock_in`, `stock_out`, `comment`) VALUES
(1, 1, 1, 500, 0, 'Initial stock'),
(2, 2, 2, 300, 0, 'Initial stock'),
(3, 3, 3, 400, 0, 'Initial stock'),
(4, 4, 4, 350, 0, 'Initial stock'),
(5, 5, 5, 200, 0, 'Initial stock - 30g'),
(6, 5, 6, 150, 0, 'Initial stock - 50g'),
(7, 6, 7, 400, 0, 'Initial stock'),
(8, 7, 8, 300, 0, 'Initial stock'),
(9, 8, 9, 100, 0, 'Initial stock'),
(10, 9, 10, 250, 0, 'Initial stock'),
(11, 10, 11, 500, 0, 'Initial stock - 50ml'),
(12, 10, 12, 350, 0, 'Initial stock - 100ml'),
-- Stock out from orders
(13, 1, 1, 0, 2, 'Order #1'),
(14, 2, 2, 0, 1, 'Order #1'),
(15, 3, 3, 0, 1, 'Order #2'),
(16, 9, 10, 0, 2, 'Order #2'),
(17, 8, 9, 0, 1, 'Order #3'),
(18, 5, 6, 0, 1, 'Order #4'),
(19, 6, 7, 0, 1, 'Order #4'),
(20, 2, 2, 0, 1, 'Order #5 (SGD)');

-- ============================================================
-- CART (active abandoned cart)
-- ============================================================

INSERT INTO `cart` (`id`, `session_id`, `p_id`, `pv_id`, `quantity`, `price`, `weight`, `total_weight`, `currency_sign`, `country_id`, `status`) VALUES
(1, 'sess_abandon_001', 2, 2, 1, 49.90, 100, 100, 'MYR', 1, 4),
(2, 'sess_abandon_001', 7, 8, 2, 27.90, 250, 500, 'MYR', 1, 4),
(3, 'sess_active_002', 1, 1, 3, 29.90, 200, 600, 'MYR', 1, 0);

-- ============================================================
-- PAYMENT GATEWAY SETTINGS (sandbox defaults)
-- ============================================================

INSERT INTO `billplz` (`id`, `sandbox_production`, `sand_box_url`, `production_url`, `api_key`, `x_signature`, `bill_collection_id`, `payment_collection_slug`, `bill_charge`, `payment_charge`) VALUES
(1, 0, 'https://www.billplz-sandbox.com/', 'https://www.billplz.com/', 'your-sandbox-api-key', 'your-sandbox-x-signature', 'your-collection-id', 'your-payment-slug', 1.00, 1.00);

INSERT INTO `stripe_setting` (`id`, `publish_key`, `secret_key`, `webhook_secret`) VALUES
(1, 'pk_test_xxxxxxxxxxxx', 'sk_test_xxxxxxxxxxxx', 'whsec_xxxxxxxxxxxx');

-- ============================================================
-- SHIPPING SETTINGS (sandbox defaults)
-- ============================================================

INSERT INTO `dhl` (`id`, `production_sandbox`, `clientid`, `password`, `format`, `url`, `clientid_test`, `password_test`, `format_test`, `url_test`) VALUES
(1, 2, 'your-production-clientid', 'your-production-password', 'json', 'https://api.dhlecommerce.dhl.com/', 'your-test-clientid', 'your-test-password', 'json', 'https://apitest.dhlecommerce.asia/');

INSERT INTO `jt_setting` (`id`, `production_sandbox`, `url_sandbox`, `username_sanbox`, `password_sandbox`, `cuscode_sandbox`, `key_sandbox`, `url_production`, `username_production`, `password_production`, `cuscode_production`, `key_production`) VALUES
(1, 0, 'https://demostandard.jtexpress.my/blibli/order/createOrder', 'TEST', 'TES123', 'ITTEST0001', 'your-sandbox-key', 'https://ylstandard.jtexpress.my/blibli/order/createOrder', 'your-username', 'your-production-password', 'your-cuscode', 'your-production-key');

-- ============================================================
-- ROLE ACCESS (sample pages)
-- ============================================================

INSERT INTO `role_access` (`id`, `page_url`, `name`, `allowed_user`, `sort`) VALUES
(1, '/admin/dashboard', 'Dashboard', '1,2,3,4,5', 1),
(2, '/admin/orders', 'Orders', '1,2,3,4', 2),
(3, '/admin/products', 'Products', '1,2,3', 3),
(4, '/admin/members', 'Members', '1,2', 4),
(5, '/admin/settings', 'Settings', '1', 5),
(6, '/admin/reports', 'Reports', '1,2', 6),
(7, '/admin/shipping', 'Shipping', '1,3,5', 7),
(8, '/admin/cs-tickets', 'Customer Service', '1,2,3,4', 8);

INSERT INTO `role_access_button` (`id`, `page_url`, `name`, `allowed_user`, `sort`) VALUES
(1, '/admin/orders', 'btn_update_status', '1,2,3', 1),
(2, '/admin/orders', 'btn_print_awb', '1,3,5', 2),
(3, '/admin/products', 'btn_add_product', '1,2,3', 1),
(4, '/admin/products', 'btn_delete_product', '1', 2),
(5, '/admin/members', 'btn_ban_member', '1', 1);

-- ============================================================
-- NEWS BLOG
-- ============================================================

INSERT INTO `news_blog` (`id`, `post_by`, `update_by`, `title`, `contents`, `reader`) VALUES
(1, 1, '1', 'Welcome to Shaniena!', '<p>We are excited to launch our new online store. Discover premium skincare products carefully curated for Malaysian skin.</p>', ''),
(2, 1, '1', 'How to Build Your Skincare Routine', '<p>A good skincare routine starts with cleansing, followed by toning, serum, moisturizer, and sunscreen. Check out our Complete Skincare Set for everything you need!</p>', '');

-- ============================================================
-- VISITORS (sample analytics data)
-- ============================================================

INSERT INTO `visitors` (`id`, `ip_address`, `user_agent`, `cookie_id`, `country`, `visit_time`) VALUES
(1, '203.0.113.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)', 'cookie_abc123', 'Malaysia', NOW() - INTERVAL 2 DAY),
(2, '203.0.113.2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'cookie_def456', 'Malaysia', NOW() - INTERVAL 1 DAY),
(3, '203.0.113.3', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0)', 'cookie_ghi789', 'Singapore', NOW() - INTERVAL 1 DAY),
(4, '203.0.113.4', 'Mozilla/5.0 (Linux; Android 14)', 'cookie_jkl012', 'Malaysia', NOW()),
(5, '203.0.113.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)', 'cookie_abc123', 'Malaysia', NOW());

INSERT INTO `online_visitor_unique` (`id`, `session_id`, `ip_address`, `created_at`, `updated_at`, `session_end_at`) VALUES
(1, 'sess_vis_001', '203.0.113.1', NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 2 DAY + INTERVAL 15 MINUTE),
(2, 'sess_vis_002', '203.0.113.2', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY + INTERVAL 8 MINUTE),
(3, 'sess_vis_003', '203.0.113.3', NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 DAY + INTERVAL 22 MINUTE),
(4, 'sess_vis_004', '203.0.113.4', NOW(), NOW(), NOW() + INTERVAL 10 MINUTE);

INSERT INTO `online_visitor_return` (`id`, `ip_address`, `created_at`, `updated_at`, `session_end_at`) VALUES
(1, '203.0.113.1', NOW(), NOW(), NOW() + INTERVAL 12 MINUTE);

-- ============================================================
-- USER ACTIVITIES (sample audit log)
-- ============================================================

INSERT INTO `user_activities` (`id`, `user_id`, `user_name`, `segment`, `details`) VALUES
(1, 1, 'Admin Shaniena', 'product', 'Added product: Hydra Glow Cleanser 150ml'),
(2, 1, 'Admin Shaniena', 'product', 'Added product: Vitamin C Brightening Serum 30ml'),
(3, 1, 'Admin Shaniena', 'product', 'Added product: Complete Skincare Set (5pcs)'),
(4, 2, 'Sarah Ahmad', 'order', 'Updated order #1 status to Shipped'),
(5, 2, 'Sarah Ahmad', 'order', 'Updated order #2 status to Shipped'),
(6, 3, 'Ali Hassan', 'shipping', 'Printed AWB for order #1'),
(7, 3, 'Ali Hassan', 'shipping', 'Printed AWB for order #2');

-- ============================================================
-- SAMPLE POSTCODE DATA (small subset)
-- ============================================================

INSERT INTO `postcode_my` (`postcode`, `area_name`, `post_office`, `state_code`) VALUES
('40000', 'Shah Alam', 'Shah Alam', 'SGR'),
('40100', 'Shah Alam', 'Shah Alam', 'SGR'),
('40150', 'Shah Alam', 'Shah Alam', 'SGR'),
('40170', 'Shah Alam', 'Shah Alam', 'SGR'),
('40200', 'Shah Alam', 'Shah Alam', 'SGR'),
('47300', 'Petaling Jaya', 'Petaling Jaya', 'SGR'),
('47301', 'Petaling Jaya', 'Petaling Jaya', 'SGR'),
('47400', 'Petaling Jaya', 'Petaling Jaya', 'SGR'),
('50000', 'Kuala Lumpur', 'Kuala Lumpur', 'KUL'),
('50100', 'Kuala Lumpur', 'Kuala Lumpur', 'KUL'),
('50200', 'Kuala Lumpur', 'Kuala Lumpur', 'KUL'),
('53300', 'Setapak', 'Kuala Lumpur', 'KUL'),
('75100', 'Melaka', 'Melaka', 'MLK'),
('75200', 'Melaka', 'Melaka', 'MLK'),
('11900', 'Bayan Lepas', 'Bayan Lepas', 'PNG'),
('10000', 'Georgetown', 'Penang', 'PNG'),
('80000', 'Johor Bahru', 'Johor Bahru', 'JHR'),
('81100', 'Johor Bahru', 'Johor Bahru', 'JHR'),
('88000', 'Kota Kinabalu', 'Kota Kinabalu', 'SBH'),
('93000', 'Kuching', 'Kuching', 'SWK');
