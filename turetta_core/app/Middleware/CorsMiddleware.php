<?php
/**
 * CorsMiddleware
 */
class CorsMiddleware
{
    public static function handle()
    {
        $config = config('cors');

        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

        if (in_array('*', $config['allowed_origins']) || in_array($origin, $config['allowed_origins'])) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $config['allowed_methods']));
        header('Access-Control-Allow-Headers: ' . implode(', ', $config['allowed_headers']));
        header('Access-Control-Max-Age: ' . $config['max_age']);

        return true;
    }
}
