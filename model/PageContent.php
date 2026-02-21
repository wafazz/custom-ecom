<?php

require_once __DIR__ . '/BaseModel.php';

class PageContent extends BaseModel
{
    protected $table = 'policy';

    public function getContent($tableName)
    {
        $sql = "SELECT * FROM `{$tableName}` LIMIT 1";
        $rows = $this->query($sql);
        return $rows[0] ?? null;
    }

    public function updateContent($tableName, $description, $dateNow)
    {
        $sql = "UPDATE `{$tableName}` SET `description` = ?, `updated_at` = ? WHERE id = 1";
        return $this->execute($sql, "ss", [$description, $dateNow]);
    }
}
