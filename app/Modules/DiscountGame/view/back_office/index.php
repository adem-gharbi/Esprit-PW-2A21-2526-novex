<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/GameReviewController.php';

$reviewController = new GameReviewController();
$latestReviews = $reviewController->latest(8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Admin Back Office</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>

    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">VO</div>
                <div>
                    <div class="brand-title">Voyagio</div>
                    <div class="brand-subtitle">Back Office</div>
                </div>
            </div>
            <nav class="menu">
                <a href="index.php" class="menu-item active">Dashboard</a>
                <a href="games.php" class="menu-item">Games</a>
                <a href="discounts.php" class="menu-item">Discounts</a>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <div class="page-title">Admin Dashboard</div>
                    <div class="page-subtitle">Manage Voyagio back-office sections for travel games and discount campaigns.</div>
                </div>
                <div class="user-card">
                    <span>Admin</span>
                    <div class="avatar">AD</div>
                </div>
            </header>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h2>Back Office Overview</h2>
                        <p>Choose one section to add, review, and manage your Voyagio records.</p>
                    </div>
                    <a href="games.php" class="btn btn-primary">Open Games</a>
                </div>

                <div class="overview-grid">
                    <article class="info-card">
                        <h3>Manage Games</h3>
                        <p>Add the game id, name, release date, expiration date, type, and status for each Voyagio game published by the admin team.</p>
                        <a href="games.php" class="btn btn-secondary">Games Manager</a>
                    </article>

                    <article class="info-card">
                        <h3>Manage Discounts</h3>
                        <p>Add discount id, type, release date, and expiration date for each discount campaign available in the back office.</p>
                        <a href="discounts.php" class="btn btn-secondary">Discounts Manager</a>
                    </article>
                </div>

                <section class="reviews-panel" aria-labelledby="reviews-title">
                    <div class="reviews-panel__header">
                        <div>
                            <h3 id="reviews-title">Game Reviews</h3>
                            <p>Latest player feedback submitted after playing a Voyagio game.</p>
                        </div>
                    </div>

                    <?php if (!$latestReviews): ?>
                        <div class="empty-state">No game reviews submitted yet.</div>
                    <?php else: ?>
                        <div class="reviews-list">
                            <?php foreach ($latestReviews as $review): ?>
                                <article class="review-card">
                                    <div class="review-card__top">
                                        <div>
                                            <strong><?= htmlspecialchars($review['game_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span><?= htmlspecialchars($review['game_identifier'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="review-stars" aria-label="<?= (int) $review['rating'] ?> out of 5 stars">
                                            <?= str_repeat('★', (int) $review['rating']) ?><?= str_repeat('☆', 5 - (int) $review['rating']) ?>
                                        </div>
                                    </div>

                                    <p><?= htmlspecialchars($review['description'], ENT_QUOTES, 'UTF-8') ?></p>

                                    <div class="review-card__meta">
                                        <span><?= htmlspecialchars($review['full_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span><?= htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </section>
        </main>
    </div>
</body>
</html>
