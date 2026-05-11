CREATE DATABASE IF NOT EXISTS voyagio_game CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voyagio_game;

CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    destination VARCHAR(120) NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS admin_discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    discount_identifier VARCHAR(60) NOT NULL UNIQUE,
    discount_type VARCHAR(100) NOT NULL,
    discount_percentage INT NOT NULL,
    release_date DATE NOT NULL,
    expiration_date DATE NOT NULL,
    created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS admin_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_identifier VARCHAR(60) NOT NULL UNIQUE,
    game_name VARCHAR(150) NOT NULL,
    release_date DATE NOT NULL,
    expiration_date DATE NOT NULL,
    game_type VARCHAR(100) NOT NULL,
    game_description TEXT NOT NULL,
    prompt_text TEXT NOT NULL,
    clue_text TEXT NOT NULL,
    correct_answer VARCHAR(150) NOT NULL,
    admin_discount_id INT NOT NULL,
    game_status VARCHAR(40) NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_admin_games_admin_discount
        FOREIGN KEY (admin_discount_id) REFERENCES admin_discounts(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    admin_game_id INT NOT NULL,
    submitted_answer VARCHAR(255) NOT NULL,
    chosen_box TINYINT NULL,
    is_winner TINYINT(1) NOT NULL DEFAULT 0,
    played_at DATETIME NOT NULL,
    CONSTRAINT fk_game_sessions_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_game_sessions_admin_game
        FOREIGN KEY (admin_game_id) REFERENCES admin_games(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    admin_discount_id INT NOT NULL,
    code VARCHAR(40) NOT NULL UNIQUE,
    discount_type VARCHAR(100) NOT NULL,
    discount_percentage INT NOT NULL,
    expires_at DATE NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_coupons_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_coupons_admin_discount
        FOREIGN KEY (admin_discount_id) REFERENCES admin_discounts(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS game_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    admin_game_id INT NOT NULL,
    game_session_id INT NOT NULL UNIQUE,
    rating TINYINT NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_game_reviews_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_game_reviews_admin_game
        FOREIGN KEY (admin_game_id) REFERENCES admin_games(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_game_reviews_game_session
        FOREIGN KEY (game_session_id) REFERENCES game_sessions(id)
        ON DELETE CASCADE
);
