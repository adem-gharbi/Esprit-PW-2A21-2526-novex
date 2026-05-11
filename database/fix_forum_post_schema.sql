-- Run this if the forum post insert shows:
-- Unknown column 'user_id' in 'field list'

USE `voyagio`;

SET @db_name = DATABASE();

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `titre` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `id`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'titre'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `contenu` TEXT NOT NULL AFTER `titre`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'contenu'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL AFTER `contenu`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'image'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `user_id` INT DEFAULT NULL AFTER `image`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'user_id'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `scheduled_at` DATETIME DEFAULT NULL AFTER `user_id`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'scheduled_at'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `scheduled_at`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'is_pinned'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `is_pinned`',
    'SELECT 1')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'post' AND COLUMN_NAME = 'created_at'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'Voyagio User', 'user@voyagio.local', NULL);

INSERT IGNORE INTO `post` (`id`, `titre`, `contenu`, `image`, `user_id`, `is_pinned`) VALUES
(1, 'Welcome to Voyagio Forum', 'Share your best travel ideas, questions, and destination tips here.', NULL, 1, 1);
