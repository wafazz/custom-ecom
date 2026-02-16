<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

require_once __DIR__ . '/config/redis.php';
require_once __DIR__ . '/jobs/LiveUpdaterJob.php';
require_once __DIR__ . '/jobs/LiveOrdersJob.php';
require_once __DIR__ . '/jobs/CheckAbandonJob.php';

$queue = 'jobs:queue';

// Persistent DB connection — created once, reused across all jobs
function createDbConnection()
{
    $host = '178.128.20.226';
    $user = 'keya88';
    $pass = '224223Fakrul2897!';
    $db   = '2025_rozeyana';
    $port = 3306;

    $conn = new mysqli();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    $conn->real_connect($host, $user, $pass, $db, $port);

    if ($conn->connect_error) {
        throw new Exception("DB Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

$jobMap = [
    'live-updater'     => 'LiveUpdaterJob',
    'live-orders'      => 'LiveOrdersJob',
    'checking-abandon' => 'CheckAbandonJob',
];

echo "[Worker] Started at " . date('Y-m-d H:i:s') . "\n";

$mysqli = createDbConnection();
echo "[Worker] DB connected\n";

while (true) {
    $payload = queue_pop($queue, 5);

    if ($payload === null) {
        // Timeout — no jobs, check DB connection is alive
        if (!$mysqli->ping()) {
            echo "[Worker] DB connection lost, reconnecting...\n";
            $mysqli = createDbConnection();
        }
        continue;
    }

    $jobName = $payload['job'] ?? 'unknown';
    $className = $jobMap[$jobName] ?? null;

    if (!$className) {
        echo "[Worker] Unknown job: {$jobName}\n";
        continue;
    }

    try {
        // Reconnect if connection dropped
        if (!$mysqli->ping()) {
            echo "[Worker] DB reconnecting...\n";
            $mysqli = createDbConnection();
        }

        $job = new $className($mysqli);
        $result = $job->handle();
        echo "[Worker] {$jobName}: {$result}\n";
    } catch (Exception $e) {
        echo "[Worker] ERROR {$jobName}: " . $e->getMessage() . "\n";
        // Try to reconnect on next iteration
        try {
            $mysqli = createDbConnection();
        } catch (Exception $reconnectEx) {
            echo "[Worker] DB reconnect failed: " . $reconnectEx->getMessage() . "\n";
            sleep(2);
        }
    }
}
