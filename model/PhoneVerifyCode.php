<?php

require_once __DIR__ . '/BaseModel.php';

class PhoneVerifyCode extends BaseModel
{
    protected $table = 'phone_verify_code';

    public function findByMemberAndPhone($memberId, $phoneNo)
    {
        $sql = "SELECT * FROM `phone_verify_code` WHERE `member` = ? AND `phone_no` = ?";
        $rows = $this->query($sql, "ss", [$memberId, $phoneNo]);
        return $rows[0] ?? null;
    }

    public function findByMember($memberId)
    {
        $sql = "SELECT * FROM `phone_verify_code` WHERE `member` = ?";
        $rows = $this->query($sql, "s", [$memberId]);
        return $rows[0] ?? null;
    }

    public function createRecord($data)
    {
        $sql = "INSERT INTO `phone_verify_code` (`mem_id`, `member`, `phone_no`, `wasap_code`, `status`, `date_send`, `date_update`, `code_expired_on`) VALUES (NULL, ?, ?, ?, '0', ?, ?, ?)";
        return $this->execute($sql, "ssssss", [
            $data['member'], $data['phone_no'], $data['wasap_code'],
            $data['date_send'], $data['date_update'], $data['code_expired_on']
        ]);
    }

    public function updateCode($memberId, $phoneNo, $data)
    {
        $sql = "UPDATE `phone_verify_code` SET `wasap_code` = ?, `status` = '0', `date_send` = ?, `date_update` = ?, `code_expired_on` = ? WHERE `member` = ? AND `phone_no` = ?";
        return $this->execute($sql, "ssssss", [
            $data['wasap_code'], $data['date_send'], $data['date_update'],
            $data['code_expired_on'], $memberId, $phoneNo
        ]);
    }

    public function verifyCode($memberId, $code)
    {
        $sql = "UPDATE `phone_verify_code` SET `status` = '1' WHERE `member` = ? AND `wasap_code` = ?";
        return $this->execute($sql, "ss", [$memberId, $code]);
    }
}
