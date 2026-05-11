<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> | Full Project</title>
    <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <div class="mark">V</div>
            <div><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> MVC Project</div>
        </div>
        <nav class="nav">
            <a href="admin.php">Admin</a>
            <a href="README.md">Setup</a>
        </nav>
    </header>

    <main class="wrap">
        <section class="intro">
            <h1>One integrated travel website.</h1>
            <p>
                The project is now organized as MVC: shared controllers and views live in `app`,
                public entry files live in `public`, and each feature is isolated as a module.
            </p>
        </section>

        <section class="grid" aria-label="Project modules">
            <?php foreach ($modules as $module): ?>
                <article class="card">
                    <a href="<?= htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="tag"><?= htmlspecialchars($module['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                        <h2><?= htmlspecialchars($module['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($module['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="open">Open module</span>
                    </a>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
