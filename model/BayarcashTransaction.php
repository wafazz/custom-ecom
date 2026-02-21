<?php

require_once __DIR__ . '/BaseModel.php';

class BayarcashTransaction extends BaseModel
{
    protected $table = 'bayarcash_transactions';

    public function createTransaction($data)
    {
        return $this->create($data);
    }

    public function updateByOrderNumber($orderNumber, $data)
    {
        $sets = [];
        $types = '';
        $params = [];

        foreach ($data as $col => $val) {
            $sets[] = "`{$col}` = ?";
            $types .= 's';
            $params[] = $val;
        }

        $types .= 's';
        $params[] = $orderNumber;

        $sql = "UPDATE `bayarcash_transactions` SET " . implode(', ', $sets) . " WHERE `order_number` = ?";
        return $this->execute($sql, $types, $params);
    }
}
