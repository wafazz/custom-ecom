<?php

require_once __DIR__ . '/BaseModel.php';

class SupportTicket extends BaseModel
{
    protected $table = 'cs_tickets';

    public function getOpenTickets()
    {
        $sql = "
            SELECT id, customer_name, customer_email, ticket_no, customer_id, title,
                description, status, order_id, assigned_to, created_at, updated_at, priority
            FROM cs_tickets
            WHERE status != 'closed'
            ORDER BY
                FIELD(priority, 'urgent', 'high', 'medium', 'low'),
                created_at DESC
        ";
        return $this->query($sql);
    }

    public function getTicketById($id)
    {
        $sql = "SELECT id, title AS subject, description, status, customer_name, customer_email, priority, created_at, ticket_no FROM cs_tickets WHERE id = ?";
        $rows = $this->query($sql, "i", [$id]);
        return $rows[0] ?? null;
    }

    public function getTicketAttachments($ticketId)
    {
        $sql = "SELECT id, filename, file_path, file_type, created_at FROM cs_ticket_attachments WHERE ticket_id = ?";
        return $this->query($sql, "i", [$ticketId]);
    }

    public function getReplies($ticketId)
    {
        $sql = "SELECT id, ticket_id, user_type, user_id, message, created_at FROM cs_ticket_replies WHERE ticket_id = ? ORDER BY id ASC";
        return $this->query($sql, "i", [$ticketId]);
    }

    public function getReplyAttachments($replyId)
    {
        $sql = "SELECT id, filename, file_path, file_type, created_at FROM cs_reply_attachments WHERE reply_id = ?";
        return $this->query($sql, "i", [$replyId]);
    }

    public function addReply($data)
    {
        $sql = "INSERT INTO cs_ticket_replies (ticket_id, user_type, user_id, message, created_at) VALUES (?, ?, ?, ?, ?)";
        $this->execute($sql, "isiss", [$data['ticket_id'], $data['user_type'], $data['user_id'], $data['message'], $data['created_at']]);
        return $this->conn->insert_id;
    }

    public function addReplyAttachment($data)
    {
        $sql = "INSERT INTO cs_reply_attachments (reply_id, filename, file_path, file_type, created_at) VALUES (?, ?, ?, ?, ?)";
        return $this->execute($sql, "issss", [$data['reply_id'], $data['filename'], $data['file_path'], $data['file_type'], $data['created_at']]);
    }

    public function closeTicket($id)
    {
        $sql = "UPDATE cs_tickets SET status = 'closed', updated_at = NOW() WHERE id = ?";
        return $this->execute($sql, "i", [$id]);
    }
}
