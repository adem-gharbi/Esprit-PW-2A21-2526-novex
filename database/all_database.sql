-- Voyagio full project database script
-- Import this file in phpMyAdmin or run it with MySQL.
-- It creates the databases used by the current module configs:
--   projet_ecologique  -> Users module
--   voyagio_game       -> Discount game module
--   travel_db          -> Excursions module
--   voyagio            -> Hotels, Destinations, Forum modules

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =========================================================
-- Users module database: projet_ecologique
-- =========================================================
CREATE DATABASE IF NOT EXISTS `projet_ecologique`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `projet_ecologique`;

CREATE TABLE IF NOT EXISTS `client` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `birthdate` DATE NOT NULL,
  `tel` VARCHAR(20) NOT NULL,
  `sexe` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `recovery_key` VARCHAR(20) DEFAULT NULL,
  `profile_photo` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `client`
  ADD COLUMN IF NOT EXISTS `recovery_key` VARCHAR(20) DEFAULT NULL AFTER `password`,
  ADD COLUMN IF NOT EXISTS `profile_photo` VARCHAR(255) DEFAULT NULL AFTER `recovery_key`,
  ADD COLUMN IF NOT EXISTS `status` VARCHAR(30) NOT NULL DEFAULT 'active' AFTER `profile_photo`;

CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin',
  `profile_photo` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `admin`
  ADD COLUMN IF NOT EXISTS `profile_photo` VARCHAR(255) DEFAULT NULL AFTER `role`;

INSERT IGNORE INTO `admin` (`fullname`, `email`, `password`, `role`) VALUES
('Super Admin', 'admin@projet-ecologique.com', 'admin123', 'admin');

-- =========================================================
-- Discount game module database: voyagio_game
-- =========================================================
CREATE DATABASE IF NOT EXISTS `voyagio_game`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `voyagio_game`;

CREATE TABLE IF NOT EXISTS `players` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `destination` VARCHAR(120) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_discounts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `discount_identifier` VARCHAR(60) NOT NULL,
  `discount_type` VARCHAR(100) NOT NULL,
  `discount_percentage` INT NOT NULL,
  `release_date` DATE NOT NULL,
  `expiration_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discount_identifier` (`discount_identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_games` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `game_identifier` VARCHAR(60) NOT NULL,
  `game_name` VARCHAR(150) NOT NULL,
  `release_date` DATE NOT NULL,
  `expiration_date` DATE NOT NULL,
  `game_type` VARCHAR(100) NOT NULL,
  `game_description` TEXT NOT NULL,
  `prompt_text` TEXT NOT NULL,
  `clue_text` TEXT NOT NULL,
  `correct_answer` VARCHAR(150) NOT NULL,
  `admin_discount_id` INT NOT NULL,
  `game_status` VARCHAR(40) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `game_identifier` (`game_identifier`),
  KEY `idx_admin_games_discount` (`admin_discount_id`),
  CONSTRAINT `fk_admin_games_admin_discount`
    FOREIGN KEY (`admin_discount_id`) REFERENCES `admin_discounts` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `game_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NOT NULL,
  `admin_game_id` INT NOT NULL,
  `submitted_answer` VARCHAR(255) NOT NULL,
  `chosen_box` TINYINT NULL,
  `is_winner` TINYINT(1) NOT NULL DEFAULT 0,
  `played_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_game_sessions_player` (`player_id`),
  KEY `idx_game_sessions_game` (`admin_game_id`),
  CONSTRAINT `fk_game_sessions_player`
    FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_game_sessions_admin_game`
    FOREIGN KEY (`admin_game_id`) REFERENCES `admin_games` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NOT NULL,
  `admin_discount_id` INT NOT NULL,
  `code` VARCHAR(40) NOT NULL,
  `discount_type` VARCHAR(100) NOT NULL,
  `discount_percentage` INT NOT NULL,
  `expires_at` DATE NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_coupons_player` (`player_id`),
  KEY `idx_coupons_discount` (`admin_discount_id`),
  CONSTRAINT `fk_coupons_player`
    FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_coupons_admin_discount`
    FOREIGN KEY (`admin_discount_id`) REFERENCES `admin_discounts` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `game_reviews` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `player_id` INT NOT NULL,
  `admin_game_id` INT NOT NULL,
  `game_session_id` INT NOT NULL,
  `rating` TINYINT NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `game_session_id` (`game_session_id`),
  KEY `idx_game_reviews_player` (`player_id`),
  KEY `idx_game_reviews_game` (`admin_game_id`),
  CONSTRAINT `fk_game_reviews_player`
    FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_game_reviews_admin_game`
    FOREIGN KEY (`admin_game_id`) REFERENCES `admin_games` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_game_reviews_game_session`
    FOREIGN KEY (`game_session_id`) REFERENCES `game_sessions` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `admin_discounts`
(`discount_identifier`, `discount_type`, `discount_percentage`, `release_date`, `expiration_date`, `created_at`) VALUES
('DEFAULT-DISCOUNT', 'travel_discount', 10, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), NOW());

