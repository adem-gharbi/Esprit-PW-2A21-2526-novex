USE voyagio_game;

ALTER TABLE admin_discounts
    ADD COLUMN discount_percentage INT NOT NULL DEFAULT 10 AFTER discount_type;

INSERT INTO admin_discounts (discount_identifier, discount_type, discount_percentage, release_date, expiration_date, created_at)
SELECT 'DEFAULT-DISCOUNT', 'travel_discount', 10, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM admin_discounts
);

ALTER TABLE admin_games
    ADD COLUMN game_description TEXT NOT NULL AFTER game_type,
    ADD COLUMN prompt_text TEXT NOT NULL AFTER game_description,
    ADD COLUMN clue_text TEXT NOT NULL AFTER prompt_text,
    ADD COLUMN correct_answer VARCHAR(150) NOT NULL AFTER clue_text,
    ADD COLUMN admin_discount_id INT NOT NULL DEFAULT 1 AFTER correct_answer;

UPDATE admin_games
SET admin_discount_id = (
    SELECT id
    FROM admin_discounts
    ORDER BY id ASC
    LIMIT 1
)
WHERE admin_discount_id = 1 OR admin_discount_id IS NULL;

INSERT INTO admin_games (
    game_identifier,
    game_name,
    release_date,
    expiration_date,
    game_type,
    game_description,
    prompt_text,
    clue_text,
    correct_answer,
    admin_discount_id,
    game_status,
    created_at
)
SELECT
    'DEFAULT-GAME',
    'Default Destination Guessing',
    CURDATE(),
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    'destination_guessing',
    'Guess the hidden destination from the short travel description and clue.',
    'Guess the destination',
    'A safe placeholder game added during migration.',
    'paris',
    (SELECT id FROM admin_discounts ORDER BY id ASC LIMIT 1),
    'inactive',
    NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM admin_games
);

ALTER TABLE game_sessions
    ADD COLUMN admin_game_id INT NOT NULL DEFAULT 1 AFTER player_id,
    ADD COLUMN submitted_answer VARCHAR(255) NOT NULL DEFAULT '' AFTER admin_game_id,
    MODIFY COLUMN chosen_box TINYINT NULL;

ALTER TABLE coupons
    ADD COLUMN admin_discount_id INT NOT NULL DEFAULT 1 AFTER player_id,
    ADD COLUMN discount_type VARCHAR(100) NOT NULL DEFAULT 'travel_discount' AFTER code;

UPDATE game_sessions
SET admin_game_id = (
    SELECT id
    FROM admin_games
    ORDER BY id ASC
    LIMIT 1
)
WHERE admin_game_id = 1 OR admin_game_id IS NULL;

UPDATE coupons
SET admin_discount_id = (
    SELECT id
    FROM admin_discounts
    ORDER BY id ASC
    LIMIT 1
)
WHERE admin_discount_id = 1 OR admin_discount_id IS NULL;

ALTER TABLE admin_games
    ADD CONSTRAINT fk_admin_games_admin_discount
    FOREIGN KEY (admin_discount_id) REFERENCES admin_discounts(id)
    ON DELETE CASCADE;

ALTER TABLE game_sessions
    ADD CONSTRAINT fk_game_sessions_admin_game
    FOREIGN KEY (admin_game_id) REFERENCES admin_games(id)
    ON DELETE CASCADE;

ALTER TABLE coupons
    ADD CONSTRAINT fk_coupons_admin_discount
    FOREIGN KEY (admin_discount_id) REFERENCES admin_discounts(id)
    ON DELETE CASCADE;
