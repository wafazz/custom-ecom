CREATE TABLE IF NOT EXISTS `store_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `store_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'ROZZ BEAUTY LEGACY'),
('footer_address', 'A-G-30, SAVANNA LIFESTYLE RETAIL,\nJALAN SOUTHVILLE 2, SOUTHVILLE CITY,\n43800 DENGKIL, SELANGOR'),
('footer_phone', '603 8912 3807'),
('footer_email', 'wafazz.tech@gmail.com'),
('footer_copyright_text', 'Rozyana.com'),
('footer_copyright_url', 'https://rozyana.com'),
('google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d778.8775116341542!2d101.76361164054465!3d2.905044997071393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdc9060cd47979%3A0x6c7c89f89dc02e7!2sROZEYANA%20KOSMETIK!5e0!3m2!1sen!2smy!4v1752277732026!5m2!1sen!2smy')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
