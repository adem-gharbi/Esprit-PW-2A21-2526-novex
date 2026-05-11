<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Coupon.php';

class CouponController
{
    private Coupon $couponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
    }

    public function index(): array
    {
        return $this->couponModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->couponModel->getById($id);
    }

    public function showLatestForPlayer(int $playerId): ?array
    {
        return $this->couponModel->getLatestByPlayer($playerId);
    }

    public function update(int $id, int $percentage, string $expiresAt): bool
    {
        return $this->couponModel->update($id, $percentage, $expiresAt);
    }

    public function delete(int $id): bool
    {
        return $this->couponModel->delete($id);
    }
}
