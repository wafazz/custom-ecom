CREATE TABLE IF NOT EXISTS `store_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `store_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'ROZZ BEAUTY LEGACY'),
('footer_address', 'A-G-30, SAVANNA LIFESTYLE RETAIL,\nJALAN SOUTHVILLE 2, SOUTHVILLE CITY,\n43800 DENGKIL, SELANGOR'),
('footer_phone', '603 8912 3807'),
('footer_email', 'wafazz.tech@gmail.com'),
('footer_copyright_text', 'Rozyana.com'),
('footer_copyright_url', 'https://rozyana.com')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
