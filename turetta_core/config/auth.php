<?php
/**
 * Configuração de Autenticação JWT
 */
return [
    'secret'      => env('JWT_SECRET', 'CHANGE_ME'),
    'algorithm'   => 'HS256',
    'ttl'         => (int) env('JWT_TTL', 3600),         // 1 hora
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 604800), // 7 dias
];
