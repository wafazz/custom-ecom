<?php

require_once __DIR__ . '/BaseModel.php';

class StateSetting extends BaseModel
{
    protected $table = 'state';

    public function findByCountryAndName($countryId, $name)
    {
        $sql = "SELECT * FROM `state` WHERE `country_id` = ? AND `name` = ?";
        $rows = $this->query($sql, "ss", [$countryId, $name]);
        return $rows[0] ?? null;
    }
}
