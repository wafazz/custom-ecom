<?php

require_once __DIR__ . '/BaseModel.php';

class OrderDetail extends BaseModel
{
    protected $table = 'order_details';

    public function createDetail($orderId, $hashCode, $dateNow)
    {
        $sql = "INSERT INTO `order_details` (`order_id`, `hash_code`, `created_at`) VALUES (?, ?, ?)";
        return $this->execute($sql, "sss", [$orderId, $hashCode, $dateNow]);
    }

    public function findByOrderId($orderId)
    {
        $sql = "SELECT * FROM `order_details` WHERE `order_id` = ?";
        $rows = $this->query($sql, "s", [$orderId]);
        return $rows[0] ?? null;
    }

    public function findByHashCode($hashCode)
    {
        $sql = "SELECT * FROM `order_details` WHERE `hash_code` = ?";
        $rows = $this->query($sql, "s", [$hashCode]);
        return $rows[0] ?? null;
    }
}
