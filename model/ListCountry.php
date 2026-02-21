<?php

require_once __DIR__ . '/BaseModel.php';

class ListCountry extends BaseModel
{
    protected $table = 'list_country';

    public function getAll()
    {
        $sql = "SELECT `id`, `name`, `sign`, `rate`, `phone_code`, `created_at`, `updated_at`, `status` FROM `list_country`";
        return $this->query($sql);
    }

    public function getNamesAndCodes()
    {
        $sql = "SELECT `name`, `phone_code` FROM `list_country` ORDER BY `name` ASC";
        return $this->query($sql);
    }

    public function getAllWorldCountries()
    {
        $sql = "SELECT * FROM `all_country`";
        return $this->query($sql);
    }

    public function addCountry($data)
    {
        $sql = "INSERT INTO `list_country` (`name`, `sign`, `rate`, `phone_code`, `created_at`, `updated_at`, `status`) VALUES (?, ?, ?, ?, ?, ?, '1')";
        return $this->execute($sql, "ssssss", [
            $data['name'], $data['sign'], $data['rate'], $data['phone_code'],
            $data['created_at'], $data['updated_at']
        ]);
    }

    public function updateCountry($id, $data)
    {
        $sql = "UPDATE `list_country` SET `sign` = ?, `rate` = ?, `updated_at` = ?, `status` = ? WHERE `id` = ?";
        return $this->execute($sql, "ssssi", [
            $data['sign'], $data['rate'], $data['updated_at'], $data['status'], $id
        ]);
    }

    public function getActiveWithDetails()
    {
        $sql = "SELECT `id`, `name`, `sign`, `rate`, `phone_code` FROM `list_country` WHERE `status` = 1";
        return $this->query($sql);
    }
}
