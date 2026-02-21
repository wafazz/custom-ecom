<?php

require_once __DIR__ . '/BaseModel.php';

class ShopCategory extends BaseModel
{
    protected $table = 'category';

    public function getByAssignedUser($userId)
    {
        $sql = "SELECT * FROM `category` WHERE `status` = '1' AND `assigned_user` LIKE ?";
        return $this->query($sql, "s", ["%[$userId]%"]);
    }
}
