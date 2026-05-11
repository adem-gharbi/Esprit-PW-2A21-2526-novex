# Database Files

Use `all_database.sql` if you want one import file for the whole project.

It creates the databases used by the current module configs:

- `projet_ecologique` for users
- `voyagio_game` for the discount game
- `travel_db` for excursions
- `voyagio` for hotels, destinations, and forum

The older split files were copied from the original modules:

- `users.sql`
- `discount_game.sql`
- `discount_game_migration.sql`
- `discount_game_patch_game_description.sql`
- `discount_game_patch_game_reviews.sql`
- `excursions.sql`

`all_database.sql` also includes inferred tables for modules that did not include SQL dumps.
