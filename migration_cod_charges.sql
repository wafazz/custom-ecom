-- COD Charges (benchmark-based, one row per country)
-- Example: benchmark = RM100, below_fee = RM10, above_fee = RM8
-- Sales < RM100 → charge RM10, Sales >= RM100 → charge RM8
CREATE TABLE IF NOT EXISTS `cod_charges` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `country_id` INT(11) NOT NULL,
    `benchmark_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'The threshold amount',
    `cod_fee_below` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'COD fee if sales < benchmark',
    `cod_fee_above` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'COD fee if sales >= benchmark',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Example: Malaysia
-- INSERT INTO `cod_charges` (`country_id`, `benchmark_amount`, `cod_fee_below`, `cod_fee_above`) VALUES (1, 100.00, 10.00, 8.00);
