<?php

require_once __DIR__ . '/BaseModel.php';

class CodCharge extends BaseModel
{
    protected $table = 'cod_charges';

    public function findByCountryZone($countryId, $zone)
    {
        $sql = "SELECT * FROM `cod_charges` WHERE `country_id` = ? AND `shipping_zone` = ?";
        $rows = $this->query($sql, "ss", [$countryId, $zone]);
        return $rows[0] ?? null;
    }

    public function upsert($countryId, $zone, $benchmarkAmount, $codFeeBelow, $codFeeAbove, $dateNow)
    {
        $existing = $this->findByCountryZone($countryId, $zone);

        if ($existing) {
            $sql = "UPDATE `cod_charges` SET `benchmark_amount` = ?, `cod_fee_below` = ?, `cod_fee_above` = ?, `updated_at` = ? WHERE `country_id` = ? AND `shipping_zone` = ?";
            return $this->execute($sql, "ssssss", [$benchmarkAmount, $codFeeBelow, $codFeeAbove, $dateNow, $countryId, $zone]);
        } else {
            $sql = "INSERT INTO `cod_charges` (`country_id`, `shipping_zone`, `benchmark_amount`, `cod_fee_below`, `cod_fee_above`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            return $this->execute($sql, "sssssss", [$countryId, $zone, $benchmarkAmount, $codFeeBelow, $codFeeAbove, $dateNow, $dateNow]);
        }
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM `cod_charges` WHERE `id` = ?";
        return $this->execute($sql, "i", [$id]);
    }
}