INSERT IGNORE INTO `admin_games`
(`game_identifier`, `game_name`, `release_date`, `expiration_date`, `game_type`, `game_description`, `prompt_text`, `clue_text`, `correct_answer`, `admin_discount_id`, `game_status`, `created_at`)
SELECT
  'DEFAULT-GAME',
  'Default Destination Guessing',
  CURDATE(),
  DATE_ADD(CURDATE(), INTERVAL 30 DAY),
  'destination_guessing',
  'Guess the hidden destination from the short travel description and clue.',
  'Guess the destination',
  'A safe placeholder game added during setup.',
  'paris',
  `id`,
  'inactive',
  NOW()
FROM `admin_discounts`
WHERE `discount_identifier` = 'DEFAULT-DISCOUNT'
LIMIT 1;

-- =========================================================
-- Excursions module database: travel_db
-- =========================================================
CREATE DATABASE IF NOT EXISTS `travel_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `travel_db`;

CREATE TABLE IF NOT EXISTS `guide` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `tel` VARCHAR(20) NOT NULL DEFAULT '',
  `specialite` VARCHAR(150) NOT NULL,
  `langue` VARCHAR(150) NOT NULL,
  `photo` VARCHAR(255) DEFAULT 'user.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `guide`
  ADD COLUMN IF NOT EXISTS `tel` VARCHAR(20) NOT NULL DEFAULT '' AFTER `prenom`,
  ADD COLUMN IF NOT EXISTS `specialite` VARCHAR(150) NOT NULL DEFAULT 'General' AFTER `prenom`,
  ADD COLUMN IF NOT EXISTS `langue` VARCHAR(150) NOT NULL DEFAULT 'English' AFTER `specialite`,
  ADD COLUMN IF NOT EXISTS `photo` VARCHAR(255) DEFAULT 'user.jpg' AFTER `langue`,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `photo`;

CREATE TABLE IF NOT EXISTS `excursion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `duree` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `prix` DECIMAL(10,2) NOT NULL,
  `circuit_id` INT NOT NULL DEFAULT 0,
  `guide_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_excursion_guide` (`guide_id`),
  CONSTRAINT `fk_excursion_guide`
    FOREIGN KEY (`guide_id`) REFERENCES `guide` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `guide` (`id`, `nom`, `prenom`, `tel`, `specialite`, `langue`, `photo`) VALUES
(1, 'Ben Ali', 'Sara', '21697768214', 'Culture', 'Arabic, French, English', 'person_1.jpg'),
(2, 'Mansour', 'Omar', '21697768215', 'Adventure', 'Arabic, English', 'person_2.jpg');

INSERT IGNORE INTO `excursion` (`id`, `titre`, `duree`, `description`, `prix`, `circuit_id`, `guide_id`) VALUES
(1, 'Cultural City Walk', '4 hours', 'A guided cultural walk through the most important local landmarks.', 80.00, 0, 1),
(2, 'Outdoor Adventure Day', '1 day', 'A full-day outdoor excursion for travelers who enjoy active experiences.', 160.00, 0, 2);

-- =========================================================
-- Shared travel database: voyagio
-- Hotels, Destinations, and Forum modules
-- =========================================================
CREATE DATABASE IF NOT EXISTS `voyagio`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `voyagio`;

-- -------------------------
-- Hotels module
-- -------------------------
CREATE TABLE IF NOT EXISTS `hotel` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Nom` VARCHAR(150) NOT NULL,
  `Ville` VARCHAR(120) NOT NULL,
  `Etoiles` INT NOT NULL,
  `Prix` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reservation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hotel_id` INT NOT NULL,
  `nom_client` VARCHAR(150) NOT NULL,
  `date_arrivee` DATE NOT NULL,
  `date_depart` DATE NOT NULL,
  `nb_personnes` INT NOT NULL,
  `discount` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reservation_hotel` (`hotel_id`),
  CONSTRAINT `fk_reservation_hotel`
    FOREIGN KEY (`hotel_id`) REFERENCES `hotel` (`Id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `hotel` (`Id`, `Nom`, `Ville`, `Etoiles`, `Prix`) VALUES
(1, 'Voyagio Palace', 'Tunis', 5, 240.00),
(2, 'Medina Stay', 'Marrakech', 4, 150.00),
(3, 'Blue Coast Hotel', 'Barcelona', 4, 180.00);

