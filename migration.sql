-- ============================================================
-- Migration Script - Shaniena Ecom
-- Generated from base_ecom.sql (2025_rozeyana)
-- Date: 2026-02-16
-- MySQL 8.0+ / utf8mb4
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- TABLE STRUCTURES
-- ============================================================

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` bigint NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `description` text NOT NULL,
  `table_name` text,
  `activities` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `all_country`
--

CREATE TABLE `all_country` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `sign` varchar(10) DEFAULT NULL,
  `phone_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apps_token`
--

CREATE TABLE `apps_token` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `awb_printed`
--

CREATE TABLE `awb_printed` (
  `id` bigint NOT NULL,
  `order_id` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `printed_by` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billplz`
--

CREATE TABLE `billplz` (
  `id` int NOT NULL,
  `sandbox_production` int NOT NULL COMMENT '0-sandbox, 1-production',
  `sand_box_url` varchar(255) NOT NULL,
  `production_url` varchar(255) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `x_signature` varchar(1500) NOT NULL,
  `bill_collection_id` varchar(50) NOT NULL,
  `payment_collection_slug` varchar(100) NOT NULL,
  `bill_charge` float(10,2) NOT NULL,
  `payment_charge` float(10,2) NOT NULL COMMENT '1-Seller/Company, 2-Customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(1500) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `p_id` bigint NOT NULL,
  `pv_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `weight` int NOT NULL,
  `total_weight` int NOT NULL,
  `currency_sign` varchar(255) NOT NULL,
  `country_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL COMMENT '0-new, 1-confirm, 2-return, 3-cancel, 4-abandon cart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_lock`
--

CREATE TABLE `cart_lock` (
  `id` bigint NOT NULL,
  `cart_id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `p_id` bigint NOT NULL,
  `pv_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `weight` int NOT NULL,
  `total_weight` int NOT NULL,
  `currency_sign` varchar(255) NOT NULL,
  `country_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locked_date` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL COMMENT '0-new, 1-confirm, 2-return, 3-cancel, 4-abandon cart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_lock_senangpay`
--

CREATE TABLE `cart_lock_senangpay` (
  `id` bigint NOT NULL,
  `cart_id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `p_id` bigint NOT NULL,
  `pv_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `weight` int NOT NULL,
  `total_weight` int NOT NULL,
  `currency_sign` varchar(255) NOT NULL,
  `country_id` bigint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `locked_date` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL COMMENT '0-new, 1-confirm, 2-return, 3-cancel, 4-abandon cart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `image` varchar(1500) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_customers`
--

CREATE TABLE `cs_customers` (
  `id` int NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_reply_attachments`
--

CREATE TABLE `cs_reply_attachments` (
  `id` int NOT NULL,
  `reply_id` int DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_staff_users`
--

CREATE TABLE `cs_staff_users` (
  `id` int NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_tickets`
--

CREATE TABLE `cs_tickets` (
  `id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `ticket_no` varchar(30) DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('new','in_progress','waiting_customer','resolved','closed') DEFAULT 'new',
  `order_id` varchar(255) DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `priority` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_ticket_attachments`
--

CREATE TABLE `cs_ticket_attachments` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_by` enum('customer','staff') DEFAULT 'customer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_ticket_logs`
--

CREATE TABLE `cs_ticket_logs` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_by` int DEFAULT NULL,
  `previous_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cs_ticket_replies`
--

CREATE TABLE `cs_ticket_replies` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `user_type` enum('customer','staff') DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `message` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

CREATE TABLE `customer_orders` (
  `id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `order_to` bigint NOT NULL,
  `product_var_id` varchar(1500) NOT NULL,
  `total_qty` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `postage_cost` decimal(10,2) NOT NULL,
  `currency_sign` varchar(255) NOT NULL,
  `country_id` int NOT NULL,
  `country` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postcode` varchar(50) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_name_last` varchar(255) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `payment_channel` varchar(255) NOT NULL,
  `payment_code` varchar(255) NOT NULL,
  `payment_url` varchar(255) NOT NULL,
  `ship_channel` varchar(255) NOT NULL,
  `courier_service` varchar(255) NOT NULL,
  `awb_number` varchar(255) NOT NULL,
  `tracking_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remark_comment` text NOT NULL,
  `tracking_milestone` text NOT NULL,
  `to_myr_rate` decimal(10,2) NOT NULL,
  `myr_value_include_postage` decimal(10,2) NOT NULL,
  `myr_value_without_postage` decimal(10,2) NOT NULL,
  `printed_awb` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dhl`
--

CREATE TABLE `dhl` (
  `id` bigint NOT NULL,
  `production_sandbox` int NOT NULL DEFAULT '1' COMMENT '1-production, 2-sandbox',
  `clientid` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `clientid_test` varchar(255) NOT NULL,
  `password_test` varchar(255) NOT NULL,
  `format_test` varchar(255) NOT NULL,
  `url_test` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_bulk_print`
--

CREATE TABLE `dhl_bulk_print` (
  `id` bigint NOT NULL,
  `order_id` text NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `deleted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_ship`
--

CREATE TABLE `dhl_ship` (
  `id` bigint NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `deliveryConfirmationNo` varchar(255) NOT NULL,
  `deliveryDepotCode` varchar(255) NOT NULL,
  `primarySortCode` varchar(255) NOT NULL,
  `secondarySortCode` varchar(255) NOT NULL,
  `shipmentID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_token`
--

CREATE TABLE `dhl_token` (
  `id` bigint NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_type` varchar(255) NOT NULL,
  `expires_in_seconds` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dhl_token_test`
--

CREATE TABLE `dhl_token_test` (
  `id` bigint NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_type` varchar(255) NOT NULL,
  `expires_in_seconds` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `image_setting`
--

CREATE TABLE `image_setting` (
  `id` bigint NOT NULL,
  `use_type` varchar(50) NOT NULL COMMENT 'logo,slider',
  `image_path` varchar(255) NOT NULL,
  `use_link` varchar(255) DEFAULT NULL,
  `sorting` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jt_code`
--

CREATE TABLE `jt_code` (
  `id` bigint NOT NULL,
  `order_id` bigint NOT NULL,
  `awb` varchar(255) NOT NULL,
  `jt_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jt_setting`
--

CREATE TABLE `jt_setting` (
  `id` bigint NOT NULL,
  `production_sandbox` int NOT NULL DEFAULT '0' COMMENT '0-Sandbox, 1-Production',
  `url_sandbox` varchar(255) NOT NULL,
  `username_sanbox` varchar(50) NOT NULL,
  `password_sandbox` varchar(50) NOT NULL,
  `cuscode_sandbox` varchar(50) NOT NULL,
  `key_sandbox` varchar(100) NOT NULL,
  `url_production` varchar(255) NOT NULL,
  `username_production` varchar(50) NOT NULL,
  `password_production` varchar(255) NOT NULL,
  `cuscode_production` varchar(50) NOT NULL,
  `key_production` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_country`
--

CREATE TABLE `list_country` (
  `id` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `sign` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `phone_code` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL,
  `status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_country_product_price`
--

CREATE TABLE `list_country_product_price` (
  `id` bigint NOT NULL,
  `country_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `market_price` float(10,2) NOT NULL,
  `sale_price` float(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postcode` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `verification_status` enum('unconfirm','confirm') DEFAULT 'unconfirm',
  `status` enum('inactive','active','banned') DEFAULT 'inactive',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_hq`
--

CREATE TABLE `member_hq` (
  `id` bigint NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sec_pin` varchar(255) NOT NULL,
  `f_name` varchar(255) NOT NULL,
  `l_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `role` int NOT NULL COMMENT '1-HQ, 2-Account, 3-Staff Admin, 4-Staff Sales, 5-Staff Logistic',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(11) NOT NULL COMMENT '0-Inactive, 1-Active, 2-Banned/Blocked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_blog`
--

CREATE TABLE `news_blog` (
  `id` bigint NOT NULL,
  `post_by` int NOT NULL,
  `update_by` varchar(1500) NOT NULL,
  `title` varchar(1500) NOT NULL,
  `contents` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `reader` varchar(1500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_visitor_return`
--

CREATE TABLE `online_visitor_return` (
  `id` bigint NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_end_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_visitor_unique`
--

CREATE TABLE `online_visitor_unique` (
  `id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_end_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint NOT NULL,
  `order_id` bigint NOT NULL,
  `hash_code` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_temp_data`
--

CREATE TABLE `order_temp_data` (
  `id` bigint NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `add_1` varchar(255) NOT NULL,
  `add_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postcode` varchar(50) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_id` bigint NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `method` varchar(255) NOT NULL,
  `currency_sign` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_hubs`
--

CREATE TABLE `pickup_hubs` (
  `id` bigint UNSIGNED NOT NULL,
  `hub_code` varchar(50) NOT NULL,
  `hub_name` varchar(150) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pickup_hub_staff`
--

CREATE TABLE `pickup_hub_staff` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `hub_id` bigint UNSIGNED NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policy`
--

CREATE TABLE `policy` (
  `id` bigint NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postage_cost`
--

CREATE TABLE `postage_cost` (
  `id` bigint NOT NULL,
  `country_id` int NOT NULL,
  `shipping_zone` int DEFAULT NULL,
  `currency` varchar(255) NOT NULL,
  `first_kilo` decimal(10,2) NOT NULL,
  `next_kilo` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postcode_my`
--

CREATE TABLE `postcode_my` (
  `postcode` char(5) DEFAULT NULL,
  `area_name` varchar(100) DEFAULT NULL,
  `post_office` varchar(50) DEFAULT NULL,
  `state_code` char(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `type` enum('simple','variable') NOT NULL DEFAULT 'simple',
  `category_id` int NOT NULL,
  `brand_id` int UNSIGNED DEFAULT NULL,
  `price_capital` decimal(10,2) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `weight` int NOT NULL,
  `length` int NOT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` int UNSIGNED NOT NULL,
  `attribute_id` int UNSIGNED NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `image` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `variant_name` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price_retail` decimal(10,2) NOT NULL,
  `price_sale` decimal(10,2) NOT NULL,
  `stock` int UNSIGNED DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `max_purchase` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_access`
--

CREATE TABLE `role_access` (
  `id` bigint NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `allowed_user` varchar(1500) NOT NULL,
  `sort` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_access_button`
--

CREATE TABLE `role_access_button` (
  `id` bigint NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `allowed_user` varchar(1500) NOT NULL,
  `sort` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senangpay_api`
--

CREATE TABLE `senangpay_api` (
  `id` bigint UNSIGNED NOT NULL,
  `merchant_id` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `pro_merchant_id` varchar(255) DEFAULT NULL,
  `pro_secret_key` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `type` enum('sandbox','production') NOT NULL DEFAULT 'sandbox',
  `sandbox_url` varchar(255) NOT NULL DEFAULT 'https://sandbox.senangpay.my/',
  `production_url` varchar(255) NOT NULL DEFAULT 'https://app.senangpay.my/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int UNSIGNED NOT NULL,
  `country_id` int NOT NULL DEFAULT '1',
  `shipping_zone` int NOT NULL DEFAULT '1',
  `state_code` char(3) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Table structure for table `state_my`
--

CREATE TABLE `state_my` (
  `state_code` char(3) NOT NULL,
  `state_name` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Table structure for table `stock_control`
--

CREATE TABLE `stock_control` (
  `id` bigint NOT NULL,
  `p_id` bigint NOT NULL,
  `pv_id` bigint NOT NULL,
  `stock_in` int NOT NULL,
  `stock_out` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stripe_setting`
--

CREATE TABLE `stripe_setting` (
  `id` bigint NOT NULL,
  `publish_key` varchar(255) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `webhook_secret` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `terms_conditions`
--

CREATE TABLE `terms_conditions` (
  `id` bigint NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `segment` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variant_attribute_values`
--

CREATE TABLE `variant_attribute_values` (
  `variant_id` int UNSIGNED NOT NULL,
  `attribute_value_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` bigint NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `cookie_id` varchar(64) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `visit_time` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INDEXES FOR DUMPED TABLES
-- ============================================================

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `all_country`
--
ALTER TABLE `all_country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apps_token`
--
ALTER TABLE `apps_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awb_printed`
--
ALTER TABLE `awb_printed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `billplz`
--
ALTER TABLE `billplz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_pv_status` (`pv_id`,`status`),
  ADD KEY `idx_cart_session_status` (`session_id`,`status`,`deleted_at`),
  ADD KEY `idx_cart_order_status` (`status`,`deleted_at`),
  ADD KEY `idx_cart_checkout` (`session_id`,`status`,`deleted_at`,`pv_id`,`quantity`),
  ADD KEY `idx_updated_at` (`updated_at`),
  ADD KEY `idx_session_updated` (`session_id`,`updated_at`),
  ADD KEY `idx_deleted_updated` (`deleted_at`,`updated_at`);

--
-- Indexes for table `cart_lock`
--
ALTER TABLE `cart_lock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_locked_date` (`locked_date`),
  ADD KEY `idx_session_locked` (`session_id`,`locked_date`),
  ADD KEY `idx_deleted_locked` (`deleted_at`,`locked_date`);

--
-- Indexes for table `cart_lock_senangpay`
--
ALTER TABLE `cart_lock_senangpay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_locked_date` (`locked_date`),
  ADD KEY `idx_session_locked` (`session_id`,`locked_date`),
  ADD KEY `idx_deleted_locked` (`deleted_at`,`locked_date`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `cs_customers`
--
ALTER TABLE `cs_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cs_reply_attachments`
--
ALTER TABLE `cs_reply_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reply_id` (`reply_id`);

--
-- Indexes for table `cs_staff_users`
--
ALTER TABLE `cs_staff_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cs_tickets`
--
ALTER TABLE `cs_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `cs_ticket_attachments`
--
ALTER TABLE `cs_ticket_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `cs_ticket_logs`
--
ALTER TABLE `cs_ticket_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `cs_ticket_replies`
--
ALTER TABLE `cs_ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_filter` (`status`,`deleted_at`,`created_at`),
  ADD KEY `idx_orders_status_deleted` (`status`,`deleted_at`),
  ADD KEY `idx_orders_created` (`created_at`),
  ADD KEY `idx_orders_country` (`country_id`),
  ADD KEY `idx_orders_checkout` (`status`,`deleted_at`,`created_at`),
  ADD KEY `idx_orders_session` (`session_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_country_created` (`country_id`,`created_at`),
  ADD KEY `idx_session_created` (`session_id`,`created_at`);

--
-- Indexes for table `dhl`
--
ALTER TABLE `dhl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dhl_bulk_print`
--
ALTER TABLE `dhl_bulk_print`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dhl_ship`
--
ALTER TABLE `dhl_ship`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dhl_token`
--
ALTER TABLE `dhl_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dhl_token_test`
--
ALTER TABLE `dhl_token_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `image_setting`
--
ALTER TABLE `image_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jt_code`
--
ALTER TABLE `jt_code`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jt_setting`
--
ALTER TABLE `jt_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `list_country`
--
ALTER TABLE `list_country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `list_country_product_price`
--
ALTER TABLE `list_country_product_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member_hq`
--
ALTER TABLE `member_hq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_blog`
--
ALTER TABLE `news_blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_visitor_return`
--
ALTER TABLE `online_visitor_return`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_session_end_at` (`session_end_at`),
  ADD KEY `idx_ip_created` (`ip_address`,`created_at`);

--
-- Indexes for table `online_visitor_unique`
--
ALTER TABLE `online_visitor_unique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_session_end_at` (`session_end_at`),
  ADD KEY `idx_session_created_end` (`created_at`,`session_end_at`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_temp_data`
--
ALTER TABLE `order_temp_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pickup_hubs`
--
ALTER TABLE `pickup_hubs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hub_code` (`hub_code`);

--
-- Indexes for table `pickup_hub_staff`
--
ALTER TABLE `pickup_hub_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hub_id` (`hub_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `policy`
--
ALTER TABLE `policy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postage_cost`
--
ALTER TABLE `postage_cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `postcode_my`
--
ALTER TABLE `postcode_my`
  ADD KEY `idx_postcode` (`postcode`),
  ADD KEY `idx_state_code` (`state_code`),
  ADD KEY `idx_city_name` (`post_office`) USING BTREE,
  ADD KEY `idx_place_name` (`area_name`) USING BTREE;

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `idx_products_status_deleted` (`status`,`deleted_at`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_brand` (`brand_id`),
  ADD KEY `idx_products_created` (`created_at`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_image_pid` (`product_id`,`id`),
  ADD KEY `idx_product_image` (`product_id`,`id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `role_access`
--
ALTER TABLE `role_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_access_button`
--
ALTER TABLE `role_access_button`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `senangpay_api`
--
ALTER TABLE `senangpay_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_state_name` (`name`);

--
-- Indexes for table `state_my`
--
ALTER TABLE `state_my`
  ADD PRIMARY KEY (`state_code`),
  ADD KEY `idx_state_name` (`state_name`);

--
-- Indexes for table `stock_control`
--
ALTER TABLE `stock_control`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stock_control_pv` (`pv_id`);

--
-- Indexes for table `stripe_setting`
--
ALTER TABLE `stripe_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD PRIMARY KEY (`variant_id`,`attribute_value_id`),
  ADD KEY `attribute_value_id` (`attribute_value_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

-- ============================================================
-- AUTO_INCREMENT FOR DUMPED TABLES
-- ============================================================

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `all_country`
--
ALTER TABLE `all_country`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apps_token`
--
ALTER TABLE `apps_token`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awb_printed`
--
ALTER TABLE `awb_printed`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `billplz`
--
ALTER TABLE `billplz`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_lock`
--
ALTER TABLE `cart_lock`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_lock_senangpay`
--
ALTER TABLE `cart_lock_senangpay`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_customers`
--
ALTER TABLE `cs_customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_reply_attachments`
--
ALTER TABLE `cs_reply_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_staff_users`
--
ALTER TABLE `cs_staff_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_tickets`
--
ALTER TABLE `cs_tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_ticket_attachments`
--
ALTER TABLE `cs_ticket_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_ticket_logs`
--
ALTER TABLE `cs_ticket_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cs_ticket_replies`
--
ALTER TABLE `cs_ticket_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dhl`
--
ALTER TABLE `dhl`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dhl_bulk_print`
--
ALTER TABLE `dhl_bulk_print`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dhl_ship`
--
ALTER TABLE `dhl_ship`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dhl_token`
--
ALTER TABLE `dhl_token`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dhl_token_test`
--
ALTER TABLE `dhl_token_test`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `image_setting`
--
ALTER TABLE `image_setting`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jt_code`
--
ALTER TABLE `jt_code`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jt_setting`
--
ALTER TABLE `jt_setting`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_country`
--
ALTER TABLE `list_country`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_country_product_price`
--
ALTER TABLE `list_country_product_price`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `member_hq`
--
ALTER TABLE `member_hq`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_blog`
--
ALTER TABLE `news_blog`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_visitor_return`
--
ALTER TABLE `online_visitor_return`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_visitor_unique`
--
ALTER TABLE `online_visitor_unique`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_temp_data`
--
ALTER TABLE `order_temp_data`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pickup_hubs`
--
ALTER TABLE `pickup_hubs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pickup_hub_staff`
--
ALTER TABLE `pickup_hub_staff`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policy`
--
ALTER TABLE `policy`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postage_cost`
--
ALTER TABLE `postage_cost`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_access`
--
ALTER TABLE `role_access`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_access_button`
--
ALTER TABLE `role_access_button`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `senangpay_api`
--
ALTER TABLE `senangpay_api`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_control`
--
ALTER TABLE `stock_control`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stripe_setting`
--
ALTER TABLE `stripe_setting`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `terms_conditions`
--
ALTER TABLE `terms_conditions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

-- ============================================================
-- CONSTRAINTS FOR DUMPED TABLES
-- ============================================================

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cs_reply_attachments`
--
ALTER TABLE `cs_reply_attachments`
  ADD CONSTRAINT `cs_reply_attachments_ibfk_1` FOREIGN KEY (`reply_id`) REFERENCES `cs_ticket_replies` (`id`);

--
-- Constraints for table `cs_tickets`
--
ALTER TABLE `cs_tickets`
  ADD CONSTRAINT `cs_tickets_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `cs_customers` (`id`);

--
-- Constraints for table `cs_ticket_attachments`
--
ALTER TABLE `cs_ticket_attachments`
  ADD CONSTRAINT `cs_ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `cs_tickets` (`id`);

--
-- Constraints for table `cs_ticket_logs`
--
ALTER TABLE `cs_ticket_logs`
  ADD CONSTRAINT `cs_ticket_logs_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `cs_tickets` (`id`);

--
-- Constraints for table `cs_ticket_replies`
--
ALTER TABLE `cs_ticket_replies`
  ADD CONSTRAINT `cs_ticket_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `cs_tickets` (`id`);

--
-- Constraints for table `pickup_hub_staff`
--
ALTER TABLE `pickup_hub_staff`
  ADD CONSTRAINT `fk_pickup_staff_hub` FOREIGN KEY (`hub_id`) REFERENCES `pickup_hubs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `product_attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD CONSTRAINT `variant_attribute_values_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variant_attribute_values_ibfk_2` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
