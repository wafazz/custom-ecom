<?php

class LiveUpdaterJob
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function handle()
    {
        $nows = date("Y-m-d H:i:s");
        $today = date("Y-m-d");

        $liveCount = 0;
        $liveVisitorResult = $this->mysqli->query("
            SELECT COUNT(*) AS live_user
            FROM `online_visitor_return`
            WHERE `session_end_at` >= '$nows'
        ");
        if ($liveVisitorResult) {
            $row = $liveVisitorResult->fetch_assoc();
            $liveCount = intval($row['live_user']);
        }

        $allVisitor = 0;
        $allVisitorResult = $this->mysqli->query("
            SELECT COUNT(*) AS all_user
            FROM `online_visitor_return`
        ");
        if ($allVisitorResult) {
            $row = $allVisitorResult->fetch_assoc();
            $allVisitor = intval($row['all_user']);
        }

        $allToday = 0;
        $allTodayResult = $this->mysqli->query("
            SELECT COUNT(*) AS all_today
            FROM `online_visitor_return`
            WHERE `created_at` LIKE '%$today%'
        ");
        if ($allTodayResult) {
            $row = $allTodayResult->fetch_assoc();
            $allToday = intval($row['all_today']);
        }

        $data = [
            'live_user' => $liveCount,
            'all_user'  => $allVisitor,
            'all_today' => $allToday,
            'update_at' => $nows
        ];

        $filePath = __DIR__ . '/../live_visitors.json';
        if (!file_exists($filePath)) {
            file_put_contents($filePath, '');
            chmod($filePath, 0666);
        }
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return "Live visitors updated: {$liveCount}";
    }
}
