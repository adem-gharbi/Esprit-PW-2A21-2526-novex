# Voyagio MVC Project

This project merges the six original website parts into one modular MVC structure.

## Structure

```text
voyagio_full_project/
  app/
    Controllers/      Shared gateway controllers
    Core/             Small base MVC helpers
    Modules/          Feature modules
      Users/
      Forum/
      DiscountGame/
      Hotels/
      Destinations/
      Excursions/
    Views/            Shared gateway views
  config/             Shared app config
  database/           SQL files copied from the original projects
  public/             Public entry files and shared assets
  index.php           Compatibility entry, loads public/index.php
  admin.php           Compatibility entry, loads public/admin.php
```

## Feature Modules

Each original folder is now a module:

- `adem` -> `app/Modules/Users`
- `adem_bou` -> `app/Modules/Forum`
- `game_disount` -> `app/Modules/DiscountGame`
- `nourhene` -> `app/Modules/Hotels`
- `raef` -> `app/Modules/Destinations`
- `voyage` -> `app/Modules/Excursions`

The modules keep their internal controllers, models, views, configs, and assets. This is intentional because the original parts use different database classes and many relative paths.

## Entry Points

- Main website entry: `index.php`, redirects to the Users login module
- Client dashboard after login: `dashboard.php`
- Admin gateway: `admin.php`, redirects to `back.php`
- Back-office dashboard after admin login: `back.php`
- MVC public home: `public/index.php`
- MVC public admin: `public/admin.php`

Module pages are linked from the gateways.

## Integrated Flow

Front office:

1. The user starts in `app/Modules/Users/view/login.html`.
2. The user can create an account from the same Users module.
3. A successful client login redirects to `dashboard.php`.
4. The dashboard header exposes the five integrated feature areas:
   - Hotel and Reservation
   - Guide and Excursion
   - Circuit and Destination
   - Game and Discount
   - Forums

Back office:

1. Admin login starts in `app/Modules/Users/view/login_admin.html`.
2. A successful admin login redirects to `back.php`.
3. The left sidebar links to the five back-office module areas.

The front dashboard uses a style inspired by the Destinations module, and the back dashboard uses the Discount Game back-office style.

## Database

SQL files found in the original parts were copied into `database/`.

Some modules expect different database names:

- Users module: `projet_ecologique`
- Excursions module: `travel_db`
- Hotels, destinations, and forum modules: `voyagio`
- Discount game module: check `app/Modules/DiscountGame/config.php`

Before running all features, import the needed SQL files and update module configs if your local database names differ.

## Next Refactor Step

For an even stricter MVC application, the next step is to normalize every module to the same naming convention:

- `Controllers/`
- `Models/`
- `Views/front`
- `Views/back`
- `Config/`
- `public/assets`

That deeper step requires editing many internal `require`, `include`, `href`, and `src` paths inside each module.
