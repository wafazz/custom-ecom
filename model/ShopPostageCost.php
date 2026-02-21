<?php

require_once __DIR__ . '/BaseModel.php';

class ShopPostageCost extends BaseModel
{
    protected $table = 'postage_cost';

    public function findByStateId($stateId)
    {
        $sql = "SELECT * FROM `postage_cost` WHERE `countrys` LIKE ? AND `type` = '1'";
        $rows = $this->query($sql, "s", ["%[$stateId]%"]);
        return $rows[0] ?? null;
    }
}
