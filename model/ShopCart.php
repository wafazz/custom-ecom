<?php

require_once __DIR__ . '/BaseModel.php';

class ShopCart extends BaseModel
{
    protected $table = 'cart';

    public function getByUserAndCart($userId, $cartId, $status = '0')
    {
        $sql = "SELECT * FROM `cart` WHERE `order_by` = ? AND `cart_id` = ? AND `status` = ?";
        return $this->query($sql, "sss", [$userId, $cartId, $status]);
    }

    public function getTotalWeight($userId, $cartId)
    {
        $sql = "SELECT SUM(`total_weight`) AS tweight FROM `cart` WHERE `order_by` = ? AND `cart_id` = ?";
        $rows = $this->query($sql, "ss", [$userId, $cartId]);
        return (float) ($rows[0]['tweight'] ?? 0);
    }

    public function getActiveByCartId($cartId)
    {
        $sql = "SELECT * FROM `cart` WHERE `cart_id` = ? AND `status` IN(0,1)";
        return $this->query($sql, "s", [$cartId]);
    }

    public function getTotalQtyByCartId($cartId)
    {
        $sql = "SELECT SUM(`quantity`) AS qty FROM `cart` WHERE `cart_id` = ? AND `status` IN(0,1)";
        $rows = $this->query($sql, "s", [$cartId]);
        return (int) ($rows[0]['qty'] ?? 0);
    }
}
