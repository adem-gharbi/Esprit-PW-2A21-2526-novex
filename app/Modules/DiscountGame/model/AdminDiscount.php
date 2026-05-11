<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class AdminDiscount
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO admin_discounts (
                    discount_identifier,
                    discount_type,
                    discount_percentage,
                    release_date,
                    expiration_date,
                    created_at
                ) VALUES (
                    :discount_identifier,
                    :discount_type,
                    :discount_percentage,
                    :release_date,
                    :expiration_date,
                    NOW()
                )';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':discount_identifier' => $data['discount_identifier'],
            ':discount_type' => $data['discount_type'],
            ':discount_percentage' => $data['discount_percentage'],
            ':release_date' => $data['release_date'],
            ':expiration_date' => $data['expiration_date'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM admin_discounts ORDER BY created_at DESC, id DESC');

        return $stmt->fetchAll();
    }

    public function getActive(): array
    {
        $sql = 'SELECT * FROM admin_discounts
                WHERE release_date <= CURDATE()
                  AND expiration_date >= CURDATE()
                ORDER BY created_at DESC, id DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM admin_discounts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $discount = $stmt->fetch();

        return $discount ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE admin_discounts
                SET discount_identifier = :discount_identifier,
                    discount_type = :discount_type,
                    discount_percentage = :discount_percentage,
                    release_date = :release_date,
                    expiration_date = :expiration_date
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':discount_identifier' => $data['discount_identifier'],
            ':discount_type' => $data['discount_type'],
            ':discount_percentage' => $data['discount_percentage'],
            ':release_date' => $data['release_date'],
            ':expiration_date' => $data['expiration_date'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM admin_discounts WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }
}
