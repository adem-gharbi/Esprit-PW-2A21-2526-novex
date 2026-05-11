<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Player.php';

class PlayerController
{
    private Player $playerModel;

    public function __construct()
    {
        $this->playerModel = new Player();
    }

    public function index(): array
    {
        return $this->playerModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->playerModel->getById($id);
    }

    public function register(array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $existingPlayer = $this->playerModel->getByEmail($data['email']);

        if ($existingPlayer) {
            return ['success' => true, 'player' => $existingPlayer, 'is_existing' => true];
        }

        $playerId = $this->playerModel->create($data);
        $player = $this->playerModel->getById($playerId);

        return ['success' => true, 'player' => $player, 'is_existing' => false];
    }

    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->playerModel->update($id, $data);

        return ['success' => true, 'player' => $this->playerModel->getById($id)];
    }

    public function delete(int $id): bool
    {
        return $this->playerModel->delete($id);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['full_name'] ?? ''))) {
            $errors[] = 'Full name is required.';
        }

        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        if (empty(trim($data['destination'] ?? ''))) {
            $errors[] = 'Destination is required.';
        }

        return $errors;
    }
}
