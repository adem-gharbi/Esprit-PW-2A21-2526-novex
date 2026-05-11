USE voyagio_game;

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
