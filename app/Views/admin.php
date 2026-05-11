<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> | Admin Gateway</title>
    <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body>
    <header class="topbar">
        <strong><?= htmlspecialchars($appName, ENT_QUOTES, 'UTF-8') ?> Admin Gateway</strong>
        <nav class="nav">
            <a href="index.php">Home</a>
        </nav>
    </header>

    <main class="wrap page">
        <h1>Back-office modules</h1>
        <section class="list">
            <?php foreach ($adminLinks as $link): ?>
                <a class="item" href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>">
                    <h2><?= htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p><?= htmlspecialchars($link['description'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
