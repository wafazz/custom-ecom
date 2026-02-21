<?php

require_once __DIR__ . '/BaseModel.php';

class Membership extends BaseModel
{
    protected $table = 'membership';

    public function findById($id)
    {
        $sql = "SELECT * FROM `membership` WHERE `id` = ?";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function addPointHistory($data)
    {
        $sql = "INSERT INTO `membership_point_history` (`membership_id`, `referral`, `order_id`, `purchase_amount`, `point_amount`, `date_purchase`, `date_expired_point`, `point_status`) VALUES (?, ?, ?, ?, ?, ?, '', '0')";
        return $this->execute($sql, "ssssss", [
            $data['membership_id'], $data['referral'], $data['order_id'],
            $data['purchase_amount'], $data['point_amount'], $data['date_purchase']
        ]);
    }
}
