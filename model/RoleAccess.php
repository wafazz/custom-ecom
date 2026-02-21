<?php

require_once __DIR__ . '/BaseModel.php';

class RoleAccess extends BaseModel
{
    protected $table = 'role_access';

    public function getAllSorted()
    {
        $sql = "SELECT * FROM `role_access` ORDER BY `sort` ASC";
        return $this->query($sql);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM `role_access` WHERE `id` = ?";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function updateAllowedUser($id, $allowedUser)
    {
        $sql = "UPDATE `role_access` SET `allowed_user` = ? WHERE `id` = ?";
        return $this->execute($sql, "si", [$allowedUser, $id]);
    }
}
