<?php

require_once __DIR__ . '/BaseModel.php';

class SenangPaySetting extends BaseModel
{
    protected $table = 'senangpay_api';

    public function getSettings()
    {
        $sql = "SELECT * FROM `senangpay_api` ORDER BY id DESC LIMIT 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function getCredentials()
    {
        $row = $this->getSettings();
        if (!$row) return null;

        if ($row['type'] == 'sandbox') {
            return [
                'merchant_id' => $row['merchant_id'],
                'secret_key'  => $row['secret_key'],
                'url'         => $row['sandbox_url'],
                'type'        => 'sandbox',
            ];
        }

        return [
            'merchant_id' => $row['pro_merchant_id'],
            'secret_key'  => $row['pro_secret_key'],
            'url'         => $row['production_url'],
            'type'        => 'production',
        ];
    }
}
