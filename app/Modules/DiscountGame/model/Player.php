<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class Player
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO players (full_name, email, destination, created_at)
                VALUES (:full_name, :email, :destination, NOW())';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':destination' => $data['destination'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM players ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM players WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $player = $stmt->fetch();

        return $player ?: null;
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM players WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $player = $stmt->fetch();

        return $player ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE players
                SET full_name = :full_name, email = :email, destination = :destination
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':destination' => $data['destination'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM players WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }
}
