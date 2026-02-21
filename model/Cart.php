<?php

require_once __DIR__ . '/BaseModel.php';

class Cart extends BaseModel
{
    protected $table = 'cart';

    /**
     * Check if an item already exists in the cart for a given session.
     */
    public function findExistingItem(string $sessionId, int $productId, int $variantId): ?array
    {
        $sql = "SELECT * FROM `cart`
                WHERE `session_id` = ? AND `p_id` = ? AND `pv_id` = ?
                AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $rows = $this->query($sql, "sii", [$sessionId, $productId, $variantId]);
        return $rows[0] ?? null;
    }

    /**
     * Insert a new cart item.
     */
    public function addItem(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Update quantity and weight for an existing cart item.
     */
    public function updateItemQuantity(string $sessionId, int $productId, int $variantId, int $newQty, float $newWeight, string $dateNow): bool
    {
        $sql = "UPDATE `cart` SET `quantity` = ?, `total_weight` = ?, `updated_at` = ?
                WHERE `session_id` = ? AND `p_id` = ? AND `pv_id` = ?
                AND `deleted_at` IS NULL AND `status` IN(0,1)";
        return $this->execute($sql, "idssii", [$newQty, $newWeight, $dateNow, $sessionId, $productId, $variantId]);
    }

    /**
     * Update timestamps on all active items in a session.
     */
    public function touchSession(string $sessionId, string $dateNow): bool
    {
        $sql = "UPDATE `cart` SET `updated_at` = ?
                WHERE `session_id` = ? AND `deleted_at` IS NULL AND `status` IN(0,1)";
        return $this->execute($sql, "ss", [$dateNow, $sessionId]);
    }

    /**
     * Get the sum of quantities for a session (replaces cartCount()).
     */
    public function countBySession(string $sessionId): int
    {
        $sql = "SELECT SUM(`quantity`) AS cartQTY FROM `cart`
                WHERE `session_id` = ? AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $rows = $this->query($sql, "s", [$sessionId]);
        return (int) ($rows[0]['cartQTY'] ?? 0);
    }

    /**
     * Get all active cart items for a session (replaces cartList()).
     */
    public function getActiveBySession(string $sessionId): array
    {
        $sql = "SELECT * FROM `cart`
                WHERE `session_id` = ? AND `deleted_at` IS NULL AND `status` IN(0,1)";
        return $this->query($sql, "s", [$sessionId]);
    }

    /**
     * Soft delete a cart item by ID.
     */
    public function removeItem(int $cartId): bool
    {
        $now = dateNow();
        $sql = "UPDATE `cart` SET `deleted_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$now, $cartId]);
    }

    /**
     * Mark a cart item as paid (status = 1).
     */
    public function markAsPaid(int $cartId, string $dateNow): bool
    {
        $sql = "UPDATE `cart` SET `status` = '1', `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$dateNow, $cartId]);
    }

    public function updateStatusBySession($sessionId, $status, $dateNow)
    {
        $sql = "UPDATE `cart` SET `status` = ?, `updated_at` = ? WHERE `session_id` = ? AND `deleted_at` IS NULL";
        return $this->execute($sql, "sss", [$status, $dateNow, $sessionId]);
    }

    public function getTotalWeight($sessionId)
    {
        $sql = "SELECT SUM(`quantity` * `weight`) AS tWeight FROM `cart` WHERE `session_id` = ? AND `deleted_at` IS NULL AND `status` IN(0,1)";
        $rows = $this->query($sql, "s", [$sessionId]);
        return (float) ($rows[0]['tWeight'] ?? 0);
    }

    public function softDeleteById($cartId, $dateNow)
    {
        $sql = "UPDATE `cart` SET `deleted_at` = ?, `status` = '4' WHERE `id` = ?";
        return $this->execute($sql, "si", [$dateNow, $cartId]);
    }

    public function getById($cartId)
    {
        $sql = "SELECT * FROM `cart` WHERE `id` = ?";
        $rows = $this->query($sql, "i", [$cartId]);
        return $rows[0] ?? null;
    }

    public function updateById($cartId, $data)
    {
        $sets = [];
        $types = '';
        $params = [];
        foreach ($data as $col => $val) {
            $sets[] = "`{$col}` = ?";
            $types .= 's';
            $params[] = $val;
        }
        $types .= 'i';
        $params[] = $cartId;
        $sql = "UPDATE `cart` SET " . implode(', ', $sets) . " WHERE `id` = ?";
        return $this->execute($sql, $types, $params);
    }

    public function restoreAndMarkPaid($cartId, $dateNow)
    {
        $sql = "UPDATE `cart` SET `updated_at` = ?, `deleted_at` = NULL, `status` = '1' WHERE `id` = ?";
        return $this->execute($sql, "si", [$dateNow, $cartId]);
    }

    public function findActiveByIdAndSession($id, $sessionId)
    {
        $sql = "SELECT * FROM `cart` WHERE `id` = ? AND `session_id` = ? AND `deleted_at` IS NULL AND `status` = '0'";
        $rows = $this->query($sql, "is", [$id, $sessionId]);
        return $rows[0] ?? null;
    }

    public function softDeleteWithStatus($id, $dateNow, $status = '5')
    {
        $sql = "UPDATE `cart` SET `deleted_at` = ?, `status` = ? WHERE `id` = ?";
        return $this->execute($sql, "ssi", [$dateNow, $status, $id]);
    }
}
