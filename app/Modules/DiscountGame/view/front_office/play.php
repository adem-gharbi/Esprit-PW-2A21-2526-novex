<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/GameController.php';

if (!isset($_SESSION['player'])) {
    header('Location: index.php');
    exit;
}

$player = $_SESSION['player'];
$gameController = new GameController();
$games = $gameController->availableGames();
$selectedGame = null;
$selectedGameId = (int) ($_GET['game_id'] ?? $_POST['game_id'] ?? 0);
$result = null;
$error = null;
$reviewMessage = $_SESSION['review_message'] ?? null;
$reviewGame = null;
$reviewSession = null;

unset($_SESSION['review_message']);

function gameDescriptionText(array $game): string
{
    $description = trim((string) ($game['game_description'] ?? ''));

    if ($description !== '') {
        return $description;
    }

    if (($game['game_type'] ?? '') === 'crossword') {
        return 'Solve the word puzzle using the clue provided by Voyagio.';
    }

    return 'Guess the destination from the travel clue provided by Voyagio.';
}

if ($selectedGameId > 0) {
    $selectedGame = $gameController->findAvailableGame($selectedGameId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = trim($_POST['answer'] ?? '');
    $result = $gameController->play((int) $player['id'], $selectedGameId, $answer);

    if (!($result['success'] ?? false)) {
        $error = $result['message'] ?? 'Unable to process the game.';
    } else {
        $_SESSION['last_game'] = $result;
        $_SESSION['pending_review'] = [
            'game' => $result['game'],
            'session' => $result['session'],
        ];

        if (($result['is_winner'] ?? false) === true) {
            header('Location: coupon.php');
            exit;
        }

        $reviewGame = $result['game'];
        $reviewSession = $result['session'];
        unset($_SESSION['pending_review']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Play The Game</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --sky: #e6f4ff;
            --navy: #0f3552;
            --mint: #daf1e8;
            --gold: #ffbe55;
            --coral: #f36f5d;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at right top, rgba(243, 111, 93, 0.2), transparent 22%),
                linear-gradient(180deg, #fdfcff, #eaf7ff 50%, #eef8ef);
            color: var(--navy);
            padding: 24px;
        }

        .wrap {
            width: min(1100px, 100%);
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 24px;
        }

        .headline {
            background: white;
            border-radius: 26px;
            padding: 28px;
            box-shadow: 0 18px 50px rgba(15, 53, 82, 0.08);
        }

        h1 {
            margin: 0 0 10px;
            font-size: clamp(2.2rem, 6vw, 4rem);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-top: 28px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 16px 40px rgba(15, 53, 82, 0.08);
            text-align: center;
        }

        .box {
            font-size: 4rem;
            margin: 14px 0;
        }

        button {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 14px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold), #ffd98e);
            color: #4b2f04;
            cursor: pointer;
        }

        .flash {
            margin-top: 24px;
            border-radius: 18px;
            padding: 18px 20px;
            background: var(--mint);
        }

        .error {
            background: rgba(243, 111, 93, 0.18);
            color: #84281e;
        }

        a {
            color: var(--navy);
            text-decoration: none;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../dashboard.php">&larr; Dashboard</a>

    <div class="wrap">
        <div class="topbar">
            <div>
                <strong>Traveler:</strong> <?= htmlspecialchars($player['full_name'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Destination:</strong> <?= htmlspecialchars($player['destination'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <a href="index.php">Register another traveler</a>
        </div>

        <section class="headline">
            <h1>Choose and play an admin-created Voyagio game</h1>
            <p>Each active game shown below comes from the back office. If you answer correctly, you unlock the linked admin discount.</p>
        </section>

        <?php if ($error): ?>
            <div class="flash error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($reviewMessage): ?>
            <div class="flash"><?= htmlspecialchars($reviewMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($result && !($result['is_winner'] ?? false)): ?>
            <div class="flash">
                Your answer was not correct for <?= htmlspecialchars($result['game']['game_name'], ENT_QUOTES, 'UTF-8') ?>.
                Read the clue and try again to win the linked discount.
            </div>
        <?php endif; ?>

        <?php if (!$selectedGame): ?>
            <div class="grid">
                <?php if (!$games): ?>
                    <article class="card">
                        <div>No active admin game is available yet. Add one in the back office and assign a valid discount to publish it here.</div>
                    </article>
                <?php endif; ?>

                <?php foreach ($games as $game): ?>
                    <article class="card">
                        <div><?= htmlspecialchars($game['game_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="box"><?= htmlspecialchars($game['game_type'] === 'crossword' ? 'WORD' : 'TRIP', ENT_QUOTES, 'UTF-8') ?></div>
                        <p><strong>Type:</strong> <?= htmlspecialchars(str_replace('_', ' ', $game['game_type']), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><?= htmlspecialchars(gameDescriptionText($game), ENT_QUOTES, 'UTF-8') ?></p>
                        <p><strong>Reward:</strong> <?= (int) $game['discount_percentage'] ?>% <?= htmlspecialchars($game['discount_type'], ENT_QUOTES, 'UTF-8') ?></p>
                        <a href="play.php?game_id=<?= (int) $game['id'] ?>" style="display:inline-block; width: 100%; text-align: center; padding: 14px; border-radius: 14px; background: linear-gradient(135deg, var(--gold), #ffd98e); color: #4b2f04; text-decoration: none; font-weight: 700;">Play this game</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="flash">
                <strong>Game:</strong> <?= htmlspecialchars($selectedGame['game_name'], ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Type:</strong> <?= htmlspecialchars(str_replace('_', ' ', $selectedGame['game_type']), ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Description:</strong> <?= htmlspecialchars(gameDescriptionText($selectedGame), ENT_QUOTES, 'UTF-8') ?><br>
                <strong>Prize:</strong> <?= (int) $selectedGame['discount_percentage'] ?>% <?= htmlspecialchars($selectedGame['discount_type'], ENT_QUOTES, 'UTF-8') ?>
            </div>

            <section class="headline" style="margin-top: 24px;">
                <h1><?= htmlspecialchars($selectedGame['prompt_text'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p><?= htmlspecialchars($selectedGame['clue_text'], ENT_QUOTES, 'UTF-8') ?></p>
            </section>

            <form method="post" style="margin-top: 24px;">
                <input type="hidden" name="game_id" value="<?= (int) $selectedGame['id'] ?>">
                <div class="card" style="max-width: 620px; margin: 0 auto;">
                    <div style="margin-bottom: 14px;">
                        <?= htmlspecialchars($selectedGame['game_type'] === 'crossword'
                            ? 'Enter the crossword solution'
                            : 'Enter the destination you think is correct', ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <input
                        type="text"
                        name="answer"
                        placeholder="<?= htmlspecialchars($selectedGame['game_type'] === 'crossword' ? 'Type the crossword word' : 'Type the destination name', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        style="width: 100%; padding: 14px; border-radius: 14px; border: 1px solid rgba(15, 53, 82, 0.15); margin-bottom: 16px;"
                    >
                    <button type="submit">Submit answer and play</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <?php
        $reviewReturnTo = 'play.php';
        require __DIR__ . '/review_modal.php';

        $chatbotGames = $games;
        $chatbotSelectedGame = $selectedGame;
        require __DIR__ . '/chatbot.php';
    ?>
</body>
</html>
