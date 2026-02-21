<?php

require_once __DIR__ . '/BaseModel.php';

class DhlSetting extends BaseModel
{
    protected $table = 'dhl';

    public function getSettings()
    {
        return $this->find(1);
    }

    public function updateSandbox($clientid, $password)
    {
        $sql = "UPDATE `dhl` SET `clientid_test` = ?, `password_test` = ? WHERE `id` = 1";
        return $this->execute($sql, "ss", [$clientid, $password]);
    }

    public function updateProduction($clientid, $password)
    {
        $sql = "UPDATE `dhl` SET `clientid` = ?, `password` = ? WHERE `id` = 1";
        return $this->execute($sql, "ss", [$clientid, $password]);
    }

    public function updateMode($mode)
    {
        $sql = "UPDATE `dhl` SET `production_sandbox` = ? WHERE `id` = 1";
        return $this->execute($sql, "s", [$mode]);
    }
}
