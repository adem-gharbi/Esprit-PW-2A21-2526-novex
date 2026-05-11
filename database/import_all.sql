-- Optional helper script.
-- Run from this directory with a MySQL client that supports SOURCE commands.
-- Check database names in each module config before using this file.

SOURCE users.sql;
SOURCE discount_game.sql;
SOURCE discount_game_migration.sql;
SOURCE discount_game_patch_game_description.sql;
SOURCE discount_game_patch_game_reviews.sql;
SOURCE excursions.sql;
