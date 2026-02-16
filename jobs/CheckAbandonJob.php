<?php

class CheckAbandonJob
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function handle()
    {
        $timezone = new DateTimeZone('Asia/Kuala_Lumpur');
        $datetime = new DateTime('now', $timezone);
        $dateNow = $datetime->format('Y-m-d H:i:s');

        $result = $this->mysqli->query("SELECT * FROM cart WHERE deleted_at IS NULL AND `status`='0'");

        $expired = 0;
        while ($row = $result->fetch_assoc()) {
            $idCart = $row["id"];
            $newTime = date('Y-m-d H:i:s', strtotime($row["updated_at"] . ' +30 minutes'));

            if ($dateNow > $newTime) {
                $this->mysqli->query("UPDATE cart SET updated_at='$dateNow', deleted_at='$dateNow', `status`='4' WHERE id='$idCart'");
                $expired++;
            }
        }

        return "Abandon check done: {$expired} expired";
    }
}
