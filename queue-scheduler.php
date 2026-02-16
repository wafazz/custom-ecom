<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

require_once __DIR__ . '/config/redis.php';

$queue = 'jobs:queue';

$schedule = [
    'live-updater'     => ['interval' => 1,  'last_run' => 0],
    'live-orders'      => ['interval' => 1,  'last_run' => 0],
    'checking-abandon' => ['interval' => 5,  'last_run' => 0],
];

echo "[Scheduler] Started at " . date('Y-m-d H:i:s') . "\n";

while (true) {
    $now = time();

    foreach ($schedule as $jobName => &$config) {
        if ($now - $config['last_run'] >= $config['interval']) {
            $pushed = queue_push($queue, [
                'job'       => $jobName,
                'pushed_at' => date('Y-m-d H:i:s'),
            ]);

            if ($pushed) {
                $config['last_run'] = $now;
            } else {
                echo "[Scheduler] ERROR: Failed to push {$jobName} — Redis down?\n";
            }
        }
    }
    unset($config);

    usleep(500000); // 0.5s check interval
}
