<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/GameReview.php';

class GameReviewController
{
    private GameReview $gameReviewModel;

    public function __construct()
    {
        $this->gameReviewModel = new GameReview();
    }

    public function save(array $data): array
    {
        $playerId = (int) ($data['player_id'] ?? 0);
        $adminGameId = (int) ($data['admin_game_id'] ?? 0);
        $gameSessionId = (int) ($data['game_session_id'] ?? 0);
        $rating = (int) ($data['rating'] ?? 0);
        $description = trim((string) ($data['description'] ?? ''));

        if ($playerId <= 0 || $adminGameId <= 0 || $gameSessionId <= 0) {
            return ['success' => false, 'message' => 'Missing review information.'];
        }

        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Please choose a rating from 1 to 5 stars.'];
        }

        if ($description === '') {
            return ['success' => false, 'message' => 'Please write a short description for the game.'];
        }

        $this->gameReviewModel->save($playerId, $adminGameId, $gameSessionId, $rating, $description);

        return ['success' => true, 'message' => 'Thank you for reviewing this game.'];
    }

    public function latest(int $limit = 10): array
    {
        return $this->gameReviewModel->getLatest($limit);
    }
}
