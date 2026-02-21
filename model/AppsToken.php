<?php

require_once __DIR__ . '/BaseModel.php';

class AppsToken extends BaseModel
{
    protected $table = 'apps_token';

    public function createToken($userId, $token, $dateNow, $expiredAt)
    {
        $sql = "INSERT INTO `apps_token` (`user_id`, `token`, `created_at`, `expired_at`) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, "ssss", [$userId, $token, $dateNow, $expiredAt]);
    }

    public function validateToken($userId, $token)
    {
        $sql = "SELECT * FROM `apps_token` WHERE `user_id` = ? AND `token` = ?";
        $rows = $this->query($sql, "ss", [$userId, $token]);
        return $rows[0] ?? null;
    }
}
