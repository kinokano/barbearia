<?php
/**
 * Configuração CORS
 */
return [
    'allowed_origins' => [
        env('APP_URL', '*'),
    ],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    'max_age'         => 3600,
];
