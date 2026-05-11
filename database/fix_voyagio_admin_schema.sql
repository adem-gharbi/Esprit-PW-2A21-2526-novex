-- Run this if the forum admin insert shows:
-- Unknown column 'email' in 'field list'

USE `voyagio`;

SET @db_name = DATABASE();

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `admin` ADD COLUMN `username` VARCHAR(100) NOT NULL DEFAULT ''admin'' AFTER `id`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'admin'
    AND COLUMN_NAME = 'username'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `admin` ADD COLUMN `email` VARCHAR(150) NOT NULL DEFAULT ''admin@voyagio.local'' AFTER `username`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'admin'
    AND COLUMN_NAME = 'email'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `admin` ADD COLUMN `password` VARCHAR(255) NOT NULL DEFAULT ''admin123'' AFTER `email`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'admin'
    AND COLUMN_NAME = 'password'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT IGNORE INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@voyagio.local', 'admin123');
