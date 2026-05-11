<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/PlayerController.php';

$playerController = new PlayerController();
$errors = [];
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $playerController->register([
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'destination' => trim($_POST['destination'] ?? ''),
    ]);

    if ($result['success']) {
        $_SESSION['player'] = $result['player'];

        if (!headers_sent()) {
            header('Location: play.php');
            exit;
        }
    }

    $errors = $result['errors'] ?? [];
    $message = $result['is_existing'] ?? false
        ? 'You are already registered. Let us send you to the game.'
        : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Travel Game Registration</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --sand: #f6efe4;
            --navy: #10324a;
            --teal: #2e7c74;
            --gold: #e2a93b;
            --ink: #1f2933;
            --card: rgba(255, 255, 255, 0.88);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(226, 169, 59, 0.35), transparent 30%),
                linear-gradient(135deg, #fff9f1, #dff2f0 60%, #fdf3da);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .layout {
            width: min(1080px, 100%);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(16, 50, 74, 0.15);
            background: white;
        }

        .hero {
            padding: 56px;
            background:
                radial-gradient(circle at top right, rgba(226, 169, 59, 0.22), transparent 24%),
                radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.12), transparent 26%),
                linear-gradient(180deg, rgba(16, 50, 74, 0.97), rgba(46, 124, 116, 0.9));
            color: white;
        }

        .hero h1 {
            font-size: clamp(2.4rem, 6vw, 4.5rem);
            line-height: 0.95;
            margin: 0 0 18px;
        }

        .hero p {
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 460px;
        }

        .badge {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            margin-bottom: 22px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.78rem;
        }

        .panel {
            background: var(--sand);
            padding: 48px 34px;
        }

        form {
            display: grid;
            gap: 16px;
        }

        h2 {
            margin-top: 0;
            font-size: 2rem;
            color: var(--navy);
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            border: 1px solid rgba(16, 50, 74, 0.15);
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 1rem;
        }

        button {
            border: 0;
            border-radius: 16px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--gold), #f4cb70);
            color: var(--navy);
            font-weight: 800;
            cursor: pointer;
            font-size: 1rem;
        }

        .note, .errors {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .note {
            background: rgba(46, 124, 116, 0.12);
            color: var(--teal);
        }

        .errors {
            background: rgba(177, 53, 53, 0.1);
            color: #932d2d;
        }

        .mini {
            margin-top: 18px;
            color: #536471;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        @media (max-width: 860px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .hero, .panel {
                padding: 34px 24px;
            }
        }
    </style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>

    <main class="layout">
        <section class="hero">
            <span class="badge">Voyagio Travel Agency</span>
            <h1>Play. Win. Travel for less.</h1>
            <p>
                Register for the Voyagio destination game, test your luck, and unlock an instant travel coupon if you win.
                Your next getaway could start with one smart click.
            </p>
        </section>

        <section class="panel">
            <h2>Join the game</h2>

            <?php if ($message): ?>
                <div class="note"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div>
                    <label for="full_name">Full name</label>
                    <input id="full_name" name="full_name" type="text" placeholder="Sara Ben Ali" required>
                </div>

                <div>
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" placeholder="sara@example.com" required>
                </div>

                <div>
                    <label for="destination">Dream destination</label>
                    <input id="destination" name="destination" type="text" placeholder="Santorini" required>
                </div>

                <button type="submit">Register and start playing</button>
            </form>

            <p class="mini">
                After registration, the traveler enters the game area and sees the active games published by the admin team.
                A correct answer unlocks the discount campaign linked to that game.
            </p>
        </section>
    </main>
</body>
</html>
