<?php

require_once __DIR__ . '/BaseModel.php';

class Activity extends BaseModel
{
    protected $table = 'activities';

    public function getWithMembers()
    {
        $sql = "
            SELECT
                a.id AS activity_id, a.user_id, a.created_at AS activity_created,
                a.updated_at AS activity_updated, a.deleted_at AS activity_deleted,
                a.description, a.table_name, a.activities,
                m.id AS member_id, m.email, m.password, m.sec_pin,
                m.f_name, m.l_name, m.phone, m.role,
                m.created_at AS member_created, m.updated_at AS member_updated,
                m.deleted_at AS member_deleted, m.status
            FROM activities a
            LEFT JOIN member_hq m ON a.user_id = m.id
            ORDER BY activity_id DESC
        ";
        return $this->query($sql);
    }
}
