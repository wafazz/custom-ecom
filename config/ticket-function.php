<?php

function mainTicketAttachments($ticketid) {
    $conn = getDbConnection();
    // Function implementation goes here
    $query = "SELECT * FROM cs_ticket_attachments WHERE ticket_id = '$ticketid'";
    $result = $conn->query($query);

    return $result;
}

function replyTicket($ticketid) {
    $conn = getDbConnection();
    // Function implementation goes here
    $query = "SELECT * FROM cs_ticket_replies WHERE ticket_id = '$ticketid' ORDER BY created_at ASC";
    $result = $conn->query($query);

    return $result;
}

function replyTicketAttachments($replyid) {
    $conn = getDbConnection();
    // Function implementation goes here
    $query = "SELECT * FROM cs_reply_attachments WHERE reply_id = '$replyid'";
    $result = $conn->query($query);

    return $result;
}