<?php

require_once __DIR__ . '/BaseModel.php';

class ImageSetting extends BaseModel
{
    protected $table = 'image_setting';

    public function getLogos()
    {
        $sql = "SELECT * FROM `image_setting` WHERE `use_type` = 'logo' AND `deleted_at` IS NULL ORDER BY `created_at` DESC";
        return $this->query($sql);
    }

    public function disableAll()
    {
        return $this->execute("UPDATE `image_setting` SET `sorting` = '0'");
    }

    public function setDefault($id)
    {
        $disableAll = $this->disableAll();
        if ($disableAll) {
            $sql = "UPDATE `image_setting` SET `sorting` = '1' WHERE `id` = ?";
            return $this->execute($sql, "i", [$id]);
        }
        return false;
    }

    public function addImage($data)
    {
        $sql = "INSERT INTO `image_setting` (`use_type`, `image_path`, `use_link`, `sorting`, `created_at`, `updated_at`, `deleted_at`) VALUES (?, ?, NULL, ?, ?, ?, NULL)";
        return $this->execute($sql, "sssss", [$data['use_type'], $data['image_path'], $data['sorting'], $data['created_at'], $data['updated_at']]);
    }
}
