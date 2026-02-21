<?php

require_once __DIR__ . '/BaseModel.php';

class CourierSetting extends BaseModel
{
    protected $table = 'jt_setting';

    public function getSettings($tableName)
    {
        $sql = "SELECT * FROM `{$tableName}` WHERE id = 1 LIMIT 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function updateMode($tableName, $mode)
    {
        $sql = "UPDATE `{$tableName}` SET `production_sandbox` = ? WHERE id = 1";
        return $this->execute($sql, "s", [$mode]);
    }

    public function updateSandbox($tableName, $data)
    {
        $sql = "UPDATE `{$tableName}` SET `username_sanbox` = ?, `password_sandbox` = ?, `cuscode_sandbox` = ?, `key_sandbox` = ? WHERE id = 1";
        return $this->execute($sql, "ssss", [$data['username'], $data['password'], $data['cuscode'], $data['key']]);
    }

    public function updateProduction($tableName, $data)
    {
        $sql = "UPDATE `{$tableName}` SET `username_production` = ?, `password_production` = ?, `cuscode_production` = ?, `key_production` = ? WHERE id = 1";
        return $this->execute($sql, "ssss", [$data['username'], $data['password'], $data['cuscode'], $data['key']]);
    }
}
