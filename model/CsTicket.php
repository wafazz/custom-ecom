<?php

require_once __DIR__ . '/BaseModel.php';

class CsTicket extends BaseModel
{
    protected $table = 'cs_tickets';

    public function createTicket($data)
    {
        $sql = "INSERT INTO `cs_tickets` (`customer_name`, `customer_email`, `ticket_no`, `customer_id`, `title`, `description`, `status`, `order_id`, `assigned_to`, `created_at`, `updated_at`, `priority`) VALUES (?, ?, ?, ?, ?, ?, 'new', ?, 0, ?, ?, ?)";
        $this->execute($sql, "sssissssss", [
            $data['customer_name'], $data['customer_email'], $data['ticket_no'],
            $data['customer_id'], $data['title'], $data['description'],
            $data['order_id'], $data['created_at'], $data['updated_at'], $data['priority']
        ]);
        return $this->conn->insert_id;
    }

    public function findByTicketNo($ticketNo)
    {
        $sql = "SELECT * FROM `cs_tickets` WHERE `ticket_no` = ? AND `status` != 'closed' LIMIT 1";
        $rows = $this->query($sql, "s", [$ticketNo]);
        return $rows[0] ?? null;
    }

    public function createReply($data)
    {
        $sql = "INSERT INTO `cs_ticket_replies` (`ticket_id`, `user_type`, `user_id`, `message`, `created_at`) VALUES (?, ?, ?, ?, ?)";
        $this->execute($sql, "sssss", [
            $data['ticket_id'], $data['user_type'], $data['user_id'],
            $data['message'], $data['created_at']
        ]);
        return $this->conn->insert_id;
    }

    public function createTicketAttachment($data)
    {
        $sql = "INSERT INTO `cs_ticket_attachments` (`ticket_id`, `filename`, `file_path`, `file_type`, `uploaded_by`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, "isssss", [
            $data['ticket_id'], $data['filename'], $data['file_path'],
            $data['file_type'], $data['uploaded_by'], $data['created_at']
        ]);
    }

    public function createReplyAttachment($data)
    {
        $sql = "INSERT INTO `cs_reply_attachments` (`reply_id`, `filename`, `file_path`, `file_type`, `created_at`) VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, "issss", [
            $data['reply_id'], $data['filename'], $data['file_path'],
            $data['file_type'], $data['created_at']
        ]);
    }

    public function createLog($data)
    {
        $sql = "INSERT INTO `cs_ticket_logs` (`ticket_id`, `action`, `action_by`, `previous_value`, `new_value`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, "issss" . "s", [
            $data['ticket_id'], $data['action'], $data['action_by'],
            $data['previous_value'], $data['new_value'], $data['created_at']
        ]);
    }
}
