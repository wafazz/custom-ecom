-- NinjaVan OAuth token cache table
-- Run on remote DB server (178.128.20.226)

CREATE TABLE IF NOT EXISTS `ninjavan_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` varchar(20) NOT NULL COMMENT 'production or sandbox',
  `access_token` text NOT NULL,
  `token_type` varchar(50) NOT NULL DEFAULT 'Bearer',
  `expires_in` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mode` (`mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
