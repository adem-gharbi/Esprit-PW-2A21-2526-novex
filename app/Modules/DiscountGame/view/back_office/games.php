<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/AdminGameController.php';

$controller = new AdminGameController();
$discountOptions = $controller->discountOptions();
$errors = [];
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

$editingGame = null;

if (isset($_GET['edit'])) {
    $editingGame = $controller->show((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $payload = [
        'game_identifier' => trim($_POST['game_identifier'] ?? ''),
        'game_name' => trim($_POST['game_name'] ?? ''),
        'release_date' => $_POST['release_date'] ?? '',
        'expiration_date' => $_POST['expiration_date'] ?? '',
        'game_type' => trim($_POST['game_type'] ?? ''),
        'game_description' => trim($_POST['game_description'] ?? ''),
        'prompt_text' => trim($_POST['prompt_text'] ?? ''),
        'clue_text' => trim($_POST['clue_text'] ?? ''),
        'correct_answer' => trim($_POST['correct_answer'] ?? ''),
        'admin_discount_id' => (int) ($_POST['admin_discount_id'] ?? 0),
        'game_status' => trim($_POST['game_status'] ?? ''),
    ];

    if ($action === 'delete') {
        $controller->delete((int) ($_POST['id'] ?? 0));
        $_SESSION['admin_flash'] = 'Game deleted successfully.';
        header('Location: games.php');
        exit;
    }

    if ($action === 'update') {
        $result = $controller->update((int) ($_POST['id'] ?? 0), $payload);

        if ($result['success']) {
            $_SESSION['admin_flash'] = 'Game updated successfully.';
            header('Location: games.php');
            exit;
        }

        $errors = $result['errors'] ?? [];
        $editingGame = array_merge(['id' => (int) ($_POST['id'] ?? 0)], $payload);
    } else {
        $result = $controller->store($payload);

        if ($result['success']) {
            $_SESSION['admin_flash'] = 'Game added successfully.';
            header('Location: games.php');
            exit;
        }

        $errors = $result['errors'] ?? [];
        $editingGame = $payload;
    }
}

$games = $controller->index();
$formData = $editingGame ?? [
    'id' => '',
    'game_identifier' => '',
    'game_name' => '',
    'release_date' => '',
    'expiration_date' => '',
    'game_type' => '',
    'game_description' => '',
    'prompt_text' => '',
    'clue_text' => '',
    'correct_answer' => '',
    'admin_discount_id' => '',
    'game_status' => 'active',
];
$isEditing = isset($formData['id']) && $formData['id'] !== '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Admin Games</title>
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
                <a href="index.php" class="menu-item">Dashboard</a>
                <a href="games.php" class="menu-item active">Games</a>
                <a href="discounts.php" class="menu-item">Discounts</a>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <div class="page-title">Games Management</div>
                    <div class="page-subtitle">Add, update, and review Voyagio games from the back office.</div>
                </div>
                <div class="user-card">
                    <span>Admin</span>
                    <div class="avatar">AD</div>
                </div>
            </header>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h2><?= $isEditing ? 'Update Game' : 'Add Game' ?></h2>
                        <p>Manage the game type, clue, correct answer, and linked discount that the traveler can win.</p>
                    </div>
                    <a href="index.php" class="btn btn-primary">Back Office</a>
                </div>

                <?php if ($flash): ?>
                    <div class="success-banner"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="error-banner">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="panel-form" method="post">
                    <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($formData['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                    <label for="game_identifier">
                        Game id
                        <input id="game_identifier" name="game_identifier" type="text" value="<?= htmlspecialchars((string) $formData['game_identifier'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="game_name">
                        Game name
                        <input id="game_name" name="game_name" type="text" value="<?= htmlspecialchars((string) $formData['game_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="release_date">
                        Date of release
                        <input id="release_date" name="release_date" type="date" value="<?= htmlspecialchars((string) $formData['release_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="expiration_date">
                        Date of expiration
                        <input id="expiration_date" name="expiration_date" type="date" value="<?= htmlspecialchars((string) $formData['expiration_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="game_type">
                        Type of game
                        <select id="game_type" name="game_type" required>
                            <option value="">Select a game type</option>
                            <option value="destination_guessing" <?= $formData['game_type'] === 'destination_guessing' ? 'selected' : '' ?>>Destination guessing</option>
                            <option value="crossword" <?= $formData['game_type'] === 'crossword' ? 'selected' : '' ?>>Crossword</option>
                        </select>
                    </label>

                    <label for="game_description">
                        Small game description
                        <input id="game_description" name="game_description" type="text" value="<?= htmlspecialchars((string) $formData['game_description'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: Guess the destination from the travel clue." required>
                    </label>

                    <label for="prompt_text">
                        Prompt text
                        <input id="prompt_text" name="prompt_text" type="text" value="<?= htmlspecialchars((string) $formData['prompt_text'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="clue_text">
                        Clue text
                        <input id="clue_text" name="clue_text" type="text" value="<?= htmlspecialchars((string) $formData['clue_text'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="correct_answer">
                        Correct answer
                        <input id="correct_answer" name="correct_answer" type="text" value="<?= htmlspecialchars((string) $formData['correct_answer'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="admin_discount_id">
                        Winning discount
                        <select id="admin_discount_id" name="admin_discount_id" required>
                            <option value="">Select a discount</option>
                            <?php foreach ($discountOptions as $discountOption): ?>
                                <option value="<?= (int) $discountOption['id'] ?>" <?= (string) $formData['admin_discount_id'] === (string) $discountOption['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($discountOption['discount_identifier'] . ' - ' . $discountOption['discount_type'] . ' (' . $discountOption['discount_percentage'] . '%)', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label for="game_status">
                        Status of game
                        <select id="game_status" name="game_status" required>
                            <?php foreach (['active', 'inactive', 'draft', 'archived'] as $status): ?>
                                <option value="<?= $status ?>" <?= $formData['game_status'] === $status ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?= $isEditing ? 'Update game' : 'Add game' ?></button>
                        <?php if ($isEditing): ?>
                            <a href="games.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="table-card">
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th>Game id</th>
                                <th>Name</th>
                                <th>Release</th>
                                <th>Expiration</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Discount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$games): ?>
                                <tr>
                                    <td colspan="9">No games added yet.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($games as $game): ?>
                                <tr>
                                    <td><?= htmlspecialchars($game['game_identifier'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['game_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['release_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['expiration_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['game_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['game_description'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($game['discount_identifier'] . ' (' . $game['discount_percentage'] . '%)', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($game['game_status'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($game['game_status'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a class="action-link" href="games.php?edit=<?= (int) $game['id'] ?>">Edit</a>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                            <button type="submit" class="action-link danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php if ($flash): ?>
        <script>
            alert(<?= json_encode($flash, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);
        </script>
    <?php elseif ($errors): ?>
        <script>
            alert(<?= json_encode(implode("\n", $errors), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);
        </script>
    <?php endif; ?>
</body>
</html>
