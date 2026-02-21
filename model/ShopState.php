<?php

require_once __DIR__ . '/BaseModel.php';

class ShopState extends BaseModel
{
    protected $table = 'so_states';

    public function findByName($name)
    {
        $sql = "SELECT * FROM `so_states` WHERE `name` = ?";
        $rows = $this->query($sql, "s", [$name]);
        return $rows[0] ?? null;
    }

    public function getByCountryId($countryId)
    {
        $sql = "SELECT * FROM `so_states` WHERE `country_id` = ?";
        return $this->query($sql, "i", [$countryId]);
    }
}
