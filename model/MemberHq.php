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

    public function getStaffList($excludeIds = [])
    {
        if (empty($excludeIds)) {
            $sql = "SELECT * FROM `member_hq` WHERE `deleted_at` IS NULL";
            return $this->query($sql);
        }
        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
        $types = str_repeat('i', count($excludeIds));
        $sql = "SELECT * FROM `member_hq` WHERE `id` NOT IN ({$placeholders}) AND `deleted_at` IS NULL";
        return $this->query($sql, $types, $excludeIds);
    }

    public function addStaff($data)
    {
        $sql = "INSERT INTO `member_hq` (`email`, `password`, `sec_pin`, `f_name`, `l_name`, `phone`, `role`, `created_at`, `updated_at`, `status`)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '1')";
        return $this->execute($sql, "sssssssss", [
            $data['email'], $data['password'], $data['sec_pin'],
            $data['f_name'], $data['l_name'], $data['phone'], $data['role'],
            $data['created_at'], $data['updated_at']
        ]);
    }

    public function updateMemberStatus($id, $status, $dateNow, $softDelete = false)
    {
        if ($softDelete) {
            $sql = "UPDATE `member_hq` SET `status` = ?, `updated_at` = ?, `deleted_at` = ? WHERE `id` = ?";
            return $this->execute($sql, "sssi", [$status, $dateNow, $dateNow, $id]);
        }
        $sql = "UPDATE `member_hq` SET `status` = ?, `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "ssi", [$status, $dateNow, $id]);
    }

    public function updateProfile($id, $data, $dateNow)
    {
        $sql = "UPDATE `member_hq` SET `f_name` = ?, `l_name` = ?, `phone` = ?, `updated_at` = ? WHERE `id` = ?";
        return $this->execute($sql, "ssssi", [$data['f_name'], $data['l_name'], $data['phone'], $dateNow, $id]);
    }

}
