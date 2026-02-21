<?php

require_once __DIR__ . '/BaseModel.php';

class Membership extends BaseModel
{
    protected $table = 'membership';

    public function findById($id)
    {
        $sql = "SELECT * FROM `membership` WHERE `id` = ?";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function addPointHistory($data)
    {
        $sql = "INSERT INTO `membership_point_history` (`membership_id`, `referral`, `order_id`, `purchase_amount`, `point_amount`, `date_purchase`, `date_expired_point`, `point_status`) VALUES (?, ?, ?, ?, ?, ?, '', '0')";
        return $this->execute($sql, "ssssss", [
            $data['membership_id'], $data['referral'], $data['order_id'],
            $data['purchase_amount'], $data['point_amount'], $data['date_purchase']
        ]);
    }

    public function checkEmailExists($email)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `membership` WHERE `email` = ?";
        $rows = $this->query($sql, "s", [$email]);
        return (int) ($rows[0]['cnt'] ?? 0) > 0;
    }

    public function checkPhoneExists($ccode, $phone)
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `membership` WHERE `phone_c_code` = ? AND `phone_no` = ?";
        $rows = $this->query($sql, "ss", [$ccode, $phone]);
        return (int) ($rows[0]['cnt'] ?? 0) > 0;
    }

    public function findByEmailAndPassword($email, $password)
    {
        $sql = "SELECT * FROM `membership` WHERE `email` = ? AND `password` = ?";
        $rows = $this->query($sql, "ss", [$email, $password]);
        return $rows[0] ?? null;
    }

    public function registerMember($data)
    {
        $sql = "INSERT INTO `membership` (`reg_hex`, `name`, `email`, `password`, `phone_c_code`, `phone_no`, `address_1`, `address_2`, `city`, `postcode`, `state`, `country`, `date_added`, `date_update`, `date_delete`, `status`, `phone_verify`, `email_verify`, `membership_stage`, `referral`, `referral_membership`) VALUES (?, ?, ?, ?, ?, ?, '', '', '', '', '', ?, ?, ?, NULL, '0', '0', '0', '0', ?, ?)";
        $this->execute($sql, "sssssssssss", [
            $data['reg_hex'], $data['name'], $data['email'], $data['password'],
            $data['phone_c_code'], $data['phone_no'], $data['country'],
            $data['date_added'], $data['date_update'], $data['referral'],
            $data['referral_membership'] ?? '0'
        ]);
        return $this->conn->insert_id;
    }

    public function findByRegHex($regHex)
    {
        $sql = "SELECT * FROM `membership` WHERE `reg_hex` = ?";
        $rows = $this->query($sql, "s", [$regHex]);
        return $rows[0] ?? null;
    }

    public function updatePhoneVerify($id)
    {
        $sql = "UPDATE `membership` SET `phone_verify` = '1' WHERE `id` = ?";
        return $this->execute($sql, "s", [$id]);
    }
}
