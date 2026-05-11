<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class Coupon
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getPDO();
    }

    public function create(
        int $playerId,
        int $adminDiscountId,
        string $code,
        string $discountType,
        int $percentage,
        string $expiresAt
    ): int
    {
        $sql = 'INSERT INTO coupons (player_id, admin_discount_id, code, discount_type, discount_percentage, expires_at, created_at)
                VALUES (:player_id, :admin_discount_id, :code, :discount_type, :discount_percentage, :expires_at, NOW())';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':player_id' => $playerId,
            ':admin_discount_id' => $adminDiscountId,
            ':code' => $code,
            ':discount_type' => $discountType,
            ':discount_percentage' => $percentage,
            ':expires_at' => $expiresAt,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $sql = 'SELECT c.*, p.full_name, p.email, ad.discount_identifier
                FROM coupons c
                INNER JOIN players p ON p.id = c.player_id
                INNER JOIN admin_discounts ad ON ad.id = c.admin_discount_id
                ORDER BY c.created_at DESC';

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM coupons WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $coupon = $stmt->fetch();

        return $coupon ?: null;
    }

    public function getLatestByPlayer(int $playerId): ?array
    {
        $sql = 'SELECT * FROM coupons WHERE player_id = :player_id ORDER BY created_at DESC LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':player_id' => $playerId]);
        $coupon = $stmt->fetch();

        return $coupon ?: null;
    }

    public function update(int $id, int $percentage, string $expiresAt): bool
    {
        $sql = 'UPDATE coupons
                SET discount_percentage = :discount_percentage, expires_at = :expires_at
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':discount_percentage' => $percentage,
            ':expires_at' => $expiresAt,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM coupons WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }
}
