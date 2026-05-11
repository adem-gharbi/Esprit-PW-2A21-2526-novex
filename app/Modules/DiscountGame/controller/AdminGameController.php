<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/AdminGame.php';
require_once __DIR__ . '/../model/AdminDiscount.php';

class AdminGameController
{
    private AdminGame $gameModel;
    private AdminDiscount $discountModel;

    public function __construct()
    {
        $this->gameModel = new AdminGame();
        $this->discountModel = new AdminDiscount();
    }

    public function index(): array
    {
        return $this->gameModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->gameModel->getById($id);
    }

    public function availableGames(): array
    {
        return $this->gameModel->getAvailableGames();
    }

    public function discountOptions(): array
    {
        return $this->discountModel->getAll();
    }

    public function store(array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $id = $this->gameModel->create($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return ['success' => false, 'errors' => ['This game id already exists. Please use a different one.']];
            }

            throw $exception;
        }

        return ['success' => true, 'game' => $this->gameModel->getById($id)];
    }

    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->gameModel->update($id, $data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return ['success' => false, 'errors' => ['This game id already exists. Please use a different one.']];
            }

            throw $exception;
        }

        return ['success' => true, 'game' => $this->gameModel->getById($id)];
    }

    public function delete(int $id): bool
    {
        return $this->gameModel->delete($id);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['game_identifier'] ?? ''))) {
            $errors[] = 'Game id is required.';
        }

        if (empty(trim($data['game_name'] ?? ''))) {
            $errors[] = 'Game name is required.';
        }

        if (empty($data['release_date'] ?? '')) {
            $errors[] = 'Release date is required.';
        }

        if (empty($data['expiration_date'] ?? '')) {
            $errors[] = 'Expiration date is required.';
        }

        if (!empty($data['release_date']) && !empty($data['expiration_date']) && $data['expiration_date'] < $data['release_date']) {
            $errors[] = 'Expiration date must be after release date.';
        }

        if (empty(trim($data['game_type'] ?? ''))) {
            $errors[] = 'Game type is required.';
        }

        if (empty(trim($data['game_description'] ?? ''))) {
            $errors[] = 'Game description is required.';
        }

        if (empty(trim($data['prompt_text'] ?? ''))) {
            $errors[] = 'Prompt text is required.';
        }

        if (empty(trim($data['clue_text'] ?? ''))) {
            $errors[] = 'Clue text is required.';
        }

        if (empty(trim($data['correct_answer'] ?? ''))) {
            $errors[] = 'Correct answer is required.';
        }

        if (empty($data['admin_discount_id'] ?? '')) {
            $errors[] = 'You must assign a discount to the game.';
        }

        if (empty(trim($data['game_status'] ?? ''))) {
            $errors[] = 'Game status is required.';
        }

        return $errors;
    }
}
