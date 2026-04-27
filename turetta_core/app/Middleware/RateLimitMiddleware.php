<?php
/**
 * RateLimitMiddleware — Throttle simples por IP
 * 
 * Utiliza arquivos em cache/ para controlar rate limit
 * sem dependência de Redis/Memcached.
 */
class RateLimitMiddleware
{
    private static int $maxRequests = 60; // por minuto
    private static int $window = 60; // segundos

    public static function handle()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = md5($ip);
        $cacheFile = STORAGE_PATH . '/cache/rate_' . $key;

        $requests = 0;
        $windowStart = time();

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && (time() - $data['start']) < self::$window) {
                $requests = $data['count'];
                $windowStart = $data['start'];
            }
        }

        if ($requests >= self::$maxRequests) {
            http_response_code(429);
            return [
                'success' => false,
                'message' => 'Muitas requisições. Tente novamente em alguns segundos.',
            ];
        }

        file_put_contents($cacheFile, json_encode([
            'count' => $requests + 1,
            'start' => $windowStart,
        ]));

        return true;
    }
}
