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
        $sql = "INSERT INTO `store_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = NOW()";
        return $this->execute($sql, "ss", [$key, $value]);
    }
}
