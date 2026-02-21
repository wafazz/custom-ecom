<?php

require_once __DIR__ . '/BaseModel.php';

class ShopOrder extends BaseModel
{
    protected $table = 'customer_order';

    public function createOrder($data)
    {
        return $this->create($data);
    }

    public function updatePayment($verifyId, $payCode, $payUrl)
    {
        $sql = "UPDATE `customer_order` SET `payment_code` = ?, `payment_url` = ? WHERE `verify_id` = ?";
        return $this->execute($sql, "sss", [$payCode, $payUrl, $verifyId]);
    }
}
