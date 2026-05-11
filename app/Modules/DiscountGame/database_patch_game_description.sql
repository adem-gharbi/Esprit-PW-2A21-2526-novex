USE voyagio_game;

ALTER TABLE admin_games
    ADD COLUMN game_description TEXT NOT NULL AFTER game_type;

UPDATE admin_games
SET game_description = CASE
    WHEN game_type = 'crossword' THEN 'Solve the word puzzle using the clue provided by Voyagio.'
    ELSE 'Guess the destination from the travel clue provided by Voyagio.'
END
WHERE game_description IS NULL OR game_description = '';
