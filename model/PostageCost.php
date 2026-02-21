<?php

require_once __DIR__ . '/BaseModel.php';

class PostageCost extends BaseModel
{
    protected $table = 'postage_cost';

    public function getAll()
    {
        $sql = "SELECT * FROM `postage_cost` ORDER BY id ASC";
        return $this->query($sql);
    }

    public function findByCountryZone($countryId, $zone)
    {
        $sql = "SELECT * FROM `postage_cost` WHERE `country_id` = ? AND `shipping_zone` = ?";
        $rows = $this->query($sql, "ss", [$countryId, $zone]);
        return $rows[0] ?? null;
    }

    public function upsert($countryId, $zone, $currency, $firstKilo, $nextKilo, $dateNow)
    {
        $existing = $this->findByCountryZone($countryId, $zone);

        if ($existing) {
            $sql = "UPDATE `postage_cost` SET `currency` = ?, `first_kilo` = ?, `next_kilo` = ?, `updated_at` = ? WHERE `country_id` = ? AND `shipping_zone` = ?";
            return $this->execute($sql, "ssssss", [$currency, $firstKilo, $nextKilo, $dateNow, $countryId, $zone]);
        } else {
            $sql = "INSERT INTO `postage_cost` (`country_id`, `shipping_zone`, `currency`, `first_kilo`, `next_kilo`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            return $this->execute($sql, "sssssss", [$countryId, $zone, $currency, $firstKilo, $nextKilo, $dateNow, $dateNow]);
        }
    }
}
