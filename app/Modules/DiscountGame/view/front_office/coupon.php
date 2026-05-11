<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['player'], $_SESSION['last_game'])) {
    header('Location: index.php');
    exit;
}

$player = $_SESSION['player'];
$lastGame = $_SESSION['last_game'];
$coupon = $lastGame['coupon'] ?? null;
$reviewMessage = $_SESSION['review_message'] ?? null;
$reviewGame = $_SESSION['pending_review']['game'] ?? null;
$reviewSession = $_SESSION['pending_review']['session'] ?? null;

unset($_SESSION['review_message']);

if (!$coupon) {
    header('Location: play.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Your Discount Coupon</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --night: #1f1b4d;
            --sun: #ffca5b;
            --sea: #41b6a6;
            --paper: #fffdf7;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(31, 27, 77, 0.96), rgba(65, 182, 166, 0.88)),
                #1f1b4d;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: white;
        }

        .ticket {
            width: min(760px, 100%);
            background: var(--paper);
            color: var(--night);
            border-radius: 30px;
            padding: 36px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.22);
            position: relative;
            overflow: hidden;
        }

        .ticket::before,
        .ticket::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 202, 91, 0.25);
        }

        .ticket::before {
            top: -60px;
            right: -40px;
        }

        .ticket::after {
            bottom: -70px;
            left: -50px;
        }

        h1 {
            margin-top: 0;
            font-size: clamp(2rem, 6vw, 3.6rem);
            line-height: 1;
        }

        .pill {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(65, 182, 166, 0.15);
            color: var(--sea);
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .coupon {
            margin: 26px 0;
            padding: 24px;
            border: 2px dashed rgba(31, 27, 77, 0.2);
            border-radius: 22px;
            background: white;
        }

        .code {
            font-size: clamp(1.6rem, 5vw, 2.6rem);
            font-weight: 900;
            letter-spacing: 0.08em;
            margin: 10px 0;
        }

        .actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        a {
            text-decoration: none;
            padding: 14px 18px;
            border-radius: 14px;
            font-weight: 800;
        }

        .primary {
            background: linear-gradient(135deg, var(--sun), #ffe09a);
            color: #533600;
        }

        .secondary {
            background: rgba(31, 27, 77, 0.08);
            color: var(--night);
        }
    </style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>

    <section class="ticket">
        <span class="pill">Voyagio winner</span>
        <h1>Your trip just got more affordable.</h1>
        <p>
            Congratulations <?= htmlspecialchars($player['full_name'], ENT_QUOTES, 'UTF-8') ?>.
            You unlocked a travel coupon for your next Voyagio booking to
            <strong><?= htmlspecialchars($player['destination'], ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>

        <?php if ($reviewMessage): ?>
            <p><strong><?= htmlspecialchars($reviewMessage, ENT_QUOTES, 'UTF-8') ?></strong></p>
        <?php endif; ?>

        <div class="coupon">
            <div><?= htmlspecialchars($coupon['discount_type'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="code"><?= (int) $coupon['discount_percentage'] ?>% OFF</div>
            <div><strong>Coupon code:</strong> <?= htmlspecialchars($coupon['code'], ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>Valid until:</strong> <?= htmlspecialchars($coupon['expires_at'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div class="actions">
            <a class="primary" href="play.php">Play again</a>
            <a class="secondary" href="index.php">Back to registration</a>
        </div>
    </section>
    <?php
        $reviewReturnTo = 'coupon.php';
        require __DIR__ . '/review_modal.php';

        $chatbotCoupon = $coupon;
        require __DIR__ . '/chatbot.php';
    ?>
</body>
</html>
