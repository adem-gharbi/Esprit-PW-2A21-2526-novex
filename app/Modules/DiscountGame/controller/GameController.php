<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/GameSession.php';
require_once __DIR__ . '/../model/Coupon.php';
require_once __DIR__ . '/../model/AdminGame.php';

class GameController
{
    private GameSession $gameSessionModel;
    private Coupon $couponModel;
    private AdminGame $adminGameModel;

    public function __construct()
    {
        $this->gameSessionModel = new GameSession();
        $this->couponModel = new Coupon();
        $this->adminGameModel = new AdminGame();
    }

    public function index(): array
    {
        return $this->gameSessionModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->gameSessionModel->getById($id);
    }

    public function availableGames(): array
    {
        return $this->adminGameModel->getAvailableGames();
    }

    public function findAvailableGame(int $gameId): ?array
    {
        $game = $this->adminGameModel->getById($gameId);

        if (!$game) {
            return null;
        }

        $today = date('Y-m-d');
        $isGameAvailable = $game['game_status'] === 'active'
            && $game['release_date'] <= $today
            && $game['expiration_date'] >= $today
            && $game['discount_release_date'] <= $today
            && $game['discount_expiration_date'] >= $today;

        return $isGameAvailable ? $game : null;
    }

    public function play(int $playerId, int $gameId, string $answer): array
    {
        $game = $this->findAvailableGame($gameId);

        if (!$game) {
            return ['success' => false, 'message' => 'This game is not available right now.'];
        }

        $normalizedAnswer = $this->normalizeAnswer($answer);

        if ($normalizedAnswer === '') {
            return ['success' => false, 'message' => 'Please enter an answer before submitting.'];
        }

        $isWinner = $normalizedAnswer === $this->normalizeAnswer((string) $game['correct_answer']);
        $sessionId = $this->gameSessionModel->create($playerId, $gameId, $answer, $isWinner);

        $coupon = null;
        if ($isWinner) {
            $coupon = $this->createCoupon($playerId, $game);
        }

        return [
            'success' => true,
            'game' => $game,
            'session' => $this->gameSessionModel->getById($sessionId),
            'is_winner' => $isWinner,
            'coupon' => $coupon,
        ];
    }

    public function update(int $id, string $submittedAnswer, bool $isWinner): bool
    {
        return $this->gameSessionModel->update($id, $submittedAnswer, $isWinner);
    }

    public function delete(int $id): bool
    {
        return $this->gameSessionModel->delete($id);
    }

    private function createCoupon(int $playerId, array $game): array
    {
        $code = 'VOYAGIO-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $expiresAt = (string) $game['discount_expiration_date'];

        $couponId = $this->couponModel->create(
            $playerId,
            (int) $game['admin_discount_id'],
            $code,
            (string) $game['discount_type'],
            (int) $game['discount_percentage'],
            $expiresAt
        );

        return $this->couponModel->getById($couponId);
    }

    private function normalizeAnswer(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return strtolower($value);
    }
}
