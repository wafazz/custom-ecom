-- Bayarcash Payment Gateway API Configuration
CREATE TABLE IF NOT EXISTS `bayarcash_api` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `type` ENUM('sandbox','production') NOT NULL DEFAULT 'sandbox',
    `sandbox_api_token` VARCHAR(255) DEFAULT NULL,
    `sandbox_secret_key` VARCHAR(255) DEFAULT NULL,
    `sandbox_portal_key` VARCHAR(255) DEFAULT NULL,
    `api_token` VARCHAR(255) DEFAULT NULL,
    `secret_key` VARCHAR(255) DEFAULT NULL,
    `portal_key` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default sandbox row (update with your actual keys)
INSERT INTO `bayarcash_api` (`type`, `sandbox_api_token`, `sandbox_secret_key`, `sandbox_portal_key`)
VALUES ('sandbox', 'YOUR_SANDBOX_API_TOKEN', 'YOUR_SANDBOX_SECRET_KEY', 'YOUR_SANDBOX_PORTAL_KEY');

-- Bayarcash Transaction Log
CREATE TABLE IF NOT EXISTS `bayarcash_transactions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL,
    `order_number` VARCHAR(100) NOT NULL,
    `payment_intent_id` VARCHAR(255) DEFAULT NULL,
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `payment_channel` TINYINT(4) DEFAULT NULL COMMENT '1=FPX, 2=DuitNow QR, 3=DuitNow Online, 4=Credit Card, 5=SPayLater',
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0=New, 1=Pending, 2=Failed, 3=Successful, -1=Cancelled',
    `callback_payload` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_id` (`order_id`),
    KEY `idx_order_number` (`order_number`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
