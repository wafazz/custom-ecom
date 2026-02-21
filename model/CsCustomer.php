<?php

require_once __DIR__ . '/BaseModel.php';

class CsCustomer extends BaseModel
{
    protected $table = 'cs_customers';

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM `cs_customers` WHERE `email` = ? LIMIT 1";
        $rows = $this->query($sql, "s", [$email]);
        return $rows[0] ?? null;
    }

    public function createCustomer($name, $email, $dateNow)
    {
        $sql = "INSERT INTO `cs_customers` (`name`, `email`, `created_at`) VALUES (?, ?, ?)";
        $this->execute($sql, "sss", [$name, $email, $dateNow]);
        return $this->conn->insert_id;
    }
}
