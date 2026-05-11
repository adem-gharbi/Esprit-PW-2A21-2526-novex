<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/GameReviewController.php';

if (!isset($_SESSION['player'])) {
    header('Location: index.php');
    exit;
}

$returnTo = (string) ($_POST['return_to'] ?? 'play.php');
$allowedReturns = ['play.php', 'coupon.php'];

if (!in_array($returnTo, $allowedReturns, true)) {
    $returnTo = 'play.php';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $returnTo);
    exit;
}

$controller = new GameReviewController();
$result = $controller->save([
    'player_id' => (int) $_SESSION['player']['id'],
    'admin_game_id' => (int) ($_POST['admin_game_id'] ?? 0),
    'game_session_id' => (int) ($_POST['game_session_id'] ?? 0),
    'rating' => (int) ($_POST['rating'] ?? 0),
    'description' => trim($_POST['description'] ?? ''),
]);

$_SESSION['review_message'] = $result['message'];

if ($result['success'] ?? false) {
    unset($_SESSION['pending_review']);
}

header('Location: ' . $returnTo);
exit;
