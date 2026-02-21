<?php

require_once __DIR__ . '/BaseModel.php';

class StripeSetting extends BaseModel
{
    protected $table = 'stripe_setting';

    public function getSettings()
    {
        $sql = "SELECT * FROM `stripe_setting` WHERE `id` = 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }
}