-- -------------------------
-- Destinations module
-- -------------------------
CREATE TABLE IF NOT EXISTS `destination` (
  `id_destination` INT NOT NULL AUTO_INCREMENT,
  `ville` VARCHAR(120) NOT NULL,
  `pays` VARCHAR(120) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `categorie` VARCHAR(120) DEFAULT NULL,
  `latitude` DECIMAL(10,7) DEFAULT NULL,
  `longitude` DECIMAL(10,7) DEFAULT NULL,
  PRIMARY KEY (`id_destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `circuit` (
  `id_circuit` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(180) NOT NULL,
  `duree` INT NOT NULL,
  `prix` DECIMAL(10,2) NOT NULL,
  `nb_places` INT NOT NULL,
  `date_depart` DATE NOT NULL,
  `id_destination` INT NOT NULL,
  `id_hotel` INT DEFAULT NULL,
  PRIMARY KEY (`id_circuit`),
  KEY `idx_circuit_destination` (`id_destination`),
  KEY `idx_circuit_hotel` (`id_hotel`),
  CONSTRAINT `fk_circuit_destination`
    FOREIGN KEY (`id_destination`) REFERENCES `destination` (`id_destination`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_circuit_hotel`
    FOREIGN KEY (`id_hotel`) REFERENCES `hotel` (`Id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `destination`
(`id_destination`, `ville`, `pays`, `description`, `image`, `categorie`, `latitude`, `longitude`) VALUES
(1, 'Paris', 'France', 'Classic culture, food, and architecture.', 'paris.jpg', 'Culture', 48.8566000, 2.3522000),
(2, 'Tunis', 'Tunisia', 'Mediterranean coast, history, and old city charm.', 'tunis.JPG', 'Culture', 36.8065000, 10.1815000),
(3, 'Dubai', 'United Arab Emirates', 'Modern skyline, shopping, and desert experiences.', 'dubai.jpg', 'Luxury', 25.2048000, 55.2708000);

INSERT IGNORE INTO `circuit`
(`id_circuit`, `titre`, `duree`, `prix`, `nb_places`, `date_depart`, `id_destination`, `id_hotel`) VALUES
(1, 'Paris Discovery', 5, 1200.00, 12, DATE_ADD(CURDATE(), INTERVAL 20 DAY), 1, 1),
(2, 'Tunis Heritage Tour', 3, 650.00, 10, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 2, 1),
(3, 'Dubai Luxury Escape', 4, 1800.00, 8, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 3, 3);

-- -------------------------
-- Forum module
-- -------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `post` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `contenu` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `scheduled_at` DATETIME DEFAULT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_user` (`user_id`),
  CONSTRAINT `fk_post_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `titre` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `id`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'titre'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `contenu` TEXT NOT NULL AFTER `titre`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'contenu'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL AFTER `contenu`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'image'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `user_id` INT DEFAULT NULL AFTER `image`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'user_id'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `scheduled_at` DATETIME DEFAULT NULL AFTER `user_id`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'scheduled_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `scheduled_at`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'is_pinned'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `post` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `is_pinned`',
    'SELECT 1'
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @db_name
    AND TABLE_NAME = 'post'
    AND COLUMN_NAME = 'created_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `post_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `contenu` TEXT NOT NULL,
  `edited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_history_post` (`post_id`),
  CONSTRAINT `fk_post_history_post`
    FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `post_tags` (
  `post_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  CONSTRAINT `fk_post_tags_post`
    FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_post_tags_tag`
    FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_ip` VARCHAR(45) NOT NULL,
  `type` ENUM('like','love','haha','wow','sad') NOT NULL DEFAULT 'like',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_reaction_ip` (`post_id`, `user_ip`),
  CONSTRAINT `fk_reactions_post`
    FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `commentaire` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `contenu` TEXT NOT NULL,
  `parent_id` INT DEFAULT NULL,
  `username` VARCHAR(100) NOT NULL DEFAULT 'User',
  `date_comment` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comment_post` (`post_id`),
  KEY `idx_comment_parent` (`parent_id`),
  CONSTRAINT `fk_comment_post`
    FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_comment_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `commentaire` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comment_likes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comment_id` INT NOT NULL,
  `user_ip` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_comment_like_ip` (`comment_id`, `user_ip`),
  CONSTRAINT `fk_comment_likes_comment`
    FOREIGN KEY (`comment_id`) REFERENCES `commentaire` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(30) NOT NULL,
  `target_id` INT NOT NULL,
  `motif` VARCHAR(120) NOT NULL,
  `user_ip` VARCHAR(45) NOT NULL,
  `is_reviewed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `post_id` INT DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_post` (`post_id`),
  CONSTRAINT `fk_notifications_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_notifications_post`
    FOREIGN KEY (`post_id`) REFERENCES `post` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `followers` (
  `follower_id` INT NOT NULL,
  `followed_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`follower_id`, `followed_id`),
  CONSTRAINT `fk_followers_follower`
    FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_followers_followed`
    FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'Voyagio User', 'user@voyagio.local', NULL);

INSERT IGNORE INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@voyagio.local', 'admin123');

INSERT IGNORE INTO `tags` (`id`, `nom`) VALUES
(1, 'travel'),
(2, 'hotel'),
(3, 'destination'),
(4, 'tips');

INSERT IGNORE INTO `post` (`id`, `titre`, `contenu`, `image`, `user_id`, `is_pinned`) VALUES
(1, 'Welcome to Voyagio Forum', 'Share your best travel ideas, questions, and destination tips here.', NULL, 1, 1);

INSERT IGNORE INTO `post_tags` (`post_id`, `tag_id`) VALUES
(1, 1),
(1, 4);
