<?php

require_once __DIR__ . '/BaseModel.php';

class Member extends BaseModel
{
    protected $table = 'member';

    public function checkEmailExists($email)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `member` WHERE `email` = ?";
        $rows = $this->query($sql, "s", [$email]);
        return (int) ($rows[0]['cnt'] ?? 0) > 0;
    }

    public function checkPhoneExists($phone)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `member` WHERE `m_phone` LIKE ?";
        $rows = $this->query($sql, "s", ["%{$phone}%"]);
        return (int) ($rows[0]['cnt'] ?? 0) > 0;
    }
}
