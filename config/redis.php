<?php

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use Predis\Client as PredisClient;

$_redisInstance = null;

function getRedis()
{
    global $_redisInstance;
    if ($_redisInstance === null) {
        try {
            $_redisInstance = new PredisClient([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
            ]);
            $_redisInstance->ping();
        } catch (\Exception $e) {
            $_redisInstance = null;
            return null;
        }
    }
    return $_redisInstance;
}

function cache_get($key)
{
    $redis = getRedis();
    if (!$redis) return null;
    try {
        $val = $redis->get($key);
        return $val !== null ? unserialize($val) : null;
    } catch (\Exception $e) {
        return null;
    }
}

function cache_set($key, $value, $ttl = 300)
{
    $redis = getRedis();
    if (!$redis) return false;
    try {
        $redis->setex($key, $ttl, serialize($value));
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function cache_delete($key)
{
    $redis = getRedis();
    if (!$redis) return false;
    try {
        $redis->del([$key]);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function cache_flush($prefix)
{
    $redis = getRedis();
    if (!$redis) return false;
    try {
        $keys = $redis->keys($prefix . '*');
        if (!empty($keys)) {
            $redis->del($keys);
        }
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function cache_remember($key, $ttl, $callback)
{
    $cached = cache_get($key);
    if ($cached !== null) {
        return $cached;
    }
    $value = $callback();
    cache_set($key, $value, $ttl);
    return $value;
}

function rate_limit($key, $maxAttempts, $windowSeconds)
{
    $redis = getRedis();
    if (!$redis) return true; // allow if Redis is down
    try {
        $current = (int) $redis->get($key);
        if ($current >= $maxAttempts) {
            return false;
        }
        $redis->incr($key);
        if ($current === 0) {
            $redis->expire($key, $windowSeconds);
        }
        return true;
    } catch (\Exception $e) {
        return true;
    }
}

function queue_push($queue, $jobData)
{
    $redis = getRedis();
    if (!$redis) return false;
    try {
        $redis->lpush($queue, [json_encode($jobData)]);
        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function queue_pop($queue, $timeout = 5)
{
    $redis = getRedis();
    if (!$redis) return null;
    try {
        $result = $redis->brpop([$queue], $timeout);
        if ($result) {
            return json_decode($result[1], true);
        }
        return null;
    } catch (\Exception $e) {
        return null;
    }
}

class RedisSessionHandler implements \SessionHandlerInterface
{
    private $redis;
    private $ttl;
    private $prefix;

    public function __construct($redis, $ttl = 7200, $prefix = 'session:')
    {
        $this->redis = $redis;
        $this->ttl = $ttl;
        $this->prefix = $prefix;
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        try {
            $data = $this->redis->get($this->prefix . $id);
            return $data !== null ? $data : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    public function write($id, $data): bool
    {
        try {
            $this->redis->setex($this->prefix . $id, $this->ttl, $data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function destroy($id): bool
    {
        try {
            $this->redis->del([$this->prefix . $id]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function gc($max_lifetime): int|false
    {
        // Redis handles expiry automatically via TTL
        return 0;
    }
}
