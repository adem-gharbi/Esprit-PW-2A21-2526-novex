<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class GameReview
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function save(int $playerId, int $adminGameId, int $gameSessionId, int $rating, string $description): bool
    {
        $sql = 'INSERT INTO game_reviews (
                    player_id,
                    admin_game_id,
                    game_session_id,
                    rating,
                    description,
                    created_at
                ) VALUES (
                    :player_id,
                    :admin_game_id,
                    :game_session_id,
                    :rating,
                    :description,
                    NOW()
                )
                ON DUPLICATE KEY UPDATE
                    rating = VALUES(rating),
                    description = VALUES(description)';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':player_id' => $playerId,
            ':admin_game_id' => $adminGameId,
            ':game_session_id' => $gameSessionId,
            ':rating' => $rating,
            ':description' => $description,
        ]);
    }

    public function getLatest(int $limit = 10): array
    {
        $sql = 'SELECT gr.*, p.full_name, p.email, ag.game_name, ag.game_identifier
                FROM game_reviews gr
                INNER JOIN players p ON p.id = gr.player_id
                INNER JOIN admin_games ag ON ag.id = gr.admin_game_id
                ORDER BY gr.created_at DESC
                LIMIT :limit';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
