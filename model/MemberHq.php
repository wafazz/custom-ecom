<?php

require_once __DIR__ . '/BaseModel.php';

class MemberHq extends BaseModel
{
    protected $table = 'member_hq';

    public function findById($id)
    {
        $sql = "SELECT * FROM `member_hq` WHERE `id` = ? LIMIT 1";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function updatePassword($id, $hash)
    {
        $sql = "UPDATE `member_hq` SET `password` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$hash, $id]);
    }
}
