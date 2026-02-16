-- COD Charges (benchmark-based, one row per country+zone)
-- Example: benchmark = RM100, below_fee = RM10, above_fee = RM8
-- Sales < RM100 → charge RM10, Sales >= RM100 → charge RM8
CREATE TABLE IF NOT EXISTS `cod_charges` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `country_id` INT(11) NOT NULL,
    `shipping_zone` VARCHAR(10) NOT NULL DEFAULT '1' COMMENT '1=West MY, 2=East MY, 1=default for other countries',
    `benchmark_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'The threshold amount',
    `cod_fee_below` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'COD fee if sales < benchmark',
    `cod_fee_above` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'COD fee if sales >= benchmark',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_country_zone` (`country_id`, `shipping_zone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example: Malaysia West
-- INSERT INTO `cod_charges` (`country_id`, `shipping_zone`, `benchmark_amount`, `cod_fee_below`, `cod_fee_above`) VALUES (1, '1', 100.00, 10.00, 8.00);
-- Example: Malaysia East
-- INSERT INTO `cod_charges` (`country_id`, `shipping_zone`, `benchmark_amount`, `cod_fee_below`, `cod_fee_above`) VALUES (1, '2', 100.00, 12.00, 10.00);
