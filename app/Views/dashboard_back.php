<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> | Back Office</title>
    <link rel="stylesheet" href="public/assets/css/back-dashboard.css">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">VO</div>
                <div>
                    <div class="brand-title">Voyagio</div>
                    <div class="brand-subtitle">Back Office</div>
                </div>
            </div>

            <nav class="menu" aria-label="Back office navigation">
                <a href="back.php" class="menu-item active">Dashboard</a>
                <?php foreach ($sections as $section): ?>
                    <a href="<?= htmlspecialchars($section['url'], ENT_QUOTES, 'UTF-8') ?>" class="menu-item">
                        <?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <a href="app/Modules/Users/view/admin/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <div class="page-title">Admin Dashboard</div>
                    <div class="page-subtitle">Manage all Voyagio modules from one left sidebar.</div>
                </div>
                <div class="user-card">
                    <span><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
                    <div class="avatar">AD</div>
                </div>
            </header>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h2>Back Office Overview</h2>
                        <p>Choose a section to add, review, and manage project records.</p>
                    </div>
                </div>

                <div class="overview-grid">
                    <?php foreach ($sections as $section): ?>
                        <article class="info-card">
                            <h3><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($section['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <a href="<?= htmlspecialchars($section['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary">Open Manager</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
