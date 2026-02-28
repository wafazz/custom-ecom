<?php
/**
 * NinjaVan Token Refresh — run via cron twice a day
 * Crontab: 0 6,18 * * * php /path/to/cron/ninjavan-token.php
 */

date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../config/mainConfig.php';
require_once __DIR__ . '/../config/function.php';

$nv = dataSettingNinjaVan();
$sandbox = ($nv['status'] == '0');
$mode = $sandbox ? 'sandbox' : 'production';

if ($sandbox) {
    $clientId = $nv['username_sanbox'];
    $clientSecret = $nv['key_sandbox'];
} else {
    $clientId = $nv['username_production'];
    $clientSecret = $nv['key_production'];
}

if (empty($clientId) || empty($clientSecret)) {
    echo "[" . date('Y-m-d H:i:s') . "] NinjaVan {$mode} credentials not set. Skipping.\n";
    exit(1);
}

$result = tokenNinjaVanGenerate($clientId, $clientSecret, $sandbox);

if (!empty($result['success'])) {
    echo "[" . date('Y-m-d H:i:s') . "] NinjaVan {$mode} token refreshed. Expires in {$result['expires_in']}s.\n";
} else {
    echo "[" . date('Y-m-d H:i:s') . "] NinjaVan {$mode} token FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    exit(1);
}
