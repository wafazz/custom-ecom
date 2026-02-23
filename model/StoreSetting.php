<?php

require_once __DIR__ . '/BaseModel.php';

class StoreSetting extends BaseModel
{
    protected $table = 'store_settings';

    public function getAll()
    {
        $rows = $this->query("SELECT `setting_key`, `setting_value` FROM `store_settings`");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateSetting($key, $value)
    {
        $now = dateNow();
        $sql = "INSERT INTO `store_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = VALUES(`updated_at`)";
        return $this->execute($sql, "sss", [$key, $value, $now]);
    }
}
