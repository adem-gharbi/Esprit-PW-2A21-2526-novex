<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class GameSession
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function create(int $playerId, int $adminGameId, string $submittedAnswer, bool $isWinner): int
    {
        $sql = 'INSERT INTO game_sessions (player_id, admin_game_id, submitted_answer, chosen_box, is_winner, played_at)
                VALUES (:player_id, :admin_game_id, :submitted_answer, NULL, :is_winner, NOW())';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':player_id' => $playerId,
            ':admin_game_id' => $adminGameId,
            ':submitted_answer' => $submittedAnswer,
            ':is_winner' => $isWinner ? 1 : 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $sql = 'SELECT gs.*, p.full_name, p.email, ag.game_name, ag.game_type
                FROM game_sessions gs
                INNER JOIN players p ON p.id = gs.player_id
                INNER JOIN admin_games ag ON ag.id = gs.admin_game_id
                ORDER BY gs.played_at DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM game_sessions WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $session = $stmt->fetch();

        return $session ?: null;
    }

    public function update(int $id, string $submittedAnswer, bool $isWinner): bool
    {
        $sql = 'UPDATE game_sessions
                SET submitted_answer = :submitted_answer, is_winner = :is_winner
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':submitted_answer' => $submittedAnswer,
            ':is_winner' => $isWinner ? 1 : 0,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM game_sessions WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function countWinsByPlayer(int $playerId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM game_sessions WHERE player_id = :player_id AND is_winner = 1');
        $stmt->execute([':player_id' => $playerId]);

        return (int) $stmt->fetchColumn();
    }
}
