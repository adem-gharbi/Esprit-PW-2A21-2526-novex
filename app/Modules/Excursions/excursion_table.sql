-- Excursion Table Creation Script
-- Run this in your MySQL database "voyage"

CREATE TABLE IF NOT EXISTS `excursion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `duree` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10, 2) NOT NULL,
  `circuit_id` int(11) NOT NULL,
  `guide_id` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`guide_id`) REFERENCES `guide`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

