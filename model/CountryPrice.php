<?php

require_once __DIR__ . '/BaseModel.php';

class CountryPrice extends BaseModel
{
    protected $table = 'list_country_product_price';

    public function insertPrice($countryId, $productId, $marketPrice, $salePrice, $dateNow)
    {
        $sql = "INSERT INTO `list_country_product_price` (`country_id`, `product_id`, `market_price`, `sale_price`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, "iissss", [$countryId, $productId, $marketPrice, $salePrice, $dateNow, $dateNow]);
    }

    public function updatePrice($countryId, $productId, $marketPrice, $salePrice, $dateNow)
    {
        $sql = "UPDATE `list_country_product_price` SET `market_price` = ?, `sale_price` = ?, `updated_at` = ? WHERE `country_id` = ? AND `product_id` = ?";
        return $this->execute($sql, "sssii", [$marketPrice, $salePrice, $dateNow, $countryId, $productId]);
    }
}
