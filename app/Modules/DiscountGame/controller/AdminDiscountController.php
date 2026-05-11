<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/AdminDiscount.php';

class AdminDiscountController
{
    private AdminDiscount $discountModel;

    public function __construct()
    {
        $this->discountModel = new AdminDiscount();
    }

    public function index(): array
    {
        return $this->discountModel->getAll();
    }

    public function show(int $id): ?array
    {
        return $this->discountModel->getById($id);
    }

    public function active(): array
    {
        return $this->discountModel->getActive();
    }

    public function store(array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $id = $this->discountModel->create($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return ['success' => false, 'errors' => ['This discount id already exists. Please use a different one.']];
            }

            throw $exception;
        }

        return ['success' => true, 'discount' => $this->discountModel->getById($id)];
    }

    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->discountModel->update($id, $data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                return ['success' => false, 'errors' => ['This discount id already exists. Please use a different one.']];
            }

            throw $exception;
        }

        return ['success' => true, 'discount' => $this->discountModel->getById($id)];
    }

    public function delete(int $id): bool
    {
        return $this->discountModel->delete($id);
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['discount_identifier'] ?? ''))) {
            $errors[] = 'Discount id is required.';
        }

        if (empty(trim($data['discount_type'] ?? ''))) {
            $errors[] = 'Discount type is required.';
        }

        if (!isset($data['discount_percentage']) || (int) $data['discount_percentage'] <= 0) {
            $errors[] = 'Discount percentage is required.';
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

        return $errors;
    }
}
