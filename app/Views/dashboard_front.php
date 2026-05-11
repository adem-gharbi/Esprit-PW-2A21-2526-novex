<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> | Dashboard</title>
    <link rel="stylesheet" href="public/assets/css/front-dashboard.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="dashboard.php">
            <span class="brand-mark">V</span>
            <span>Voyagio</span>
        </a>

        <nav class="main-nav" aria-label="Main navigation">
            <?php foreach ($sections as $section): ?>
                <a href="<?= htmlspecialchars($section['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <a class="logout" href="app/Modules/Users/Controller/UserController.php?action=logoutClient">Logout</a>
    </header>

    <section class="hero">
        <div>
            <p class="eyebrow">Welcome <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
            <h1>Nos Destinations</h1>
        </div>
    </section>

    <main class="dashboard-wrap">
        <section class="intro">
            <h2>Choose your travel service</h2>
            <p>Use the header or the cards below to move through the five integrated parts of the site.</p>
        </section>

        <section class="feature-grid" aria-label="Voyagio services">
            <?php foreach ($sections as $section): ?>
                <article class="feature-card">
                    <img src="<?= htmlspecialchars($section['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                    <div class="feature-content">
                        <span class="badge"><?= htmlspecialchars($section['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <h3><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($section['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <a class="btn" href="<?= htmlspecialchars($section['url'], ENT_QUOTES, 'UTF-8') ?>">Open page</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
