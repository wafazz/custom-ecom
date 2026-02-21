<?php

require_once __DIR__ . '/BaseModel.php';

class CartLock extends BaseModel
{
    protected $table = 'cart_lock_senangpay';

    public function softDeleteBySession($sessionId, $dateNow)
    {
        $sql = "UPDATE `cart_lock_senangpay` SET `deleted_at` = ? WHERE `session_id` = ?";
        return $this->execute($sql, "ss", [$dateNow, $sessionId]);
    }

    public function findByCartAndSession($cartId, $sessionId)
    {
        $sql = "SELECT * FROM `cart_lock_senangpay` WHERE `cart_id` = ? AND `session_id` = ?";
        $rows = $this->query($sql, "ss", [$cartId, $sessionId]);
        return $rows[0] ?? null;
    }

    public function updateLock($id, $data)
    {
        $sql = "UPDATE `cart_lock_senangpay` SET `quantity` = ?, `price` = ?, `weight` = ?, `total_weight` = ?, `updated_at` = ?, `locked_date` = ?, `deleted_at` = NULL WHERE `id` = ?";
        return $this->execute($sql, "ssssssi", [
            $data['quantity'], $data['price'], $data['weight'], $data['total_weight'],
            $data['updated_at'], $data['locked_date'], $id
        ]);
    }

    public function createLock($data)
    {
        return $this->create($data);
    }

    public function getActiveBySession($sessionId)
    {
        $sql = "SELECT * FROM `cart_lock_senangpay` WHERE `session_id` = ? AND `deleted_at` IS NULL";
        return $this->query($sql, "s", [$sessionId]);
    }
}
