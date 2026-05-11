<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class AdminGame
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO admin_games (
                    game_identifier,
                    game_name,
                    release_date,
                    expiration_date,
                    game_type,
                    game_description,
                    prompt_text,
                    clue_text,
                    correct_answer,
                    admin_discount_id,
                    game_status,
                    created_at
                ) VALUES (
                    :game_identifier,
                    :game_name,
                    :release_date,
                    :expiration_date,
                    :game_type,
                    :game_description,
                    :prompt_text,
                    :clue_text,
                    :correct_answer,
                    :admin_discount_id,
                    :game_status,
                    NOW()
                )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':game_identifier' => $data['game_identifier'],
            ':game_name' => $data['game_name'],
            ':release_date' => $data['release_date'],
            ':expiration_date' => $data['expiration_date'],
            ':game_type' => $data['game_type'],
            ':game_description' => $data['game_description'],
            ':prompt_text' => $data['prompt_text'],
            ':clue_text' => $data['clue_text'],
            ':correct_answer' => $data['correct_answer'],
            ':admin_discount_id' => $data['admin_discount_id'],
            ':game_status' => $data['game_status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $sql = 'SELECT ag.*, ad.discount_identifier, ad.discount_type, ad.discount_percentage
                FROM admin_games ag
                INNER JOIN admin_discounts ad ON ad.id = ag.admin_discount_id
                ORDER BY ag.created_at DESC, ag.id DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getAvailableGames(): array
    {
        $sql = 'SELECT ag.*, ad.discount_identifier, ad.discount_type, ad.discount_percentage,
                       ad.release_date AS discount_release_date, ad.expiration_date AS discount_expiration_date
                FROM admin_games ag
                INNER JOIN admin_discounts ad ON ad.id = ag.admin_discount_id
                WHERE ag.game_status = "active"
                  AND ag.release_date <= CURDATE()
                  AND ag.expiration_date >= CURDATE()
                  AND ad.release_date <= CURDATE()
                  AND ad.expiration_date >= CURDATE()
                ORDER BY ag.created_at DESC, ag.id DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT ag.*, ad.discount_identifier, ad.discount_type, ad.discount_percentage,
                       ad.release_date AS discount_release_date, ad.expiration_date AS discount_expiration_date
                FROM admin_games ag
                INNER JOIN admin_discounts ad ON ad.id = ag.admin_discount_id
                WHERE ag.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $game = $stmt->fetch();

        return $game ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE admin_games
                SET game_identifier = :game_identifier,
                    game_name = :game_name,
                    release_date = :release_date,
                    expiration_date = :expiration_date,
                    game_type = :game_type,
                    game_description = :game_description,
                    prompt_text = :prompt_text,
                    clue_text = :clue_text,
                    correct_answer = :correct_answer,
                    admin_discount_id = :admin_discount_id,
                    game_status = :game_status
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':game_identifier' => $data['game_identifier'],
            ':game_name' => $data['game_name'],
            ':release_date' => $data['release_date'],
            ':expiration_date' => $data['expiration_date'],
            ':game_type' => $data['game_type'],
            ':game_description' => $data['game_description'],
            ':prompt_text' => $data['prompt_text'],
            ':clue_text' => $data['clue_text'],
            ':correct_answer' => $data['correct_answer'],
            ':admin_discount_id' => $data['admin_discount_id'],
            ':game_status' => $data['game_status'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM admin_games WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }
}
