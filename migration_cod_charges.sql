-- COD Charges (benchmark-based)
-- Example: total < 100 → charge RM10, total >= 101 → charge RM8
CREATE TABLE IF NOT EXISTS `cod_charges` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `country_id` INT(11) NOT NULL,
    `min_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `max_amount` DECIMAL(10,2) DEFAULT NULL COMMENT 'NULL means no upper limit',
    `cod_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example data for Malaysia (country_id=1)
-- INSERT INTO `cod_charges` (`country_id`, `min_amount`, `max_amount`, `cod_fee`) VALUES (1, 0.00, 100.00, 10.00);
-- INSERT INTO `cod_charges` (`country_id`, `min_amount`, `max_amount`, `cod_fee`) VALUES (1, 100.01, NULL, 8.00);
